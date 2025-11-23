<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\BookingOtpMail;

class OtpController extends Controller
{
    /**
     * Gửi OTP tới email khách hàng
     * POST /otp/send
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'booking_id' => 'required|integer',
        ]);

        $email = $request->input('email');
        $bookingId = $request->input('booking_id');

        // Sinh mã OTP 6 chữ số
        $otpCode = rand(100000, 999999);

        // Lưu OTP vào session 5 phút
        session([
            "otp.booking.$email" => [
                'code' => $otpCode,
                'booking_id' => $bookingId,
                'expires_at' => now()->addMinutes(5)
            ],
            "otp.booking_email" => $email
        ]);

        // Gửi email OTP
        Mail::to($email)->send(new BookingOtpMail($otpCode));

        return redirect()
            ->route('otp.form')
            ->with('success', 'Mã OTP đã được gửi tới email của bạn.');
    }

    /**
     * Hiển thị form nhập OTP
     * GET /otp/verify
     */
    public function showOtpForm()
    {
        $email = session('otp.booking_email');

        if (!$email) {
            return redirect()
                ->route('home')
                ->with('error', 'Vui lòng gửi OTP trước.');
        }

        return view('restaurants.OTP.form', compact('email'));
    }

    /**
     * Xác thực OTP
     * POST /otp/verify
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $email = $request->input('email');
        $otpInput = $request->input('otp');
        $otpData = session("otp.booking.$email");

        // Kiểm tra tồn tại và hết hạn
        if (!$otpData || Carbon::parse($otpData['expires_at'])->lt(now())) {
            session()->forget("otp.booking.$email");
            session()->forget("otp.booking_email");
            return back()->withErrors(['otp' => 'OTP không tồn tại hoặc đã hết hạn.']);
        }

        // Kiểm tra OTP
        if ($otpInput != $otpData['code']) {
            return back()->withErrors(['otp' => 'OTP không đúng, vui lòng thử lại.']);
        }

        // OTP hợp lệ → chuyển sang trang chọn phương thức thanh toán
        $bookingId = $otpData['booking_id'];

        // Xóa session OTP và email
        session()->forget("otp.booking.$email");
        session()->forget("otp.booking_email");

        return redirect()
            ->route('booking.payment_method', ['booking_id' => $bookingId])
            ->with('success', 'OTP hợp lệ. Vui lòng chọn phương thức thanh toán.');
    }
}
