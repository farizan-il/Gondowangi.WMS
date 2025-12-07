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
                    <h5 class="m-b-10">Penanggungjawaban</h5>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row g-4 justify-content-center">
  <div class="card shadow-sm mb-4">
  <div class="card-header text-white">
    <h5 class="mb-0">Laporan Biaya Settlement (L B S)</h5>
  </div>

  <div class="card-body">
    <form id="settlementForm" action="" method="POST">
      @csrf

      {{-- HEADER IDENTITAS --}}
      <div class="row mb-4">
        <div class="col-md-3">
          <label class="form-label">Nama</label>
          <input type="text" name="nama" class="form-control form-control-sm" value="{{ old('nama') }}" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Area</label>
          <input type="text" name="area" class="form-control form-control-sm" value="{{ old('area') }}" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Nomor</label>
          <input type="text" name="nomor" class="form-control form-control-sm" value="{{ old('nomor') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Periode</label>
          <input type="month" name="periode" class="form-control form-control-sm" value="{{ old('periode') }}" required>
        </div>
      </div>

      {{-- TABEL FORM --}}
      {{-- TABEL FORM --}}
      <div class="table-responsive mb-4">
        <table class="table table-bordered table-sm align-middle text-center" id="table-settlement">
          <thead class="table-light">
            <tr>
              <th style="width:5%;">No.</th>
              <th class="text-start">Uraian</th>
              <th style="width:12%;">Advance (Rp)</th>
              <th style="width:12%;">Settlement (Rp)</th>
              <th style="width:12%;">Variance (Rp)</th>
              <th style="width:20%;">Bukti</th>
            </tr>
          </thead>
          <tbody>
            @php
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
              // contoh data advance yang sesungguhnya dari Controller
              $actualAdvance = [100000,75000,25000,500000,150000,80000,30000,120000,50000,10000];
            @endphp

            @foreach($biayaItems as $idx => $label)
            <tr data-idx="{{ $idx }}">
              <td>{{ $idx+1 }}</td>
              <td class="text-start">{{ $label }}</td>
              <td>
                <input type="number"
                       name="advance[{{ $idx }}]"
                       class="form-control form-control-sm advance-input"
                       value="{{ $actualAdvance[$idx] ?? 0 }}"
                       readonly>
              </td>
              <td>
                <input type="number"
                       name="settlement[{{ $idx }}]"
                       class="form-control form-control-sm settlement-input"
                       value="{{ old("settlement.$idx", '') }}">
              </td>
              <td>
                <input type="number"
                       name="variance[{{ $idx }}]"
                       class="form-control form-control-sm variance-input"
                       readonly>
              </td>
              <td>
                <input type="file"
                       name="bukti[{{ $idx }}]"
                       class="form-control form-control-sm"
                       accept=".jpg,.png,.pdf">
              </td>
            </tr>
            @endforeach
          </tbody>
          <tfoot class="table-light">
            {{-- <tr>
              <td colspan="2" class="text-end" ><strong>GRAND TOTAL Advance :</strong></td>
              <td><input type="number" id="totalAdvance" class="form-control form-control-sm" readonly></td>
              <td colspan="3"></td>
            </tr> --}}
            <tr>
              <td colspan="3" class="text-end"><strong>GRAND TOTAL Settlement :</strong></td>
              <td><input type="number" id="totalSettlement" class="form-control form-control-sm" readonly></td>
              <td colspan="2"></td>
            </tr>
            <tr>
              <td colspan="4" class="text-end"><strong>GRAND TOTAL Variance :</strong></td>
              <td><input type="number" id="totalVariance" class="form-control form-control-sm" readonly></td>
              <td></td>
            </tr>
            <tr>
              <td colspan="4" class="text-end">UANG MUKA EX PPB NO :</td>
              <td colspan="2">
                <input type="number"
                  id="totalAdvance"
                  name="uang_muka"
                  class="form-control form-control-sm"
                  value="{{ old('uang_muka') }}" readonly>
              </td>
            </tr>
            <tr>
              <td colspan="4" class="text-end">BALANCE YANG AKAN DITRANSFER/DIKEMBALIKAN :</td>
              <td colspan="2">
                <input type="number" id="balance" class="form-control form-control-sm" readonly>
              </td>
            </tr>

            <!-- Baris Status Settlement -->
            <tr id="status-row">
              <td colspan="4" class="text-end"><strong>Status Settlement</strong></td>
              <td colspan="2">
                <span id="status-label" class="badge bg-secondary">–</span>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="mb-3 text-end">
        <button type="button" id="checkStatus" class="btn btn-sm btn-outline-info">
          Cek Status Settlement
        </button>
      </div>

      <div class="text-end">
        <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
      </div>
    </form>
  </div>
</div>
</div>
<!-- [ Main Content ] end -->

@endsection

@section('script')
<script>
  (function(){
    const table = document.getElementById('table-settlement');
    const tbody = table.querySelector('tbody');
    const tfAdvance = document.getElementById('totalAdvance');
    const tfSettlement = document.getElementById('totalSettlement');
    const tfVariance = document.getElementById('totalVariance');
    const tfBalance = document.getElementById('balance');
    const inputUangMuka = document.querySelector('input[name="uang_muka"]');

    function recalcRow(row){
      const adv = parseFloat(row.querySelector('.advance-input').value) || 0;
      const set = parseFloat(row.querySelector('.settlement-input').value) || 0;
      const varc = set - adv;
      row.querySelector('.variance-input').value = varc;
      return { adv, set, varc };
    }

    function recalcAll(){
      let sumAdv = 0, sumSet = 0, sumVar = 0;
      tbody.querySelectorAll('tr').forEach(r=>{
        const { adv, set, varc } = recalcRow(r);
        sumAdv += adv; sumSet += set; sumVar += varc;
      });
      tfAdvance.value   = sumAdv;
      tfSettlement.value= sumSet;
      tfVariance.value  = sumVar;
      const muka = parseFloat(inputUangMuka.value) || 0;
      tfBalance.value   = sumSet - muka;
    }

    // Delegasi event input pada settlement-input saja
    tbody.addEventListener('input', function(e){
      if(e.target.matches('.settlement-input')){
        recalcAll();
      }
    });

    inputUangMuka.addEventListener('input', recalcAll);

    const statusLabel = document.getElementById('status-label');
    const btnCheck   = document.getElementById('checkStatus');

    btnCheck.addEventListener('click', () => {
      const balance = parseFloat(document.getElementById('balance').value) || 0;
      const fmt = new Intl.NumberFormat('id-ID');
      if (balance > 0) {
        statusLabel.textContent = `Perusahaan bayar ke Anda: Rp ${fmt.format(balance)}`;
        statusLabel.className   = 'badge bg-success';
      } else if (balance < 0) {
        const amt = Math.abs(balance);
        statusLabel.textContent = `Anda kembalikan ke perusahaan: Rp ${fmt.format(amt)}`;
        statusLabel.className   = 'badge bg-danger';
      } else {
        statusLabel.textContent = 'Seimbang (Tidak Ada)';
        statusLabel.className   = 'badge bg-primary';
      }
    });

    // pastikan recalcAll() sudah dipanggil saat load
    document.addEventListener('DOMContentLoaded', recalcAll);
  })();


</script>
@endsection