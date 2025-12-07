<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pengembalian Settlement</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            text-align: center;
            padding: 30px 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .success-icon {
            font-size: 48px;
            color: #28a745;
            margin-bottom: 15px;
        }
        .info-section {
            background-color: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-section h3 {
            margin: 0 0 15px 0;
            color: #28a745;
            font-size: 18px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
            flex: 1;
        }
        .info-value {
            flex: 2;
            text-align: right;
            color: #2c3e50;
        }
        .refund-status {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        .refund-status.pengembalian_ke_requester {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .refund-status.pengembalian_ke_perusahaan {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .refund-status.balance {
            background-color: #cce7ff;
            border-color: #b3d9ff;
            color: #004085;
        }
        .refund-amount {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e9ecef;
            font-size: 14px;
            color: #6c757d;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
            transition: transform 0.2s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .highlight {
            background-color: #fff3cd;
            padding: 15px;
            border-radius: 6px;
            border-left: 4px solid #ffc107;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="success-icon">✅</div>
            <h1>Settlement Disetujui</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                Halo <strong>{{ $data['requester_name'] }}</strong>,
            </div>
            
            <p>Selamat! Settlement untuk pengajuan Anda telah <strong>disetujui lengkap</strong> dan selesai diproses.</p>
            
            <div class="info-section">
                <h3>📋 Detail Settlement</h3>
                <div class="info-row">
                    <span class="info-label">Nomor Pengajuan:</span>
                    <span class="info-value"><strong>{{ $data['nomor_pengajuan'] }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nomor Settlement:</span>
                    <span class="info-value"><strong>{{ $data['nomor_settlement'] }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Kategori:</span>
                    <span class="info-value">{{ $data['kategori_pengajuan'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Actual:</span>
                    <span class="info-value"><strong>Rp {{ $data['total_actual'] }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Settlement:</span>
                    <span class="info-value">{{ $data['tanggal_settlement'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Disetujui oleh:</span>
                    <span class="info-value">{{ $data['approver_name'] }}</span>
                </div>
            </div>

            @if($data['refund_status'] == 'pengembalian_ke_requester')
            <div class="refund-status pengembalian_ke_requester">
                <h4 style="margin: 0 0 10px 0;">💰 Pengembalian Dana ke Anda</h4>
                <div class="refund-amount">+ Rp {{ $data['selisih'] }}</div>
                <p style="margin: 0;">Dana kelebihan akan dikembalikan kepada Anda melalui transfer bank atau metode yang telah ditentukan perusahaan.</p>
            </div>
            @elseif($data['refund_status'] == 'pengembalian_ke_perusahaan')
            <div class="refund-status pengembalian_ke_perusahaan">
                <h4 style="margin: 0 0 10px 0;">💳 Pembayaran Tambahan ke Perusahaan</h4>
                <div class="refund-amount">- Rp {{ $data['selisih'] }}</div>
                <p style="margin: 0;">Terdapat kekurangan dana yang perlu Anda bayar kepada perusahaan. Silakan hubungi tim finance untuk prosedur pembayaran.</p>
            </div>
            @else
            <div class="refund-status balance">
                <h4 style="margin: 0 0 10px 0;">⚖️ Settlement Balance</h4>
                <div class="refund-amount">Rp 0</div>
                <p style="margin: 0;">Tidak ada selisih. Settlement sudah balance, tidak ada pengembalian atau pembayaran tambahan.</p>
            </div>
            @endif

            <div class="highlight">
                <p style="margin: 0;"><strong>Status:</strong> Pengajuan Anda telah berstatus <strong>COMPLETED</strong> dan semua proses settlement telah selesai.</p>
            </div>

            <div style="text-align: center;">
                <a href="{{ $data['app_url'] }}/pengajuan/{{ $data['nomor_pengajuan'] }}" class="btn">
                    Lihat Detail Lengkap
                </a>
            </div>

            <p>Terima kasih atas kerjasama Anda dalam proses settlement ini.</p>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim secara otomatis dari sistem. Mohon jangan membalas email ini.</p>
            <p>Jika ada pertanyaan mengenai settlement atau pengembalian dana, silakan hubungi tim finance.</p>
        </div>
    </div>
</body>
</html>