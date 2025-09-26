<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .otp-code {
            background-color: #f8f9fa;
            border: 2px dashed #007bff;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-number {
            font-size: 36px;
            font-weight: bold;
            color: #007bff;
            letter-spacing: 5px;
            margin: 15px 0;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .button {
            display: inline-block;
            padding: 12px 25px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">PT Gondowangi Tradisional Kosmetika</div>
            <h2>Reset Password</h2>
        </div>

        <p>Halo <strong>{{ $name }}</strong>,</p>
        
        <p>Kami menerima permintaan untuk mereset kata sandi akun Anda. Gunakan kode OTP di bawah ini untuk melanjutkan proses reset password:</p>

        <div class="otp-code">
            <div style="margin-bottom: 10px; font-size: 16px; color: #666;">Kode OTP Anda:</div>
            <div class="otp-number">{{ $otp }}</div>
            <div style="margin-top: 10px; font-size: 14px; color: #666;">
                Berlaku hingga {{ $expires_at }} WIB
            </div>
        </div>

        <div class="warning">
            <strong>⚠️ Penting:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Kode OTP ini hanya berlaku selama <strong>10 menit</strong></li>
                <li>Jangan bagikan kode ini kepada siapapun</li>
                <li>Jika Anda tidak meminta reset password, abaikan email ini</li>
                <li>Untuk keamanan, silakan segera ganti password setelah berhasil login</li>
            </ul>
        </div>

        <p>Jika Anda mengalami kesulitan atau tidak meminta reset password, silakan hubungi tim support kami segera.</p>

        <p>Terima kasih,<br>
        <strong>Tim {{ $app_name ?? config('app.name') }}</strong></p>

        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} {{ $app_name ?? config('app.name') }}. All rights reserved.</p>
            <p>Jika Anda memerlukan bantuan, silakan hubungi support kami.</p>
        </div>
    </div>
</body>
</html>