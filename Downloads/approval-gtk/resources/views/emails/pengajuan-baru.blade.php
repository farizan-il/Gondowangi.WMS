<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Pengajuan Baru</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .pengajuan-info {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 6px 6px 0;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
            min-width: 150px;
            margin-right: 10px;
        }
        .info-value {
            color: #2c3e50;
            flex: 1;
        }
        .nominal {
            color: #e74c3c;
            font-weight: 600;
            font-size: 16px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            background-color: #ffc107;
            color: #212529;
        }
        .btn-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            transition: transform 0.2s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            text-decoration: none;
            color: white;
        }
        .footer {
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        .footer a {
            color: #3498db;
            text-decoration: none;
        }
        .divider {
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            margin: 20px 0;
            border-radius: 1px;
        }
        @media (max-width: 600px) {
            .info-row {
                flex-direction: column;
            }
            .info-label {
                min-width: auto;
                margin-bottom: 5px;
            }
            .container {
                margin: 10px;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Notifikasi Pengajuan Baru</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                Halo {{ $penerima->nama }},
            </div>
            
            <p>
                @if($tipePenerima == 'approver')
                    Anda mendapat pengajuan baru yang memerlukan persetujuan Anda.
                @else
                    Tim Anda telah membuat pengajuan baru yang perlu Anda ketahui.
                @endif
            </p>

            <div class="pengajuan-info" style="border-radius: 10px;">
                <div class="info-row">
                    <div class="info-label">Nomor Pengajuan:</div>
                    <div class="info-value"><strong>{{ $pengajuan->nomor_pengajuan }}</strong></div>
                </div>
                
                <!--<div class="info-row">-->
                <!--    <div class="info-label">Judul:</div>-->
                <!--    <div class="info-value">{{ $pengajuan->judul }}</div>-->
                <!--</div>-->
                
                <div class="info-row">
                    <div class="info-label">Kategori:</div>
                    <div class="info-value">{{ $pengajuan->kategoriPengajuan->nama }}</div>
                </div>
                
                <div class="info-row">
                    <div class="info-label">Pengaju:</div>
                    <div class="info-value">
                        {{ $pengajuan->requester->nama }}
                        @if($pengajuan->requester->department)
                            <small>({{ $pengajuan->requester->department->nama }})</small>
                        @endif
                    </div>
                </div>
                
                @if($pengajuan->nominal_pengajuan > 0)
                <div class="info-row">
                    <div class="info-label">Nominal:</div>
                    <div class="info-value nominal">
                        {{ $pengajuan->mata_uang }} {{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}
                    </div>
                </div>
                @endif
                
                <div class="info-row">
                    <div class="info-label">Tanggal Pengajuan:</div>
                    <div class="info-value">{{ $pengajuan->tanggal_pengajuan->format('d/m/Y H:i') }}</div>
                </div>
                
                @if($pengajuan->tanggal_kebutuhan)
                <div class="info-row">
                    <div class="info-label">Tanggal Kebutuhan:</div>
                    <div class="info-value">{{ $pengajuan->tanggal_kebutuhan->format('d/m/Y') }}</div>
                </div>
                @endif
                
                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <span class="status-badge">{{ ucfirst($pengajuan->status_pengajuan) }}</span>
                    </div>
                </div>
                
                @if($pengajuan->deskripsi)
                <div class="divider"></div>
                <div class="info-row">
                    <div class="info-label">Deskripsi:</div>
                    <div class="info-value">{{ $pengajuan->deskripsi }}</div>
                </div>
                @endif
            </div>

            @if($tipePenerima == 'approver')
            <div class="btn-container">
                <a href="https://rosybrown-peafowl-442992.hostingersite.com/" class="btn">
                    👁️ Lihat & Proses Pengajuan
                </a>
            </div>
            <p style="text-align: center; color: #7f8c8d; font-size: 14px;">
                <em>Harap segera menindaklanjuti pengajuan ini.</em>
            </p>
            @else
            <div class="btn-container">
                <a href="{{ $appUrl }}/pengajuan/{{ $pengajuan->id }}" class="btn">
                    👁️ Lihat Detail Pengajuan
                </a>
            </div>
            <p style="text-align: center; color: #7f8c8d; font-size: 14px;">
                <em>Pengajuan ini sedang dalam proses persetujuan.</em>
            </p>
            @endif
        </div>
        
        <div class="footer">
            <p>
                Email ini dikirim secara otomatis dari Sistem Approval.<br>
                Jangan membalas email ini. Jika ada pertanyaan, silakan hubungi administrator sistem.
            </p>
            <p>
                <a href="https://rosybrown-peafowl-442992.hostingersite.com/">🔗 Akses Sistem Approval</a>
            </p>
        </div>
    </div>
</body>
</html>