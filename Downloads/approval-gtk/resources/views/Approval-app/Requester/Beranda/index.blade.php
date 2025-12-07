@extends('Approval-app.Layout.main')

@section('head')
    
@endsection

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Dashboard Pengajuan</h5>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- stat cards -->
<div class="row mb-2">
  <div class="col-md-3">
    <div class="card ">
      <div class="card-body">
        <h6 class="card-title">Total Pengajuan</h6>
        <h3 class="card-text">10</h3>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Menunggu</h6>
        <h3 class="card-text">4</h3>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Diterima</h6>
        <h3 class="card-text">3</h3>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Ditolak</h6>
        <h3 class="card-text">3</h3>
      </div>
    </div>
  </div>
</div>

<!-- [ Main Content ] start -->
<div class="row">
  <div class="col-12">
    <div class="card table-card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Daftar Pengajuan Saya</h5>
        <a href="/Pengajuan" class="btn btn-sm btn-primary">
          <i class="feather icon-plus"></i> Buat Pengajuan Baru
        </a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Periode</th>
                <th>Tipe</th>
                <th>Status</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {{-- Contoh data statis --}}
              <tr>
                <td>1001</td>
                <td>01/05/2025</td>
                <td>01/05/2025 – 03/05/2025</td>
                <td>Perdin</td>
                <td><span class="badge bg-secondary">Menunggu</span></td>
                <td class="text-center">
                  <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal1001">Detail</button>
                </td>
              </tr>
              <tr>
                <td>1002</td>
                <td>05/05/2025</td>
                <td>05/05/2025 – 07/05/2025</td>
                <td>Settlement</td>
                <td><span class="badge bg-success">Diterima</span></td>
                <td class="text-center">
                  <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal1002">Detail</button>
                </td>
              </tr>
              <tr>
                <td>1003</td>
                <td>10/05/2025</td>
                <td>10/05/2025 – 12/05/2025</td>
                <td>Perdin</td>
                <td><span class="badge bg-danger">Ditolak</span></td>
                <td class="text-center">
                  <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal1003">Detail</button>
                </td>
              </tr>
              {{-- ... --}}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- [ Main Content ] end -->

<div class="modal fade" id="detailModal1001" tabindex="-1" aria-labelledby="detailModalLabel1001" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Pengajuan #1001</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        {{-- Reuse detail markup sebelumnya --}}
        <dl class="row mb-3">
          <dt class="col-sm-3">Nama</dt><dd class="col-sm-9">Budi Santoso</dd>
          <dt class="col-sm-3">Area</dt><dd class="col-sm-9">Jakarta Selatan</dd>
          <dt class="col-sm-3">Periode</dt><dd class="col-sm-9">01/05/2025 s/d 03/05/2025</dd>
        </dl>
        <h6 class="mt-4">A. Biaya Yang Diperlukan</h6>
        <div class="table-responsive mb-4">
          <table class="table table-bordered table-sm text-center">
            <thead class="table-light">
              <tr>
                <th>No</th><th class="text-start">Uraian</th><th>Perjalanan 1</th><th>Perjalanan 2</th><th>Perjalanan 3</th><th>Total</th>
              </tr>
            </thead>
            <tbody>
              <tr><td>1</td><td class="text-start">Transportasi Udara</td><td>Rp 1.200.000</td><td>Rp 0</td><td>Rp 0</td><td>Rp 1.200.000</td></tr>
              <tr class="fw-bold"><td colspan="5" class="text-end">Grand Total</td><td>Rp 1.200.000</td></tr>
            </tbody>
          </table>
        </div>
        <h6 class="mt-4">B. Tujuan Perjalanan</h6>
        <p>Perjalanan 1: Jakarta – Bogor (01/05/2025)</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endsection