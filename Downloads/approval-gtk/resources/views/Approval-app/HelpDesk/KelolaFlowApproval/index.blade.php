@extends('Approval-app.Layout.main-admin')
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .drag-handle {
            cursor: grab;
        }
        .drag-handle:active {
            cursor: grabbing;
        }
        .flow-item {
            border: 2px dashed transparent;
            transition: all 0.3s ease;
        }
        /*.flow-item:hover {*/
        /*    border-color: #0d6efd;*/
        /*}*/
        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .card {
            box-shadow: 0 0px 0px 0px rgba(69, 90, 100, 0.3);
        }
    </style>
@endsection

@section('content')

<div class="row">
    <!-- Filter Section -->
    <div class="col-12 mb-4">
        <div class="page-header mb-0">
            <div class="page-block bg-white rounded shadow">
                <div class="row align-items-center p-3">
                    <div class="col-md-8">
                        <div class="page-header-title">
                            <h4 class="mb-3 fw-bold">
                                Kelola Flow Approval
                            </h4>
                            <p class="mb-0 opacity-75">Konfigurasi dan atur ulang alur persetujuan untuk setiap kategori pengajuan</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFlowModal">
                            <i class="fas fa-plus me-2"></i>Tambah Flow
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Kategori Pengajuan</label>
                        <select id="filterKategori" class="form-select">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Department</label>
                        <select id="filterDepartment" class="form-select">
                            <option value="">-- Semua Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button id="btnFilter" class="btn btn-info me-2">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <button id="btnReset" class="btn btn-outline-secondary">
                            <i class="fas fa-refresh me-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flow List Section -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Flow Approval</h5>
            </div>
            <div class="card-body">
                <div id="flowContainer">
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Pilih kategori untuk melihat flow approval</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Flow -->
<div class="modal fade" id="addFlowModal" tabindex="-1">
    <div class="modal-dialog modal-md  modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Flow Approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addFlowForm">
                    <div class="row mb-3">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Kategori Pengajuan <span class="text-danger">*</span></label>
                            <select name="kategori_pengajuan_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Departemen <span class="text-danger">*</span></label>
                            <select name="department_id" id="departmentSelect" class="form-select" required>
                                <option value="">-- Pilih Departemen --</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Requester <span class="text-danger">*</span></label>
                            <select name="requester_id" id="requesterSelect" class="form-select" required>
                                <option value="">-- Pilih Requester --</option>
                            </select>
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Layer Approval</h6>
                            <button type="button" class="btn btn-sm btn-success" id="addStep">
                                <i class="fas fa-plus me-1"></i>Tambah Step
                            </button>
                        </div>

                        <div id="stepsContainer">
                            <!-- Steps will be added here dynamically -->
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="addFlowForm" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Flow -->
<div class="modal fade" id="editFlowModal" tabindex="-1">
    <div class="modal-dialog modal-xl  modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Flow Approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editFlowForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Kategori Pengajuan</label>
                            <input type="text" class="form-control" id="editKategoriName" readonly>
                            <input type="hidden" id="editKategoriId">
                            <input type="hidden" id="editRequesterId">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Requester</label>
                            <input type="text" class="form-control" id="editRequesterName" readonly>
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Steps Approval</h6>
                            <button type="button" class="btn btn-sm btn-success" id="addEditStep">
                                <i class="fas fa-plus me-1"></i>Tambah Step
                            </button>
                        </div>

                        <div id="editStepsContainer">
                            <!-- Steps will be loaded here -->
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="editFlowForm" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    let stepCounter = 0;
    let editStepCounter = 0;
    let karyawanData = @json($karyawans);
    
    $(document).ready(function() {
        // Filter functionality
        $('#btnFilter').click(function() {
            loadFlows();
        });
    
        $('#btnReset').click(function() {
            $('#filterKategori').val('').trigger('change');
            $('#filterDepartment').val('');
            $('#flowContainer').html(`
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Pilih kategori untuk melihat flow approval</p>
                </div>
            `);
        });
    
        // Add step functionality
        $('#addStep').click(function() {
            addStep('stepsContainer');
        });
    
        $('#addEditStep').click(function() {
            addStep('editStepsContainer', true);
        });
    
        // Form submissions
        $('#addFlowForm').submit(function(e) {
            e.preventDefault();
            saveFlow();
        });
    
        $('#editFlowForm').submit(function(e) {
            e.preventDefault();
            updateFlow();
        });
    
        // Add first step by default
        addStep('stepsContainer');
    });
    
    function loadFlows() {
        const kategoriId = $('#filterKategori').val();
        const departmentId = $('#filterDepartment').val(); // Tambahkan ini
        
        // if (!kategoriId) {
        //     alert('Pilih kategori terlebih dahulu');
        //     return;
        // }
    
        // Modifikasi URL untuk menyertakan parameter department
        let url = `/kelola-flow-approval/kategori/${kategoriId}`;
        if (departmentId) {
            url += `?department_id=${departmentId}`;
        }
    
        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                displayFlows(data);
            },
            error: function() {
                alert('Gagal memuat data flow');
            }
        });
    }
    
    function displayFlows(flows) {
        let html = '';
        
        if (Object.keys(flows).length === 0) {
            html = `
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada flow approval untuk kategori ini</p>
                </div>
            `;
        } else {
            Object.keys(flows).forEach(requesterId => {
                const requesterFlows = flows[requesterId];
                const requester = requesterFlows[0].requester;
                
                html += `
                    <div class="card border mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">${requester.nama}</h5>
                                <h6 class="text-muted">${requester.jabatan} - ${requester.department?.nama || ''}</h6>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-warning me-2" onclick="editFlow('${requester.kategori_pengajuan_id}', '${requesterId}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteFlow('${requester.kategori_pengajuan_id}', '${requesterId}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                `;
                
                requesterFlows.forEach((flow, index) => {
                    html += `
                        <div class="col-md-2 mb-2">
                            <div class="d-flex align-items-center">
                                <div class="step-number bg-primary text-white me-2">${flow.urutan}</div>
                                <div>
                                    <small class="text-muted">${flow.approver.nama}</small>
                                    <br><small class="text-muted">${flow.approver.jabatan}</small>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    if (index < requesterFlows.length - 1) {
                        html += `
                            <div class="col-auto d-flex align-items-center">
                                <i class="fas fa-arrow-right text-primary"></i>
                            </div>
                        `;
                    }
                });
                
                html += `
                            </div>
                        </div>
                    </div>
                `;
            });
        }
        
        $('#flowContainer').html(html);
    }
    
    function addStep(containerId, isEdit = false) {
        const counter = isEdit ? ++editStepCounter : ++stepCounter;
        const prefix = isEdit ? 'edit_' : '';
        
        const stepHtml = `
            <div class="step-item mb-3 p-3 border rounded" data-step="${counter}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">
                        <i class="fas fa-bars drag-handle me-2"></i>
                        Step ${counter}
                    </h6>
                    <button type="button" class="btn btn-sm btn-danger remove-step">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="row">
                    
                    <div class="col-12">
                        <label class="form-label">Approver <span class="text-danger">*</span></label>
                        <select name="${prefix}flows[${counter-1}][approver_id]" class="form-select" required>
                            <option value="">-- Pilih Approver --</option>
                        </select>
                    </div>
                    
                </div>
            </div>
        `;
        
        $(`#${containerId}`).append(stepHtml);
        populateApprovers(`${prefix}flows\\[${counter-1}\\]\\[approver_id\\]`);
        
        // Remove step functionality
        $(`.step-item[data-step="${counter}"] .remove-step`).click(function() {
            $(this).closest('.step-item').remove();
            reorderSteps(containerId, isEdit);
        });
    }
    
    function populateApprovers(selectName) {
        let options = '<option value="">-- Pilih Approver --</option>';
        karyawanData.forEach(function(karyawan) {
            options += `<option value="${karyawan.id}">${karyawan.nama} - ${karyawan.jabatan} (${karyawan.department?.nama || ''})</option>`;
        });
        
        $(`select[name="${selectName}"]`).html(options);
    }
    
    function reorderSteps(containerId, isEdit = false) {
        const prefix = isEdit ? 'edit_' : '';
        
        $(`#${containerId} .step-item`).each(function(index) {
            $(this).find('h6').html(`<i class="fas fa-bars drag-handle me-2"></i>Step ${index + 1}`);
            
            // Update form field names
            $(this).find(`input[name*="${prefix}flows"]`).each(function() {
                const name = $(this).attr('name');
                const newName = name.replace(/\[\d+\]/, `[${index}]`);
                $(this).attr('name', newName);
            });
            
            $(this).find(`select[name*="${prefix}flows"]`).each(function() {
                const name = $(this).attr('name');
                const newName = name.replace(/\[\d+\]/, `[${index}]`);
                $(this).attr('name', newName);
            });
        });
    }
    
    function saveFlow() {
        const formData = new FormData($('#addFlowForm')[0]);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            if (key.includes('flows[')) {
                if (!data.flows) data.flows = [];
                
                const match = key.match(/flows\[(\d+)\]\[(\w+)\]/);
                if (match) {
                    const index = parseInt(match[1]);
                    const field = match[2];
                    
                    if (!data.flows[index]) data.flows[index] = {};
                    data.flows[index][field] = value;
                }
            } else {
                data[key] = value;
            }
        }
    
        $.ajax({
            url: '{{ route("kelola-flow-approval.store") }}',
            method: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert('Flow approval berhasil disimpan');
                    $('#addFlowModal').modal('hide');
                    $('#addFlowForm')[0].reset();
                    $('#stepsContainer').empty();
                    stepCounter = 0;
                    addStep('stepsContainer');
                    loadFlows();
                }
            },
            error: function(xhr) {
                alert('Gagal menyimpan flow: ' + xhr.responseJSON.message);
            }
        });
    }
    
    function editFlow(kategoriId, requesterId) {
        // Load existing flow data
        $.ajax({
            url: `/kelola-flow-approval/kategori/${kategoriId}`,
            method: 'GET',
            success: function(data) {
                if (data[requesterId]) {
                    populateEditForm(data[requesterId]);
                    $('#editFlowModal').modal('show');
                }
            }
        });
    }
    
    function populateEditForm(flows) {
        const firstFlow = flows[0];
        
        $('#editKategoriId').val(firstFlow.kategori_pengajuan_id);
        $('#editRequesterId').val(firstFlow.requester_id);
        $('#editKategoriName').val(firstFlow.kategori_pengajuan?.nama_kategori || '');
        $('#editRequesterName').val(`${firstFlow.requester.nama} - ${firstFlow.requester.jabatan}`);
        
        // Clear existing steps
        $('#editStepsContainer').empty();
        editStepCounter = 0;
        
        // Add steps
        flows.forEach(function(flow, index) {
            addStep('editStepsContainer', true);
            
            const stepContainer = $('#editStepsContainer .step-item').last();
            stepContainer.find('input[name*="nama_step"]').val(flow.nama_step);
            stepContainer.find('select[name*="approver_id"]').val(flow.approver_id);
            stepContainer.find('input[name*="deskripsi"]').val(flow.deskripsi || '');
        });
    }
    
    function updateFlow() {
        const formData = new FormData($('#editFlowForm')[0]);
        const data = {};
        
        // Get kategori and requester IDs
        data.kategori_id = $('#editKategoriId').val();
        data.requester_id = $('#editRequesterId').val();
        
        for (let [key, value] of formData.entries()) {
            if (key.includes('edit_flows[')) {
                if (!data.flows) data.flows = [];
                
                const match = key.match(/edit_flows\[(\d+)\]\[(\w+)\]/);
                if (match) {
                    const index = parseInt(match[1]);
                    const field = match[2];
                    
                    if (!data.flows[index]) data.flows[index] = {};
                    data.flows[index][field] = value;
                }
            }
        }
    
        $.ajax({
            url: `{{ url('/kelola-flow-approval') }}/${data.kategori_id}/${data.requester_id}`,
            method: 'PUT',
            data: data,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert('Flow approval berhasil diperbarui');
                    $('#editFlowModal').modal('hide');
                    loadFlows();
                }
            },
            error: function(xhr) {
                alert('Gagal memperbarui flow: ' + xhr.responseJSON.message);
            }
        });
    }
    
    function deleteFlow(kategoriId, requesterId) {
        if (confirm('Apakah Anda yakin ingin menghapus flow ini?')) {
            $.ajax({
                url: `{{ url('/kelola-flow-approval') }}/${kategoriId}/${requesterId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert('Flow approval berhasil dihapus');
                        loadFlows();
                    }
                },
                error: function() {
                    alert('Gagal menghapus flow');
                }
            });
        }
    }
</script>

<script>
    $(document).ready(function() {
        $('#departmentSelect').change(function() {
            const departmentId = $(this).val();
            const requesterSelect = $('#requesterSelect');
            
            // Reset requester dropdown
            requesterSelect.html('<option value="">-- Pilih Requester --</option>');
            
            if (departmentId) {
                $.get(`/get-karyawan-by-department/${departmentId}`)
                    .done(function(data) {
                        data.forEach(function(karyawan) {
                            const departmentName = karyawan.department ? karyawan.department.nama : '';
                            requesterSelect.append(
                                `<option value="${karyawan.id}">
                                    ${karyawan.nama} - ${karyawan.jabatan} (${departmentName})
                                </option>`
                            );
                        });
                    })
                    .fail(function() {
                        alert('Gagal mengambil data karyawan');
                    });
            }
        });
    });
</script>
@endsection