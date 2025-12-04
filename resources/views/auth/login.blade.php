<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập/Đăng ký Hệ thống Buffet Ocean</title>
    
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
            width: 70vw;
            height: 80vh;
            /* Đổi nền Container sang màu tối (Đen xám) */
            background: #1f1f1f; 
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 14px 28px rgba(0,0,0,0.5), 0 10px 10px rgba(0,0,0,0.5);
            animation: containerPopUp 1s cubic-bezier(0.19, 1, 0.22, 1) forwards; 
            border: 1px solid #333;
        }

        .container::before {
            content: "";
            position: absolute;
            top: 0;
            left: -50%;
            width: 100%;
            height: 100%;
            /* Gradient cam đen để hợp tông với theme */
            background: linear-gradient(-45deg, #FF9900, #141414);
            z-index: 6;
            transform: translateX(100%);
            transition: 1s ease-in-out;
        }

        .singin-singup {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-around;
            z-index: 5;
        }

        form {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            width: 40%;
            min-width: 238px;
            padding: 0 10px;
        }

        form.sign-in-form {
            opacity: 1;
            transition: 0.5s ease-in-out;
            transition-delay: 1s;
        }
        form.sign-up-form {
            opacity: 0;
            transition: 0.5s ease-in-out;
            transition-delay: 1s;
        }

        .title {
            font-size: 32px;
            /* Đổi màu chữ tiêu đề sang trắng */
            color: #ffffff; 
            margin-bottom: 15px;
            text-align: center;
            font-weight: 700;
        }

        /* Style mới cho ô nhập liệu (Dark Theme) */
        .input-field {
            width: 100%;
            height: 50px;
            /* Nền input tối */
            background: #2b2b2b; 
            margin: 10px 0;
            /* Viền mỏng màu cam tối hơn chút */
            border: 1px solid #444; 
            border-radius: 8px; /* Bo góc vuông hơn chút cho hiện đại */
            display: flex;
            align-items: center;
            transition: 0.3s;
        }
        
        .input-field:focus-within {
            border-color: #FF9900;
            box-shadow: 0 0 5px rgba(255, 153, 0, 0.3);
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
            font-size: 16px;
            font-weight: 500;
            /* Chữ khi gõ màu trắng */
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
            width: 150px;
            height: 48px;
            border: none;
            border-radius: 8px; /* Bo góc vuông hơn */
            background: #FF9900; 
            color: #fff;
            font-weight: 700;
            margin: 10px 0;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }

        .btn:hover {
            background: #cc7a00; 
        }

        .social-text {
            margin: 10px 0;
            font-size: 16px;
            color: #ccc;
        }

        .panels-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-around;
        }

        .panel {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-around;
            width: 35%;
            min-width: 238px;
            padding: 0 10px;
            text-align: center;
            z-index: 6;
        }

        .left-panel {
            pointer-events: none;
        }

        .content {
            color: #fff;
            transition: 1.1s ease-in-out;
            transition-delay: 0.5s;
        }

        .panel h3 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .panel p {
            font-size: 15px;
            padding: 10px 0;
            color: #f0f0f0;
        }

        .image {
            width: 100%;
            transition: 1.1s ease-in-out;
            transition-delay: 0.4s;
        }
        
        /* Nút trong suốt bên panel */
        .btn.transparent {
            background: none;
            border: 2px solid #fff;
            color: #fff;
            width: 130px;
            height: 41px;
            font-weight: 600;
            font-size: 14px;
        }

        .btn.transparent:hover {
            background: #fff;
            color: #FF9900;
        }

        .left-panel .image,
        .left-panel .content {
            transform: translateX(-200%);
        }

        .right-panel .image,
        .right-panel .content {
            transform: translateX(0);
        }

        .account-text {
            display: none;
            margin-top: 20px;
            font-size: 14px;
            color: #ccc;
        }

        .account-text a {
            color: #FF9900; 
            font-weight: bold;
            text-decoration: none;
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
        
        /* --- ANIMATION LOGIC (Giữ nguyên) --- */
        .container.sign-up-mode::before {
            transform: translateX(0);
        }
        .container.sign-up-mode .right-panel .image,
        .container.sign-up-mode .right-panel .content {
            transform: translateX(200%);
        }
        .container.sign-up-mode .left-panel .image,
        .container.sign-up-mode .left-panel .content {
            transform: translateX(0);
        }
        .container.sign-up-mode form.sign-in-form {
            opacity: 0;
            pointer-events: none;
        }
        .container.sign-up-mode form.sign-up-form {
            opacity: 1;
            pointer-events: all;
        }
        .container.sign-up-mode .right-panel {
            pointer-events: none;
        }
        .container.sign-up-mode .left-panel {
            pointer-events: all;
        }

        /* --- RESPONSIVE --- */
        @media(max-width:779px){
            .container {
                width: 100vw;
                height: 100vh;
                border-radius: 0;
                box-shadow: none;
                animation: bgFadeIn 1s ease-out; 
                background: rgba(31, 31, 31, 0.95); /* Mobile nền hơi trong suốt */
            }
        }
        @media(max-width:635px){
            .container::before {
                display: none;
            }
            form {
                width: 90%;
            }
            .singin-singup {
                justify-content: center;
            }
            form.sign-in-form, form.sign-up-form {
                transition: none;
                transition-delay: 0s;
            }
            form.sign-up-form {
                display: none;
            }
            form.sign-in-form {
                opacity: 1;
            }
            .panels-container {
                display: none;
            }
            .account-text {
                display: initial;
            }
            .container.sign-up-mode form.sign-in-form {
                display: none;
                opacity: 0;
            }
            .container.sign-up-mode form.sign-up-form {
                display: flex;
                opacity: 1;
            }
            .container.sign-up-mode .singin-singup {
                transform: none;
            }
        }
    </style>
</head>
@php
    $showSignUpMode = false;
    $hasSignUpError = false;
    $autoSlideBack = false; 

    if (
        $errors->has('ho_ten') || 
        $errors->has('sdt') || 
        $errors->has('email') || 
        $errors->has('mat_khau') || 
        $errors->has('vai_tro') || 
        (old('ho_ten') || old('sdt') || old('vai_tro')) 
    ) {
        $hasSignUpError = true;
    }

    if ($hasSignUpError && !session('success')) {
        $showSignUpMode = true;
    }
    
    // Logic trượt về khi đăng ký thành công
    if (session('success')) {
        $showSignUpMode = true; 
        $autoSlideBack = true;  
    }
    
    $initialClass = $showSignUpMode ? 'sign-up-mode' : '';
@endphp

<body>
    <div class="container {{ $initialClass }}" id="main-container" data-slide-back="{{ $autoSlideBack ? 'true' : 'false' }}">
        <div class="singin-singup">

            <form method="POST" action="{{ route('login') }}" class="sign-in-form">
                @csrf
                
                <h2 class="title">ĐĂNG NHẬP</h2>

                @if ($errors->any() || session('success'))
                    <div class="alert-error" style="{{ session('success') ? 'background-color: rgba(40, 167, 69, 0.2); color: #28a745; border-color: #28a745;' : '' }}">
                        <ul>
                            @if (session('success'))
                                <li>{{ session('success') }}</li>
                            @else
                                @if (!$hasSignUpError || session('success'))
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                @endif
                            @endif
                        </ul>
                    </div>
                @endif
                
                <div class="input-field">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Email" required autofocus>
                </div>

                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="mat_khau" name="mat_khau" placeholder="Mật khẩu" required>
                </div>

                <button type="submit" class="btn">Đăng nhập</button>
                
                <p class="account-text">Bạn chưa có tài khoản? <a href="#" id="sign-up-btn2">Đăng ký</a></p>
            </form>

            <form method="POST" action="{{ route('register.store') }}" class="sign-up-form">
                @csrf
                
                <h2 class="title">ĐĂNG KÝ NV</h2>

                @if ($hasSignUpError && !session('success'))
                    <div class="alert-error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <div class="input-field">
                    <i class="fas fa-user"></i>
                    <input type="text" name="ho_ten" value="{{ old('ho_ten') }}" placeholder="Họ và tên" required>
                </div>
                
                <div class="input-field">
                    <i class="fas fa-phone"></i>
                    <input type="text" name="sdt" value="{{ old('sdt') }}" placeholder="Số điện thoại" required>
                </div>

                <div class="input-field">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
                </div>
                
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="mat_khau" placeholder="Mật khẩu (min 6 ký tự)" required>
                </div>
                
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="mat_khau_confirmation" placeholder="Xác nhận Mật khẩu" required>
                </div>

                <div class="input-field">
                    <i class="fas fa-briefcase"></i>
                    <select name="vai_tro" required>
                        <option value="" disabled {{ old('vai_tro') == '' ? 'selected' : '' }} >Chọn Vai trò</option>
                        <option value="phuc_vu" {{ old('vai_tro') == 'phuc_vu' ? 'selected' : '' }}>Phục vụ</option>
                        <option value="bep" {{ old('vai_tro') == 'bep' ? 'selected' : '' }}>Bếp</option>
                    </select>
                </div>

                <button type="submit" class="btn">Đăng ký</button>
                
                <p class="account-text">Bạn đã có tài khoản? <a href="#" id="sign-in-btn2">Đăng nhập</a></p>
            </form>
        </div>

        <div class="panels-container">
            <div class="panel left-panel">
                <div class="content">
                    <h3>Buffet Ocean</h3>
                    <p>Chào mừng bạn trở lại! Hãy đăng nhập để bắt đầu ca làm việc.</p>
                    <button class="btn transparent" id="sign-in-btn">Đăng nhập</button>
                </div>
            </div>
            <div class="panel right-panel">
                <div class="content">
                    <h3>Thành viên mới?</h3>
                    <p>Đăng ký tài khoản nhân viên Bếp hoặc Phục vụ ngay tại đây.</p>
                    <button class="btn transparent" id="sign-up-btn">Đăng ký</button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        const sign_in_btn = document.querySelector("#sign-in-btn");
        const sign_up_btn = document.querySelector("#sign-up-btn");
        const container = document.querySelector("#main-container");
        const sign_in_btn2 = document.querySelector("#sign-in-btn2");
        const sign_up_btn2 = document.querySelector("#sign-up-btn2");

        const shouldSlideBack = container.getAttribute('data-slide-back') === 'true';

        if (shouldSlideBack) {
            setTimeout(() => {
                container.classList.remove("sign-up-mode");
            }, 500);
        }

        sign_up_btn.addEventListener("click", () => {
            container.classList.add("sign-up-mode");
        });

        sign_in_btn.addEventListener("click", () => {
            container.classList.remove("sign-up-mode");
        });

        sign_up_btn2.addEventListener("click", (e) => {
            e.preventDefault(); 
            container.classList.add("sign-up-mode");
        });

        sign_in_btn2.addEventListener("click", (e) => {
            e.preventDefault(); 
            container.classList.remove("sign-up-mode");
        });
    </script>
</body>
</html>