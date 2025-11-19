<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NhanVien;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class NhanVienController extends Controller
{
    /**
     * Danh sách nhân viên + lọc + tìm kiếm
     */
    public function index(Request $request)
    {
        $query = NhanVien::query();

        // Lọc vai trò
        if ($request->filled('vai_tro')) {
            $query->where('vai_tro', $request->vai_tro);
        }

        // Lọc trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Tìm kiếm theo tên / email / sđt
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('ho_ten', 'like', "%$keyword%")
                  ->orWhere('email', 'like', "%$keyword%")
                  ->orWhere('sdt', 'like', "%$keyword%");
            });
        }

        $nhanViens = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('admins.nhan-vien.index', compact('nhanViens'));
    }

    /**
     * Hiển thị form tạo nhân viên
     */
    public function create()
    {
        return view('admins.nhan-vien.create');
    }

    /**
     * Lưu nhân viên mới
     */
    public function store(Request $request)
    {
        $rules = [
            'ho_ten' => 'required|string|max:255',
            'sdt' => 'required|string|max:20|unique:nhan_vien,sdt',
            'email' => 'required|email|unique:nhan_vien,email',
            'mat_khau' => 'required|min:6',
            'vai_tro' => ['required', Rule::in(['quan_ly', 'phuc_vu', 'bep', 'le_tan'])],
            'trang_thai' => ['required', Rule::in([0,1,2])], // 0: nghỉ, 1: đang làm, 2: khóa
        ];

        $messages = [
            'ho_ten.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique' => 'Email đã tồn tại.',
            'sdt.unique' => 'Số điện thoại đã tồn tại.',
            'mat_khau.required' => 'Vui lòng nhập mật khẩu.',
            'vai_tro.in' => 'Vai trò không hợp lệ.',
            'trang_thai.in' => 'Trạng thái không hợp lệ.',
        ];

        $request->validate($rules, $messages);

        try {
            NhanVien::create([
                'ho_ten' => $request->ho_ten,
                'sdt' => $request->sdt,
                'email' => $request->email,
                'mat_khau' => Hash::make($request->mat_khau),
                'vai_tro' => $request->vai_tro,
                'trang_thai' => $request->trang_thai,
            ]);

            return redirect()->route('admin.nhan-vien.index')->with('success', 'Thêm nhân viên thành công!');
        } catch (QueryException $e) {
            Log::error("CREATE NHAN VIEN FAILED: " . $e->getMessage());
            return back()->with('error', 'Lỗi hệ thống khi tạo nhân viên.');
        }
    }

    /**
     * Hiển thị form sửa nhân viên
     */
    public function edit($id)
    {
        $nhanVien = NhanVien::findOrFail($id);
        return view('admins.nhan-vien.edit', compact('nhanVien'));
    }

    /**
     * Cập nhật thông tin nhân viên
     */
    public function update(Request $request, $id)
    {
        $nhanVien = NhanVien::findOrFail($id);

        $rules = [
            'ho_ten' => 'required|string|max:255',
            'sdt' => ['required', 'string', 'max:20', Rule::unique('nhan_vien', 'sdt')->ignore($nhanVien->id)],
            'email' => ['required', 'email', Rule::unique('nhan_vien', 'email')->ignore($nhanVien->id)],
            'vai_tro' => ['required', Rule::in(['quan_ly', 'phuc_vu', 'bep', 'le_tan'])],
            'trang_thai' => ['required', Rule::in([0,1,2])], // 0: nghỉ, 1: đang làm, 2: khóa
        ];

        $request->validate($rules);

        try {
            $nhanVien->update([
                'ho_ten' => $request->ho_ten,
                'sdt' => $request->sdt,
                'email' => $request->email,
                'vai_tro' => $request->vai_tro,
                'trang_thai' => $request->trang_thai,
            ]);

            return redirect()->route('admin.nhan-vien.index')->with('success', 'Cập nhật nhân viên thành công!');
        } catch (\Exception $e) {
            Log::error("UPDATE NHAN VIEN FAILED: " . $e->getMessage());
            return back()->with('error', 'Không thể cập nhật nhân viên.');
        }
    }

    /**
     * Xóa nhân viên
     */
    public function destroy($id)
    {
        try {
            $nhanVien = NhanVien::findOrFail($id);
            $nhanVien->delete();

            return redirect()->route('admin.nhan-vien.index')->with('success', 'Xóa nhân viên thành công!');
        } catch (\Exception $e) {
            Log::error("DELETE NHAN VIEN FAILED: " . $e->getMessage());
            return back()->with('error', 'Lỗi hệ thống khi xóa nhân viên.');
        }
    }

    /**
     * Cập nhật trạng thái (AJAX hoặc PATCH)
     */
    public function capNhatTrangThai($id)
    {
        $nhanVien = NhanVien::findOrFail($id);

        // Chuyển trạng thái 1 <-> 0, 2 giữ nguyên
        if ($nhanVien->trang_thai != 2) {
            $nhanVien->trang_thai = $nhanVien->trang_thai == 1 ? 0 : 1;
            $nhanVien->save();
        }

        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    /**
     * Reset mật khẩu về mặc định
     */
    public function resetMatKhau($id)
    {
        try {
            $nhanVien = NhanVien::findOrFail($id);
            $nhanVien->mat_khau = Hash::make('123456');
            $nhanVien->save();

            return back()->with('success', "Mật khẩu nhân viên {$nhanVien->ho_ten} đã được đặt lại về mặc định (123456)");
        } catch (\Exception $e) {
            Log::error("RESET PASSWORD FAILED: " . $e->getMessage());
            return back()->with('error', 'Không thể đặt lại mật khẩu.');
        }
    }
}