@extends('Approval-app.Layout.main-admin')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="row">
    <!-- Main Content -->
    <div class="col-12 mt-0">
        <div class="page-header mb-0">
            <div class="page-block bg-white rounded shadow">
                <div class="row align-items-center p-3">
                    <div class="col-md-8">
                        <div class="page-header-title">
                            <h4 class="mb-3 fw-bold">
                                Kelola Kategori Pengajuan
                            </h4>
                            <p class="mb-0 opacity-75">Mengelola kategori pengajuan untuk sistem approval</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-primary" id="btnTambah">
                            <i class="fas fa-plus me-2"></i>Tambah Kategori
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list-alt text-primary me-2"></i>
                    Daftar Kategori Pengajuan
                </h5>
            </div>
            <div class="card-body">
                <!-- Table -->
                <div class="table-responsive">
                    <table id="tableKategori" class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Kode</th>
                                <th width="20%">Nama</th>
                                <th width="25%">Deskripsi</th>
                                <th width="8%">Icon</th>
                                <th width="8%">Warna</th>
                                <th width="10%">Status</th>
                                <th width="9%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormLabel">
                    <i class="fas fa-plus-circle text-primary me-2"></i>
                    Tambah Kategori
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formKategori">
                <div class="modal-body">
                    <div class="row">
                        <!-- Nama Kategori -->
                        <div class="col-md-6 mb-3">
                            <label for="nama" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama kategori" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Kode Kategori -->
                        <div class="col-md-6 mb-3">
                            <label for="kode" class="form-label">Kode Kategori <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kode" name="kode" placeholder="Contoh: REQ001" required>
                            <div class="form-text">Kode harus unik dan akan otomatis menjadi huruf besar</div>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Deskripsi -->
                        <!--<div class="col-12 mb-3">-->
                        <!--    <label for="deskripsi" class="form-label">Deskripsi</label>-->
                        <!--    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Masukkan deskripsi kategori"></textarea>-->
                        <!--    <div class="invalid-feedback"></div>-->
                        <!--</div>-->

                        <!-- Icon & Warna -->
                        <div class="col-md-6 mb-3">
                            <label for="icon" class="form-label">Icon (FontAwesome)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i id="iconPreview" class="fas fa-file"></i></span>
                                <input type="text" class="form-control" id="icon" name="icon" value="fas fa-file" placeholder="fas fa-file">
                                <a href="https://fontawesome.com/icons" target="_blank" class="input-group-text" title="Lihat daftar icon">
                                    Lihat Detail
                                </a>
                            </div>
                            <div class="form-text">Gunakan class FontAwesome, contoh: fas fa-file, fas fa-user</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="warna" class="form-label">Warna</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="warna" name="warna" value="#007bff">
                                <input type="text" class="form-control" id="warnaText" placeholder="#007bff">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Status & Settlement -->
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Non-Aktif</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Settlement</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="settlement" name="settlement" value="1">
                                <label class="form-check-label" for="settlement">
                                    Memerlukan Settlement
                                </label>
                            </div>
                            <div class="form-text">Centang jika kategori ini memerlukan proses settlement</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSimpan">
                        <i class="fas fa-save me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

<script>
$(document).ready(function() {
    // Global Variables
    let table;
    let isEdit = false;
    let editId = null;

    // Initialize DataTable
    function initDataTable() {
        table = $('#tableKategori').DataTable({
            processing: true,
            ajax: {
                url: "{{ route('admin.kategori.api.index') }}",
                type: "GET",
                dataSrc: function(json) {
                    if (!json.success) {
                        Swal.fire('Error!', json.message || 'Gagal memuat data', 'error');
                        return [];
                    }
                    return json.data;
                }
            },
            columns: [
                {
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { 
                    data: 'kode',
                    render: function(data) {
                        return '<span class="badge bg-secondary">' + data + '</span>';
                    }
                },
                { data: 'nama' },
                { 
                    data: 'deskripsi',
                    render: function(data) {
                        return data ? (data.length > 50 ? data.substring(0, 50) + '...' : data) : '-';
                    }
                },
                {
                    data: 'icon',
                    render: function(data, type, row) {
                        return '<i class="' + data + '" style="color: ' + row.warna + ';"></i>';
                    },
                    className: 'text-center'
                },
                {
                    data: 'warna',
                    render: function(data) {
                        return '<div class="d-flex align-items-center">' +
                               '<div class="color-preview me-2" style="width: 20px; height: 20px; background-color: ' + data + '; border: 1px solid #ddd; border-radius: 3px;"></div>' +
                               '<small>' + data + '</small>' +
                               '</div>';
                    }
                },
                {
                    data: 'status',
                    render: function(data, type, row) {
                        let badgeClass = data === 'aktif' ? 'bg-success' : 'bg-danger';
                        let settlementBadge = row.settlement ? '<span class="badge bg-info ms-1">Settlement</span>' : '';
                        return '<span class="badge ' + badgeClass + '">' + data.toUpperCase() + '</span>' + settlementBadge;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return '<div class="btn-group" role="group">' +
                               '<button type="button" class="btn btn-sm btn-outline-primary" onclick="editKategori(' + row.id + ')" title="Edit">' +
                               '<i class="fas fa-edit"></i>' +
                               '</button>' +
                               '<button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteKategori(' + row.id + ', \'' + row.nama + '\')" title="Hapus">' +
                               '<i class="fas fa-trash"></i>' +
                               '</button>' +
                               '</div>';
                    },
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                }
            ],
            order: [[2, 'asc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            responsive: true
        });
    }

    // Initialize page
    initDataTable();

    // Event Handlers
    $('#btnTambah').click(function() {
        resetForm();
        isEdit = false;
        editId = null;
        $('#modalFormLabel').html('<i class="fas fa-plus-circle text-primary me-2"></i>Tambah Kategori');
        $('#modalForm').modal('show');
    });

    // Icon preview
    $('#icon').on('input', function() {
        let iconClass = $(this).val() || 'fas fa-file';
        $('#iconPreview').attr('class', iconClass);
    });

    // Color sync
    $('#warna').change(function() {
        $('#warnaText').val($(this).val());
    });

    $('#warnaText').on('input', function() {
        let color = $(this).val();
        if (/^#[0-9A-F]{6}$/i.test(color)) {
            $('#warna').val(color);
        }
    });

    // Form submission
    $('#formKategori').submit(function(e) {
        e.preventDefault();
        
        let formData = {
            nama: $('#nama').val(),
            kode: $('#kode').val().toUpperCase(),
            deskripsi: $('#deskripsi').val(),
            icon: $('#icon').val() || 'fas fa-file',
            warna: $('#warna').val(),
            status: $('#status').val(),
            settlement: $('#settlement').is(':checked') ? 1 : 0,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        let url = isEdit ? "{{ url('/admin/api/kategori-pengajuan') }}/" + editId : "{{ route('admin.kategori.api.store') }}";
        let method = isEdit ? 'PUT' : 'POST';

        // Disable button
        $('#btnSimpan').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...');

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire('Berhasil!', response.message, 'success');
                    $('#modalForm').modal('hide');
                    table.ajax.reload();
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors || {};
                let message = xhr.responseJSON?.message || 'Terjadi kesalahan';
                
                // Clear previous errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                // Show field errors
                $.each(errors, function(field, messages) {
                    $('#' + field).addClass('is-invalid');
                    $('#' + field).siblings('.invalid-feedback').text(messages[0]);
                });

                if (Object.keys(errors).length === 0) {
                    Swal.fire('Error!', message, 'error');
                }
            },
            complete: function() {
                $('#btnSimpan').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Simpan');
            }
        });
    });

    // Global functions
    window.editKategori = function(id) {
        $.ajax({
            url: "{{ url('/admin/api/kategori-pengajuan') }}/" + id,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    let data = response.data;
                    
                    isEdit = true;
                    editId = id;
                    
                    $('#nama').val(data.nama);
                    $('#kode').val(data.kode);
                    $('#deskripsi').val(data.deskripsi);
                    $('#icon').val(data.icon);
                    $('#warna').val(data.warna);
                    $('#warnaText').val(data.warna);
                    $('#status').val(data.status);
                    $('#settlement').prop('checked', data.settlement == 1);
                    
                    // Update icon preview
                    $('#iconPreview').attr('class', data.icon);
                    
                    $('#modalFormLabel').html('<i class="fas fa-edit text-warning me-2"></i>Edit Kategori');
                    $('#modalForm').modal('show');
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Gagal mengambil data kategori', 'error');
            }
        });
    };

    window.deleteKategori = function(id, nama) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: 'Apakah Anda yakin ingin menghapus kategori <strong>"' + nama + '"</strong>?<br><small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/admin/api/kategori-pengajuan') }}/" + id,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Terhapus!', response.message, 'success');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        let message = xhr.responseJSON?.message || 'Gagal menghapus kategori';
                        Swal.fire('Error!', message, 'error');
                    }
                });
            }
        });
    };

    function resetForm() {
        $('#formKategori')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#warna').val('#007bff');
        $('#warnaText').val('#007bff');
        $('#icon').val('fas fa-file');
        $('#iconPreview').attr('class', 'fas fa-file');
        $('#settlement').prop('checked', false);
    }

    // Modal events
    $('#modalForm').on('hidden.bs.modal', function() {
        resetForm();
    });
});
</script>
@endsection