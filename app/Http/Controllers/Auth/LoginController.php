<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\NhanVien; // Cần import Model NhanVien

class LoginController extends Controller
{
    /**
     * Hiển thị form đăng nhập.
     */
    public function showLoginForm()
    {
        // Kiểm tra nếu đã đăng nhập, chuyển hướng luôn về dashboard tương ứng
        if (Auth::check()) {
            $user = Auth::user();
            
            // Điều hướng nhanh nếu user đã login
            switch ($user->vai_tro) {
                case 'quan_ly':
                    return redirect()->route('admin.dashboard');
                case 'bep':
                    return redirect()->route('bep.dashboard');
                case 'le_tan':
                    return redirect()->route('nhanVien.ban-an.index');
                case 'phuc_vu':
                    return redirect()->route('nhanVien.order.index');
                default:
                    return redirect()->route('login');
            }
        }
        
        // Trả về view login (bao gồm cả form Đăng nhập và Đăng ký)
        return view('auth.login'); 
    }

    /**
     * Xử lý đăng nhập và điều hướng theo vai trò (Admin, Bếp, Nhân viên).
     */
    public function login(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $request->validate([
            'email' => 'required|email',
            'mat_khau' => 'required', // Tên field trong form login
        ]);

        // 2. Chuẩn bị thông tin đăng nhập. 
        // Key 'password' được yêu cầu bởi Auth::attempt, nhưng sẽ dùng cột 'mat_khau' 
        // nhờ vào hàm getAuthPassword() trong Model NhanVien (cần phải được định nghĩa).
        $credentials = [
            'email' => $request->email,
            'password' => $request->mat_khau 
        ];

        // 3. Thử đăng nhập
        if (Auth::attempt($credentials)) {
            
            $request->session()->regenerate();
            $user = Auth::user();

            // 4. Kiểm tra trạng thái: Đảm bảo tài khoản Đang làm (trang_thai = 1)
            if ($user->trang_thai !== 1) {
                Auth::logout();
                return back()->withErrors(['email' => 'Tài khoản đã bị khóa hoặc ngừng hoạt động.']);
            }

            // 5. Điều hướng theo vai trò
            switch ($user->vai_tro) {
                case 'quan_ly':
                    return redirect()->route('admin.dashboard');
                case 'bep':
                    return redirect()->route('bep.dashboard');
                case 'phuc_vu':
                    return redirect()->route('nhanVien.order.index'); // Phục vụ không có trang quản lý bàn
                case 'le_tan':
                    return redirect()->route('nhanVien.ban-an.index'); // Lễ tân có trang quản lý bàn ăn
                default:
                    Auth::logout();
                    return back()->withErrors(['email' => 'Vai trò không hợp lệ.']);
            }
        }

        // 6. Đăng nhập thất bại
        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ]);
    }

    /**
     * Xử lý đăng ký nhân viên mới (Phục vụ hoặc Bếp).
     * Yêu cầu các trường: ho_ten, sdt, email, mat_khau, mat_khau_confirmation, vai_tro.
     */
    public function storeNhanVien(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $request->validate([
            'ho_ten' => 'required|string|max:255',
            'sdt' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:nhan_vien,email',
            'mat_khau' => 'required|string|min:6|confirmed', // min:6 cho dễ test, bạn có thể đổi thành min:8
            'vai_tro' => 'required|in:phuc_vu,bep', // Chỉ cho phép đăng ký vai trò phục vụ hoặc bếp
        ], [
            'ho_ten.required' => 'Họ và tên là bắt buộc.',
            'sdt.required' => 'Số điện thoại là bắt buộc.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'mat_khau.required' => 'Mật khẩu là bắt buộc.',
            'mat_khau.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'mat_khau.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'vai_tro.required' => 'Vai trò là bắt buộc.',
            'vai_tro.in' => 'Vai trò được chọn không hợp lệ.',
        ]);

        try {
            // 2. Hash mật khẩu trước khi lưu vào DB
            $hashedPassword = Hash::make($request->mat_khau);

            // 3. Tạo và lưu nhân viên mới
            $nhanVien = NhanVien::create([
                'ho_ten' => $request->ho_ten,
                'sdt' => $request->sdt,
                'email' => $request->email,
                'mat_khau' => $hashedPassword,
                'vai_tro' => $request->vai_tro, 
                'trang_thai' => 1, // Mặc định là đang làm
            ]);

            // 4. Chuyển hướng thành công về trang Đăng nhập
            return redirect()->route('login')->with('success', 'Đăng ký thành công! Vui lòng Đăng nhập.');

        } catch (\Exception $e) {
            // Xử lý lỗi (ví dụ: lỗi DB, lỗi server)
            return redirect()->back()->withInput()->withErrors(['error' => 'Đăng ký thất bại. Vui lòng thử lại.']);
        }
    }

    /**
     * Xử lý đăng xuất.
     */
    public function logout(Request $request)
    {
        // Xóa phiên đăng nhập
        Auth::logout();
        
        // Vô hiệu hóa session hiện tại
        $request->session()->invalidate();
        
        // Tái tạo token CSRF
        $request->session()->regenerateToken();
        
        // Chuyển hướng về trang Đăng nhập (login)
        return redirect()->route('login'); 
    }
}