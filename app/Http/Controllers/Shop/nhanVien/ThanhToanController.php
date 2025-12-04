<?php

namespace App\Http\Controllers\Shop\NhanVien;

use App\Http\Controllers\Controller;
use App\Models\OrderMon;
use App\Models\DatBan;
use App\Models\HoaDon;
use App\Models\ChiTietHoaDon;
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
                'chiTietDatBan.combo',
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

        // Tính tổng tiền combo: tính từng combo với số lượng tương ứng, giảm 50% cho trẻ em
        $tienComboChinh = 0;
        $soTreEm = $datBan->tre_em ?? 0;
        $soNguoiDaXuLy = 0; // Đếm số người đã xử lý
        
        foreach ($datBan->chiTietDatBan as $chiTiet) {
            if ($chiTiet->combo) {
                $giaComboGoc = $chiTiet->combo->gia_co_ban ?? 0;
                $soLuongCombo = $chiTiet->so_luong ?? 1;
                
                // Tính số người được giảm giá trong combo này
                $soNguoiDuocGiam = 0;
                if($soTreEm > 0 && $soNguoiDaXuLy < $soTreEm) {
                    // Số người được giảm = min(số trẻ em còn lại, số lượng combo này)
                    $soTreEmConLai = $soTreEm - $soNguoiDaXuLy;
                    $soNguoiDuocGiam = min($soTreEmConLai, $soLuongCombo);
                }
                
                // Tính thành tiền: người được giảm giá (50%) + người không giảm giá (100%)
                $soNguoiKhongGiam = $soLuongCombo - $soNguoiDuocGiam;
                $thanhTienCombo = ($giaComboGoc * 0.5 * $soNguoiDuocGiam) + ($giaComboGoc * $soNguoiKhongGiam);
                
                $tienComboChinh += $thanhTienCombo;
                $soNguoiDaXuLy += $soLuongCombo; // Tăng số người đã xử lý
            }
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

        // Không tính phụ thu tự động nữa (đã xóa logic phụ thu thời gian vượt quá)
        $phuThuTuDong = 0;

        // Lấy danh sách voucher hợp lệ
        $vouchers = Voucher::where('trang_thai', 'dang_ap_dung')
            ->where('ngay_ket_thuc', '>=', now())
            ->whereRaw('so_luong > so_luong_da_dung')
            ->get();

        // Kiểm tra xem VNPay đã được cấu hình chưa
        $vnp_TmnCode = config('services.vnpay.tmn_code') ?: env('VNP_TMNCODE');
        $vnp_HashSecret = config('services.vnpay.hash_secret') ?: env('VNP_HASHSECRET');
        $vnpayConfigured = !empty($vnp_TmnCode) && !empty($vnp_HashSecret);
        
        return view('Shop.nhanVien.thanh-toan.thanh-toan-tu-ban', compact(
            'ban', 'datBan', 'tongTienOrder', 'tienCoc', 'vouchers', 'gioVao', 'gioRa', 'thoiGianPhucVu', 'phuThuTuDong', 'vnpayConfigured'
        ));
    }

    /**
     * Lưu hóa đơn từ ban_id
     */
    public function luuThanhToanTuBan(Request $request, $banId)
    {
        $request->validate([
            'phuong_thuc_tt' => 'required|string|in:tien_mat,chuyen_khoan,the_ATM,vnpay',
            'phu_thu' => 'nullable|numeric|min:0',
            'voucher_id' => 'nullable|exists:vouchers,id',
            'tien_khach_dua' => 'nullable|numeric|min:0',
        ]);

        $ban = BanAn::findOrFail($banId);
        
        // Tìm đặt bàn đang hoạt động
        $datBan = DatBan::where('ban_id', $banId)
            ->whereIn('trang_thai', ['khach_da_den', 'dang_phuc_vu'])
            ->with([
                'chiTietDatBan.combo',
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

        // Tính tổng tiền combo: tính từng combo với số lượng tương ứng, giảm 50% cho trẻ em
        $tienComboChinh = 0;
        $soTreEm = $datBan->tre_em ?? 0;
        $soNguoiDaXuLy = 0; // Đếm số người đã xử lý
        
        foreach ($datBan->chiTietDatBan as $chiTiet) {
            if ($chiTiet->combo) {
                $giaComboGoc = $chiTiet->combo->gia_co_ban ?? 0;
                $soLuongCombo = $chiTiet->so_luong ?? 1;
                
                // Tính số người được giảm giá trong combo này
                $soNguoiDuocGiam = 0;
                if($soTreEm > 0 && $soNguoiDaXuLy < $soTreEm) {
                    // Số người được giảm = min(số trẻ em còn lại, số lượng combo này)
                    $soTreEmConLai = $soTreEm - $soNguoiDaXuLy;
                    $soNguoiDuocGiam = min($soTreEmConLai, $soLuongCombo);
                }
                
                // Tính thành tiền: người được giảm giá (50%) + người không giảm giá (100%)
                $soNguoiKhongGiam = $soLuongCombo - $soNguoiDuocGiam;
                $thanhTienCombo = ($giaComboGoc * 0.5 * $soNguoiDuocGiam) + ($giaComboGoc * $soNguoiKhongGiam);
                
                $tienComboChinh += $thanhTienCombo;
                $soNguoiDaXuLy += $soLuongCombo; // Tăng số người đã xử lý
            }
        }
        
        // Tính tiền món gọi thêm và món combo vượt giới hạn
        $tongTienMonGoiThem = $this->tinhTienMonGoiThem($datBan);
        
        // Tổng tiền thực tế = combo chính + món gọi thêm
        $tongTienOrder = $tienComboChinh + $tongTienMonGoiThem;
        
        $tienCoc = (float) ($datBan->tien_coc ?? 0);
        
        // Tính thời gian phục vụ (chỉ để lưu vào chi tiết, không dùng để tính phụ thu)
        $gioVao = Carbon::parse($datBan->gio_den);
        $gioRa = now();
        $thoiGianPhucVu = $gioVao->diffInMinutes($gioRa);
        
        // Phụ thu tự động = 0 (đã loại bỏ phụ thu thời gian)
        $phuThuTuDong = 0;
        
        // Phụ thu = phụ thu thủ công (nếu có)
        $phuThuThucCong = (float) ($request->phu_thu ?? 0);
        $phuThu = $phuThuThucCong;

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

        // Đã loại bỏ logic tính phụ thu thời gian - set các giá trị = 0
        $thoiGianQuyDinh = null;
        $thoiGianVuot = 0;
        $soLan10Phut = 0;
        $phuThuThoiGian = 0;
        
        // Lấy combo đầu tiên để lưu thông tin combo vào chi tiết hóa đơn
        $comboDauTien = $datBan->chiTietDatBan->first();

        // Chuẩn bị danh sách món đã gọi
        // Món combo không có giới hạn và phụ phí nữa, chỉ cần kiểm tra xem món có trong combo không
        $monTrongComboIds = [];
        
        foreach ($datBan->chiTietDatBan as $chiTiet) {
            if ($chiTiet->combo) {
                $monTrongCombo = \App\Models\MonTrongCombo::where('combo_id', $chiTiet->combo->id)->get();
                foreach ($monTrongCombo as $mtc) {
                    $monAnId = $mtc->mon_an_id;
                    if (!in_array($monAnId, $monTrongComboIds)) {
                        $monTrongComboIds[] = $monAnId;
                    }
                }
            }
        }
        
        // Tính tổng số lượng đã order cho từng món
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
        
        // Tạo danh sách món với thông tin chi tiết
        $danhSachMon = [];
        $stt = 1;
        foreach ($tongSoLuongMon as $monAnId => $tongSoLuong) {
            $monAn = \App\Models\MonAn::find($monAnId);
            
            if (!$monAn) continue;
            
            $laMonCombo = in_array($monAnId, $monTrongComboIds);
            
            if ($laMonCombo) {
                // Món thuộc combo: luôn miễn phí (không có giới hạn, không có phụ phí)
                $donGiaHienThi = 0;
                $tienMon = 0;
                $phuPhi = 0;
                $tienPhuPhiTong = 0;
                $soLuongVuot = 0;
                $tongGioiHan = null;
            } else {
                // Món không thuộc combo: tính tiền bình thường
                $donGiaHienThi = $monAn->gia ?? 0;
                $tienMon = $donGiaHienThi * $tongSoLuong;
                $phuPhi = 0;
                $tienPhuPhiTong = 0;
                $soLuongVuot = 0;
                $tongGioiHan = null;
            }
            
            $danhSachMon[] = [
                'stt' => $stt++,
                'ten_mon' => $monAn->ten_mon,
                'so_luong' => $tongSoLuong,
                'gioi_han' => $tongGioiHan,
                'don_gia' => $donGiaHienThi,
                'phu_phi' => $phuPhi,
                'phu_phi_tong' => $tienPhuPhiTong,
                'so_luong_vuot' => $soLuongVuot,
                'thanh_tien' => $tienMon,
                'la_mon_combo' => $laMonCombo,
                'vuot_gioi_han' => false,
            ];
        }

        // Tính lại tổng tiền món gọi thêm từ danh sách món để đảm bảo tính nhất quán
        $tongTienMonGoiThemTuDanhSach = 0;
        foreach ($danhSachMon as $mon) {
            $tongTienMonGoiThemTuDanhSach += $mon['thanh_tien'];
        }
        
        // Tính lại tổng tiền combo + món từ danh sách món
        $tongTienComboMonTuDanhSach = $tienComboChinh + $tongTienMonGoiThemTuDanhSach;
        
        // Sử dụng giá trị tính từ danh sách món để đảm bảo tính nhất quán
        $tongTienOrder = $tongTienComboMonTuDanhSach;

        // Tính lại tiền phải thanh toán với giá trị mới
        $daThanhToan = $tongTienOrder - $tienGiam + $phuThu - $tienCoc;
        if ($daThanhToan < 0) $daThanhToan = 0;

        // Tính tiền khách đưa và tiền trả lại
        $tienKhachDua = (float) ($request->tien_khach_dua ?? 0);
        $tienTraLai = 0;
        if ($request->phuong_thuc_tt == 'tien_mat' && $tienKhachDua > 0) {
            $tienTraLai = max(0, $tienKhachDua - $daThanhToan);
        }

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

        // Tính tổng tiền món gọi thêm (theo trạng thái)
        $tongTienMonGoiThem = $this->tinhTienMonGoiThem($datBan);
        
        // Tính tổng tiền sau voucher
        $tongTienSauVoucher = $tongTienOrder - $tienGiam;
        if($tongTienSauVoucher < 0) $tongTienSauVoucher = 0;

        // Lưu chi tiết hóa đơn
        ChiTietHoaDon::create([
            'hoa_don_id' => $hoaDon->id,
            'ten_khach' => $datBan->ten_khach,
            'sdt_khach' => $datBan->sdt_khach,
            'email_khach' => $datBan->email_khach,
            'so_khach' => $datBan->so_khach ?? 1,
            'nguoi_lon' => $datBan->nguoi_lon ?? 0,
            'tre_em' => $datBan->tre_em ?? 0,
            'ban_so' => $datBan->banAn->so_ban ?? null,
            'khu_vuc' => $datBan->banAn->khuVuc->ten_khu_vuc ?? null,
            'tang' => $datBan->banAn->khuVuc->tang ?? null,
            'so_ghe' => $datBan->banAn->so_ghe ?? null,
            'ma_dat_ban' => $datBan->ma_dat_ban,
            'gio_vao' => $gioVao,
            'gio_ra' => $gioRa,
            'thoi_gian_phuc_vu_phut' => $thoiGianPhucVu,
            'thoi_gian_quy_dinh_phut' => $thoiGianQuyDinh,
            'thoi_gian_vuot_phut' => $thoiGianVuot,
            'so_lan_10_phut' => $soLan10Phut,
            'phu_thu_thoi_gian' => $phuThuThoiGian,
            'ten_combo' => $comboDauTien && $comboDauTien->combo ? $comboDauTien->combo->ten_combo : null,
            'gia_combo_per_person' => $comboDauTien && $comboDauTien->combo ? $comboDauTien->combo->gia_co_ban : 0,
            'tong_tien_combo' => $tienComboChinh,
            'tong_tien_mon_goi_them' => $tongTienMonGoiThem,
            'danh_sach_mon' => $danhSachMon,
            'tong_tien_combo_mon' => $tongTienOrder,
            'tien_giam_voucher' => $tienGiam,
            'tong_tien_sau_voucher' => $tongTienSauVoucher,
            'tien_coc' => $tienCoc,
            'phu_thu_tu_dong' => $phuThuTuDong,
            'phu_thu_thu_cong' => $phuThuThucCong,
            'tong_phu_thu' => $phuThu,
            'phai_thanh_toan' => $daThanhToan,
            'tien_khach_dua' => $tienKhachDua > 0 ? $tienKhachDua : null,
            'tien_tra_lai' => $tienTraLai > 0 ? $tienTraLai : null,
            'phuong_thuc_tt' => $request->phuong_thuc_tt,
            'ma_voucher' => $voucher ? $voucher->ma_voucher : null,
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
            'datBan.banAn.khuVuc'
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
        
        // Tính tiền món gọi thêm theo trạng thái (sử dụng hàm tinhTienMonGoiThem)
        $tongTienMonGoiThem = $this->tinhTienMonGoiThem($datBan);
        
        // Tổng tiền thực tế = combo chính + món gọi thêm
        $tongTienOrder = $tienComboChinh + $tongTienMonGoiThem;
        
        $tienCoc = (float) ($datBan->tien_coc ?? 0);
        
        // Tính thời gian phục vụ (chỉ để lưu vào chi tiết, không dùng để tính phụ thu)
        $gioVao = Carbon::parse($datBan->gio_den);
        $gioRa = now();
        $thoiGianPhucVu = $gioVao->diffInMinutes($gioRa);
        
        // Phụ thu tự động = 0 (đã loại bỏ phụ thu thời gian)
        $phuThuTuDong = 0;
        
        // Phụ thu = phụ thu thủ công (nếu có)
        $phuThuThucCong = (float) ($request->phu_thu ?? 0);
        $phuThu = $phuThuThucCong;

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

        // Tính tiền khách đưa và tiền trả lại
        $tienKhachDua = (float) ($request->tien_khach_dua ?? 0);
        $tienTraLai = 0;
        if ($request->phuong_thuc_tt == 'tien_mat' && $tienKhachDua > 0) {
            $tienTraLai = max(0, $tienKhachDua - $daThanhToan);
        }

        // Tính tổng tiền sau voucher
        $tongTienSauVoucher = $tongTienOrder - $tienGiam;
        if($tongTienSauVoucher < 0) $tongTienSauVoucher = 0;

        // Đã loại bỏ logic tính phụ thu thời gian - set các giá trị = 0
        $thoiGianQuyDinh = null;
        $thoiGianVuot = 0;
        $soLan10Phut = 0;
        $phuThuThoiGian = 0;
        
        // Lấy combo đầu tiên để lưu thông tin combo vào chi tiết hóa đơn
        $comboDauTien = $datBan->chiTietDatBan->first();

        // Chuẩn bị danh sách món đã gọi
        $danhSachMon = [];
        $stt = 1;
        foreach ($datBan->orderMon as $orderItem) {
            foreach ($orderItem->chiTietOrders as $ct) {
                if ($ct->trang_thai != 'huy_mon' && $ct->monAn) {
                    $danhSachMon[] = [
                        'stt' => $stt++,
                        'ten_mon' => $ct->monAn->ten_mon,
                        'so_luong' => $ct->so_luong,
                        'gioi_han' => null,
                        'don_gia' => $ct->loai_mon == 'goi_them' ? ($ct->monAn->gia ?? 0) : 0,
                        'phu_phi' => 0,
                        'phu_phi_tong' => 0,
                        'so_luong_vuot' => 0,
                        'thanh_tien' => $ct->loai_mon == 'goi_them' ? (($ct->monAn->gia ?? 0) * $ct->so_luong) : 0,
                        'la_mon_combo' => $ct->loai_mon == 'combo',
                        'vuot_gioi_han' => false,
                    ];
                }
            }
        }

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

        // Lưu chi tiết hóa đơn
        ChiTietHoaDon::create([
            'hoa_don_id' => $hoaDon->id,
            'ten_khach' => $datBan->ten_khach,
            'sdt_khach' => $datBan->sdt_khach,
            'email_khach' => $datBan->email_khach,
            'so_khach' => $datBan->so_khach ?? 1,
            'nguoi_lon' => $datBan->nguoi_lon ?? 0,
            'tre_em' => $datBan->tre_em ?? 0,
            'ban_so' => $datBan->banAn->so_ban ?? null,
            'khu_vuc' => $datBan->banAn->khuVuc->ten_khu_vuc ?? null,
            'tang' => $datBan->banAn->khuVuc->tang ?? null,
            'so_ghe' => $datBan->banAn->so_ghe ?? null,
            'ma_dat_ban' => $datBan->ma_dat_ban,
            'gio_vao' => $gioVao,
            'gio_ra' => $gioRa,
            'thoi_gian_phuc_vu_phut' => $thoiGianPhucVu,
            'thoi_gian_quy_dinh_phut' => $thoiGianQuyDinh,
            'thoi_gian_vuot_phut' => $thoiGianVuot,
            'so_lan_10_phut' => $soLan10Phut,
            'phu_thu_thoi_gian' => $phuThuThoiGian,
            'ten_combo' => $datBan->comboBuffet ? $datBan->comboBuffet->ten_combo : null,
            'gia_combo_per_person' => $datBan->comboBuffet ? $datBan->comboBuffet->gia_co_ban : 0,
            'tong_tien_combo' => $tienComboChinh,
            'tong_tien_mon_goi_them' => $tongTienMonGoiThem,
            'danh_sach_mon' => $danhSachMon,
            'tong_tien_combo_mon' => $tongTienOrder,
            'tien_giam_voucher' => $tienGiam,
            'tong_tien_sau_voucher' => $tongTienSauVoucher,
            'tien_coc' => $tienCoc,
            'phu_thu_tu_dong' => $phuThuTuDong,
            'phu_thu_thu_cong' => $phuThuThucCong,
            'tong_phu_thu' => $phuThu,
            'phai_thanh_toan' => $daThanhToan,
            'tien_khach_dua' => $tienKhachDua > 0 ? $tienKhachDua : null,
            'tien_tra_lai' => $tienTraLai > 0 ? $tienTraLai : null,
            'phuong_thuc_tt' => $request->phuong_thuc_tt,
            'ma_voucher' => $voucher ? $voucher->ma_voucher : null,
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
            'datBan.chiTietDatBan.combo',
            'datBan.orderMon.chiTietOrders.monAn',
            'voucher',
            'chiTietHoaDon'
        ])->findOrFail($id);

        // Nếu có chi tiết hóa đơn, sử dụng dữ liệu từ đó
        if ($hoaDon->chiTietHoaDon) {
            $chiTiet = $hoaDon->chiTietHoaDon;
            
            return view('Shop.nhanVien.thanh-toan.hien-thi-hoa-don', compact(
                'hoaDon',
                'chiTiet'
            ));
        }

        // Fallback: tính toán lại nếu chưa có chi tiết (cho các hóa đơn cũ)
        // Chỉ tính các giá trị cần thiết cho hiển thị, không tính lại tổng tiền
        $soKhach = $hoaDon->datBan->so_khach ?? 1;
        $gioVao = $hoaDon->datBan->gio_den ? Carbon::parse($hoaDon->datBan->gio_den) : null;
        $gioRa = $hoaDon->created_at;
        $thoiGianPhucVu = $gioVao ? $gioVao->diffInMinutes($gioRa) : 0;

        return view('Shop.nhanVien.thanh-toan.hien-thi-hoa-don', compact(
            'hoaDon',
            'soKhach',
            'gioVao',
            'gioRa',
            'thoiGianPhucVu'
        ));
    }



    /**
     * Trang in hóa đơn
     */
    public function inHoaDon($hoaDonId)
    {
        $hoaDon = HoaDon::with([
            'datBan.banAn.khuVuc',
            'datBan.chiTietDatBan.combo',
            'datBan.orderMon.chiTietOrders.monAn',
            'voucher',
            'chiTietHoaDon'
        ])->findOrFail($hoaDonId);

        // Nếu có chi tiết hóa đơn, sử dụng dữ liệu từ đó
        if ($hoaDon->chiTietHoaDon) {
            return view('Shop.nhanVien.thanh-toan.in-hoa-don', compact('hoaDon'));
        }

        // Fallback: tính toán lại cho hóa đơn cũ
        // Chỉ tính các giá trị cần thiết cho hiển thị, không tính lại tổng tiền
        $datBan = $hoaDon->datBan;
        
        // Tính giờ vào và giờ ra
        $gioVao = $datBan->gio_den ? Carbon::parse($datBan->gio_den) : null;
        $gioRa = $hoaDon->created_at;
        $thoiGianPhucVu = $gioVao ? $gioVao->diffInMinutes($gioRa) : 0;

        return view('Shop.nhanVien.thanh-toan.in-hoa-don', compact(
            'hoaDon', 'gioVao', 'gioRa', 'thoiGianPhucVu'
        ));
    }

    /**
     * Tính tiền món gọi thêm và món combo vượt giới hạn
     * Tính theo trạng thái: đã lên/đang nấu = 100%, chờ bếp = 0%, đã hủy = 0%
     */
    private function tinhTienMonGoiThem($datBan)
    {
        $tongTienMonGoiThem = 0;
        
        // Lấy danh sách món trong combo (để kiểm tra món có thuộc combo không)
        $monTrongComboIds = [];
        foreach ($datBan->chiTietDatBan as $chiTiet) {
            if ($chiTiet->combo) {
                $monTrongCombo = \App\Models\MonTrongCombo::where('combo_id', $chiTiet->combo->id)->get();
                foreach ($monTrongCombo as $mtc) {
                    $monAnId = $mtc->mon_an_id;
                    if (!in_array($monAnId, $monTrongComboIds)) {
                        $monTrongComboIds[] = $monAnId;
                    }
                }
            }
        }
        
        // Tính tiền cho từng món theo trạng thái
        foreach ($datBan->orderMon as $order) {
            foreach ($order->chiTietOrders as $ct) {
                $monAnId = $ct->mon_an_id;
                
                // Kiểm tra xem món có thuộc combo không
                $laMonCombo = in_array($monAnId, $monTrongComboIds);
                
                if ($ct->loai_mon == 'goi_them' || !$laMonCombo) {
                    // Món gọi thêm hoặc không thuộc combo: tính tiền theo trạng thái
                    $donGia = $ct->monAn->gia ?? 0;
                    
                    if ($ct->trang_thai == 'da_len_mon') {
                        // Đã lên: 100% giá
                        $tongTienMonGoiThem += $donGia * $ct->so_luong;
                    } elseif ($ct->trang_thai == 'dang_che_bien') {
                        // Đang nấu: 100% giá
                        $tongTienMonGoiThem += $donGia * $ct->so_luong;
                    } elseif ($ct->trang_thai == 'cho_bep') {
                        // Chờ bếp: 0 đồng (chưa xác nhận, có thể hủy)
                        // Không tính tiền
                    } elseif ($ct->trang_thai == 'huy_mon') {
                        // Đã hủy: 0 đồng
                        // Không tính tiền
                    }
                }
                // Món combo: không tính tiền (luôn miễn phí)
            }
        }
        
        return $tongTienMonGoiThem;
    }
    
    /**
     * Tính phụ thu tự động: đã loại bỏ phụ thu thời gian, luôn trả về 0
     */
    private function tinhPhuThuTuDong($datBan, $thoiGianPhucVu)
    {
        // Đã loại bỏ logic tính phụ thu thời gian
        return 0;
    }

    public function vnpayPayment(Request $request, $banId)
    {
        $ban = BanAn::findOrFail($banId);
        
        // Tìm đặt bàn đang hoạt động
        $datBan = DatBan::where('ban_id', $banId)
            ->whereIn('trang_thai', ['khach_da_den', 'dang_phuc_vu', 'da_xac_nhan'])
            ->with([
                'chiTietDatBan.combo',
                'orderMon.chiTietOrders.monAn'
            ])
            ->latest()
            ->first();
        
        if (!$datBan) {
            return redirect()
                ->route('nhanVien.ban-an.index')
                ->with('error', 'Không tìm thấy đặt bàn!');
        }
        
        // Lấy tổng tiền từ request hoặc tính lại
        $tongTien = $request->input('tong_tien') ?? $request->input('tongTien');
        
        // Nếu không có trong request, tính lại từ datBan
        if (!$tongTien || $tongTien <= 0) {
            // Tính tiền combo với giảm 50% cho trẻ em
            $tienComboChinh = 0;
            $soTreEm = $datBan->tre_em ?? 0;
            $soNguoiDaXuLy = 0;
            foreach ($datBan->chiTietDatBan as $chiTiet) {
                if ($chiTiet->combo) {
                    $giaComboGoc = $chiTiet->combo->gia_co_ban ?? 0;
                    $soLuongCombo = $chiTiet->so_luong ?? 1;
                    
                    // Tính số người được giảm giá
                    $soNguoiDuocGiam = 0;
                    if($soTreEm > 0 && $soNguoiDaXuLy < $soTreEm) {
                        $soTreEmConLai = $soTreEm - $soNguoiDaXuLy;
                        $soNguoiDuocGiam = min($soTreEmConLai, $soLuongCombo);
                    }
                    
                    $soNguoiKhongGiam = $soLuongCombo - $soNguoiDuocGiam;
                    $thanhTienCombo = ($giaComboGoc * 0.5 * $soNguoiDuocGiam) + ($giaComboGoc * $soNguoiKhongGiam);
                    $tienComboChinh += $thanhTienCombo;
                    $soNguoiDaXuLy += $soLuongCombo;
                }
            }
            
            $tongTienMonGoiThem = $this->tinhTienMonGoiThem($datBan);
            $tongTien = $tienComboChinh + $tongTienMonGoiThem;
        }
        
        // Lấy cấu hình VNPay từ config hoặc env
        // Đọc từ nhiều nguồn để đảm bảo đọc được
        $vnp_TmnCode = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret'); 
        $vnp_Url = config('services.vnpay.url');
        
        // Fallback: đọc trực tiếp từ env nếu config không có
        if (empty($vnp_TmnCode)) {
            $vnp_TmnCode = env('VNP_TMNCODE');
        }
        if (empty($vnp_HashSecret)) {
            $vnp_HashSecret = env('VNP_HASHSECRET');
        }
        if (empty($vnp_Url)) {
            $vnp_Url = env('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        }
        
        // Kiểm tra các biến môi trường VNPay (chỉ kiểm tra TMN_CODE và HASH_SECRET, URL có giá trị mặc định)
        if (empty($vnp_TmnCode) || empty($vnp_HashSecret)) {
            return redirect()
                ->route('nhanVien.thanh-toan.ban', $banId)
                ->with('error', 'Cấu hình VNPay chưa đầy đủ. Vui lòng kiểm tra file .env có các biến: VNP_TMNCODE, VNP_HASHSECRET. Sau đó chạy: php artisan config:clear và restart server');
        }
        
        // Đảm bảo VNP_URL có protocol
        $vnp_Url = trim($vnp_Url);
        if (!preg_match('/^https?:\/\//', $vnp_Url)) {
            $vnp_Url = 'https://' . ltrim($vnp_Url, '/');
        }
        $vnp_Url = rtrim($vnp_Url, '/');
        
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
