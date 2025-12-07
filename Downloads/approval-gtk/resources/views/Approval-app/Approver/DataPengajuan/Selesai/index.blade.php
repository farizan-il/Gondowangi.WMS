@extends('Approval-app.Layout.approver-main')

@section('head')
    <style>
        .timeline-modern {
        position: relative;
        padding: 2rem 0;
        }
        
        /* Vertical line tengah */
        .timeline-modern::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 50%;
        width: 2px;
        background: #e9ecef;
        }
        
        /* Item dasar */
        .timeline-modern .timeline-item {
        position: relative;
        width: 50%;
        padding: 1rem 2rem;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.6s ease-out;
        }
        
        /* Kartu */
        .timeline-modern .timeline-content {
        background: #fff;
        border: 2px solid #e0e0e0;
        border-radius: .5rem;
        border-radius: .5rrgb(201, 201, 201)
        box-shadow: 0 .25rem .5rem rgba(0,0,0,.1);
        padding: 1rem;
        }

        .timeline-modern .timeline-item.current .timeline-marker {
            background: #ffc107;
            border-color: #fff;
        }
        .timeline-modern .timeline-item.current .timeline-content {
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
        }
        
        /* Node bulat */
        .timeline-modern .timeline-item::after {
        content: '';
        position: absolute;
        top: 1.5rem;
        right: -9px; /* untuk kiri */
        width: 18px;
        height: 18px;
        background: #fff;
        border: 3px solid #49976d;
        border-radius: 50%;
        }
        
        /* Ganjil di kiri */
        .timeline-modern .timeline-item:nth-child(odd) {
        left: 0;
        text-align: right;
        }
        
        .timeline-modern .timeline-item:nth-child(odd) .timeline-content {
        margin-right: 2rem;
        }
        
        .timeline-modern .timeline-item:nth-child(odd)::after {
        right: -9px;
        }
        
        /* Genap di kanan */
        .timeline-modern .timeline-item:nth-child(even) {
        left: 50%;
        }
        
        .timeline-modern .timeline-item:nth-child(even) .timeline-content {
        margin-left: 2rem;
        }
        
        .timeline-modern .timeline-item:nth-child(even)::after {
        left: -9px;
        }
        
        /* State tampilan setelah muncul */
        .timeline-modern .timeline-item.show {
        opacity: 1;
        transform: translateY(0);
        }
    </style>
@endsection

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Pengajuan Selesai</h5>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <!-- prject ,team member start -->
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header">
                <div class="card-header-right">
                    <div class="btn-group card-option">
                        <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="feather icon-more-horizontal"></i>
                        </button>
                        <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                            <li class="dropdown-item full-card">
                                <a href="#!">
                                    <span><i class="feather icon-maximize"></i> maximize</span>
                                    <span style="display:none"><i class="feather icon-minimize"></i> Restore</span>
                                </a>
                            </li>
                            <li class="dropdown-item minimize-card">
                                <a href="#!">
                                    <span><i class="feather icon-minus"></i> collapse</span>
                                    <span style="display:none"><i class="feather icon-plus"></i> expand</span>
                                </a>
                            </li>
                            <li class="dropdown-item reload-card">
                                <a href="#!"><i class="feather icon-refresh-cw"></i> reload</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Aksi</th>
                                <th>Request ID</th>
                                <th>Request Date</th>
                                <th>Requester ID</th>
                                <th>Dept. ID</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th class="text-right">Amount</th>
                                <th>Currency</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Row 1 -->
                            <tr>
                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-success btn-sm rounded"
                                        data-bs-toggle="modal"
                                        data-bs-target="#detailSettlementModal">
                                        <strong class="text-dark">Detail</strong>
                                    </button>

                                    <button
                                        class="btn btn-secondary btn-sm rounded"
                                        data-bs-toggle="modal"
                                        data-bs-target="#timelineModal">
                                        Log
                                    </button>
                                </td>
                                <td>1001</td>
                                <td>2025-04-28 09:15</td>
                                <td>200</td>
                                <td>10</td>
                                <td>Reimbursement</td>
                                <td>Hotel dinas klien Surabaya</td>
                                <td class="text-right">1,500,000</td>
                                <td>IDR</td>
                                <td><span class="badge badge-success">Disetujui</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>                        
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->


<!-- Static Timeline Modal -->
<div class="modal fade" id="timelineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Riwayat Pengajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="timeline-modern list-unstyled m-0 p-0 position-relative">
                    <!-- Semua langkah disetujui (completed) -->
                    <li class="timeline-item completed">
                        <div class="timeline-content">
                            <small>01 Jul 2025 08:07</small>
                            <h6 style="color: #0e6a39;">
                                <strong>
                                    <i class="feather icon-check-circle icon-completed" style="margin-right: .5rem;"></i>
                                    Submit Pengajuan
                                </strong>
                            </h6>
                            <p>Pengajuan masuk ke sistem</p>
                        </div>
                    </li>

                    <li class="timeline-item completed">
                        <div class="timeline-content">
                            <small>01 Jul 2025 08:08</small>
                            <h6 style="color: #0e6a39;">
                                <strong>
                                    <i class="feather icon-check-circle icon-completed" style="margin-right: .5rem;"></i>
                                    Validasi Data & Workflow
                                </strong>
                            </h6>
                            <p>Sistem cek aturan dan level approval</p>
                        </div>
                    </li>

                    <li class="timeline-item completed">
                        <div class="timeline-content">
                            <small>01 Jul 2025 08:09</small>
                            <h6 style="color: #0e6a39;">
                                <strong>
                                    <i class="feather icon-check-circle icon-completed" style="margin-right: .5rem;"></i>
                                    Disetujui Supervisor
                                </strong>
                            </h6>
                            <p>Dikirim ke Manager</p>
                        </div>
                    </li>

                    <li class="timeline-item completed">
                        <div class="timeline-content">
                            <small>01 Jul 2025 09:00</small>
                            <h6 style="color: #0e6a39;">
                                <strong>
                                    <i class="feather icon-check-circle icon-completed" style="margin-right: .5rem;"></i>
                                    Disetujui Manager
                                </strong>
                            </h6>
                            <p>Dikirim ke Head Dept</p>
                        </div>
                    </li>

                    <li class="timeline-item completed">
                        <div class="timeline-content">
                            <small>01 Jul 2025 09:30</small>
                            <h6 style="color: #0e6a39;">
                                <strong>
                                    <i class="feather icon-check-circle icon-completed" style="margin-right: .5rem;"></i>
                                    Disetujui Head Dept
                                </strong>
                            </h6>
                            <p>Dikirim ke Finance</p>
                        </div>
                    </li>

                    <li class="timeline-item completed">
                        <div class="timeline-content">
                            <small>01 Jul 2025 09:45</small>
                            <h6 style="color: #0e6a39;">
                                <strong>
                                    <i class="feather icon-check-circle icon-completed" style="margin-right: .5rem;"></i>
                                    Disetujui Finance
                                </strong>
                            </h6>
                            <p>Dikirim ke Direktur</p>
                        </div>
                    </li>

                    <li class="timeline-item completed">
                        <div class="timeline-content">
                            <small>01 Jul 2025 10:30</small>
                            <h6 style="color: #0e6a39;">
                                <strong>
                                    <i class="feather icon-check-circle icon-completed" style="margin-right: .5rem;"></i>
                                    Disetujui Direktur
                                </strong>
                            </h6>
                            <p>Pengajuan Approved. Persiapkan Settlement</p>
                        </div>
                    </li>

                    <!-- Settlement Step -->
                    <!-- <li class="timeline-item completed">
                        <div class="timeline-content">
                            <small>02 Jul 2025 08:00</small>
                            <h6 style="color: #0e6a39;">
                                <strong>
                                    <i class="feather icon-check-circle icon-completed" style="margin-right: .5rem;"></i>
                                    Proses Settlement
                                </strong>
                            </h6>
                            <p>Otorisasi, Batching, Clearing, dan Transfer Dana</p>
                        </div>
                    </li> -->

                    <!-- Selesai -->
                    <li class="timeline-item completed">
                        <div class="timeline-content">
                            <small>03 Jul 2025 09:00</small>
                            <h6 style="color: #0e6a39;">
                                <strong>
                                    <i class="feather icon-check-circle icon-completed" style="margin-right: .5rem;"></i>
                                    Pengajuan Selesai
                                </strong>
                            </h6>
                            <p>Notifikasi lengkap dikirim</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal (static) -->
<!-- Detail Settlement Modal -->
<div class="modal fade" id="detailSettlementModal" tabindex="-1" aria-labelledby="detailSettlementLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content" id="detailSettlementContent">
      <div class="modal-header">
        <h5 class="modal-title" id="detailSettlementLabel">Detail Settlement Biaya Perjalanan #1001</h5>
        <button class="btn btn-sm btn-outline-secondary me-2" id="downloadPdfSettlementBtn">
          <i class="bi bi-download"></i> Unduh PDF
        </button>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">

        <!-- 1. Header Dokumen -->
        <dl class="row mb-4">
          <dt class="col-sm-3">Nama</dt>
          <dd class="col-sm-9">Budi Santoso</dd>
          <dt class="col-sm-3">Area</dt>
          <dd class="col-sm-9">Jakarta Selatan</dd>
          <dt class="col-sm-3">Periode</dt>
          <dd class="col-sm-9">2025-05-10 s/d 2025-05-12</dd>
          <dt class="col-sm-3">Tanggal Settlement</dt>
          <dd class="col-sm-9">2025-05-15</dd>
          <dt class="col-sm-3">No. Bukti Transfer</dt>
          <dd class="col-sm-8">TRX-20250515-009</dd>
          <dt class="col-sm-1">File</dt>
          <dd class="col-sm-8">
            <a href="{{ asset('assets/bukti_transfer_palsu.jpg') }}" target="_blank">
              <img src="{{ asset('assets/bukti_transfer_palsu.jpg') }}" class="img-fluid" style="max-height:100px;" alt="Bukti Transfer">
            </a>
          </dd>
        </dl>

        <!-- 2. Rincian Biaya -->
        <h6 class="mb-2">A. Rincian Biaya</h6>
        <div class="table-responsive mb-4">
          <table class="table table-bordered table-sm text-center align-middle">
            <thead class="table-light">
              <tr>
                <th>No.</th>
                <th class="text-start">Uraian</th>
                <th>Advance</th>
                <th>Settlement</th>
                <th>Variance</th>
                <th>Bukti Pembayaran</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td class="text-start">Transportasi Udara - Tiket PP</td>
                <td>1.200.000</td>
                <td>1.150.000</td>
                <td>-50.000</td>
                <td>
                  <a href="/uploads/bukti/tiket_udara.pdf" class="link-primary" target="_blank">
                    tiket_udara.pdf
                  </a>
                </td>
              </tr>
              <tr>
                <td>2</td>
                <td class="text-start">Transportasi Darat - Tol & Parkir</td>
                <td>200.000</td>
                <td>180.000</td>
                <td>-20.000</td>
                <td>
                  <a href="/uploads/bukti/tol_parkir.jpg" class="link-primary" target="_blank">
                    tol_parkir.jpg
                  </a>
                </td>
              </tr>
              <tr>
                <td>3</td>
                <td class="text-start">Airport Tax</td>
                <td>50.000</td>
                <td>50.000</td>
                <td>0</td>
                <td>
                  <a href="/uploads/bukti/airport_tax.pdf" class="link-primary" target="_blank">
                    airport_tax.pdf
                  </a>
                </td>
              </tr>
              <tr>
                <td>4</td>
                <td class="text-start">Hotel - 2 malam @300.000</td>
                <td>600.000</td>
                <td>650.000</td>
                <td>50.000</td>
                <td>
                  <a href="/uploads/bukti/hotel_invoice.pdf" class="link-primary" target="_blank">
                    hotel_invoice.pdf
                  </a>
                </td>
              </tr>
              <tr>
                <td>5</td>
                <td class="text-start">Makan - @50.000 x 3 hari</td>
                <td>150.000</td>
                <td>140.000</td>
                <td>-10.000</td>
                <td>
                  <a href="/uploads/bukti/struk_makan1.jpg" class="link-primary" target="_blank">
                    struk_makan1.jpg
                  </a>
                </td>
              </tr>
              <tr>
                <td>6</td>
                <td class="text-start">Uang Saku - @25.000 x 4 hari</td>
                <td>100.000</td>
                <td>100.000</td>
                <td>0</td>
                <td>
                  <a href="/uploads/bukti/uang_saku.pdf" class="link-primary" target="_blank">
                    uang_saku.pdf
                  </a>
                </td>
              </tr>
              <tr>
                <td>7</td>
                <td class="text-start">Telephone & Fax</td>
                <td>30.000</td>
                <td>25.000</td>
                <td>-5.000</td>
                <td>
                  <a href="/uploads/bukti/fax_telephone.jpg" class="link-primary" target="_blank">
                    fax_telephone.jpg
                  </a>
                </td>
              </tr>
              <tr>
                <td>8</td>
                <td class="text-start">Entertainment - Klien Dinner</td>
                <td>50.000</td>
                <td>60.000</td>
                <td>10.000</td>
                <td>
                  <a href="/uploads/bukti/entertainment.pdf" class="link-primary" target="_blank">
                    entertainment.pdf
                  </a>
                </td>
              </tr>
              <tr>
                <td>9</td>
                <td class="text-start">Dokumentasi - Foto Kegiatan</td>
                <td>25.000</td>
                <td>25.000</td>
                <td>0</td>
                <td>
                  <a href="/uploads/bukti/dokumentasi.jpg" class="link-primary" target="_blank">
                    dokumentasi.jpg
                  </a>
                </td>
              </tr>
              <tr>
                <td>10</td>
                <td class="text-start">Lain-lain - Parkir Malam</td>
                <td>10.000</td>
                <td>15.000</td>
                <td>5.000</td>
                <td>
                  <a href="/uploads/bukti/parkir_malam.pdf" class="link-primary" target="_blank">
                    parkir_malam.pdf
                  </a>
                </td>
              </tr>
              <tr class="fw-bold">
                <td colspan="2" class="text-end">Grand Total:</td>
                <td>2.615.000</td>
                <td>2.695.000</td>
                <td>80.000</td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- 3. Approval Workflow -->
        <!-- 3. Approval Workflow -->
        <h6 class="mt-4">D. Pengesahan</h6>
<div class="table-responsive mb-4">
  <table class="table table-bordered">
    <thead>
      <tr class="text-center">
        <th>DIBUAT</th>
        <th>DIPERIKSA</th>
        <th>DISETUJUI</th>
        <th>DIPERIKSA</th>
      </tr>
    </thead>
    <tbody>
      <!-- Baris cap/stempel -->
      <tr class="text-center" style="height:80px;">
        <td>
          <img src="{{ asset('assets/stamp-approved.png') }}" alt="Stamp Dibuat" style="max-height:60px;">
        </td>
        <td>
          <img src="{{ asset('assets/stamp-approved.png') }}" alt="Stamp Diperiksa" style="max-height:60px;">
        </td>
        <td>
          <img src="{{ asset('assets/stamp-approved.png') }}" alt="Stamp Disetujui" style="max-height:60px;">
        </td>
        <td>
          <img src="{{ asset('assets/stamp-approved.png') }}" alt="Stamp Diperiksa Akhir" style="max-height:60px;">
        </td>
      </tr>
      <!-- Baris tanggal -->
      <tr>
        <td class="text-center"><strong>Tgl.</strong> 07/05/2025</td>
        <td class="text-center"><strong>Tgl.</strong> 08/05/2025</td>
        <td class="text-center"><strong>Tgl.</strong> 09/05/2025</td>
        <td class="text-center"><strong>Tgl.</strong> 10/05/2025</td>
      </tr>
      <!-- Baris keterangan dengan teks statis -->
      <tr>
        <td colspan="4">
          <strong>KETERANGAN :</strong><br>
          1. Dokumen sudah diperiksa sesuai prosedur internal.<br>
          2. Semua bukti transaksi terlampir lengkap dan sah.<br>
          3. Tidak ada selisih lebih dari toleransi perusahaan.<br>
          4. Laporan siap untuk proses transfer saldo akhir.
        </td>
      </tr>
    </tbody>
  </table>
</div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const items = document.querySelectorAll('.timeline-modern .timeline-item');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                    observer.unobserve(entry.target);
                }
                });
            }, { threshold: 0.2 });
            
            items.forEach(item => observer.observe(item));
        });
    </script>
    
@endsection