<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DatBan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MomoController extends Controller
{
    /** Tạo payment request đến MOMO */
    public function createPayment(Request $request, $booking_id)
    {
        $datBan = DatBan::findOrFail($booking_id);

        $endpoint = env('MOMO_ENDPOINT');
        $partnerCode = env('MOMO_PARTNER_CODE');
        $accessKey = env('MOMO_ACCESS_KEY');
        $secretKey = env('MOMO_SECRET_KEY');
        $orderId = 'ORDER-' . now()->format('YmdHis') . '-' . Str::random(4);
        $amount = $datBan->tien_coc ?: 10000; // giá test, có thể là 0
        $orderInfo = "Thanh toán đặt bàn #" . $datBan->ma_dat_ban;
        $returnUrl = env('MOMO_RETURN_URL');
        $notifyUrl = env('MOMO_NOTIFY_URL');
        $requestId = Str::uuid();
        $extraData = ''; // nếu cần

        // Chuẩn bị signature
        $rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$notifyUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$returnUrl&requestId=$requestId&requestType=captureWallet";
        $signature = hash_hmac('sha256', $rawHash, $secretKey);

        $data = [
            "partnerCode" => $partnerCode,
            "accessKey" => $accessKey,
            "requestId" => $requestId,
            "amount" => (string)$amount,
            "orderId" => $orderId,
            "orderInfo" => $orderInfo,
            "redirectUrl" => $returnUrl,
            "ipnUrl" => $notifyUrl,
            "extraData" => $extraData,
            "requestType" => "captureWallet",
            "signature" => $signature
        ];

        // Gửi request đến MOMO
        $response = Http::post($endpoint, $data)->json();

        if (isset($response['payUrl'])) {
            return redirect($response['payUrl']);
        }

        return back()->with('error', 'Không tạo được link thanh toán MOMO.');
    }

    /** Xử lý khách thanh toán xong quay lại (returnUrl) */
    public function handleReturn(Request $request)
    {
        $booking_id = $request->orderId ?? null;
        $datBan = DatBan::where('ma_dat_ban', $booking_id)->first();

        if (!$datBan) {
            return redirect()->route('booking')->with('error', 'Đơn không tồn tại.');
        }

        if ($request->resultCode == 0) {
            $datBan->update(['trang_thai' => 'da_thanh_toan']);
            return redirect()->route('booking.success')->with('success', 'Thanh toán MOMO thành công.');
        }

        return redirect()->route('booking')->with('error', 'Thanh toán MOMO thất bại.');
    }

    /** Nhận callback từ MOMO (notifyUrl) */
    public function handleNotify(Request $request)
    {
        $booking_id = $request->orderId ?? null;
        $datBan = DatBan::where('ma_dat_ban', $booking_id)->first();

        if ($datBan && $request->resultCode == 0) {
            $datBan->update(['trang_thai' => 'da_thanh_toan']);
        }

        return response()->json(['status' => 'ok']);
    }
}
