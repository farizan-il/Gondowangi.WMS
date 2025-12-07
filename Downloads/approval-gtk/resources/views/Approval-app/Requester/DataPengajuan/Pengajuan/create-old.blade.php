@extends('Approval-app.Layout.main')

@section('head')
<style>
    /* Card Modern Styles */
    .card-modern {
      border: none;
      border-radius: .75rem;
      overflow: hidden;
      position: relative;
      transform: translateY(20px);
      opacity: 0;
      transition: all 0.6s ease-out;
      box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
    }
    .card-modern.show {
      transform: translateY(0);
      opacity: 1;
    }
    .card-modern .card-body {
      padding: 2rem 1.5rem;
    }
    .card-modern .icon-bg {
      width: 4rem;
      height: 4rem;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      margin: 0 auto 1rem;
    }
    .card-modern .card-title {
      font-size: 1.25rem;
      margin-bottom: .5rem;
    }
    .card-modern .card-text {
      font-size: .9rem;
      margin-bottom: 1rem;
      color: #6c757d;
    }
    .card-modern:hover {
      transform: translateY(-5px) scale(1.02);
      box-shadow: 0 1rem 1.5rem rgba(0,0,0,0.2);
    }
    /* Icon backgrounds */
    .card-reim .icon-bg { background: rgba(40,167,69,0.1); color: #28a745; }
    .card-pur .icon-bg { background: rgba(0,123,255,0.1); color: #007bff; }
    .card-settle .icon-bg { background: rgba(255,193,7,0.1); color: #ffc107; }
    .card-perdin .icon-bg { background: rgba(220,53,69,0.1); color: #dc3545; }
    .card-asset .icon-bg { background: rgba(102,16,242,0.1); color: #6610f2; }
    .card-leave .icon-bg { background: rgba(23,162,184,0.1); color: #17a2b8; }
    .card-training .icon-bg { background: rgba(255,193,7,0.1); color: #ffc107; }
    .card-meeting .icon-bg { background: rgba(108,117,125,0.1); color: #6c757d; }
  </style>
@endsection

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Daftar Pengajuan</h5>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row g-4 justify-content-center">
    <!-- Card Perdin -->
    <div class="col-sm-6 col-lg-3">
      <div class="card card-modern card-perdin" data-onscroll>
        <div class="card-body text-center">
          <div class="icon-bg mb-3">
            <i data-feather="map-pin"></i>
          </div>
          <h5 class="card-title">Perjalanan Dinas</h5>
          <p class="card-text">Ajukan perjalanan dinas & klaim biaya.</p>
          <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#pengajuanModal">Buat Pengajuan</button>
        </div>
      </div>
    </div>

    <!-- Card Settlement -->
    <!-- <div class="col-sm-6 col-lg-3">
      <div class="card card-modern card-settle" data-onscroll>
        <div class="card-body text-center">
          <div class="icon-bg mb-3">
            <i data-feather="file-text"></i>
          </div>
          <h5 class="card-title">Settlement</h5>
          <p class="card-text">Proses penyelesaian & posting transaksi.</p>
          <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#settlementModal">Buat Pengajuan</button>
        </div>
      </div>
    </div> -->

    <!-- Card: Permohonan Pengeluaran Biaya -->
    <div class="col-sm-6 col-lg-3">
      <div class="card card-modern card-expense" data-onscroll>
        <div class="card-body text-center">
          <div class="icon-bg mb-3">
            <i data-feather="dollar-sign"></i>
          </div>
          <h5 class="card-title">Permohonan Pengeluaran Biaya</h5>
          <p class="card-text">Ajukan permintaan pengeluaran biaya operasional.</p>
          <button class="btn btn-outline-success btn-sm"
                  data-bs-toggle="modal"
                  data-bs-target="#modalPengeluaranBiaya">
            Buat Pengajuan
          </button>
        </div>
      </div>
    </div>

    <!-- Card: Proposal Sales & Promotion -->
    <div class="col-sm-6 col-lg-3">
      <div class="card card-modern card-proposal" data-onscroll>
        <div class="card-body text-center">
          <div class="icon-bg mb-3">
            <i data-feather="file-text"></i>
          </div>
          <h5 class="card-title">Proposal Sales & Promotion</h5>
          <p class="card-text">Ajukan proposal untuk kegiatan sales & promotion.</p>
          <button class="btn btn-outline-primary btn-sm"
                  data-bs-toggle="modal"
                  data-bs-target="#modalProposalSP">
            Buat Pengajuan
          </button>
        </div>
      </div>
    </div>

    <!-- Card: Laporan Biaya Perjalanan Dinas -->
    <div class="col-sm-6 col-lg-3">
      <div class="card card-modern card-travel" data-onscroll>
        <div class="card-body text-center">
          <div class="icon-bg mb-3">
            <i data-feather="map-pin"></i>
          </div>
          <h5 class="card-title">Laporan Biaya Perjalanan Dinas</h5>
          <p class="card-text">Ajukan laporan & settlement perjalanan dinas.</p>
          <button class="btn btn-outline-secondary btn-sm"
                  data-bs-toggle="modal"
                  data-bs-target="#modalLaporanPerjalanan">
            Buat Pengajuan
          </button>
        </div>
      </div>
    </div>

    <!-- Card: Permohonan Biaya Service -->
    <div class="col-sm-6 col-lg-3">
      <div class="card card-modern card-service-request" data-onscroll>
        <div class="card-body text-center">
          <div class="icon-bg mb-3">
            <i data-feather="tool"></i>
          </div>
          <h5 class="card-title">Permohonan Biaya Service</h5>
          <p class="card-text">Ajukan penggantian biaya untuk service.</p>
          <button class="btn btn-outline-warning btn-sm"
                  data-bs-toggle="modal"
                  data-bs-target="#modalServiceRequest">
            Buat Pengajuan
          </button>
        </div>
      </div>
    </div>

    <!-- Card: Rekap Biaya Service -->
    <div class="col-sm-6 col-lg-3">
      <div class="card card-modern card-service-report" data-onscroll>
        <div class="card-body text-center">
          <div class="icon-bg mb-3">
            <i data-feather="archive"></i>
          </div>
          <h5 class="card-title">Rekap Biaya Service</h5>
          <p class="card-text">Lihat dan unduh rekap biaya service.</p>
          <button class="btn btn-outline-danger btn-sm"
                  data-bs-toggle="modal"
                  data-bs-target="#modalRekapService">
            Buat Pengajuan
          </button>
        </div>
      </div>
    </div>
  </div>
<!-- [ Main Content ] end -->

@php
  $journeys = 3;
  $biayaItems = [
    'Transportasi Udara',
    'Transportasi Darat',
    'Transportasi Airport Tax',
    'Hotel, Jumlah hari',
    'Makan',
    'Uang Saku',
    'Telephone & Fax',
    'Entertainment',
    'Dokumentasi',
    'Lain‐lain',
  ];
@endphp

<!-- Modal -->
<div class="modal fade" id="pengajuanModal" tabindex="-1" aria-labelledby="pengajuanModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Form Pengajuan Lanjutan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        {{-- HEADER DOKUMEN --}}
        <div class="text-center mb-4">
          <h5 class="mb-0">PT. GONDOWANGI TRADISIONAL KOSMETIKA</h5>
          <h6 class="mb-0">PENGAJUAN BIAYA PERJALANAN DINAS</h6>
        </div>
        <div class="row mb-4">
          <div class="col-md-4">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control form-control-sm" value="{{ old('nama', auth()->user()->name ?? '') }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">Area</label>
            <input type="text" name="area" class="form-control form-control-sm" value="{{ old('area') }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">Periode</label>
            <div class="input-group input-group-sm">
              <input type="date" name="periode_awal" class="form-control" value="{{ old('periode_awal') }}">
              <span class="input-group-text">s/d</span>
              <input type="date" name="periode_akhir" class="form-control" value="{{ old('periode_akhir') }}">
            </div>
          </div>
        </div>

        {{-- A. BIAYA YANG DIPERLUKAN --}}
        <h6 class="mb-3">A. Biaya Yang Diperlukan</h6>

        <div class="table-responsive">
          <table class="table table-bordered table-sm text-center align-middle" id="table-biaya">
            <thead class="table-secondary">
      <tr>
        <th>No</th>
        <th class="text-start">Uraian</th>
        @for($j=1; $j<=$journeys; $j++)
          <th>
            <div class="form-group mb-1">
              <label class="form-label d-block">Perjalanan {{$j}}</label>
              <div class="input-group input-group-sm">
                <input type="date" name="tgl_awal[{{$j}}]" class="form-control" value="{{ old('tgl_awal.'.$j) }}">
                <span class="input-group-text">s/d</span>
                <input type="date" name="tgl_akhir[{{$j}}]" class="form-control" value="{{ old('tgl_akhir.'.$j) }}">
              </div>
            </div>
          </th>
        @endfor
        <th>Total</th>
      </tr>
    </thead>
            <tbody>
              @foreach($biayaItems as $idx => $label)
              <tr data-default="1">
                <td class="row-number">{{ $idx+1 }}</td>
                <td class="text-start">{{ $label }}</td>
                @for($j=1; $j<=$journeys; $j++)
                  <td>
                    <input type="number"
                           name="biaya[{{$j}}][{{$idx}}]"
                           class="form-control form-control-sm biaya-input"
                           data-journey="{{$j}}"
                           data-row="{{ $idx }}"
                           value="0" min="0" step="1">
                  </td>
                @endfor
                <td>
                  <input type="number"
                         readonly
                         class="form-control form-control-sm row-total text-end"
                         data-row="{{ $idx }}"
                         value="0">
                </td>
              </tr>
              @endforeach

              <tr class="table-light fw-bold" id="footer-total">
                <td><button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-row">
          + Tambah Baris Biaya
        </button></td>
                <td colspan="{{ 2 + $journeys }}" class="text-end">Grand Total:</td>
                <td>
                  <input type="number" readonly id="grand-total"
                         class="form-control form-control-sm text-end" value="0">
                </td>
              </tr>

            </tbody>
          </table>

        </div>


        {{-- B. TUJUAN PERJALANAN --}}
    <h6 class="mt-4">B. Tujuan Perjalanan</h6>
    @for($j=1; $j<=$journeys; $j++)
      <div class="mb-5" data-journey-table="{{ $j }}">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <strong>Perjalanan {{ $j }}</strong>
          <button type="button"
                  class="btn btn-sm btn-outline-primary add-tujuan"
                  data-journey="{{ $j }}">
            + Tambah Baris Perjalanan {{ $j }}
          </button>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-sm text-center align-middle"
                 id="table-tujuan-{{ $j }}">
            <thead class="table-secondary">
              <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Daerah</th>
                <th>Sales Rata-Rata/Bln</th>
                <th>Estimasi Sales</th>
                <th>Jumlah Outlet</th>
              </tr>
            </thead>
            <tbody>
              {{-- default 6 baris --}}
              @for($i=0; $i<1; $i++)
              <tr data-default="1">
                <td class="tp-no">{{ $i+1 }}</td>
                <td>
                  <input type="date"
                         name="tp[{{ $j }}][tanggal][{{ $i }}]"
                         class="form-control form-control-sm tujuan-input"
                         data-journey="{{ $j }}"
                         data-col="tanggal"
                         data-row="{{ $i }}">
                </td>
                <td>
                  <input type="text"
                         name="tp[{{ $j }}][daerah][{{ $i }}]"
                         class="form-control form-control-sm tujuan-input"
                         data-journey="{{ $j }}"
                         data-col="daerah"
                         data-row="{{ $i }}">
                </td>
                <td>
                  <input type="number" min="0"
                         name="tp[{{ $j }}][srp][{{ $i }}]"
                         class="form-control form-control-sm tujuan-input"
                         data-journey="{{ $j }}"
                         data-col="srp"
                         data-row="{{ $i }}"
                         value="0">
                </td>
                <td>
                  <input type="number" min="0"
                         name="tp[{{ $j }}][est][{{ $i }}]"
                         class="form-control form-control-sm tujuan-input"
                         data-journey="{{ $j }}"
                         data-col="est"
                         data-row="{{ $i }}"
                         value="0">
                </td>
                <td>
                  <input type="number" min="0"
                         name="tp[{{ $j }}][out][{{ $i }}]"
                         class="form-control form-control-sm tujuan-input"
                         data-journey="{{ $j }}"
                         data-col="out"
                         data-row="{{ $i }}"
                         value="0">
                </td>
              </tr>
              @endfor

              {{-- baris total --}}
              <tr class="table-light fw-bold" id="tp-footer-{{ $j }}">
                <td colspan="3" class="text-end">Total:</td>
                <td>
                  <input type="number" readonly
                         class="form-control form-control-sm text-end tp-total"
                         id="tp{{ $j }}-srp-total" value="0">
                </td>
                <td>
                  <input type="number" readonly
                         class="form-control form-control-sm text-end tp-total"
                         id="tp{{ $j }}-est-total" value="0">
                </td>
                <td>
                  <input type="number" readonly
                         class="form-control form-control-sm text-end tp-total"
                         id="tp{{ $j }}-out-total" value="0">
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    @endfor
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <a href="/Pengajuan">
          <button class="btn btn-primary">Kirim Pengajuan</button>
        </a>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    feather.replace();
    // Scroll-in animation for cards
    const cards = document.querySelectorAll('[data-onscroll]');
    const obs = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('show');
          obs.unobserve(e.target);
        }
      });
    }, { threshold: 0.2 });
    cards.forEach(c => obs.observe(c));
    
    // Auto-calc total cost
    const unit = document.getElementById('unitPrice');
    const qty = document.getElementById('quantity');
    const total = document.getElementById('totalCost');
    function calculate(){ total.value = (Number(unit.value) * Number(qty.value)).toLocaleString(); }
    unit.addEventListener('input', calculate);
    qty.addEventListener('input', calculate);
  });
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const table    = document.getElementById('table-biaya');
  const tbody    = table.querySelector('tbody');
  const addBtn   = document.getElementById('add-row');
  const footer   = document.getElementById('footer-total');
  let rowCount   = {{ count($biayaItems) }};

  // Re-hitung totals
  function updateBiaya() {
    const sums = {};
    table.querySelectorAll('.biaya-input').forEach(inp => {
      const r = inp.dataset.row;
      sums[r] = (sums[r]||0) + (Number(inp.value)||0);
    });
    // per-baris
    table.querySelectorAll('.row-total').forEach(rt => {
      rt.value = sums[rt.dataset.row]||0;
    });
    // grand
    const grand = Object.values(sums).reduce((a,b)=>a+b,0);
    document.getElementById('grand-total').value = grand;
  }

  // Delegasi untuk input biaya
  table.addEventListener('input', e => {
    if (e.target.classList.contains('biaya-input')) updateBiaya();
  });

  // Delegasi untuk remove-row
  table.addEventListener('click', e => {
    if (!e.target.classList.contains('remove-row')) return;
    const tr = e.target.closest('tr');
    tr.remove();
    reindexRows();
    updateBiaya();
  });

  // Tambah baris baru
  addBtn.addEventListener('click', () => {
    const idx = rowCount++;
    const tr  = document.createElement('tr');

    // Kolom nomor
    tr.innerHTML = `
      <td class="row-number"></td>
      <td class="text-start">
        <input type="text" name="uraian_baru[${idx}]"
               class="form-control form-control-sm" placeholder="Uraian">
        <button type="button" class="btn-close remove-row"
                title="Hapus baris"></button>
      </td>
      ${[...Array({{ $journeys }})].map((_,j)=>
        `<td>
           <input type="number" name="biaya[${j+1}][${idx}]"
                  class="form-control form-control-sm biaya-input"
                  data-journey="${j+1}" data-row="${idx}"
                  value="0" min="0" step="1">
         </td>`
      ).join('')}
      <td>
        <input type="number" readonly
               class="form-control form-control-sm row-total text-end"
               data-row="${idx}" value="0">
      </td>`;
    
    tbody.insertBefore(tr, footer);
    reindexRows();
  });

  function reindexRows() {
    Array.from(tbody.querySelectorAll('tr'))
      .filter(r => r !== footer)
      .forEach((tr,i) => {
        // no
        tr.querySelector('.row-number').textContent = i+1;
        // semua biaya-input
        tr.querySelectorAll('.biaya-input').forEach(inp => {
          const journey = inp.dataset.journey;
          inp.dataset.row = i;
          inp.name = `biaya[${journey}][${i}]`;
        });
        // row-total
        const rt = tr.querySelector('.row-total');
        rt.dataset.row = i;
        // uraian baru jika ada
        const ur = tr.querySelector('input[name^="uraian_baru"]');
        if (ur) ur.name = `uraian_baru[${i}]`;
      });
  }
});
</script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
  const journeys = {{ $journeys }};
  // track jumlah baris per journey
  const tpCounts = {};
  for (let j = 1; j <= journeys; j++) tpCounts[j] = 6;

  // fungsi update total tiap journey
  function updateTujuan(j) {
    const table = document.getElementById(`table-tujuan-${j}`);
    const sums = { srp: 0, est: 0, out: 0 };
    table.querySelectorAll('.tujuan-input').forEach(inp => {
      if (Number(inp.dataset.row) >= 0 &&
          ['srp','est','out'].includes(inp.dataset.col)) {
        sums[inp.dataset.col] += Number(inp.value) || 0;
      }
    });
    document.getElementById(`tp${j}-srp-total`).value = sums.srp;
    document.getElementById(`tp${j}-est-total`).value = sums.est;
    document.getElementById(`tp${j}-out-total`).value = sums.out;
  }

  // delegasi input untuk semua tujuan
  document.body.addEventListener('input', e => {
    if (e.target.classList.contains('tujuan-input')) {
      updateTujuan(e.target.dataset.journey);
    }
  });

  // delegasi click untuk tombol add-tujuan
  document.body.addEventListener('click', e => {
    // Tambah baris
    if (e.target.classList.contains('add-tujuan')) {
      const j = e.target.dataset.journey;
      const table = document.getElementById(`table-tujuan-${j}`);
      const tbody = table.querySelector('tbody');
      const footer = document.getElementById(`tp-footer-${j}`);
      const idx = tpCounts[j]++;
      // buat tr baru
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="tp-no"></td>
        <td>
          <input type="date"
                 name="tp[${j}][tanggal][${idx}]"
                 class="form-control form-control-sm tujuan-input"
                 data-journey="${j}"
                 data-col="tanggal"
                 data-row="${idx}">
        </td>
        <td>
          <input type="text"
                 name="tp[${j}][daerah][${idx}]"
                 class="form-control form-control-sm tujuan-input"
                 data-journey="${j}"
                 data-col="daerah"
                 data-row="${idx}"
                 placeholder="Daerah">
        </td>
        <td>
          <input type="number" min="0"
                 name="tp[${j}][srp][${idx}]"
                 class="form-control form-control-sm tujuan-input"
                 data-journey="${j}"
                 data-col="srp"
                 data-row="${idx}"
                 value="0">
        </td>
        <td>
          <input type="number" min="0"
                 name="tp[${j}][est][${idx}]"
                 class="form-control form-control-sm tujuan-input"
                 data-journey="${j}"
                 data-col="est"
                 data-row="${idx}"
                 value="0">
        </td>
        <td>
          <div class="d-flex justify-content-between align-items-center">
            <input type="number" min="0"
                   name="tp[${j}][out][${idx}]"
                   class="form-control form-control-sm tujuan-input"
                   data-journey="${j}"
                   data-col="out"
                   data-row="${idx}"
                   value="0">
            <button type="button"
                    class="btn-close remove-tujuan ms-2"
                    aria-label="Hapus"></button>
          </div>
        </td>`;
      tbody.insertBefore(tr, footer);
      reindexTujuan(j);
    }

    // Hapus baris baru
    if (e.target.classList.contains('remove-tujuan')) {
      const tr = e.target.closest('tr');
      const j = tr.querySelector('.tujuan-input').dataset.journey;
      tr.remove();
      tpCounts[j]--;
      reindexTujuan(j);
      updateTujuan(j);
    }
  });

  // re-nomor dan update attributes setiap journey
  function reindexTujuan(j) {
    const table = document.getElementById(`table-tujuan-${j}`);
    Array.from(table.querySelectorAll('tbody tr'))
      .filter(r => r.id !== `tp-footer-${j}`)
      .forEach((tr, i) => {
        tr.querySelector('.tp-no').textContent = i + 1;
        tr.querySelectorAll('.tujuan-input').forEach(inp => {
          const col = inp.dataset.col;
          inp.dataset.row = i;
          inp.name = `tp[${j}][${col}][${i}]`;
        });
      });
  }

  // inisialisasi totals awal
  for (let j = 1; j <= journeys; j++) updateTujuan(j);
});

</script>
@endsection