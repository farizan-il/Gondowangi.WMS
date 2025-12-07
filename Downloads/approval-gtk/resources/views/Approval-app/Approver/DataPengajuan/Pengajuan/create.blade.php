@extends('Approval-app.Layout.approver-main')

@section('head')
<!-- Add Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<style>
    /* Card Modern Styles */
    .card-modern {
        border: none;
        border-radius: 1rem;
        overflow: hidden;
        position: relative;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        background: linear-gradient(145deg, #ffffff, #f8f9fa);
        cursor: pointer;
    }
    
    .card-modern:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .card-modern.active {
        border: 2px solid #007bff;
        background: linear-gradient(145deg, #f8f9ff, #e3f2fd);
    }
    
    .card-modern .card-body {
        padding: 2rem 1.5rem;
        text-align: center;
    }
    
    .card-modern .icon-bg {
        width: 4.5rem;
        height: 4.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin: 0 auto 1.5rem;
        position: relative;
        overflow: hidden;
    }
    
    .card-modern .icon-bg::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: inherit;
        opacity: 0.1;
        border-radius: 50%;
    }
    
    .card-modern .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #2d3748;
    }
    
    .card-modern .card-text {
        font-size: 0.875rem;
        color: #718096;
        margin-bottom: 1rem;
        line-height: 1.5;
    }
    
    .btn-create {
        border-radius: 0.5rem;
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .btn-create:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Modal Styles */
    .modal-xl {
        max-width: 95%;
    }
    
    .modal-content {
        border-radius: 1rem;
        border: none;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
    
    .modal-header {
        /*background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);*/
        color: white;
        border-radius: 1rem 1rem 0 0;
        padding: 1.5rem 2rem;
    }
    
    .modal-title {
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .modal-body {
        padding: 2rem;
        background: #f8f9fa;
    }
    
    .form-section {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .form-control, .form-select {
        border-radius: 0.5rem;
        border: 1.5px solid #e2e8f0;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 0.5rem;
        padding: 0.75rem 2rem;
        font-weight: 500;
    }
    
    .btn-secondary {
        border-radius: 0.5rem;
        padding: 0.75rem 2rem;
        font-weight: 500;
    }
    
    /* Loading Styles */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        border-radius: 0.75rem;
    }
    
    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Dynamic Form Styles */
    .dynamic-form-container {
        min-height: 200px;
        position: relative;
    }
    
    .field-group {
        margin-bottom: 1rem;
    }
    
    .field-label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    
    .required-field::after {
        content: " *";
        color: #ef4444;
    }
    
    /* File Upload Styles */
    .file-upload-area {
        border: 2px dashed #cbd5e0;
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .file-upload-area:hover {
        border-color: #667eea;
        background: #f7fafc;
    }
    
    .file-upload-area.dragover {
        border-color: #667eea;
        background: #ebf4ff;
    }
    
    /* Alert Styles */
    .alert {
        border-radius: 0.5rem;
        border: none;
        padding: 1rem 1.5rem;
    }
    
    .alert-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    
    .alert-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }
    /* CSS untuk tampilan Excel-like table form */
    .excel-table-container {
        /*max-height: 400px;*/
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }
    
    .excel-table {
        margin-bottom: 0;
        font-size: 0.9rem;
    }
    
    .excel-table th {
        background-color: #f8f9fa;
        border-color: #dee2e6;
        font-weight: 600;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .excel-table td {
        vertical-align: middle;
        border-color: #dee2e6;
        padding: 8px 12px;
    }
    
    .excel-row-number {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #6c757d;
        font-size: 0.8rem;
    }
    
    .excel-field-label {
        background-color: #fafbfc;
        font-weight: 500;
    }
    
    .excel-field-label .required-field::after {
        content: " *";
        color: #dc3545;
    }
    
    .excel-field-input {
        padding: 4px 8px;
    }
    
    .excel-input {
        border: 1px solid #ced4da;
        font-size: 0.9rem;
        padding: 6px 10px;
        transition: all 0.15s ease-in-out;
    }
    
    .excel-input:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        background-color: #fff;
    }
    
    .excel-radio-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .excel-radio-group .form-check-inline {
        margin-right: 0;
    }
    
    /* Hover effects */
    .excel-row:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    .excel-row:hover .excel-row-number {
        background-color: rgba(0, 123, 255, 0.1);
    }
    
    .excel-row:hover .excel-field-label {
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    /* Currency input styling */
    .currency-input {
        text-align: right;
    }
    
    /* File input styling */
    .excel-input[type="file"] {
        padding: 4px 8px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .excel-table-container {
            max-height: 300px;
        }
        
        .excel-table th,
        .excel-table td {
            padding: 6px 8px;
            font-size: 0.8rem;
        }
        
        .excel-field-label {
            min-width: 150px;
        }
        
        .excel-field-input {
            min-width: 200px;
        }
    }
    
    /* Loading state */
    .excel-table-loading {
        opacity: 0.6;
        pointer-events: none;
    }
    
    /* Validation styling */
    .excel-input.is-invalid {
        border-color: #dc3545;
    }
    
    .excel-input.is-valid {
        border-color: #28a745;
    }
    
    /* Custom scrollbar untuk table container */
    .excel-table-container::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    .excel-table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    .excel-table-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }
    
    .excel-table-container::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Add new styles for maximizing and minimizing */
    .modal-fullscreen {
        max-width: 100% !important;
        height: 100% !important;
        margin: 0;
        border-radius: 0;
    }
    
    .modal-body {
        max-height: calc(100vh - 200px); /* Ensure content is scrollable when maximized */
        overflow-y: auto;
    }
    
    /* For minimizing, you can add any custom styles, if necessary */
    .modal-minimized .modal-body {
        max-height: 400px; /* Restrict the height when minimized */
    }
    
    /* Add styles for the expanded "Detail Pengajuan" */
        /* Ensure the form inside Detail Pengajuan expands with the section */
    .detail-expanded .dynamic-form-container {
        height: calc(100vh - 200px); /* Take up the full available height minus padding */
        overflow-y: auto;
    }
    
    /* Ensure the form fields take up the available height */
    .detail-expanded .form-section {
        margin-bottom: 1.5rem;
        height: auto;  /* Allow sections to grow as needed */
        max-height: none;
    }
    
    .detail-expanded .form-control,
    .detail-expanded .form-select,
    .detail-expanded .excel-input {
        height: 40px; /* Adjust input fields' height for consistency */
    }
    
    /* Adjust the text area to fill the available space */
    .detail-expanded textarea {
        height: auto;
        min-height: 150px;
        resize: vertical;
    }
    
    /* For better responsiveness, ensure the form can scroll if it's too long */
    .detail-expanded .modal-body {
        overflow-y: auto;
        padding-right: 0; /* Prevents unnecessary scrolling */
    }
    .detail-expanded {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background-color: white;
        z-index: 1050;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
        padding: 2rem;
        border-radius: 0;
    }
    
    /* Styles for minimizing */
    .detail-minimized {
        max-height: 400px; /* Restrict height when minimized */
        overflow-y: auto;
    }
    /* Hover effect for the icon */
    .icon-bg i {
        transition: transform 0.3s ease, color 0.3s ease;
    }
    
    /* Hover effect when the user hovers over the icon */
    .icon-bg:hover i {
        transform: scale(1.2);  /* Slightly enlarge the icon */
        color: #007bff;  /* Change the color when hovered */
    }
    
    .icon-bg:hover {
        background: rgba(0, 123, 255, 0.1); /* Change background color on hover */
        cursor: pointer; /* Change cursor to indicate it's interactive */
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
                    <h5 class="m-b-10">Buat Pengajuan Baru</h5>
                    <p class="text-muted">Pilih kategori pengajuan dan isi form yang tersedia</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row g-4 justify-content-center">
    @forelse($kategoriPengajuan as $kategori)
    <div class="col-sm-6 col-lg-3">
        <div class="card card-modern" data-kategori-id="{{ $kategori->id }}" data-kategori-nama="{{ $kategori->nama }}">
            <div class="card-body">
                <div class="icon-bg mb-3 bg-success" style="background: rgba({{ $kategori->warna ?? '102,16,242' }}, 0.1); color: {{ $kategori->warna ? '#' . $kategori->warna : '#6610f2' }};">
                    <!--<i data-feather="{{ $kategori->icon ?? 'file-text' }}"></i>-->
                    <i class="fas fa-{{ $kategori->icon ?? 'fa-file-alt' }}"></i>
                </div>
                <h5 class="card-title">{{ $kategori->nama }}</h5>
                <p class="card-text">{{ $kategori->deskripsi ?? 'Buat pengajuan ' . strtolower($kategori->nama) }}</p>
                <button class="btn btn-create btn-outline-primary btn-sm" style="border-color: #0e6a39; border-width: 1px;">
                    <i class="fas fa-plus" class="me-2"></i>
                    Buat Pengajuan
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info text-center">
            <i data-feather="info" class="me-2"></i>
            Belum ada kategori pengajuan yang tersedia.
        </div>
    </div>
    @endforelse
</div>
<!-- [ Main Content ] end -->

<!-- Modal -->
<div class="modal fade" id="pengajuanModal" tabindex="-1" aria-labelledby="pengajuanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title" id="pengajuanModalLabel">
                    <i data-feather="file-plus"></i>
                    <span id="modalTitle">Buat Pengajuan</span>
                </h5>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <!-- Alert Container -->
                <div id="alertContainer"></div>
                
                <form id="pengajuanForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="kategoriPengajuanId" name="kategori_pengajuan_id" value="">
                    
                    <!-- Dynamic Form Fields Section -->
                    <div class="form-section" id="detailPengajuanSection">
                        <h6 class="section-title">Detail Pengajuan
                            <button type="button" class="btn btn-outline-primary btn-sm" id="maximizeDetailBtn">
                                <i data-feather="maximize-2"></i> Perbesar Detail
                            </button>
                        </h6>
                        <div class="dynamic-form-container" id="dynamicFormContainer">
                            <div class="loading-overlay" id="formLoadingOverlay" style="display: none;">
                                <div class="spinner"></div>
                            </div>
                            <div class="text-center text-muted" id="selectCategoryMessage">
                                <i data-feather="info" class="me-2"></i>
                                Pilih kategori pengajuan untuk memuat form detail
                            </div>
                        </div>
                    </div>
                    
                    <!-- File Upload Section -->
                    <div class="form-section">
                        <h6 class="section-title">File Pendukung</h6>
                        <div class="file-upload-area" id="fileUploadArea">
                            <i data-feather="upload" class="mb-2" style="width: 48px; height: 48px; color: #9ca3af;"></i>
                            <p class="mb-2"><strong>Klik untuk upload</strong> atau drag & drop file disini</p>
                            <p class="text-muted small">Maksimal 10MB per file. Format: PDF, JPG, PNG, DOC, DOCX</p>
                            <input type="file" id="fileInput" name="file_pendukung[]" multiple style="display: none;" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        </div>
                        <div id="fileList" class="mt-3"></div>
                    </div>
                    
                    <!-- Additional Notes Section -->
                    <div class="form-section">
                        <h6 class="section-title">Catatan Tambahan</h6>
                        <textarea class="form-control" name="catatan_requester" rows="3" placeholder="Catatan untuk approver (opsional)"></textarea>
                        
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="is_settlement_required" id="settlementRequired">
                            <label class="form-check-label" for="settlementRequired">
                                Pengajuan ini memerlukan settlement
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i data-feather="x" class="me-1"></i>
                    Batal
                </button>
                <div>
                    <button type="button" class="btn btn-outline-primary rounded me-2" id="saveDraftBtn">
                        <i data-feather="save" class="me-1"></i>
                        Simpan Draft
                    </button>
                    <button type="button" class="btn btn-primary" id="submitBtn">
                        <i data-feather="send" class="me-1"></i>
                        Submit Pengajuan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    if (response.success && response.redirect) {
        window.location.href = response.redirect;
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const maximizeDetailBtn = document.getElementById('maximizeDetailBtn');
        const detailPengajuanSection = document.getElementById('detailPengajuanSection');
        
        // Handle the maximize button for the "Detail Pengajuan"
        maximizeDetailBtn.addEventListener('click', function() {
            if (detailPengajuanSection.classList.contains('detail-expanded')) {
                // Minimize the detail section
                detailPengajuanSection.classList.remove('detail-expanded');
                detailPengajuanSection.classList.add('detail-minimized');
                maximizeDetailBtn.innerHTML = '<i data-feather="maximize-2"></i> Perbesar Detail';
            } else {
                // Expand the detail section
                detailPengajuanSection.classList.remove('detail-minimized');
                detailPengajuanSection.classList.add('detail-expanded');
                maximizeDetailBtn.innerHTML = '<i data-feather="minimize-2"></i> Perkecil Detail';
            }
    
            // Re-initialize feather icons to update the icon
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    });

</script>

<script>
let selectedKategoriId = null;
let formFields = {};
let selectedFiles = [];

// Debug function untuk troubleshooting
function debugBootstrap() {
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap JavaScript tidak tersedia');
        return false;
    }
    
    if (typeof bootstrap.Modal === 'undefined') {
        console.error('Bootstrap Modal tidak tersedia');
        return false;
    }
    
    console.log('Bootstrap Modal siap digunakan');
    return true;
}

// Debug modal element
function debugModal() {
    const modal = document.getElementById('pengajuanModal');
    if (modal) {
        console.log('Modal element found');
        console.log('Modal classes:', modal.className);
        console.log('Modal style:', modal.style.cssText);
        console.log('Modal computed display:', window.getComputedStyle(modal).display);
        return true;
    } else {
        console.error('Modal element not found');
        return false;
    }
}

// PERBAIKAN: Reset form dan data sebelum load kategori baru
function resetFormAndData() {
    // Reset form
    const form = document.getElementById('pengajuanForm');
    if (form) {
        form.reset();
    }
    
    // Clear alerts
    const alertContainer = document.getElementById('alertContainer');
    if (alertContainer) {
        alertContainer.innerHTML = '';
    }
    
    // Reset global variables
    formFields = {};
    selectedFiles = [];
    
    // Reset dynamic form container ke state awal
    const container = document.getElementById('dynamicFormContainer');
    if (container) {
        container.innerHTML = `
            <div class="loading-overlay" id="formLoadingOverlay" style="display: none;">
                <div class="spinner"></div>
            </div>
            <div class="text-center text-muted" id="selectCategoryMessage">
                <i data-feather="info" class="me-2"></i>
                Pilih kategori pengajuan untuk memuat form detail
            </div>
        `;
    }
    
    // Reset file list
    const fileList = document.getElementById('fileList');
    if (fileList) {
        fileList.innerHTML = '';
    }
    
    // Reset file input
    const fileInput = document.getElementById('fileInput');
    if (fileInput) {
        fileInput.value = '';
    }
    
    console.log('Form dan data berhasil di-reset');
}

// Select kategori pengajuan - FIXED VERSION dengan reset form
function selectKategori(kategoriId, kategoriNama) {
    console.log('selectKategori called with:', kategoriId, kategoriNama);
    
    // Debug checks
    if (!debugBootstrap() || !debugModal()) {
        return;
    }
    
    // PERBAIKAN: Reset form dan data sebelum memuat kategori baru
    resetFormAndData();
    
    // Remove active class from all cards
    document.querySelectorAll('.card-modern').forEach(card => {
        card.classList.remove('active');
    });
    
    // Add active class to selected card
    const selectedCard = document.querySelector(`[data-kategori-id="${kategoriId}"]`);
    if (selectedCard) {
        selectedCard.classList.add('active');
    }
    
    // Set kategori yang dipilih
    selectedKategoriId = kategoriId;
    document.getElementById('kategoriPengajuanId').value = kategoriId;
    document.getElementById('modalTitle').textContent = `Buat Pengajuan - ${kategoriNama}`;
    
    // Show modal with error handling
    try {
        const modalElement = document.getElementById('pengajuanModal');
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            console.log('Modal should be visible now');
            
            // PERBAIKAN: Load form fields setelah modal ditampilkan
            setTimeout(() => {
                loadFormFields(kategoriId);
            }, 100); // Delay kecil untuk memastikan modal sudah terbuka
        } else {
            console.error('Modal element not found');
        }
    } catch (error) {
        console.error('Error showing modal:', error);
    }
}

// PERBAIKAN: Load dynamic form fields dengan handling yang lebih baik
async function loadFormFields(kategoriId) {
    console.log('Loading form fields for kategori:', kategoriId);
    
    const container = document.getElementById('dynamicFormContainer');
    const loadingOverlay = document.getElementById('formLoadingOverlay');
    const selectMessage = document.getElementById('selectCategoryMessage');
    
    if (!container || !loadingOverlay) {
        console.error('Form container elements not found');
        return;
    }
    
    // Show loading
    loadingOverlay.style.display = 'flex';
    if (selectMessage) {
        selectMessage.style.display = 'none';
    }
    
    try {
        const response = await fetch(`/kategori-pengajuan/${kategoriId}/form-fields`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('Form fields response:', result);
        
        if (result.success) {
            // PERBAIKAN: Reset formFields sebelum assign data baru
            formFields = {};
            formFields = result.data;
            renderFormFields();
        } else {
            showAlert('danger', result.message || 'Gagal memuat form fields');
            // Tampilkan pesan error di container
            container.innerHTML = `
                <div class="text-center text-muted">
                    <i data-feather="alert-circle" class="me-2"></i>
                    Gagal memuat form fields untuk kategori ini
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading form fields:', error);
        showAlert('danger', 'Terjadi kesalahan saat memuat form fields');
        
        // Tampilkan pesan error di container
        container.innerHTML = `
            <div class="text-center text-muted">
                <i data-feather="alert-circle" class="me-2"></i>
                Terjadi kesalahan saat memuat form fields
            </div>
        `;
    } finally {
        loadingOverlay.style.display = 'none';
        
        // Re-initialize feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }
}

// PERBAIKAN: Render dynamic form fields dengan cleanup yang lebih baik
function renderFormFields() {
    const container = document.getElementById('dynamicFormContainer');
    if (!container) {
        console.error('Dynamic form container not found');
        return;
    }
    
    let html = '';
    
    console.log('Rendering form fields:', formFields);
    
    if (!formFields || Object.keys(formFields).length === 0) {
        html = '<p class="text-muted text-center">Tidak ada form field tambahan untuk kategori ini</p>';
    } else {
        // Buat table Excel-like
        html += `
            <div class="excel-table-container">
                <div class="table-responsive">
                    <table class="table table-bordered excel-table" id="excelTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">#</th>
                                <th style="min-width: 200px;">Field</th>
                                <th style="min-width: 300px;">Value</th>
                                <th style="width: 100px;" class="text-center">Required</th>
                            </tr>
                        </thead>
                        <tbody>
        `;
        
        let rowNumber = 1;
        // Urutkan berdasarkan posisi row dan col
        const sortedFields = [];
        Object.keys(formFields).forEach(row => {
            if (Array.isArray(formFields[row])) {
                formFields[row].forEach(field => {
                    sortedFields.push({...field, row_pos: parseInt(row)});
                });
            }
        });
        
        sortedFields.sort((a, b) => {
            if (a.row_pos !== b.row_pos) return a.row_pos - b.row_pos;
            return (a.posisi_col || 0) - (b.posisi_col || 0);
        });
        
        sortedFields.forEach(field => {
            html += `
                <tr class="excel-row" data-field="${field.nama_field}">
                    <td class="text-center excel-row-number">${rowNumber}</td>
                    <td class="excel-field-label">
                        <label class="form-label mb-0 ${field.wajib ? 'required-field' : ''}">
                            ${field.label}
                        </label>
                        ${field.placeholder ? `<small class="text-muted d-block">${field.placeholder}</small>` : ''}
                    </td>
                    <td class="excel-field-input">
                        ${renderExcelField(field)}
                    </td>
                    <td class="text-center">
                        ${field.wajib ? '<i class="text-danger" data-feather="asterisk" style="width: 12px; height: 12px;"></i>' : '-'}
                    </td>
                </tr>
            `;
            rowNumber++;
        });
        
        html += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }
    
    // PERBAIKAN: Set innerHTML dan cleanup event listeners lama
    container.innerHTML = html;
    
    // Initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Initialize form enhancements
    initializeExcelFormEnhancements();
    
    console.log('Form fields rendered successfully');
}

// Render field untuk tampilan Excel
function renderExcelField(field) {
    const isRequired = field.wajib ? 'required' : '';
    const fieldName = `form_data[${field.nama_field}]`;
    
    let fieldHtml = '';
    
    switch (field.tipe_field) {
        case 'text':
            fieldHtml = `<input type="text" class="form-control excel-input" name="${fieldName}" placeholder="${field.placeholder || ''}" ${isRequired}>`;
            break;
            
        case 'textarea':
            fieldHtml = `<textarea class="form-control excel-input" name="${fieldName}" rows="2" placeholder="${field.placeholder || ''}" ${isRequired}></textarea>`;
            break;
            
        case 'number':
            fieldHtml = `<input type="number" class="form-control excel-input" name="${fieldName}" placeholder="${field.placeholder || '0'}" step="0.01" ${isRequired}>`;
            break;
            
        case 'currency':
            fieldHtml = `
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" class="form-control excel-input currency-input" name="${fieldName}" placeholder="0" step="0.01" ${isRequired}>
                </div>
            `;
            break;
            
        case 'date':
            fieldHtml = `<input type="date" class="form-control excel-input" name="${fieldName}" ${isRequired}>`;
            break;
            
        case 'select':
            fieldHtml = `<select class="form-select excel-input" name="${fieldName}" ${isRequired}>`;
            fieldHtml += `<option value="">- Pilih ${field.label} -</option>`;
            if (field.opsi) {
                const options = typeof field.opsi === 'string' ? JSON.parse(field.opsi) : field.opsi;
                if (Array.isArray(options)) {
                    options.forEach(option => {
                        fieldHtml += `<option value="${option}">${option}</option>`;
                    });
                }
            }
            fieldHtml += '</select>';
            break;
            
        case 'radio':
            fieldHtml = '<div class="excel-radio-group">';
            if (field.opsi) {
                const options = typeof field.opsi === 'string' ? JSON.parse(field.opsi) : field.opsi;
                if (Array.isArray(options)) {
                    options.forEach((option, index) => {
                        const radioId = `${field.nama_field}_${index}_${Date.now()}`; // Unique ID
                        fieldHtml += `
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="${fieldName}" value="${option}" id="${radioId}" ${isRequired}>
                                <label class="form-check-label" for="${radioId}">
                                    ${option}
                                </label>
                            </div>
                        `;
                    });
                }
            }
            fieldHtml += '</div>';
            break;
            
        case 'checkbox':
            const checkboxId = `${field.nama_field}_${Date.now()}`; // Unique ID
            fieldHtml = `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="${fieldName}" value="1" id="${checkboxId}">
                    <label class="form-check-label" for="${checkboxId}">
                        ${field.placeholder || 'Ya'}
                    </label>
                </div>
            `;
            break;
            
        case 'file':
            fieldHtml = `
                <input type="file" class="form-control excel-input" name="${fieldName}" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" ${isRequired}>
                <small class="text-muted">Max: 5MB. Format: PDF, JPG, PNG, DOC, DOCX</small>
            `;
            break;
            
        default:
            fieldHtml = `<input type="text" class="form-control excel-input" name="${fieldName}" placeholder="${field.placeholder || ''}" ${isRequired}>`;
    }
    
    return fieldHtml;
}

// Initialize enhancements untuk Excel form
function initializeExcelFormEnhancements() {
    // Format currency inputs
    document.querySelectorAll('.currency-input').forEach(input => {
        // Remove existing event listeners
        input.removeEventListener('input', handleCurrencyInput);
        input.removeEventListener('blur', handleCurrencyBlur);
        input.removeEventListener('focus', handleCurrencyFocus);
        
        // Add new event listeners
        input.addEventListener('input', handleCurrencyInput);
        input.addEventListener('blur', handleCurrencyBlur);
        input.addEventListener('focus', handleCurrencyFocus);
    });
    
    // Add row hover effects
    document.querySelectorAll('.excel-row').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.classList.add('table-active');
        });
        
        row.addEventListener('mouseleave', function() {
            this.classList.remove('table-active');
        });
    });
    
    // Auto-resize textareas
    document.querySelectorAll('textarea.excel-input').forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
}

// Currency input handlers
function handleCurrencyInput(e) {
    let value = e.target.value.replace(/[^\d]/g, '');
    if (value) {
        e.target.dataset.rawValue = value;
    }
}

function handleCurrencyBlur(e) {
    let rawValue = e.target.value.replace(/[^\d.]/g, '');
    if (rawValue && !isNaN(rawValue)) {
        e.target.value = parseFloat(rawValue).toLocaleString('id-ID');
    }
}

function handleCurrencyFocus(e) {
    let rawValue = e.target.value.replace(/[^\d.]/g, '');
    if (rawValue) {
        e.target.value = rawValue;
    }
}

// Enhanced form submission with currency handling
async function submitForm(action) {
    const form = document.getElementById('pengajuanForm');
    const formData = new FormData(form);
    
    // Process currency fields before submission
    document.querySelectorAll('.currency-input').forEach(input => {
        const rawValue = input.value.replace(/[^\d]/g, '');
        if (rawValue) {
            // Update form data dengan raw value
            const name = input.name;
            if (name) {
                formData.set(name, rawValue);
            }
        }
    });
    
    // Add action type
    formData.append('submit_action', action);
    
    // Disable buttons
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    saveDraftBtn.disabled = true;
    submitBtn.disabled = true;
    
    // Show loading
    const originalSaveDraftText = saveDraftBtn.innerHTML;
    const originalSubmitText = submitBtn.innerHTML;
    
    if (action === 'draft') {
        saveDraftBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';
    } else {
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Mengirim...';
    }
    
    try {
        const endpoint = action === 'draft' ? '/pengajuan/save-draft' : '/pengajuan/submit';
        const response = await fetch(endpoint, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success', result.message);
            
            // Reset form after successful submission
            if (action === 'submit') {
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('pengajuanModal'));
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Optionally redirect or reload
                    // window.location.reload();
                }, 2000);
            }
        } else {
            if (result.errors) {
                let errorMessage = 'Validasi gagal:\n';
                Object.keys(result.errors).forEach(field => {
                    errorMessage += `- ${result.errors[field].join(', ')}\n`;
                });
                showAlert('danger', errorMessage);
            } else {
                showAlert('danger', result.message || 'Terjadi kesalahan');
            }
        }
    } catch (error) {
        console.error('Error submitting form:', error);
        showAlert('danger', 'Terjadi kesalahan saat mengirim data');
    } finally {
        // Re-enable buttons and restore text
        saveDraftBtn.disabled = false;
        submitBtn.disabled = false;
        saveDraftBtn.innerHTML = originalSaveDraftText;
        submitBtn.innerHTML = originalSubmitText;
        
        // Re-initialize feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }
}

// Show alert function
function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) {
        console.error('Alert container not found');
        return;
    }
    
    const alertId = 'alert-' + Date.now();
    
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert" id="${alertId}">
            <i data-feather="${type === 'success' ? 'check-circle' : 'alert-circle'}" class="me-2"></i>
            ${message.replace(/\n/g, '<br>')}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    alertContainer.innerHTML = alertHtml;
    
    // Initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Auto hide success alerts
    if (type === 'success') {
        setTimeout(() => {
            const alert = document.getElementById(alertId);
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    }
}

// Reset modal when closed - IMPROVED VERSION
function resetModal() {
    console.log('Resetting modal...');
    
    // Reset form
    const form = document.getElementById('pengajuanForm');
    if (form) {
        form.reset();
    }
    
    // Clear alerts
    const alertContainer = document.getElementById('alertContainer');
    if (alertContainer) {
        alertContainer.innerHTML = '';
    }
    
    // Reset selected kategori
    selectedKategoriId = null;
    document.querySelectorAll('.card-modern').forEach(card => {
        card.classList.remove('active');
    });
    
    // Reset global variables
    formFields = {};
    selectedFiles = [];
    
    // Reset dynamic form container
    const container = document.getElementById('dynamicFormContainer');
    if (container) {
        container.innerHTML = `
            <div class="loading-overlay" id="formLoadingOverlay" style="display: none;">
                <div class="spinner"></div>
            </div>
            <div class="text-center text-muted" id="selectCategoryMessage">
                <i data-feather="info" class="me-2"></i>
                Pilih kategori pengajuan untuk memuat form detail
            </div>
        `;
    }
    
    // Reset file list
    const fileList = document.getElementById('fileList');
    if (fileList) {
        fileList.innerHTML = '';
        selectedFiles = [];
    }
    
    // Reset file input
    const fileInput = document.getElementById('fileInput');
    if (fileInput) {
        fileInput.value = '';
    }
    
    // Re-initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    console.log('Modal reset complete');
}

// Initialize file upload (placeholder - implement sesuai kebutuhan)
function initializeFileUpload() {
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('fileInput');
    
    if (fileUploadArea && fileInput) {
        fileUploadArea.addEventListener('click', () => fileInput.click());
        
        fileInput.addEventListener('change', function(e) {
            // Handle file selection
            const files = Array.from(e.target.files);
            selectedFiles = [...selectedFiles, ...files];
            updateFileList();
        });
        
        // Drag and drop functionality
        fileUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            fileUploadArea.classList.add('dragover');
        });
        
        fileUploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            fileUploadArea.classList.remove('dragover');
        });
        
        fileUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            fileUploadArea.classList.remove('dragover');
            
            const files = Array.from(e.dataTransfer.files);
            selectedFiles = [...selectedFiles, ...files];
            updateFileList();
        });
    }
}

function updateFileList() {
    const fileList = document.getElementById('fileList');
    if (!fileList) return;
    
    let html = '';
    selectedFiles.forEach((file, index) => {
        html += `
            <div class="d-flex justify-content-between align-items-center p-2 border rounded mb-2">
                <span>${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                    <i data-feather="x"></i>
                </button>
            </div>
        `;
    });
    
    fileList.innerHTML = html;
    
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    updateFileList();
}

// Document ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing...');
    
    // Debug Bootstrap availability
    debugBootstrap();
    
    // Event delegation untuk card selection - IMPROVED VERSION
    document.addEventListener('click', function(e) {
        const card = e.target.closest('.card-modern');
        if (card) {
            console.log('Card clicked:', card);
            
            const kategoriId = card.getAttribute('data-kategori-id');
            const kategoriNama = card.getAttribute('data-kategori-nama');
            
            console.log('Kategori ID:', kategoriId, 'Nama:', kategoriNama);
            
            if (kategoriId && kategoriNama) {
                // PERBAIKAN: Pastikan tidak ada konflik dengan kategori sebelumnya
                if (selectedKategoriId !== kategoriId) {
                    selectKategori(kategoriId, kategoriNama);
                } else {
                    console.log('Same category selected, no need to reload');
                }
            } else {
                console.error('Missing kategori data');
            }
        }
    });
    
    // Initialize file upload
    initializeFileUpload();
    
    // Form submission event listeners
    document.getElementById('saveDraftBtn').addEventListener('click', function() {
        submitForm('draft');
    });

    document.getElementById('submitBtn').addEventListener('click', function() {
        submitForm('submit');
    });
    
    // Modal reset event listener
    const modalElement = document.getElementById('pengajuanModal');
    if (modalElement) {
        modalElement.addEventListener('hidden.bs.modal', resetModal);
        modalElement.addEventListener('show.bs.modal', function() {
            console.log('Modal is about to show');
        });
        modalElement.addEventListener('shown.bs.modal', function() {
            console.log('Modal is now visible');
        });
    }
    
    // Initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
        console.log('Feather icons initialized');
    }
    
    console.log('Initialization complete');
});
</script>
@endsection