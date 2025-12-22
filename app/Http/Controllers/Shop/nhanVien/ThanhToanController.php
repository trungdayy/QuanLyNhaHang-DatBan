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
use Illuminate\Support\Str;
use PayOS\PayOS;
use PayOS\Models\V2\PaymentRequests\CreatePaymentLinkRequest;
use PayOS\Exceptions\APIException;

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

        // Tự động hủy tất cả món đang chờ bếp khi vào trang thanh toán
        foreach ($datBan->orderMon as $order) {
            foreach ($order->chiTietOrders as $ct) {
                if ($ct->trang_thai == 'cho_bep') {
                    $ct->update(['trang_thai' => 'huy_mon']);
                }
            }
        }

        // Reload lại dữ liệu sau khi hủy để đảm bảo tính toán đúng
        $datBan->refresh();
        $datBan->load([
            'chiTietDatBan.combo',
            'orderMon.chiTietOrders.monAn',
            'banAn.khuVuc',
            'hoaDon'
        ]);

        // Tính tổng tiền combo: tính từng combo với số lượng tương ứng, giảm 50% cho trẻ em
        $tienComboChinh = 0;
        $soTreEm = $datBan->tre_em ?? 0;
        $soNguoiDaXuLy = 0; // Đếm số người đã xử lý để phân bổ giảm giá cho trẻ em

        foreach ($datBan->chiTietDatBan as $chiTiet) {
            if ($chiTiet->combo) {
                $giaComboGoc = $chiTiet->combo->gia_co_ban;
                $soLuongCombo = $chiTiet->so_luong ?? 1;

                // Tính số người được giảm giá trong combo này (trẻ em)
                $soNguoiDuocGiam = 0;
                if ($soTreEm > 0 && $soNguoiDaXuLy < $soTreEm) {
                    // Số người được giảm = min(số trẻ em còn lại, số lượng combo này)
                    $soTreEmConLai = $soTreEm - $soNguoiDaXuLy;
                    $soNguoiDuocGiam = min($soTreEmConLai, $soLuongCombo);
                }

                // Tính số người không giảm giá (người lớn)
                $soNguoiKhongGiam = $soLuongCombo - $soNguoiDuocGiam;

                // Tính tiền combo: (giá gốc * 0.5 * số trẻ em) + (giá gốc * số người lớn)
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

        // Tính chi tiết phụ thu thời gian (lấy thời gian quy định từ combo đầu tiên hoặc combo có thời gian dài nhất)
        $thoiGianQuyDinh = 0;
        $thoiGianMienPhi = 0;
        $thoiGianVuot = 0;
        $soLan10Phut = 0;
        $phuThuThoiGian = 0;

        // Lấy thời gian quy định từ combo đầu tiên (hoặc combo có thời gian dài nhất)
        $comboDauTien = $datBan->chiTietDatBan->first();
        if ($comboDauTien && $comboDauTien->combo && $comboDauTien->combo->thoi_luong_phut) {
            $thoiGianQuyDinh = $comboDauTien->combo->thoi_luong_phut;
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

        // Kiểm tra xem VNPay đã được cấu hình chưa
        $vnp_TmnCode = config('services.vnpay.tmn_code') ?: env('VNP_TMNCODE');
        $vnp_HashSecret = config('services.vnpay.hash_secret') ?: env('VNP_HASHSECRET');
        $vnpayConfigured = !empty($vnp_TmnCode) && !empty($vnp_HashSecret);

        return view('Shop.nhanVien.thanh-toan.thanh-toan-tu-ban', compact(
            'ban',
            'datBan',
            'tongTienOrder',
            'tienCoc',
            'vouchers',
            'gioVao',
            'gioRa',
            'thoiGianPhucVu',
            'phuThuTuDong',
            'vnpayConfigured'
        ));
    }

    /**
     * Lưu hóa đơn với trạng thái "chưa thanh toán" (thanh toán sau)
     */
    public function luuThanhToanSau(Request $request, $banId)
    {
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

        // Tự động hủy tất cả món đang chờ bếp trước khi thanh toán
        foreach ($datBan->orderMon as $order) {
            foreach ($order->chiTietOrders as $ct) {
                if ($ct->trang_thai == 'cho_bep') {
                    $ct->update(['trang_thai' => 'huy_mon']);
                }
            }
        }

        // Reload lại dữ liệu sau khi hủy để đảm bảo tính toán đúng
        $datBan->refresh();
        $datBan->load([
            'chiTietDatBan.combo',
            'orderMon.chiTietOrders.monAn',
            'banAn',
            'hoaDon'
        ]);

        // Tính tổng tiền combo
        $tienComboChinh = 0;
        $soTreEm = $datBan->tre_em ?? 0;
        $soNguoiDaXuLy = 0;

        foreach ($datBan->chiTietDatBan as $chiTiet) {
            if ($chiTiet->combo) {
                $giaComboGoc = $chiTiet->combo->gia_co_ban;
                $soLuongCombo = $chiTiet->so_luong ?? 1;

                $soNguoiDuocGiam = 0;
                if ($soTreEm > 0 && $soNguoiDaXuLy < $soTreEm) {
                    $soTreEmConLai = $soTreEm - $soNguoiDaXuLy;
                    $soNguoiDuocGiam = min($soTreEmConLai, $soLuongCombo);
                }

                $soNguoiKhongGiam = $soLuongCombo - $soNguoiDuocGiam;
                $thanhTienCombo = ($giaComboGoc * 0.5 * $soNguoiDuocGiam) + ($giaComboGoc * $soNguoiKhongGiam);
                $tienComboChinh += $thanhTienCombo;
                $soNguoiDaXuLy += $soLuongCombo;
            }
        }

        // Tính tiền món gọi thêm
        $tongTienMonGoiThem = $this->tinhTienMonGoiThem($datBan);
        $tongTienOrder = $tienComboChinh + $tongTienMonGoiThem;

        $tienCoc = (float) ($datBan->tien_coc ?? 0);
        $phuThu = 0; // Không có phụ thu khi thanh toán sau

        // Tính voucher nếu có
        $voucher = null;
        $tienGiam = 0;
        if ($request->filled('voucher_id')) {
            $voucher = Voucher::find($request->voucher_id);
            if ($voucher) {
                // Kiểm tra điều kiện voucher
                if ($voucher->trang_thai != 'dang_ap_dung') {
                    return redirect()->back()
                        ->with('error', 'Voucher "' . $voucher->ma_voucher . '" không còn hiệu lực!')
                        ->withInput();
                }
                
                if ($voucher->ngay_ket_thuc < now()) {
                    return redirect()->back()
                        ->with('error', 'Voucher "' . $voucher->ma_voucher . '" đã hết hạn!')
                        ->withInput();
                }
                
                if ($voucher->so_luong <= $voucher->so_luong_da_dung) {
                    return redirect()->back()
                        ->with('error', 'Voucher "' . $voucher->ma_voucher . '" đã hết số lượng sử dụng!')
                        ->withInput();
                }
                
                // Tính tiền giảm nếu voucher hợp lệ
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
        }

        $daThanhToan = $tongTienOrder - $tienGiam + $phuThu - $tienCoc;
        if ($daThanhToan < 0) $daThanhToan = 0;

        // Tính thời gian phục vụ
        $gioVao = Carbon::parse($datBan->gio_den);
        $gioRa = now();
        $thoiGianPhucVu = $gioVao->diffInMinutes($gioRa);

        // Tạo danh sách món
        $danhSachMon = [];
        foreach ($datBan->orderMon as $order) {
            foreach ($order->chiTietOrders as $ct) {
                if ($ct->trang_thai != 'huy_mon' && $ct->monAn) {
                    $danhSachMon[] = [
                        'ten_mon' => $ct->monAn->ten_mon,
                        'so_luong' => $ct->so_luong,
                        'don_gia' => $ct->monAn->gia,
                        'thanh_tien' => $ct->so_luong * $ct->monAn->gia,
                    ];
                }
            }
        }

        // Tạo hóa đơn với trạng thái "chưa thanh toán"
        $hoaDon = HoaDon::create([
            'dat_ban_id' => $datBan->id,
            'voucher_id' => $voucher ? $voucher->id : null,
            'ma_hoa_don' => 'HD' . date('YmdHis') . '-' . $datBan->id,
            'tong_tien' => $tongTienOrder,
            'tien_giam' => $tienGiam,
            'phu_thu' => $phuThu,
            'da_thanh_toan' => 0, // Chưa thanh toán nên = 0
            'phuong_thuc_tt' => 'chua_thanh_toan',
            'trang_thai' => 'chua_thanh_toan',
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
            'tong_tien_combo' => $tienComboChinh,
            'tong_tien_mon_goi_them' => $tongTienMonGoiThem,
            'danh_sach_mon' => json_encode($danhSachMon),
            'tong_tien_combo_mon' => $tongTienOrder,
            'tien_giam_voucher' => $tienGiam,
            'tong_tien_sau_voucher' => $tongTienOrder - $tienGiam,
            'tien_coc' => $tienCoc,
            'tong_phu_thu' => $phuThu,
            'phai_thanh_toan' => $daThanhToan,
            'phuong_thuc_tt' => 'chua_thanh_toan',
            'ma_voucher' => $voucher ? $voucher->ma_voucher : null,
        ]);

        // Cập nhật voucher
        if ($voucher) {
            $voucher->increment('so_luong_da_dung');
        }

        // Cập nhật trạng thái đặt bàn
        $datBan->update(['trang_thai' => 'hoan_tat']);

        // Cập nhật trạng thái bàn thành trống và tạo mới mã QR
        if ($datBan->banAn) {
            $newUniqueCode = Str::random(12);
            $baseUrl = config('app.url');

            $datBan->banAn->update([
                'trang_thai' => 'trong',
                'ma_qr' => $newUniqueCode,
                'duong_dan_qr' => $baseUrl . '/oderqr/menu/' . $newUniqueCode,
            ]);
        }

        // Cập nhật trạng thái tất cả order
        foreach ($datBan->orderMon as $order) {
            $order->update(['trang_thai' => 'hoan_thanh']);
        }

        // Chuyển hướng đến trang hiển thị hóa đơn
        return redirect()
            ->route('nhanVien.thanh-toan.hien-thi-hoa-don', $hoaDon->id)
            ->with('success', 'Hóa đơn đã được tạo với trạng thái chưa thanh toán!');
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

        // Tự động hủy tất cả món đang chờ bếp trước khi thanh toán
        foreach ($datBan->orderMon as $order) {
            foreach ($order->chiTietOrders as $ct) {
                if ($ct->trang_thai == 'cho_bep') {
                    $ct->update(['trang_thai' => 'huy_mon']);
                }
            }
        }

        // Reload lại dữ liệu sau khi hủy để đảm bảo tính toán đúng
        $datBan->refresh();
        $datBan->load([
            'chiTietDatBan.combo',
            'orderMon.chiTietOrders.monAn',
            'banAn',
            'hoaDon'
        ]);

        // Tính tổng tiền combo: tính từng combo với số lượng tương ứng, giảm 50% cho trẻ em
        $tienComboChinh = 0;
        $soTreEm = $datBan->tre_em ?? 0;
        $soNguoiDaXuLy = 0; // Đếm số người đã xử lý để phân bổ giảm giá cho trẻ em

        foreach ($datBan->chiTietDatBan as $chiTiet) {
            if ($chiTiet->combo) {
                $giaComboGoc = $chiTiet->combo->gia_co_ban;
                $soLuongCombo = $chiTiet->so_luong ?? 1;

                // Tính số người được giảm giá trong combo này (trẻ em)
                $soNguoiDuocGiam = 0;
                if ($soTreEm > 0 && $soNguoiDaXuLy < $soTreEm) {
                    // Số người được giảm = min(số trẻ em còn lại, số lượng combo này)
                    $soTreEmConLai = $soTreEm - $soNguoiDaXuLy;
                    $soNguoiDuocGiam = min($soTreEmConLai, $soLuongCombo);
                }

                // Tính số người không giảm giá (người lớn)
                $soNguoiKhongGiam = $soLuongCombo - $soNguoiDuocGiam;

                // Tính tiền combo: (giá gốc * 0.5 * số trẻ em) + (giá gốc * số người lớn)
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
            // Kiểm tra điều kiện voucher
            if ($voucher->trang_thai != 'dang_ap_dung') {
                return redirect()->back()
                    ->with('error', 'Voucher "' . $voucher->ma_voucher . '" không còn hiệu lực!')
                    ->withInput();
            }
            
            if ($voucher->ngay_ket_thuc < now()) {
                return redirect()->back()
                    ->with('error', 'Voucher "' . $voucher->ma_voucher . '" đã hết hạn!')
                    ->withInput();
            }
            
            if ($voucher->so_luong <= $voucher->so_luong_da_dung) {
                return redirect()->back()
                    ->with('error', 'Voucher "' . $voucher->ma_voucher . '" đã hết số lượng sử dụng!')
                    ->withInput();
            }
            
            // Tính tiền giảm nếu voucher hợp lệ
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

        // Tính chi tiết thời gian (lấy từ combo đầu tiên)
        $thoiGianQuyDinh = 0;
        $thoiGianVuot = 0;
        $soLan10Phut = 0;
        $phuThuThoiGian = 0;

        $comboDauTien = $datBan->chiTietDatBan->first();
        if ($comboDauTien && $comboDauTien->combo && $comboDauTien->combo->thoi_luong_phut) {
            $thoiGianQuyDinh = $comboDauTien->combo->thoi_luong_phut;
            $thoiGianMienPhi = $thoiGianQuyDinh + 10;

            if ($thoiGianPhucVu > $thoiGianMienPhi) {
                $thoiGianVuot = $thoiGianPhucVu - $thoiGianMienPhi;
                $soLan10Phut = ceil($thoiGianVuot / 10);
                $phuThuThoiGian = $soLan10Phut * 30000;
            }
        }

        // Chuẩn bị danh sách món đã gọi
        // Tính tổng giới hạn cho từng món từ tất cả các combo
        $tongGioiHanMon = [];
        $phuPhiMon = [];
        $monTrongComboIds = []; // Danh sách ID món thuộc combo (bất kể có giới hạn hay không)

        foreach ($datBan->chiTietDatBan as $chiTiet) {
            if ($chiTiet->combo) {
                $monTrongCombo = \App\Models\MonTrongCombo::where('combo_id', $chiTiet->combo->id)->get();
                foreach ($monTrongCombo as $mtc) {
                    $monAnId = $mtc->mon_an_id;

                    // Đánh dấu món này thuộc combo (bất kể có giới hạn hay không)
                    if (!in_array($monAnId, $monTrongComboIds)) {
                        $monTrongComboIds[] = $monAnId;
                    }

                    $gioiHan = $mtc->gioi_han_so_luong ?? null;
                    if ($gioiHan !== null && $gioiHan > 0) {
                        $soLuongCombo = $chiTiet->so_luong ?? 1;
                        if (!isset($tongGioiHanMon[$monAnId])) {
                            $tongGioiHanMon[$monAnId] = 0;
                            $phuPhiMon[$monAnId] = $mtc->phu_phi_goi_them ?? 0;
                        }
                        $tongGioiHanMon[$monAnId] += $gioiHan * $soLuongCombo;
                    } else {
                        // Món trong combo nhưng không có giới hạn, vẫn cần lưu phụ phí
                        if (!isset($phuPhiMon[$monAnId])) {
                            $phuPhiMon[$monAnId] = $mtc->phu_phi_goi_them ?? 0;
                        }
                    }
                }
            }
        }

        // Tính tổng số lượng đã order cho từng món
        // Bỏ qua món đã hủy và món đang chờ bếp
        $tongSoLuongMon = [];
        foreach ($datBan->orderMon as $order) {
            foreach ($order->chiTietOrders as $ct) {
                if ($ct->trang_thai != 'huy_mon' && $ct->trang_thai != 'cho_bep') {
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

            $tongGioiHan = $tongGioiHanMon[$monAnId] ?? null;
            $soLuongVuot = 0;
            $donGiaHienThi = 0;
            $tienMon = 0;
            $phuPhi = $phuPhiMon[$monAnId] ?? 0;
            // Xác định món thuộc combo dựa vào danh sách món trong combo (không chỉ dựa vào giới hạn)
            $laMonCombo = in_array($monAnId, $monTrongComboIds);

            $tienPhuPhiTong = 0; // Tổng phụ phí (đã nhân với số lượng vượt)

            if ($laMonCombo) {
                // Món thuộc combo
                if ($tongGioiHan !== null && $tongGioiHan > 0) {
                    // Món combo có giới hạn: chỉ tính tiền cho phần vượt giới hạn
                    if ($tongSoLuong > $tongGioiHan) {
                        $soLuongVuot = $tongSoLuong - $tongGioiHan;
                        $donGiaHienThi = $monAn->gia ?? 0;
                        // Tiền món = giá * số lượng vượt
                        $tienMon = $donGiaHienThi * $soLuongVuot;
                        // Phụ phí tổng = phụ phí đơn vị * số lượng vượt
                        $tienPhuPhiTong = $phuPhi * $soLuongVuot;
                        // Tổng = tiền món + phụ phí
                        $tienMon = $tienMon + $tienPhuPhiTong;
                    } else {
                        // Số lượng <= giới hạn: miễn phí
                        $donGiaHienThi = 0;
                        $tienMon = 0;
                    }
                } else {
                    // Món combo không có giới hạn: miễn phí hoàn toàn
                    $donGiaHienThi = 0;
                    $tienMon = 0;
                }
            } else {
                // Món không thuộc combo: tính tiền đầy đủ
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
                'phu_phi' => $phuPhi, // Phụ phí đơn vị (để hiển thị)
                'phu_phi_tong' => $tienPhuPhiTong, // Tổng phụ phí (đã nhân với số lượng vượt)
                'so_luong_vuot' => $soLuongVuot,
                'thanh_tien' => $tienMon,
                'la_mon_combo' => $laMonCombo,
                'vuot_gioi_han' => $soLuongVuot > 0,
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

        // Tạo hóa đơn với trạng thái "đã thanh toán"
        $hoaDon = HoaDon::create([
            'dat_ban_id' => $datBan->id,
            'voucher_id' => $voucher ? $voucher->id : null,
            'ma_hoa_don' => 'HD' . date('YmdHis') . '-' . $datBan->id,
            'tong_tien' => $tongTienOrder,
            'tien_giam' => $tienGiam,
            'phu_thu' => $phuThu,
            'da_thanh_toan' => $daThanhToan,
            'phuong_thuc_tt' => $request->phuong_thuc_tt,
            'trang_thai' => 'da_thanh_toan',
        ]);

        // Tính tổng tiền món gọi thêm (theo trạng thái)
        $tongTienMonGoiThem = $this->tinhTienMonGoiThem($datBan);

        // Tính tổng tiền sau voucher
        $tongTienSauVoucher = $tongTienOrder - $tienGiam;
        if ($tongTienSauVoucher < 0) $tongTienSauVoucher = 0;

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

        // Cập nhật trạng thái bàn thành trống và tạo mới mã QR
        if ($datBan->banAn) {
            $newUniqueCode = Str::random(12);
            $baseUrl = config('app.url');

            // Tạo mã QR mới và đường dẫn QR mới
            $datBan->banAn->update([
                'trang_thai' => 'trong',
                'ma_qr' => $newUniqueCode,
                'duong_dan_qr' => $baseUrl . '/oderqr/menu/' . $newUniqueCode,
            ]);
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

        // Tự động hủy tất cả món đang chờ bếp trước khi thanh toán
        foreach ($datBan->orderMon as $orderItem) {
            foreach ($orderItem->chiTietOrders as $ct) {
                if ($ct->trang_thai == 'cho_bep') {
                    $ct->update(['trang_thai' => 'huy_mon']);
                }
            }
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
                if ($ct->trang_thai != 'huy_mon' && $ct->trang_thai != 'cho_bep') {
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
            // Kiểm tra điều kiện voucher
            if ($voucher->trang_thai != 'dang_ap_dung') {
                return redirect()->back()
                    ->with('error', 'Voucher "' . $voucher->ma_voucher . '" không còn hiệu lực!')
                    ->withInput();
            }
            
            if ($voucher->ngay_ket_thuc < now()) {
                return redirect()->back()
                    ->with('error', 'Voucher "' . $voucher->ma_voucher . '" đã hết hạn!')
                    ->withInput();
            }
            
            if ($voucher->so_luong <= $voucher->so_luong_da_dung) {
                return redirect()->back()
                    ->with('error', 'Voucher "' . $voucher->ma_voucher . '" đã hết số lượng sử dụng!')
                    ->withInput();
            }
            
            // Tính tiền giảm nếu voucher hợp lệ
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
        if ($tongTienSauVoucher < 0) $tongTienSauVoucher = 0;

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

        // Tạo hóa đơn với trạng thái "đã thanh toán"
        $hoaDon = HoaDon::create([
            'dat_ban_id' => $datBan->id,
            'voucher_id' => $voucher ? $voucher->id : null,
            'ma_hoa_don' => 'HD' . date('YmdHis') . '-' . $datBan->id,
            'tong_tien' => $tongTienOrder,
            'tien_giam' => $tienGiam,
            'phu_thu' => $phuThu,
            'da_thanh_toan' => $daThanhToan,
            'phuong_thuc_tt' => $request->phuong_thuc_tt,
            'trang_thai' => 'da_thanh_toan',
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

        // Cập nhật trạng thái bàn thành trống và tạo mới mã QR
        if ($datBan->banAn) {
            $newUniqueCode = Str::random(12);
            $baseUrl = config('app.url');

            // Tạo mã QR mới và đường dẫn QR mới
            $datBan->banAn->update([
                'trang_thai' => 'trong',
                'ma_qr' => $newUniqueCode,
                'duong_dan_qr' => $baseUrl . '/oderqr/menu/' . $newUniqueCode,
            ]);
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

        // Tính tiền combo từ chiTietDatBan
        $tienComboChinh = 0;
        foreach ($hoaDon->datBan->chiTietDatBan as $chiTiet) {
            if ($chiTiet->combo) {
                $tienComboChinh += $chiTiet->combo->gia_co_ban * ($chiTiet->so_luong ?? 1);
            }
        }

        $tongTienMonGoiThem = $this->tinhTienMonGoiThem($hoaDon->datBan);
        $tongTienThucTe = $tienComboChinh + $tongTienMonGoiThem;

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

        // Tính tổng tiền combo: tính từng combo với số lượng tương ứng
        $tienComboChinh = 0;
        foreach ($datBan->chiTietDatBan as $chiTiet) {
            if ($chiTiet->combo) {
                $tienComboChinh += $chiTiet->combo->gia_co_ban * ($chiTiet->so_luong ?? 1);
            }
        }

        $tongTienMonGoiThem = $this->tinhTienMonGoiThem($datBan);
        $tongTienThucTe = $tienComboChinh + $tongTienMonGoiThem;
        $tongTienSauVoucher = $tongTienThucTe - ($hoaDon->tien_giam ?? 0);

        return view('Shop.nhanVien.thanh-toan.in-hoa-don', compact(
            'hoaDon',
            'gioVao',
            'gioRa',
            'thoiGianPhucVu'
        ));
    }

    /**
     * Tính tiền món gọi thêm và món combo vượt giới hạn
     * Tính theo trạng thái: đã lên/đang nấu = 100%, chờ bếp = 0%, đã hủy = 0%
     */
    private function tinhTienMonGoiThem($datBan)
    {
        $tongTienMonGoiThem = 0;

        // Tính tổng giới hạn cho từng món từ tất cả các combo
        // Key: mon_an_id, Value: tổng giới hạn từ tất cả các combo
        $tongGioiHanMon = [];
        $phuPhiMon = []; // Key: mon_an_id, Value: phụ phí (lấy từ combo đầu tiên tìm thấy)

        foreach ($datBan->chiTietDatBan as $chiTiet) {
            if ($chiTiet->combo) {
                $monTrongCombo = \App\Models\MonTrongCombo::where('combo_id', $chiTiet->combo->id)->get();
                foreach ($monTrongCombo as $mtc) {
                    $monAnId = $mtc->mon_an_id;
                    $gioiHan = $mtc->gioi_han_so_luong ?? null;
                    if ($gioiHan !== null && $gioiHan > 0) {
                        // Nhân giới hạn với số lượng combo
                        $soLuongCombo = $chiTiet->so_luong ?? 1;
                        if (!isset($tongGioiHanMon[$monAnId])) {
                            $tongGioiHanMon[$monAnId] = 0;
                            $phuPhiMon[$monAnId] = $mtc->phu_phi_goi_them ?? 0;
                        }
                        $tongGioiHanMon[$monAnId] += $gioiHan * $soLuongCombo;
                    }
                }
            }
        }

        // Tính tổng số lượng đã order cho từng món (cả combo và goi_them)
        // Bỏ qua món đã hủy và món đang chờ bếp
        // QUAN TRỌNG: Bao gồm món có trạng thái da_len_mon (đã lên món), dang_che_bien (đang nấu) và cho_cung_ung (chờ cung ứng - đã nấu xong, chờ nhân viên xác nhận)
        $tongSoLuongMon = [];
        foreach ($datBan->orderMon as $order) {
            foreach ($order->chiTietOrders as $ct) {
                // Chỉ tính món đã lên (da_len_mon), đang nấu (dang_che_bien) và chờ cung ứng (cho_cung_ung), bỏ qua món đã hủy và chờ bếp
                if ($ct->trang_thai != 'huy_mon' && $ct->trang_thai != 'cho_bep') {
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
            $tongGioiHan = $tongGioiHanMon[$monAnId] ?? null;
            if ($tongGioiHan !== null && $tongGioiHan > 0) {
                $soLuongVuotMon[$monAnId] = max(0, $tongSoLuong - $tongGioiHan);
            } else {
                // Món không có trong combo hoặc không có giới hạn: không tính vượt quá
                $soLuongVuotMon[$monAnId] = 0;
            }
        }

        // Đếm số lượng đã phân bổ cho phần vượt quá (để tránh tính trùng)
        $daPhanBoVuot = [];

        // Tính tiền cho từng món (bỏ qua món đã hủy và món đang chờ bếp)
        // QUAN TRỌNG: Bao gồm món có trạng thái da_len_mon (đã lên món), dang_che_bien (đang nấu) và cho_cung_ung (chờ cung ứng - đã nấu xong, chờ nhân viên xác nhận)
        foreach ($datBan->orderMon as $order) {
            foreach ($order->chiTietOrders as $ct) {
                // Chỉ tính món đã lên (da_len_mon), đang nấu (dang_che_bien) và chờ cung ứng (cho_cung_ung), bỏ qua món đã hủy và chờ bếp
                if ($ct->trang_thai != 'huy_mon' && $ct->trang_thai != 'cho_bep') {
                    $monAnId = $ct->mon_an_id;

                    if ($ct->loai_mon == 'goi_them') {
                        // Món gọi thêm: tính tiền bình thường (bao gồm cả món da_len_mon, dang_che_bien và cho_cung_ung)
                        $tongTienMonGoiThem += ($ct->monAn->gia ?? 0) * $ct->so_luong;
                    } elseif ($ct->loai_mon == 'combo') {
                        // Món combo: tính tiền theo giới hạn
                        $tongGioiHan = $tongGioiHanMon[$monAnId] ?? null;

                        if ($tongGioiHan !== null && $tongGioiHan > 0) {
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

                                    // Tính tiền cho phần vượt quá: giá * số lượng vượt + phụ phí * số lượng vượt
                                    $donGia = $ct->monAn->gia ?? 0;
                                    $phuPhi = $phuPhiMon[$monAnId] ?? 0;
                                    // Tiền món = giá * số lượng vượt
                                    $tienMon = $donGia * $soLuongVuotTrongOrder;
                                    // Phụ phí = phụ phí * số lượng vượt
                                    $tienPhuPhi = $phuPhi * $soLuongVuotTrongOrder;
                                    // Tổng = tiền món + phụ phí
                                    $tongTienMonGoiThem += $tienMon + $tienPhuPhi;

                                    // Cập nhật số lượng đã phân bổ
                                    $daPhanBoVuot[$monAnId] += $soLuongVuotTrongOrder;
                                }
                            }
                        } else {
                            // Không có giới hạn hoặc giới hạn = 0: tính tiền bình thường cho toàn bộ số lượng
                            $tongTienMonGoiThem += ($ct->monAn->gia ?? 0) * $ct->so_luong;
                        }
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
                    if ($soTreEm > 0 && $soNguoiDaXuLy < $soTreEm) {
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

        $vnp_Returnurl = env('VNPAY_RETURN_URL') . "?banId=$banId";

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
            "vnp_OrderType" => "billpayment",
        ];

        ksort($inputData);
        $query = http_build_query($inputData, '', '&', PHP_QUERY_RFC3986);
        $vnp_SecureHash = hash_hmac('sha512', $query, $vnp_HashSecret);
        $vnp_Url = $vnp_Url . "?" . $query . "&vnp_SecureHash=" . $vnp_SecureHash;

        return redirect($vnp_Url);
    }

    /**
     * ==========================================
     * THANH TOÁN ONLINE QUA PAYOS (QR CODE)
     * ==========================================
     */
    public function createPayOSPayment(Request $request, $banId)
    {
        $ban = BanAn::findOrFail($banId);
        // Lấy tổng tiền từ form gửi sang (đã tính toán voucher, cọc...)
        $tongTien = (int) $request->input('tong_tien');

        // Nếu không có tổng tiền (trường hợp rủi ro), tính lại từ đầu
        if (!$tongTien || $tongTien <= 0) {
            // ... (Logic tính lại nếu cần thiết, nhưng thường form đã gửi đúng) ...
            // Để đơn giản, ta redirect lại nếu lỗi
            return redirect()->back()->with('error', 'Lỗi: Số tiền thanh toán không hợp lệ.');
        }

        // Khởi tạo SDK PayOS
        $payOS = new PayOS(
            env('PAYOS_CLIENT_ID'),
            env('PAYOS_API_KEY'),
            env('PAYOS_CHECKSUM_KEY')
        );

        // Mã đơn hàng: dùng time() + banId để đảm bảo duy nhất
        // Giới hạn độ dài orderCode theo yêu cầu PayOS (số nguyên)
        $orderCode = intval(substr(strval(microtime(true) * 10000), -6));

        // URL callback
        // Lưu ý: Route này phải được định nghĩa trong web.php
        $returnUrl = route('nhanVien.thanh-toan.payos.callback', ['banId' => $banId, 'status' => 'PAID']);
        $cancelUrl = route('nhanVien.thanh-toan.payos.callback', ['banId' => $banId, 'status' => 'CANCELLED']);

        try {
            $description = "Thanh toan ban " . $ban->so_ban;
            // Cắt ngắn mô tả nếu quá dài (PayOS giới hạn 25 ký tự cho description trong một số trường hợp, nhưng an toàn là < 50)
            $description = substr($description, 0, 25);

            $paymentData = new CreatePaymentLinkRequest(
                orderCode: $orderCode,
                amount: $tongTien,
                description: $description,
                returnUrl: $returnUrl,
                cancelUrl: $cancelUrl
            );

            $response = $payOS->paymentRequests->create($paymentData);

            // Chuyển hướng sang trang thanh toán của PayOS
            return redirect($response->checkoutUrl);
        } catch (APIException $e) {
            return redirect()->back()->with('error', 'Lỗi tạo link thanh toán PayOS: ' . $e->getMessage());
        }
    }

    public function handlePayOSCallback(Request $request, $banId)
    {
        $status = $request->input('status');

        if ($status == 'CANCELLED') {
            return redirect()->route('nhanVien.ban-an.index')
                ->with('error', 'Đã hủy thanh toán PayOS!');
        }

        if ($status == 'PAID') {
            // Gọi hàm xử lý chung: Trạng thái hóa đơn là "da_thanh_toan", phương thức là "payos"
            return $this->xuLyLuuHoaDon($request, $banId, 'da_thanh_toan', 'payos');
        }

        return redirect()->route('nhanVien.ban-an.index')
            ->with('error', 'Trạng thái thanh toán không xác định!');
    }

    /**
     * Hàm xử lý chung để lưu hóa đơn (Dùng cho cả Tiền mặt, PayOS, VNPay...)
     */
    private function xuLyLuuHoaDon(Request $request, $banId, $trangThaiHoaDon, $phuongThucTT = null)
    {
        $ban = BanAn::findOrFail($banId);

        // Lấy đặt bàn
        $datBan = DatBan::where('ban_id', $banId)
            ->whereIn('trang_thai', ['khach_da_den', 'dang_phuc_vu'])
            ->with(['chiTietDatBan.combo', 'orderMon.chiTietOrders.monAn'])
            ->latest()
            ->first();

        if (!$datBan) return redirect()->route('nhanVien.ban-an.index')->with('error', 'Bàn không hợp lệ!');
        if ($datBan->hoaDon) return redirect()->route('nhanVien.thanh-toan.hien-thi-hoa-don', $datBan->hoaDon->id);

        // Hủy món đang chờ bếp
        foreach ($datBan->orderMon as $order) {
            foreach ($order->chiTietOrders as $ct) {
                if ($ct->trang_thai == 'cho_bep') $ct->update(['trang_thai' => 'huy_mon']);
            }
        }
        $datBan->refresh();

        // --- TÍNH TIỀN ---
        // 1. Tính tiền Combo
        $tienComboChinh = 0;
        $soTreEm = $datBan->tre_em ?? 0;
        $soNguoiDaXuLy = 0;
        foreach ($datBan->chiTietDatBan as $chiTiet) {
            if ($chiTiet->combo) {
                $gia = $chiTiet->combo->gia_co_ban;
                $sl = $chiTiet->so_luong ?? 1;
                $giam = 0;
                if ($soTreEm > 0 && $soNguoiDaXuLy < $soTreEm) {
                    $conLai = $soTreEm - $soNguoiDaXuLy;
                    $giam = min($conLai, $sl);
                }
                $khongGiam = $sl - $giam;
                $tienComboChinh += ($gia * 0.5 * $giam) + ($gia * $khongGiam);
                $soNguoiDaXuLy += $sl;
            }
        }

        // 2. Tính tiền món gọi thêm
        $tongTienMonGoiThem = $this->tinhTienMonGoiThem($datBan);
        $tongTienOrder = $tienComboChinh + $tongTienMonGoiThem;
        $tienCoc = (float) ($datBan->tien_coc ?? 0);

        // 3. Phụ thu & Voucher
        $phuThu = (float) ($request->phu_thu ?? 0);
        $voucher = $request->voucher_id ? Voucher::find($request->voucher_id) : null;
        $tienGiam = 0;

        if ($voucher) {
            // Kiểm tra điều kiện voucher
            if ($voucher->trang_thai != 'dang_ap_dung') {
                return redirect()->back()
                    ->with('error', 'Voucher "' . $voucher->ma_voucher . '" không còn hiệu lực!')
                    ->withInput();
            }
            
            if ($voucher->ngay_ket_thuc < now()) {
                return redirect()->back()
                    ->with('error', 'Voucher "' . $voucher->ma_voucher . '" đã hết hạn!')
                    ->withInput();
            }
            
            if ($voucher->so_luong <= $voucher->so_luong_da_dung) {
                return redirect()->back()
                    ->with('error', 'Voucher "' . $voucher->ma_voucher . '" đã hết số lượng sử dụng!')
                    ->withInput();
            }
            
            // Tính tiền giảm nếu voucher hợp lệ
            if ($voucher->loai_giam == 'phan_tram') {
                $tienGiam = $tongTienOrder * ($voucher->gia_tri / 100);
                if ($voucher->gia_tri_toi_da && $tienGiam > $voucher->gia_tri_toi_da) $tienGiam = $voucher->gia_tri_toi_da;
            } else {
                $tienGiam = $voucher->gia_tri;
            }
            if ($tienGiam > $tongTienOrder) $tienGiam = $tongTienOrder;
        }

        // 4. Tính thực thu
        $daThanhToan = max(0, $tongTienOrder - $tienGiam + $phuThu - $tienCoc);

        // Nếu chọn "thanh toán sau" thì số tiền đã trả = 0
        $daThanhToanLuuDB = ($trangThaiHoaDon == 'chua_thanh_toan') ? 0 : $daThanhToan;

        // Nếu không truyền phương thức thanh toán cụ thể (PayOS/VNPay), lấy từ request
        $pttt = $phuongThucTT ?? $request->phuong_thuc_tt ?? 'chua_thanh_toan';

        // --- TẠO DANH SÁCH MÓN (JSON) ---
        $danhSachMon = [];
        $monTrongComboIds = [];
        // Lấy ID món combo
        foreach ($datBan->chiTietDatBan as $ct) {
            if ($ct->combo) {
                $ids = \App\Models\MonTrongCombo::where('combo_id', $ct->combo->id)->pluck('mon_an_id')->toArray();
                $monTrongComboIds = array_merge($monTrongComboIds, $ids);
            }
        }
        // Gom món từ order
        $tongSoLuongMon = [];
        foreach ($datBan->orderMon as $order) {
            foreach ($order->chiTietOrders as $ct) {
                if ($ct->trang_thai != 'huy_mon') {
                    $mid = $ct->mon_an_id;
                    if (!isset($tongSoLuongMon[$mid])) $tongSoLuongMon[$mid] = 0;
                    $tongSoLuongMon[$mid] += $ct->so_luong;
                }
            }
        }
        $stt = 1;
        foreach ($tongSoLuongMon as $mid => $sl) {
            $mon = \App\Models\MonAn::find($mid);
            if ($mon) {
                $isCombo = in_array($mid, $monTrongComboIds);
                $gia = $isCombo ? 0 : $mon->gia;
                $danhSachMon[] = [
                    'stt' => $stt++,
                    'ten_mon' => $mon->ten_mon,
                    'so_luong' => $sl,
                    'don_gia' => $gia,
                    'thanh_tien' => $gia * $sl,
                    'la_mon_combo' => $isCombo
                ];
            }
        }

        // --- LƯU DATABASE ---
        $hoaDon = HoaDon::create([
            'dat_ban_id' => $datBan->id,
            'voucher_id' => $voucher ? $voucher->id : null,
            'ma_hoa_don' => 'HD' . date('YmdHis') . '-' . $datBan->id,
            'tong_tien' => $tongTienOrder,
            'tien_giam' => $tienGiam,
            'phu_thu' => $phuThu,
            'da_thanh_toan' => $daThanhToanLuuDB,
            'phuong_thuc_tt' => $pttt,
            'trang_thai' => $trangThaiHoaDon,
        ]);

        // Lưu chi tiết
        $gioVao = Carbon::parse($datBan->gio_den);
        $gioRa = now();
        $comboDauTien = $datBan->chiTietDatBan->first();

        ChiTietHoaDon::create([
            'hoa_don_id' => $hoaDon->id,
            'ten_khach' => $datBan->ten_khach,
            'sdt_khach' => $datBan->sdt_khach,
            'email_khach' => $datBan->email_khach,
            'so_khach' => $datBan->so_khach ?? 1,
            'nguoi_lon' => $datBan->nguoi_lon,
            'tre_em' => $datBan->tre_em,
            'ban_so' => $ban->so_ban,
            'khu_vuc' => $ban->khuVuc->ten_khu_vuc ?? null,
            'tang' => $ban->khuVuc->tang ?? null,
            'so_ghe' => $ban->so_ghe,
            'ma_dat_ban' => $datBan->ma_dat_ban,
            'gio_vao' => $gioVao,
            'gio_ra' => $gioRa,
            'thoi_gian_phuc_vu_phut' => $gioVao->diffInMinutes($gioRa),
            'tong_tien_combo' => $tienComboChinh,
            'tong_tien_mon_goi_them' => $tongTienMonGoiThem,
            'danh_sach_mon' => json_encode($danhSachMon),
            'tong_tien_combo_mon' => $tongTienOrder,
            'tien_giam_voucher' => $tienGiam,
            'tong_tien_sau_voucher' => $tongTienOrder - $tienGiam,
            'tien_coc' => $tienCoc,
            'tong_phu_thu' => $phuThu,
            'phai_thanh_toan' => $daThanhToan,
            'tien_khach_dua' => $request->tien_khach_dua,
            'tien_tra_lai' => ($request->tien_khach_dua > $daThanhToan) ? ($request->tien_khach_dua - $daThanhToan) : 0,
            'phuong_thuc_tt' => $pttt,
            'ma_voucher' => $voucher ? $voucher->ma_voucher : null,
        ]);

        if ($voucher) $voucher->increment('so_luong_da_dung');

        // Cập nhật trạng thái bàn & đơn hàng
        $datBan->update(['trang_thai' => 'hoan_tat']);
        $newQr = Str::random(12);
        $ban->update(['trang_thai' => 'trong', 'ma_qr' => $newQr, 'duong_dan_qr' => config('app.url') . '/oderqr/menu/' . $newQr]);
        foreach ($datBan->orderMon as $o) $o->update(['trang_thai' => 'hoan_thanh']);

        return redirect()
            ->route('nhanVien.thanh-toan.hien-thi-hoa-don', $hoaDon->id)
            ->with('success', 'Thanh toán thành công!');
    }
}
