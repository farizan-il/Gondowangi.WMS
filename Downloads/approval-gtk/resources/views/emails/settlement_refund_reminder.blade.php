<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Pengembalian Sisa Dana</title>
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
            background: #0e6a39;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
            border: 1px solid #dee2e6;
        }
        .info-box {
            background: white;
            border: 1px solid #e3f2fd;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
        }
        .amount {
            font-size: 1.2em;
            font-weight: bold;
            color: #0066cc;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 0.9em;
        }
        .highlight {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            padding: 10px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0;">
            <i class="fas fa-bell"></i> Pengingat Pengembalian Dana
        </h2>
    </div>

    <div class="content">
        <p><strong>Yth. {{ $requester->nama }},</strong></p>
        
        <p>Kami ingin mengingatkan Anda bahwa terdapat sisa dana dari settlement yang perlu dikembalikan.</p>

        <div class="info-box">
            <h4 style="margin-top: 0; color: #0066cc;">Detail Settlement:</h4>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 5px 0; font-weight: bold;">Nomor Pengajuan:</td>
                    <td style="padding: 5px 0;">{{ $pengajuan->nomor_pengajuan }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; font-weight: bold;">Nomor Settlement:</td>
                    <td style="padding: 5px 0;">{{ $settlement->nomor_settlement }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; font-weight: bold;">Judul Pengajuan:</td>
                    <td style="padding: 5px 0;">{{ $pengajuan->judul }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; font-weight: bold;">Budget Awal:</td>
                    <td style="padding: 5px 0;">Rp. {{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; font-weight: bold;">Total Actual:</td>
                    <td style="padding: 5px 0;">Rp. {{ number_format($settlement->total_actual, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="highlight">
            <h4 style="margin-top: 0; color: #856404;">
                <i class="fas fa-exclamation-triangle"></i> Sisa Dana yang Perlu Dikembalikan:
            </h4>
            <p class="amount" style="margin: 0;">
                Rp. {{ number_format($settlement->selisih, 0, ',', '.') }}
            </p>
        </div>

        <p><strong>Mohon segera melakukan pengembalian sisa dana tersebut sesuai dengan prosedur yang berlaku.</strong></p>

        <p>Jika Anda memerlukan bantuan atau memiliki pertanyaan mengenai proses pengembalian dana, silakan hubungi tim finance.</p>

        <p>Terima kasih atas perhatian dan kerjasamanya.</p>

        <div class="footer">
            <p>
                <strong>Tim Finance</strong><br>
                <em>Email otomatis - Mohon tidak membalas email ini</em><br>
                Dikirim pada: {{ now()->format('d/m/Y H:i:s') }}
            </p>
        </div>
    </div>
</body>
</html>