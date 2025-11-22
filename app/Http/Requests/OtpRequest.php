<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OtpRequest extends FormRequest
{
    /**
     * Xác định xem người dùng có được phép gửi request này không
     */
    public function authorize(): bool
    {
        return true; // true nghĩa là bất kỳ ai cũng có thể gửi
    }

    /**
     * Các quy tắc validate
     */
    public function rules(): array
    {
        return [
            'otp' => ['required', 'digits:6'], // chỉ nhận 6 chữ số
        ];
    }

    /**
     * Thông báo lỗi tuỳ chỉnh (tuỳ ý)
     */
    public function messages(): array
    {
        return [
            'otp.required' => 'Vui lòng nhập mã OTP',
            'otp.digits'   => 'Mã OTP phải là 6 chữ số',
        ];
    }
}
