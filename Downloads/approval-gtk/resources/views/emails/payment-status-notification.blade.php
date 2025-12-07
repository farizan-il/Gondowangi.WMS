<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Status Pembayaran</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header.paid {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .header.waiting {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }
        .header.rejected {
            background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header .subtitle {
            font-size: 14px;
            opacity: 0.9;
            margin-top: 5px;
        }
        .content {
            padding: 30px;
        }
        .status-banner {
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
            color: white;
            font-weight: 600;
        }
        .status-banner.paid {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .status-banner.waiting {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }
        .status-banner.rejected {
            background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
        }
        .status-icon {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        .detail-box {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 25px;
            margin: 25px 0;
            border-radius: 5px;
        }
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .detail-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        .detail-label {
            font-weight: bold;
            color: #495057;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .detail-value {
            color: #212529;
            font-size: 16px;
            font-weight: 500;
        }
        .amount-highlight {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 20px;
            font-weight: bold;
        }
        .catatan-box {
            background-color: #fff8e1;
            border: 1px solid #ffcc02;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        .catatan-box h4 {
            margin-top: 0;
            color: #f57c00;
            font-size: 16px;
        }
        .download-section {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border-radius: 10px;
            padding: 25px;
            margin: 25px 0;
            text-align: center;
        }
        .download-btn {
            display: inline-block;
            background: linear-gradient(135deg, #2196f3 0%, #21cbf3 100%);
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 500;
            margin-top: 15px;
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(33, 150, 243, 0.3);
        }
        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(33, 150, 243, 0.4);
            text-decoration: none;
            color: white;
        }
        .next-steps {
            background-color: #e8f5e8;
            border-left: 4px solid #28a745;
            padding: 20px;
            margin: 25px 0;
            border-radius: 5px;
        }
        .next-steps.waiting {
            background-color: #fff8e1;
            border-left-color: #ffc107;
        }
        .next-steps.rejected {
            background-color: #ffebee;
            border-left-color: #dc3545;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 25px 30px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #dee2e6;
        }
        .contact-info {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        @media (max-width: 600px) {
            body { padding: 10px; }
            .content { padding: 20px; }
            .detail-grid { 
                grid-template-columns: 1fr;
                gap: 15px; 
            }
            .header h1 { font-size: 20px; }
            .status-icon { font-size: 2rem; }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!--<div class="header {{ $status }}">-->
        <!--    <h1>-->
        <!--        @if($status === 'paid')-->
        <!--            💰 Pembayaran Berhasil-->
        <!--        @elseif($status === 'rejected')-->
        <!--            ⚠️ Pembayaran Ditolak-->
        <!--        @else-->
        <!--            📋 Update Status Pembayaran-->
        <!--        @endif-->
        <!--    </h1>-->
        <!--    <div class="subtitle">Sistem Manajemen Pengajuan</div>-->
        <!--</div>-->
        
        <div class="content">
            <p>Halo <strong>{{ $pengajuan->requester->nama }}</strong>,</p>
            
            <p>Tim Finance telah memproses status pembayaran untuk pengajuan Anda:</p>
            
            <div class="status-banner {{ $status }}">
                <div class="status-icon">
                    @if($status === 'paid')
                        ✅
                    @elseif($status === 'rejected')
                        ❌
                    @else
                        ⏳
                    @endif
                </div>
                <h3 style="margin: 0; font-size: 24px;">
                    @if($status === 'paid')
                        PEMBAYARAN SELESAI
                    @elseif($status === 'rejected')
                        PEMBAYARAN DITOLAK
                    @else
                        MENUNGGU PROSES
                    @endif
                </h3>
            </div>
            
            <div class="detail-box">
                <h4 style="margin-top: 0; color: #495057; margin-bottom: 20px;">📋 Detail Pengajuan</h4>
                
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Nomor Pengajuan</div>
                        <div class="detail-value">{{ $pengajuan->nomor_pengajuan }}</div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Judul Pengajuan</div>
                        <div class="detail-value">{{ $pengajuan->judul }}</div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Kategori</div>
                        <div class="detail-value">{{ $pengajuan->kategoriPengajuan->nama ?? '-' }}</div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Nominal Pengajuan</div>
                        <div class="detail-value amount-highlight">
                            {{ $pengajuan->mata_uang }} {{ number_format($pengajuan->nominal_pengajuan, 2, ',', '.') }}
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Status Pembayaran</div>
                        <div class="detail-value" style="color: 
                            @if($status === 'paid') #28a745
                            @elseif($status === 'rejected') #dc3545
                            @else #ffc107 @endif;">
                            <strong>
                                @if($status === 'paid')
                                    ✅ Dibayarkan
                                @elseif($status === 'rejected')
                                    ❌ Ditolak
                                @else
                                    ⏳ Menunggu
                                @endif
                            </strong>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">Diproses Oleh</div>
                        <div class="detail-value">{{ $financeName }}</div>
                    </div>
                    
                    @if($transactionRequest->tanggal_transfer)
                    <div class="detail-item">
                        <div class="detail-label">Tanggal Transfer</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($transactionRequest->tanggal_transfer)->format('d F Y') }}</div>
                    </div>
                    @endif
                    
                    <div class="detail-item">
                        <div class="detail-label">Tanggal Update</div>
                        <div class="detail-value">{{ now()->format('d F Y, H:i') }} WIB</div>
                    </div>
                </div>
            </div>
            
            <!--@if($catatan)-->
            <!--<div class="catatan-box">-->
            <!--    <h4>💬 Catatan dari Finance:</h4>-->
            <!--    <p style="margin-bottom: 0; font-style: italic;">"{{ $catatan }}"</p>-->
            <!--    <p style="margin: 0; color: #212529; font-size: 14px; line-height: 1.6;">-->
            <!--        ⏳ <strong>Perhatian:</strong> Perhitungan argo dimulai <strong>21 hari setelah tim Finance melakukan transfer dana</strong>. -->
            <!--        Mohon untuk segera melakukan proses settlement <strong>sebelum periode argo tersebut berakhir</strong>.-->
            <!--    </p>-->
            <!--</div>-->
            <!--@endif-->
            <div class="catatan-box">
                <h4>💬 Catatan dari Finance:</h4>
                <p style="margin-bottom: 0; font-style: italic;">"{{ $catatan }}"</p>
                <p style="margin: 0; color: #212529; font-size: 14px; line-height: 1.6;">
                    ⏳ <strong>Perhatian:</strong> Perhitungan argo dimulai <strong>21 hari setelah tim Finance melakukan transfer dana</strong>. 
                    Mohon untuk segera melakukan proses settlement <strong>sebelum periode argo tersebut berakhir</strong>.
                </p>
            </div>
            
            @if($status === 'paid' && $transactionRequest->bukti_transfer)
            <div class="download-section">
                <h4 style="margin-top: 0; color: #1976d2;">📎 Bukti Transfer Tersedia</h4>
                <p style="margin: 10px 0; color: #424242;">Bukti transfer untuk pengajuan Anda sudah tersedia dan dapat diunduh.</p>
                <a href="{{ url('/pengajuan/' . $pengajuan->id . '/bukti-transfer') }}" class="download-btn">
                    📥 Download Bukti Transfer
                </a>
                <p style="margin: 15px 0 0 0; font-size: 12px; color: #666;">
                    * Simpan bukti transfer ini untuk keperluan administrasi Anda
                </p>
            </div>
            @endif
            
            <!--<div class="next-steps {{ $status }}">-->
            <!--    <h4 style="margin-top: 0; color: -->
            <!--        @if($status === 'paid') #28a745-->
            <!--        @elseif($status === 'rejected') #dc3545-->
            <!--        @else #f57c00 @endif;">-->
            <!--        🔄 Informasi Selanjutnya-->
            <!--    </h4>-->
                
            <!--    @if($status === 'paid')-->
            <!--        <ul style="margin-bottom: 0; color: #155724;">-->
            <!--            <li>✅ <strong>Pembayaran telah berhasil diproses</strong></li>-->
            <!--            <li>📄 Unduh dan simpan bukti transfer untuk dokumentasi</li>-->
            <!--            <li>📋 Proses pengajuan Anda telah selesai</li>-->
            <!--            <li>📞 Hubungi Finance jika ada pertanyaan terkait pembayaran</li>-->
            <!--        </ul>-->
            <!--    @elseif($status === 'rejected')-->
            <!--        <ul style="margin-bottom: 0; color: #721c24;">-->
            <!--            <li>❌ <strong>Pembayaran tidak dapat diproses</strong></li>-->
            <!--            <li>📝 Periksa catatan dari Finance di atas</li>-->
            <!--            <li>📞 Hubungi tim Finance untuk klarifikasi</li>-->
            <!--            <li>🔄 Perbaiki dokumen/data jika diperlukan</li>-->
            <!--        </ul>-->
            <!--    @else-->
            <!--        <ul style="margin-bottom: 0; color: #856404;">-->
            <!--            <li>⏳ <strong>Pembayaran sedang dalam proses</strong></li>-->
            <!--            <li>🔄 Tim Finance sedang memproses pengajuan Anda</li>-->
            <!--            <li>📧 Anda akan mendapat notifikasi update selanjutnya</li>-->
            <!--            <li>📞 Hubungi Finance jika ada pertanyaan</li>-->
            <!--        </ul>-->
            <!--    @endif-->
            <!--</div>-->
            
            <!-- 🔔 Section Argo -->
            <!--<div class="argo-info" style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-left: 4px solid #007bff;">-->
            <!--    <p style="margin: 0; color: #212529; font-size: 14px; line-height: 1.6;">-->
            <!--        ⏳ <strong>Perhatian:</strong> Perhitungan argo dimulai <strong>21 hari setelah tim Finance melakukan transfer dana</strong>. -->
            <!--        Mohon untuk segera melakukan proses settlement <strong>sebelum periode argo tersebut berakhir</strong>.-->
            <!--    </p>-->
            <!--</div>-->
            
            <div class="contact-info">
                <h5 style="margin-top: 0; color: #0056b3;">📞 Butuh Bantuan?</h5>
                <p style="margin-bottom: 10px; color: #495057;">Tim Finance kami siap membantu Anda:</p>
                <ul style="margin: 0; padding-left: 20px; color: #495057;">
                    <li>📧 Email: finance@company.com</li>
                    <li>📞 Telepon: (021) 1234-5678</li>
                    <li>🕐 Jam Kerja: Senin - Jumat, 08:00 - 17:00 WIB</li>
                </ul>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/') }}" style="
                    display: inline-block;
                    padding: 12px 24px;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    text-decoration: none;
                    border-radius: 25px;
                    font-weight: 500;
                    transition: transform 0.2s;
                ">🏠 Akses Sistem</a>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>Tim Finance - Sistem Manajemen Pengajuan</strong></p>
            <p>Email ini dibuat secara otomatis, mohon jangan membalas email ini.</p>
            <p style="font-size: 12px; margin-top: 15px;">
                © {{ date('Y') }} PT. Your Company Name. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>