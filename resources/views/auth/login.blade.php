<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hệ thống Buffet Ocean</title>
    
    {{-- Liên kết Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    {{-- Font Poppins --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* --- 1. ĐỊNH NGHĨA ANIMATION --- */
        @keyframes bgFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes containerPopUp {
            0% { 
                opacity: 0; 
                transform: translateY(50px) scale(0.9);
            }
            100% { 
                opacity: 1; 
                transform: translateY(0) scale(1);
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            /* Thay đổi background thành hình ảnh BBQ tối màu + lớp phủ đen */
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('https://images.unsplash.com/photo-1544025162-d76690b6d029?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            animation: bgFadeIn 1.2s ease-out; 
            overflow: hidden;
        }

        .container {
            position: relative;
            width: 900px;
            max-width: 90vw;
            min-height: 550px;
            height: auto;
            background: #1f1f1f; 
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.6), 0 10px 20px rgba(0,0,0,0.4);
            animation: containerPopUp 1s cubic-bezier(0.19, 1, 0.22, 1) forwards; 
            border: 1px solid rgba(255, 153, 0, 0.2);
            display: flex;
        }


        .login-wrapper {
            display: flex;
            width: 100%;
            min-height: 100%;
            align-items: stretch;
        }

        .branding-section {
            flex: 1;
            background: linear-gradient(135deg, rgba(255, 153, 0, 0.15), rgba(255, 153, 0, 0.05));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            border-right: 1px solid rgba(255, 153, 0, 0.2);
            position: relative;
            overflow: hidden;
            align-self: stretch;
            min-height: 100%;
        }

        .branding-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 153, 0, 0.1) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        .branding-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .brand-logo {
            font-size: 48px;
            color: #FF9900;
            margin-bottom: 20px;
            text-shadow: 0 0 20px rgba(255, 153, 0, 0.5);
        }

        .brand-title {
            font-size: 32px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        .brand-subtitle {
            font-size: 16px;
            color: #cccccc;
            line-height: 1.6;
            max-width: 300px;
        }

        .form-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            align-self: stretch;
            min-height: 100%;
        }

        form {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            width: 100%;
            max-width: 400px;
            padding: 0;
        }

        form.sign-in-form {
            opacity: 1;
            transition: 0.5s ease-in-out;
            transition-delay: 1s;
        }

        .title {
            font-size: 28px;
            color: #ffffff; 
            margin-bottom: 30px;
            text-align: center;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        /* Style mới cho ô nhập liệu (Dark Theme) */
        .input-field {
            width: 100%;
            height: 55px;
            background: #2b2b2b; 
            margin: 15px 0;
            border: 1px solid #444; 
            border-radius: 12px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .input-field:focus-within {
            border-color: #FF9900;
            box-shadow: 0 0 0 3px rgba(255, 153, 0, 0.1), 0 0 15px rgba(255, 153, 0, 0.2);
            transform: translateY(-2px);
        }

        .input-field i {
            flex: 1;
            text-align: center;
            /* Icon màu cam để nổi bật trên nền đen */
            color: #FF9900; 
            font-size: 18px;
        }

        .input-field input, .input-field select {
            flex: 5;
            background: none;
            border: none;
            outline: none;
            width: 100%;
            font-size: 15px;
            font-weight: 400;
            color: #e0e0e0; 
            height: 100%; 
            padding-right: 15px;
        }
        
        /* Placeholder màu xám nhạt */
        .input-field input::placeholder {
            color: #888;
        }

        /* Fix màu option select */
        .input-field select option {
            background: #2b2b2b;
            color: #fff;
        }

        .btn {
            width: 100%;
            height: 50px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #FF9900, #ff8800); 
            color: #fff;
            font-weight: 700;
            font-size: 16px;
            margin: 25px 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 153, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn i {
            font-size: 14px;
        }

        .btn:hover {
            background: linear-gradient(135deg, #ff8800, #cc7a00);
            box-shadow: 0 6px 20px rgba(255, 153, 0, 0.4);
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .social-text {
            margin: 10px 0;
            font-size: 16px;
            color: #ccc;
        }


        
        /* Alert Messages */
        .alert-error {
            padding: 8px 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            /* Đổi màu alert cho hợp nền tối */
            color: #ff6b6b;
            background-color: rgba(255, 107, 107, 0.1); 
            border: 1px solid #ff6b6b;
            width: 100%;
            text-align: left;
            font-size: 13px;
        }
        .alert-error ul {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }
        
        /* Nút quay lại */
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 153, 0, 0.1);
            border: 1px solid rgba(255, 153, 0, 0.3);
            color: #FF9900;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            z-index: 10;
        }
        
        .back-button:hover {
            background: rgba(255, 153, 0, 0.2);
            border-color: #FF9900;
            transform: translateX(-3px);
        }
        
        .back-button i {
            font-size: 14px;
        }

        /* --- RESPONSIVE --- */
        @media(max-width:900px){
            .container {
                width: 95vw;
                flex-direction: column;
            }
            .branding-section {
                border-right: none;
                border-bottom: 1px solid rgba(255, 153, 0, 0.2);
                padding: 30px 20px;
            }
            .brand-logo {
                font-size: 36px;
            }
            .brand-title {
                font-size: 24px;
            }
            .form-section {
                padding: 30px 20px;
            }
        }
        @media(max-width:635px){
            .container {
                width: 100vw;
                height: 100vh;
                border-radius: 0;
                box-shadow: none;
            }
            .branding-section {
                padding: 20px;
            }
            .brand-logo {
                font-size: 32px;
                margin-bottom: 15px;
            }
            .brand-title {
                font-size: 20px;
                margin-bottom: 10px;
            }
            .brand-subtitle {
                font-size: 14px;
            }
            .form-section {
                padding: 20px;
            }
            form {
                max-width: 100%;
            }
            .back-button {
                top: 10px;
                left: 10px;
                padding: 8px 16px;
                font-size: 13px;
            }
        }
    </style>
</head>

<body>
    <div class="container" id="main-container">
        <a href="{{ route('home') }}" class="back-button">
            <i class="fas fa-arrow-left"></i>
            <span>Quay lại</span>
        </a>
        <div class="login-wrapper">
            {{-- Branding Section (Left) --}}
            <div class="branding-section">
                <div class="branding-content">
                    <div class="brand-logo">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h1 class="brand-title">Buffet Ocean</h1>
                    <p class="brand-subtitle">Chào mừng bạn trở lại!<br>Hãy đăng nhập để bắt đầu ca làm việc.</p>
                </div>
            </div>

            {{-- Form Section (Right) --}}
            <div class="form-section">
                <form method="POST" action="{{ route('login') }}" class="sign-in-form">
                    @csrf
                    
                    <h2 class="title">ĐĂNG NHẬP</h2>

                    @if ($errors->any())
                        <div class="alert-error">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    @if (session('success'))
                        <div class="alert-error" style="background-color: rgba(40, 167, 69, 0.2); color: #28a745; border-color: #28a745;">
                            <ul>
                                <li>{{ session('success') }}</li>
                            </ul>
                        </div>
                    @endif
                    
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Nhập email của bạn" required autofocus>
                    </div>

                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="mat_khau" name="mat_khau" placeholder="Nhập mật khẩu" required>
                    </div>

                    <button type="submit" class="btn">
                        <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                    </button>
                </form>
            </div>
        </div>
    </div>
    
</body>
</html>