@extends('Gondowangi.Admin.Layout.main')

@section('head')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Summernote CSS untuk WYSIWYG Editor -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
<style>
    .news-thumbnail {
        width: 80px;
        height: 60px;
        object-fit: cover;
    }
    .badge-category {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
   
    .status-draft {
        color: #212529;
    }
    .status-archived {
        background-color: #6c757d;
    }
    .news-excerpt {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .modal-lg {
        max-width: 900px;
    }
    .image-preview {
        max-width: 200px;
        max-height: 150px;
        object-fit: cover;
        border-radius: 5px;
        margin-top: 10px;
    }
    .ck-editor__editable_inline {
        min-height: 300px;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-0">Manajemen Berita Perusahaan 4</h4>
                        <p class="card-description">Kelola semua berita dan artikel perusahaan</p>
                    </div>
                    <button type="button" class="btn btn-primary btn-icon-text" data-bs-toggle="modal" data-bs-target="#addNewsModal">
                        <i class="mdi mdi-plus btn-icon-prepend"></i> Tambah Berita
                    </button>
                </div>

                <!-- Filter dan Search -->
                <form method="GET" action="" id="filterForm">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="categoryFilter" name="category">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->category_name }}" 
                                        {{ $filters['category'] == $category->category_name ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter" name="status">
                                <option value="">Semua Status</option>
                                <option value="published" {{ $filters['status'] == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="draft" {{ $filters['status'] == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="archived" {{ $filters['status'] == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Cari berita..." 
                                       id="searchNews" name="search" value="{{ $filters['search'] }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="mdi mdi-magnify"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Total Berita</h6>
                                        <h2 class="mb-0">{{ $stats['total'] }}</h2>
                                    </div>
                                    <i class="mdi mdi-newspaper mdi-24px"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Published</h6>
                                        <h2 class="mb-0">{{ $stats['published'] }}</h2>
                                    </div>
                                    <i class="mdi mdi-check-circle mdi-24px"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Draft</h6>
                                        <h2 class="mb-0">{{ $stats['draft'] }}</h2>
                                    </div>
                                    <i class="mdi mdi-pencil mdi-24px"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Bulan Ini</h6>
                                        <h2 class="mb-0">{{ $stats['this_month'] }}</h2>
                                    </div>
                                    <i class="mdi mdi-calendar mdi-24px"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Berita -->
                <div class="table-responsive">
                    <table class="table table-hover" id="newsTable">
                        <thead>
                            <tr>
                                <!--<th>ID</th>-->
                                <th>Foto Sampul</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Author</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Views</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($news as $item)
                            <tr>
                                <!--<td>{{ $item->id }}</td>-->
                                <td>
                                    <img src="{{ $item->thumbnail_url }}" class="news-thumbnail rounded" alt="Thumbnail" style="width: 80px; height: 60px; object-fit: cover;">
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-1">{{ Str::limit($item->title, 50) }}</h6>
                                        <p class="text-muted mb-0 news-excerpt">{{ Str::limit($item->excerpt, 80) }}</p>
                                    </div>
                                </td>
                                <td>
                                    @if ($item->category)
                                        @php
                                            $categoryClass = match(strtolower($item->category->category_name)) {
                                                'award' => 'bg-warning',
                                                'artikel', 'article' => 'bg-primary',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="rounded badge {{ $categoryClass }}">
                                            {{ $item->category->category_name }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">No Category</span>
                                    @endif
                                </td>
                                <td>{{ $item->author->name ?? 'Unknown' }}</td>
                                <td>{{ $item->formatted_date }}</td>
                                <td><strong>{!! $item->status_badge !!}</strong></td>
                                <td>{{ $item->formatted_views }}</td>
                                <td>
                                <td>        
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-info" onclick="viewNews({{ $item->id }})" title="Lihat">
                                            <i class="mdi mdi-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning" onclick="editNews({{ $item->id }})" title="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteNews({{ $item->id }})" title="Hapus">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    <div class="py-4">
                                        <i class="mdi mdi-newspaper mdi-48px text-muted"></i>
                                        <p class="text-muted mt-2">Tidak ada berita ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($news->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <p class="text-muted">
                            Menampilkan {{ $news->firstItem() }} - {{ $news->lastItem() }} dari {{ $news->total() }} berita
                        </p>
                    </div>
                    <div>
                        {{ $news->appends(request()->query())->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Berita -->
<div class="modal fade" id="addNewsModal" tabindex="-1" aria-labelledby="addNewsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="newsForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="newsId" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNewsModalLabel">Tambah Berita Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label for="newsTitle" class="form-label">Judul Berita *</label>
                                <input type="text" class="form-control" id="newsTitle" name="title" required>
                                <div class="form-text">Masukkan judul berita yang menarik dan deskriptif</div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="newsSlug" class="form-label">Slug URL (dibuat otomatis)</label>
                                <input type="text" class="form-control" id="newsSlug" name="slug" readonly>
                                <div class="form-text">URL slug akan otomatis dibuat dari judul</div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="newsExcerpt" class="form-label">Ringkasan/Excerpt</label>
                                <textarea class="form-control" id="newsExcerpt" name="excerpt" rows="3" maxlength="300"></textarea>
                                <div class="form-text">Ringkasan singkat untuk preview (maksimal 500 karakter)</div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="newsContentEditor" class="form-label">Konten Berita *</label>
                                <div id="newsContentEditor"></div>
                                <textarea id="newsContent" name="content" style="display: none;" ></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="newsCategory" class="form-label">Kategori *</label>
                                <select class="form-select" id="newsCategory" name="category_id" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="newsStatus" class="form-label">Status *</label>
                                <select class="form-select" id="newsStatus" name="status" required>
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="publishDate" class="form-label">Tanggal Publikasi</label>
                                <input type="datetime-local" class="form-control" id="publishDate" name="published_at">
                            </div>

                            <div class="form-group mb-3">
                                <label for="newsThumbnail" class="form-label">Thumbnail</label>
                                <input type="file" class="form-control" id="newsThumbnail" name="featured_image" accept="image/*">
                                <div class="form-text">Format: JPG, PNG, GIF (Max: 2MB)</div>
                                <img id="thumbnailPreview" class="image-preview mt-2" style="display: none; max-width: 100%; height: auto;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="saveAsDraft()">Simpan sebagai Draft</button>
                    <button type="submit" class="btn btn-success">Publikasikan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Read/View News -->
<div class="modal fade" id="viewNewsModal" tabindex="-1" aria-labelledby="viewNewsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewNewsModalLabel">Detail Berita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="viewNewsContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit News -->
<div class="modal fade" id="editNewsModal" tabindex="-1" aria-labelledby="editNewsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editNewsModalLabel">Edit Berita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editNewsForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Judul</label>
                        <input type="text" class="form-control" id="editTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="editCategory" class="form-label">Kategori</label>
                        <select class="form-select" id="editCategory" name="category_id" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editExcerpt" class="form-label">Ringkasan</label>
                        <textarea class="form-control" id="editExcerpt" name="excerpt" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editContent" class="form-label">Konten</label>
                        <textarea class="form-control" id="editContent" name="content" rows="6" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editStatus" class="form-label">Status</label>
                        <select class="form-select" id="editStatus" name="status" required>
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="editIsFeatured" name="is_featured" value="1">
                            <label class="form-check-label" for="editIsFeatured">
                                Berita Utama
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete News -->
<div class="modal fade" id="deleteNewsModal" tabindex="-1" aria-labelledby="deleteNewsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteNewsModalLabel">Hapus Berita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus berita ini?</p>
                <div class="alert alert-warning">
                    <i class="mdi mdi-alert-circle"></i>
                    <strong>Peringatan:</strong> Aksi ini tidak dapat dibatalkan.
                </div>
                <div id="deleteNewsInfo">
                    <!-- News info will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>


<script>
    let editor;
    document.addEventListener("DOMContentLoaded", function () {
        ClassicEditor
            .create(document.querySelector('#newsContentEditor'))
            .then(newEditor => {
                editor = newEditor;
            })
            .catch(error => {
                console.error(error);
            });

        // Sebelum submit, salin isi editor ke textarea
        document.querySelector('#newsForm').addEventListener('submit', function (e) {
            document.querySelector('#newsContent').value = editor.getData();
        });
    });
</script>

<script>
    let editor;
    document.addEventListener("DOMContentLoaded", function () {
        ClassicEditor
            .create(document.querySelector('#newsContentEditor'))
            .then(newEditor => {
                editor = newEditor;
            })
            .catch(error => {
                console.error(error);
            });

        document.querySelector('#newsForm').addEventListener('submit', function (e) {
            e.preventDefault();
            document.querySelector('#newsContent').value = editor.getData();

            const formData = new FormData(this);

            fetch('/allberita', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // ✅ Tutup modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addNewsModal'));
                    modal.hide();

                    // ✅ Reset form
                    document.getElementById('newsForm').reset();
                    editor.setData('');

                    // ✅ Tampilkan alert
                    alert(data.message);
                } else {
                    alert('Gagal menyimpan: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error(error);
                alert('Terjadi kesalahan saat menyimpan berita.');
            });
        });
    });
</script>

{{-- JavaScript untuk Modal --}}
<script>

</script>
@endsection