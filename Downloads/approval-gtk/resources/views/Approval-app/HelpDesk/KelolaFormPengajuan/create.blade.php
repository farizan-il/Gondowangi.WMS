@extends('Approval-app.Layout.main-admin')

@section('head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
@endsection

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Tambah Form Field</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Help Desk</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kelola-form-pengajuan.index') }}">Kelola Form Pengajuan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah Form Field</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Form Tambah Field</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <h6 class="alert-heading">Terjadi Kesalahan!</h6>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('kelola-form-pengajuan.store') }}" method="POST" id="formTambah">
                    @csrf
                    
                    <div class="row">
                        <!-- Informasi Dasar -->
                        <div class="col-lg-6">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Informasi Dasar</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="kategori_pengajuan_id" class="form-label">Kategori Pengajuan <span class="text-danger">*</span></label>
                                        <select class="form-select" id="kategori_pengajuan_id" name="kategori_pengajuan_id" required>
                                            <option value="">Pilih Kategori Pengajuan</option>
                                            @foreach($kategoriPengajuan as $kategori)
                                                <option value="{{ $kategori->id }}" 
                                                        {{ (old('kategori_pengajuan_id') == $kategori->id || request('kategori') == $kategori->id) ? 'selected' : '' }}>
                                                    {{ $kategori->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="nama_field" class="form-label">Nama Field <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nama_field" name="nama_field" 
                                               value="{{ old('nama_field') }}" placeholder="contoh: nomor_hp" required>
                                        <div class="form-text">Nama field dalam format snake_case (contoh: nomor_hp, alamat_lengkap)</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="label" class="form-label">Label <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="label" name="label" 
                                               value="{{ old('label') }}" placeholder="contoh: Nomor HP" required>
                                        <div class="form-text">Label yang akan ditampilkan di form</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="tipe_field" class="form-label">Tipe Field <span class="text-danger">*</span></label>
                                        <select class="form-select" id="tipe_field" name="tipe_field" required>
                                            <option value="">Pilih Tipe Field</option>
                                            <option value="text" {{ old('tipe_field') == 'text' ? 'selected' : '' }}>Text</option>
                                            <option value="textarea" {{ old('tipe_field') == 'textarea' ? 'selected' : '' }}>Textarea</option>
                                            <option value="number" {{ old('tipe_field') == 'number' ? 'selected' : '' }}>Number</option>
                                            <option value="date" {{ old('tipe_field') == 'date' ? 'selected' : '' }}>Date</option>
                                            <option value="select" {{ old('tipe_field') == 'select' ? 'selected' : '' }}>Select/Dropdown</option>
                                            <option value="radio" {{ old('tipe_field') == 'radio' ? 'selected' : '' }}>Radio Button</option>
                                            <option value="checkbox" {{ old('tipe_field') == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                                            <option value="file" {{ old('tipe_field') == 'file' ? 'selected' : '' }}>File Upload</option>
                                            <option value="currency" {{ old('tipe_field') == 'currency' ? 'selected' : '' }}>Currency</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="placeholder" class="form-label">Placeholder</label>
                                        <input type="text" class="form-control" id="placeholder" name="placeholder" 
                                               value="{{ old('placeholder') }}" placeholder="Masukkan placeholder...">
                                        <div class="form-text">Teks bantuan yang muncul di dalam field</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pengaturan Layout -->
                        <div class="col-lg-6">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Pengaturan Layout & Validasi</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="urutan" class="form-label">Urutan <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="urutan" name="urutan" 
                                                       value="{{ old('urutan', 1) }}" min="1" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="posisi_row" class="form-label">Posisi Row <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="posisi_row" name="posisi_row" 
                                                       value="{{ old('posisi_row', 1) }}" min="1" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="posisi_col" class="form-label">Posisi Kolom <span class="text-danger">*</span></label>
                                                <select class="form-select" id="posisi_col" name="posisi_col" required>
                                                    @for($i = 1; $i <= 12; $i++)
                                                        <option value="{{ $i }}" {{ old('posisi_col', 1) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="lebar_col" class="form-label">Lebar Kolom <span class="text-danger">*</span></label>
                                                <select class="form-select" id="lebar_col" name="lebar_col" required>
                                                    @for($i = 1; $i <= 12; $i++)
                                                        <option value="{{ $i }}" {{ old('lebar_col', 12) == $i ? 'selected' : '' }}>{{ $i }} ({{ round(($i/12)*100) }}%)</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="wajib" class="form-label">Field Wajib <span class="text-danger">*</span></label>
                                                <select class="form-select" id="wajib" name="wajib" required>
                                                    <option value="1" {{ old('wajib', 0) == 1 ? 'selected' : '' }}>Ya</option>
                                                    <option value="0" {{ old('wajib', 0) == 0 ? 'selected' : '' }}>Tidak</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                                <select class="form-select" id="status" name="status" required>
                                                    <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                                    <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Non Aktif</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Validasi Rules -->
                                    <div class="mb-3">
                                        <label class="form-label">Rules Validasi</label>
                                        <div id="validasi-container">
                                            <div class="input-group mb-2">
                                                <input type="text" class="form-control" name="validasi[]" 
                                                       placeholder="contoh: min:3, max:100, email" value="{{ old('validasi.0') }}">
                                                <button type="button" class="btn btn-outline-success btn-add-validasi">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-text">Contoh: required, min:3, max:100, email, numeric</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Opsi untuk Select/Radio/Checkbox -->
                    <div class="row" id="opsi-section" style="display: none;">
                        <div class="col-12">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Opsi untuk Select/Radio/Checkbox</h6>
                                </div>
                                <div class="card-body">
                                    <div id="opsi-container">
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="opsi[]" 
                                                   placeholder="Masukkan opsi" value="{{ old('opsi.0') }}">
                                            <button type="button" class="btn btn-outline-success btn-add-opsi">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-text">Tambahkan opsi-opsi yang tersedia untuk field select, radio, atau checkbox</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('kelola-form-pengajuan.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Kembali
                                        </a>
                                        <div>
                                            <button type="reset" class="btn btn-outline-warning me-2">
                                                <i class="fas fa-undo me-2"></i>Reset
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Simpan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2
    $('#kategori_pengajuan_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'Pilih Kategori Pengajuan'
    });

    // Show/Hide opsi section based on field type
    $('#tipe_field').on('change', function() {
        const tipeField = $(this).val();
        const opsiSection = $('#opsi-section');
        
        if (['select', 'radio', 'checkbox'].includes(tipeField)) {
            opsiSection.show();
        } else {
            opsiSection.hide();
        }
    });

    // Check on page load
    const currentType = $('#tipe_field').val();
    if (['select', 'radio', 'checkbox'].includes(currentType)) {
        $('#opsi-section').show();
    }

    // Add new validasi rule
    $(document).on('click', '.btn-add-validasi', function() {
        const container = $('#validasi-container');
        const newInput = `
            <div class="input-group mb-2">
                <input type="text" class="form-control" name="validasi[]" placeholder="contoh: min:3, max:100, email">
                <button type="button" class="btn btn-outline-danger btn-remove-validasi">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        `;
        container.append(newInput);
    });

    // Remove validasi rule
    $(document).on('click', '.btn-remove-validasi', function() {
        $(this).closest('.input-group').remove();
    });

    // Add new opsi
    $(document).on('click', '.btn-add-opsi', function() {
        const container = $('#opsi-container');
        const newInput = `
            <div class="input-group mb-2">
                <input type="text" class="form-control" name="opsi[]" placeholder="Masukkan opsi">
                <button type="button" class="btn btn-outline-danger btn-remove-opsi">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        `;
        container.append(newInput);
    });

    // Remove opsi
    $(document).on('click', '.btn-remove-opsi', function() {
        $(this).closest('.input-group').remove();
    });

    // Auto generate nama_field from label
    $('#label').on('input', function() {
        const label = $(this).val();
        const namaField = label.toLowerCase()
                              .replace(/[^a-z0-9\s]/g, '') // Remove special characters
                              .replace(/\s+/g, '_') // Replace spaces with underscore
                              .replace(/_+/g, '_') // Replace multiple underscores with single
                              .replace(/^_|_$/g, ''); // Remove leading/trailing underscores
        
        if ($('#nama_field').val() === '' || $('#nama_field').data('auto-generated')) {
            $('#nama_field').val(namaField).data('auto-generated', true);
        }
    });

    // Mark as manually edited when user types in nama_field
    $('#nama_field').on('input', function() {
        $(this).data('auto-generated', false);
    });

    // Form validation
    $('#formTambah').on('submit', function(e) {
        const tipeField = $('#tipe_field').val();
        
        // Check if opsi is required but empty
        if (['select', 'radio', 'checkbox'].includes(tipeField)) {
            const opsiInputs = $('input[name="opsi[]"]').filter(function() {
                return $(this).val().trim() !== '';
            });
            
            if (opsiInputs.length === 0) {
                e.preventDefault();
                alert('Silakan tambahkan minimal satu opsi untuk field ' + tipeField);
                return false;
            }
        }
    });
});
</script>
@endsection