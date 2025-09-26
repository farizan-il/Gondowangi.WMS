<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di {{ $app_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f8f9fa;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 20px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        
        .message {
            font-size: 16px;
            margin-bottom: 25px;
            color: #555555;
        }
        
        .welcome-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
            border-radius: 5px;
        }
        
        .welcome-box h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .welcome-box ul {
            list-style: none;
            padding: 0;
        }
        
        .welcome-box li {
            margin-bottom: 10px;
            padding-left: 25px;
            position: relative;
        }
        
        .welcome-box li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
        }
        
        .login-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.3s ease;
        }
        
        .login-button:hover {
            transform: translateY(-2px);
        }
        
        .info-section {
            background-color: #e3f2fd;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
            border: 1px solid #bbdefb;
        }
        
        .info-section h4 {
            color: #1565c0;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .info-section p {
            color: #1976d2;
            margin-bottom: 8px;
        }
        
        .footer {
            background-color: #2c3e50;
            color: white;
            padding: 30px;
            text-align: center;
            font-size: 14px;
        }
        
        .footer p {
            margin-bottom: 10px;
            opacity: 0.9;
        }
        
        .footer a {
            color: #74b9ff;
            text-decoration: none;
        }
        
        .social-links {
            margin-top: 20px;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #74b9ff;
            text-decoration: none;
        }
        
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }
            
            .header, .content, .footer {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .greeting {
                font-size: 18px;
            }
            
            .login-button {
                display: block;
                text-align: center;
                margin: 20px 0;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header Section -->
        <div class="header">
            <h1>Selamat Datang!</h1>
            <p>Terima kasih telah bergabung dengan {{ $app_name }}</p>
        </div>
        
        <!-- Content Section -->
        <div class="content">
            <div class="greeting">
                Halo {{ $name }}! 👋
            </div>
            
            <div class="message">
                Selamat datang di <strong>{{ $app_name }}</strong>! Kami sangat senang Anda telah bergabung dengan platform job portal kami. Akun Anda telah berhasil dibuat dan siap digunakan.
            </div>
            
            <div class="welcome-box">
                <h3>Apa yang bisa Anda lakukan sekarang?</h3>
                <ul>
                    <li>Lengkapi profil Anda untuk meningkatkan peluang diterima kerja</li>
                    <li>Lamar pekerjaan yang sesuai dengan keahlian Anda</li>
                    <li>Dapatkan notifikasi lowongan kerja terbaru</li>
                    <li>Pantau status lamaran Anda secara real-time</li>
                </ul>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ $login_url }}" class="login-button">
                    Mulai Sekarang - Login ke Akun Anda
                </a>
            </div>
            
            <div class="info-section">
                <h4>📧 Informasi Akun Anda:</h4>
                <p><strong>Email:</strong> {{ $email }}</p>
                <p><strong>Tanggal Registrasi:</strong> {{ $registration_date }}</p>
                <p><strong>Status Akun:</strong> Aktif</p>
            </div>
            
            <div class="message">
                <strong>Tips untuk memaksimalkan penggunaan platform:</strong>
                <br><br>
                1. <strong>Lengkapi profil Anda</strong> - Semakin lengkap profil, semakin mudah HRD menemukan Anda<br>
                2. <strong>Upload CV terbaru</strong> - Pastikan CV Anda up-to-date dan menarik<br>
                3. <strong>Aktifkan notifikasi</strong> - Jangan sampai melewatkan lowongan impian Anda<br>
                4. <strong>Follow up lamaran</strong> - Pantau terus status lamaran yang telah dikirim
            </div>
            
            <div class="message">
                Jika Anda memiliki pertanyaan atau membutuhkan bantuan, jangan ragu untuk menghubungi tim support kami di <a href="mailto:{{ $contact_email }}" style="color: #667eea;">{{ $contact_email }}</a>
            </div>
            
            <div class="message" style="font-style: italic; color: #777;">
                Sekali lagi, selamat datang di {{ $app_name }}! Mari wujudkan karir impian Anda bersama kami. 🚀
            </div>
        </div>
        
        <!-- Footer Section -->
        <div class="footer">
            <p><strong>{{ $app_name }}</strong></p>
            <p>Platform Job Portal Terpercaya</p>
            <p>Email ini dikirim otomatis, mohon tidak membalas email ini.</p>
            
            <div class="social-links">
                <a href="{{ $app_url }}">Website</a> |
                <a href="mailto:{{ $contact_email }}">Contact Support</a>
            </div>
            
            <p style="margin-top: 20px; font-size: 12px; opacity: 0.7;">
                © {{ date('Y') }} {{ $app_name }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>