{{-- File: resources/views/emails/settlement-refund-notification.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Pengembalian Dana Settlement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px 20px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .info-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #dc3545;
        }
        .amount-highlight {
            background: #dc3545;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            margin: 15px 0;
        }
        .steps {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .step {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .step:last-child {
            border-bottom: none;
        }
        .step-number {
            background: #007bff;
            color: white;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background: #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            color: #6c757d;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 15px 0;
        }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔄 Notifikasi Pengembalian Dana</h1>
        <p>Settlement memerlukan pengembalian sisa dana</p>
    </div>
    
    <div class="content">
        <h2>Halo, {{ $requesterName }}!</h2>
        
        <p>Kami ingin memberitahukan bahwa settlement Anda memerlukan <strong>pengembalian sisa dana</strong>. Berikut adalah detail informasinya:</p>
        
        <div class="info-box">
            <h3 style="margin-top: 0; color: #dc3545;">📋 Detail Settlement</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Nomor Settlement:</td>
                    <td style="padding: 8px 0;">{{ $settlementNumber }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Nomor Pengajuan:</td>
                    <td style="padding: 8px 0;">{{ $pengajuanNumber }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Kategori:</td>
                    <td style="padding: 8px 0;">{{ $kategori }}</td>
                </tr>
            </table>
        </div>
        
        <div class="amount-highlight">
            💰 Jumlah yang harus dikembalikan:<br>
            {{ $currency }} {{ $refundAmount }}
        </div>
        
        @if($additionalMessage)
        <div class="alert">
            <strong>📝 Pesan dari Finance:</strong><br>
            {{ $additionalMessage }}
        </div>
        @endif
        
        <div class="steps">
            <h3 style="margin-top: 0; color: #007bff;">📋 Langkah-langkah yang harus dilakukan:</h3>
            
            <div class="step">
                <span class="step-number">1</span>
                <strong>Transfer Dana</strong><br>
                Lakukan transfer pengembalian sisa dana sesuai dengan nominal yang tertera di atas.
            </div>
            
            <div class="step">
                <span class="step-number">2</span>
                <strong>Upload Bukti Transfer</strong><br>
                Setelah transfer selesai, login ke sistem dan upload bukti transfer pada halaman settlement.
            </div>
            
            <div class="step">
                <span class="step-number">3</span>
                <strong>Konfirmasi Selesai</strong><br>
                Sistem akan otomatis mengupdate status settlement setelah bukti transfer berhasil diupload.
            </div>
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/settlement') }}" class="btn">
                🔗 Akses Halaman Settlement
            </a>
        </div>
        
        <div class="alert">
            <strong>⚠️ Penting:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Mohon lakukan pengembalian dana maksimal <strong>3 hari kerja</strong> setelah menerima notifikasi ini</li>
                <li>Pastikan nominal transfer sesuai dengan jumlah yang tertera</li>
                <li>Upload bukti transfer dalam format JPG, PNG, atau PDF</li>
                <li>Jika ada kendala, silakan hubungi tim Finance</li>
            </ul>
        </div>
    </div>
    
    <div class="footer">
        <p>
            <strong>Tim Finance</strong><br>
            Email ini dikirim secara otomatis oleh sistem. Mohon jangan membalas email ini.<br>
            Jika ada pertanyaan, silakan hubungi tim Finance melalui kanal komunikasi resmi.
        </p>
        <hr style="margin: 20px 0; border: none; border-top: 1px solid #dee2e6;">
        <p style="font-size: 12px;">
            © {{ date('Y') }} Sistem Pengajuan. All rights reserved.<br>
            Dikirim pada {{ date('d/m/Y H:i:s') }}
        </p>
    </div>
</body>
</html>