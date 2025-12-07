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
                                Kelola Nonimal 
                            </h4>
                            <p class="mb-0 opacity-75">Mengelola nominal untuk sistem approval</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-primary" id="btnTambah">
                            <i class="fas fa-plus me-2"></i>Tambah Golongan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list-alt text-primary me-2"></i>
                    Daftar Kelola Nonimal
                </h5>
            </div>
            <div class="card-body">
                <!-- Table -->
                <div class="table-responsive">
                    <table id="tableKategori" class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Golongan</th>
                                <th width="20%">Biaya Hotel Permalam</th>
                                <th width="10%">Biaya Makan Perhari</th>
                                <th width="9%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($nominal as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->nama_golongan }}</td>
                                    <td>Rp {{ number_format($item->biaya_hotel_per_hari, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item->biaya_makan_per_hari, 0, ',', '.') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info btnShow" data-id="{{ $item->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning btnEdit" data-id="{{ $item->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btnDelete" data-id="{{ $item->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Data belum tersedia</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form (Tambah/Edit) -->
<div class="modal fade" id="modalForm" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formGolongan">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah/Edit Golongan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" name="_method" id="_method">

                    <div class="mb-3">
                        <label>Nama Golongan</label>
                        <input type="text" name="nama_golongan" id="nama_golongan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Biaya Hotel / Malam</label>
                        <input type="number" name="biaya_hotel_per_hari" id="biaya_hotel_per_hari" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Biaya Makan / Hari</label>
                        <input type="number" name="biaya_makan_per_hari" id="biaya_makan_per_hari" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSave">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Show -->
<div class="modal fade" id="modalShow" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Golongan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Isi detail via JS -->
            </div>
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
        // Tambah
        $("#btnTambah").click(function() {
            $("#formGolongan")[0].reset();
            $("#id").val('');
            // Reset error messages
            $(".is-invalid").removeClass("is-invalid");
            $(".invalid-feedback").remove();
            $("#modalForm").modal("show");
        });
    
        // Simpan (Tambah/Update)
        $("#formGolongan").submit(function(e) {
            e.preventDefault();
            
            // Reset previous error messages
            $(".is-invalid").removeClass("is-invalid");
            $(".invalid-feedback").remove();
            
            let id = $("#id").val();
            let url = id ? "/kelola-nominal/" + id : "/kelola-nominal";
            let method = id ? "PUT" : "POST";
    
            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function(res) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: res.message || 'Data berhasil disimpan.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        // Handle validation errors
                        let errors = xhr.responseJSON.errors;
                        
                        // Display validation errors
                        $.each(errors, function(field, messages) {
                            let inputField = $("#" + field);
                            inputField.addClass("is-invalid");
                            inputField.after('<div class="invalid-feedback">' + messages[0] + '</div>');
                        });
                        
                        // Show general error message
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terdapat kesalahan dalam input data. Silakan periksa kembali.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        console.log(xhr);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan pada server.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });
    
        // Edit
        $(".btnEdit").click(function() {
            let id = $(this).data("id");
            
            // Reset error messages
            $(".is-invalid").removeClass("is-invalid");
            $(".invalid-feedback").remove();
            
            $.get("/kelola-nominal/" + id, function(res) {
                $("#id").val(res.id);
                $("#nama_golongan").val(res.nama_golongan);
                $("#biaya_hotel_per_hari").val(res.biaya_hotel_per_hari);
                $("#biaya_makan_per_hari").val(res.biaya_makan_per_hari);
                $("#modalForm").modal("show");
            });
        });
    
        // Show
        $(".btnShow").click(function() {
            let id = $(this).data("id");
            $.get("/kelola-nominal/" + id, function(res) {
                $("#detailContent").html(`
                    <p><b>Nama Golongan:</b> ${res.nama_golongan}</p>
                    <p><b>Biaya Hotel:</b> Rp ${res.biaya_hotel_per_hari}</p>
                    <p><b>Biaya Makan:</b> Rp ${res.biaya_makan_per_hari}</p>
                `);
                $("#modalShow").modal("show");
            });
        });
    
        // Delete
        $(".btnDelete").click(function() {
            let id = $(this).data("id");
            Swal.fire({
                title: "Yakin?",
                text: "Data akan dihapus permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/kelola-nominal/" + id,
                        type: "DELETE",
                        data: {_token: "{{ csrf_token() }}"},
                        success: function(res) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: res.message || 'Data berhasil dihapus.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        },
                        error: function(xhr) {
                            console.log(xhr);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menghapus data.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });
    });
</script>

@endsection