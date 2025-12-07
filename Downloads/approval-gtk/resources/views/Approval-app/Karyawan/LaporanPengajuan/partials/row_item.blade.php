@php
    // 1. Logika Cek Settlement
    $isSettlementRequest = $pengajuan->progressApprovals->contains(function ($value, $key) {
        return !is_null($value->settlement_id);
    });
    
    $settlementInfo = null;
    $needsRetransfer = false;
    $hasTransferProof = false;
    
    if ($isSettlementRequest) {
        $progressWithSettlement = $pengajuan->progressApprovals->whereNotNull('settlement_id')->first();
        if ($progressWithSettlement && $progressWithSettlement->settlement) {
            $settlementInfo = $progressWithSettlement->settlement;
            $selisih = $settlementInfo->selisih ?? 0;
            $hasTransferProof = !empty($settlementInfo->file_bukti_transfer) && !is_null($settlementInfo->tanggal_transfer);
            $needsRetransfer = $selisih > 0 && !$hasTransferProof;
        }
    }

    // 2. Logika Menentukan Status & Progress User Login
    // Cari progress yang statusnya 'pending' atau 'proses' terlebih dahulu (Prioritas Active Task)
    $myProgress = $pengajuan->progressApprovals->first(function ($progress) {
        return in_array($progress->status, ['pending', 'proses']);
    });

    // Jika tidak ada task aktif, baru ambil yang terakhir (biasanya status approved/rejected dari history)
    if (!$myProgress) {
        $myProgress = $pengajuan->progressApprovals->last();
    }

    $myStatus = $myProgress ? $myProgress->status : 'pending';
@endphp

<tr>
    <td>{{ $index + 1 }}</td>
    <td>
         @if($isSettlementRequest && $settlementInfo)
            <h4 class="badge bg-warning text-dark">
                <i class="fas fa-receipt me-1"></i>Settlement: {{ $settlementInfo->nomor_settlement }}
            </h4>
            @if($needsRetransfer)
                <br><small class="badge bg-danger mt-1"><i class="fas fa-exclamation-triangle me-1"></i>Belum Di Transfer Ulang</small>
            @elseif($hasTransferProof && $settlementInfo->selisih > 0)
                <br><small class="text-primary"><i class="fas fa-check-circle me-1"></i>Sisa Sudah Ditransfer Requester<br>Tgl: {{ \Carbon\Carbon::parse($settlementInfo->tanggal_transfer)->format('d/m/Y') }}</small>
            @endif
        @else
            <h5 class="text-primary">{{ $pengajuan->nomor_pengajuan }}</h5>
        @endif
    </td>
    <td>
        @if($isSettlementRequest)
            <h6 class="fw-bold text-primary"><strong>LBS - {{ $pengajuan->judul }}</strong></h6>
            @if($isSettlementRequest && $settlementInfo)
                <code class="text-primary">{{ $pengajuan->nomor_pengajuan }}</code>
            @endif
        @else
            <div class="fw-bold">{{ $pengajuan->judul }}</div>
        @endif
    </td>
    <td>
        <div class="fw-bold">{{ $pengajuan->requester->nama ?? '-' }}</div>
        <small class="text-muted">{{ $pengajuan->requester->department->nama ?? '-' }}</small>
    </td>
    <td>
        @if($isSettlementRequest && $settlementInfo)
            <div class="fw-bold text-primary"><strong>Actual: Rp. {{ number_format($settlementInfo->total_actual, 0, ',', '.') }}</strong></div>
            <small class="text-muted">
                Budget: Rp. {{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}
            </small>
        @else
            <div class="fw-bold">{{ $pengajuan->mata_uang }} {{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}</div>
        @endif
    </td>
    <td>
        {{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}
    </td>
    <td>
        @if($type === 'history')
            {{-- Tampilan Status untuk Tab Riwayat --}}
            <span class="badge bg-{{ $myStatus == 'approved' ? 'success' : 'danger' }}">
                {{ ucfirst($myStatus) }}
            </span>
            @if($isSettlementRequest && $settlementInfo)
                 <br><small class="text-muted">Setl: {{ ucfirst($settlementInfo->status_settlement) }}</small>
            @endif
        @else
            {{-- Tampilan Status untuk Tab Active --}}
            @if($isSettlementRequest && $settlementInfo)
                <small class="badge bg-warning text-dark mt-1">{{ ucfirst($settlementInfo->status_settlement) }} </small>
            @else
                <span class="badge bg-info text-dark">
                    {{ ucfirst($pengajuan->status_pengajuan) }} 
                </span>
            @endif
        @endif
    </td>
    <td>
        @if($isSettlementRequest)
            <button type="button" class="btn btn-primary btn-sm" onclick="showSettlementDetailInModal({{ $pengajuan->id }})">
                <i class="fas fa-receipt me-1"></i> {{ $type === 'history' ? 'Lihat Detail' : 'Review Settlement' }}
            </button>
            @if(Auth::user()->department->nama == 'Finance' && $needsRetransfer && $type !== 'history')
                <button type="button" class="btn btn-info btn-sm mt-1" data-bs-toggle="modal" data-bs-target="#notifikasiModal" 
                    data-settlement-id="{{ $settlementInfo->id }}"
                    data-pengajuan-nomor="{{ $pengajuan->nomor_pengajuan }}"
                    data-requester-nama="{{ $pengajuan->requester->nama }}"
                    data-requester-email="{{ $pengajuan->requester->email }}"
                    data-selisih="{{ number_format($settlementInfo->selisih, 0, ',', '.') }}">
                <i class="fas fa-bell me-1"></i> Notifikasi
                </button>
            @endif
        @else
            <button type="button" class="btn btn-primary btn-sm" onclick="showDetailModal({{ $pengajuan->id }})">
                <i class="fas fa-eye me-1"></i>{{ $type === 'history' ? 'Lihat Detail' : 'Detail & Approval' }}
            </button>
        @endif
        <a href="{{ route('pengajuan.print-pdf', $pengajuan->id) }}" class="btn btn-secondary btn-sm" target="_blank" title="Cetak Dokumen">
            <i class="fas fa-print"></i>
        </a>
    </td>
</tr>