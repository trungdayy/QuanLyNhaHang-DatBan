<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonAnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // cho phép tất cả user gửi request
    }

    public function rules(): array
    {
        $hinhAnhRule = $this->isMethod('post')
            ? 'required|image|mimes:jpg,jpeg,png,gif|max:2048'
            : 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048';

        return [
            'ten_mon' => 'required|string|max:255',
            'danh_muc_id' => 'required|exists:danh_muc_mon,id',
            'gia' => 'required|numeric',
            'mo_ta' => 'nullable|string',
            'trang_thai' => 'required|in:con,het,an',
            'loai_mon' => 'nullable|in:Sống,Chín,Nướng,Xào / Luộc,Bánh ngọt,Trái cây,Nước có ga,Nước không ga,Trà / Cà phê',
            'hinh_anh' => $hinhAnhRule,
        ];
    }
}