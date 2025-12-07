<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settlement Mendekati Penyelesaian</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
            color: white;
            text-align: center;
            padding: 40px 30px;
            margin: 0;
            position: relative;
        }
        
        .warning-icon {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        
        .content {
            padding: 30px;
            background-color: #ffffff;
        }
        
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #495057;
        }
        
        .content p {
            color: #6c757d;
            margin-bottom: 20px;
        }
        
        .content h3 {
            color: #495057;
            margin-top: 25px;
            margin-bottom: 15px;
            font-size: 18px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 8px;
        }
        
        .info-section {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .info-section h3 {
            margin-top: 0;
            color: #495057;
            border-bottom: none;
            padding-bottom: 0;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f1f3f4;
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
            flex: 1;
            text-align: right;
            color: #6c757d;
        }
        
        .progress-bar {
            background-color: #e9ecef;
            height: 20px;
            border-radius: 10px;
            overflow: hidden;
            margin: 25px 0 5px 0;
            position: relative;
        }
        
        .progress-fill {
            background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
            height: 100%;
            transition: width 0.3s ease;
            border-radius: 10px;
        }
        
        .refund-status {
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            border-left: 4px solid;
        }
        
        .refund-status.pengembalian_ke_requester {
            background-color: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }
        
        .refund-status.pengembalian_ke_perusahaan {
            background-color: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }
        
        .refund-status.balance {
            background-color: #d1ecf1;
            border-left-color: #17a2b8;
            color: #0c5460;
        }
        
        .refund-status h4 {
            margin-top: 0;
            font-size: 16px;
        }
        
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            transition: transform 0.2s ease;
            margin: 20px 0;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            text-decoration: none;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 25px 30px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }
        
        .footer p {
            margin: 8px 0;
        }
        
        /* Responsive */
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 8px;
            }
            
            .header {
                padding: 30px 20px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .content {
                padding: 20px;
            }
            
            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .info-value {
                text-align: left;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="warning-icon">⚠️</div>
            <h1>Settlement Mendekati Penyelesaian</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                Halo <strong>{{ $data['requester_name'] }}</strong>,
            </div>
            
            <p>Settlement untuk pengajuan Anda sudah mendekati tahap penyelesaian. Berikut adalah detail terkini:</p>
            
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
            </div>

            <div class="info-section">
                <h3>📊 Progress Approval</h3>
                <div class="info-row">
                    <span class="info-label">Step Saat Ini:</span>
                    <span class="info-value"><strong>{{ $data['current_step'] }} dari {{ $data['total_step'] }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Sisa Step:</span>
                    <span class="info-value"><strong style="color: #dc3545;">{{ $data['steps_remaining'] }} step lagi</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Disetujui oleh:</span>
                    <span class="info-value">{{ $data['approver_name'] }}</span>
                </div>
            </div>

            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ ($data['current_step'] / $data['total_step']) * 100 }}%"></div>
            </div>
            <p style="text-align: center; font-size: 14px; color: #6c757d; margin-top: 5px;">
                Progress: {{ number_format(($data['current_step'] / $data['total_step']) * 100, 1) }}%
            </p>

            @if($data['selisih'] != '0')
            <div class="refund-status {{ $data['refund_status'] }}">
                <h4 style="margin: 0 0 10px 0;">💰 Status Pengembalian</h4>
                @if($data['refund_status'] == 'pengembalian_ke_requester')
                    <p style="margin: 0;"><strong>Ada kelebihan sebesar Rp {{ $data['selisih'] }}</strong><br>
                    Dana akan dikembalikan kepada Anda setelah settlement selesai disetujui.</p>
                @elseif($data['refund_status'] == 'pengembalian_ke_perusahaan')
                    <p style="margin: 0;"><strong>Ada kelebihan sebesar Rp {{ $data['selisih'] }}</strong><br>
                    Anda perlu melakukan pembayaran pengembalian kepada perusahaan.</p>
                @endif
            </div>
            @else
            <div class="refund-status balance">
                <h4 style="margin: 0 0 10px 0;">✅ Status Pengembalian</h4>
                <p style="margin: 0;"><strong>Tidak ada selisih</strong><br>
                Settlement sudah balance, tidak ada pengembalian.</p>
            </div>
            @endif

            <p>Settlement Anda tinggal <strong>{{ $data['steps_remaining'] }} step lagi</strong> untuk diselesaikan. Mohon pastikan semua dokumen dan informasi yang diperlukan sudah lengkap.</p>

            <div style="text-align: center;">
                <a href="{{ $data['app_url'] }}/pengajuan/{{ $data['nomor_pengajuan'] }}" class="btn">
                    Lihat Detail Settlement
                </a>
            </div>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim secara otomatis dari sistem. Mohon jangan membalas email ini.</p>
            <p>Jika ada pertanyaan, silakan hubungi tim finance melalui sistem internal.</p>
        </div>
    </div>
</body>
</html>