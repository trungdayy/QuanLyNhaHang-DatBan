<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DanhGiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $danhGias = [
            [
                'ten_khach' => 'Nguyễn Văn An',
                'sdt' => '0901234567',
                'email' => 'an.nguyen@example.com',
                'nghe_nghiep' => 'Nhân viên văn phòng',
                'noi_dung' => 'Đồ ăn rất tươi ngon, đặc biệt là quầy hải sản. Nhân viên phục vụ nhiệt tình, thay vỉ nướng liên tục. Sẽ quay lại ủng hộ quán dài dài!',
                'so_sao' => 5,
                'trang_thai' => 'hien_thi', // Đã duyệt
                'created_at' => Carbon::now()->subDays(2), // Cách đây 2 ngày
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'ten_khach' => 'Trần Thị Bích',
                'sdt' => '0912345678',
                'email' => 'bich.tran@example.com',
                'nghe_nghiep' => 'Giáo viên',
                'noi_dung' => 'Không gian quán đẹp, thoáng mát. Tuy nhiên hôm nay đi cuối tuần hơi đông nên món lên hơi chậm một chút. Đồ ăn thì ok.',
                'so_sao' => 4,
                'trang_thai' => 'hien_thi', // Đã duyệt
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'ten_khach' => 'Lê Hoàng Nam',
                'sdt' => '0987654321',
                'email' => null,
                'nghe_nghiep' => 'Freelancer',
                'noi_dung' => 'Vừa mới ăn xong, hải sản tươi roi rói. Giá vé buffet hợp lý so với chất lượng. 10 điểm cho món tôm càng xanh nướng.',
                'so_sao' => 5,
                'trang_thai' => 'cho_duyet', // Chờ Admin duyệt (Test màu vàng)
                'created_at' => Carbon::now()->subHours(5), // Cách đây 5 tiếng
                'updated_at' => Carbon::now()->subHours(5),
            ],
            [
                'ten_khach' => 'Phạm Văn Dũng',
                'sdt' => '0933445566',
                'email' => 'dung.pham@example.com',
                'nghe_nghiep' => null,
                'noi_dung' => 'Thất vọng quá. Đặt bàn trước mà đến nơi nhân viên bảo hết bàn phải chờ 30p. Đồ ăn thì nguội ngắt.',
                'so_sao' => 1,
                'trang_thai' => 'an', // Đã ẩn (Test màu xám)
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(4),
            ],
            [
                'ten_khach' => 'Hoàng Thùy Linh',
                'sdt' => '0977889900',
                'email' => 'linh.hoang@example.com',
                'nghe_nghiep' => 'Sinh viên',
                'noi_dung' => 'Buffet nhiều món, tráng miệng ngon. Phù hợp cho sinh viên tụ tập. Góp ý là nên thêm nhiều loại nước chấm hơn.',
                'so_sao' => 4,
                'trang_thai' => 'cho_duyet', // Chờ duyệt
                'created_at' => Carbon::now()->subMinutes(30), // Vừa gửi 30 phút trước
                'updated_at' => Carbon::now()->subMinutes(30),
            ],
        ];

        DB::table('danh_gia')->insert($danhGias);
    }
}