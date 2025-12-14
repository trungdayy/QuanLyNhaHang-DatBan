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

        // Tính tổng tiền combo: tính từng combo với số lượng tương ứng
        $tienComboChinh = 0;
        foreach ($datBan->chiTietDatBan as $chiTiet) {
            if ($chiTiet->combo) {
                $tienComboChinh += $chiTiet->combo->gia_co_ban * ($chiTiet->so_luong ?? 1);
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
            'thoiGianQuyDinh',
            'thoiGianMienPhi',
            'thoiGianVuot',
            'soLan10Phut',
            'phuThuThoiGian'
        ));
    }

    /**
     * Lưu hóa đơn từ ban_id (THANH TOÁN THƯỜNG)
     */
    public function luuThanhToanTuBan(Request $request, $banId)
    {
        $request->validate([
            'phuong_thuc_tt' => 'required|string',
            'phu_thu' => 'nullable|numeric|min:0',
            'voucher_id' => 'nullable|exists:vouchers,id',
            'tien_khach_dua' => 'nullable|numeric|min:0',
        ]);

        // Gọi hàm chung để xử lý logic lưu hóa đơn
        return $this->xuLyLuuHoaDonChung($request, $banId, $request->phuong_thuc_tt);
    }

    /**
     * === HÀM XỬ LÝ CHUNG CHO CẢ TIỀN MẶT VÀ PAYOS ===
     * Hàm này thực hiện toàn bộ logic tính toán, trừ tiền, tạo hóa đơn, update trạng thái.
     */
    private function xuLyLuuHoaDonChung(Request $request, $banId, $phuongThucThanhToan)
    {
        $ban = BanAn::findOrFail($banId);

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
            return redirect()->route('nhanVien.ban-an.index')->with('error', 'Bàn này chưa có khách!');
        }

        if ($datBan->hoaDon) {
            return redirect()->route('nhanVien.thanh-toan.hien-thi-hoa-don', $datBan->hoaDon->id);
        }

        // --- BẮT ĐẦU LOGIC TÍNH TIỀN ---
        // 1. Tính tổng tiền combo
        $tienComboChinh = 0;
        foreach ($datBan->chiTietDatBan as $chiTiet) {
            if ($chiTiet->combo) {
                $tienComboChinh += $chiTiet->combo->gia_co_ban * ($chiTiet->so_luong ?? 1);
            }
        }

        // 2. Tính tiền món gọi thêm
        $tongTienMonGoiThem = $this->tinhTienMonGoiThem($datBan);
        $tongTienOrder = $tienComboChinh + $tongTienMonGoiThem;

        $tienCoc = (float) ($datBan->tien_coc ?? 0);

        // 3. Tính phụ thu
        $gioVao = Carbon::parse($datBan->gio_den);
        $gioRa = now();
        $thoiGianPhucVu = $gioVao->diffInMinutes($gioRa);
        $phuThuTuDong = $this->tinhPhuThuTuDong($datBan, $thoiGianPhucVu);

        // Lấy phụ thu thủ công từ request (nếu có, nếu không có thì mặc định 0)
        $phuThuThucCong = (float) ($request->phu_thu ?? 0);
        $phuThu = $phuThuTuDong + $phuThuThucCong;

        // 4. Xử lý Voucher
        // Nếu request có voucher_id thì lấy, không thì null
        $voucherId = $request->voucher_id ?? null;
        $voucher = $voucherId ? Voucher::find($voucherId) : null;
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

        // 5. Tính tiền cuối cùng
        $daThanhToan = $tongTienOrder - $tienGiam + $phuThu - $tienCoc;
        if ($daThanhToan < 0) $daThanhToan = 0;

        // 6. Tính chi tiết thời gian (để lưu vào DB)
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

        // 7. Tạo danh sách món chi tiết (để lưu vào JSON)
        $monTrongComboIds = [];
        foreach ($datBan->chiTietDatBan as $chiTiet) {
            if ($chiTiet->combo) {
                $monTrongCombo = \App\Models\MonTrongCombo::where('combo_id', $chiTiet->combo->id)->get();
                foreach ($monTrongCombo as $mtc) {
                    if (!in_array($mtc->mon_an_id, $monTrongComboIds)) $monTrongComboIds[] = $mtc->mon_an_id;
                }
            }
        }

        $tongSoLuongMon = [];
        foreach ($datBan->orderMon as $order) {
            foreach ($order->chiTietOrders as $ct) {
                if ($ct->trang_thai != 'huy_mon') {
                    $tongSoLuongMon[$ct->mon_an_id] = ($tongSoLuongMon[$ct->mon_an_id] ?? 0) + $ct->so_luong;
                }
            }
        }

        $danhSachMon = [];
        $stt = 1;
        foreach ($tongSoLuongMon as $monAnId => $tongSoLuong) {
            $monAn = \App\Models\MonAn::find($monAnId);
            if (!$monAn) continue;

            $laMonCombo = in_array($monAnId, $monTrongComboIds);
            $donGiaHienThi = $laMonCombo ? 0 : ($monAn->gia ?? 0);
            $tienMon = $donGiaHienThi * $tongSoLuong;

            $danhSachMon[] = [
                'stt' => $stt++,
                'ten_mon' => $monAn->ten_mon,
                'so_luong' => $tongSoLuong,
                'gioi_han' => null,
                'don_gia' => $donGiaHienThi,
                'phu_phi' => 0,
                'phu_phi_tong' => 0,
                'so_luong_vuot' => 0,
                'thanh_tien' => $tienMon,
                'la_mon_combo' => $laMonCombo,
                'vuot_gioi_han' => false,
            ];
        }

        // --- LƯU DATABASE ---

        // Tạo hóa đơn
        $hoaDon = HoaDon::create([
            'dat_ban_id' => $datBan->id,
            'voucher_id' => $voucher ? $voucher->id : null,
            'ma_hoa_don' => 'HD' . date('YmdHis') . '-' . $datBan->id,
            'tong_tien' => $tongTienOrder,
            'tien_giam' => $tienGiam,
            'phu_thu' => $phuThu,
            'da_thanh_toan' => $daThanhToan,
            'phuong_thuc_tt' => $phuongThucThanhToan, // Lưu phương thức (payos hoặc tien_mat)
        ]);

        // Tiền khách đưa/trả lại
        $tienKhachDua = (float) ($request->tien_khach_dua ?? 0);
        $tienTraLai = 0;
        if ($phuongThucThanhToan == 'tien_mat' && $tienKhachDua > 0) {
            $tienTraLai = max(0, $tienKhachDua - $daThanhToan);
        }

        // Lưu chi tiết hóa đơn
        ChiTietHoaDon::create([
            'hoa_don_id' => $hoaDon->id,
            'ten_khach' => $datBan->ten_khach,
            'sdt_khach' => $datBan->sdt_khach,
            'email_khach' => $datBan->email_khach,
            'so_khach' => $datBan->so_khach ?? 1,
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
            'danh_sach_mon' => $danhSachMon,
            'tong_tien_combo_mon' => $tongTienOrder,
            'tien_giam_voucher' => $tienGiam,
            'tien_coc' => $tienCoc,
            'phu_thu_tu_dong' => $phuThuTuDong,
            'phu_thu_thu_cong' => $phuThuThucCong,
            'tong_phu_thu' => $phuThu,
            'phai_thanh_toan' => $daThanhToan,
            'tien_khach_dua' => $tienKhachDua > 0 ? $tienKhachDua : null,
            'tien_tra_lai' => $tienTraLai > 0 ? $tienTraLai : null,
            'phuong_thuc_tt' => $phuongThucThanhToan,
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

        return redirect()
            ->route('nhanVien.thanh-toan.hien-thi-hoa-don', $hoaDon->id)
            ->with('success', 'Thanh toán thành công!');
    }

    /**
     * ==========================================
     * THANH TOÁN ONLINE QUA PAYOS (QR CODE)
     * ==========================================
     */
    public function createPayOSPayment(Request $request, $banId)
    {
        $ban = BanAn::findOrFail($banId);
        $tongTien = (int) $request->input('tong_tien');

        // Khởi tạo SDK PayOS
        $payOS = new PayOS(
            env('PAYOS_CLIENT_ID'),
            env('PAYOS_API_KEY'),
            env('PAYOS_CHECKSUM_KEY')
        );

        // Mã đơn hàng: dùng time() + banId để đảm bảo duy nhất
        $orderCode = intval(substr(strval(microtime(true) * 10000), -6));

        // URL callback
        $returnUrl = route('nhanVien.thanh-toan.payos.callback', ['banId' => $banId, 'status' => 'PAID']);
        $cancelUrl = route('nhanVien.thanh-toan.payos.callback', ['banId' => $banId, 'status' => 'CANCELLED']);

        try {
            $paymentData = new CreatePaymentLinkRequest(
                orderCode: $orderCode,
                amount: $tongTien,
                // SỬA DÒNG NÀY: Rút ngắn mô tả xuống dưới 25 ký tự
                description: "Thanh toan " . ($ban->so_ban ?? $banId),
                returnUrl: $returnUrl,
                cancelUrl: $cancelUrl
            );

            $response = $payOS->paymentRequests->create($paymentData);

            // Chuyển hướng sang PayOS
            return redirect($response->checkoutUrl);
        } catch (APIException $e) {
            return redirect()->back()->with('error', 'Lỗi tạo PayOS: ' . $e->getMessage());
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
            // Khi thanh toán thành công -> Gọi hàm chung để Lưu Hóa Đơn
            // Lưu ý: PayOS callback là GET, không có body request chứa thông tin phụ thu/voucher
            // Nên ở đây ta mặc định không có voucher và phụ thu thủ công
            // (Nếu muốn giữ voucher, cần lưu vào session lúc tạo link thanh toán)

            $fakeRequest = new Request();
            $fakeRequest->merge([
                'phuong_thuc_tt' => 'payos', // Đánh dấu là PayOS
                'phu_thu' => 0, // Mặc định 0
                'voucher_id' => null, // Mặc định null
                'tien_khach_dua' => 0
            ]);

            return $this->xuLyLuuHoaDonChung($fakeRequest, $banId, 'payos');
        }

        return redirect()->route('nhanVien.ban-an.index')->with('error', 'Trạng thái thanh toán không xác định!');
    }

    /**
     * Hiển thị hóa đơn (Giữ nguyên)
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

        if ($hoaDon->chiTietHoaDon) {
            $chiTiet = $hoaDon->chiTietHoaDon;
            return view('Shop.nhanVien.thanh-toan.hien-thi-hoa-don', compact('hoaDon', 'chiTiet'));
        }

        // Fallback cũ
        $soKhach = $hoaDon->datBan->so_khach ?? 1;
        $gioVao = $hoaDon->datBan->gio_den ? Carbon::parse($hoaDon->datBan->gio_den) : null;
        $gioRa = $hoaDon->created_at;
        $thoiGianPhucVu = $gioVao ? $gioVao->diffInMinutes($gioRa) : 0;

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
            'thoiGianPhucVu',
            'tienComboChinh',
            'tongTienMonGoiThem',
            'tongTienThucTe'
        ));
    }

    /**
     * In hóa đơn (Giữ nguyên)
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

        if ($hoaDon->chiTietHoaDon) {
            return view('Shop.nhanVien.thanh-toan.in-hoa-don', compact('hoaDon'));
        }

        $datBan = $hoaDon->datBan;
        $gioVao = $datBan->gio_den ? Carbon::parse($datBan->gio_den) : null;
        $gioRa = $hoaDon->created_at;
        $thoiGianPhucVu = $gioVao ? $gioVao->diffInMinutes($gioRa) : 0;

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
            'thoiGianPhucVu',
            'tongTienThucTe',
            'tongTienSauVoucher',
            'tienComboChinh',
            'tongTienMonGoiThem'
        ));
    }

    /**
     * Tính tiền món gọi thêm (Logic cũ giữ nguyên)
     */
    private function tinhTienMonGoiThem($datBan)
    {
        $tongTienMonGoiThem = 0;

        $monTrongComboIds = [];
        foreach ($datBan->chiTietDatBan as $chiTiet) {
            if ($chiTiet->combo) {
                $monTrongCombo = \App\Models\MonTrongCombo::where('combo_id', $chiTiet->combo->id)->get();
                foreach ($monTrongCombo as $mtc) {
                    if (!in_array($mtc->mon_an_id, $monTrongComboIds)) {
                        $monTrongComboIds[] = $mtc->mon_an_id;
                    }
                }
            }
        }

        foreach ($datBan->orderMon as $order) {
            foreach ($order->chiTietOrders as $ct) {
                if ($ct->trang_thai != 'huy_mon') {
                    $monAnId = $ct->mon_an_id;
                    $laMonCombo = in_array($monAnId, $monTrongComboIds);

                    if ($ct->loai_mon == 'goi_them' || !$laMonCombo) {
                        $tongTienMonGoiThem += ($ct->monAn->gia ?? 0) * $ct->so_luong;
                    }
                }
            }
        }

        return $tongTienMonGoiThem;
    }

    /**
     * Tính phụ thu tự động (Logic cũ giữ nguyên)
     */
    private function tinhPhuThuTuDong($datBan, $thoiGianPhucVu)
    {
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
        return $phuThuThoiGian;
    }
}
