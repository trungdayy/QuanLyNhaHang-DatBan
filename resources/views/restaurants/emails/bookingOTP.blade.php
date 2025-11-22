<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            background-color: #fff;
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            border-radius: 6px 6px 0 0;
            text-align: center;
            font-size: 20px;
        }

        .content {
            margin-top: 20px;
            font-size: 16px;
            color: #333;
        }

        .otp {
            display: block;
            margin: 20px 0;
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            text-align: center;
            letter-spacing: 4px;
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #777;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">XÁC THỰC ĐẶT BÀN</div>
        <div class="content">
            <p>Xin chào,</p>
            <p>Bạn vừa yêu cầu đặt bàn tại nhà hàng của chúng tôi. Mã OTP để xác thực đặt bàn là:</p>
            <span class="otp">{{ $otp }}</span>
            <p>Mã này có hiệu lực trong 5 phút. Vui lòng không chia sẻ mã OTP này với bất kỳ ai.</p>
        </div>
        <div class="footer">
            Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi.
        </div>
    </div>
</body>

</html>