@extends('Gondowangi.Admin.Layout.main')
@section('head')
    <!-- Boxicons CSS CDN -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .footer-edit-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e3e6f0;
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 20px;
            border: none;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-control {
            border-radius: 8px;
            border: 1px solid #d1d3e2;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
        }
        
        .logo-preview {
            border: 2px dashed #d1d3e2;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background: #f8f9fc;
            transition: all 0.3s ease;
        }
        
        .logo-preview:hover {
            border-color: #667eea;
            background: #f0f2ff;
        }
        
        .logo-preview img {
            max-width: 200px;
            max-height: 100px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .social-input-group {
            display: flex;
            align-items: center;
            border: 1px solid #d1d3e2;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .social-input-group .input-group-text {
            background: #f8f9fc;
            border: none;
            padding: 12px 15px;
            font-size: 18px;
            color: #667eea;
        }
        
        .social-input-group .form-control {
            border: none;
            border-radius: 0;
        }
        
        .status-toggle {
            position: relative;
            display: inline-block;
        }
        
        .status-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .status-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
            width: 60px;
            height: 32px;
        }
        
        .status-slider:before {
            position: absolute;
            content: "";
            height: 24px;
            width: 24px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .status-slider {
            background-color: #667eea;
        }
        
        input:checked + .status-slider:before {
            transform: translateX(28px);
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            padding: 15px 20px;
        }
        
        .alert-success {
            background: #d1edff;
            color: #0c5460;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .section-divider {
            border-bottom: 2px solid #e3e6f0;
            margin: 2rem 0;
            padding-bottom: 1rem;
        }
        
        .section-title {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
            font-size: 20px;
        }
    </style>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-sm-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-0 font-size-18">
                        Edit Footer Website
                    </h4>
                    <p class="text-muted mt-1">Edit informasi footer yang ditampilkan di website</p>
                </div>
                <div class="page-title-right">
                    <span class="status-badge {{ $footer && $footer->status ? 'status-active' : 'status-inactive' }}">
                        <i class="bx {{ $footer && $footer->status ? 'bx-check-circle' : 'bx-x-circle' }}"></i>
                        {{ $footer && $footer->status ? 'Footer Aktif' : 'Footer Non-aktif' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error-circle me-2"></i>
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="footer-edit-card">
                <div class="card-header-custom">
                    <h5 class="mb-0">
                        <i class="bx bx-cog me-2"></i>
                        Pengaturan Footer Website
                    </h5>
                    <p class="mb-0 mt-2 opacity-75">Kelola semua informasi yang ditampilkan di footer website</p>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.footer.update', $footer->id ?? 1) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Logo Section -->
                        <div class="section-title">
                            <i class="bx bx-image"></i>
                            Logo Perusahaan
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Logo</label>
                            <div class="logo-preview">
                                @if($footer && $footer->logo)
                                    <img src="{{ $footer->logo_url }}" alt="Current Logo" class="current-logo mb-3">
                                    <p class="text-muted mb-2">Logo saat ini</p>
                                @else
                                    <i class="bx bx-image-add" style="font-size: 48px; color: #d1d3e2;"></i>
                                    <p class="text-muted mb-2">Belum ada logo</p>
                                @endif
                                <input type="file" class="form-control mt-2" id="logo" name="logo" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG, GIF, SVG. Maksimal 2MB</small>
                            </div>
                        </div>
                        
                        <div class="section-divider"></div>
                        
                        <!-- Company Information -->
                        <div class="section-title">
                            <i class="bx bx-buildings"></i>
                            Informasi Perusahaan
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_name" class="form-label">Nama Perusahaan *</label>
                                    <input type="text" class="form-control" id="company_name" name="company_name" 
                                           value="{{ old('company_name', $footer->company_name ?? '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="copyright_text" class="form-label">Teks Copyright</label>
                                    <input type="text" class="form-control" id="copyright_text" name="copyright_text" 
                                           value="{{ old('copyright_text', $footer->copyright_text ?? '2025. All Rights Reserved.') }}" 
                                           placeholder="© 2025. All Rights Reserved.">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="description" class="form-label">Deskripsi Perusahaan</label>
                            <textarea class="form-control" id="description" name="description" rows="4" 
                                      placeholder="Masukkan deskripsi singkat tentang perusahaan...">{{ old('description', $footer->description ?? '') }}</textarea>
                        </div>
                        
                        <div class="section-divider"></div>
                        
                        <!-- Contact Information -->
                        <div class="section-title">
                            <i class="bx bx-phone"></i>
                            Informasi Kontak
                        </div>
                        
                        <div class="form-group">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control" id="address" name="address" rows="3" 
                                      placeholder="Masukkan alamat lengkap perusahaan...">{{ old('address', $footer->address ?? '') }}</textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone" class="form-label">Nomor Telepon</label>
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                           value="{{ old('phone', $footer->phone ?? '') }}" 
                                           placeholder="Contoh: +62 21 1234567">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                            
                                           placeholder="tidak di publikasikan" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="section-divider"></div>
                        
                        <!-- Social Media -->
                        <div class="section-title">
                            <i class="bx bx-share-alt"></i>
                            Media Sosial
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="facebook_url" class="form-label">Tiktok URL</label>
                                    <div class="social-input-group">
                                        <span class="input-group-text">
                                            <i class="bx bxl-tiktok"></i>
                                        </span>
                                        <input type="url" class="form-control" id="facebook_url" name="facebook_url" 
                                               value="{{ old('facebook_url', $footer->facebook_url ?? '') }}" 
                                               placeholder="https://tiktok.com/username">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="instagram_url" class="form-label">Instagram URL</label>
                                    <div class="social-input-group">
                                        <span class="input-group-text">
                                            <i class="bx bxl-instagram"></i>
                                        </span>
                                        <input type="url" class="form-control" id="instagram_url" name="instagram_url" 
                                               value="{{ old('instagram_url', $footer->instagram_url ?? '') }}" 
                                               placeholder="https://instagram.com/username">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="youtube_url" class="form-label">YouTube URL</label>
                                    <div class="social-input-group">
                                        <span class="input-group-text">
                                            <i class="bx bxl-youtube"></i>
                                        </span>
                                        <input type="url" class="form-control" id="youtube_url" name="youtube_url" 
                                               value="{{ old('youtube_url', $footer->youtube_url ?? '') }}" 
                                               placeholder="https://youtube.com/channel/username">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="linkedin_url" class="form-label">LinkedIn URL</label>
                                    <div class="social-input-group">
                                        <span class="input-group-text">
                                            <i class="bx bxl-linkedin"></i>
                                        </span>
                                        <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                                               value="{{ old('linkedin_url', $footer->linkedin_url ?? '') }}" 
                                               placeholder="https://linkedin.com/company/username">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="section-divider"></div>
                        
                        <!-- Status -->
                        <div class="section-title">
                            <i class="bx bx-toggle-left"></i>
                            Status Footer
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Status Aktif</label>
                            <div class="d-flex align-items-center">
                                <label class="status-toggle me-3">
                                    <input type="checkbox" name="status" value="1" {{ old('status', $footer->status ?? true) ? 'checked' : '' }}>
                                    <span class="status-slider"></span>
                                </label>
                                <span class="text-muted">
                                    <i class="bx bx-info-circle me-1"></i>
                                    Aktifkan untuk menampilkan footer di website
                                </span>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">
                                <i class="bx bx-arrow-back me-1"></i>
                                Kembali
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview logo when file is selected
    const logoInput = document.getElementById('logo');
    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector('.logo-preview');
                    const existingImg = preview.querySelector('img');
                    
                    if (existingImg) {
                        existingImg.src = e.target.result;
                    } else {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.maxWidth = '200px';
                        img.style.maxHeight = '100px';
                        img.style.borderRadius = '8px';
                        img.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.1)';
                        img.className = 'mb-3';
                        
                        const icon = preview.querySelector('.bx-image-add');
                        const text = preview.querySelector('p');
                        
                        if (icon) icon.remove();
                        if (text) text.textContent = 'Logo baru';
                        
                        preview.insertBefore(img, preview.children[0]);
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Form validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const companyName = document.getElementById('company_name').value.trim();
            
            if (!companyName) {
                e.preventDefault();
                alert('Nama perusahaan wajib diisi!');
                document.getElementById('company_name').focus();
                return false;
            }
        });
    }
    
    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert) {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }
        }, 5000);
    });
});
</script>
@endsection