<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NhanVien;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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

        // Chỉ validate các trường được phép sửa
        $rules = [
            'ho_ten' => 'required|string|max:255',
            'sdt' => ['required', 'string', 'max:20', Rule::unique('nhan_vien', 'sdt')->ignore($nhanVien->id)],
            // Email, vai_tro và trang_thai không được sửa trong form edit
        ];

        $request->validate($rules);

        try {
            // Chỉ cập nhật các trường được phép sửa (giữ nguyên email, vai_tro, trang_thai)
            $nhanVien->update([
                'ho_ten' => $request->ho_ten,
                'sdt' => $request->sdt,
                // Không cập nhật email, vai_tro, trang_thai
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
     * Toggle trạng thái nhân viên (Tắt/Mở)
     * Tắt: đổi trạng thái từ 1 (đang làm) sang 0 (nghỉ)
     * Bật: đổi trạng thái từ 0 (nghỉ) sang 1 (đang làm)
     * Không thay đổi nếu trạng thái là 2 (khóa)
     * Admin không thể tự tắt chính mình
     */
    public function toggleStatus($id)
    {
        try {
            $nhanVien = NhanVien::findOrFail($id);
            $trangThaiHienTai = $nhanVien->trang_thai;
            $adminHienTai = Auth::user(); // Admin đang đăng nhập

            // Nếu nhân viên đang bị khóa, không cho phép toggle
            if ($trangThaiHienTai == 2) {
                return back()->with('error', '❌ Nhân viên đang bị khóa, không thể thay đổi trạng thái.');
            }

            // Kiểm tra nếu đang tắt (từ đang làm -> nghỉ)
            if ($trangThaiHienTai == 1) {
                // Nếu nhân viên là admin và là chính admin đang đăng nhập, không cho phép tắt
                if ($nhanVien->vai_tro === 'quan_ly' && $nhanVien->id === $adminHienTai->id) {
                    return back()->with('error', '❌ Bạn không thể tự tắt tài khoản của chính mình.');
                }
                
                // Tắt: chuyển từ đang làm (1) sang nghỉ (0)
                $nhanVien->update(['trang_thai' => 0]);
                return back()->with('success', "🔒 Đã tắt nhân viên {$nhanVien->ho_ten}.");
            } else {
                // Bật: chuyển từ nghỉ (0) sang đang làm (1)
                $nhanVien->update(['trang_thai' => 1]);
                return back()->with('success', "✅ Đã bật nhân viên {$nhanVien->ho_ten}.");
            }
        } catch (\Exception $e) {
            Log::error("Lỗi toggle trạng thái nhân viên: " . $e->getMessage());
            return back()->with('error', 'Lỗi hệ thống.');
        }
    }

    /**
     * Cập nhật trạng thái (AJAX hoặc PATCH)
     */
    public function capNhatTrangThai($id)
    {
        $nhanVien = NhanVien::findOrFail($id);
        $adminHienTai = Auth::user(); // Admin đang đăng nhập

        // Chuyển trạng thái 1 <-> 0, 2 giữ nguyên
        if ($nhanVien->trang_thai != 2) {
            // Kiểm tra nếu đang tắt (từ đang làm -> nghỉ)
            if ($nhanVien->trang_thai == 1) {
                // Nếu nhân viên là admin và là chính admin đang đăng nhập, không cho phép tắt
                if ($nhanVien->vai_tro === 'quan_ly' && $nhanVien->id === $adminHienTai->id) {
                    return back()->with('error', '❌ Bạn không thể tự tắt tài khoản của chính mình.');
                }
            }
            
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