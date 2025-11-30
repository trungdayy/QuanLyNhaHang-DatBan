<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\DatBan;
use App\Models\BanAn;
use App\Models\ComboBuffet;
use App\Models\KhuVuc;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Mail\BookingOtpMail;
use App\Mail\InvoiceMail;
use PayOS\PayOS;
use PayOS\Models\V2\PaymentRequests\CreatePaymentLinkRequest;
use App\Helpers\BookingHelper;

class BookingController extends Controller
{
    /** TRANG CHÍNH: danh sách booking của khách */
    public function index()
    {
        $sdt = session('sdt_khach', null);
        $datBans = $sdt
            ? DatBan::where('sdt_khach', $sdt)->orderByDesc('created_at')->get()
            : collect();

        $combos = ComboBuffet::where('trang_thai', 'dang_ban')->get();
        $banAns = BanAn::whereNotIn('trang_thai', ['dang_phuc_vu', 'da_dat', 'khong_su_dung'])->get();
        $khuVucs = KhuVuc::all();

        $selectedKhuVucId = null; // thêm dòng này

        return view('restaurants.booking.index', compact('datBans', 'sdt', 'combos', 'banAns', 'khuVucs', 'selectedKhuVucId'));
    }

    /** FORM ĐẶT BÀN ONLINE */
    public function create(Request $request)
    {
        $combos = ComboBuffet::where('trang_thai', 'dang_ban')->get();
        $khuVucs = KhuVuc::all();

        $selectedKhuVucId = $request->khu_vuc_id ?? null;

        // Chỉ load bàn nếu đã chọn khu vực
        $selectedKhuVucId = $request->khu_vuc_id ?? null;

        $banAns = collect();
        if ($selectedKhuVucId) {
            $banAns = BanAn::where('khu_vuc_id', $selectedKhuVucId)
                ->where('trang_thai', 'trong') // chỉ lấy bàn trống
                ->orderBy('so_ban')
                ->get();
        }


        $selectedCombo = $request->combo_id ? ComboBuffet::find($request->combo_id) : null;

        return view('restaurants.booking.create', compact(
            'combos',
            'banAns',
            'khuVucs',
            'selectedCombo',
            'selectedKhuVucId'
        ));
    }

    /** Lấy bàn trống theo khu vực (dành cho AJAX nếu vẫn muốn) */
    // BookingController.php
    public function getBansByKhuVuc(Request $request, $khu_vuc_id)
    {
        try {
            // Lấy ngày giờ khách chọn, default là now()
            $gioDen = $request->query('gio_den') ? Carbon::parse($request->query('gio_den')) : now();

            // Lấy bàn trống theo khu vực và ngày đã chọn
            $banAns = BanAn::where('khu_vuc_id', $khu_vuc_id)
                ->whereDoesntHave('datBans', function ($q) use ($gioDen) {
                    $q->whereNotIn('trang_thai', ['huy', 'hoan_tat'])
                        ->whereDate('gio_den', $gioDen->toDateString());
                })
                ->orderBy('so_ban')
                ->get();

            return response()->json($banAns);
        } catch (\Exception $e) {
            // Nếu có lỗi, trả về mảng rỗng thay vì HTML
            return response()->json([], 500);
        }
    }



    /** LƯU ĐẶT BÀN + gửi OTP nếu có email */
    public function store(Request $request)
    {
        $request->validate([
            'ten_khach' => 'required|string|max:255',
            'email_khach' => 'nullable|email|max:255',
            'sdt_khach' => 'required|string|max:20',
            'so_khach' => 'required|integer|min:1',
            'combo_id' => 'nullable|exists:combo_buffet,id',
            'ban_id' => 'nullable|exists:ban_an,id',
            'gio_den' => 'required|date',
            'ghi_chu' => 'nullable|string',
        ]);

        session(['sdt_khach' => $request->sdt_khach]);
        $combo_id = $request->combo_id ?: null;
        $ban_id = $request->ban_id ?: null;

        // Kiểm tra trùng bàn
        if ($ban_id) {
            $duration = 120;
            $gioDen = Carbon::parse($request->gio_den);
            $conflict = DatBan::where('ban_id', $ban_id)
                ->whereNotIn('trang_thai', ['huy', 'hoan_tat'])
                ->whereBetween('gio_den', [
                    $gioDen->copy()->subMinutes($duration - 1),
                    $gioDen->copy()->addMinutes($duration - 1)
                ])->first();

            if ($conflict) {
                $gioBiTrung = Carbon::parse($conflict->gio_den)->format('H:i d/m/Y');
                return back()->withInput()->with('error', "Bàn đã được đặt lúc $gioBiTrung.");
            }
        }

        $maDatBan = 'DB-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        $comboPrice = $combo_id ? ComboBuffet::find($combo_id)->gia_co_ban : 0;
        $tienCoc = BookingHelper::calculateDeposit($comboPrice, $request->so_khach);

        $datBan = DatBan::create([
            'ma_dat_ban' => $maDatBan,
            'ten_khach' => $request->ten_khach,
            'email_khach' => $request->email_khach,
            'sdt_khach' => $request->sdt_khach,
            'so_khach' => $request->so_khach,
            'combo_id' => $combo_id,
            'ban_id' => $ban_id,
            'gio_den' => $request->gio_den,
            'thoi_luong_phut' => 120,
            'ghi_chu' => $request->ghi_chu,
            'tien_coc' => $tienCoc,
            'la_dat_online' => 1,
            'trang_thai' => 'cho_xac_nhan'
        ]);

        if ($request->email_khach) {
            $otp = rand(100000, 999999);
            session([
                "otp.booking.{$request->email_khach}" => [
                    'code' => $otp,
                    'booking_id' => $datBan->id,
                    'expires_at' => now()->addMinutes(5)
                ],
                "otp.booking_email" => $request->email_khach
            ]);
            Mail::to($request->email_khach)->send(new BookingOtpMail($otp));
            return redirect()->route('otp.form')->with('success', 'Mã OTP đã gửi tới email của bạn.');
        }

        $ban = BanAn::find($request->ban_id);
        return redirect()->route('booking.payment_method', $datBan->id)
            ->with('success', 'Đặt bàn thành công! Chọn phương thức thanh toán.');
    }

    /** TRANG CHỌN PHƯƠNG THỨC THANH TOÁN */
    public function paymentMethod($booking_id)
    {
        $datBan = DatBan::findOrFail($booking_id);
        $sdt = session('sdt_khach', null);

        if ($datBan->sdt_khach !== $sdt) {
            return redirect()->route('booking.index')->with('error', 'Bạn không có quyền truy cập đơn này.');
        }

        return view('restaurants.booking.payment-method', compact('datBan'));
    }

    /** Thanh toán PayOS (SDK mới) */
    public function payOS($booking_id)
    {
        $datBan = DatBan::findOrFail($booking_id);

        $payOS = new PayOS(
            clientId: env('PAYOS_CLIENT_ID'),
            apiKey: env('PAYOS_API_KEY'),
            checksumKey: env('PAYOS_CHECKSUM_KEY'),
        );

        // 1. Tạo orderCode kiểu int duy nhất
        $orderCode = (int) round(microtime(true) * 1000);

        // 2. Lưu vào DB
        $datBan->update(['payos_order_code' => $orderCode]);

        // 3. Tạo payment request
        $paymentData = new CreatePaymentLinkRequest(
            orderCode: $orderCode,
            amount: $datBan->tien_coc ?: 50000,
            description: substr("Pay #{$datBan->ma_dat_ban}", 0, 25),
            returnUrl: env('PAYOS_RETURN_URL'),
            cancelUrl: env('PAYOS_CANCEL_URL')
        );

        try {
            $result = $payOS->paymentRequests->create($paymentData);
            return redirect()->to($result->checkoutUrl);
        } catch (\PayOS\Exceptions\APIException $e) {
            return back()->with('error', 'Tạo đơn thanh toán thất bại: ' . $e->getMessage());
        }
    }


    /** 
     * Callback PayOS sau khi khách thanh toán cọc thành công
     */
    /**
     * Callback PayOS
     */
    public function payOSCallback(Request $request)
    {
        $orderCode = $request->query('orderCode');
        $status    = strtoupper($request->query('status') ?? '');

        // 1. Kiểm tra orderCode hợp lệ
        if (!$orderCode) {
            return redirect()->route('booking.index')
                ->with('error', 'Callback không hợp lệ (thiếu orderCode).');
        }

        // 2. Tìm đặt bàn theo mã orderCode
        $datBan = DatBan::where('payos_order_code', $orderCode)->first();

        if (!$datBan) {
            return redirect()->route('booking.index')
                ->with('error', 'Không tìm thấy đơn đặt bàn.');
        }

        // 3. PayOS báo thành công
        if (in_array($status, ['SUCCESS', 'PAID'])) {

            // A. Nếu đang chờ xác nhận -> chuyển sang đã xác nhận
            if ($datBan->trang_thai === 'cho_xac_nhan') {
                $datBan->trang_thai = 'da_xac_nhan';
                $datBan->save();
            }

            // B. Nếu đã xác nhận hoặc vừa xác nhận xong -> chuyển sang đã thanh toán
            if ($datBan->trang_thai === 'da_xac_nhan') {
                $datBan->trang_thai      = 'da_thanh_toan';
                $datBan->ngay_thanh_toan = now();
                $datBan->deposit_paid     = 1;
                $datBan->deposit_paid_at  = now();
                $datBan->save();
            }

            // C. Gửi email hóa đơn nếu có email
            if (!empty($datBan->email_khach)) {
                try {
                    Mail::to($datBan->email_khach)->send(new InvoiceMail($datBan));
                } catch (\Exception $e) {
                    Log::error("Gửi InvoiceMail thất bại cho booking_id {$datBan->id}: " . $e->getMessage());
                }
            }

            // D. Điều hướng về trang success
            return redirect()->route('booking.success')
                ->with('success', 'Thanh toán PayOS thành công!');
        }

        // 4. Nếu PayOS báo chưa thanh toán
        return redirect()->route('booking.payment_method', $datBan->id)
            ->with('error', 'Thanh toán chưa hoàn tất.');
    }


    /** Trang thành công */
    public function success()
    {
        $sdt = session('sdt_khach', null);
        $datBans = $sdt ? DatBan::where('sdt_khach', $sdt)->orderByDesc('created_at')->get() : collect();
        $combos = ComboBuffet::where('trang_thai', 'dang_ban')->get();
        $banAns = BanAn::whereNotIn('trang_thai', ['dang_phuc_vu', 'da_dat', 'khong_su_dung'])->get();
        $khuVucs = KhuVuc::all();

        return view('restaurants.booking.index', compact('datBans', 'sdt', 'combos', 'banAns', 'khuVucs'))
            ->with('success', 'Đặt bàn và thanh toán thành công!');
    }

    /** Thanh toán chuyển khoản / VietQR */
    public function payVietQR($booking_id)
    {
        $datBan = DatBan::findOrFail($booking_id);
        return view('restaurants.pay.pay-vietqr', compact('datBan'));
    }

    /** Hủy thanh toán */
    public function cancel()
    {
        return redirect()->route('booking.index')->with('error', 'Thanh toán bị hủy.');
    }
}
