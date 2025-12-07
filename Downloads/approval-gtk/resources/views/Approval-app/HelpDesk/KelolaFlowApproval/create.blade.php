@extends('Approval-app.Layout.main-admin')
@section('head')
<style>
.flow-builder {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
    background: #f8f9fa;
}
.flow-step-item {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    position: relative;
}
.flow-step-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.step-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}
.step-number {
    background: #007bff;
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 10px;
}
.remove-step {
    position: absolute;
    top: 10px;
    right: 10px;
    color: #dc3545;
    cursor: pointer;
    font-size: 18px;
}
.remove-step:hover {
    color: #c82333;
}
.drag-handle {
    cursor: move;
    color: #6c757d;
    margin-right: 10px;
}
.drag-handle:hover {
    color: #495057;
}
.add-step-btn {
    border: 2px dashed #007bff;
    background: transparent;
    color: #007bff;
    padding: 15px;
    border-radius: 8px;
    width: 100%;
    transition: all 0.3s ease;
}
.add-step-btn:hover {
    background: #007bff;
    color: white;
}
.template-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}
.template-btn {
    padding: 8px 16px;
    border: 1px solid #dee2e6;
    background: white;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.template-btn:hover {
    background: #e9ecef;
    border-color: #adb5bd;
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
                    <h5 class="m-b-10">{{ isset($flows) ? 'Edit' : 'Buat' }} Flow Approval</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.flow-approval.index') }}">Flow Approval</a></li>
                    <li class="breadcrumb-item active">{{ isset($flows) ? 'Edit' : 'Buat' }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Konfigurasi Flow Approval</h5>
                <small class="text-muted">Atur urutan approval untuk setiap kategori pengajuan</small>
            </div>
            <div class="card-body">
                <form id="flowApprovalForm">
                    @csrf
                    
                    <!-- Pilih Kategori -->
                    <div class="form-group">
                        <label for="kategori_pengajuan_id">Kategori Pengajuan <span class="text-danger">*</span></label>
                        <select name="kategori_pengajuan_id" id="kategori_pengajuan_id" class="form-control" required>
                            <option value="">Pilih kategori pengajuan...</option>
                            
                        </select>
                    </div>

                    <!-- Template Buttons -->
                    <div class="form-group">
                        <label>Template Flow (Opsional)</label>
                        <div class="template-buttons">
                            <button type="button" class="template-btn" onclick="loadTemplate('sales')">
                                <i class="feather icon-users"></i> Sales Standard
                            </button>
                            <button type="button" class="template-btn" onclick="loadTemplate('marketing')">
                                <i class="feather icon-trending-up"></i> Marketing Standard
                            </button>
                            <button type="button" class="template-btn" onclick="loadTemplate('finance')">
                                <i class="feather icon-dollar-sign"></i> Finance Heavy
                            </button>
                            <button type="button" class="template-btn" onclick="loadTemplate('simple')">
                                <i class="feather icon-zap"></i> Simple Flow
                            </button>
                        </div>
                        <small class="text-muted">Klik template untuk memuat flow yang sudah disediakan, atau buat manual di bawah</small>
                    </div>

                    <!-- Flow Builder -->
                    <div class="form-group">
                        <label>Flow Approval Steps</label>
                        <div class="flow-builder">
                            <div id="flowSteps">
                                @if(isset($flows) && $flows->count() > 0)
                                    @foreach($flows as $index => $flow)
                                    <div class="flow-step-item" data-index="{{ $index }}">
                                        <i class="feather icon-x remove-step" onclick="removeStep(this)"></i>
                                        <div class="step-header">
                                            <i class="feather icon-menu drag-handle"></i>
                                            <div class="step-number">{{ $index + 1 }}</div>
                                            <h6 class="mb-0">Step {{ $index + 1 }}</h6>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Department</label>
                                                    <select name="flows[{{ $index }}][department_id]" class="form-control department-select" required>
                                                        <option value="">Pilih department...</option>
                                                        @foreach($departments as $dept)
                                                        <option value="{{ $dept->id }}" 
                                                            {{ $flow->department_id == $dept->id ? 'selected' : '' }}>
                                                            {{ $dept->nama }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Role Level</label>
                                                    <select name="flows[{{ $index }}][role_level_id]" class="form-control role-select" required>
                                                        <option value="">Pilih role level...</option>
                                                        @foreach($roleLevels as $role)
                                                        <option value="{{ $role->id }}" 
                                                            data-department="{{ $role->department_id }}"
                                                            {{ $flow->role_level_id == $role->id ? 'selected' : '' }}>
                                                            {{ $role->nama }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Nama Step</label>
                                                    <input type="text" name="flows[{{ $index }}][nama_step]" 
                                                           class="form-control" value="{{ $flow->nama_step }}" 
                                                           placeholder="contoh: Review oleh Supervisor Sales" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Deskripsi (Opsional)</label>
                                            <textarea name="flows[{{ $index }}][deskripsi]" class="form-control" rows="2" 
                                                      placeholder="Deskripsi detail tentang step ini...">{{ $flow->deskripsi }}</textarea>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <!-- Default empty step -->
                                    <div class="flow-step-item" data-index="0">
                                        <i class="feather icon-x remove-step" onclick="removeStep(this)"></i>
                                        <div class="step-header">
                                            <i class="feather icon-menu drag-handle"></i>
                                            <div class="step-number">1</div>
                                            <h6 class="mb-0">Step 1</h6>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Department</label>
                                                    <select name="flows[0][department_id]" class="form-control department-select" required>
                                                        <option value="">Pilih department...</option>
                                                        @foreach($departments as $dept)
                                                        <option value="{{ $dept->id }}">{{ $dept->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Role Level</label>
                                                    <select name="flows[0][role_level_id]" class="form-control role-select" required>
                                                        <option value="">Pilih role level...</option>
                                                        @foreach($roleLevels as $role)
                                                        <option value="{{ $role->id }}" data-department="{{ $role->department_id }}">
                                                            {{ $role->nama }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Nama Step</label>
                                                    <input type="text" name="flows[0][nama_step]" class="form-control" 
                                                           placeholder="contoh: Review oleh Supervisor Sales" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Deskripsi (Opsional)</label>
                                            <textarea name="flows[0][deskripsi]" class="form-control" rows="2" 
                                                      placeholder="Deskripsi detail tentang step ini..."></textarea>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <button type="button" class="add-step-btn" onclick="addStep()">
                                <i class="feather icon-plus"></i>
                                Tambah Step Baru
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="form-group">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.flow-approval.index') }}" class="btn btn-secondary">
                                <i class="feather icon-arrow-left"></i> Kembali
                            </a>
                            <div>
                                <button type="button" class="btn btn-warning" onclick="previewFlow()">
                                    <i class="feather icon-eye"></i> Preview Flow
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="feather icon-save"></i> Simpan Flow
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Flow Approval</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
let stepCounter = {{ isset($flows) ? $flows->count() : 1 }};

$(document).ready(function() {
    // Initialize sortable
    initializeSortable();
    
    // Department change handler
    $(document).on('change', '.department-select', function() {
        const departmentId = $(this).val();
        const roleSelect = $(this).closest('.flow-step-item').find('.role-select');
        
        filterRolesByDepartment(roleSelect, departmentId);
    });
    
    // Form submit handler
    $('#flowApprovalForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            return;
        }
        
        const formData = new FormData(this);
        const isEdit = {{ isset($flows) ? 'true' : 'false' }};
    
        const method = isEdit ? 'PUT' : 'POST';
        
        if (isEdit) {
            formData.append('_method', 'PUT');
        }
        
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showNotification('success', response.message);
                    if (response.redirect) {
                        setTimeout(() => window.location.href = response.redirect, 1500);
                    } else {
                        setTimeout(() => window.location.href = '{{ route("admin.flow-approval.index") }}', 1500);
                    }
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response.errors) {
                    let errorMsg = 'Validasi gagal:\n';
                    Object.keys(response.errors).forEach(key => {
                        errorMsg += `- ${response.errors[key][0]}\n`;
                    });
                    showNotification('error', errorMsg);
                } else {
                    showNotification('error', response.message || 'Terjadi kesalahan');
                }
            }
        });
    });
});

function initializeSortable() {
    const flowSteps = document.getElementById('flowSteps');
    Sortable.create(flowSteps, {
        handle: '.drag-handle',
        animation: 150,
        onEnd: function() {
            updateStepNumbers();
        }
    });
}

function addStep() {
    const flowSteps = $('#flowSteps');
    const stepHtml = `
        <div class="flow-step-item" data-index="${stepCounter}">
            <i class="feather icon-x remove-step" onclick="removeStep(this)"></i>
            <div class="step-header">
                <i class="feather icon-menu drag-handle"></i>
                <div class="step-number">${stepCounter + 1}</div>
                <h6 class="mb-0">Step ${stepCounter + 1}</h6>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Department</label>
                        <select name="flows[${stepCounter}][department_id]" class="form-control department-select" required>
                            <option value="">Pilih department...</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Role Level</label>
                        <select name="flows[${stepCounter}][role_level_id]" class="form-control role-select" required>
                            <option value="">Pilih role level...</option>
                            @foreach($roleLevels as $role)
                            <option value="{{ $role->id }}" data-department="{{ $role->department_id }}">{{ $role->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Nama Step</label>
                        <input type="text" name="flows[${stepCounter}][nama_step]" class="form-control" 
                               placeholder="contoh: Review oleh Supervisor Sales" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label>Deskripsi (Opsional)</label>
                <textarea name="flows[${stepCounter}][deskripsi]" class="form-control" rows="2" 
                          placeholder="Deskripsi detail tentang step ini..."></textarea>
            </div>
        </div>
    `;
    
    flowSteps.find('.add-step-btn').before(stepHtml);
    stepCounter++;
    updateStepNumbers();
}

function removeStep(element) {
    const stepItem = $(element).closest('.flow-step-item');
    
    if ($('.flow-step-item').length <= 1) {
        showNotification('error', 'Minimal harus ada 1 step approval');
        return;
    }
    
    stepItem.remove();
    updateStepNumbers();
}

function updateStepNumbers() {
    $('.flow-step-item').each(function(index) {
        $(this).attr('data-index', index);
        $(this).find('.step-number').text(index + 1);
        $(this).find('.step-header h6').text(`Step ${index + 1}`);
        
        // Update form field names
        $(this).find('select, input, textarea').each(function() {
            const name = $(this).attr('name');
            if (name && name.includes('flows[')) {
                const newName = name.replace(/flows\[\d+\]/, `flows[${index}]`);
                $(this).attr('name', newName);
            }
        });
    });
}

function filterRolesByDepartment(roleSelect, departmentId) {
    roleSelect.find('option').each(function() {
        const optionDeptId = $(this).data('department');
        if ($(this).val() === '') {
            $(this).show();
        } else if (optionDeptId == departmentId || !departmentId) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
    
    // Reset selection if current selection is not valid for new department
    const currentSelection = roleSelect.val();
    const currentOption = roleSelect.find(`option[value="${currentSelection}"]`);
    if (currentOption.length && currentOption.data('department') != departmentId && departmentId) {
        roleSelect.val('');
    }
}

function loadTemplate(templateType) {
    const templates = {
        sales: [
            { dept: 'Sales', role: 'Supervisor', step: 'Review oleh Supervisor Sales' },
            { dept: 'Sales', role: 'Manager', step: 'Review oleh Manager Sales' },
            { dept: 'Sales', role: 'General Manager', step: 'Approval General Manager Sales' },
            { dept: 'Finance', role: 'Finance 1', step: 'Review Finance' },
            { dept: 'Direksi', role: 'Direksi', step: 'Final Approval Direksi' }
        ],
        marketing: [
            { dept: 'Marketing', role: 'Supervisor', step: 'Review oleh Atasan Langsung' },
            { dept: 'Marketing', role: 'Head Marketing', step: 'Approval Head Marketing' },
            { dept: 'Finance', role: 'Finance 2', step: 'Review Finance Level 2' },
            { dept: 'Direksi', role: 'Direksi', step: 'Final Approval Direksi' }
        ],
        finance: [
            { dept: 'Finance', role: 'Finance 1', step: 'Review Finance Staff' },
            { dept: 'Finance', role: 'Finance 2', step: 'Review Finance Supervisor' },
            { dept: 'Finance', role: 'Finance 3', step: 'Approval Finance Manager' },
            { dept: 'Direksi', role: 'Direksi', step: 'Final Approval Direksi' }
        ],
        simple: [
            { dept: 'Finance', role: 'Finance 2', step: 'Review Finance' },
            { dept: 'Direksi', role: 'Direksi', step: 'Final Approval' }
        ]
    };
    
    if (!templates[templateType]) return;
    
    // Clear existing steps
    $('#flowSteps .flow-step-item').remove();
    
    // Add template steps
    templates[templateType].forEach((template, index) => {
        addStep();
        const stepItem = $('.flow-step-item').last();
        
        // Set department
        const deptSelect = stepItem.find('.department-select');
        deptSelect.find('option').each(function() {
            if ($(this).text().includes(template.dept)) {
                deptSelect.val($(this).val());
                return false;
            }
        });
        
        // Filter and set role
        const roleSelect = stepItem.find('.role-select');
        filterRolesByDepartment(roleSelect, deptSelect.val());
        roleSelect.find('option').each(function() {
            if ($(this).text().includes(template.role)) {
                roleSelect.val($(this).val());
                return false;
            }
        });
        
        // Set step name
        stepItem.find('input[name*="nama_step"]').val(template.step);
    });
    
    stepCounter = templates[templateType].length;
    updateStepNumbers();
    
    showNotification('success', `Template ${templateType} berhasil dimuat`);
}

function previewFlow() {
    const steps = [];
    $('.flow-step-item').each(function() {
        const deptText = $(this).find('.department-select option:selected').text();
        const roleText = $(this).find('.role-select option:selected').text();
        const stepName = $(this).find('input[name*="nama_step"]').val();
        const desc = $(this).find('textarea[name*="deskripsi"]').val();
        
        if (stepName && deptText && roleText) {
            steps.push({
                department: deptText,
                role: roleText,
                name: stepName,
                description: desc
            });
        }
    });
    
    if (steps.length === 0) {
        showNotification('error', 'Belum ada step yang valid untuk di-preview');
        return;
    }
    
    let html = '<div class="flow-preview">';
    steps.forEach((step, index) => {
        html += `
            <div class="media mb-3">
                <div class="step-number mr-3">${index + 1}</div>
                <div class="media-body">
                    <h6 class="mt-0 mb-1">${step.name}</h6>
                    <p class="mb-1">
                        <strong>Department:</strong> ${step.department}<br>
                        <strong>Role Level:</strong> ${step.role}
                    </p>
                    ${step.description ? `<small class="text-muted">${step.description}</small>` : ''}
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    $('#previewContent').html(html);
    $('#previewModal').modal('show');
}

function validateForm() {
    const kategoriId = $('#kategori_pengajuan_id').val();
    if (!kategoriId) {
        showNotification('error', 'Silakan pilih kategori pengajuan');
        return false;
    }
    
    let isValid = true;
    $('.flow-step-item').each(function() {
        const deptId = $(this).find('.department-select').val();
        const roleId = $(this).find('.role-select').val();
        const stepName = $(this).find('input[name*="nama_step"]').val();
        
        if (!deptId || !roleId || !stepName) {
            isValid = false;
            return false;
        }
    });
    
    if (!isValid) {
        showNotification('error', 'Pastikan semua field pada setiap step telah diisi');
        return false;
    }
    
    return true;
}

function showNotification(type, message) {
    // Implementasi notifikasi sesuai dengan library yang Anda gunakan
    if (typeof toastr !== 'undefined') {
        toastr[type](message);
    } else {
        alert(message);
    }
}
</script>
@endsection