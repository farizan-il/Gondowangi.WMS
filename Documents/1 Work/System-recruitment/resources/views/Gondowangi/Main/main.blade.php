<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gondowangi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="icon" type="image/png" href="{{ asset('assets/logo/logo-gondowangi-noname.png') }}" />

    <!-- Bootstrap 5 CSS & JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('head')

    <style>
        .nav-link.active {
            color: #FFD717 !important;           /* Ubah warna teks seperti hover */
            transform: translateY(-2px);         /* Geser sedikit ke atas seperti hover */
        }

        /* Pseudo-elemen untuk underline pada state aktif */
        .nav-link.active::after {
            content: '';
            position: absolute;
            width: 100%;                         /* Buat garis bawah penuh */
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #FFD717;           /* Warna garis sama dengan hover */
            /* Kalau perlu, tambahkan transition agar halus */
            transition: width 0.3s ease, left 0.3s ease;
        }
    </style>
</head>


<body>
    <!-- Scroll progress bar -->
    <div class="scroll-progress"></div>

    <div class="site-wrapper">
        <nav class="navbar navbar-expand-lg navbar-dark @yield('background') @yield('style-navbar') pb-0 mb-0 position-absolute top-0 start-0 w-100" style="z-index: 99;">
            <div class="container">
                <a class="navbar-brand animate__animated animate__fadeIn" href="#">
                    <!-- Logo default (hitam putih) -->
                    <img id="logo-default" src="assets/logo/LOGO_GONDOWANGI.png" alt="Gondowangi" style="max-height: 45px; display: block;">
                    
                    <!-- Logo berwarna -->
                    <img id="logo-colored" src="assets/logo/Logo-gondowangi-berwarna.png" alt="Gondowangi" style="max-height: 45px; display: none;">
                </a>

                <button class="navbar-toggler animate__animated animate__fadeIn" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navMenu">
                    <ul class="navbar-nav m-auto align-items-center">
                        <li class="nav-item me-4 fade-in-up">
                            <a class="nav-link beritaclient text-white {{ Request::is('beranda') ? 'active' : '' }}" href="/beranda"
                                style="letter-spacing: 2px; @yield('style')"><strong>Beranda</strong></a>
                        </li>
                        <li class="nav-item me-4 fade-in-up">
                            <a class="nav-link beritaclient text-white {{ Request::is('tentangkami') ? 'active' : '' }}" href="/tentangkami"
                                style="letter-spacing: 2px; @yield('style')"><strong>Tentang Kami</strong></a>
                        </li>
                        {{-- <li class="nav-item me-4 dropdown fade-in-up" style="transition-delay: 0.1s;">
                            <div class="dropdown">
                                <a class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Produk
                                </a>
                                <ul class="dropdown-menu dropdown-menu-dark">
                                    <li><a class="dropdown-item active" href="#">Action</a></li>
                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#">Separated link</a></li>
                                </ul>
                            </div>
                        </li> --}}
                        <li class="nav-item me-4 dropdown fade-in-up" style="transition-delay: 0.1s;">
                            <a class="nav-link text-white beritaclient" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" style="letter-spacing: 2px;">
                                <strong style="@yield('style')">Brands <i class="bx bx-chevron-down"></i></strong>
                            </a>
                            <ul class="dropdown-menu animate__animated animate__fadeIn animate__faster"
                                aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item text-dark" href="/semuabrand">Semua</a></li>
                                <li><a class="dropdown-item text-dark" href="/azalea">Azalea</a></li>
                                <li><a class="dropdown-item text-dark" href="/hgforman">HG</a></li>
                                <li><a class="dropdown-item text-dark" href="/natur" >Natur </a></li>
                                <li><a class="dropdown-item text-dark" href="/mizzu">Mizzu</a></li>
                            </ul>
                        </li>
                        <li class="nav-item me-4 fade-in-up" style="transition-delay: 0.2s;">
                            <a class="nav-link beritaclient text-white {{ Request::is('karir') ? 'active' : '' }}" href="/karir"
                                style="letter-spacing: 2px; @yield('style')"><strong>Karir</strong></a>
                        </li>
                        <li class="nav-item me-4 fade-in-up " style="transition-delay: 0.2s;">
                            <a class="nav-link  text-white {{ Request::is('beritaclient') || Request::is('berita/*') ? 'active' : '' }}" href="/beritaclient"
                                style="letter-spacing: 2px; @yield('style')"><strong>Berita</strong></a>
                        </li>
                    </ul>
                    <a href="kontakkami"
                        class="btn btn-success text-white rounded-pill fw-medium px-4 py-2 btn-ripple fade-in-up {{ Request::is('kontakkami') ? 'active' : '' }}"
                        style="transition-delay: 0.4s;">Kontak Kami <i class='bx bx-right-arrow-alt'></i></a>
                </div>
            </div>
        </nav>

        @yield('hero')
    </div>

    @yield('content')
    
    @php
        // Get footer data (you can call this from any controller)
        $footerData = App\Http\Controllers\Gondowangi\AdminController\Footer\FooterController::getFooterData();
    @endphp
    
    @if($footerData['status'])
    <!-- Footer -->
    <footer class="bg-dark-green text-white pt-5" style="margin-top: 0px;">
        <div class="container">
            <div class="row">
                <!-- Logo and Company Vision -->
                <div class="col-md-5">
                    <a class="navbar-brand text-white mb-3 d-block" href="#">
                        <img src="{{ $footerData['logo_url'] }}" alt="{{ $footerData['company_name'] }}" class="mb-3"
                            style="max-height: 50px;" 
                            onerror="this.src='{{ asset('assets/logo/LOGO_GONDOWANGI.png') }}';">
                    </a>
                    <p class="mb-0" style="font-size: 14px; line-height: 1.6;">
                        {{ $footerData['description'] ?: 'Visi kami sejak awal adalah menghadirkan produk perawatan yang tidak hanya efektif tetapi juga aman dan ramah lingkungan. Kami percaya bahwa kekayaan alam Indonesia, mulai dari lidah buaya, jahe, hingga urang-aring, memiliki potensi luar biasa untuk kesehatan dan kecantikan.' }}
                    </p>
                </div>
    
                <!-- Contact Information -->
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="fw-bold mb-3">Contact us</h5>
                    <ul class="list-unstyled">
                        @if($footerData['address'])
                        <li class="d-flex mb-3">
                            <i class='bx bx-map me-2 mt-1'></i>
                            <span>{{ $footerData['address'] }}</span>
                        </li>
                        @endif
                        
                        
                        
                        @if($footerData['email'])
                        <li class="d-flex">
                            <i class='bx bx-envelope me-2 mt-1'></i>
                            <span>{{ $footerData['email'] }}</span>
                        </li>
                        @endif
                    </ul>
                </div>
    
                <!-- Social Media Links -->
                <div class="col-md-3">
                    <h5 class="fw-bold mb-3">Follow us</h5>
                    <div class="d-flex">
                        @if($footerData['instagram_url'])
                        <a href="{{ $footerData['instagram_url'] }}" class="text-white me-3" target="_blank" rel="noopener">
                            <i class='bx bxl-instagram'></i>
                        </a>
                        @endif
                        
                        @if($footerData['youtube_url'])
                        <a href="{{ $footerData['youtube_url'] }}" class="text-white me-3" target="_blank" rel="noopener">
                            <i class='bx bxl-youtube'></i>
                        </a>
                        @endif
                        
                        @if($footerData['linkedin_url'])
                        <a href="{{ $footerData['linkedin_url'] }}" class="text-white me-3" target="_blank" rel="noopener">
                            <i class='bx bxl-linkedin'></i>
                        </a>
                        @endif
                        
                        @if($footerData['facebook_url'])
                        <a href="{{ $footerData['facebook_url'] }}" class="text-white" target="_blank" rel="noopener">
                            <i class='bx bxl-tiktok'></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            <hr class="border-secondary my-4" />
            <div class="text-center pb-3">
                &copy; {{ $footerData['copyright_text'] }}
            </div>
        </div>
    </footer>
    @endif

    @yield('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.0/dist/boxicons.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Dapatkan path saat ini
        const path = window.location.pathname;
    
        // Daftar path yang akan menampilkan logo berwarna
        const warnaPaths = ['/semuabrand', '/azalea', '/hgforman', '/mizzu', '/natur'];
    
        // Cek apakah path cocok
        if (warnaPaths.includes(path)) {
            document.getElementById('logo-default').style.display = 'none';
            document.getElementById('logo-colored').style.display = 'block';
        }
    </script>
</body>

</html>