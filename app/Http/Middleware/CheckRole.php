<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     * $roles sẽ là danh sách các vai trò được phép truy cập (vd: 'quan_ly', 'bep'...)
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // 1. Kiểm tra đã đăng nhập chưa
        if (!Auth::check()) {
            // Chuyển hướng đến trang đăng nhập nếu chưa login
            return redirect()->route('login'); 
        }

        // 2. Lấy user (NhanVien) hiện tại
        $user = Auth::user();

        // 3. Kiểm tra trạng thái: Nếu bị khóa (trang_thai = 0) thì logout và thông báo
        if ($user->trang_thai === 0) {
             Auth::logout();
             return redirect()->route('login')->with('error', 'Tài khoản của bạn đã bị khóa hoặc ngừng hoạt động.');
        }

        // 4. Kiểm tra vai trò: Nếu vai trò của user nằm trong danh sách cho phép (truyền vào từ route)
        if (in_array($user->vai_tro, $roles)) {
            return $next($request); // Cho phép đi tiếp
        }

        // 5. Nếu không đúng quyền: Báo lỗi 403 (Forbidden)
        return abort(403, 'Bạn không có quyền truy cập vào khu vực này.');
    }
}