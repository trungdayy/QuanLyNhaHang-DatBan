<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhGia;
use Illuminate\Http\Request;

class DanhGiaController extends Controller
{
    /**
     * Hiển thị danh sách đánh giá
     */
    public function index()
    {
        // Lấy danh sách đánh giá mới nhất, phân trang 10 dòng
        $danhGias = DanhGia::orderBy('created_at', 'desc')->paginate(10);
        
        // SỬA Ở ĐÂY: admins.danh_gia.index (thêm chữ 's' vào admin để khớp với thư mục view của bạn)
        return view('admins.danh_gia.index', compact('danhGias'));
    }

    /**
     * Cập nhật trạng thái đánh giá (Duyệt / Ẩn)
     */
    public function updateStatus($id, $status)
    {
        $danhGia = DanhGia::findOrFail($id);
        
        // Chỉ cho phép các trạng thái hợp lệ
        $validStatuses = ['cho_duyet', 'hien_thi', 'an'];

        if (in_array($status, $validStatuses)) {
            $danhGia->trang_thai = $status;
            $danhGia->save();
            
            $message = $status == 'hien_thi' ? 'Đã duyệt đánh giá.' : 'Đã ẩn đánh giá.';
            return redirect()->back()->with('success', $message);
        }

        return redirect()->back()->with('error', 'Trạng thái không hợp lệ.');
    }

    /**
     * Xóa đánh giá
     */
    public function destroy($id)
    {
        $danhGia = DanhGia::findOrFail($id);
        $danhGia->delete();

        return redirect()->back()->with('success', 'Đã xóa đánh giá thành công.');
    }
}