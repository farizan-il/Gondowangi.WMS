@extends('Approval-app.Layout.approver-main')

@section('head')
    <!-- Tambahkan CSRF token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .notes-cell {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .notes-cell:hover {
            overflow: visible;
            white-space: normal;
            word-wrap: break-word;
        }
        
        @media (max-width: 768px) {
            .notes-cell {
                max-width: 100px;
            }
        }
        .swal2-container {
            z-index: 99999 !important;
        }
        
        #processPaymentModal.modal.show {
            background-color: rgba(0, 0, 0, 0.7) !important;
        }
        
        #processPaymentModal .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.8) !important;
        }
        
        /* Tambahan efek blur untuk elemen di belakang */
        body.modal-open {
            overflow: hidden;
        }
        
        #processPaymentModal.show ~ * {
            filter: blur(2px);
            transition: filter 0.3s ease;
        }
        
        /* Style untuk box detail di modal pembayaran */
        #pengajuanDetail {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
        }
        
        #pengajuanDetail table tr td {
            padding: 4px 0;
            vertical-align: top;
        }
        
        .border-top-danger { border-top: 4px solid #dc3545 !important; }
        .border-top-secondary { border-top: 4px solid #6c757d !important; }
        .table-responsive { max-height: 500px; overflow-y: auto; }
        
        .border-top-primary { border-top: 4px solid #0d6efd !important; }
        .border-top-info { border-top: 4px solid #0dcaf0 !important; }

    </style>
@endsection

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Mengelola Transaction Request</h5>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <!-- Transaction Request Groups Table -->
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header">
                <h5>Daftar Transaction Request</h5>
                <div class="card-header-right">
                    <div class="btn-group card-option">
                        <button type="button" class="btn btn-primary rounded mr-3" id="createTRBtn">
                            <i class="feather icon-plus"></i> Create TR
                        </button>
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
                                <th>No. TR</th>
                                <th>Tanggal Dibuat</th>
                                <th>Kategori TR</th>
                                <th>Jumlah Pengajuan</th>
                                <th>Total Nominal</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Dibuat Oleh</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trGroups as $trGroup)
                            <tr>
                                <td>
                                    <strong>{{ $trGroup->tr_number }}</strong>
                                </td>
                                <td>{{ $trGroup->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($trGroup->notes)
                                        <div class="notes-cell" title="{{ $trGroup->notes }}">
                                            {{ $trGroup->notes }}
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <?php $breakdown = $trGroup->getItemsBreakdown(); ?>
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge badge-light-info">
                                            {{ $breakdown['total'] }} Total Items
                                        </span>
                                        <small class="text-muted">
                                            {{ $breakdown['pengajuan'] }} Pengajuan, {{ $breakdown['settlement'] }} LBS
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <strong>Rp {{ number_format($trGroup->total_nominal, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    @if($trGroup->status == 'pending')
                                        <span class="badge badge-light-warning"><strong>Menunggu</strong></span>
                                    @elseif($trGroup->status == 'processing')
                                        <span class="badge badge-light-info">Processing</span>
                                    @else
                                        <span class="badge badge-light-primary"><strong>Selesai</strong></span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $paidCount = $trGroup->getPaidCount();
                                        $totalCount = $trGroup->getTotalCount();
                                        $percentage = $totalCount > 0 ? ($paidCount / $totalCount) * 100 : 0;
                                    @endphp
                                    <div>
                                    <div class="progress" style="width: 100px; height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $percentage }}%">
                                        </div>
                                        
                                    </div>
                                    <small class="text-muted">{{ $paidCount }}/{{ $totalCount }}</small>
                                    </div>
                                </td>
                                <td>{{ $trGroup->createdBy->nama }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-info view-tr-btn" 
                                                data-id="{{ $trGroup->id }}" title="Lihat Detail & Proses Pembayaran">
                                            <i class="feather icon-eye"></i>
                                        </button>
                                        
                                        @if($trGroup->status == 'pending')
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-tr-btn" 
                                                data-id="{{ $trGroup->id }}" title="Hapus TR">
                                            <i class="feather icon-trash-2"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="feather icon-inbox" style="font-size: 48px;"></i>
                                        <h6 class="mt-2">Belum ada Transaction Request</h6>
                                        <p>Klik "Create TR" untuk membuat Transaction Request baru</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>                        
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

<!-- Modal Create TR -->
<div class="modal fade" id="createTRModal" tabindex="-1" aria-labelledby="createTRModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTRModalLabel">Buat Transaction Request Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createTRForm">
                <div class="modal-body">
                    <!-- Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="filter_category" class="form-label">Filter Kategori</label>
                            <select class="form-control" id="filter_category" name="filter_category">
                                <option value="">-- Semua Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="filter_department" class="form-label">Filter Department</label>
                            <select class="form-control" id="filter_department" name="filter_department">
                                <option value="">-- Semua Department --</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tr_notes" class="form-label">Kategori TR</label>
                        <textarea class="form-control" id="tr_notes" name="notes" rows="2" 
                                  placeholder="Kategori akan terisi otomatis berdasarkan kategori yang dipilih..."></textarea>
                        <small class="form-text text-muted">
                            <i class="feather icon-info"></i>
                            Kategori akan otomatis terisi saat memilih filter kategori, tapi tetap bisa diedit manual.
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Pilih Pengajuan untuk dimasukkan ke TR:</label>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label" for="selectAll">
                                    <strong>Pilih Semua</strong>
                                </label>
                            </div>
                            <span id="pengajuanCount" class="badge badge-info">0 pengajuan tersedia</span>
                        </div>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;" id="pengajuanTableContainer">
                            <table class="table table-sm" id="pengajuanTable">
                                <thead class="sticky-top bg-light">
                                    <tr>
                                        <th width="5%">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAllTable">
                                                <label class="form-check-label" for="selectAllTable"></label>
                                            </div>
                                        </th>
                                        <th>No. Pengajuan</th>
                                        <th>Requester</th>
                                        <th>Kategori</th>
                                        <th>Departemen</th>
                                        <th>Nominal</th>
                                    </tr>
                                </thead>
                                <tbody id="pengajuanTableBody">
                                    @forelse($availablePengajuan as $pengajuan)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input pengajuan-checkbox" 
                                                       type="checkbox" 
                                                       name="pengajuan_ids[]" 
                                                       value="{{ $pengajuan->id }}" 
                                                       data-category="{{ $pengajuan->kategori_pengajuan_id }}"
                                                       data-department="{{ $pengajuan->requester->department_id }}"
                                                       id="pengajuan_{{ $pengajuan->id }}">
                                                <label class="form-check-label" for="pengajuan_{{ $pengajuan->id }}"></label>
                                            </div>
                                        </td>
                                        <td>{{ $pengajuan->nomor_pengajuan }}</td>
                                        <td>{{ $pengajuan->requester->nama }}</td>
                                        <td>{{ $pengajuan->kategoriPengajuan->nama }}</td>
                                        <td>{{ $pengajuan->requester->department->nama }}</td>
                                        <td>Rp {{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}</td>
                                    </tr>
                                    @empty
                                    <tr id="noPengajuanRow">
                                        <td colspan="6" class="text-center py-3">
                                            <div class="text-muted">
                                                <i class="feather icon-info"></i>
                                                Tidak ada pengajuan yang tersedia
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mb-3" id="settlementSection" style="display: none;">
                        <label class="form-label">Settlement Over Budget:</label>
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm">
                                <thead class="sticky-top bg-light">
                                    <tr>
                                        <th width="5%">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAllSettlement">
                                            </div>
                                        </th>
                                        <th>No. Settlement</th>
                                        <th>No. Pengajuan</th>
                                        <th>Kategori</th>
                                        <th>Requester</th>
                                        <th>Over Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="settlementTableBody">
                                    @foreach($availableSettlement as $settlement)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input settlement-checkbox" 
                                                       type="checkbox" 
                                                       name="settlement_ids[]" 
                                                       value="{{ $settlement->id }}">
                                            </div>
                                        </td>
                                        <td>{{ $settlement->nomor_settlement }}</td>
                                        <td>{{ $settlement->pengajuan->nomor_pengajuan }}</td>
                                        <td>LBS - {{ $settlement->pengajuan->kategoripengajuan->nama }}</td>
                                        <td>{{ $settlement->pengajuan->requester->nama }}</td>
                                        <td class="text-danger">
                                            <strong>Rp {{ number_format(abs($settlement->selisih), 0, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <small>
                            <i class="feather icon-info"></i>
                            Gunakan filter di atas untuk mempermudah pencarian pengajuan berdasarkan kategori atau department. 
                            Pilih pengajuan yang ingin dimasukkan ke dalam Transaction Request.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitCreateTR">
                        <i class="feather icon-plus"></i> Buat TR
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View TR Detail with Individual Payment Processing -->
<div class="modal fade" id="viewTRModal" tabindex="-1" aria-labelledby="viewTRModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTRModalLabel">Detail Transaction Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="trDetailContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Process Individual Payment -->
<div class="modal fade" id="processPaymentModal" tabindex="-1" aria-labelledby="processPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="processPaymentModalLabel">Proses Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="processPaymentForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="pengajuan_id" name="pengajuan_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Detail Pengajuan</label>
                        <div id="pengajuanDetail" class="p-3 bg-light rounded">
                            <!-- Detail pengajuan akan dimuat di sini -->
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_status" class="form-label">Status Pembayaran *</label>
                        <select class="form-control" id="payment_status" name="status" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="paid">Bayar</option>
                            <option value="rejected">Tolak</option>
                        </select>
                    </div>
                    
                    <div id="payment_fields" style="display: none;">
                        <div class="mb-3">
                            <label for="bukti_transfer" class="form-label">Bukti Transfer *</label>
                            <input type="file" class="form-control" id="bukti_transfer" name="bukti_transfer" 
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <small class="form-text text-muted">
                                Format yang diizinkan: PDF, JPG, JPEG, PNG. Maksimal 5MB
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tanggal_transfer" class="form-label">Tanggal Transfer *</label>
                            <input type="date" class="form-control" id="tanggal_transfer" name="tanggal_transfer">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="catatan_finance" class="form-label">Catatan Finance</label>
                        <textarea class="form-control" id="catatan_finance" name="catatan_finance" rows="3" 
                                  placeholder="Masukkan catatan tambahan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="submitProcessPayment">
                        <i class="feather icon-check"></i> Proses Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Pengajuan Individual -->
<div class="modal fade" id="detailPengajuanModal" tabindex="-1" aria-labelledby="detailPengajuanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailPengajuanModalLabel">Detail Pengajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detailPengajuanContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Variabel untuk menyimpan data pengajuan asli
        var originalPengajuanData = [];
        
        // Simpan data pengajuan asli saat halaman dimuat
        @foreach($availablePengajuan as $pengajuan)
        originalPengajuanData.push({
            id: {{ $pengajuan->id }},
            nomor_pengajuan: '{{ $pengajuan->nomor_pengajuan }}',
            requester: '{{ $pengajuan->requester->nama }}',
            kategori: '{{ $pengajuan->kategoriPengajuan->nama }}',
            department: '{{ $pengajuan->requester->department->nama }}',
            nominal: {{ $pengajuan->nominal_pengajuan }},
            kategori_id: {{ $pengajuan->kategori_pengajuan_id }},
            department_id: {{ $pengajuan->requester->department_id }}
        });
        @endforeach
        
        
        @if($availableSettlement->count() > 0)
        $('#settlementSection').show();
        @endif
        
        // Handle settlement checkbox
        $(document).on('change', '#selectAllSettlement', function() {
            $('.settlement-checkbox').prop('checked', $(this).is(':checked'));
        });
        
        $(document).on('change', '.settlement-checkbox', function() {
            updateSelectAllSettlementState();
        });
        
        function updateSelectAllSettlementState() {
            var total = $('.settlement-checkbox').length;
            var checked = $('.settlement-checkbox:checked').length;
            $('#selectAllSettlement').prop('checked', total > 0 && total === checked);
        }
        
        // Create TR Button Click
        $('#createTRBtn').on('click', function() {
            // Reset filter dan form
            $('#filter_category').val('').trigger('change');
            $('#filter_department').val('').trigger('change');
            $('#tr_notes').val('');
            loadPengajuanData(originalPengajuanData);
            $('#createTRModal').modal('show');
        });
        
        // Filter change events
        $('#filter_category, #filter_department').on('change', function() {
            filterPengajuan();
        });
        
        // Function untuk filter pengajuan
        function filterPengajuan() {
            var categoryId = $('#filter_category').val();
            var departmentId = $('#filter_department').val();
            
            // Filter data berdasarkan kriteria
            var filteredData = originalPengajuanData.filter(function(pengajuan) {
                var matchCategory = !categoryId || pengajuan.kategori_id == categoryId;
                var matchDepartment = !departmentId || pengajuan.department_id == departmentId;
                return matchCategory && matchDepartment;
            });
            
            // Update auto notes berdasarkan kategori
            updateAutoNotes(categoryId);
            
            // Load data yang sudah difilter
            loadPengajuanData(filteredData);
        }
        
        // Function untuk update auto notes
        function updateAutoNotes(categoryId) {
            if (categoryId) {
                var categoryText = $('#filter_category option:selected').text();
                if (categoryText && categoryText !== '-- Semua Kategori --') {
                    var currentNotes = $('#tr_notes').val().trim();
                    var autoNotes = 'TR ' + categoryText;
                    
                    // Hanya update jika notes kosong atau masih berupa auto-generated
                    if (!currentNotes || currentNotes.startsWith('TR ')) {
                        $('#tr_notes').val(autoNotes);
                    }
                }
            } else {
                // Reset notes jika tidak ada kategori yang dipilih
                var currentNotes = $('#tr_notes').val().trim();
                if (currentNotes.startsWith('TR ')) {
                    $('#tr_notes').val('');
                }
            }
        }
        
        // Function untuk load data pengajuan ke tabel
        function loadPengajuanData(data) {
            var tbody = $('#pengajuanTableBody');
            tbody.empty();
            
            if (data.length === 0) {
                tbody.append(`
                    <tr id="noPengajuanRow">
                        <td colspan="6" class="text-center py-3">
                            <div class="text-muted">
                                <i class="feather icon-info"></i>
                                Tidak ada pengajuan yang sesuai dengan filter
                            </div>
                        </td>
                    </tr>
                `);
            } else {
                data.forEach(function(pengajuan) {
                    var row = `
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input pengajuan-checkbox" 
                                           type="checkbox" 
                                           name="pengajuan_ids[]" 
                                           value="${pengajuan.id}" 
                                           data-category="${pengajuan.kategori_id}"
                                           data-department="${pengajuan.department_id}"
                                           id="pengajuan_${pengajuan.id}">
                                    <label class="form-check-label" for="pengajuan_${pengajuan.id}"></label>
                                </div>
                            </td>
                            <td>${pengajuan.nomor_pengajuan}</td>
                            <td>${pengajuan.requester}</td>
                            <td>${pengajuan.kategori}</td>
                            <td>${pengajuan.department}</td>
                            <td>Rp ${new Intl.NumberFormat('id-ID').format(pengajuan.nominal)}</td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }
            
            // Update counter
            $('#pengajuanCount').text(data.length + ' pengajuan tersedia');
            
            // Reset checkbox states
            $('#selectAll, #selectAllTable').prop('checked', false);
            updateSelectAllState();
        }
        
        // Select All functionality
        $('#selectAll, #selectAllTable').on('change', function() {
            var isChecked = $(this).is(':checked');
            $('.pengajuan-checkbox').prop('checked', isChecked);
            $('#selectAll, #selectAllTable').prop('checked', isChecked);
        });
        
        // Individual checkbox change
        $(document).on('change', '.pengajuan-checkbox', function() {
            updateSelectAllState();
        });
        
        // Function untuk update state select all
        function updateSelectAllState() {
            var totalCheckboxes = $('.pengajuan-checkbox').length;
            var checkedCheckboxes = $('.pengajuan-checkbox:checked').length;
            var allChecked = totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes;
            $('#selectAll, #selectAllTable').prop('checked', allChecked);
        }
        
        // Submit Create TR Form
        $('#createTRForm').on('submit', function(e) {
            e.preventDefault();
            
            var selectedPengajuan = $('.pengajuan-checkbox:checked').length;
            var selectedSettlement = $('.settlement-checkbox:checked').length;
            
            if (selectedPengajuan === 0 && selectedSettlement === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan', 
                    text: 'Pilih minimal satu pengajuan atau settlement untuk membuat TR!'
                });
                return;
            }
            
            var formData = new FormData(this);
            
            $.ajax({
                url: '{{ route("transactionrequest.create") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('#submitCreateTR').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memproses...');
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Transaction Request berhasil dibuat: ' + response.tr_number,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            $('#createTRModal').modal('hide');
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON?.errors;
                    if (errors) {
                        var errorMsg = Object.values(errors).flat().join('\n');
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Error',
                            text: errorMsg
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                        });
                    }
                },
                complete: function() {
                    $('#submitCreateTR').prop('disabled', false).html('<i class="feather icon-plus"></i> Buat TR');
                }
            });
        });
        
        // View TR Detail with Individual Payment Options
        $(document).on('click', '.view-tr-btn', function() {
            var trId = $(this).data('id');
            
            $.ajax({
                url: '/TransactionRequest/detail/' + trId,
                type: 'GET',
                beforeSend: function() {
                    $('#trDetailContent').html('<div class="text-center py-4"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
                },
                success: function(response) {
                    if (response.success) {
                        var trData = response.data;
                        buildTRDetailHTML(trData);
                        $('#viewTRModal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal mengambil detail TR'
                    });
                }
            });
        });
        
        // Function to build TR detail HTML
        function buildTRDetailHTML(trData) {
            var html = '<div class="row">';
            
            // TR Information
            html += '<div class="col-md-6">';
            html += '<h6>Informasi TR</h6>';
            html += '<table class="table table-sm">';
            html += '<tr><td><strong>No. TR:</strong></td><td>' + trData.tr_number + '</td></tr>';
            html += '<tr><td><strong>Status:</strong></td><td>';
            if (trData.status === 'completed') {
                html += '<span class="badge badge-success">Completed</span>';
            } else if (trData.status === 'processing') {
                html += '<span class="badge badge-info">Processing</span>';
            } else {
                html += '<span class="badge badge-warning">Pending</span>';
            }
            html += '</td></tr>';
            html += '<tr><td><strong>Dibuat:</strong></td><td>' + new Date(trData.created_at).toLocaleString('id-ID') + '</td></tr>';
            html += '<tr><td><strong>Dibuat Oleh:</strong></td><td>' + trData.created_by.nama + '</td></tr>';
            html += '</table>';
            html += '</div>';
            
            // Notes
            html += '<div class="col-md-6">';
            html += '<h6>Kategori TR</h6>';
            html += '<p>' + (trData.notes || '-') + '</p>';
            html += '</div>';
            html += '</div><hr>';
            
            // Items List
            html += '<h6>Daftar Items (' + trData.transaction_requests.length + ')</h6>';
            html += '<div class="table-responsive">';
            html += '<table class="table table-sm">';
            html += '<thead>';
            html += '<tr>';
            html += '<th>No. Transaksi</th>';
            html += '<th>Requester</th>';
            html += '<th>Kategori</th>';
            html += '<th>Nominal</th>';
            html += '<th>Type</th>';
            html += '<th>Status</th>';
            html += '<th>Aksi</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';
            
            var totalNominal = 0;
            trData.transaction_requests.forEach(function(tr) {
                var itemData = {};
                
                // Cek apakah ini pengajuan atau settlement
                if (tr.pengajuan) {
                    itemData = {
                        id: tr.pengajuan.id,
                        type: 'pengajuan',
                        typeLabel: 'Pengajuan',
                        badgeClass: 'badge-primary',
                        nominal: parseFloat(tr.pengajuan.nominal_pengajuan),
                        nomorTransaksi: tr.pengajuan.nomor_pengajuan,
                        requester: tr.pengajuan.requester.nama,
                        kategori: tr.pengajuan.kategori_pengajuan.nama
                    };
                } else if (tr.settlement) {
                    itemData = {
                        id: tr.settlement.id,
                        type: 'settlement',
                        typeLabel: 'Settlement (Over)',
                        badgeClass: 'badge-danger',
                        nominal: Math.abs(parseFloat(tr.settlement.selisih)),
                        nomorTransaksi: tr.settlement.nomor_settlement,
                        requester: tr.settlement.pengajuan.requester.nama,
                        kategori: tr.settlement.pengajuan.kategori_pengajuan.nama
                    };
                }
                
                var statusBadge = getStatusBadge(tr.status);
                var actionButton = getActionButton(tr, itemData);
                
                html += '<tr>';
                html += '<td>' + itemData.nomorTransaksi + '</td>';
                html += '<td>' + itemData.requester + '</td>';
                html += '<td>' + itemData.kategori + '</td>';
                html += '<td>Rp ' + new Intl.NumberFormat('id-ID').format(itemData.nominal) + '</td>';
                html += '<td><span class="badge ' + itemData.badgeClass + '">' + itemData.typeLabel + '</span></td>';
                html += '<td>' + statusBadge + '</td>';
                html += '<td>' + actionButton + '</td>';
                html += '</tr>';
                
                totalNominal += itemData.nominal;
            });
            
            html += '</tbody>';
            html += '<tfoot>';
            html += '<tr class="table-active">';
            html += '<th colspan="3">Total</th>';
            html += '<th>Rp ' + new Intl.NumberFormat('id-ID').format(totalNominal) + '</th>';
            html += '<th colspan="3"></th>';
            html += '</tr>';
            html += '</tfoot>';
            html += '</table>';
            html += '</div>';
            $('#trDetailContent').html(html);
        }
        
        function buildDetailPengajuanHTML(data) {
            var html = '';
    
            // --- 1. Header Informasi Utama & Requester ---
            html += `
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm border-top-primary">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 text-primary"><i class="fas fa-file-alt me-2"></i>Informasi Pengajuan</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td class="text-muted" width="130">Nomor</td><td class="fw-bold">: ${data.nomor_pengajuan}</td></tr>
                                    <tr><td class="text-muted">Judul</td><td class="fw-bold">: ${data.judul}</td></tr>
                                    <tr><td class="text-muted">Kategori</td><td>: ${data.kategori_pengajuan.nama}</td></tr>
                                    <tr><td class="text-muted">Tgl Pengajuan</td><td>: ${formatDate(data.tanggal_pengajuan)}</td></tr>
                                    <tr><td class="text-muted">Tgl Kebutuhan</td><td>: ${formatDate(data.tanggal_kebutuhan)}</td></tr>
                                    <tr><td class="text-muted">Status</td><td>: ${getStatusPengajuanBadge(data.status_pengajuan)}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 shadow-sm border-top-info">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 text-info"><i class="fas fa-user me-2"></i>Informasi Requester</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td class="text-muted" width="130">Nama</td><td class="fw-bold">: ${data.requester.nama}</td></tr>
                                    <tr><td class="text-muted">Email</td><td>: ${data.requester.email}</td></tr>
                                    <tr><td class="text-muted">Departemen</td><td>: ${data.requester.department}</td></tr>
                                    <tr>
                                        <td class="text-muted">Total Nominal</td>
                                        <td class="fw-bold text-success fs-5">: ${data.mata_uang} ${new Intl.NumberFormat('id-ID').format(data.nominal_pengajuan)}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            `;
    
            // --- 2. Deskripsi & Catatan ---
            if (data.deskripsi || data.catatan_requester) {
                html += `
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body bg-light">
                `;
                if (data.deskripsi) {
                    html += `<h6 class="fw-bold">Deskripsi:</h6><p class="text-muted mb-3">${data.deskripsi.replace(/\n/g, '<br>')}</p>`;
                }
                if (data.catatan_requester) {
                    html += `<h6 class="fw-bold">Catatan Requester:</h6><p class="text-muted mb-0 fst-italic">"${data.catatan_requester}"</p>`;
                }
                html += `
                        </div>
                    </div>
                `;
            }
    
            // --- 3. Rincian Detail Item (Dynamic Fields) ---
            if (data.detail_fields && data.detail_fields.length > 0) {
                html += `
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-list-ol me-2"></i>Rincian Detail Pengajuan</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="50" class="text-center">No</th>
                                        <th>Nama Field</th>
                                        <th>Isi / Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                `;
    
                data.detail_fields.forEach(function(field, index) {
                    let displayValue = field.value;
    
                    // Format value berdasarkan tipe field
                    if (field.type === 'currency') {
                        displayValue = 'Rp ' + new Intl.NumberFormat('id-ID').format(parseFloat(field.value) || 0);
                    } else if (field.type === 'date') {
                        displayValue = formatDate(field.value);
                    } else if (field.type === 'file') {
                        displayValue = `<a href="/storage/${field.value}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-download me-1"></i> Lihat File</a>`;
                    }
    
                    // Cek Intervensi Finance
                    let interventionBadge = '';
                    if (field.is_intervened_by_finance) {
                        interventionBadge = ` <span class="badge bg-warning text-dark ms-2"><i class="fas fa-edit"></i> Direvisi Finance</span>`;
                        if (field.nilai_awal) {
                            let originalVal = field.nilai_awal;
                            if (field.type === 'currency') originalVal = 'Rp ' + new Intl.NumberFormat('id-ID').format(parseFloat(field.nilai_awal));
                            displayValue += `<br><small class="text-muted text-decoration-line-through">Awal: ${originalVal}</small>`;
                        }
                    }
    
                    html += `
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td class="fw-bold">${field.label}</td>
                            <td>${displayValue} ${interventionBadge}</td>
                        </tr>
                    `;
                });
    
                html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            }
    
            // --- 4. File Pendukung ---
            if (data.file_pendukung && data.file_pendukung.length > 0) {
                html += `
                    <div class="card mb-3 shadow-sm border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="fas fa-paperclip me-2"></i>File Pendukung</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                `;
                data.file_pendukung.forEach(function(file, index) {
                    html += `
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-center p-2 border rounded bg-light">
                                <i class="fas fa-file me-3 text-secondary fa-lg"></i>
                                <span class="text-truncate me-auto" style="max-width: 200px;">File ${index + 1}</span>
                                <a href="/storage/${file}" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    `;
                });
                html += `
                            </div>
                        </div>
                    </div>
                `;
            }
    
            // Render ke modal
            $('#detailPengajuanContent').html(html);
        }
        
        // Function to get status badge
        function getStatusBadge(status) {
            switch(status) {
                case 'waiting':
                    return '<span class="badge badge-warning">Menunggu</span>';
                case 'paid':
                    return '<span class="badge badge-success">Dibayar</span>';
                case 'rejected':
                    return '<span class="badge badge-danger">Ditolak</span>';
                default:
                    return '<span class="badge badge-secondary">Unknown</span>';
            }
        }
        
        function getStatusPengajuanBadge(status) {
            switch(status) {
                case 'pending': return '<span class="badge bg-warning text-dark">Pending</span>';
                case 'proses': return '<span class="badge bg-info text-dark">Proses Approval</span>';
                case 'approved': return '<span class="badge bg-success">Disetujui</span>';
                case 'rejected': return '<span class="badge bg-danger">Ditolak</span>';
                case 'revision': return '<span class="badge bg-warning">Revisi</span>';
                case 'completed': return '<span class="badge bg-primary">Selesai</span>';
                case 'proses_settlement': return '<span class="badge bg-secondary">Proses Settlement</span>';
                default: return '<span class="badge bg-secondary">' + status + '</span>';
            }
        }
        
        function formatDate(dateString) {
            if (!dateString) return '-';
            return new Date(dateString).toLocaleDateString('id-ID', {
                day: '2-digit', month: 'long', year: 'numeric'
            });
        }
        
        function formatDateTime(dateString) {
            if (!dateString) return '-';
            return new Date(dateString).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        function formatFieldValue(value, tipeField) {
            if (!value) return '-';
            
            switch(tipeField) {
                case 'currency':
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(parseFloat(value));
                case 'number':
                    return new Intl.NumberFormat('id-ID').format(parseFloat(value));
                case 'date':
                    return formatDate(value);
                case 'file':
                    return '<a href="/storage/' + value + '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="feather icon-external-link"></i> Lihat File</a>';
                default:
                    return value.toString().replace(/\n/g, '<br>');
            }
        }
        
        // Function to get action button
        function getActionButton(tr, itemData) {
            var buttons = '';
            
            // Button Process/Download berdasarkan status
            if (tr.status === 'waiting') {
                buttons += '<button class="btn btn-sm btn-primary me-1 process-payment-btn" data-item-id="' + itemData.id + '" data-item-type="' + itemData.type + '" title="Proses Pembayaran">';
                buttons += '<i class="feather icon-credit-card"></i> Proses';
                buttons += '</button>';
            } else if (tr.status === 'paid' && tr.bukti_transfer) {
                // PERBAIKAN: Sesuaikan URL dengan route yang benar
                var downloadUrl = itemData.type === 'settlement' ? 
                    '/TransactionRequest/download-bukti-settlement/' + itemData.id : 
                    '/download-bukti-pengajuan/' + itemData.id; // Sesuai dengan route yang sudah ada
                    
                buttons += '<a href="' + downloadUrl + '" class="btn btn-sm me-1 btn-outline-primary" title="Download Bukti">';
                buttons += '<i class="feather icon-download"></i> Download';
                buttons += '</a>';
                
                // Tambahan: Button debug untuk troubleshooting (bisa dihapus di production)
                if (typeof debugMode !== 'undefined' && debugMode === true) {
                    var debugUrl = '/debug-bukti-pengajuan/' + itemData.id;
                    buttons += '<a href="' + debugUrl + '" target="_blank" class="btn btn-sm btn-warning ms-1" title="Debug Info">';
                    buttons += '<i class="feather icon-info"></i> Debug';
                    buttons += '</a>';
                }
            }
            
            // Button Detail (selalu tampil)
            if (itemData.type === 'settlement') {
                buttons += '<button class="btn btn-sm btn-outline-info  detail-settlement-btn" data-settlement-id="' + itemData.id + '" title="Detail Settlement">';
                buttons += '<i class="feather icon-eye"></i> Detail';
                buttons += '</button>';
            } else {
                buttons += '<button class="btn btn-sm btn-outline-info  detail-pengajuan-btn" data-pengajuan-id="' + itemData.id + '" title="Detail Pengajuan">';
                buttons += '<i class="feather icon-eye"></i> Detail';
                buttons += '</button>';
            }
            
            return buttons;
        }
        
        // Process Individual Payment Button Click
        $(document).on('click', '.process-payment-btn', function() {
            var itemId = $(this).data('item-id');
            var itemType = $(this).data('item-type');
            openProcessPaymentModal(itemId, itemType);
        });
        
        $(document).on('click', '.detail-pengajuan-btn', function() {
            var pengajuanId = $(this).data('pengajuan-id');
            loadDetailPengajuan(pengajuanId);
        });
        
        $(document).on('click', '.detail-settlement-btn', function() {
            var settlementId = $(this).data('settlement-id');
            loadDetailSettlement(settlementId);
        });
        
        function loadDetailSettlement(settlementId) {
            $.ajax({
                url: '/TransactionRequest/detail-settlement/' + settlementId,
                type: 'GET',
                beforeSend: function() {
                    // Tampilkan loading di dalam modal
                    $('#detailPengajuanContent').html(`
                        <div class="text-center py-5">
                            <div class="spinner-border text-danger" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Memuat data settlement over...</p>
                        </div>
                    `);
                    // Ubah judul modal agar sesuai konteks
                    $('#detailPengajuanModalLabel').html('<i class="fas fa-exclamation-circle text-danger me-2"></i>Detail Settlement (Over Budget)');
                    $('#detailPengajuanModal').modal('show');
                },
                success: function(response) {
                    if (response.success) {
                        buildDetailSettlementHTML(response.data);
                    } else {
                        $('#detailPengajuanContent').html('<div class="alert alert-danger">Data tidak ditemukan.</div>');
                    }
                },
                error: function() {
                    $('#detailPengajuanContent').html('<div class="alert alert-danger">Gagal mengambil data dari server.</div>');
                }
            });
        }
        
        function buildDetailSettlementHTML(data) {
            // Mapping data (menyesuaikan struktur JSON umum Laravel)
            // Jika 'data' langsung berisi settlement, gunakan 'data'. 
            // Jika terbungkus dalam 'data.settlement', sesuaikan.
            // Asumsi disini: response.data adalah objek settlement yang punya relasi ke pengajuan
            
            var settlement = data.settlement ? data.settlement : data; 
            var pengajuan = settlement.pengajuan;
            var requester = pengajuan.requester;
            
            var html = '';

            // 1. Header Info (Settlement & Pengajuan Asal)
            html += `
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-top-danger shadow-sm">
                            <div class="card-body">
                                <h6 class="text-danger mb-3"><i class="fas fa-receipt me-2"></i>Informasi Settlement</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td class="text-muted" width="140">No. Settlement</td><td class="fw-bold">: ${settlement.nomor_settlement}</td></tr>
                                    <tr><td class="text-muted">Tanggal</td><td>: ${formatDate(settlement.tanggal_settlement)}</td></tr>
                                    <tr><td class="text-muted">Total Actual</td><td>: Rp ${new Intl.NumberFormat('id-ID').format(settlement.total_actual)}</td></tr>
                                    <tr class="bg-light text-danger">
                                        <td class="fw-bold">Over Amount</td>
                                        <td class="fw-bold">: Rp ${new Intl.NumberFormat('id-ID').format(Math.abs(settlement.selisih))}</td>
                                    </tr>
                                    <tr><td class="text-muted">Status</td><td>: <span class="badge bg-danger">Over Budget</span></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-top-secondary shadow-sm">
                            <div class="card-body">
                                <h6 class="text-secondary mb-3"><i class="fas fa-file-alt me-2"></i>Referensi Pengajuan Awal</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td class="text-muted" width="140">No. Pengajuan</td><td class="fw-bold">: ${pengajuan.nomor_pengajuan}</td></tr>
                                    <tr><td class="text-muted">Judul</td><td>: ${pengajuan.judul}</td></tr>
                                    <tr><td class="text-muted">Budget Awal</td><td>: Rp ${new Intl.NumberFormat('id-ID').format(pengajuan.nominal_pengajuan)}</td></tr>
                                    <tr><td class="text-muted">Requester</td><td>: ${requester.nama}</td></tr>
                                    <tr><td class="text-muted">Departemen</td><td>: ${requester.department ? requester.department.nama : '-'}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // 2. Catatan Settlement
            if (settlement.catatan_settlement) {
                html += `
                    <div class="card mb-4 shadow-sm bg-light border-warning">
                        <div class="card-body">
                            <h6 class="fw-bold text-warning"><i class="fas fa-sticky-note me-2"></i>Catatan Settlement:</h6>
                            <p class="mb-0 fst-italic">"${settlement.catatan_settlement}"</p>
                        </div>
                    </div>
                `;
            }

            // 3. Tabel Rincian Item Settlement
            if (settlement.details && settlement.details.length > 0) {
                html += `
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-list-ol me-2"></i>Rincian Perbandingan Biaya</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0" style="font-size: 0.9rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" width="5%">No</th>
                                        <th width="25%">Keterangan Item</th>
                                        <th width="10%">Kategori</th>
                                        <th width="15%" class="text-end bg-light text-muted">Budget Awal</th>
                                        <th width="15%" class="text-end">Actual (LBS)</th>
                                        <th width="15%" class="text-end">Selisih</th>
                                        <th width="10%" class="text-center">Bukti</th>
                                    </tr>
                                </thead>
                                <tbody>
                `;

                settlement.details.forEach(function(detail, index) {
                    var fileLink = detail.file_bukti ? 
                        `<a href="/storage/${detail.file_bukti}" target="_blank" class="btn btn-xs btn-outline-danger"><i class="fas fa-image"></i></a>` : 
                        '<span class="text-muted">-</span>';

                    // Hitung Budget Awal untuk item ini
                    // Pastikan properti ini dikirim dari Controller (relasi ke detailPengajuan)
                    var nominalAwal = parseFloat(detail.nominal_awal) || 0;
                    var jumlahHari = parseFloat(detail.jumlah_hari) || 1;
                    var totalBudgetAwal = nominalAwal * jumlahHari;
                    
                    var nominalActual = parseFloat(detail.nominal) || 0;
                    var selisihItem = totalBudgetAwal - nominalActual;
                    
                    // Tentukan warna selisih
                    // Positif (Hijau) = Hemat/Sisa
                    // Negatif (Merah) = Over Budget
                    var selisihClass = selisihItem >= 0 ? 'text-success' : 'text-danger fw-bold';
                    var selisihText = (selisihItem < 0 ? '(' : '') + 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(selisihItem)) + (selisihItem < 0 ? ')' : '');

                    html += `
                        <tr>
                            <td class="text-center align-middle">${index + 1}</td>
                            <td class="align-middle">
                                <span class="fw-bold">${detail.keterangan}</span>
                                ${detail.catatan ? '<br><small class="text-muted fst-italic"><i class="fas fa-info-circle me-1"></i>'+detail.catatan+'</small>' : ''}
                            </td>
                            <td class="align-middle">${detail.kategori_biaya || '-'}</td>
                            
                            <td class="text-end align-middle text-muted bg-light">
                                Rp ${new Intl.NumberFormat('id-ID').format(totalBudgetAwal)}
                            </td>
                            
                            <td class="text-end align-middle fw-bold">
                                Rp ${new Intl.NumberFormat('id-ID').format(nominalActual)}
                            </td>
                            
                            <td class="text-end align-middle ${selisihClass}">
                                ${selisihText}
                            </td>

                            <td class="text-center align-middle">${fileLink}</td>
                        </tr>
                    `;
                });

                // Hitung Total untuk Footer Tabel
                var totalBudgetAwalGlobal = parseFloat(settlement.total_Awal) || 0; // Pastikan ini dikirim dari backend
                if(!totalBudgetAwalGlobal && settlement.pengajuan) {
                     totalBudgetAwalGlobal = parseFloat(settlement.pengajuan.nominal_pengajuan) || 0;
                }
                
                var totalActualGlobal = parseFloat(settlement.total_actual) || 0;
                var totalSelisihGlobal = totalBudgetAwalGlobal - totalActualGlobal;
                var totalSelisihClass = totalSelisihGlobal >= 0 ? 'text-success' : 'text-danger';
                var totalSelisihText = (totalSelisihGlobal < 0 ? '- ' : '') + 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(totalSelisihGlobal));

                html += `       </tbody>
                                <tfoot class="table-light" style="border-top: 2px solid #dee2e6;">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold text-uppercase text-muted">Total Keseluruhan:</td>
                                        <td class="text-end fw-bold text-muted">Rp ${new Intl.NumberFormat('id-ID').format(totalBudgetAwalGlobal)}</td>
                                        <td class="text-end fw-bold text-dark">Rp ${new Intl.NumberFormat('id-ID').format(totalActualGlobal)}</td>
                                        <td class="text-end fw-bold ${totalSelisihClass}">${totalSelisihText}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                `;
            }

            // 4. Bukti Transfer Pengembalian (Jika Ada)
            if (settlement.file_bukti_transfer) {
                html += `
                    <div class="card mb-3 shadow-sm border-success">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-success fw-bold mb-1"><i class="fas fa-check-circle me-2"></i>Bukti Transfer Pengembalian Dana</h6>
                                <small class="text-muted">Tanggal Transfer: ${formatDate(settlement.tanggal_transfer)}</small>
                            </div>
                            <a href="/storage/${settlement.file_bukti_transfer}" target="_blank" class="btn btn-success">
                                <i class="fas fa-download me-1"></i> Download Bukti
                            </a>
                        </div>
                    </div>
                `;
            } else {
                // Jika belum ada bukti transfer (biasanya untuk case Over Budget yang belum dibayar requester)
                html += `
                    <div class="alert alert-warning d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                        <div>
                            <strong>Menunggu Transfer Requester</strong><br>
                            Requester belum mengunggah bukti transfer untuk pengembalian selisih dana sebesar 
                            <b>Rp ${new Intl.NumberFormat('id-ID').format(Math.abs(settlement.selisih))}</b>.
                        </div>
                    </div>
                `;
            }

            $('#detailPengajuanContent').html(html);
        }
        
        // Function to open process payment modal
        function openProcessPaymentModal(itemId, itemType) {
            // Simpan info item type ke modal data
            $('#processPaymentModal').data('item-type', itemType);
            
            // Set value hidden input berdasarkan tipe
            if (itemType === 'settlement') {
                $('#pengajuan_id').val('settlement_' + itemId); 
            } else {
                $('#pengajuan_id').val(itemId);
            }
            
            // Reset form tampilan awal
            $('#processPaymentForm')[0].reset();
            $('#payment_fields').hide();
            $('#bukti_transfer').prop('required', false);
            $('#tanggal_transfer').prop('required', false);
            
            // Tampilkan loading state
            $('#pengajuanDetail').html('<div class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Memuat data...</div>');
            
            // Tentukan URL endpoint berdasarkan type
            var detailUrl = itemType === 'settlement' ? 
                '/TransactionRequest/detail-settlement/' + itemId :
                '/TransactionRequest/detail-pengajuan/' + itemId;
            
            // AJAX Call
            $.ajax({
                url: detailUrl,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        var html = '<table class="table table-sm table-borderless mb-0" style="font-size: 0.9rem;">';
                        
                        if (itemType === 'settlement') {
                            // --- TAMPILAN DETAIL SETTLEMENT (OVER BUDGET) ---
                            var requesterName = data.pengajuan.requester ? data.pengajuan.requester.nama : '-';
                            var deptName = (data.pengajuan.requester && data.pengajuan.requester.department) ? data.pengajuan.requester.department.nama : '-';
                            var categoryName = data.pengajuan.kategori_pengajuan ? data.pengajuan.kategori_pengajuan.nama : '-';
                            var nominalOver = Math.abs(parseFloat(data.selisih));

                            html += '<tr><td width="35%" class="text-muted">No. Settlement</td><td class="fw-bold">: ' + data.nomor_settlement + '</td></tr>';
                            html += '<tr><td class="text-muted">No. Pengajuan Asal</td><td>: ' + data.pengajuan.nomor_pengajuan + '</td></tr>';
                            html += '<tr><td class="text-muted">Requester</td><td>: ' + requesterName + '</td></tr>';
                            html += '<tr><td class="text-muted">Departemen</td><td>: ' + deptName + '</td></tr>';
                            html += '<tr><td class="text-muted">Kategori</td><td>: ' + categoryName + '</td></tr>';
                            html += '<tr><td class="text-muted">Total Actual</td><td>: Rp ' + new Intl.NumberFormat('id-ID').format(data.total_actual) + '</td></tr>';
                            html += '<tr class="table-danger"><td class="text-danger fw-bold">Over Amount</td><td class="text-danger fw-bold">: Rp ' + new Intl.NumberFormat('id-ID').format(nominalOver) + '</td></tr>';
                            
                        } else {
                            // --- TAMPILAN DETAIL PENGAJUAN BIASA ---
                            var requesterName = data.requester ? data.requester.nama : '-';
                            var deptName = (data.requester && data.requester.department) ? data.requester.department.nama : '-';
                            var categoryName = data.kategori_pengajuan ? data.kategori_pengajuan.nama : '-';
                            
                            html += '<tr><td width="35%" class="text-muted">No. Pengajuan</td><td class="fw-bold">: ' + data.nomor_pengajuan + '</td></tr>';
                            html += '<tr><td class="text-muted">Requester</td><td>: ' + requesterName + '</td></tr>';
                            html += '<tr><td class="text-muted">Departemen</td><td>: ' + deptName + '</td></tr>';
                            html += '<tr><td class="text-muted">Kategori</td><td>: ' + categoryName + '</td></tr>';
                            html += '<tr><td class="text-muted">Tanggal Kebutuhan</td><td>: ' + formatDate(data.tanggal_kebutuhan) + '</td></tr>'; // Pastikan fungsi formatDate ada
                            html += '<tr class="table-primary"><td class="text-primary fw-bold">Nominal</td><td class="text-primary fw-bold">: Rp ' + new Intl.NumberFormat('id-ID').format(data.nominal_pengajuan) + '</td></tr>';
                        }
                        
                        html += '</table>';
                        
                        // Masukkan HTML ke dalam modal
                        $('#pengajuanDetail').html(html);
                        $('#processPaymentModal').modal('show');
                    } else {
                        $('#pengajuanDetail').html('<div class="alert alert-danger">Gagal memuat data.</div>');
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal mengambil detail item dari server.'
                    });
                    $('#processPaymentModal').modal('hide');
                }
            });
        }
        
        // Function untuk memuat detail pengajuan
        function loadDetailPengajuan(pengajuanId) {
            $.ajax({
                // Pastikan route ini sesuai dengan route di web.php yang mengarah ke LaporanPengajuanController@detail
                url: '/laporan-pengajuan/detail/' + pengajuanId, 
                type: 'GET',
                beforeSend: function() {
                    $('#detailPengajuanContent').html(`
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Sedang memuat data...</p>
                        </div>
                    `);
                    $('#detailPengajuanModal').modal('show');
                },
                success: function(response) {
                    if (response.success) {
                        buildDetailPengajuanHTML(response.data);
                    } else {
                        $('#detailPengajuanContent').html(`
                            <div class="alert alert-danger">
                                <i class="feather icon-alert-triangle me-2"></i> ${response.message}
                            </div>
                        `);
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                    $('#detailPengajuanContent').html(`
                        <div class="alert alert-danger">
                            <i class="feather icon-alert-triangle me-2"></i> Gagal mengambil data dari server.
                        </div>
                    `);
                }
            });
        }
        
        // Show/hide payment fields based on status
        $('#payment_status').on('change', function() {
            if ($(this).val() === 'paid') {
                $('#payment_fields').show();
                $('#bukti_transfer').prop('required', true);
                $('#tanggal_transfer').prop('required', true);
            } else {
                $('#payment_fields').hide();
                $('#bukti_transfer').prop('required', false);
                $('#tanggal_transfer').prop('required', false);
            }
        });
        
        // Submit Process Payment Form
        $('#processPaymentForm').on('submit', function(e) {
            e.preventDefault();
            
            var itemId = $('#pengajuan_id').val();
            var itemType = $('#processPaymentModal').data('item-type');
            var formData = new FormData(this);
            
            // Tentukan URL berdasarkan type
            var submitUrl;
            if (itemType === 'settlement') {
                var actualSettlementId = itemId.replace('settlement_', '');
                submitUrl = '/TransactionRequest/settlement/' + actualSettlementId + '/update-status';
            } else {
                submitUrl = '/TransactionRequest/' + itemId + '/update-status';
            }
            
            $.ajax({
                url: submitUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('#submitProcessPayment').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memproses...');
                },
                success: function(response) {
                    if (response.success) {
                        var itemTypeText = itemType === 'settlement' ? 'settlement' : 'pengajuan';
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Status pembayaran ' + itemTypeText + ' berhasil diperbarui!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            $('#processPaymentModal').modal('hide');
                            $('#viewTRModal').modal('hide');
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON?.errors;
                    if (errors) {
                        var errorMsg = Object.values(errors).flat().join('\n');
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Error',
                            text: errorMsg
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                        });
                    }
                },
                complete: function() {
                    $('#submitProcessPayment').prop('disabled', false).html('<i class="feather icon-check"></i> Proses Pembayaran');
                }
            });
        });
        
        // Delete TR Button Click
        $(document).on('click', '.delete-tr-btn', function() {
            var trId = $(this).data('id');
            
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus Transaction Request ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/TransactionRequest/' + trId + '/delete',
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Transaction Request berhasil dihapus!',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endsection