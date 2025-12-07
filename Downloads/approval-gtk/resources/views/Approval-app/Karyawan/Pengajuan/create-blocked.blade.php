@extends('Approval-app.Layout.approver-main')


@section('title', 'Buat Pengajuan - Tidak Dapat Melanjutkan')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Kelola Pengajuan Anda</h5>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="icon-bg me-3 text-warning" style="background: rgba(255,193,7,0.1);">
                            <i class='bx bxs-error-alt bx-lg'></i>

                        </div>
                        <div>
                            <h4 class="mb-1">Tidak Dapat Membuat Pengajuan Baru</h4>
                            <p class="text-muted mb-0">Anda telah mencapai batas maksimal pengajuan yang sedang dalam proses</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-danger">
                <div class="d-flex align-items-start">
                    <i class="fas fa-ban fa-2x me-3 mt-1"></i>
                    <div>
                        <h5 class="alert-heading mb-2">Batas Maksimal Tercapai</h5>
                        <p class="mb-2">
                            Anda saat ini memiliki <strong>{{ $pengajuanBelumSelesai }} pengajuan</strong> yang masih dalam proses. 
                            Maksimal pengajuan yang dapat diproses bersamaan adalah <strong>3 pengajuan</strong>.
                        </p>
                        <p class="mb-0">
                            <small>Silakan tunggu hingga salah satu pengajuan Anda selesai diproses (status menjadi "Settlement Approved" atau "Completed") sebelum membuat pengajuan baru.</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pengajuan yang Sedang Berjalan -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Pengajuan yang Sedang Dalam Proses ({{ $pengajuanBelumSelesai }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($pengajuanPending->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. Pengajuan</th>
                                        <th>Kategori</th>
                                        <th>Judul</th>
                                        <th>Nominal</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Status</th>
                                        <th>Status Pembayaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pengajuanPending as $pengajuan)
                                    <tr>
                                        <td>
                                            <strong>{{ $pengajuan->nomor_pengajuan }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-dark">{{ $pengajuan->kategoriPengajuan->nama }}</span>
                                        </td>
                                        <td>{{ $pengajuan->judul }}</td>
                                        <td>
                                            {{ $pengajuan->mata_uang }} {{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}
                                        </td>
                                        <td>{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</td>
                                        <td>
                                            @switch($pengajuan->status_pengajuan)
                                                @case('proses')
                                                    <span class="badge bg-warning text-dark">Proses</span>
                                                    @break
                                                @case('settlement_created')
                                                    <span class="badge bg-primary">Settlement Created</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ ucfirst($pengajuan->status_pengajuan) }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($pengajuan->statuspembayaran)
                                                @case('Menunggu')
                                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                                    @break
                                                @case('Ditolak')
                                                    <span class="badge bg-danger">Ditolak</span>
                                                    @break
                                                @case('Dibayarkan')
                                                    <span class="badge bg-success">Dibayarkan</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $pengajuan->status_pembayaran ?? '-' }}</span>
                                            @endswitch
                                        </td>
                                       
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada pengajuan yang sedang dalam proses</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <button onclick="window.history.back()" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Kembali
                </button>
                
                <button onclick="window.location.reload()" class="btn btn-outline-primary">
                    <i class="fas fa-sync-alt me-2"></i>
                    Refresh Status
                </button>
            </div>
        </div>
    </div>



<script>
// Auto refresh setiap 30 detik untuk cek status pengajuan
setInterval(function() {
    // Optional: Bisa ditambahkan AJAX call untuk cek status tanpa reload page
    console.log('Checking pengajuan status...');
}, 30000);

// Fungsi untuk cek status via AJAX (opsional)
function checkPengajuanStatus() {
    fetch('{{ route("pengajuan.check-status") }}')
        .then(response => response.json())
        .then(data => {
            if (data.can_create) {
                // Redirect ke halaman create jika sudah bisa buat pengajuan baru
                window.location.href = '{{ route("pengajuan.index") }}';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}
</script>
@endsection