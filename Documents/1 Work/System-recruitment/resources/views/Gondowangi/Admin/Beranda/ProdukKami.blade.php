@extends('Gondowangi.Admin.Layout.main')
@section('head')
<style>
    .brand-logo {
        width: 60px;
        height: 60px;
        object-fit: contain;
        border-radius: 50%;
        border: 2px solid #e9ecef;
    }
    .brand-card {
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }
    .brand-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .status-active {
        background-color: #d4edda;
        color: #155724;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
    }
    .status-inactive {
        background-color: #f8d7da;
        color: #721c24;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
    }
    
    /* Gamification Styles */
    .gamification-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 20px;
        position: relative;
        overflow: hidden;
    }
    
    .gamification-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100px;
        height: 100px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }
    
    .gamification-card::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -30%;
        width: 80px;
        height: 80px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
        animation: float 8s ease-in-out infinite reverse;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }
    
    .achievement-icon {
        font-size: 3rem;
        margin-bottom: 15px;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .progress-ring {
        width: 60px;
        height: 60px;
        margin: 0 auto 15px;
    }
    
    .progress-ring circle {
        fill: none;
        stroke: rgba(255,255,255,0.3);
        stroke-width: 4;
    }
    
    .progress-ring .progress {
        stroke: #fff;
        stroke-linecap: round;
        transition: stroke-dasharray 0.5s ease;
    }
    
    .motivation-text {
        font-size: 1.1rem;
        font-weight: 500;
        margin-bottom: 8px;
    }
    
    .sub-text {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    .floating-elements {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 1.5rem;
        opacity: 0.6;
        animation: bounce 3s infinite;
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
    }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Page Header -->
    <div class="row">
        <div class="col-sm-12">
            <div class="home-tab">
                <div class="d-sm-flex align-items-center justify-content-between ">
                    <div>
                        <div class="btn-wrapper">
                            <a href="#" class="btn btn-primary text-white me-0" data-bs-toggle="modal" data-bs-target="#addBrandModal">
                                <i class="icon-plus"></i> Tambahkan Brand Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="card-title mb-2">Total Brand</h6>
                            <h3 class="font-weight-bold mb-0">{{ $totalBrands }}</h3>
                        </div>
                        <div class="icon-big text-center icon-warning">
                            <i class="nc-icon nc-globe text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="card-title mb-2">Brand Aktif</h6>
                            <h3 class="font-weight-bold mb-0 text-success">{{ $activeBrands }}</h3>
                        </div>
                        <div class="icon-big text-center">
                            <i class="nc-icon nc-check-2 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-12 col-lg-6">
            <!-- Gamification Card -->
            <div class="card gamification-card shadow-lg">
                <div class="floating-elements">
                    🎯✨🚀
                </div>
                
                <div class="text-center">
                    <!-- Progress Ring -->
                    <!--<svg class="progress-ring" viewBox="0 0 36 36">-->
                    <!--    <circle cx="18" cy="18" r="16"></circle>-->
                    <!--    <circle class="progress" cx="18" cy="18" r="16" -->
                    <!--            stroke-dasharray="{{ $activeBrands > 0 ? ($activeBrands / max($totalBrands, 1)) * 100 : 0 }}, 100"-->
                    <!--            stroke-dashoffset="0"></circle>-->
                    <!--</svg>-->
                    
                    <!-- Achievement Icon -->
                    <!--<div class="achievement-icon">-->
                    <!--    @if($totalBrands == 0)-->
                    <!--        🌱-->
                    <!--    @elseif($totalBrands < 5)-->
                    <!--        🌿-->
                    <!--    @elseif($totalBrands < 10)-->
                    <!--        🌳-->
                    <!--    @else-->
                    <!--        🏆-->
                    <!--    @endif-->
                    <!--</div>-->
                    
                    <!-- Motivational Messages -->
                    <div class="motivation-text mb-3">
                        @if($totalBrands == 0)
                            Mari Mulai Perjalanan Anda!
                        @elseif($totalBrands < 5)
                            Hebat! Anda sedang berkembang
                        @elseif($totalBrands < 10)
                            Luar Biasa! Portfolio menguat
                        @else
                            Brand Master! Anda luar biasa
                        @endif
                    </div>
                    
                    <div class="sub-text">
                        @if($totalBrands == 0)
                            Tambahkan brand pertama Anda dan mulai membangun kerajaan bisnis! 
                        @elseif($activeBrands == $totalBrands && $totalBrands > 0)
                            Semua brand aktif! Konsistensi adalah kunci kesuksesan 🎉
                        @elseif($activeBrands > 0)
                            {{ $activeBrands }} dari {{ $totalBrands }} brand aktif. Terus pertahankan momentum!
                        @else
                            Saatnya mengaktifkan brand Anda untuk meraih kesuksesan!
                        @endif
                    </div>
                    
                    <!-- Level Badge -->
                    <!--<div class="mt-3">-->
                    <!--    <span class="badge badge-light" style="background: rgba(255,255,255,0.2); color: white; padding: 5px 15px; border-radius: 20px;">-->
                    <!--        @if($totalBrands == 0)-->
                    <!--            Level: Pemula 🥉-->
                    <!--        @elseif($totalBrands < 5)-->
                    <!--            Level: Berkembang 🥈-->
                    <!--        @elseif($totalBrands < 10)-->
                    <!--            Level: Mahir 🥇-->
                    <!--        @else-->
                    <!--            Level: Expert 💎-->
                    <!--        @endif-->
                    <!--    </span>-->
                    <!--</div>-->
                    
                    <!-- Next Goal -->
                    <!--@if($totalBrands < 10)-->
                    <!--    <div class="mt-2" style="font-size: 0.8rem; opacity: 0.8;">-->
                    <!--        Target berikutnya: -->
                    <!--        @if($totalBrands < 5)-->
                    <!--            {{ 5 - $totalBrands }} brand lagi untuk level "Mahir"-->
                    <!--        @else-->
                    <!--            {{ 10 - $totalBrands }} brand lagi untuk level "Expert"-->
                    <!--        @endif-->
                    <!--    </div>-->
                    <!--@endif-->
                </div>
            </div>
        </div>
    </div>

    <!-- Brand Management Table -->
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card shadow mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Kelola Brand</h4>
                        <div class="input-group" style="width: 300px;">
                            <input type="text" class="form-control" placeholder="Search brands..." id="searchBrand">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="icon-magnifier"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        Klik gambar untuk melihat versi besarnya dalam tampilan detail.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>

                    
                    <div class="table-responsive" id="brandTable">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Logo</th>
                                    <th>Nama Brand</th>
                                    <!--<th>Kategori</th>-->
                                    <th>Tagline</th>
                                    <th>Status</th>
                                    <th>Dibuat pada</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($brands as $index => $brand)
                                <tr>
                                    <td>{{ $brands->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($brand->logo_url)
                                                <img src="{{ asset($brand->logo_url) }}" 
                                                     alt="{{ $brand->brand_name }}" 
                                                     class="rounded" 
                                                     style="width: 40px; height: 40px; object-fit: cover; cursor: pointer;"
                                                     data-bs-toggle="modal" 
                                                     data-bs-target="#brandLogoModal"
                                                     onclick="showBrandLogoModal('{{ asset($brand->logo_url) }}', '{{ $brand->brand_name }}')">

                                            @else
                                                <div class="brand-logo bg-dark d-flex align-items-center justify-content-center text-white font-weight-bold" 
                                                     style="width: 40px; height: 40px; border-radius: 4px;">
                                                    {{ strtoupper(substr($brand->brand_name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $brand->brand_name }}</td>
                                    <td>{{ $brand->description ?? '-' }}</td>
                                    <td>
                                        @if($brand->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $brand->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="editBrand({{ $brand->id }})"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editBrandModal">
                                            <i class="icon-pencil"></i>
                                        </button>
                                        <!--<button class="btn btn-sm btn-outline-danger" -->
                                        <!--        onclick="confirmDelete({{ $brand->id }})">-->
                                        <!--    <i class="icon-trash"></i>-->
                                        <!--</button>-->
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data brand</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($brands->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $brands->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Logo Brand -->
<div class="modal fade" id="brandLogoModal" tabindex="-1" aria-labelledby="brandLogoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="brandLogoModalLabel">Logo Brand</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body text-center">
        <img id="brandLogoModalImage" src="" alt="Brand Logo" class="img-fluid rounded">
        <p class="mt-2" id="brandLogoCaption" style="font-weight: 500;"></p>
      </div>
    </div>
  </div>
</div>


<!-- Add Brand Modal -->
<div class="modal fade" id="addBrandModal" tabindex="-1" role="dialog" aria-labelledby="addBrandModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBrandModalLabel">Add New Brand</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addBrandForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="brandName">Brand Name *</label>
                                <input type="text" class="form-control" id="brandName" name="brand_name" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="brandDescription">Description</label>
                        <textarea class="form-control" id="brandDescription" name="description" rows="3" placeholder="Brand description"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="brandLogo">Brand Logo</label>
                                <input type="file" class="form-control-file" id="brandLogo" name="logo" accept="image/*">
                                <small class="form-text text-muted">Upload brand logo (max 2MB)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="brandStatus">Status</label>
                                <select class="form-control" id="brandStatus" name="is_active">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="brandWebsite">Website URL</label>
                        <input type="url" class="form-control" id="brandWebsite" name="website_url" placeholder="https://example.com">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Brand</button>
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Brand Modal -->
<div class="modal fade" id="editBrandModal" tabindex="-1" role="dialog" aria-labelledby="editBrandModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBrandModalLabel">Edit Brand</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editBrandForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="editBrandId" name="brand_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="editBrandName">Brand Name *</label>
                                <input type="text" class="form-control" id="editBrandName" name="brand_name" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="editBrandDescription">Description</label>
                        <textarea class="form-control" id="editBrandDescription" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editBrandLogo">Brand Logo</label>
                                <input type="file" class="form-control-file" id="editBrandLogo" name="logo" accept="image/*">
                                <small class="form-text text-muted">Upload new logo to replace current one</small>
                                <div id="currentLogo" class="mt-2"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editBrandStatus">Status</label>
                                <select class="form-control" id="editBrandStatus" name="is_active">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="editBrandWebsite">Website URL</label>
                        <input type="url" class="form-control" id="editBrandWebsite" name="website_url">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function showBrandLogoModal(src, brandName) {
        document.getElementById('brandLogoModalImage').src = src;
        document.getElementById('brandLogoCaption').textContent = brandName;
    }
</script>

<script>
    let searchTimeout;
    
    // Search functionality
    document.getElementById('searchBrand').addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        const query = this.value;
        
        searchTimeout = setTimeout(() => {
            if (query.length >= 2 || query.length === 0) {
                searchBrands(query);
            }
        }, 500);
    });
    
    function searchBrands(query) {
        fetch(`{{ route('admin.produkkami.search') }}?q=${encodeURIComponent(query)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateBrandTable(data.data);
            }
        })
        .catch(error => {
            console.error('Search error:', error);
        });
    }
    
    function updateBrandTable(brands) {
        const tbody = document.querySelector('#brandTable tbody');
        tbody.innerHTML = '';
        
        if (brands.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center">Tidak ada data brand</td></tr>';
            return;
        }
        
        brands.data.forEach((brand, index) => {
            const logoHtml = brand.logo_url 
                ? `<img src="/storage/${brand.logo_url}" alt="${brand.brand_name}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">`
                : `<div class="brand-logo bg-dark d-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 40px; height: 40px; border-radius: 4px;">${brand.brand_name.charAt(0).toUpperCase()}</div>`;
            
            const statusBadge = brand.is_active 
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Inactive</span>';
            
            const categoriesHtml = brand.products && brand.products.length > 0
                ? brand.products.slice(0, 2).map(product => 
                    product.category ? `<span class="badge badge-info">${product.category.category_name}</span>` : ''
                  ).join('')
                : '<span class="badge badge-light">Tidak ada kategori</span>';
            
            tbody.innerHTML += `
                <tr>
                    <td>${brands.from + index}</td>
                    <td><div class="d-flex align-items-center">${logoHtml}</div></td>
                    <td>${brand.brand_name}</td>
                    <td>${categoriesHtml}</td>
                    <td>${brand.description || '-'}</td>
                    <td>${statusBadge}</td>
                    <td>${new Date(brand.created_at).toISOString().split('T')[0]}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editBrand(${brand.id})" data-bs-toggle="modal" data-bs-target="#editBrandModal">
                            <i class="icon-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(${brand.id})">
                            <i class="icon-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }
    
    // Add Brand Form submission
    document.getElementById('addBrandForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('{{ route("admin.produkkami.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                $('#addBrandModal').modal('hide');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Something went wrong'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding brand');
        });
    });
    
    // Edit Brand functionality
    function editBrand(brandId) {
        fetch(`/produkkami/brands/${brandId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const brand = data.brand;
                    document.getElementById('editBrandId').value = brand.id;
                    document.getElementById('editBrandName').value = brand.brand_name;
                    document.getElementById('editBrandDescription').value = brand.description || '';
                    document.getElementById('editBrandStatus').value = brand.is_active ? '1' : '0';
                    document.getElementById('editBrandWebsite').value = brand.website_url || '';
                    
                    // Show current logo
                    const currentLogoDiv = document.getElementById('currentLogo');
                    if (brand.logo_url) {
                        currentLogoDiv.innerHTML = `
                            <small class="text-muted">Current logo:</small><br>
                            <img src="/storage/${brand.logo_url}" alt="${brand.brand_name}" style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                        `;
                    } else {
                        currentLogoDiv.innerHTML = '<small class="text-muted">No logo uploaded</small>';
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching brand:', error);
            });
    }
    
    // Edit Brand Form submission
    document.getElementById('editBrandForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const brandId = document.getElementById('editBrandId').value;
        const formData = new FormData(this);
        
        fetch(`/produkkami/brands/${brandId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                $('#editBrandModal').modal('hide');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Something went wrong'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating brand');
        });
    });
    
    // Delete confirmation
    function confirmDelete(brandId) {
        if (confirm('Are you sure you want to delete this brand? This action cannot be undone.')) {
            fetch(`/produkkami/brands/${brandId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Something went wrong'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting brand');
            });
        }
    }
</script>
@endsection