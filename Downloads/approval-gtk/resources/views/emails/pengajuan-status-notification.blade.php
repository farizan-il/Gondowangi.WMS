<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Status Pengajuan</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            margin: 10px 0;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .status-revision {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .detail-box {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .detail-row {
            margin-bottom: 15px;
            display: flex;
            flex-wrap: wrap;
        }
        .detail-label {
            font-weight: bold;
            color: #333;
            min-width: 150px;
            margin-bottom: 5px;
        }
        .detail-value {
            color: #666;
            flex: 1;
        }
        .catatan-box {
            background-color: #fff8e1;
            border: 1px solid #ffcc02;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #dee2e6;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 500;
            margin: 15px 0;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .content {
                padding: 20px;
            }
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>📋 Notifikasi Status Pengajuan</h1>
        </div>
        
        <div class="content">
            <p>Halo <strong>{{ $pengajuan->requester->nama }}</strong>,</p>
            
            <p>Kami ingin memberitahu Anda bahwa ada pembaruan status pada pengajuan Anda:</p>
            
            <div class="detail-box">
                <div class="detail-row">
                    <div class="detail-label">Nomor Pengajuan:</div>
                    <div class="detail-value"><strong>{{ $pengajuan->nomor_pengajuan }}</strong></div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Judul Pengajuan:</div>
                    <div class="detail-value">{{ $pengajuan->judul }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Kategori:</div>
                    <div class="detail-value">{{ $pengajuan->kategoriPengajuan->nama ?? '-' }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Nominal:</div>
                    <div class="detail-value">{{ $pengajuan->mata_uang }} {{ number_format($pengajuan->nominal_pengajuan, 2, ',', '.') }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Status Terbaru:</div>
                    <div class="detail-value">
                        @if($status === 'approved')
                            <span class="status-badge status-approved">✅ Disetujui</span>
                        @elseif($status === 'rejected')
                            <span class="status-badge status-rejected">❌ Ditolak</span>
                        @elseif($status === 'revision')
                            <span class="status-badge status-revision">📝 Perlu Revisi</span>
                        @endif
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Diproses oleh:</div>
                    <div class="detail-value">{{ $approverName }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-label">Tanggal Update:</div>
                    <div class="detail-value">{{ now()->format('d F Y, H:i') }} WIB</div>
                </div>
            </div>
            
            @if($catatan)
            <div class="catatan-box">
                <h4 style="margin-top: 0; color: #f57c00;">📝 Catatan dari Approver:</h4>
                <p style="margin-bottom: 0;">{{ $catatan }}</p>
            </div>
            @endif
            
            {{-- Smart notification messages based on approval flow --}}
            @if($notificationType === 'final_approved')
                <div style="background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;">
                    <h3 style="margin: 0 0 10px 0; font-size: 20px;">🎉 SELAMAT! PENGAJUAN ANDA TELAH DISETUJUI LENGKAP</h3>
                    <p style="margin: 0; font-size: 16px; opacity: 0.9;">Pengajuan Anda telah melalui seluruh layer approval dan disetujui oleh semua approver!</p>
                </div>
                
                <div style="background-color: #e3f2fd; border-left: 4px solid #2196f3; padding: 20px; margin: 20px 0; border-radius: 5px;">
                    <h4 style="margin-top: 0; color: #1976d2;">📋 Langkah Selanjutnya:</h4>
                    <ul style="margin-bottom: 0; color: #424242;">
                        <li>✅ Pengajuan akan diteruskan ke <strong>Tim Finance</strong> untuk diproses</li>
                        <li>🏦 Tim Finance akan membuat <strong>Transaction Request (TR)</strong></li>
                        <li>💰 Setelah TR dibuat, pengajuan akan masuk ke <strong>antrian pembayaran</strong></li>
                        <li>📧 Anda akan menerima notifikasi lebih lanjut mengenai status pembayaran</li>
                    </ul>
                </div>
                
                <p style="color: #2e7d32; font-weight: 500;">
                    🕒 <strong>Estimasi Waktu:</strong> Proses di Tim Finance biasanya memakan waktu 14 hari kerja, tergantung kompleksitas dan nominal pengajuan.
                </p>
                
            @elseif($notificationType === 'partial_approved')
                <div style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;">
                    <h3 style="margin: 0 0 10px 0; font-size: 18px;">✨ PENGAJUAN DISETUJUI - TAHAP {{ $pengajuan->current_step - 1 }}</h3>
                    <p style="margin: 0; opacity: 0.9;">Pengajuan Anda telah disetujui di tahap ini dan akan dilanjutkan ke tahap approval berikutnya</p>
                </div>
                
                <div style="background-color: #fff3e0; border-left: 4px solid #ff9800; padding: 20px; margin: 20px 0; border-radius: 5px;">
                    <h4 style="margin-top: 0; color: #ef6c00;">🔄 Status Approval:</h4>
                    <p style="margin-bottom: 10px; color: #424242;">
                        <strong>Tahap Selesai:</strong> {{ $pengajuan->current_step - 1 }} dari {{ $pengajuan->total_step }} tahap
                    </p>
                    <div style="background-color: #e0e0e0; border-radius: 10px; height: 10px; margin: 10px 0;">
                        <div style="background: linear-gradient(90deg, #4CAF50 0%, #8BC34A 100%); height: 100%; border-radius: 10px; width: {{ (($pengajuan->current_step - 1) / $pengajuan->total_step) * 100 }}%;"></div>
                    </div>
                    <p style="margin-bottom: 0; color: #666; font-size: 14px;">
                        <strong>Selanjutnya:</strong> Menunggu approval dari approver tahap {{ $pengajuan->current_step }}
                    </p>
                </div>
                
            @elseif($status === 'rejected')
                <div style="background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%); color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;">
                    <h3 style="margin: 0 0 10px 0; font-size: 18px;">❌ PENGAJUAN DITOLAK</h3>
                    <p style="margin: 0; opacity: 0.9;">Mohon maaf, pengajuan Anda tidak dapat disetujui</p>
                </div>
                
                <div style="background-color: #ffebee; border-left: 4px solid #f44336; padding: 20px; margin: 20px 0; border-radius: 5px;">
                    <h4 style="margin-top: 0; color: #c62828;">📋 Yang Perlu Anda Lakukan:</h4>
                    <ul style="margin-bottom: 0; color: #424242;">
                        <li>📝 Periksa catatan dari approver di atas</li>
                        <li>🔍 Review kembali dokumen dan detail pengajuan</li>
                        <li>📞 Hubungi approver jika perlu klarifikasi lebih lanjut</li>
                        <li>🆕 Buat pengajuan baru dengan perbaikan yang diperlukan</li>
                    </ul>
                </div>
                
            @elseif($status === 'revision')
                <div style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;">
                    <h3 style="margin: 0 0 10px 0; font-size: 18px;">📝 PENGAJUAN PERLU REVISI</h3>
                    <p style="margin: 0; opacity: 0.9;">Pengajuan Anda memerlukan perbaikan sebelum dapat disetujui</p>
                </div>
                
                <div style="background-color: #fff8e1; border-left: 4px solid #ffc107; padding: 20px; margin: 20px 0; border-radius: 5px;">
                    <h4 style="margin-top: 0; color: #f57c00;">🔧 Langkah Perbaikan:</h4>
                    <ul style="margin-bottom: 0; color: #424242;">
                        <li>📋 Baca dengan teliti catatan dari approver di atas</li>
                        <li>✏️ Lakukan perbaikan sesuai dengan feedback yang diberikan</li>
                        <li>📎 Update dokumen pendukung jika diperlukan</li>
                        <li>🔄 Submit kembali pengajuan setelah revisi selesai</li>
                    </ul>
                </div>
            @endif
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/') }}" class="btn">🏠 Akses Sistem</a>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>Sistem Approval Pengajuan</strong></p>
            <p>Email ini dibuat secara otomatis, mohon jangan membalas email ini.</p>
            <p style="font-size: 12px; margin-top: 15px;">
                © {{ date('Y') }} PT. Your Company Name. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>