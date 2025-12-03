<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                default: // phuc_vu hoặc le_tan
                    return redirect()->route('nhanVien.ban-an.index'); 
            }
        }
        
        // Trả về view login
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
        // nhờ vào hàm getAuthPassword() trong Model NhanVien.
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
                case 'le_tan':
                    return redirect()->route('nhanVien.ban-an.index'); 
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
        
        // 🔥 ĐÃ SỬA: Chuyển hướng về trang Đăng nhập (login)
        return redirect()->route('login'); 
    }
}