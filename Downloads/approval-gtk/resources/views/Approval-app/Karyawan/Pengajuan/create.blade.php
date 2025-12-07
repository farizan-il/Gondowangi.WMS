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
        cursor: pointer;d
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
        max-height: calc(120vh - 200px); /* Ensure content is scrollable when maximized */
        overflow-y: auto;
    }
    
    /* For minimizing, you can add any custom styles, if necessary */
    .modal-minimized .modal-body {
        max-height: 400px; /* Restrict the height when minimized */
    }
    
    /* Add styles for the expanded "Detail Pengajuan" */
        /* Ensure the form inside Detail Pengajuan expands with the section */
    .detail-expanded .dynamic-form-container {
        height: calc(120vh - 200px); /* Take up the full available height minus padding */
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

<style>
    .perjalanan-dinas-form {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .perjalanan-table {
        font-size: 0.9rem;
    }
    
    .perjalanan-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
        padding: 12px 8px;
        border: 1px solid #dee2e6;
    }
    
    .perjalanan-table td {
        vertical-align: middle;
        padding: 8px;
        border: 1px solid #dee2e6;
    }
    
    .perjalanan-table .total-cell {
        background-color: #f8f9fa;
        font-weight: 500;
        text-align: center;
    }
    
    .perjalanan-table .total-grand {
        background-color: #e3f2fd;
        font-weight: 700;
        color: #1976d2;
    }
    
    .perjalanan-input {
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 0.875rem;
    }
    
    .perjalanan-input:focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    /* Header styling */
    .card-header {
        padding: 10px 15px;
    }
    
    .card-header h6 {
        margin: 0;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    /* Required field indicator */
    .required-field {
        position: relative;
    }
    
    .required-field::after {
        content: " *";
        color: #dc3545;
        font-weight: bold;
    }
    
    /* Input group styling */
    .input-group-sm .input-group-text {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
        background-color: #f8f9fa;
        border-color: #ced4da;
    }
    
    /* Responsive table */
    .table-responsive {
        border-radius: 0.375rem;
    }
    
    /* Custom table borders */
    .perjalanan-table {
        border-collapse: collapse;
    }
    
    .perjalanan-table td,
    .perjalanan-table th {
        border-width: 1px;
    }
    
    /* Highlight active row */
    .perjalanan-table tbody tr:hover {
        background-color: #f5f5f5;
    }
    
    /* Section headers with different colors */
    .bg-success {
        background-color: #198754 !important;
    }
    
    .bg-info {
        background-color: #0dcaf0 !important;
    }
    
    .bg-warning {
        background-color: #ffc107 !important;
    }
    
    /* Modal adjustments */
    .modal-xl {
        max-width: 1200px;
    }
    
    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
    
    /* Form section spacing */
    .form-section {
        margin-bottom: 1.5rem;
    }
    
    .section-title {
        color: #495057;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
    }
    
    /* Print-friendly styles */
    @media print {
        .perjalanan-dinas-form {
            font-size: 12px;
        }
        
        .card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
        }
        
        .card-header {
            background-color: #f0f0f0 !important;
            color: #000 !important;
        }
        
        .perjalanan-table th,
        .perjalanan-table td {
            border: 1px solid #000 !important;
        }
        
        .btn, .modal-footer {
            display: none !important;
        }
    }
    
    /* Custom scrollbar untuk modal body */
    .modal-body::-webkit-scrollbar {
        width: 8px;
    }
    
    .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .modal-body::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }
    
    .modal-body::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    /* Animation untuk total update */
    .total-cell, .total-grand {
        transition: background-color 0.3s ease;
    }
    
    .total-updated {
        background-color: #d4edda !important;
        animation: highlight 1s ease-out;
    }
    
    @keyframes highlight {
        0% { background-color: #d4edda; }
        100% { background-color: inherit; }
    }
    
    /* Input validation states */
    .form-control.is-invalid {
        border-color: #dc3545;
    }
    
    .form-control.is-valid {
        border-color: #198754;
    }
    
    /* Loading state */
    .calculating {
        opacity: 0.7;
        pointer-events: none;
    }
    
    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
        .modal-xl {
            max-width: 95%;
            margin: 0.5rem;
        }
        
        .perjalanan-table {
            font-size: 0.8rem;
        }
        
        .input-group-sm .form-control {
            font-size: 0.75rem;
        }
        
        .card-header h6 {
            font-size: 0.8rem;
        }
    }
    
    
    
    .horizontal-group {
        display: flex;
        gap: 8px;
        margin-bottom: 8px;
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

<!-- Modal Membuat Pengajuan Baru -->
<div class="modal fade" id="pengajuanModal" tabindex="-1" aria-labelledby="pengajuanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title" id="pengajuanModalLabel">
                    <i data-feather="file-plus"></i>
                    <span class="text-white" id="modalTitle">Buat Pengajuan</span>
                </h5>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0">
                <!-- Alert Container -->
                <div id="alertContainer"></div>
                
                <form id="pengajuanForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="kategoriPengajuanId" name="kategori_pengajuan_id" value="">
                    
                    <!-- Dynamic Form Fields Section -->
                    <div class="p-1" id="detailPengajuanSection">
                        <h6 class="section-title">Detail Pengajuan
                            <button type="button" class="btn btn-info btn-sm" id="maximizeDetailBtn">
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
                        <h6 class="section-title">File Pendukung (Opsional)</h6>
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
                        <h6 class="section-title">Catatan Tambahan (Opsional)</h6>
                        <textarea class="form-control" name="catatan_requester" rows="3" placeholder="Catatan untuk approver (opsional)"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i data-feather="x" class="me-1"></i>
                    Batal
                </button>
                <div>
                    <button type="button" class="btn btn-outline-light rounded me-2 disabled" id="saveDraftBtn">
                        
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
    let selectedKategoriId = null;
    let formFields = {};
    let selectedFiles = [];

    // --- Helper Functions ---
    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    }
    
    function formatInputCurrency(angka) {
        if (!angka) return '';
        // Pastikan input string, hapus karakter selain angka
        let number_string = angka.toString().replace(/[^,\d]/g, '').toString();
        let split = number_string.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // Tambahkan titik jika ada ribuan
        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }
    
    function parseCurrencyValue(value) {
        if (!value) return 0;
        if (typeof value === 'number') return value;
        // Hapus titik, ganti koma jadi titik (jika ada desimal), lalu parse
        return parseFloat(value.toString().replace(/\./g, '').replace(',', '.')) || 0;
    }

    function calculateDays(fromDate, toDate) {
        if (!fromDate || !toDate) return 0;
        const from = new Date(fromDate);
        const to = new Date(toDate);
        if (to < from) return 0;
        const timeDiff = to.getTime() - from.getTime();
        return Math.floor(timeDiff / (1000 * 3600 * 24)) + 1;
    }

    function calculateNights(fromDate, toDate) {
        if (!fromDate || !toDate) return 0;
        const from = new Date(fromDate);
        const to = new Date(toDate);
        if (to <= from) return 0;
        const timeDiff = to.getTime() - from.getTime();
        return Math.floor(timeDiff / (1000 * 3600 * 24));
    }

    function showDateError(input, message) {
        const existingError = input.closest('.form-group').querySelector('.date-error');
        if (existingError) existingError.remove();
        input.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'date-error text-danger small mt-1';
        errorDiv.textContent = message;
        input.closest('.form-group').appendChild(errorDiv);
    }

    function clearDateError(input) {
        const errorDiv = input.closest('.form-group')?.querySelector('.date-error');
        if (errorDiv) errorDiv.remove();
        input.classList.remove('is-invalid');
    }

    function enforceUraianDependency() {
        for (let i = 1; i <= 3; i++) {
            const fromInput = document.querySelector(`input[name="form_data[perjalanan${i}_tanggal_dari]"]`);
            const toInput = document.querySelector(`input[name="form_data[perjalanan${i}_tanggal_sampai]"]`);

            const daerahInput = document.querySelector(`input[name="form_data[perjalanan${i}_daerah]"]`);
            const salesRateInput = document.querySelector(`input[name="form_data[perjalanan${i}_sales_rate]"]`);
            const estimasiInput = document.querySelector(`input[name="form_data[perjalanan${i}_estimasi]"]`);
            const outletInput = document.querySelector(`input[name="form_data[perjalanan${i}_outlet]"]`);

            const expenseInputs = document.querySelectorAll(`input.perjalanan-input[data-col="${i}"]`);

            const hasDate = fromInput && toInput && fromInput.value && toInput.value && new Date(fromInput.value) <= new Date(toInput.value);
            
            const dateErrorExists = document.querySelector(`input[name="form_data[perjalanan${i}_tanggal_dari]"].is-invalid`) ||
                                    document.querySelector(`input[name="form_data[perjalanan${i}_tanggal_sampai]"].is-invalid`);
            
            const shouldBeDisabled = !hasDate || dateErrorExists;

            const uraianFields = [daerahInput, salesRateInput, estimasiInput, outletInput];
            uraianFields.forEach(field => {
                if (field) {
                    field.disabled = shouldBeDisabled;
                    if (shouldBeDisabled) {
                        field.value = '';
                    }
                }
            });
            
            expenseInputs.forEach(input => {
                const rowName = input.getAttribute('data-row');
                if (rowName !== 'hotel_biaya' && rowName !== 'makan_biaya') {
                      if(input) input.disabled = shouldBeDisabled;
                      if(shouldBeDisabled) input.value = ''; 
                }
            });
        }
        
        calculateGrandTotal();
        calculateDetailTotals();
    }

    function enforceDateDependency() {
        let hasGlobalError = false;
        const oneDayInMs = 1000 * 60 * 60 * 24;
        
        for (let i = 1; i <= 3; i++) {
            const currentFromInput = document.querySelector(`input[name="form_data[perjalanan${i}_tanggal_dari]"]`);
            const currentToInput = document.querySelector(`input[name="form_data[perjalanan${i}_tanggal_sampai]"]`);

            if (currentFromInput && currentToInput) {
                const currentFrom = currentFromInput.value;
                const currentTo = currentToInput.value;

                if (i > 1) {
                    const prevToInput = document.querySelector(`input[name="form_data[perjalanan${i-1}_tanggal_sampai]"]`);
                    
                    if (prevToInput && prevToInput.value && currentFrom) {
                        const prevEndDate = new Date(prevToInput.value);
                        const currentStartDate = new Date(currentFrom);
                        
                        const minStartDate = new Date(prevEndDate.getTime() + oneDayInMs);
                        currentFromInput.min = minStartDate.toISOString().split('T')[0];
                        
                        if (currentStartDate < minStartDate) {
                            showDateError(currentFromInput, `Tgl mulai harus setelah ${prevEndDate.toLocaleDateString('id-ID')}`);
                            hasGlobalError = true;
                        } else {
                            clearDateError(currentFromInput);
                        }
                    } else {
                        if (currentFrom) {
                             showDateError(currentFromInput, `Isi Tgl Selesai Perjalanan ${i-1} terlebih dahulu`);
                             hasGlobalError = true;
                        } else {
                             clearDateError(currentFromInput);
                        }
                    }
                }
                
                if (!currentFrom && !currentTo) {
                    clearDateError(currentFromInput);
                    clearDateError(currentToInput);
                }
            }
        }
        return !hasGlobalError;
    }

    function updateHotelMakanForPerjalanan(perjalananNum) {
        const hotelRatePerHari = {{ Auth::user()->golongan->biaya_hotel_per_hari ?? 0 }};
        const makanRatePerHari = {{ Auth::user()->golongan->biaya_makan_per_hari ?? 0 }};

        const dateFrom = document.querySelector(`input[name="form_data[perjalanan${perjalananNum}_tanggal_dari]"]`);
        const dateTo = document.querySelector(`input[name="form_data[perjalanan${perjalananNum}_tanggal_sampai]"]`);

        const suffix = perjalananNum > 1 ? '_' + perjalananNum : '';
        const hotelInput = document.querySelector(`input[name="form_data[hotel_biaya${suffix}]"]`);
        const makanInput = document.querySelector(`input[name="form_data[makan_biaya${suffix}]"]`);

        if (dateFrom && dateTo && hotelInput && makanInput) {
            const fromDate = dateFrom.value;
            const toDate = dateTo.value;

            const dateErrorExists = dateFrom.classList.contains('is-invalid') || dateTo.classList.contains('is-invalid');
            const isDateValid = fromDate && toDate && new Date(toDate) >= new Date(fromDate) && !dateErrorExists;

            if (isDateValid) {
                const days = calculateDays(fromDate, toDate);
                const nights = calculateNights(fromDate, toDate);

                document.querySelector(`#days_perjalanan_${perjalananNum}`).textContent = `${days} hari`;
                document.querySelector(`#nights_perjalanan_${perjalananNum}`).textContent = `${nights} malam`;

                const totalBiayaHotel = nights * hotelRatePerHari;
                const totalBiayaMakan = days * makanRatePerHari;

                // Set Value dengan Format Rupiah (1.000.000)
                hotelInput.value = formatInputCurrency(totalBiayaHotel);
                makanInput.value = formatInputCurrency(totalBiayaMakan);

            } else {
                hotelInput.value = '0';
                makanInput.value = '0';
                document.querySelector(`#days_perjalanan_${perjalananNum}`).textContent = '0 hari';
                document.querySelector(`#nights_perjalanan_${perjalananNum}`).textContent = '0 malam';
            }

            calculateGrandTotal();
        }
    }

    function updateDateDisplay(row) {
        updateHotelMakanForPerjalanan(row);
        updatePeriodeFromDates();
    }

    function calculateRowTotal(rowName) {
        let total = 0;
        const isTransport = rowName.includes('transportasi');
        
        let inputName1 = rowName;
        if (isTransport && rowName.endsWith('udara')) inputName1 = rowName + '_1'; 
        
        const col1Input = document.querySelector(`input[name="form_data[${inputName1}]"]`);
        const col2Input = document.querySelector(`input[name="form_data[${rowName}_2]"]`);
        const col3Input = document.querySelector(`input[name="form_data[${rowName}_3]"]`);

        total += parseCurrencyValue(col1Input?.value);
        total += parseCurrencyValue(col2Input?.value);
        total += parseCurrencyValue(col3Input?.value);

        const totalElement = document.getElementById(`total_${rowName}`);
        if (totalElement) {
            totalElement.textContent = formatCurrency(total);
        }

        return total;
    }

    function calculateGrandTotal() {
        const expenseRows = [
            'transportasi_darat', 'transportasi_udara', 'transportasi_taxi', 'hotel_biaya', 'makan_biaya',
            'uang_saku', 'telephone_fax', 'entertainment', 'dokumentasi', 'lain_lain'
        ];

        let grandTotal = 0;

        expenseRows.forEach(rowName => {
            const rowTotal = calculateRowTotal(rowName);
            grandTotal += rowTotal;
        });

        const grandTotalElement = document.getElementById('grand_total');
        if (grandTotalElement) {
            grandTotalElement.innerHTML = `<strong>${formatCurrency(grandTotal)}</strong>`;
        }

        calculateColumnTotals(); 

        return grandTotal;
    }

    function calculateColumnTotals() {
         const expenseRows = [
             'transportasi_darat','transportasi_udara' ,'transportasi_taxi', 'hotel_biaya', 'makan_biaya',
             'uang_saku', 'telephone_fax', 'entertainment', 'dokumentasi', 'lain_lain'
         ];
         
         let col1Total = 0, col2Total = 0, col3Total = 0;
         
         expenseRows.forEach(rowName => {
             for (let i = 1; i <= 3; i++) {
                 let columnValue = 0;
                 let inputName = '';
                 if (i === 1) {
                     inputName = rowName;
                     if (rowName === 'transportasi_udara') inputName = 'transportasi_udara_1';
                 } else {
                     inputName = `${rowName}_${i}`;
                 }

                 const input = document.querySelector(`input[name="form_data[${inputName}]"]`);
                 columnValue = parseCurrencyValue(input?.value);
                 
                 if (i === 1) col1Total += columnValue;
                 else if (i === 2) col2Total += columnValue;
                 else if (i === 3) col3Total += columnValue;
             }
         });
         
         const col1Element = document.getElementById('total_perjalanan_1');
         const col2Element = document.getElementById('total_perjalanan_2');
         const col3Element = document.getElementById('total_perjalanan_3');
         
         if (col1Element) col1Element.innerHTML = `<strong>${formatCurrency(col1Total)}</strong>`;
         if (col2Element) col2Element.innerHTML = `<strong>${formatCurrency(col2Total)}</strong>`;
         if (col3Element) col3Element.innerHTML = `<strong>${formatCurrency(col3Total)}</strong>`;
         
         return { col1Total, col2Total, col3Total };
    }

    function calculateDetailTotals() {
        let totalSalesRate = 0;
        let totalEstimasiSales = 0;
        let totalOutlets = 0;

        for (let i = 1; i <= 3; i++) {
            const salesRateInput = document.querySelector(`input[name="form_data[perjalanan${i}_sales_rate]"]`);
            const estimasiInput = document.querySelector(`input[name="form_data[perjalanan${i}_estimasi]"]`);
            const outletInput = document.querySelector(`input[name="form_data[perjalanan${i}_outlet]"]`);

            totalSalesRate += parseCurrencyValue(salesRateInput?.value);
            totalEstimasiSales += parseCurrencyValue(estimasiInput?.value);
            totalOutlets += parseInt(outletInput?.value) || 0;
        }

        const salesElement = document.getElementById('total_sales_rate');
        const estimasiElement = document.getElementById('total_estimasi_sales');
        const outletsElement = document.getElementById('total_outlets');

        if (salesElement) salesElement.innerHTML = `<strong>${formatCurrency(totalSalesRate)}</strong>`;
        if (estimasiElement) estimasiElement.innerHTML = `<strong>${formatCurrency(totalEstimasiSales)}</strong>`;
        if (outletsElement) outletsElement.innerHTML = `<strong>${totalOutlets}</strong>`;
    }

    function updatePeriodeFromDates() {
        const periodeInput = document.querySelector('input[name="form_data[periode]"]');
        if (!periodeInput) return;

        const allDates = [];

        for (let i = 1; i <= 3; i++) {
            const fromInput = document.querySelector(`input[name="form_data[perjalanan${i}_tanggal_dari]"]`);
            const toInput = document.querySelector(`input[name="form_data[perjalanan${i}_tanggal_sampai]"]`);

            if (fromInput && toInput && fromInput.value && toInput.value) {
                const startDate = new Date(fromInput.value);
                const endDate = new Date(toInput.value);
                if (startDate <= endDate) {
                    allDates.push(startDate);
                    allDates.push(endDate);
                }
            }
        }

        if (allDates.length > 0) {
            const minDate = new Date(Math.min(...allDates));
            const maxDate = new Date(Math.max(...allDates));
            const formatOptions = { day: 'numeric', month: 'long', year: 'numeric' };

            if (minDate.getTime() === maxDate.getTime()) {
                periodeInput.value = minDate.toLocaleDateString('id-ID', formatOptions);
            } else if (minDate.getMonth() === maxDate.getMonth() && minDate.getFullYear() === maxDate.getFullYear()) {
                const startDay = minDate.getDate();
                const endDay = maxDate.getDate();
                const monthYear = maxDate.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
                periodeInput.value = `${startDay}-${endDay} ${monthYear}`;
            } else {
                const startDayMonth = minDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'long' });
                const endFull = maxDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                periodeInput.value = `${startDayMonth} - ${endFull}`;
            }
        } else {
            periodeInput.value = '';
        }
    }

    function resetFormAndData() {
        const form = document.getElementById('pengajuanForm');
        if (form) form.reset();
        const alertContainer = document.getElementById('alertContainer');
        if (alertContainer) alertContainer.innerHTML = '';
        formFields = {};
        selectedFiles = [];
        const container = document.getElementById('dynamicFormContainer');
        if (container) {
            container.innerHTML = `
                <div class="loading-overlay" id="formLoadingOverlay" style="display: none;">
                    <div class="spinner"></div>
                </div>
                <div class="text-center text-muted" id="selectCategoryMessage">
                    <i data-feather="info" class="me-2"></i> Pilih kategori pengajuan untuk memuat form detail
                </div>`;
        }
        const fileList = document.getElementById('fileList');
        if (fileList) fileList.innerHTML = '';
        const fileInput = document.getElementById('fileInput');
        if (fileInput) fileInput.value = '';
    }

    function selectKategori(kategoriId, kategoriNama) {
        resetFormAndData();
        document.querySelectorAll('.card-modern').forEach(card => card.classList.remove('active'));
        const selectedCard = document.querySelector(`[data-kategori-id="${kategoriId}"]`);
        if (selectedCard) selectedCard.classList.add('active');

        selectedKategoriId = kategoriId;
        document.getElementById('kategoriPengajuanId').value = kategoriId;
        document.getElementById('modalTitle').textContent = `Buat Pengajuan - ${kategoriNama}`;

        try {
            const modalElement = document.getElementById('pengajuanModal');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
                setTimeout(() => { loadFormFields(kategoriId); }, 100);
            }
        } catch (error) { console.error('Error showing modal:', error); }
    }

    async function loadFormFields(kategoriId) {
        const container = document.getElementById('dynamicFormContainer');
        const loadingOverlay = document.getElementById('formLoadingOverlay');
        const selectMessage = document.getElementById('selectCategoryMessage');

        if (!container || !loadingOverlay) return;

        loadingOverlay.style.display = 'flex';
        if (selectMessage) selectMessage.style.display = 'none';

        try {
            const response = await fetch(`/kategori-pengajuan/${kategoriId}/form-fields`, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });

            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const result = await response.json();

            if (result.success) {
                formFields = result.data;
                renderFormFields();
            } else {
                showAlert('danger', result.message || 'Gagal memuat form fields');
            }
        } catch (error) {
            console.error('Error loading form fields:', error);
            showAlert('danger', 'Form Untuk Pengajuan Ini Belum Ditambahkan');
            container.innerHTML = `<div class="text-center text-muted"><i data-feather="alert-circle" class="me-2"></i> Form Belum Ditambahkan</div>`;
        } finally {
            loadingOverlay.style.display = 'none';
            if (typeof feather !== 'undefined') feather.replace();
        }
    }

    // --- RENDER FORM ---
    function renderFormFields() {
        const container = document.getElementById('dynamicFormContainer');
        if (!container) return;

        let html = '';

        if (!formFields || Object.keys(formFields).length === 0) {
            html = '<p class="text-muted text-center">Tidak ada form field tambahan untuk kategori ini</p>';
        } else {
             // --- FORM PERJALANAN DINAS YANG DIPERBAHARUI ---
             // Penambahan style="text-align: right;" pada input currency
            html += `
                <style>
                    .currency-input { text-align: right; }
                </style>
                <div class="perjalanan-dinas-form">
                    <div class="card mb-4">
                        <div class="card-header text-white">
                            <h6 class="mb-0 text-primary">PT. GONDOWANGI TRADISIONAL KOSMETIKA</h6>
                            <h6 class="mb-0 text-primary">PENGAJUAN BIAYA PERJALANAN DINAS</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required-field">Nama</label>
                                    <input type="text" class="form-control" name="form_data[nama_karyawan]" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Periode</label>
                                    <input type="text" class="form-control" name="form_data[periode]" placeholder="Generate Otomatis" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label required-field">Area</label>
                                    <input type="text" class="form-control" name="form_data[area]" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header text-white">
                            <h6 class="mb-0 text-primary">A. BIAYA YANG DIPERLUKAN</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0 perjalanan-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th rowspan="2" style="vertical-align: middle; width: 40px;">#</th>
                                            <th rowspan="2" style="vertical-align: middle; min-width: 150px;">URAIAN</th>
                                            <th colspan="3" class="text-center">PERJALANAN</th>
                                            <th rowspan="2" style="vertical-align: middle; width: 120px;" class="text-center">TOTAL</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">
                                                <div class="fw-bold mb-2">Perjalanan 1</div>
                                                <div class="horizontal-date-container">
                                                    <div class="horizontal-group">
                                                        <div class="form-group"><label class="form-label">Dari:</label><input type="date" class="form-control form-control-sm date-from" name="form_data[perjalanan1_tanggal_dari]" data-row="1"></div>
                                                        <div class="form-group"><label class="form-label">Sampai:</label><input type="date" class="form-control form-control-sm date-to" name="form_data[perjalanan1_tanggal_sampai]" data-row="1"></div>
                                                    </div>
                                                </div>
                                                <div class="mt-2"><small class="text-muted"><span id="days_perjalanan_1">0 hari</span> | <span id="nights_perjalanan_1">0 malam</span></small></div>
                                            </th>
                                            <th class="text-center">
                                                <div class="fw-bold mb-2">Perjalanan 2</div>
                                                <div class="horizontal-date-container">
                                                    <div class="horizontal-group">
                                                        <div class="form-group"><label class="form-label">Dari:</label><input type="date" class="form-control form-control-sm date-from" name="form_data[perjalanan2_tanggal_dari]" data-row="2"></div>
                                                        <div class="form-group"><label class="form-label">Sampai:</label><input type="date" class="form-control form-control-sm date-to" name="form_data[perjalanan2_tanggal_sampai]" data-row="2"></div>
                                                    </div>
                                                </div>
                                                <div class="mt-2"><small class="text-muted"><span id="days_perjalanan_2">0 hari</span> | <span id="nights_perjalanan_2">0 malam</span></small></div>
                                            </th>
                                            <th class="text-center">
                                                <div class="fw-bold mb-2">Perjalanan 3</div>
                                                <div class="horizontal-date-container">
                                                    <div class="horizontal-group">
                                                        <div class="form-group"><label class="form-label">Dari:</label><input type="date" class="form-control form-control-sm date-from" name="form_data[perjalanan3_tanggal_dari]" data-row="3"></div>
                                                        <div class="form-group"><label class="form-label">Sampai:</label><input type="date" class="form-control form-control-sm date-to" name="form_data[perjalanan3_tanggal_sampai]" data-row="3"></div>
                                                    </div>
                                                </div>
                                                <div class="mt-2"><small class="text-muted"><span id="days_perjalanan_3">0 hari</span> | <span id="nights_perjalanan_3">0 malam</span></small></div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td class="text-center">1</td><td><strong>TRANSPORTASI</strong></td><td></td><td></td><td></td><td></td></tr>
                                        <tr><td></td><td class="ps-4">a. Darat</td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[transportasi_darat]" data-row="transportasi_darat" data-col="1" placeholder="0"></div></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[transportasi_darat_2]" data-row="transportasi_darat" data-col="2" placeholder="0"></div></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[transportasi_darat_3]" data-row="transportasi_darat" data-col="3" placeholder="0"></div></td>
                                            <td class="total-cell" id="total_transportasi_darat">Rp 0</td>
                                        </tr>
                                        <tr><td></td><td class="ps-4">b. Udara</td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[transportasi_udara_1]" data-row="transportasi_udara" data-col="1" placeholder="0"></div></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[transportasi_udara_2]" data-row="transportasi_udara" data-col="2" placeholder="0"></div></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[transportasi_udara_3]" data-row="transportasi_udara" data-col="3" placeholder="0"></div></td>
                                            <td class="total-cell" id="total_transportasi_udara">Rp 0</td>
                                        </tr>
                                        <tr><td></td><td class="ps-4">c. Airport Tax</td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[transportasi_taxi]" data-row="transportasi_taxi" data-col="1" placeholder="0"></div></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[transportasi_taxi_2]" data-row="transportasi_taxi" data-col="2" placeholder="0"></div></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[transportasi_taxi_3]" data-row="transportasi_taxi" data-col="3" placeholder="0"></div></td>
                                            <td class="total-cell" id="total_transportasi_taxi">Rp 0</td>
                                        </tr>
                                        <tr><td class="text-center">2</td><td><strong>HOTEL</strong> <small class="text-muted">(per malam)</small></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input hotel-rate-input" name="form_data[hotel_biaya]" data-row="hotel_biaya" data-col="1" value="0" readonly></div></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input hotel-rate-input" name="form_data[hotel_biaya_2]" data-row="hotel_biaya" data-col="2" value="0" readonly></div></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input hotel-rate-input" name="form_data[hotel_biaya_3]" data-row="hotel_biaya" data-col="3" value="0" readonly></div></td>
                                            <td class="total-cell" id="total_hotel_biaya">Rp 0</td>
                                        </tr>
                                        <tr><td class="text-center">3</td><td><strong>MAKAN</strong> <small class="text-muted">(per hari)</small></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input makan-rate-input" name="form_data[makan_biaya]" data-row="makan_biaya" data-col="1" value="0" readonly></div></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input makan-rate-input" name="form_data[makan_biaya_2]" data-row="makan_biaya" data-col="2" value="0" readonly></div></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input makan-rate-input" name="form_data[makan_biaya_3]" data-row="makan_biaya" data-col="3" value="0" readonly></div></td>
                                            <td class="total-cell" id="total_makan_biaya">Rp 0</td>
                                        </tr>
                                        <tr><td class="text-center">4</td><td><strong>UANG SAKU</strong></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[uang_saku]" data-row="uang_saku" data-col="1" placeholder="0"></div></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[uang_saku_2]" data-row="uang_saku" data-col="2" placeholder="0"></div></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[uang_saku_3]" data-row="uang_saku" data-col="3" placeholder="0"></div></td>
                                            <td class="total-cell" id="total_uang_saku">Rp 0</td>
                                        </tr>
                                        <tr><td class="text-center">5</td><td><strong>TELEPHONE & FAX</strong></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[telephone_fax]" data-row="telephone_fax" data-col="1" placeholder="0"></div></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[telephone_fax_2]" data-row="telephone_fax" data-col="2" placeholder="0"></div></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[telephone_fax_3]" data-row="telephone_fax" data-col="3" placeholder="0"></div></td>
                                            <td class="total-cell" id="total_telephone_fax">Rp 0</td>
                                        </tr>
                                        <tr><td class="text-center">6</td><td><strong>ENTERTAINMENT</strong></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[entertainment]" data-row="entertainment" data-col="1" placeholder="0"></div></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[entertainment_2]" data-row="entertainment" data-col="2" placeholder="0"></div></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[entertainment_3]" data-row="entertainment" data-col="3" placeholder="0"></div></td>
                                            <td class="total-cell" id="total_entertainment">Rp 0</td>
                                        </tr>
                                        <tr><td class="text-center">7</td><td><strong>DOKUMENTASI</strong></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[dokumentasi]" data-row="dokumentasi" data-col="1" placeholder="0"></div></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[dokumentasi_2]" data-row="dokumentasi" data-col="2" placeholder="0"></div></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[dokumentasi_3]" data-row="dokumentasi" data-col="3" placeholder="0"></div></td>
                                            <td class="total-cell" id="total_dokumentasi">Rp 0</td>
                                        </tr>
                                        <tr><td class="text-center">8</td><td><strong>LAIN-LAIN</strong></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[lain_lain]" data-row="lain_lain" data-col="1" placeholder="0"></div></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[lain_lain_2]" data-row="lain_lain" data-col="2" placeholder="0"></div></td>
                                            <td><div class="input-group input-group-sm"><input type="text" class="form-control currency-input perjalanan-input" name="form_data[lain_lain_3]" data-row="lain_lain" data-col="3" placeholder="0"></div></td>
                                            <td class="total-cell" id="total_lain_lain">Rp 0</td>
                                        </tr>
                                        <tr class="table-primary">
                                            <td colspan="2" class="text-center"><strong>TOTAL</strong></td>
                                            <td class="text-center" id="total_perjalanan_1"><strong>Rp 0</strong></td>
                                            <td class="text-center" id="total_perjalanan_2"><strong>Rp 0</strong></td>
                                            <td class="text-center" id="total_perjalanan_3"><strong>Rp 0</strong></td>
                                            <td class="text-center total-grand" id="grand_total"><strong>Rp 0</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header text-white">
                            <h6 class="mb-0 text-primary">B. TUJUAN PERJALANAN</h6>
                        </div>
                        <div class="card-body">
                            <textarea class="form-control" name="form_data[tujuan_perjalanan]" rows="3" placeholder="Jelaskan tujuan perjalanan dinas..." required></textarea>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header text-white">
                            <h6 class="mb-0 text-primary">DETAIL PERJALANAN</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th rowspan="2" style="vertical-align: middle; width: 40px;" class="text-center">NO</th>
                                            <th rowspan="2" style="vertical-align: middle; min-width: 150px;" class="text-center">DAERAH</th>
                                            <th rowspan="2" style="vertical-align: middle; min-width: 130px;" class="text-center">SALES RATE - RATA PER BULAN</th>
                                            <th rowspan="2" style="vertical-align: middle; min-width: 130px;" class="text-center">ESTIMASI SALES</th>
                                            <th rowspan="2" style="vertical-align: middle; min-width: 130px;" class="text-center">JUMLAH OUTLET YG AKAN DIKUNJUNGI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td class="text-center">1</td>
                                            <td><input type="text" class="form-control form-control-sm" name="form_data[perjalanan1_daerah]" placeholder="Nama daerah"></td>
                                            <td><div class="input-group input-group-sm"><span class="input-group-text">Rp</span><input type="text" class="form-control currency-input sales-rate-input" name="form_data[perjalanan1_sales_rate]" data-row="1" placeholder="0"></div></td>
                                            <td><div class="input-group input-group-sm"><span class="input-group-text">Rp</span><input type="text" class="form-control currency-input estimasi-sales-input" name="form_data[perjalanan1_estimasi]" data-row="1" placeholder="0"></div></td>
                                            <td><input type="number" class="form-control form-control-sm outlet-input" name="form_data[perjalanan1_outlet]" data-row="1" placeholder="0" min="0"></td>
                                        </tr>
                                        <tr><td class="text-center">2</td>
                                            <td><input type="text" class="form-control form-control-sm" name="form_data[perjalanan2_daerah]" placeholder="Nama daerah"></td>
                                            <td><div class="input-group input-group-sm"><span class="input-group-text">Rp</span><input type="text" class="form-control currency-input sales-rate-input" name="form_data[perjalanan2_sales_rate]" data-row="2" placeholder="0"></div></td>
                                            <td><div class="input-group input-group-sm"><span class="input-group-text">Rp</span><input type="text" class="form-control currency-input estimasi-sales-input" name="form_data[perjalanan2_estimasi]" data-row="2" placeholder="0"></div></td>
                                            <td><input type="number" class="form-control form-control-sm outlet-input" name="form_data[perjalanan2_outlet]" data-row="2" placeholder="0" min="0"></td>
                                        </tr>
                                        <tr><td class="text-center">3</td>
                                            <td><input type="text" class="form-control form-control-sm" name="form_data[perjalanan3_daerah]" placeholder="Nama daerah"></td>
                                            <td><div class="input-group input-group-sm"><span class="input-group-text">Rp</span><input type="text" class="form-control currency-input sales-rate-input" name="form_data[perjalanan3_sales_rate]" data-row="3" placeholder="0"></div></td>
                                            <td><div class="input-group input-group-sm"><span class="input-group-text">Rp</span><input type="text" class="form-control currency-input estimasi-sales-input" name="form_data[perjalanan3_estimasi]" data-row="3" placeholder="0"></div></td>
                                            <td><input type="number" class="form-control form-control-sm outlet-input" name="form_data[perjalanan3_outlet]" data-row="3" placeholder="0" min="0"></td>
                                        </tr>
                                        <tr class="table-primary">
                                            <td colspan="2" class="text-center"><strong>TOTAL</strong></td>
                                            <td class="text-center" id="total_sales_rate"><strong>Rp 0</strong></td>
                                            <td class="text-center" id="total_estimasi_sales"><strong>Rp 0</strong></td>
                                            <td class="text-center" id="total_outlets"><strong>0</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        container.innerHTML = html;

        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        initializePerjalananDinasEnhancements();

    }

    function initializePerjalananDinasEnhancements() {
        setTimeout(() => {
            document.querySelectorAll('.hotel-rate-input, .makan-rate-input').forEach(input => {
                // Format initial value jika ada
                if(input.value) {
                     input.value = formatInputCurrency(input.value);
                }
            });
        }, 100);

        document.querySelectorAll('input[type="date"]').forEach(input => {
            input.removeEventListener('change', handleDateInputChange); 
            input.addEventListener('change', handleDateInputChange);
            input.removeEventListener('blur', handleDateInputBlur); 
            input.addEventListener('blur', handleDateInputBlur);
        });

        function handleDateInputChange() {
            const row = parseInt(this.getAttribute('data-row'));
            enforceDateDependency();
            updateDateDisplay(row);
            enforceUraianDependency();
        }
        
        function handleDateInputBlur() {
            const row = parseInt(this.getAttribute('data-row'));
            enforceDateDependency();
            updateDateDisplay(row);
            enforceUraianDependency();
        }
        
        // Add event listeners untuk kalkulasi total
        document.querySelectorAll('.perjalanan-input, .hotel-rate-input, .makan-rate-input').forEach(input => {
            // Ubah ke 'input' event agar realtime saat mengetik
            input.addEventListener('input', calculateGrandTotal);
        });

        document.querySelectorAll('.sales-rate-input, .estimasi-sales-input, .outlet-input').forEach(input => {
            input.addEventListener('input', calculateDetailTotals);
        });

        // --- EVENT LISTENER FORMAT RUPIAH SAAT MENGETIK ---
        document.querySelectorAll('.currency-input').forEach(input => {
            input.addEventListener('input', function(e) {
                // Simpan posisi cursor sebelum format
                let cursorPosition = this.selectionStart;
                let oldLength = this.value.length;
                
                this.value = formatInputCurrency(this.value);
                
                // Kembalikan posisi cursor (opsional, agar lebih nyaman)
                let newLength = this.value.length;
                this.selectionEnd = cursorPosition + (newLength - oldLength);
            });
        });

        setTimeout(() => {
            initializeDateValidation(); 
            enforceDateDependency();
            enforceUraianDependency();
            calculateGrandTotal();
            calculateDetailTotals();
        }, 200);
    }
    
    function initializeDateValidation() {
         const today = new Date();
         const todayString = today.toISOString().split('T')[0];
         document.querySelectorAll('input[type="date"]').forEach(input => {
             input.min = todayString;
             input.addEventListener('change', function() {
                 const row = parseInt(this.getAttribute('data-row'));
                 const isFrom = this.classList.contains('date-from');
                 const otherDateInput = isFrom ? document.querySelector(`.date-to[data-row="${row}"]`) : document.querySelector(`.date-from[data-row="${row}"]`);
                 this.setCustomValidity('');
                 if (otherDateInput) {
                     if (isFrom && otherDateInput.value && this.value > otherDateInput.value) {
                         showDateError(otherDateInput, 'Tgl selesai harus setelah Tgl mulai');
                     } else if (!isFrom && otherDateInput.value && this.value < otherDateInput.value) {
                          showDateError(this, 'Tgl selesai harus setelah Tgl mulai');
                      } else {
                          clearDateError(this);
                          clearDateError(otherDateInput);
                      }
                 }
             });
         });
    }

    function calculateTotalNominal() {
        const grandTotalElement = document.getElementById('grand_total');
        if (grandTotalElement) {
             // Ambil teks, buang "Rp", titik, spasi, dll -> Sisakan angka saja
             const text = grandTotalElement.textContent.replace(/[^\d]/g, '');
             return parseInt(text) || 0;
        }
        return 0;
    }

    function initializeFileUpload() {
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileInput = document.getElementById('fileInput');
        if (fileUploadArea && fileInput) {
            fileUploadArea.addEventListener('click', () => fileInput.click());
            fileInput.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                selectedFiles = [...selectedFiles, ...files];
                updateFileList();
            });
            fileUploadArea.addEventListener('dragover', function(e) {
                e.preventDefault(); fileUploadArea.classList.add('dragover');
            });
            fileUploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault(); fileUploadArea.classList.remove('dragover');
            });
            fileUploadArea.addEventListener('drop', function(e) {
                e.preventDefault(); fileUploadArea.classList.remove('dragover');
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
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})"><i class="fa fa-trash"></i></button>
                </div>`;
        });
        fileList.innerHTML = html;
        if (typeof feather !== 'undefined') feather.replace();
    }

    window.removeFile = function(index) {
        selectedFiles.splice(index, 1);
        updateFileList();
    }


    // --- Submission Logic ---
    async function submitForm(action) {
        const form = document.getElementById('pengajuanForm');
        const formData = new FormData(form);
        
        // Final check sebelum submit
        if (action === 'submit') {
            if (!enforceDateDependency()) {
                showAlert('danger', 'Validasi tanggal gagal. Periksa kembali urutan dan rentang tanggal perjalanan.');
                return;
            }
            enforceUraianDependency(); 
        }
        
        // PENTING: Bersihkan format rupiah (hapus titik) sebelum dikirim ke backend
        document.querySelectorAll('.currency-input').forEach(input => {
            // Hapus titik agar menjadi angka murni (1000000)
            const rawValue = input.value.toString().replace(/\./g, '').replace(/[^0-9]/g, ''); 
            
            if (rawValue) {
                const name = input.name;
                if (name) {
                    formData.set(name, rawValue);
                }
            }
        });

        const totalNominal = calculateTotalNominal();
        formData.append('calculated_nominal', totalNominal);
        formData.append('submit_action', action);

        const saveDraftBtn = document.getElementById('saveDraftBtn');
        const submitBtn = document.getElementById('submitBtn');

        saveDraftBtn.disabled = true;
        submitBtn.disabled = true;

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

                if (action === 'submit') {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('pengajuanModal'));
                    if (modal) {
                        modal.hide();
                    }

                    setTimeout(() => {
                        window.location.href = '/BuatPengajuan';
                    }, 1500);
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
            saveDraftBtn.disabled = false;
            submitBtn.disabled = false;
            saveDraftBtn.innerHTML = originalSaveDraftText;
            submitBtn.innerHTML = originalSubmitText;

            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }
    }
    
    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        if (!alertContainer) {
            return;
        }

        const alertId = 'alert-' + Date.now();
        let displayMessage = message;
        if (type === 'success' && message.toLowerCase().includes('berhasil')) {
            displayMessage = message + '<br><small class="text-muted">Anda akan diarahkan ke halaman kategori pengajuan...</small>';
        }

        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert" id="${alertId}">
                <i data-feather="${type === 'success' ? 'check-circle' : 'alert-circle'}" class="me-2"></i>
                ${displayMessage}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        alertContainer.innerHTML = alertHtml;

        if (typeof feather !== 'undefined') {
            feather.replace();
        }

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


    // --- DOM Content Loaded ---

    document.addEventListener('DOMContentLoaded', function() {
        
        document.addEventListener('click', function(e) {
            const button = e.target.closest('.btn-create'); 
            if (button) {
                 const card = button.closest('.card-modern');
                 if (card) {
                    const kategoriId = card.getAttribute('data-kategori-id');
                    const kategoriNama = card.getAttribute('data-kategori-nama');
                    
                    if (kategoriId && kategoriNama) {
                        selectKategori(kategoriId, kategoriNama);
                    } 
                 }
            }
        });

        initializeFileUpload();

        document.getElementById('saveDraftBtn').addEventListener('click', function() {
            submitForm('draft');
        });

        document.getElementById('submitBtn').addEventListener('click', function() {
            submitForm('submit');
        });

        const modalElement = document.getElementById('pengajuanModal');
        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', resetFormAndData);
        }
        
        const maximizeDetailBtn = document.getElementById('maximizeDetailBtn');
        const detailPengajuanSection = document.getElementById('detailPengajuanSection');

        maximizeDetailBtn.addEventListener('click', function() {
            if (detailPengajuanSection.classList.contains('detail-expanded')) {
                detailPengajuanSection.classList.remove('detail-expanded');
                detailPengajuanSection.classList.add('detail-minimized');
                maximizeDetailBtn.innerHTML = '<i data-feather="maximize-2"></i> Perbesar Detail';
            } else {
                detailPengajuanSection.classList.remove('detail-minimized');
                detailPengajuanSection.classList.add('detail-expanded');
                maximizeDetailBtn.innerHTML = '<i data-feather="minimize-2"></i> Perkecil Detail';
            }

            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });

        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        // Panggil inisialisasi awal
        initializePerjalananDinasEnhancements();
    });
</script>
@endsection