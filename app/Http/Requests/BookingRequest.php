<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_khach'    => ['required', 'string', 'max:255', 'min:2'],
            'sdt_khach'    => ['required', 'string', 'regex:/^(03|05|07|08|09)[0-9]{8}$/'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'booking_time' => ['required', 'string'],
            'ca_dat'       => ['required', 'in:trua,toi'],
            'nguoi_lon'    => ['required', 'integer', 'min:1', 'max:50'],
            'tre_em'       => ['nullable', 'integer', 'min:0', 'max:20'],
            'ghi_chu'      => ['nullable', 'string', 'max:500'],
            'cart_data'    => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            // Tên khách
            'ten_khach.required' => 'Vui lòng nhập họ và tên.',
            'ten_khach.string'   => 'Họ và tên phải là chuỗi ký tự.',
            'ten_khach.max'      => 'Họ và tên không được vượt quá 255 ký tự.',
            'ten_khach.min'      => 'Họ và tên phải có ít nhất 2 ký tự.',

            // Số điện thoại
            'sdt_khach.required' => 'Vui lòng nhập số điện thoại.',
            'sdt_khach.regex'   => 'Số điện thoại không hợp lệ. Vui lòng nhập số điện thoại Việt Nam (10 chữ số, bắt đầu bằng 03, 05, 07, 08, 09).',

            // Ngày đến
            'booking_date.required'        => 'Vui lòng chọn ngày đến.',
            'booking_date.date'            => 'Ngày đến không hợp lệ.',
            'booking_date.after_or_equal'  => 'Ngày đến không được là ngày trong quá khứ. Vui lòng chọn ngày hôm nay hoặc ngày sau.',

            // Giờ đến
            'booking_time.required' => 'Vui lòng chọn khung giờ.',
            'booking_time.string'  => 'Khung giờ không hợp lệ.',

            // Ca đặt
            'ca_dat.required' => 'Vui lòng chọn ca (Trưa hoặc Tối).',
            'ca_dat.in'       => 'Ca đặt không hợp lệ. Vui lòng chọn Trưa hoặc Tối.',

            // Người lớn
            'nguoi_lon.required' => 'Vui lòng nhập số lượng người lớn.',
            'nguoi_lon.integer' => 'Số lượng người lớn phải là số nguyên.',
            'nguoi_lon.min'     => 'Số lượng người lớn phải ít nhất là 1.',
            'nguoi_lon.max'     => 'Số lượng người lớn không được vượt quá 50 người.',

            // Trẻ em
            'tre_em.integer' => 'Số lượng trẻ em phải là số nguyên.',
            'tre_em.min'     => 'Số lượng trẻ em không được nhỏ hơn 0.',
            'tre_em.max'     => 'Số lượng trẻ em không được vượt quá 20 trẻ.',

            // Ghi chú
            'ghi_chu.string' => 'Ghi chú phải là chuỗi ký tự.',
            'ghi_chu.max'    => 'Ghi chú không được vượt quá 500 ký tự.',
        ];
    }

    public function attributes(): array
    {
        return [
            'ten_khach'    => 'họ và tên',
            'sdt_khach'    => 'số điện thoại',
            'booking_date' => 'ngày đến',
            'booking_time' => 'khung giờ',
            'ca_dat'       => 'ca đặt',
            'nguoi_lon'    => 'số người lớn',
            'tre_em'       => 'số trẻ em',
            'ghi_chu'      => 'ghi chú',
        ];
    }

    protected function prepareForValidation()
    {
        // Loại bỏ khoảng trắng và ký tự đặc biệt trong số điện thoại
        if ($this->has('sdt_khach')) {
            $this->merge([
                'sdt_khach' => preg_replace('/[^0-9]/', '', $this->sdt_khach)
            ]);
        }
        
        // Gộp ngày và giờ thành gio_den
        if ($this->has(['booking_date', 'booking_time']) && $this->booking_date && $this->booking_time) {
            $this->merge([
                'gio_den' => $this->booking_date . ' ' . $this->booking_time . ':00'
            ]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate thời gian đặt phải ở tương lai
            if ($this->has('gio_den')) {
                try {
                    $gioDen = \Carbon\Carbon::parse($this->gio_den);
                    if ($gioDen->isPast()) {
                        $validator->errors()->add('gio_den', 'Thời gian đặt bàn phải ở tương lai. Vui lòng chọn thời gian sau thời điểm hiện tại.');
                    }
                } catch (\Exception $e) {
                    $validator->errors()->add('gio_den', 'Thời gian đặt bàn không hợp lệ.');
                }
            }

            // Validate số suất combo phải đủ cho số người lớn
            if ($this->filled('cart_data')) {
                $cartItems = json_decode($this->cart_data, true);
                $totalCombos = 0;
                
                if (is_array($cartItems)) {
                    foreach ($cartItems as $item) {
                        if (isset($item['key']) && str_starts_with($item['key'], 'combo_')) {
                            $totalCombos += ($item['quantity'] ?? 0);
                        }
                    }
                }
                
                $nguoiLon = $this->nguoi_lon ?? 0;
                if ($totalCombos > 0 && $totalCombos < $nguoiLon) {
                    $validator->errors()->add('cart_data', "Số suất Combo ($totalCombos) phải đủ cho số người lớn ($nguoiLon). Vui lòng chọn thêm combo hoặc giảm số người lớn.");
                }
            }
        });
    }
}

