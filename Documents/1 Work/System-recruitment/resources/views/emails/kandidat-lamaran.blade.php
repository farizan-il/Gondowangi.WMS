<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Lamaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .content {
            margin-bottom: 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .info-box {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #1976d2;
        }
        .status-badge {
            display: inline-block;
            background-color: #ff9800;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .next-steps {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            margin: 20px 0;
        }
        .next-steps h3 {
            color: #495057;
            margin-top: 0;
            font-size: 16px;
        }
        .next-steps ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .next-steps li {
            margin-bottom: 8px;
        }
        .contact-info {
            background-color: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 20px;
            margin-top: 30px;
            font-size: 14px;
            color: #666;
        }
        .warning {
            background-color: #ffebee;
            border-left: 4px solid #f44336;
            padding: 10px;
            margin: 15px 0;
            border-radius: 5px;
            font-size: 14px;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .email-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">{{ config('app.name', 'Perusahaan') }}</div>
            <p>Tim Rekrutmen</p>
        </div>

        <div class="content">
            <div class="greeting">
                Terima kasih, {{ $kandidatNama }}!
            </div>

            <p>
                Kami telah menerima lamaran Anda untuk posisi <strong>{{ $posisiNama }}</strong> 
                yang telah Anda kirimkan pada tanggal {{ $tanggalLamaran }}.
            </p>

            <div class="info-box">
                <div class="info-item">
                    <span class="info-label">Nama:</span> {{ $kandidatNama }}
                </div>
                <div class="info-item">
                    <span class="info-label">Posisi yang dilamar:</span> {{ $posisiNama }}
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal lamaran:</span> {{ $tanggalLamaran }}
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span> 
                    <span class="status-badge">{{ $statusLamaran }}</span>
                </div>
            </div>

            <p>
                Lamaran Anda saat ini berstatus <strong>{{ $statusLamaran }}</strong> dan sedang dalam proses review 
                oleh tim rekrutmen kami. Kami akan meninjau semua dokumen dan informasi yang Anda berikan.
            </p>

            <div class="next-steps">
                <h3>📋 Langkah Selanjutnya:</h3>
                <ul>
                    <li>Tim rekrutmen akan melakukan review terhadap lamaran Anda</li>
                    <li>Jika profil Anda sesuai dengan kebutuhan, kami akan menghubungi Anda untuk tahap selanjutnya</li>
                    <li>Proses seleksi dapat memakan waktu 1-2 minggu, mohon bersabar menunggu</li>
                    <li>Anda dapat memantau status lamaran melalui dashboard kandidat di website kami</li>
                </ul>
            </div>

            <div class="contact-info">
                <h3>📞 Informasi Kontak:</h3>
                <p>
                    Jika Anda memiliki pertanyaan mengenai lamaran atau proses rekrutmen, 
                    jangan ragu untuk menghubungi tim HR kami:
                </p>
                <ul>
                    <li><strong>Email:</strong> hr@perusahaan.com</li>
                    <li><strong>Telepon:</strong> (021) 123-4567</li>
                    <li><strong>WhatsApp:</strong> +62 812-3456-7890</li>
                </ul>
            </div>

            <div class="warning">
                <strong>⚠️ Penting:</strong> Mohon jangan membalas email ini secara langsung. 
                Untuk komunikasi lebih lanjut, gunakan kontak yang tertera di atas.
            </div>

            <p>
                Sekali lagi, terima kasih atas minat Anda untuk bergabung dengan tim kami. 
                Kami menghargai waktu dan usaha yang Anda berikan dalam proses aplikasi ini.
            </p>

            <p>
                Salam hormat,<br>
                <strong>Tim Rekrutmen</strong><br>
                {{ config('app.name', 'Perusahaan') }}
            </p>
        </div>

        <div class="footer">
            <p>
                Email ini dikirim secara otomatis. Mohon jangan membalas email ini.<br>
                &copy; {{ date('Y') }} {{ config('app.name', 'Perusahaan') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>