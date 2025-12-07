@extends('Approval-app.Layout.main-admin')

@section('head')
<style>
.form-preview {
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 30px;
    margin: 20px 0;
}

.preview-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 8px 8px 0 0;
    margin: -30px -30px 30px -30px;
}

.field-info {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    padding: 8px 12px;
    margin-bottom: 10px;
    font-size: 12px;
    color: #6c757d;
}

.currency-input {
    position: relative;
}

.currency-input::before {
    content: 'Rp';
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    z-index: 5;
}

.currency-input input {
    padding-left: 35px;
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
                    <h5 class="m-b-10">Preview Form: {{ $kategori->nama }}</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Help Desk</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kelola-form-pengajuan.index') }}">Kelola Form Pengajuan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Preview Form</li>
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Preview Form Pengajuan</h5>
                <div>
                    <button class="btn btn-outline-info btn-sm me-2" onclick="toggleFieldInfo()">
                        <i class="fas fa-info-circle me-1"></i>Toggle Info
                    </button>
                    <a href="{{ route('kelola-form-pengajuan.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($kategori->formFields->count() > 0)
                <div class="form-preview">
                    <div class="preview-header text-center">
                        <div class="mb-2">
                            <i class="{{ $kategori->icon ?? 'fas fa-file-alt' }}" style="font-size: 2em;"></i>
                        </div>
                        <h4 class="mb-1">{{ $kategori->nama }}</h4>
                        <p class="mb-0 opacity-75">{{ $kategori->deskripsi }}</p>
                    </div>

                    <form id="previewForm">
                        @php
                            $groupedFields = $kategori->formFields->groupBy('posisi_row');
                        @endphp

                        @foreach($groupedFields as $rowNumber => $fieldsInRow)
                            <div class="row mb-3">
                                @foreach($fieldsInRow->sortBy('posisi_col') as $field)
                                    <div class="col-md-{{ $field->lebar_col }}">
                                        <div class="field-info" style="display: none;">
                                            <strong>{{ $field->nama_field }}</strong> | 
                                            Tipe: {{ $field->tipe_field }} | 
                                            Row: {{ $field->posisi_row }} | 
                                            Col: {{ $field->posisi_col }} | 
                                            Urutan: {{ $field->urutan }}
                                            @if($field->wajib) | <span class="text-danger">Wajib</span> @endif
                                        </div>

                                        <div class="mb-3">
                                            <label for="{{ $field->nama_field }}" class="form-label">
                                                {{ $field->label }}
                                                @if($field->wajib)
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </label>

                                            @switch($field->tipe_field)
                                                @case('text')
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="{{ $field->nama_field }}" 
                                                           name="{{ $field->nama_field }}"
                                                           placeholder="{{ $field->placeholder }}"
                                                           {{ $field->wajib ? 'required' : '' }}>
                                                    @break

                                                @case('textarea')
                                                    <textarea class="form-control" 
                                                              id="{{ $field->nama_field }}" 
                                                              name="{{ $field->nama_field }}"
                                                              placeholder="{{ $field->placeholder }}"
                                                              rows="4"
                                                              {{ $field->wajib ? 'required' : '' }}></textarea>
                                                    @break

                                                @case('number')
                                                    <input type="number" 
                                                           class="form-control" 
                                                           id="{{ $field->nama_field }}" 
                                                           name="{{ $field->nama_field }}"
                                                           placeholder="{{ $field->placeholder }}"
                                                           {{ $field->wajib ? 'required' : '' }}>
                                                    @break

                                                @case('date')
                                                    <input type="date" 
                                                           class="form-control" 
                                                           id="{{ $field->nama_field }}" 
                                                           name="{{ $field->nama_field }}"
                                                           {{ $field->wajib ? 'required' : '' }}>
                                                    @break

                                                @case('currency')
                                                    <div class="currency-input">
                                                        <input type="text" 
                                                               class="form-control currency-format" 
                                                               id="{{ $field->nama_field }}" 
                                                               name="{{ $field->nama_field }}"
                                                               placeholder="{{ $field->placeholder ?: '0' }}"
                                                               {{ $field->wajib ? 'required' : '' }}>
                                                    </div>
                                                    @break

                                                @case('select')
                                                    <select class="form-select" 
                                                            id="{{ $field->nama_field }}" 
                                                            name="{{ $field->nama_field }}"
                                                            {{ $field->wajib ? 'required' : '' }}>
                                                        <option value="">{{ $field->placeholder ?: 'Pilih...' }}</option>
                                                        @if($field->opsi)
                                                            @foreach($field->opsi as $opsi)
                                                                <option value="{{ $opsi }}">{{ $opsi }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    @break

                                                @case('radio')
                                                    @if($field->opsi)
                                                        @foreach($field->opsi as $index => $opsi)
                                                            <div class="form-check">
                                                                <input class="form-check-input" 
                                                                       type="radio" 
                                                                       name="{{ $field->nama_field }}" 
                                                                       id="{{ $field->nama_field }}_{{ $index }}"
                                                                       value="{{ $opsi }}"
                                                                       {{ $field->wajib ? 'required' : '' }}>
                                                                <label class="form-check-label" for="{{ $field->nama_field }}_{{ $index }}">
                                                                    {{ $opsi }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                    @break

                                                @case('checkbox')
                                                    @if($field->opsi)
                                                        @foreach($field->opsi as $index => $opsi)
                                                            <div class="form-check">
                                                                <input class="form-check-input" 
                                                                       type="checkbox" 
                                                                       name="{{ $field->nama_field }}[]" 
                                                                       id="{{ $field->nama_field }}_{{ $index }}"
                                                                       value="{{ $opsi }}">
                                                                <label class="form-check-label" for="{{ $field->nama_field }}_{{ $index }}">
                                                                    {{ $opsi }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                    @break

                                                @case('file')
                                                    <input type="file" 
                                                           class="form-control" 
                                                           id="{{ $field->nama_field }}" 
                                                           name="{{ $field->nama_field }}"
                                                           {{ $field->wajib ? 'required' : '' }}>
                                                    @if($field->placeholder)
                                                        <div class="form-text">{{ $field->placeholder }}</div>
                                                    @endif
                                                    @break

                                                @default
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="{{ $field->nama_field }}" 
                                                           name="{{ $field->nama_field }}"
                                                           placeholder="{{ $field->placeholder }}"
                                                           {{ $field->wajib ? 'required' : '' }}>
                                            @endswitch

                                            @if($field->validasi && count($field->validasi) > 0)
                                                <div class="form-text">
                                                    <small class="text-muted">
                                                        Validasi: {{ implode(', ', $field->validasi) }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        <div class="row">
                            <div class="col-12">
                                <hr class="my-4">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-outline-secondary me-2">
                                        <i class="fas fa-times me-2"></i>Batal
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Submit Pengajuan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Info Panel -->
                <div class="alert alert-info">
                    <h6 class="alert-heading">
                        <i class="fas fa-info-circle me-2"></i>Informasi Preview
                    </h6>
                    <ul class="mb-0">
                        <li>Ini adalah preview form yang akan dilihat oleh user ketika membuat pengajuan</li>
                        <li>Total {{ $kategori->formFields->count() }} field yang dikonfigurasi</li>
                        <li>Field wajib ditandai dengan tanda asterisk (*) merah</li>
                        <li>Layout menggunakan sistem grid Bootstrap dengan {{ $groupedFields->count() }} baris</li>
                        <li>Klik tombol "Toggle Info" untuk melihat informasi teknis setiap field</li>
                    </ul>
                </div>

                @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-file-invoice fa-4x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Belum ada form field</h5>
                    <p class="text-muted">Silakan tambah form field terlebih dahulu untuk kategori {{ $kategori->nama }}</p>
                    <a href="{{ route('kelola-form-pengajuan.create') }}?kategori={{ $kategori->id }}" 
                       class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Form Field
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Currency formatting
    $('.currency-format').on('input', function() {
        let value = $(this).val().replace(/[^\d]/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
            $(this).val(value);
        }
    });

    // Preview form submission (just for demo)
    $('#previewForm').on('submit', function(e) {
        e.preventDefault();
        
        // Collect form data
        const formData = new FormData(this);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            if (data[key]) {
                if (Array.isArray(data[key])) {
                    data[key].push(value);
                } else {
                    data[key] = [data[key], value];
                }
            } else {
                data[key] = value;
            }
        }
        
        // Show preview data
        let dataPreview = '<h6>Data yang akan dikirim:</h6><pre>' + JSON.stringify(data, null, 2) + '</pre>';
        
        // Create modal or alert
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Preview Data Pengajuan',
                html: dataPreview,
                icon: 'info',
                width: '600px',
                confirmButtonText: 'OK'
            });
        } else {
            alert('Form berhasil divalidasi! Data:\n' + JSON.stringify(data, null, 2));
        }
    });
});

function toggleFieldInfo() {
    $('.field-info').toggle();
}
</script>
@endsection