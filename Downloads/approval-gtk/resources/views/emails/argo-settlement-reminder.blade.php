<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Settlement</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin: 20px;
        }
        .header {
            background-color: #0e6a39 ;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .urgency-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
        }
        .urgent { background-color: #ff4757; }
        .warning { background-color: #ffa502; }
        .overdue { background-color: #2f3542; }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .message {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        .pengajuan-details {
            background-color: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f1f3f4;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
        }
        .detail-value {
            color: #212529;
            text-align: right;
        }
        .countdown {
            text-align: center;
            margin: 25px 0;
        }
        .countdown-number {
            font-size: 48px;
            font-weight: bold;
            color: #e74c3c;
            line-height: 1;
        }
        .countdown-text {
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 5px;
        }
        .action-button {
            display: inline-block;
            background-color: #0e6a39;
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-weight: 600;
            text-align: center;
            margin: 20px auto;
            display: block;
            max-width: 200px;
            transition: transform 0.2s;
        }
        .action-button:hover {
            transform: translateY(-2px);
            text-decoration: none;
            color: white;
        }
        .warning-text {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        @media (max-width: 480px) {
            .email-container {
                margin: 10px;
            }
            .content {
                padding: 20px;
            }
            .countdown-number {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header" style="background-color: #0e6a39;">
            <h1>🚨 Pengingat Settlement</h1>
            @if($remainingDays <= 0)
                <div class="urgency-badge overdue">TERLAMBAT</div>
            @elseif($remainingDays <= 2)
                <div class="urgency-badge urgent">SANGAT MENDESAK</div>
            @else
                <div class="urgency-badge warning">MENDESAK</div>
            @endif
        </div>

        <div class="content">
            <div class="greeting">
                Halo <strong>{{ $requesterName }}</strong>,
            </div>

            <div class="message">
                @if($remainingDays <= 0)
                    <strong>⚠️ PERHATIAN:</strong> Batas waktu settlement untuk pengajuan Anda telah <strong>TERLEWATI</strong>. 
                    Segera buat settlement untuk menghindari konsekuensi lebih lanjut.
                @elseif($remainingDays <= 2)
                    <strong>🚨 URGENT:</strong> Pengajuan Anda memerlukan settlement dalam <strong>{{ $remainingDays }} hari</strong>. 
                    Segera buat settlement untuk menghindari keterlambatan!
                @else
                    <strong>⏰ REMINDER:</strong> Pengajuan Anda memerlukan settlement dalam <strong>{{ $remainingDays }} hari</strong>. 
                    Jangan lupa untuk membuat settlement sebelum batas waktu berakhir.
                @endif
            </div>

            <div class="pengajuan-details">
                <h3 style="margin-top: 0; color: #2c3e50;">📋 Detail Pengajuan</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Nomor Pengajuan:</span>
                    <span class="detail-value"><strong>{{ $pengajuan->nomor_pengajuan }}</strong></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Kategori:</span>
                    <span class="detail-value">{{ $kategori }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Judul:</span>
                    <span class="detail-value">{{ $pengajuan->judul }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Nominal:</span>
                    <span class="detail-value"><strong>{{ $pengajuan->mata_uang }} {{ $nominal }}</strong></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Batas Argo:</span>
                    <span class="detail-value"><strong>{{ $argoDate }}</strong></span>
                </div>
            </div>

            <div class="countdown">
                <div class="countdown-number">
                    @if($remainingDays <= 0)
                        ⚠️
                    @else
                        {{ $remainingDays }}
                    @endif
                </div>
                <div class="countdown-text">
                    @if($remainingDays <= 0)
                        BATAS WAKTU TERLEWATI
                    @elseif($remainingDays == 1)
                        Hari Tersisa
                    @else
                        Hari Tersisa
                    @endif
                </div>
            </div>

            <div class="warning-text">
                <strong>📢 Penting:</strong> Settlement yang tidak dibuat tepat waktu dapat mempengaruhi proses pengajuan Anda selanjutnya. 
                Pastikan untuk menyelesaikan settlement sebelum batas waktu argo berakhir.
            </div>

            <a href="{{ config('app.url') }}/dashboard" class="action-button">
                Buat Settlement Sekarang
            </a>

            <p style="text-align: center; color: #6c757d; margin-top: 30px;">
                Jika Anda mengalami kesulitan atau memiliki pertanyaan, silakan hubungi tim Finance.<br>
                <strong>Email akan dikirim setiap 5 jam hingga settlement dibuat.</strong>
            </p>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh sistem.<br>
            Jangan membalas email ini.</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>