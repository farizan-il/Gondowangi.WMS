@extends('Gondowangi.Admin.Layout.main') 

@section('head') 
<link rel="stylesheet" href="{{ asset('vendors/mdi/css/materialdesignicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendors/css/vendor.bundle.base.css') }}">
<link rel="stylesheet" href="{{ asset('vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
<link rel="stylesheet" href="{{ asset('css/vertical-layout-light/style.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">

@endsection 


@section('content') 
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Kelola Catur Pilar</h3>
                    <h6 class="font-weight-normal mb-0">Manage data catur pilar Gondowangi</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Data Catur Pilar</h4>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="mdi mdi-plus"></i> Tambah Data
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped" id="caturPilarTable">
                            <thead>
                                <tr>
                                    <!--<th>No</th>-->
                                    <th>Urutan</th>
                                    <th>Judul</th>
                                    <th>Deskripsi</th>
                                    <th>Icon</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($caturPilar as $index => $pilar)
                                <tr>
                                    <!--<td>{{ $index + 1 }}</td>-->
                                    <td>{{ $pilar->urutan }}</td>
                                    <td>{{ $pilar->judul }}</td>
                                    <td>{{ Str::limit($pilar->deskripsi, 50) }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $pilar->icon }}</span>
                                    </td>
                                    <td>
                                        @if($pilar->status == 'aktif')
                                            <label class="badge badge-success">Aktif</label>
                                        @else
                                            <label class="badge badge-danger">Nonaktif</label>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-outline-warning btn-sm" 
                                                onclick="editData({{ $pilar->id }})" data-bs-toggle="modal" data-bs-target="#editModal">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                        <form action="{{ route('admin.tentang-kami.catur-pilar.destroy', $pilar->id) }}" method="POST" 
                                              style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Catur Pilar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.tentang-kami.catur-pilar.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Judul</label>
                        <input type="text" class="form-control" name="judul" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Icon</label>
                        <select class="form-control" name="icon" required>
                            <option value="">Pilih Icon</option>
                            <option value="shield">Shield (Integritas)</option>
                            <option value="award">Award (Terbaik)</option>
                            <option value="users">Users (Pelanggan)</option>
                            <option value="handshake">Handshake (Kerjasama)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Urutan</label>
                        <input type="number" class="form-control" name="urutan" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Catur Pilar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Judul</label>
                        <input type="text" class="form-control" name="judul" id="edit_judul" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" id="edit_deskripsi" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Icon</label>
                        <select class="form-control" name="icon" id="edit_icon" required>
                            <option value="shield">Shield (Integritas)</option>
                            <option value="award">Award (Terbaik)</option>
                            <option value="users">Users (Pelanggan)</option>
                            <option value="handshake">Handshake (Kerjasama)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Urutan</label>
                        <input type="number" class="form-control" name="urutan" id="edit_urutan" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" id="edit_status" required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('vendors/datatables.net/jquery.dataTables.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#caturPilarTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            }
        });
    });

    // Data untuk edit
    const caturPilarData = @json($caturPilar);

    function editData(id) {
        const data = caturPilarData.find(item => item.id === id);
        
        document.getElementById('edit_judul').value = data.judul;
        document.getElementById('edit_deskripsi').value = data.deskripsi;
        document.getElementById('edit_icon').value = data.icon;
        document.getElementById('edit_urutan').value = data.urutan;
        document.getElementById('edit_status').value = data.status;
        
        document.getElementById('editForm').action = `/admin/catur-pilar/${id}`;
    }
</script>
@endsection