<?php

namespace App\Http\Controllers\Shop\NhanVien;

use App\Http\Controllers\Controller;
use App\Models\OrderMon;
use App\Models\DatBan;
use App\Models\HoaDon;
use App\Models\Voucher;
use App\Models\BanAn;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ThanhToanController extends Controller
{
    /**
     * Hiển thị form thanh toán từ ban_id (từ trang quản lý bàn)
     */
    public function thanhToanTuBan($banId)
    {
        $ban = BanAn::with('khuVuc')->findOrFail($banId);
        
        // Tìm đặt bàn đang hoạt động của bàn này
        $datBan = DatBan::where('ban_id', $banId)
            ->whereIn('trang_thai', ['khach_da_den', 'dang_phuc_vu', 'da_xac_nhan'])
            ->with([
                'comboBuffet',
                'orderMon.chiTietOrders.monAn',
                'banAn.khuVuc',
                'hoaDon'
            ])
            ->latest()
            ->first();

        if (!$datBan) {
            return redirect()
                ->route('nhanVien.ban-an.index')
                ->with('error', 'Bàn này chưa có khách hoặc chưa có đặt bàn hợp lệ!');
        }

        // Kiểm tra xem đã có hóa đơn chưa
        if ($datBan->hoaDon) {
            return redirect()
                ->route('nhanVien.thanh-toan.hien-thi-hoa-don', $datBan->hoaDon->id)
                ->with('info', 'Hóa đơn đã được tạo trước đó!');
        }

        // Tính tổng tiền: combo chính + món gọi thêm (tính tiền theo giới hạn combo)
        $tienComboChinh = 0;
        if ($datBan->comboBuffet) {
            $tienComboChinh = $datBan->comboBuffet->gia_co_ban * ($datBan->so_khach ?? 1);
        }
        
        // Tính tiền món gọi thêm và món combo vượt giới hạn
        $tongTienMonGoiThem = $this->tinhTienMonGoiThem($datBan);
        
        // Tổng tiền thực tế = combo chính + món gọi thêm
        $tongTienOrder = $tienComboChinh + $tongTienMonGoiThem;
        
        $tienCoc = (float) ($datBan->tien_coc ?? 0);

        // Tính giờ vào và giờ ra
        $gioVao = Carbon::parse($datBan->gio_den);
        $gioRa = now();
        $thoiGianPhucVu = $gioVao->diffInMinutes($gioRa);

        // Tính chi tiết phụ thu thời gian
        $thoiGianQuyDinh = 0;
        $thoiGianMienPhi = 0;
        $thoiGianVuot = 0;
        $soLan10Phut = 0;
        $phuThuThoiGian = 0;
        
        if ($datBan->comboBuffet && $datBan->comboBuffet->thoi_luong_phut) {
            $thoiGianQuyDinh = $datBan->comboBuffet->thoi_luong_phut;
            $thoiGianMienPhi = $thoiGianQuyDinh + 10; // Thời gian quy định + 10 phút miễn phí
            
            if ($thoiGianPhucVu > $thoiGianMienPhi) {
                $thoiGianVuot = $thoiGianPhucVu - $thoiGianMienPhi;
                $soLan10Phut = ceil($thoiGianVuot / 10); // Làm tròn lên: phút 1-10 = 1 lần, phút 11-20 = 2 lần, phút 21-30 = 3 lần...
                $phuThuThoiGian = $soLan10Phut * 30000; // Mỗi 10 phút = 30k
            }
        }

        // Tính phụ thu tự động (bao gồm cả phụ phí món)
        $phuThuTuDong = $this->tinhPhuThuTuDong($datBan, $thoiGianPhucVu);

        // Lấy danh sách voucher hợp lệ
        $vouchers = Voucher::where('trang_thai', 'dang_ap_dung')
            ->where('ngay_ket_thuc', '>=', now())
            ->whereRaw('so_luong > so_luong_da_dung')
            ->get();

        return view('Shop.nhanVien.thanh-toan.thanh-toan-tu-ban', compact(
            'ban', 'datBan', 'tongTienOrder', 'tienCoc', 'vouchers', 'gioVao', 'gioRa', 'thoiGianPhucVu', 'phuThuTuDong',
            'thoiGianQuyDinh', 'thoiGianMienPhi', 'thoiGianVuot', 'soLan10Phut', 'phuThuThoiGian'
        ));
    }

    /**
     * Lưu hóa đơn từ ban_id
     */
    public function luuThanhToanTuBan(Request $request, $banId)
    {
        $request->validate([
            'phuong_thuc_tt' => 'required|string|in:tien_mat,chuyen_khoan,the_ATM',
            'phu_thu' => 'nullable|numeric|min:0',
            'voucher_id' => 'nullable|exists:vouchers,id',
            'tien_khach_dua' => 'nullable|numeric|min:0',
        ]);

        $ban = BanAn::findOrFail($banId);
        
        // Tìm đặt bàn đang hoạt động
        $datBan = DatBan::where('ban_id', $banId)
            ->whereIn('trang_thai', ['khach_da_den', 'dang_phuc_vu'])
            ->with([
                'comboBuffet',
                'orderMon.chiTietOrders.monAn',
                'banAn',
                'hoaDon'
            ])
            ->latest()
            ->first();

        if (!$datBan) {
            return redirect()
                ->route('nhanVien.ban-an.index')
                ->with('error', 'Bàn này chưa có khách hoặc chưa có đặt bàn hợp lệ!');
        }

        // Kiểm tra xem đã có hóa đơn chưa
        if ($datBan->hoaDon) {
            return redirect()
                ->route('nhanVien.thanh-toan.hien-thi-hoa-don', $datBan->hoaDon->id)
                ->with('info', 'Hóa đơn đã được tạo trước đó!');
        }

        // Tính tổng tiền: combo chính + món gọi thêm (tính tiền theo giới hạn combo)
        $tienComboChinh = 0;
        if ($datBan->comboBuffet) {
            $tienComboChinh = $datBan->comboBuffet->gia_co_ban * ($datBan->so_khach ?? 1);
        }
        
        // Tính tiền món gọi thêm và món combo vượt giới hạn
        $tongTienMonGoiThem = $this->tinhTienMonGoiThem($datBan);
        
        // Tổng tiền thực tế = combo chính + món gọi thêm
        $tongTienOrder = $tienComboChinh + $tongTienMonGoiThem;
        
        $tienCoc = (float) ($datBan->tien_coc ?? 0);
        
        // Tính phụ thu tự động
        $gioVao = Carbon::parse($datBan->gio_den);
        $gioRa = now();
        $thoiGianPhucVu = $gioVao->diffInMinutes($gioRa);
        $phuThuTuDong = $this->tinhPhuThuTuDong($datBan, $thoiGianPhucVu);
        
        // Phụ thu = phụ thu tự động + phụ thu thủ công (nếu có)
        $phuThuThucCong = (float) ($request->phu_thu ?? 0);
        $phuThu = $phuThuTuDong + $phuThuThucCong;

        // Xử lý voucher
        $voucher = $request->voucher_id ? Voucher::find($request->voucher_id) : null;
        $tienGiam = 0;
        
        if ($voucher) {
            if ($voucher->loai_giam == 'phan_tram') {
                $tienGiam = $tongTienOrder * ($voucher->gia_tri / 100);
                if ($voucher->gia_tri_toi_da && $tienGiam > $voucher->gia_tri_toi_da) {
                    $tienGiam = $voucher->gia_tri_toi_da;
                }
            } else {
                $tienGiam = $voucher->gia_tri;
            }
            if ($tienGiam > $tongTienOrder) {
                $tienGiam = $tongTienOrder;
            }
        }

        // Tính tiền phải thanh toán
        $daThanhToan = $tongTienOrder - $tienGiam + $phuThu - $tienCoc;
        if ($daThanhToan < 0) $daThanhToan = 0;

        // Tạo hóa đơn
        $hoaDon = HoaDon::create([
            'dat_ban_id' => $datBan->id,
            'voucher_id' => $voucher ? $voucher->id : null,
            'ma_hoa_don' => 'HD' . date('YmdHis') . '-' . $datBan->id,
            'tong_tien' => $tongTienOrder,
            'tien_giam' => $tienGiam,
            'phu_thu' => $phuThu,
            'da_thanh_toan' => $daThanhToan,
            'phuong_thuc_tt' => $request->phuong_thuc_tt,
        ]);

        // Cập nhật voucher
        if ($voucher) {
            $voucher->increment('so_luong_da_dung');
        }

        // Cập nhật trạng thái đặt bàn
        $datBan->update(['trang_thai' => 'hoan_tat']);

        // Cập nhật trạng thái bàn thành trống
        if ($datBan->banAn) {
            $datBan->banAn->update(['trang_thai' => 'trong']);
        }

        // Cập nhật trạng thái tất cả order
        foreach ($datBan->orderMon as $order) {
            $order->update(['trang_thai' => 'hoan_thanh']);
        }

        // Chuyển hướng đến trang hiển thị hóa đơn
        return redirect()
            ->route('nhanVien.thanh-toan.hien-thi-hoa-don', $hoaDon->id)
            ->with('success', 'Thanh toán thành công!');
    }

    /**
     * Hiển thị form thanh toán cho một order
     */
    public function thanhToan($orderId)
    {
        $order = OrderMon::with([
            'banAn',
            'datBan.comboBuffet',
            'datBan.orderMon.chiTietOrders.monAn',
            'chiTietOrders.monAn'
        ])->findOrFail($orderId);

        // Kiểm tra xem đã có hóa đơn chưa
        if ($order->datBan->hoaDon) {
            return redirect()
                ->route('nhanVien.thanh-toan.hien-thi-hoa-don', $order->datBan->hoaDon->id)
                ->with('info', 'Hóa đơn đã được tạo trước đó!');
        }

        // Tính tổng tiền từ các order
        $tongTienOrder = $order->datBan->orderMon->sum('tong_tien');
        $tienCoc = (float) ($order->datBan->tien_coc ?? 0);

        // Lấy danh sách voucher hợp lệ
        $vouchers = Voucher::where('trang_thai', 'dang_ap_dung')
            ->where('ngay_ket_thuc', '>=', now())
            ->whereRaw('so_luong > so_luong_da_dung')
            ->get();

        return view('Shop.nhanVien.thanh-toan.thanh-toan', compact('order', 'tongTienOrder', 'tienCoc', 'vouchers'));
    }

    /**
     * Lưu hóa đơn và xử lý thanh toán
     */
    public function luuThanhToan(Request $request, $orderId)
    {
        $request->validate([
            'phuong_thuc_tt' => 'required|string|in:tien_mat,chuyen_khoan,the_ATM',
            'phu_thu' => 'nullable|numeric|min:0',
            'voucher_id' => 'nullable|exists:vouchers,id',
            'tien_khach_dua' => 'nullable|numeric|min:0', // Tiền khách đưa (nếu thanh toán tiền mặt)
        ]);

        $order = OrderMon::with([
            'datBan.comboBuffet',
            'datBan.orderMon.chiTietOrders.monAn',
            'datBan.banAn'
        ])->findOrFail($orderId);
        $datBan = $order->datBan;

        // Kiểm tra xem đã có hóa đơn chưa
        if ($datBan->hoaDon) {
            return redirect()
                ->route('nhanVien.thanh-toan.hien-thi-hoa-don', $datBan->hoaDon->id)
                ->with('info', 'Hóa đơn đã được tạo trước đó!');
        }

        // Tính tổng tiền: combo chính + món gọi thêm (tính tiền cho món order thêm, kể cả món trong combo)
        $tienComboChinh = 0;
        if ($datBan->comboBuffet) {
            $tienComboChinh = $datBan->comboBuffet->gia_co_ban * ($datBan->so_khach ?? 1);
        }
        
        // Tính tiền món gọi thêm (goi_them) và món combo order thêm
        $tongTienMonGoiThem = 0;
        
        foreach ($datBan->orderMon as $orderItem) {
            foreach ($orderItem->chiTietOrders as $ct) {
                if ($ct->trang_thai != 'huy_mon') {
                    if ($ct->loai_mon == 'goi_them') {
                        // Món gọi thêm: tính tiền bình thường
                        $tongTienMonGoiThem += ($ct->monAn->gia ?? 0) * $ct->so_luong;
                    } elseif ($ct->loai_mon == 'combo') {
                        // Món combo: kiểm tra xem có phải order thêm không
                        // Nếu số lượng > số khách, thì phần vượt quá là order thêm, tính tiền
                        $soLuongVuot = $ct->so_luong - ($datBan->so_khach ?? 0);
                        if ($soLuongVuot > 0) {
                            $tongTienMonGoiThem += ($ct->monAn->gia ?? 0) * $soLuongVuot;
                        }
                    }
                }
            }
        }
        
        // Tổng tiền thực tế = combo chính + món gọi thêm
        $tongTienOrder = $tienComboChinh + $tongTienMonGoiThem;
        
        $tienCoc = (float) ($datBan->tien_coc ?? 0);
        
        // Tính phụ thu tự động
        $gioVao = Carbon::parse($datBan->gio_den);
        $gioRa = now();
        $thoiGianPhucVu = $gioVao->diffInMinutes($gioRa);
        $phuThuTuDong = $this->tinhPhuThuTuDong($datBan, $thoiGianPhucVu);
        
        // Phụ thu = phụ thu tự động + phụ thu thủ công (nếu có)
        $phuThuThucCong = (float) ($request->phu_thu ?? 0);
        $phuThu = $phuThuTuDong + $phuThuThucCong;

        // Xử lý voucher
        $voucher = $request->voucher_id ? Voucher::find($request->voucher_id) : null;
        $tienGiam = 0;
        
        if ($voucher) {
            if ($voucher->loai_giam == 'phan_tram') {
                $tienGiam = $tongTienOrder * ($voucher->gia_tri / 100);
                if ($voucher->gia_tri_toi_da && $tienGiam > $voucher->gia_tri_toi_da) {
                    $tienGiam = $voucher->gia_tri_toi_da;
                }
            } else {
                $tienGiam = $voucher->gia_tri;
            }
            if ($tienGiam > $tongTienOrder) {
                $tienGiam = $tongTienOrder;
            }
        }

        // Tính tiền phải thanh toán
        $daThanhToan = $tongTienOrder - $tienGiam + $phuThu - $tienCoc;
        if ($daThanhToan < 0) $daThanhToan = 0;

        // Tạo hóa đơn
        $hoaDon = HoaDon::create([
            'dat_ban_id' => $datBan->id,
            'voucher_id' => $voucher ? $voucher->id : null,
            'ma_hoa_don' => 'HD' . date('YmdHis') . '-' . $datBan->id,
            'tong_tien' => $tongTienOrder,
            'tien_giam' => $tienGiam,
            'phu_thu' => $phuThu,
            'da_thanh_toan' => $daThanhToan,
            'phuong_thuc_tt' => $request->phuong_thuc_tt,
        ]);

        // Cập nhật voucher
        if ($voucher) {
            $voucher->increment('so_luong_da_dung');
        }

        // Cập nhật trạng thái đặt bàn
        $datBan->update(['trang_thai' => 'hoan_tat']);

        // Cập nhật trạng thái bàn thành trống
        if ($datBan->banAn) {
            $datBan->banAn->update(['trang_thai' => 'trong']);
        }

        // Cập nhật trạng thái order
        $order->update(['trang_thai' => 'hoan_thanh']);

        // Chuyển hướng đến trang hiển thị hóa đơn
        return redirect()
            ->route('nhanVien.thanh-toan.hien-thi-hoa-don', $hoaDon->id)
            ->with('success', 'Thanh toán thành công!');
    }

    /**
     * Hiển thị hóa đơn
     */
    public function hienThiHoaDon($id)
    {
        $hoaDon = HoaDon::with([
            'datBan.banAn.khuVuc',
            'datBan.comboBuffet.monTrongCombo.monAn',
            'datBan.orderMon.chiTietOrders.monAn',
            'voucher'
        ])->findOrFail($id);

        $gioVao = $hoaDon->datBan->gio_vao ? Carbon::parse($hoaDon->datBan->gio_vao) : null;
        $gioRa  = $hoaDon->datBan->gio_ra  ? Carbon::parse($hoaDon->datBan->gio_ra)  : null;

        $thoiGianPhucVu = $gioVao && $gioRa ? $gioRa->diffInMinutes($gioVao) : 0;

        // --- Xử lý combo ---
        $tienComboChinh = 0;
        if($hoaDon->datBan->comboBuffet) {
            $tienComboChinh = ($hoaDon->datBan->comboBuffet->gia_co_ban ?? 0) * ($hoaDon->datBan->so_khach ?? 1);
        }

        // --- Xử lý món gọi thêm ---
        $monGoiThemList = [];
        $tongTienMonGoiThem = 0;

        $soKhach = $hoaDon->datBan->so_khach ?? 1;
        $monTrongCombo = [];
        if($hoaDon->datBan->comboBuffet) {
            foreach($hoaDon->datBan->comboBuffet->monTrongCombo as $monCombo) {
                $monTrongCombo[$monCombo->monAn->id] = [
                    'gioiHan' => $monCombo->so_luong ?? 0,
                    'phuPhi' => $monCombo->phu_phi_goi_them ?? 0,
                ];
            }
        }

        foreach($hoaDon->datBan->orderMon as $order) {
            foreach($order->chiTietOrders as $ct) {
                $monId = $ct->monAn->id;
                $soLuong = $ct->so_luong;

                if(isset($monTrongCombo[$monId])) {
                    $gioiHan = $monTrongCombo[$monId]['gioiHan'] * $soKhach;
                    $soLuongVuot = max(0, $soLuong - $gioiHan);
                    $giaGoc = $soLuongVuot > 0 ? $ct->monAn->gia : 0;
                    $phuPhi = $monTrongCombo[$monId]['phuPhi'];
                } else {
                    $soLuongVuot = $soLuong;
                    $giaGoc = $ct->monAn->gia;
                    $phuPhi = 0;
                }

                $thanhTien = ($giaGoc + $phuPhi) * $soLuongVuot;
                $tongTienMonGoiThem += $thanhTien;

                $monGoiThemList[$monId] = [
                    'monAn' => $ct->monAn,
                    'soLuongVuot' => $soLuongVuot,
                    'giaGoc' => $giaGoc,
                    'phuPhi' => $phuPhi,
                    'thanhTien' => $thanhTien
                ];
            }
        }

        $tongTienThucTe = $tienComboChinh + $tongTienMonGoiThem;

        return view('shop.nhanVien.thanh-toan.hien-thi-hoa-don', compact(
            'hoaDon',
            'gioVao',
            'gioRa',
            'thoiGianPhucVu',
            'tienComboChinh',
            'monGoiThemList',
            'tongTienMonGoiThem',
            'tongTienThucTe'
        ));
    }



    /**
     * Trang in hóa đơn
     */
    public function inHoaDon($hoaDonId)
    {
        $hoaDon = HoaDon::with([
            'datBan.banAn.khuVuc',
            'datBan.comboBuffet',
            'datBan.orderMon.chiTietOrders.monAn',
            'voucher'
        ])->findOrFail($hoaDonId);

        $datBan = $hoaDon->datBan;
        
        // Tính giờ vào và giờ ra
        $gioVao = $datBan->gio_den ? Carbon::parse($datBan->gio_den) : null;
        $gioRa = $hoaDon->created_at; // Giờ ra = lúc tạo hóa đơn
        $thoiGianPhucVu = $gioVao ? $gioVao->diffInMinutes($gioRa) : 0;

        // Tính tổng tiền: combo chính + món gọi thêm (sử dụng method tinhTienMonGoiThem)
        $tienComboChinh = 0;
        if ($datBan->comboBuffet) {
            $tienComboChinh = $datBan->comboBuffet->gia_co_ban * ($datBan->so_khach ?? 1);
        }
        
        // Tính tiền món gọi thêm (sử dụng method tinhTienMonGoiThem để đảm bảo logic đúng)
        $tongTienMonGoiThem = $this->tinhTienMonGoiThem($datBan);
        
        // Tổng tiền thực tế = combo chính + món gọi thêm
        $tongTienThucTe = $tienComboChinh + $tongTienMonGoiThem;
        
        // Tổng tiền sau voucher
        $tongTienSauVoucher = $tongTienThucTe - ($hoaDon->tien_giam ?? 0);

        return view('Shop.nhanVien.thanh-toan.in-hoa-don', compact(
            'hoaDon', 'gioVao', 'gioRa', 'thoiGianPhucVu',
            'tongTienThucTe', 'tongTienSauVoucher',
            'tienComboChinh', 'tongTienMonGoiThem'
        ));
    }

    /**
     * Tính tiền món gọi thêm và món combo vượt giới hạn
     */
    private function tinhTienMonGoiThem($datBan)
    {
        $tongTienMonGoiThem = 0;
        
        // Lấy danh sách món trong combo với giới hạn
        $monTrongCombo = collect();
        if ($datBan->comboBuffet) {
            $monTrongCombo = \App\Models\MonTrongCombo::where('combo_id', $datBan->comboBuffet->id)
                ->get()
                ->keyBy('mon_an_id');
        }
        
        // Tính tổng số lượng đã order cho từng món (cả combo và goi_them)
        $tongSoLuongMon = [];
        foreach ($datBan->orderMon as $order) {
            foreach ($order->chiTietOrders as $ct) {
                if ($ct->trang_thai != 'huy_mon') {
                    $monAnId = $ct->mon_an_id;
                    if (!isset($tongSoLuongMon[$monAnId])) {
                        $tongSoLuongMon[$monAnId] = 0;
                    }
                    $tongSoLuongMon[$monAnId] += $ct->so_luong;
                }
            }
        }
        
        // Tính số lượng vượt quá cho từng món
        $soLuongVuotMon = [];
        foreach ($tongSoLuongMon as $monAnId => $tongSoLuong) {
            $monTrongComboItem = $monTrongCombo->get($monAnId);
            if ($monTrongComboItem) {
                $gioiHan = $monTrongComboItem->gioi_han_so_luong ?? 0;

                // Nhân giới hạn với số combo khách đặt
                $soCombo = $datBan->so_khach ?? 1; // hoặc số combo nếu có trường riêng
                $gioiHanThucTe = $gioiHan * $soCombo;

                if($gioiHanThucTe > 0){
                    $soLuongVuotMon[$monAnId] = max(0, $tongSoLuong - $gioiHanThucTe);
                } else {
                    $soLuongVuotMon[$monAnId] = 0;
                }

            } else {
                // Món không có trong combo: không tính vượt quá
                $soLuongVuotMon[$monAnId] = 0;
            }
        }
        
        // Đếm số lượng đã phân bổ cho phần vượt quá (để tránh tính trùng)
        $daPhanBoVuot = [];
        
        // Tính tiền cho từng món
        foreach ($datBan->orderMon as $order) {
            foreach ($order->chiTietOrders as $ct) {
                if ($ct->trang_thai != 'huy_mon') {
                    $monAnId = $ct->mon_an_id;
                    
                    if ($ct->loai_mon == 'goi_them') {
                        // Món gọi thêm: tính tiền bình thường
                        $tongTienMonGoiThem += ($ct->monAn->gia ?? 0) * $ct->so_luong;
                    } elseif ($ct->loai_mon == 'combo') {
                        // Món combo: tính tiền theo giới hạn
                        $monTrongComboItem = $monTrongCombo->get($monAnId);
                        
                        if ($monTrongComboItem) {
                            $gioiHan = $monTrongComboItem->gioi_han_so_luong ?? null;
                            
                            if ($gioiHan !== null && $gioiHan > 0) {
                                // Có giới hạn: chỉ tính tiền cho phần vượt quá
                                $soLuongVuot = $soLuongVuotMon[$monAnId] ?? 0;
                                if ($soLuongVuot > 0) {
                                    // Khởi tạo nếu chưa có
                                    if (!isset($daPhanBoVuot[$monAnId])) {
                                        $daPhanBoVuot[$monAnId] = 0;
                                    }
                                    
                                    // Tính số lượng vượt quá còn lại chưa được phân bổ
                                    $soLuongVuotConLai = $soLuongVuot - $daPhanBoVuot[$monAnId];
                                    
                                    if ($soLuongVuotConLai > 0) {
                                        // Phân bổ số lượng vượt quá cho order này
                                        $soLuongVuotTrongOrder = min($soLuongVuotConLai, $ct->so_luong);
                                        
                                        // Chỉ tính tiền cho phần vượt quá
                                        $tongTienMonGoiThem += ($ct->monAn->gia ?? 0) * $soLuongVuotTrongOrder;
                                        
                                        // Cập nhật số lượng đã phân bổ
                                        $daPhanBoVuot[$monAnId] += $soLuongVuotTrongOrder;
                                    }
                                }
                            } else {
                                // Không có giới hạn hoặc giới hạn = 0: tính tiền bình thường cho toàn bộ số lượng
                                $tongTienMonGoiThem += ($ct->monAn->gia ?? 0) * $ct->so_luong;
                            }
                        } else {
                            // Món không có trong combo: tính tiền bình thường
                            $tongTienMonGoiThem += ($ct->monAn->gia ?? 0) * $ct->so_luong;
                        }
                    }
                }
            }
        }
        
        return $tongTienMonGoiThem;
    }
    
    /**
     * Tính phụ thu tự động: thời gian + gọi món quá giới hạn
     */
    private function tinhPhuThuTuDong($datBan, $thoiGianPhucVu)
    {
        $phuThuThoiGian = 0;
        $phuPhiGoiMon = 0;

        // 1. Tính phụ thu thời gian
        if ($datBan->comboBuffet && $datBan->comboBuffet->thoi_luong_phut) {
            $thoiGianQuyDinh = $datBan->comboBuffet->thoi_luong_phut;
            $thoiGianMienPhi = $thoiGianQuyDinh + 10; // Thời gian quy định + 10 phút miễn phí

            if ($thoiGianPhucVu > $thoiGianMienPhi) {
                $thoiGianVuot = $thoiGianPhucVu - $thoiGianMienPhi;
                $soLan10Phut = ceil($thoiGianVuot / 10); // Làm tròn lên
                $phuThuThoiGian = $soLan10Phut * 30000; // Mỗi 10 phút = 30k
            }
        }

        // 2. Tính phụ phí gọi món quá giới hạn
        if ($datBan->comboBuffet) {
            // Lấy danh sách món trong combo với giới hạn
            $monTrongCombo = \App\Models\MonTrongCombo::where('combo_id', $datBan->comboBuffet->id)
                ->with('monAn')
                ->get();

            // Đếm tổng số lượng đã gọi cho từng món (có thể gọi nhiều lần)
            $tongSoLuongDaGoi = [];
            foreach ($datBan->orderMon as $order) {
                foreach ($order->chiTietOrders as $ct) {
                    if ($ct->trang_thai != 'huy_mon' && $ct->loai_mon == 'combo') {
                        $monAnId = $ct->mon_an_id;
                        if (!isset($tongSoLuongDaGoi[$monAnId])) {
                            $tongSoLuongDaGoi[$monAnId] = 0;
                        }
                        $tongSoLuongDaGoi[$monAnId] += $ct->so_luong;
                    }
                }
            }

            // Tính phụ phí cho từng món vượt quá giới hạn
            foreach ($tongSoLuongDaGoi as $monAnId => $tongSoLuong) {
                $monTrongComboItem = $monTrongCombo->firstWhere('mon_an_id', $monAnId);
                
                if ($monTrongComboItem && $monTrongComboItem->gioi_han_so_luong) {
                    $soLuongVuot = $tongSoLuong - $monTrongComboItem->gioi_han_so_luong;
                    
                    if ($soLuongVuot > 0 && $monTrongComboItem->phu_phi_goi_them) {
                        $phuPhiGoiMon += $soLuongVuot * $monTrongComboItem->phu_phi_goi_them;
                    }
                }
            }
        }

        return $phuThuThoiGian + $phuPhiGoiMon;
    }

    public function vnpayPayment(Request $request, $banId)
    {
        $ban = BanAn::findOrFail($banId);
        $tongTien = $request->input('tong_tien'); // từ form hoặc tính lại
        $vnp_TmnCode = env('VNP_TMNCODE'); // Merchant code
        $vnp_HashSecret = env('VNP_HASHSECRET'); 
        $vnp_Url = env('VNP_URL');
        $vnp_Returnurl = route('nhanVien.thanh-toan.vnpay.callback', ['banId' => $banId]);

        $vnp_TxnRef = time(); // mã giao dịch duy nhất
        $vnp_OrderInfo = "Thanh toán bàn $ban->so_ban";
        $vnp_Amount = $tongTien * 100; // VNPay nhận amount *100 (đơn vị là VND)
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip();

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        ksort($inputData);
        $query = http_build_query($inputData, '', '&', PHP_QUERY_RFC3986);
        $vnp_SecureHash = hash_hmac('sha512', $query, $vnp_HashSecret);
        $vnp_Url = $vnp_Url . "?" . $query . "&vnp_SecureHash=" . $vnp_SecureHash;

        return redirect($vnp_Url);
    }

    public function vnpayCallback(Request $request, $banId)
    {
        $vnp_ResponseCode = $request->get('vnp_ResponseCode');
        $ban = BanAn::findOrFail($banId);

        if($vnp_ResponseCode == "00"){
            $ban->trang_thai = 'da_thanh_toan';
            $ban->save();
            return redirect()->route('nhanVien.ban-an.index')->with('success','Thanh toán VNPay thành công!');
        } else {
            return redirect()->route('nhanVien.ban-an.index')->with('error','Thanh toán VNPay thất bại!');
        }
    }

}
