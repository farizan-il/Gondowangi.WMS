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
                            <i class="feather icon-plus"></i> Menambahkan TR
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
                                <th>Jumlah Item</th>
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
                                    @php
                                        $breakdown = $trGroup->getItemBreakdown();
                                    @endphp
                                    
                                    @if($breakdown['pengajuan_count'] > 0 && $breakdown['settlement_count'] > 0)
                                        <div>
                                            <span class="badge bg-primary me-1">
                                                {{ $breakdown['pengajuan_count'] }} Pengajuan
                                            </span>
                                            <span class="badge bg-warning">
                                                {{ $breakdown['settlement_count'] }} Settlement
                                            </span>
                                        </div>
                                    @elseif($breakdown['pengajuan_count'] > 0)
                                        <span class="badge bg-primary">
                                            {{ $breakdown['pengajuan_count'] }} Pengajuan
                                        </span>
                                    @elseif($breakdown['settlement_count'] > 0)
                                        <span class="badge bg-warning">
                                            {{ $breakdown['settlement_count'] }} Settlement Over
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">0 Item</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>Rp {{ number_format($trGroup->total_nominal ?: 0, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    @if($trGroup->status == 'pending')
                                        <span class="badge badge-light-warning"><strong>Menunggu</strong></span>
                                    @elseif($trGroup->status == 'processing')
                                        <span class="badge badge-light-info">Processing</span>
                                    @else
                                        <span class="badge badge-light-success"><strong>Selesai</strong></span>
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
                        <label class="form-label">Pilih Item untuk dimasukkan ke TR:</label>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label" for="selectAll">
                                    <strong>Pilih Semua</strong>
                                </label>
                            </div>
                            <span id="itemCount" class="badge badge-info">0 item tersedia</span>
                        </div>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;" id="itemTableContainer">
                            <table class="table table-sm" id="itemTable">
                                <thead class="sticky-top bg-light">
                                    <tr>
                                        <th width="5%">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAllTable">
                                                <label class="form-check-label" for="selectAllTable"></label>
                                            </div>
                                        </th>
                                        <th>Type</th>
                                        <th>No. Transaksi</th>
                                        <th>Requester</th>
                                        <th>Kategori</th>
                                        <th>Departemen</th>
                                        <th>Nominal</th>
                                    </tr>
                                </thead>
                                <tbody id="itemTableBody">
                                    @if(isset($availableItems))
                                        @forelse($availableItems as $item)
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input item-checkbox" 
                                                           type="checkbox" 
                                                           name="item_ids[]" 
                                                           value="{{ $item['id'] }}" 
                                                           data-type="{{ $item['type'] }}"
                                                           data-category="{{ $item['kategori_id'] }}"
                                                           data-department="{{ $item['department_id'] }}"
                                                           id="item_{{ $item['type'] }}_{{ $item['id'] }}">
                                                    <label class="form-check-label" for="item_{{ $item['type'] }}_{{ $item['id'] }}"></label>
                                                    <input type="hidden" name="item_types[]" value="{{ $item['type'] }}">
                                                </div>
                                            </td>
                                            <td>
                                                @if($item['type'] === 'pengajuan')
                                                    <span class="badge bg-primary">Pengajuan</span>
                                                @else
                                                    <span class="badge bg-warning">Settlement Over</span>
                                                @endif
                                            </td>
                                            <td>{{ $item['nomor'] }}</td>
                                            <td>{{ $item['requester'] }}</td>
                                            <td>{{ $item['kategori'] }}</td>
                                            <td>{{ $item['department'] }}</td>
                                            <td>Rp {{ number_format($item['nominal'], 0, ',', '.') }}</td>
                                        </tr>
                                        @empty
                                        <tr id="noItemRow">
                                            <td colspan="7" class="text-center py-3">
                                                <div class="text-muted">
                                                    <i class="feather icon-info"></i>
                                                    Tidak ada item yang tersedia
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    @endif
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
    <div class="modal-dialog modal-xl modal-dialog-centered">
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
            filterItems();
        });
        
        function filterItems() {
            var categoryId = $('#filter_category').val();
            var departmentId = $('#filter_department').val();
            
            // Filter data berdasarkan kriteria
            var filteredData = originalItemData.filter(function(item) {
                var matchCategory = !categoryId || item.kategori_id == categoryId;
                var matchDepartment = !departmentId || item.department_id == departmentId;
                return matchCategory && matchDepartment;
            });
            
            // Update auto notes berdasarkan kategori
            updateAutoNotes(categoryId);
            
            // Load data yang sudah difilter
            loadItemData(filteredData);
        }
        
        function loadItemData(data) {
            var tbody = $('#itemTableBody');
            tbody.empty();
            
            if (data.length === 0) {
                tbody.append(`
                    <tr id="noItemRow">
                        <td colspan="7" class="text-center py-3">
                            <div class="text-muted">
                                <i class="feather icon-info"></i>
                                Tidak ada item yang sesuai dengan filter
                            </div>
                        </td>
                    </tr>
                `);
            } else {
                data.forEach(function(item) {
                    var typeBadge = item.type === 'pengajuan' ? 
                        '<span class="badge bg-primary">Pengajuan</span>' :
                        '<span class="badge bg-warning">Settlement Over</span>';
                        
                    var row = `
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input item-checkbox" 
                                           type="checkbox" 
                                           name="item_ids[]" 
                                           value="${item.id}" 
                                           data-type="${item.type}"
                                           data-category="${item.kategori_id}"
                                           data-department="${item.department_id}"
                                           id="item_${item.type}_${item.id}">
                                    <label class="form-check-label" for="item_${item.type}_${item.id}"></label>
                                    <input type="hidden" name="item_types[]" value="${item.type}">
                                </div>
                            </td>
                            <td>${typeBadge}</td>
                            <td>${item.nomor}</td>
                            <td>${item.requester}</td>
                            <td>${item.kategori}</td>
                            <td>${item.department}</td>
                            <td>Rp ${new Intl.NumberFormat('id-ID').format(item.nominal)}</td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }
            
            // Update counter
            $('#itemCount').text(data.length + ' item tersedia');
            
            // Reset checkbox states
            $('#selectAll, #selectAllTable').prop('checked', false);
            updateSelectAllState();
        }
        
        $('#selectAll, #selectAllTable').on('change', function() {
            var isChecked = $(this).is(':checked');
            $('.item-checkbox').prop('checked', isChecked);
            $('#selectAll, #selectAllTable').prop('checked', isChecked);
        });
        
        // Individual checkbox change
        $(document).on('change', '.item-checkbox', function() {
            updateSelectAllState();
        });
        
        function updateSelectAllState() {
            var totalCheckboxes = $('.item-checkbox').length;
            var checkedCheckboxes = $('.item-checkbox:checked').length;
            var allChecked = totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes;
            $('#selectAll, #selectAllTable').prop('checked', allChecked);
        }
        
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
        
        
        // Individual checkbox change
        $(document).on('change', '.pengajuan-checkbox', function() {
            updateSelectAllState();
        });
        
        // Submit Create TR Form
        $('#createTRForm').on('submit', function(e) {
            e.preventDefault();
            
            var selectedItems = $('.item-checkbox:checked').length;
            if (selectedItems === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih minimal satu item untuk membuat TR!'
                });
                return;
            }
            
            var formData = new FormData();
            formData.append('notes', $('#tr_notes').val());
            
            // Collect item IDs and types
            var itemIds = [];
            var itemTypes = [];
            
            $('.item-checkbox:checked').each(function() {
                itemIds.push($(this).val());
                itemTypes.push($(this).data('type'));
            });
            
            // Append arrays to FormData
            itemIds.forEach(function(id) {
                formData.append('item_ids[]', id);
            });
            
            itemTypes.forEach(function(type) {
                formData.append('item_types[]', type);
            });
            
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
            
            // Items List (pengajuan + settlement)
            html += '<h6>Daftar Item (' + trData.transaction_requests.length + ')</h6>';
            html += '<div class="table-responsive">';
            html += '<table class="table table-sm">';
            html += '<thead>';
            html += '<tr>';
            html += '<th>Type</th>';
            html += '<th>No. Transaksi</th>';
            html += '<th>Requester</th>';
            html += '<th>Kategori</th>';
            html += '<th>Nominal</th>';
            html += '<th>Status</th>';
            html += '<th>Aksi</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';
            
            var totalNominal = 0;
            trData.transaction_requests.forEach(function(tr) {
                var itemData = getItemData(tr);
                var statusBadge = getStatusBadge(tr.status);
                var actionButton = getActionButtonForItem(tr, itemData);
                
                html += '<tr>';
                html += '<td>' + itemData.typeBadge + '</td>';
                html += '<td>' + itemData.nomor + '</td>';
                html += '<td>' + itemData.requester + '</td>';
                html += '<td>' + itemData.kategori + '</td>';
                html += '<td>Rp ' + new Intl.NumberFormat('id-ID').format(itemData.nominal) + '</td>';
                html += '<td>' + statusBadge + '</td>';
                html += '<td>' + actionButton + '</td>';
                html += '</tr>';
                
                totalNominal += parseFloat(itemData.nominal);
            });
            
            html += '</tbody>';
            html += '<tfoot>';
            html += '<tr class="table-active">';
            html += '<th colspan="4">Total</th>';
            html += '<th>Rp ' + new Intl.NumberFormat('id-ID').format(totalNominal) + '</th>';
            html += '<th colspan="2"></th>';
            html += '</tr>';
            html += '</tfoot>';
            html += '</table>';
            html += '</div>';
            
            $('#trDetailContent').html(html);
        }
        
        function getItemData(tr) {
            var itemData = {};
            
            if (tr.pengajuan) {
                // Data dari pengajuan
                itemData = {
                    type: 'pengajuan',
                    typeBadge: '<span class="badge bg-primary">Pengajuan</span>',
                    nomor: tr.pengajuan.nomor_pengajuan,
                    requester: tr.pengajuan.requester.nama,
                    kategori: tr.pengajuan.kategori_pengajuan.nama,
                    nominal: tr.pengajuan.nominal_pengajuan,
                    id: tr.pengajuan.id
                };
            } else if (tr.settlement) {
                // Data dari settlement
                itemData = {
                    type: 'settlement',
                    typeBadge: '<span class="badge bg-warning">Settlement Over</span>',
                    nomor: tr.settlement.nomor_settlement,
                    requester: tr.settlement.pengajuan.requester.nama,
                    kategori: tr.settlement.pengajuan.kategori_pengajuan.nama,
                    nominal: Math.abs(tr.settlement.selisih),
                    id: tr.settlement.id
                };
            }
            
            return itemData;
        }
        
        function getActionButtonForItem(tr, itemData) {
            var buttons = '';
            
            // Button Detail Item (selalu tampil)
            if (itemData.type === 'pengajuan') {
                buttons += '<button class="btn btn-sm btn-outline-info me-1 detail-pengajuan-btn" data-pengajuan-id="' + itemData.id + '" title="Detail Pengajuan">';
                buttons += '<i class="feather icon-eye"></i> Detail';
                buttons += '</button>';
            } else if (itemData.type === 'settlement') {
                buttons += '<button class="btn btn-sm btn-outline-info me-1 detail-settlement-btn" data-settlement-id="' + itemData.id + '" title="Detail Settlement">';
                buttons += '<i class="feather icon-eye"></i> Detail';
                buttons += '</button>';
            }
            
            // Button Process/Download berdasarkan status
            if (tr.status === 'waiting') {
                if (itemData.type === 'pengajuan') {
                    buttons += '<button class="btn btn-sm btn-primary process-payment-btn" data-pengajuan-id="' + itemData.id + '" data-type="pengajuan" title="Proses Pembayaran">';
                } else {
                    buttons += '<button class="btn btn-sm btn-primary process-payment-btn" data-settlement-id="' + itemData.id + '" data-type="settlement" title="Proses Transfer Over">';
                }
                buttons += '<i class="feather icon-credit-card"></i> Proses';
                buttons += '</button>';
            } else if (tr.status === 'paid' && tr.bukti_transfer) {
                if (itemData.type === 'pengajuan') {
                    buttons += '<a href="/TransactionRequest/download-bukti-pengajuan/' + itemData.id + '" class="btn btn-sm btn-outline-primary" title="Download Bukti">';
                } else {
                    buttons += '<a href="/TransactionRequest/download-bukti-settlement/' + itemData.id + '" class="btn btn-sm btn-outline-primary" title="Download Bukti">';
                }
                buttons += '<i class="feather icon-download"></i> Download';
                buttons += '</a>';
            }
            
            return buttons;
        }
        
        function buildDetailPengajuanHTML(pengajuan) {
            var html = '';
            
            // Header Information
            html += '<div class="row mb-4">';
            html += '<div class="col-md-6">';
            html += '<div class="card border-primary">';
            html += '<div class="card-header bg-primary text-white">';
            html += '<h6 class="card-title mb-0"><i class="feather icon-file-text me-2"></i>Informasi Pengajuan</h6>';
            html += '</div>';
            html += '<div class="card-body">';
            html += '<table class="table table-sm table-borderless">';
            html += '<tr><td><strong>No. Pengajuan:</strong></td><td>' + pengajuan.nomor_pengajuan + '</td></tr>';
            html += '<tr><td><strong>Judul:</strong></td><td>' + (pengajuan.judul || '-') + '</td></tr>';
            html += '<tr><td><strong>Nominal:</strong></td><td><strong class="text-primary fw-bold">Rp ' + new Intl.NumberFormat('id-ID').format(pengajuan.nominal_pengajuan) + '</strong></td></tr>';
            html += '<tr><td><strong>Mata Uang:</strong></td><td>' + (pengajuan.mata_uang || 'IDR') + '</td></tr>';
            html += '<tr><td><strong>Status:</strong></td><td>' + getStatusPengajuanBadge(pengajuan.status_pengajuan) + '</td></tr>';
            html += '</table>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            
            // Requester Information
            html += '<div class="col-md-6">';
            html += '<div class="card border-info">';
            html += '<div class="card-header bg-info text-white">';
            html += '<h6 class="card-title mb-0"><i class="feather icon-user me-2"></i>Informasi Requester</h6>';
            html += '</div>';
            html += '<div class="card-body">';
            html += '<table class="table table-sm table-borderless">';
            html += '<tr><td><strong>Nama:</strong></td><td>' + pengajuan.requester.nama + '</td></tr>';
            html += '<tr><td><strong>NIK:</strong></td><td>' + (pengajuan.requester.nik || '-') + '</td></tr>';
            html += '<tr><td><strong>Department:</strong></td><td>' + (pengajuan.requester.department ? pengajuan.requester.department.nama : '-') + '</td></tr>';
            html += '<tr><td><strong>Email:</strong></td><td>' + (pengajuan.requester.email || '-') + '</td></tr>';
            html += '</table>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            
            // Timeline Information
            html += '<div class="row mb-4">';
            html += '<div class="col-12">';
            html += '<div class="card border-warning">';
            html += '<div class="card-header bg-warning text-dark">';
            html += '<h6 class="card-title mb-0"><i class="feather icon-clock me-2"></i>Timeline Pengajuan</h6>';
            html += '</div>';
            html += '<div class="card-body">';
            html += '<div class="row">';
            html += '<div class="col-md-4">';
            html += '<strong>Tanggal Pengajuan:</strong><br>';
            html += '<span class="text-muted">' + formatDate(pengajuan.tanggal_pengajuan) + '</span>';
            html += '</div>';
            html += '<div class="col-md-4">';
            html += '<strong>Tanggal Kebutuhan:</strong><br>';
            html += '<span class="text-muted">' + formatDate(pengajuan.tanggal_kebutuhan) + '</span>';
            html += '</div>';
            html += '<div class="col-md-4">';
            html += '<strong>Dibuat:</strong><br>';
            html += '<span class="text-muted">' + formatDateTime(pengajuan.created_at) + '</span>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            
            // Description
            if (pengajuan.deskripsi) {
                html += '<div class="row mb-4">';
                html += '<div class="col-12">';
                html += '<div class="card">';
                html += '<div class="card-header">';
                html += '<h6 class="card-title mb-0"><i class="feather icon-align-left me-2"></i>Deskripsi</h6>';
                html += '</div>';
                html += '<div class="card-body">';
                html += '<p class="mb-0">' + pengajuan.deskripsi.replace(/\n/g, '<br>') + '</p>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            }
            
            // Catatan Requester
            if (pengajuan.catatan_requester) {
                html += '<div class="row mb-4">';
                html += '<div class="col-12">';
                html += '<div class="card">';
                html += '<div class="card-header">';
                html += '<h6 class="card-title mb-0"><i class="feather icon-message-square me-2"></i>Catatan Requester</h6>';
                html += '</div>';
                html += '<div class="card-body">';
                html += '<p class="mb-0">' + pengajuan.catatan_requester.replace(/\n/g, '<br>') + '</p>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            }
            
            // Detail Form Fields
            if (pengajuan.detail_pengajuan && pengajuan.detail_pengajuan.length > 0) {
                html += '<div class="row mb-4">';
                html += '<div class="col-12">';
                html += '<div class="card border-success">';
                html += '<div class="card-header bg-success text-white">';
                html += '<h6 class="card-title mb-0"><i class="feather icon-list me-2"></i>Detail Form Pengajuan</h6>';
                html += '</div>';
                html += '<div class="card-body">';
                html += '<div class="table-responsive">';
                html += '<table class="table table-sm table-hover">';
                html += '<thead class="table-light">';
                html += '<tr><th>Field</th><th>Nilai</th></tr>';
                html += '</thead>';
                html += '<tbody>';
                
                pengajuan.detail_pengajuan.forEach(function(detail) {
                    html += '<tr>';
                    html += '<td><strong>' + detail.form_field.label + '</strong></td>';
                    html += '<td>' + formatFieldValue(detail.nilai, detail.form_field.tipe_field) + '</td>';
                    html += '</tr>';
                });
                
                html += '</tbody>';
                html += '</table>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            }
            
            // File Pendukung
            if (pengajuan.file_pendukung && pengajuan.file_pendukung.length > 0) {
                html += '<div class="row mb-4">';
                html += '<div class="col-12">';
                html += '<div class="card border-secondary">';
                html += '<div class="card-header bg-secondary text-white">';
                html += '<h6 class="card-title mb-0"><i class="feather icon-paperclip me-2"></i>File Pendukung</h6>';
                html += '</div>';
                html += '<div class="card-body">';
                html += '<div class="row">';
                
                pengajuan.file_pendukung.forEach(function(file, index) {
                    html += '<div class="col-md-6 mb-2">';
                    html += '<div class="d-flex align-items-center p-2 border rounded">';
                    html += '<i class="feather icon-file me-2"></i>';
                    html += '<span class="me-auto">' + file + '</span>';
                    html += '<a href="/storage/assets/pengajuan/' + pengajuan.requester.nama + '/' + file + '" target="_blank" class="btn btn-sm btn-outline-primary">';
                    html += '<i class="feather icon-external-link"></i>';
                    html += '</a>';
                    html += '</div>';
                    html += '</div>';
                });
                
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
            }
            
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
                case 'pending':
                    return '<span class="badge bg-warning">Pending</span>';
                case 'approved':
                    return '<span class="text-primary fw-bold"><strong>Disetujui<strong></span>';
                case 'rejected':
                    return '<span class="badge bg-danger">Rejected</span>';
                case 'completed':
                    return '<span class="badge bg-info">Completed</span>';
                case 'settlement_created':
                    return '<span class="badge bg-primary">Settlement Created</span>';
                default:
                    return '<span class="badge bg-secondary">' + status + '</span>';
            }
        }
        
        function formatDate(dateString) {
            if (!dateString) return '-';
            return new Date(dateString).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
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
        function getActionButton(tr, pengajuan) {
            var buttons = '';
            
            // Button Detail Pengajuan (selalu tampil)
            buttons += '<button class="btn btn-sm btn-outline-info me-1 detail-pengajuan-btn" data-pengajuan-id="' + pengajuan.id + '" title="Detail Pengajuan">';
            buttons += '<i class="feather icon-eye"></i> Detail';
            buttons += '</button>';
            
            // Button Process/Download berdasarkan status
            if (tr.status === 'waiting') {
                buttons += '<button class="btn btn-sm btn-primary process-payment-btn" data-pengajuan-id="' + pengajuan.id + '" title="Proses Pembayaran">';
                buttons += '<i class="feather icon-credit-card"></i> Proses';
                buttons += '</button>';
            } else if (tr.status === 'paid' && tr.bukti_transfer) {
                buttons += '<a href="/TransactionRequest/download-bukti-pengajuan/' + pengajuan.id + '" class="btn btn-sm btn-outline-primary" title="Download Bukti">';
                buttons += '<i class="feather icon-download"></i> Download';
                buttons += '</a>';
            }
            
            return buttons;
        }
        
        
        $(document).on('click', '.detail-settlement-btn', function() {
    var settlementId = $(this).data('settlement-id');
    loadDetailSettlement(settlementId);
});

// Function untuk load detail settlement
function loadDetailSettlement(settlementId) {
    $.ajax({
        url: '/TransactionRequest/detail-settlement/' + settlementId,
        type: 'GET',
        beforeSend: function() {
            $('#detailPengajuanContent').html('<div class="text-center py-4"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
            $('#detailPengajuanModal').modal('show');
        },
        success: function(response) {
            if (response.success) {
                buildDetailSettlementHTML(response.data);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
                $('#detailPengajuanModal').modal('hide');
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal mengambil detail settlement'
            });
            $('#detailPengajuanModal').modal('hide');
        }
    });
}

// Function untuk build HTML detail settlement
function buildDetailSettlementHTML(settlement) {
    var html = '';
    
    // Header Information
    html += '<div class="row mb-4">';
    html += '<div class="col-md-6">';
    html += '<div class="card border-warning">';
    html += '<div class="card-header bg-warning text-dark">';
    html += '<h6 class="card-title mb-0"><i class="feather icon-file-text me-2"></i>Informasi Settlement</h6>';
    html += '</div>';
    html += '<div class="card-body">';
    html += '<table class="table table-sm table-borderless">';
    html += '<tr><td><strong>No. Settlement:</strong></td><td>' + settlement.nomor_settlement + '</td></tr>';
    html += '<tr><td><strong>Tanggal Settlement:</strong></td><td>' + formatDate(settlement.tanggal_settlement) + '</td></tr>';
    html += '<tr><td><strong>Total Actual:</strong></td><td><strong class="text-info">Rp ' + new Intl.NumberFormat('id-ID').format(settlement.total_actual) + '</strong></td></tr>';
    html += '<tr><td><strong>Selisih Over:</strong></td><td><strong class="text-danger">Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(settlement.selisih)) + '</strong></td></tr>';
    html += '<tr><td><strong>Status Settlement:</strong></td><td>' + getStatusSettlementBadge(settlement.status_settlement) + '</td></tr>';
    html += '<tr><td><strong>Status Realisasi:</strong></td><td>' + getStatusRealisasiBadge(settlement.status_realisasi) + '</td></tr>';
    html += '</table>';
    html += '</div>';
    html += '</div>';
    html += '</div>';
    
    // Pengajuan Asal Information
    html += '<div class="col-md-6">';
    html += '<div class="card border-primary">';
    html += '<div class="card-header bg-primary text-white">';
    html += '<h6 class="card-title mb-0"><i class="feather icon-file me-2"></i>Pengajuan Asal</h6>';
    html += '</div>';
    html += '<div class="card-body">';
    html += '<table class="table table-sm table-borderless">';
    html += '<tr><td><strong>No. Pengajuan:</strong></td><td>' + settlement.pengajuan.nomor_pengajuan + '</td></tr>';
    html += '<tr><td><strong>Requester:</strong></td><td>' + settlement.pengajuan.requester.nama + '</td></tr>';
    html += '<tr><td><strong>Department:</strong></td><td>' + (settlement.pengajuan.requester.department ? settlement.pengajuan.requester.department.nama : '-') + '</td></tr>';
    html += '<tr><td><strong>Kategori:</strong></td><td>' + settlement.pengajuan.kategori_pengajuan.nama + '</td></tr>';
    html += '<tr><td><strong>Nominal Awal:</strong></td><td><strong class="text-success">Rp ' + new Intl.NumberFormat('id-ID').format(settlement.pengajuan.nominal_pengajuan) + '</strong></td></tr>';
    html += '</table>';
    html += '</div>';
    html += '</div>';
    html += '</div>';
    html += '</div>';
    
    // Catatan Settlement
    if (settlement.catatan_settlement) {
        html += '<div class="row mb-4">';
        html += '<div class="col-12">';
        html += '<div class="card">';
        html += '<div class="card-header">';
        html += '<h6 class="card-title mb-0"><i class="feather icon-message-square me-2"></i>Catatan Settlement</h6>';
        html += '</div>';
        html += '<div class="card-body">';
        html += '<p class="mb-0">' + settlement.catatan_settlement.replace(/\n/g, '<br>') + '</p>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
    }
    
    // Detail Settlement Items
    if (settlement.details && settlement.details.length > 0) {
        html += '<div class="row mb-4">';
        html += '<div class="col-12">';
        html += '<div class="card border-success">';
        html += '<div class="card-header bg-success text-white">';
        html += '<h6 class="card-title mb-0"><i class="feather icon-list me-2"></i>Detail Settlement</h6>';
        html += '</div>';
        html += '<div class="card-body">';
        html += '<div class="table-responsive">';
        html += '<table class="table table-sm table-hover">';
        html += '<thead class="table-light">';
        html += '<tr><th>Keterangan</th><th>Nominal Budget</th><th>Nominal Actual</th><th>Selisih</th></tr>';
        html += '</thead>';
        html += '<tbody>';
        
        settlement.details.forEach(function(detail) {
            var selisih = detail.nominal_actual - detail.nominal_budget;
            var selisihClass = selisih > 0 ? 'text-danger' : (selisih < 0 ? 'text-success' : 'text-muted');
            
            html += '<tr>';
            html += '<td>' + detail.keterangan + '</td>';
            html += '<td>Rp ' + new Intl.NumberFormat('id-ID').format(detail.nominal_budget) + '</td>';
            html += '<td>Rp ' + new Intl.NumberFormat('id-ID').format(detail.nominal_actual) + '</td>';
            html += '<td class="' + selisihClass + '">Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(selisih)) + '</td>';
            html += '</tr>';
        });
        
        html += '</tbody>';
        html += '</table>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
    }
    
    // File Bukti
    if (settlement.file_bukti && settlement.file_bukti.length > 0) {
        html += '<div class="row mb-4">';
        html += '<div class="col-12">';
        html += '<div class="card border-secondary">';
        html += '<div class="card-header bg-secondary text-white">';
        html += '<h6 class="card-title mb-0"><i class="feather icon-paperclip me-2"></i>File Bukti Settlement</h6>';
        html += '</div>';
        html += '<div class="card-body">';
        html += '<div class="row">';
        
        settlement.file_bukti.forEach(function(file, index) {
            html += '<div class="col-md-6 mb-2">';
            html += '<div class="d-flex align-items-center p-2 border rounded">';
            html += '<i class="feather icon-file me-2"></i>';
            html += '<span class="me-auto">' + file + '</span>';
            html += '<a href="/storage/assets/settlement/' + settlement.pengajuan.requester.nama + '/' + file + '" target="_blank" class="btn btn-sm btn-outline-primary">';
            html += '<i class="feather icon-external-link"></i>';
            html += '</a>';
            html += '</div>';
            html += '</div>';
        });
        
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
    }
    
    $('#detailPengajuanContent').html(html);
}

// Function untuk status settlement badge
function getStatusSettlementBadge(status) {
    switch(status) {
        case 'draft':
            return '<span class="badge bg-secondary">Draft</span>';
        case 'approved':
            return '<span class="badge bg-success">Approved</span>';
        case 'proses':
            return '<span class="badge bg-info">Proses</span>';
        case 'revisi':
            return '<span class="badge bg-warning">Revisi</span>';
        default:
            return '<span class="badge bg-secondary">' + status + '</span>';
    }
}

// Function untuk status realisasi badge
function getStatusRealisasiBadge(status) {
    switch(status) {
        case 'balance':
            return '<span class="badge bg-success">Balance</span>';
        case 'over':
            return '<span class="badge bg-danger">Over Budget</span>';
        case 'under':
            return '<span class="badge bg-warning">Under Budget</span>';
        case 'transferred':
            return '<span class="badge bg-info">Transferred</span>';
        case 'proses':
            return '<span class="badge bg-primary">Proses</span>';
        default:
            return '<span class="badge bg-secondary">' + status + '</span>';
    }
}

// Update function openProcessPaymentModal untuk handle settlement
function openProcessPaymentModal(itemId, itemType = 'pengajuan') {
    if (itemType === 'pengajuan') {
        $('#pengajuan_id').val(itemId);
    } else {
        $('#pengajuan_id').val(itemId); // Menggunakan field yang sama untuk settlement_id
    }
    
    // Reset form
    $('#processPaymentForm')[0].reset();
    $('#payment_fields').hide();
    $('#bukti_transfer').prop('required', false);
    $('#tanggal_transfer').prop('required', false);
    
    // Load detail item berdasarkan type
    var url = itemType === 'pengajuan' ? 
              '/TransactionRequest/detail-pengajuan/' + itemId :
              '/TransactionRequest/detail-settlement/' + itemId;
    
    $.ajax({
        url: url,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var item = response.data;
                var detailHtml = '';
                
                if (itemType === 'pengajuan') {
                    detailHtml = '<strong>No. Pengajuan:</strong> ' + item.nomor_pengajuan + '<br>';
                    detailHtml += '<strong>Requester:</strong> ' + item.requester.nama + '<br>';
                    detailHtml += '<strong>Kategori:</strong> ' + item.kategori_pengajuan.nama + '<br>';
                    detailHtml += '<strong>Nominal:</strong> Rp ' + new Intl.NumberFormat('id-ID').format(item.nominal_pengajuan);
                } else {
                    detailHtml = '<strong>No. Settlement:</strong> ' + item.nomor_settlement + '<br>';
                    detailHtml += '<strong>Requester:</strong> ' + item.pengajuan.requester.nama + '<br>';
                    detailHtml += '<strong>Kategori:</strong> ' + item.pengajuan.kategori_pengajuan.nama + '<br>';
                    detailHtml += '<strong>Selisih Over:</strong> Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(item.selisih)) + '<br>';
                    detailHtml += '<small class="text-muted">Transfer kelebihan budget kepada karyawan</small>';
                }
                
                $('#pengajuanDetail').html(detailHtml);
                
                // Update modal title berdasarkan type
                var modalTitle = itemType === 'pengajuan' ? 'Proses Pembayaran' : 'Proses Transfer Over Budget';
                $('#processPaymentModalLabel').text(modalTitle);
                
                $('#processPaymentModal').modal('show');
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal mengambil detail ' + (itemType === 'pengajuan' ? 'pengajuan' : 'settlement')
            });
        }
    });
}

// Update event handler untuk process payment button
$(document).on('click', '.process-payment-btn', function() {
    var itemId, itemType;
    
    if ($(this).data('pengajuan-id')) {
        itemId = $(this).data('pengajuan-id');
        itemType = 'pengajuan';
    } else if ($(this).data('settlement-id')) {
        itemId = $(this).data('settlement-id');
        itemType = 'settlement';
    } else {
        itemType = $(this).data('type') || 'pengajuan';
        itemId = itemType === 'pengajuan' ? $(this).data('pengajuan-id') : $(this).data('settlement-id');
    }
    
    openProcessPaymentModal(itemId, itemType);
});

// Update bagian table display untuk menampilkan tipe item
function updateTableDisplay(trData) {
    // Update hitungan untuk display
    var pengajuanCount = 0;
    var settlementCount = 0;
    
    trData.transaction_requests.forEach(function(tr) {
        if (tr.pengajuan) {
            pengajuanCount++;
        } else if (tr.settlement) {
            settlementCount++;
        }
    });
    
    // Update badge count display
    var countText = '';
    if (pengajuanCount > 0 && settlementCount > 0) {
        countText = pengajuanCount + ' Pengajuan, ' + settlementCount + ' Settlement';
    } else if (pengajuanCount > 0) {
        countText = pengajuanCount + ' Pengajuan';
    } else if (settlementCount > 0) {
        countText = settlementCount + ' Settlement';
    } else {
        countText = '0 Item';
    }
    
    return countText;
}

// Function untuk update badge count di table utama
function updateTRTableBadge(trGroupId, countText) {
    // Find the row and update badge
    $('button[data-id="' + trGroupId + '"]').closest('tr').find('.badge-light-info').text(countText);
}
        
        // Process Individual Payment Button Click
        $(document).on('click', '.process-payment-btn', function() {
            var pengajuanId = $(this).data('pengajuan-id');
            openProcessPaymentModal(pengajuanId);
        });
        
        $(document).on('click', '.detail-pengajuan-btn', function() {
            var pengajuanId = $(this).data('pengajuan-id');
            loadDetailPengajuan(pengajuanId);
        });
        
        // Function to open process payment modal
        function openProcessPaymentModal(pengajuanId) {
            $('#pengajuan_id').val(pengajuanId);
            
            // Reset form
            $('#processPaymentForm')[0].reset();
            $('#payment_fields').hide();
            $('#bukti_transfer').prop('required', false);
            $('#tanggal_transfer').prop('required', false);
            
            // Load detail pengajuan
            $.ajax({
                url: '/TransactionRequest/detail-pengajuan/' + pengajuanId,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var pengajuan = response.data;
                        var detailHtml = '<strong>No. Pengajuan:</strong> ' + pengajuan.nomor_pengajuan + '<br>';
                        detailHtml += '<strong>Requester:</strong> ' + pengajuan.requester.nama + '<br>';
                        detailHtml += '<strong>Kategori:</strong> ' + pengajuan.kategori_pengajuan.nama + '<br>';
                        detailHtml += '<strong>Nominal:</strong> Rp ' + new Intl.NumberFormat('id-ID').format(pengajuan.nominal_pengajuan);
                        
                        $('#pengajuanDetail').html(detailHtml);
                        $('#processPaymentModal').modal('show');
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal mengambil detail pengajuan'
                    });
                }
            });
        }
        
        // funtion untuk melihat detail pengajuan
        function loadDetailPengajuan(pengajuanId) {
            $.ajax({
                url: '/TransactionRequest/detail-pengajuan-full/' + pengajuanId,
                type: 'GET',
                beforeSend: function() {
                    $('#detailPengajuanContent').html('<div class="text-center py-4"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
                    $('#detailPengajuanModal').modal('show');
                },
                success: function(response) {
                    if (response.success) {
                        buildDetailPengajuanHTML(response.data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                        $('#detailPengajuanModal').modal('hide');
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal mengambil detail pengajuan'
                    });
                    $('#detailPengajuanModal').modal('hide');
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
            
            var pengajuanId = $('#pengajuan_id').val();
            var formData = new FormData(this);
            
            $.ajax({
                url: '/TransactionRequest/' + pengajuanId + '/update-status',
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
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Status pembayaran berhasil diperbarui!',
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