<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComboBuffet;
use Illuminate\Support\Facades\File;

class ComboBuffetController extends Controller
{
    /**
     * Hiển thị danh sách combo buffet
     */
    public function index()
    {
        $combos = ComboBuffet::orderByDesc('id')->get();
        return view('admins.combo.index', compact('combos'));
    }

    /**
     * Hiển thị form thêm combo mới
     */
    public function create()
    {
        return view('admins.combo.create');
    }

    /**
     * Lưu combo mới vào CSDL
     */
    public function store(Request $request)
    {
        $request->validate([
            'ten_combo' => 'required|string|max:255',
            'loai_combo' => 'nullable|in:99k,199k,299k,399k,499k',
            'gia_co_ban' => 'required|numeric|min:1',
            'thoi_luong_phut' => 'nullable|integer|min:15',
            'thoi_gian_bat_dau' => 'nullable|date',
            'thoi_gian_ket_thuc' => 'nullable|date|after:thoi_gian_bat_dau',
            'trang_thai' => 'required|in:dang_ban,ngung_ban',
            'anh' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'ten_combo.required' => 'Vui lòng nhập tên combo.',
            'gia_co_ban.required' => 'Vui lòng nhập giá cơ bản.',
            'thoi_gian_ket_thuc.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
            'trang_thai.required' => 'Vui lòng chọn trạng thái combo.',
            'anh.image' => 'Ảnh tải lên phải là định dạng hình ảnh.',
        ]);

        $data = $request->except('anh');

        // Xử lý upload ảnh
        if ($request->hasFile('anh')) {
            $file = $request->file('anh');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $uploadPath = public_path('uploads/combo_buffet');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }
            $file->move($uploadPath, $fileName);
            $data['anh'] = 'combo_buffet/' . $fileName;
        }

        ComboBuffet::create($data);

        return redirect()->route('admin.combo-buffet.index')
            ->with('success', 'Thêm combo buffet thành công');
    }

    /**
     * Hiển thị chi tiết combo
     */
    public function show(string $id)
    {
        $combo = ComboBuffet::findOrFail($id);
        return view('admins.combo.show', compact('combo'));
    }

    /**
     * Hiển thị form sửa combo
     */
    public function edit(string $id)
    {
        $combo = ComboBuffet::findOrFail($id);
        return view('admins.combo.edit', compact('combo'));
    }

    /**
     * Cập nhật combo
     */
    public function update(Request $request, string $id)
    {
        $combo = ComboBuffet::findOrFail($id);

        $request->validate([
            'ten_combo' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',   // thêm dòng này
            'loai_combo' => 'nullable|in:99k,199k,299k,399k,499k',
            'gia_co_ban' => 'required|numeric|min:1',
            'thoi_luong_phut' => 'nullable|integer|min:15',
            'thoi_gian_bat_dau' => 'nullable|date',
            'thoi_gian_ket_thuc' => 'nullable|date|after:thoi_gian_bat_dau',
            'trang_thai' => 'required|in:dang_ban,ngung_ban',
            'anh' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except('anh');

        // Xử lý cập nhật ảnh mới
        if ($request->hasFile('anh')) {
            // Xóa ảnh cũ nếu có
            if ($combo->anh && File::exists(public_path('uploads/' . $combo->anh))) {
                File::delete(public_path('uploads/' . $combo->anh));
            }

            $file = $request->file('anh');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $uploadPath = public_path('uploads/combo_buffet');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }
            $file->move($uploadPath, $fileName);
            $data['anh'] = 'combo_buffet/' . $fileName;
        }

        $combo->update($data);

        return redirect()->route('admin.combo-buffet.index')
            ->with('success', 'Cập nhật combo buffet thành công');
    }

    /**
     * Xóa combo
     */
    public function destroy(string $id)
    {
        $combo = ComboBuffet::findOrFail($id);

        // Xóa ảnh cũ nếu có
        if ($combo->anh && File::exists(public_path('uploads/' . $combo->anh))) {
            File::delete(public_path('uploads/' . $combo->anh));
        }

        $combo->delete();

        return redirect()->route('admin.combo-buffet.index')
            ->with('success', 'Xóa combo buffet thành công');
    }
}