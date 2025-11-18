@extends('layouts.Shop.layout-oderqr')

@section('title', 'Thực Đơn Gọi Món')

@section('content')
<style>
    /* ===========================
       Trang Thực Đơn Gọi Món
    =========================== */

    /* 1. CẤU TRÚC CHUNG */
    .app-content {
        min-height: 100vh;
        background: linear-gradient(to right, #f5f7fa, #c3cfe2);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: 20px 0;
    }

    .container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
        /* QUAN TRỌNG: Để sticky hoạt động, container không được stretch chiều cao */
        align-items: flex-start; 
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
    }

    /* 2. KHUNG NỘI DUNG CHÍNH (BÊN TRÁI) */
    #main-content {
        flex: 1 1 600px; /* Co giãn, chiều rộng cơ sở 600px */
        background: #ffffff;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    /* Tiêu đề & Thông tin */
    h1, h2, h3 {
        color: #1c7ed6;
        margin-bottom: 15px;
    }

    h1 span#ten-ban {
        color: #fd7e14;
    }

    #main-content p {
        font-size: 1rem;
        color: #333;
        margin-bottom: 8px;
    }

    /* Khung hiển thị Combo đã chọn */
    #combo-display {
        background: #e7f5ff;
        border-left: 5px solid #1c7ed6;
        padding: 15px;
        margin: 20px 0;
        border-radius: 8px;
    }

    /* 3. DANH SÁCH MÓN ĂN (MENU) */
    .danh-muc h3 {
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
        margin-top: 30px;
    }

    .mon-an {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.2s ease;
    }

    .mon-an:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border-color: #1c7ed6;
    }

    .mon-info {
        display: flex;
        gap: 15px;
        align-items: flex-start;
        flex: 1;
    }

    .mon-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #ddd;
        flex-shrink: 0;
    }

    .mon-info div strong {
        font-size: 1.1rem;
        color: #333;
        display: block;
        margin-bottom: 5px;
    }

    .mon-an button {
        background: #1c7ed6;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        white-space: nowrap;
        transition: background 0.2s;
        margin-left: 10px;
    }

    .mon-an button:hover {
        background: #1864ab;
    }

    /* Trạng thái món ăn (Đã gọi) */
    .mon-an-status {
        background: #f8f9fa;
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        border-left: 4px solid #40c057; /* Màu xanh lá */
        font-size: 0.95rem;
    }

    /* 4. GIỎ HÀNG (STICKY SIDEBAR) */
    #cart {
        flex: 0 0 320px; /* Chiều rộng cố định 320px */
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        
        /* --- CẤU HÌNH STICKY --- */
        position: -webkit-sticky; /* Cho Safari */
        position: sticky;
        top: 20px; /* Cách mép trên màn hình 20px */
        z-index: 100;
        
        /* Tạo thanh cuộn bên trong nếu giỏ hàng quá dài */
        max-height: calc(100vh - 40px); 
        overflow-y: auto;
        /* ----------------------- */
    }

    #cart h4 {
        color: #e8590c;
        margin-bottom: 20px;
        font-size: 1.2rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
    }

    #cart-items {
        list-style: none;
        padding: 0;
        margin: 0 0 20px 0;
    }

    #cart-items li {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 10px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.95rem;
    }

    /* Link Ghi chú & Xóa */
    .note-link {
        color: #1c7ed6;
        font-size: 0.8rem;
        text-decoration: none;
        margin-left: 5px;
    }
    
    .delete-link {
        color: #fa5252;
        font-weight: bold;
        text-decoration: none;
        padding: 5px 10px;
        border-radius: 5px;
    }

    .delete-link:hover {
        background-color: #ffe3e3;
    }

    .note-text {
        display: block;
        font-size: 0.8rem;
        color: #868e96;
        font-style: italic;
        margin-top: 4px;
    }

    /* Nút Gửi Order */
    #submit-order-btn {
        width: 100%;
        padding: 15px;
        background: linear-gradient(45deg, #fd7e14, #f76707);
        color: #fff;
        font-weight: 700;
        font-size: 1.1rem;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 4px 15px rgba(253, 126, 20, 0.3);
    }

    #submit-order-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(253, 126, 20, 0.4);
    }
    
    #submit-order-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* 5. RESPONSIVE (MOBILE) */
    @media (max-width: 992px) {
        .container {
            flex-direction: column; /* Xếp chồng lên nhau */
            align-items: stretch;
        }

        #main-content {
            flex: 1 1 auto;
            padding: 15px;
        }

        #cart {
            width: 100%;
            margin-top: 20px;
            
            /* Tắt Sticky trên mobile vì chiếm màn hình */
            position: static; 
            max-height: none;
            overflow-y: visible;
        }
        
        .mon-image {
            width: 60px;
            height: 60px;
        }
    }
</style>


    <main class="app-content">

        <div class="container">

            <main id="main-content">
                <h1>Chào mừng! <span id="ten-ban">{{ $tenBan }}</span></h1>

                <p>Khách: <strong id="ten-khach">...</strong> (<strong id="so-nguoi">...</strong> người)</p>
                <p>Thời gian còn lại: <strong id="thoi-gian">...</strong> phút</p>

                <div id="combo-display">
                    <p>Combo Đã Đặt: <strong id="combo-name">Đang tải...</strong></p>
                    <div id="combo-details"></div>
                </div>

                <hr>

                <h2>Trạng Thái Món Đã Gọi</h2>
                <div id="status-container">
                    <p>Đang tải trạng thái...</p>
                </div>

                <hr>
                <h2>Thực Đơn</h2>
                <div id="menu-container">
                    <p>Đang tải thực đơn...</p>
                </div>
            </main>

            <aside id="cart">
                <h4>Giỏ hàng</h4>
                <ul id="cart-items"></ul>
                <button id="submit-order-btn">
                    Gửi Order
                </button>
            </aside>

        </div>

        <script>
            // ✅ ĐÃ SỬA: Dùng url('/') để lấy đường dẫn gốc của website
            // Kết quả: http://127.0.0.1:8000
            // Khi nối với "uploads/monan/anh.jpg" sẽ ra đường dẫn đúng
            const STORAGE_URL = '{{ url('/') }}'; 
            
            const QR_KEY = '{{ $qrKey }}';

            let DAT_BAN_ID = null;
            let cart = [];
            const menuContainer = document.getElementById('menu-container');
            const cartItemsList = document.getElementById('cart-items');
            const submitOrderBtn = document.getElementById('submit-order-btn');
            const statusContainer = document.getElementById('status-container');
            const soNguoiElement = document.getElementById('so-nguoi');
            const comboNameElement = document.getElementById('combo-name');
            const comboDetailsElement = document.getElementById('combo-details');


            // ----- HÀM 1: GỌI API KHI TẢI TRANG (API 1) -----
            async function loadSessionInfo() {
                try {
                    // SỬ DỤNG QR_KEY THAY CHO BAN_ID
                    const response = await fetch(`/oderqr/session/table/${QR_KEY}`);
                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Không tìm thấy bàn');
                    }

                    const data = await response.json();

                    // Lấy thông tin từ data.dat_ban_info
                    DAT_BAN_ID = data.dat_ban_info.id;
                    document.getElementById('thoi-gian').innerText = data.dat_ban_info.thoi_gian_con_lai_phut;
                    document.getElementById('ten-khach').innerText = data.dat_ban_info.ten_khach;
                    soNguoiElement.innerText = data.dat_ban_info.so_khach;
                    menuContainer.innerHTML = '';

                    // Lấy thông tin Combo
                    const combo = data.dat_ban_info.combo_buffet;

                    if (combo) {
                        comboNameElement.innerText = combo.ten_combo;
                        comboDetailsElement.innerHTML = `
                        <p>Giá Gói: <strong>${combo.gia_co_ban} VND</strong></p>
                        <p>Thời gian tối đa: ${combo.thoi_luong_phut} phút</p>
                    `;
                    } else {
                        comboNameElement.innerText = 'Không áp dụng';
                        comboDetailsElement.innerHTML = '<p>Khách đang gọi món lẻ.</p>';
                    }


                    // VẼ MENU (Đã Fix monAn -> mon_an)
                    data.menu.forEach(danhMuc => {
                        let danhMucHtml = `<div class="danh-muc"><h3>${danhMuc.ten_danh_muc}</h3>`;

                        if (danhMuc.mon_an) {
                            danhMuc.mon_an.forEach(monAn => {
                                const loaiMon = monAn.is_in_combo ? 'combo' : 'goi_them';
                                const loaiMonText = monAn.is_in_combo ? 'Trong gói' : 'Gọi thêm';


                                const imagePath = `${STORAGE_URL}/${monAn.hinh_anh}`;

                                danhMucHtml += `
                                <div class="mon-an ${loaiMon}">
                                    <div class="mon-info">
                                        <img src="${imagePath}" 
                                             alt="${monAn.ten_mon}" 
                                             class="mon-image"
                                             onerror="this.src='https://placehold.co/60x60/eee/ccc?text=No+Img'">
                                        <div>
                                            <strong>${monAn.ten_mon}</strong> (${loaiMonText})
                                            <p>Giá: ${monAn.gia}</p>
                                            <p style="font-size: 0.85em; color: #666; margin-top: 5px;">${monAn.mo_ta}</p>
                                        </div>
                                    </div>
                                    <button onclick="addToCart(${monAn.id}, '${monAn.ten_mon}', '${loaiMon}')">
                                        Thêm
                                    </button>
                                </div>
                            `;
                            });
                        }
                        danhMucHtml += `</div>`;
                        menuContainer.innerHTML += danhMucHtml;
                    });

                    loadOrderStatus();

                } catch (error) {
                    console.error(error);
                    menuContainer.innerHTML = `<p style="color: red;">Lỗi: ${error.message}</p>`;
                }
            }

            // ===============================================
            // MỚI: HÀM CẬP NHẬT GHI CHÚ
            // ===============================================
            function editNote(index) {
                const currentNote = cart[index].ghi_chu || '';
                const newNote = prompt(`Nhập ghi chú cho ${cart[index].ten_mon}:`, currentNote);

                if (newNote !== null) {
                    cart[index].ghi_chu = newNote.trim();
                    renderCart();
                }
            }

            // ===============================================
            // MỚI: HÀM XÓA MÓN KHỎI GIỎ HÀNG
            // ===============================================
            function deleteItem(index) {
                if (confirm(`Bạn có chắc muốn xóa món ${cart[index].ten_mon} khỏi giỏ hàng?`)) {
                    cart.splice(index, 1);
                    renderCart();
                }
            }

            // ----- HÀM 2: Thêm vào giỏ hàng (LUÔN LÀ 'goi_them') -----
            function addToCart(monAnId, tenMon, loaiMonGoc) {
                cart.push({
                    mon_an_id: monAnId,
                    ten_mon: tenMon,
                    so_luong: 1,
                    ghi_chu: null,
                    loai_mon: 'goi_them' // Luôn là món thêm khi chọn thủ công
                });

                renderCart();
            }

            // ----- HÀM 3: Vẽ lại giỏ hàng (ĐÃ THÊM NÚT XÓA VÀ GHI CHÚ) -----
            function renderCart() {
                cartItemsList.innerHTML = '';
                cart.forEach((item, index) => {
                    const li = document.createElement('li');
                    li.innerHTML = `
                    <div style="display: flex; flex-direction: column;">
                        <div>
                            ${item.ten_mon} (x${item.so_luong}) 
                            <small>(${item.loai_mon})</small>
                            <a href="#" onclick="editNote(${index}); return false;" class="note-link">[Ghi chú]</a>
                        </div>
                        ${item.ghi_chu ? `<span class="note-text">↳ ${item.ghi_chu}</span>` : ''}
                    </div>
                    <a href="#" onclick="deleteItem(${index}); return false;" class="delete-link">[X]</a>
                `;
                    cartItemsList.appendChild(li);
                });
            }

            // ----- HÀM 4: GỌI API GỬI ORDER (API 2) -----
            submitOrderBtn.addEventListener('click', async () => {
                if (cart.length === 0) return alert('Vui lòng chọn món');
                if (!DAT_BAN_ID) return alert('Lỗi: Không tìm thấy ID bàn đặt');

                submitOrderBtn.disabled = true;
                submitOrderBtn.innerText = 'Đang gửi...';

                try {
                    const response = await fetch('/oderqr/order/submit', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            dat_ban_id: DAT_BAN_ID,
                            items: cart
                        })
                    });
                    const result = await response.json();
                    if (!response.ok) throw new Error(result.message || 'Lỗi khi gửi order');

                    alert('Gửi order thành công!');
                    cart = [];
                    renderCart();
                    loadOrderStatus();

                } catch (error) {
                    alert(`Lỗi: ${error.message}`);
                }
                submitOrderBtn.disabled = false;
                submitOrderBtn.innerText = 'Gửi Order';
            });

            // ----- HÀM 5: GỌI API XEM TRẠNG THÁI (API 3) -----
            async function loadOrderStatus() {
                if (!DAT_BAN_ID) return;

                try {
                    const response = await fetch(`/oderqr/order/status/${DAT_BAN_ID}`);
                    if (response.status === 404) {
                        statusContainer.innerHTML = "<p>Chưa gọi món nào.</p>";
                        return;
                    }
                    const data = await response.json();
                    statusContainer.innerHTML = '';
                    data.items.forEach(item => {
                        statusContainer.innerHTML += `
                        <div class="mon-an-status">
                            <p>
                                ${item.mon_an.ten_mon} (x${item.so_luong}) - 
                                <strong>${item.trang_thai}</strong>
                                ${item.ghi_chu ? `<br><small class="note-text">Ghi chú: ${item.ghi_chu}</small>` : ''}
                            </p>
                        </div>
                    `;
                    });

                } catch (error) {
                    console.error("Lỗi tải trạng thái:", error);
                    statusContainer.innerHTML = "<p style='color: red;'>Lỗi khi tải trạng thái.</p>";
                }
            }

            // ----- CHẠY KHI MỞ TRANG -----
            document.addEventListener('DOMContentLoaded', () => {
                loadSessionInfo();
                setInterval(loadOrderStatus, 15000);
            });
        </script>
    </main>
@endsection