<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settlement Pengajuan Disetujui</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #28a745;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #28a745;
            margin: 0;
            font-size: 24px;
        }
        
        .status-badge {
            background-color: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            margin: 10px 0;
        }
        
        .content-section {
            margin-bottom: 25px;
        }
        
        .content-section h3 {
            color: #495057;
            border-left: 4px solid #28a745;
            padding-left: 15px;
            margin-bottom: 15px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .info-table th,
        .info-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        .info-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            width: 40%;
        }
        
        .amount {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
        
        .amount.negative {
            color: #dc3545;
        }
        
        .amount.positive {
            color: #17a2b8;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        
        .action-note {
            background-color: #e8f5e8;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #28a745;
            margin: 20px 0;
        }
        
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        .signature {
            margin-top: 30px;
            font-style: italic;
        }
        
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            
            .email-container {
                padding: 20px;
            }
            
            .info-table th,
            .info-table td {
                padding: 8px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 Settlement Disetujui</h1>
            <div class="status-badge">APPROVED</div>
        </div>

        <!-- Greeting -->
        <div class="greeting">
            <p>Halo <strong>{{ $requester_name }}</strong>,</p>
            <p>Kami dengan senang hati menginformasikan bahwa settlement untuk pengajuan Anda telah <strong>disetujui</strong>!</p>
        </div>

        <!-- Detail Pengajuan -->
        <div class="content-section">
            <h3>📋 Detail Pengajuan</h3>
            <table class="info-table">
                <tr>
                    <th>Nomor Pengajuan</th>
                    <td><strong>{{ $pengajuan_nomor }}</strong></td>
                </tr>
                <tr>
                    <th>Judul Pengajuan</th>
                    <td>{{ $pengajuan_judul }}</td>
                </tr>
                <tr>
                    <th>Nominal Pengajuan</th>
                    <td><span class="amount">{{ $mata_uang }} {{ number_format($pengajuan_nominal, 0, ',', '.') }}</span></td>
                </tr>
            </table>
        </div>

        <!-- Detail Settlement -->
        <div class="content-section">
            <h3>💰 Detail Settlement</h3>
            <table class="info-table">
                <tr>
                    <th>Nomor Settlement</th>
                    <td><strong>{{ $settlement_nomor }}</strong></td>
                </tr>
                <tr>
                    <th>Total Pengeluaran Aktual</th>
                    <td><span class="amount">{{ $mata_uang }} {{ number_format($settlement_total, 0, ',', '.') }}</span></td>
                </tr>
                <tr>
                    <th>Selisih</th>
                    <td>
                        @if($settlement_selisih > 0)
                            <span class="amount positive">+{{ $mata_uang }} {{ number_format($settlement_selisih, 0, ',', '.') }}</span>
                            <small>(Sisa yang harus dikembalikan)</small>
                        @elseif($settlement_selisih < 0)
                            <span class="amount negative">{{ $mata_uang }} {{ number_format($settlement_selisih, 0, ',', '.') }}</span>
                            <small>(Kekurangan yang perlu ditambahkan)</small>
                        @else
                            <span class="amount">{{ $mata_uang }} 0</span>
                            <small>(Pas)</small>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- Informasi Approval -->
        <div class="content-section">
            <h3>✅ Informasi Persetujuan</h3>
            <table class="info-table">
                <tr>
                    <th>Disetujui Oleh</th>
                    <td>{{ $approver_name }}</td>
                </tr>
                <tr>
                    <th>Tanggal Persetujuan</th>
                    <td>{{ $tanggal_approval }}</td>
                </tr>
            </table>
        </div>

        <!-- Action Note -->
        <div class="action-note">
            <p><strong>Pengajuan dan Settlement Anda Telah Disetujui ✅</strong></p>
            <p>
                Terima kasih atas kepercayaan Anda dalam menggunakan sistem pengajuan kami. 
                Proses pengajuan dan settlement yang Anda ajukan telah mendapatkan persetujuan dari seluruh lapisan yang terkait dan telah tercatat dalam sistem.
            </p>
            <p class="text-muted">
                <em>Jika ada pertanyaan lebih lanjut, silakan hubungi tim support kami. Kami siap membantu Anda.</em>
            </p>
        </div>

        <!-- Penutup -->
        <div class="signature">
            <p>Terima kasih atas kerjasama Anda dalam proses settlement ini.</p>
            <p>Jika ada pertanyaan, silakan hubungi tim Finance atau HR.</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Email ini dikirim otomatis oleh sistem {{ $company_name ?? 'Perusahaan' }}</p>
            <p>Tanggal: {{ $tanggal_approval }}</p>
            <p><em>Mohon jangan membalas email ini</em></p>
        </div>
    </div>
</body>
</html>