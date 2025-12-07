@extends('Approval-app.Layout.approver-main')

@section('head')
    
@endsection

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Dashboard Approver</h5>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- Statistik Pengajuan -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card ">
      <div class="card-body">
        <h6 class="card-title">Total Pengajuan</h6>
        <h3 class="card-text">8</h3>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card ">
      <div class="card-body">
        <h6 class="card-title">Menunggu</h6>
        <h3 class="card-text">4</h3>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card ">
      <div class="card-body">
        <h6 class="card-title">Diterima</h6>
        <h3 class="card-text">2</h3>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card ">
      <div class="card-body">
        <h6 class="card-title">Ditolak</h6>
        <h3 class="card-text">2</h3>
      </div>
    </div>
  </div>
</div>

<!-- Daftar Pengajuan -->
<div class="row">
  <div class="col-12">
    <div class="card table-card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Daftar Pengajuan Masuk</h5>
        <button id="toggleCompletedBtn" class="btn btn-sm btn-outline-primary">
          Sembunyikan Selesai
        </button>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0" id="pengajuanTable">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Tanggal Request</th>
                <th>Nama</th>
                <th>Periode</th>
                <th>Tipe</th>
                <th>Status</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {{-- Baris 1 --}}
              <tr>
                <td>1001</td>
                <td>01/05/2025</td>
                <td>Budi Santoso</td>
                <td>01/05 – 03/05</td>
                <td>Perdin</td>
                <td><span class="badge bg-secondary">Menunggu</span></td>
                <td class="text-center">
                  <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detail1001">Detail</button>
                  <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#status1001">Approve</button>
                  <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#status1001">Reject</button>
                </td>
              </tr>
              {{-- Baris 2 --}}
              <tr>
                <td>1002</td>
                <td>02/05/2025</td>
                <td>Siti Aminah</td>
                <td>05/05 – 07/05</td>
                <td>Settlement</td>
                <td><span class="badge bg-warning text-dark">Revisi</span></td>
                <td class="text-center">
                  <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detail1002">Detail</button>
                  <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#status1002">Approve</button>
                  <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#status1002">Reject</button>
                </td>
              </tr>
              {{-- Baris 3 --}}
              <tr>
                <td>1003</td>
                <td>03/05/2025</td>
                <td>Joko Widodo</td>
                <td>10/05 – 12/05</td>
                <td>Perdin</td>
                <td><span class="badge bg-success">Diterima</span></td>
                <td class="text-center">
                  <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detail1003">Detail</button>
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

<!-- Modal Detail 1001 -->
<div class="modal fade" id="detail1001" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Pengajuan #1001</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        {{-- Detail static seperti sebelumnya --}}
        <dl class="row mb-3">
          <dt class="col-sm-3">Nama</dt><dd class="col-sm-9">Budi Santoso</dd>
          <dt class="col-sm-3">Area</dt><dd class="col-sm-9">Jakarta Selatan</dd>
          <dt class="col-sm-3">Periode</dt><dd class="col-sm-9">01/05 s/d 03/05</dd>
        </dl>
        <!-- rincian biaya & tujuan -->
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Status 1001 -->
<div class="modal fade" id="status1001" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title">Ubah Status #1001</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select">
              <option>Menunggu</option>
              <option selected>Diterima</option>
              <option>Ditolak</option>
              <option>Revisi</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Catatan</label>
            <textarea class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
document.getElementById('toggleCompletedBtn').addEventListener('click', function(){
  const rows = document.querySelectorAll('#pengajuanTable tbody tr');
  const hide = this.textContent.includes('Sembunyikan');
  rows.forEach(r => {
    const badge = r.querySelector('.badge.bg-success');
    if (badge) r.style.display = hide ? 'none' : '';
  });
  this.textContent = hide ? 'Tampilkan Selesai' : 'Sembunyikan Selesai';
});
</script>
@endsection