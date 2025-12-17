<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống Quản Lý - Buffet Ocean</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

        :root {
            --primary-color: #ff9f00; /* Màu cam của nút Đặt Bàn */
            --hover-color: #e68a00;
            --bg-overlay: rgba(0, 0, 0, 0.85);
            --card-bg: rgba(255, 255, 255, 0.05);
            --text-main: #ffffff;
            --text-sub: #cccccc;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            background-color: #111;
            /* Tạo nền tối giả lập vân gỗ hoặc đá đen như trong ảnh */
            background-image: radial-gradient(circle at center, #222 0%, #000 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            color: var(--text-main);
        }

        /* Hình nền mờ phía sau (Optional - tạo cảm giác không gian) */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://images.unsplash.com/photo-1544025162-d76694265947?q=80&w=2069&auto=format&fit=crop'); /* Ảnh minh họa thịt nướng tối màu */
            background-size: cover;
            background-position: center;
            opacity: 0.2; /* Làm mờ để không rối mắt */
            z-index: -1;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        /* --- HEADER --- */
        .header {
            text-align: center;
            margin-bottom: 50px;
            animation: fadeInDown 1s ease-out;
        }

        .logo-text {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .logo-text i {
            font-size: 2.5rem;
        }

        .subtitle {
            font-size: 1.2rem;
            font-weight: 300;
            color: var(--text-main);
            border-top: 1px solid var(--primary-color);
            border-bottom: 1px solid var(--primary-color);
            padding: 10px 20px;
            display: inline-block;
        }

        /* --- DASHBOARD GRID --- */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 30px;
            width: 100%;
            padding: 20px;
        }

        .dashboard-card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px; /* Bo góc nhẹ giống nút trong ảnh */
            padding: 40px 20px;
            text-decoration: none;
            color: var(--text-main);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }

        /* Hiệu ứng thanh line cam bên dưới */
        .dashboard-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0%;
            height: 4px;
            background-color: var(--primary-color);
            transition: width 0.3s ease;
        }

        .dashboard-card:hover::after {
            width: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.5);
            border-color: var(--primary-color);
        }

        /* Icon styling */
        .dashboard-card i {
            font-size: 3.5rem;
            margin-bottom: 20px;
            color: var(--primary-color); /* Icon màu cam */
            transition: transform 0.3s;
        }

        .dashboard-card:hover i {
            transform: scale(1.1);
            color: #fff; /* Hover thì icon chuyển trắng */
        }

        /* Text styling */
        .dashboard-card span {
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-main);
        }

        /* Special highlight for Hover state mimicking the button */
        .dashboard-card:hover {
            background: linear-gradient(135deg, rgba(255, 159, 0, 0.2), rgba(0,0,0,0));
        }

        /* --- FOOTER --- */
        .footer {
            margin-top: 50px;
            font-size: 0.9rem;
            color: #666;
            font-weight: 300;
        }

        /* Animations */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            .logo-text {
                font-size: 2rem;
            }
            .dashboard-card {
                padding: 30px 15px;
            }
            .dashboard-card i {
                font-size: 2.5rem;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <div class="logo-text">
                <i class="fa-solid fa-utensils"></i> Buffet Ocean
            </div>
            <div class="subtitle">HỆ THỐNG QUẢN LÝ NHÀ HÀNG</div>
        </div>

        <div class="dashboard-grid">
            <a href="{{ route('admin.dashboard') }}" target="_blank" class="dashboard-card">
                <i class="fa fa-cogs"></i>
                <span>Quản Trị Admin</span>
            </a>

            <a href="{{ route('home') }}" target="_blank" class="dashboard-card">
                <i class="fa fa-home"></i>
                <span>Trang Chủ Khách</span>
            </a>

            <a href="{{ route('nhanVien.ban-an.index') }}" target="_blank" class="dashboard-card">
                <i class="fa fa-concierge-bell"></i>
                <span>Lễ Tân / Đặt Bàn</span>
            </a> 

            <a href="{{ route('nhanVien.order.index') }}" target="_blank" class="dashboard-card">
                <i class="fa fa-user-tie"></i>
                <span>Nhân Viên Order</span>
            </a>

            <a href="{{ route('bep.dashboard') }}" target="_blank" class="dashboard-card">
                <i class="fa fa-fire-burner"></i>
                <span>Bếp & Chế Biến</span>
            </a>

            <a href="{{ route('oderqr.list') }}" target="_blank" class="dashboard-card">
                <i class="fa fa-qrcode"></i>
                <span>QR Menu System</span>
            </a>
        </div>

        <div class="footer">
            &copy; 2024 Buffet Ocean Management System
        </div>
    </div>

</body>
</html>