<?php
// Cấu hình phân chia khu vực bếp theo ID Danh mục (danh_muc_id)
// Không cần sửa DB, không cần liệt kê tên món.
// Khi thêm món mới, chỉ cần chọn đúng Danh mục là nó tự về đúng bếp.

return [
    // 1. Khu Bếp Nóng (Các món nấu, xào, hấp...)
    // Gồm: Danh mục Hải sản (1) và Món chay (3)
    'nong' => [1, 3],

    // 2. Khu Bếp Nướng (Các món nướng, chiên...)
    // Gồm: Danh mục Thịt nướng (2)
    'nuong' => [2],

    // 3. Khu Bếp Lạnh (Tráng miệng, gỏi, salad...)
    // Gồm: Danh mục Tráng miệng (4)
    'lanh' => [4],

    // 4. Khu Bar (Pha chế)
    // Gồm: Danh mục Đồ uống (5)
    'nuoc' => [5],
];
?>