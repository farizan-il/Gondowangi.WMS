<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin CMS</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('assets-admin/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-admin/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-admin/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-admin/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-admin/vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-admin/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-admin/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-admin/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!--<link rel="stylesheet" type="text/css" href="{{ asset('assets-admin/js/select.dataTables.min.css') }}">-->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('assets-admin/css/style.css') }}">
    <!-- endinject -->
    <link rel="icon" type="image/png" href="{{ asset('assets/logo/logo-gondowangi-noname.png') }}" />
    <style>
        
    </style>
    @yield('head')
  </head>
  <body class="with-welcome-text sidebar-icon-only"><!--  with-welcome-text-->
      <!-- partial:partials/_navbar.html -->
      <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
          <div class="me-3">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
              <span class="icon-menu"></span>
            </button>
          </div>
          <div>
            <a class="navbar-brand brand-logo" href="index.html">
              <img src="https://1.bp.blogspot.com/-L0kjDSfe1Lk/XsPxoOlPc7I/AAAAAAAA55Q/jbk1x9s9i88Vgpei_4T_z28cXov4lGZywCNcBGAsYHQ/w680/logo-gondowangi.png" alt="logo" />
            </a>
            <a class="navbar-brand brand-logo-mini" href="index.html">
              <img src="https://1.bp.blogspot.com/-L0kjDSfe1Lk/XsPxoOlPc7I/AAAAAAAA55Q/jbk1x9s9i88Vgpei_4T_z28cXov4lGZywCNcBGAsYHQ/w680/logo-gondowangi.png" alt="logo" />
            </a>
          </div>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-top">
            <ul class="navbar-nav">
                <li class="nav-item fw-semibold d-none d-lg-block ms-0">
                    <h1 class="welcome-text" id="greeting">Selamat Pagi, <span class="text-black fw-bold">{{ Auth::user()->fullName }}</span></h1>
                    <h3 class="welcome-sub-text" id="subText">Ringkasan performa Anda minggu ini</h3>
                </li>
            </ul>
          <ul class="navbar-nav ms-auto">
            <li class="nav-item d-none d-lg-block">
              <div id="datepicker-popup" class="input-group date datepicker navbar-date-picker">
                <span class="input-group-addon input-group-prepend border-right">
                  <span class="icon-calendar input-group-text calendar-icon"></span>
                </span>
                <input type="text" class="form-control">
              </div>
            </li>
            <!--<li class="nav-item">-->
            <!--  <form class="search-form" action="#">-->
            <!--    <i class="icon-search"></i>-->
            <!--    <input type="search" class="form-control" placeholder="Search Here" title="Search here">-->
            <!--  </form>-->
              
            <!--</li>-->
            <!--<li class="nav-item dropdown">-->
            <!--  <a class="nav-link count-indicator" id="notificationDropdown" href="#" data-bs-toggle="dropdown">-->
            <!--    <i class="icon-bell"></i>-->
            <!--    <span class="count"></span>-->
            <!--  </a>-->
            <!--  <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="notificationDropdown">-->
            <!--    <a class="dropdown-item py-3 border-bottom">-->
            <!--      <p class="mb-0 fw-medium float-start">You have 4 new notifications </p>-->
            <!--      <span class="badge badge-pill badge-primary float-end">View all</span>-->
            <!--    </a>-->
            <!--    <a class="dropdown-item preview-item py-3">-->
            <!--      <div class="preview-thumbnail">-->
            <!--        <i class="mdi mdi-alert m-auto text-primary"></i>-->
            <!--      </div>-->
            <!--      <div class="preview-item-content">-->
            <!--        <h6 class="preview-subject fw-normal text-dark mb-1">Application Error</h6>-->
            <!--        <p class="fw-light small-text mb-0"> Just now </p>-->
            <!--      </div>-->
            <!--    </a>-->
            <!--    <a class="dropdown-item preview-item py-3">-->
            <!--      <div class="preview-thumbnail">-->
            <!--        <i class="mdi mdi-lock-outline m-auto text-primary"></i>-->
            <!--      </div>-->
            <!--      <div class="preview-item-content">-->
            <!--        <h6 class="preview-subject fw-normal text-dark mb-1">Settings</h6>-->
            <!--        <p class="fw-light small-text mb-0"> Private message </p>-->
            <!--      </div>-->
            <!--    </a>-->
            <!--    <a class="dropdown-item preview-item py-3">-->
            <!--      <div class="preview-thumbnail">-->
            <!--        <i class="mdi mdi-airballoon m-auto text-primary"></i>-->
            <!--      </div>-->
            <!--      <div class="preview-item-content">-->
            <!--        <h6 class="preview-subject fw-normal text-dark mb-1">New user registration</h6>-->
            <!--        <p class="fw-light small-text mb-0"> 2 days ago </p>-->
            <!--      </div>-->
            <!--    </a>-->
            <!--  </div>-->
            <!--</li>-->
            <!--<li class="nav-item dropdown">-->
            <!--  <a class="nav-link count-indicator" id="countDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">-->
            <!--    <i class="icon-mail icon-lg"></i>-->
            <!--  </a>-->
            <!--  <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="countDropdown">-->
            <!--    <a class="dropdown-item py-3">-->
            <!--      <p class="mb-0 fw-medium float-start">You have 7 unread mails </p>-->
            <!--      <span class="badge badge-pill badge-primary float-end">View all</span>-->
            <!--    </a>-->
            <!--    <div class="dropdown-divider"></div>-->
            <!--    <a class="dropdown-item preview-item">-->
            <!--      <div class="preview-thumbnail">-->
            <!--        <img src="assets/images/faces/face10.jpg" alt="image" class="img-sm profile-pic">-->
            <!--      </div>-->
            <!--      <div class="preview-item-content flex-grow py-2">-->
            <!--        <p class="preview-subject ellipsis fw-medium text-dark">Marian Garner </p>-->
            <!--        <p class="fw-light small-text mb-0"> The meeting is cancelled </p>-->
            <!--      </div>-->
            <!--    </a>-->
            <!--    <a class="dropdown-item preview-item">-->
            <!--      <div class="preview-thumbnail">-->
            <!--        <img src="assets/images/faces/face12.jpg" alt="image" class="img-sm profile-pic">-->
            <!--      </div>-->
            <!--      <div class="preview-item-content flex-grow py-2">-->
            <!--        <p class="preview-subject ellipsis fw-medium text-dark">David Grey </p>-->
            <!--        <p class="fw-light small-text mb-0"> The meeting is cancelled </p>-->
            <!--      </div>-->
            <!--    </a>-->
            <!--    <a class="dropdown-item preview-item">-->
            <!--      <div class="preview-thumbnail">-->
            <!--        <img src="https://static.vecteezy.com/system/resources/previews/014/388/508/original/avatar-portrait-of-a-young-caucasian-boy-man-in-round-blue-frame-illustration-in-cartoon-flat-style-vector.jpg" alt="image" class="img-sm profile-pic">-->
            <!--      </div>-->
            <!--      <div class="preview-item-content flex-grow py-2">-->
            <!--        <p class="preview-subject ellipsis fw-medium text-dark">Travis Jenkins </p>-->
            <!--        <p class="fw-light small-text mb-0"> The meeting is cancelled </p>-->
            <!--      </div>-->
            <!--    </a>-->
            <!--  </div>-->
            <!--</li>-->
            <li class="nav-item dropdown d-none d-lg-block user-dropdown">
              <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <img class="img-xs rounded-circle" src="https://static.vecteezy.com/system/resources/previews/014/388/508/original/avatar-portrait-of-a-young-caucasian-boy-man-in-round-blue-frame-illustration-in-cartoon-flat-style-vector.jpg" alt="Profile image"> </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                <div class="dropdown-header text-center">
                  <!--<img class="img-md rounded-circle" src="assets/images/faces/face8.jpg" alt="Profile image">-->
                  <p class="mb-1 mt-3 fw-semibold">{{ Auth::user()->fullName }}</p>
                  <p class="fw-light text-muted mb-0">{{ Auth::user()->email }}</p>
                </div>
                <a href="/masuk" class="dropdown-item"><i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Keluar</a>
              </div>
            </li>
          </ul>
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
          </button>
        </div>
      </nav>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <!-- Dashboard Analytic -->
            <li class="nav-item">
              <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="mdi mdi-view-dashboard menu-icon"></i>
                <span class="menu-title">Dashboard Analytic</span>
              </a>
            </li>
            
            <!-- Beranda -->
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#beranda" aria-expanded="false" aria-controls="beranda">
                <i class="menu-icon mdi mdi-home"></i>
                <span class="menu-title">Beranda</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="beranda">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"><a class="nav-link" href="{{ route('admin.banner.index') }}">Banner</a></li>
                  <li class="nav-item"><a class="nav-link" href="{{ route('admin.produkkami.index') }}">Brand Kami</a></li>
                  <!--<li class="nav-item"><a class="nav-link" href="{{ route('admin.awards.index') }}">Award</a></li>-->
                  
                  <li class="nav-item"><a class="nav-link" href="{{ route('admin.acara-mendatang.index') }}">Acara Mendatang</a></li>
                </ul>
              </div>
            </li>
            
            <!-- Tentang Kami -->
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#tentang-kami" aria-expanded="false" aria-controls="tentang-kami">
                <i class="menu-icon mdi mdi-information"></i>
                <span class="menu-title">Tentang Kami</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="tentang-kami">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"><a class="nav-link" href="{{ route('admin.bannertentangkami.index') }}">Banner</a></li>
                  <li class="nav-item"><a class="nav-link" href="{{ route('admin.tentang-kami.perjalanan.index') }}">Perjalanan</a></li>
                  <li class="nav-item"><a class="nav-link" href="{{ route('admin.tentang-kami.catur-pilar.index') }}">Catur Pilar</a></li>
                  <li class="nav-item"><a class="nav-link" href="{{ route('admin.newproduk.index') }}">Produk</a></li>
                </ul>
              </div>
            </li>
            
            <!-- Karir -->
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#karir" aria-expanded="false" aria-controls="karir">
                <i class="menu-icon mdi mdi-account-tie"></i>
                <span class="menu-title">Karir</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="karir">
                <ul class="nav flex-column sub-menu">
                  
                  <li class="nav-item"><a class="nav-link" href="{{ route('admin.lowongan.index') }}">Lowongan</a></li>
                </ul>
              </div>
            </li>
            
            <!-- Kelola Berita -->
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#kelola-berita" aria-expanded="false" aria-controls="kelola-berita">
                <i class="menu-icon mdi mdi-newspaper"></i>
                <span class="menu-title">Kelola Berita</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="kelola-berita">
                <ul class="nav flex-column sub-menu">
                  <!--<li class="nav-item"><a class="nav-link" href="{{ route('admin.berita.index') }}">Berita</a></li>-->
                  <li class="nav-item"><a class="nav-link" href="{{ route('admin.news.index') }}">Berita & Artikel</a></li>
                  <li class="nav-item"><a class="nav-link" href="{{ route('admin.postingan.index') }}">Qoute</a></li>
                </ul>
              </div>
            </li>
            
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="collapse" href="#kelola-berita-2" aria-expanded="false" aria-controls="kelola-berita-2">
                <i class="menu-icon mdi mdi-tag-multiple"></i>
                <span class="menu-title">Brand Kami</span>
                <i class="menu-arrow"></i>
              </a>
              <div class="collapse" id="kelola-berita-2">
                <ul class="nav flex-column sub-menu">
                  <li class="nav-item"><a class="nav-link" href="{{ route('admin.semuabrandadmin.index') }}">Semua Brand</a></li>
                  <li class="nav-item"><a class="nav-link" href="{{ route('admin.naturadmin.index') }}">Brand Content</a></li>
                  <!--<li class="nav-item"><a class="nav-link" href="#">Mizzu</a></li>-->
                  <!--<li class="nav-item"><a class="nav-link" href="#">Azalea</a></li>-->
                  <!--<li class="nav-item"><a class="nav-link" href="#">HG for Man</a></li>-->
                </ul>
              </div>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.contacts.index') }}">
                    <i class="mdi mdi-message-text-outline menu-icon"></i>
                    <span class="menu-title">Pesan</span>
                    <span id="message-badge" class="badge badge-danger"  style="
                            position: absolute; 
                            top: 8px; 
                            left: 20px; 
                            font-size: 10px; 
                            padding: 2px 5px; 
                            min-width: 16px; 
                            height: 16px; 
                            border-radius: 50%; 
                            line-height: 12px; 
                            text-align: center;
                            z-index: 999;">0</span>
                </a>
            </li>
            
            <!-- Footer -->
            <li class="nav-item">
              <a class="nav-link" href="{{ route('admin.footer.index') }}">
                <i class="mdi mdi-page-layout-footer menu-icon"></i>
                <span class="menu-title">Footer</span>
              </a>
            </li>
          </ul>
        </nav>
        <!-- partial -->
        <div class="main-panel">
          @yield('content')
          <!-- content-wrapper ends -->
          <!-- partial:partials/_footer.html -->
          <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <!--<span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Premium <a href="https://www.bootstrapdash.com/" target="_blank">Bootstrap admin template</a> from BootstrapDash.</span>-->
              <span class="float-none float-sm-end d-block mt-1 mt-sm-0 text-center">Copyright © 2025. All rights reserved.</span>
            </div>
          </footer>
          <!-- partial -->
        </div>
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    @yield('script')
    
    <!--script mengambil dan menampikan pesan baru-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function untuk update badge pesan
            function updateMessageBadge() {
                fetch('{{ route("admin.contacts.unread-count") }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('message-badge');
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'inline';
                    } else {
                        badge.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error fetching message count:', error);
                });
            }
        
            // Update badge saat halaman dimuat
            updateMessageBadge();
        
            // Update badge setiap 30 detik
            setInterval(updateMessageBadge, 30000);
        });
        </script>
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            $('#perjalananTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json'
                },
                order: [[1, 'desc']] // Mengurutkan berdasarkan tahun secara menurun
            });
        });
    </script>
    <script>
        // Fungsi untuk mendapatkan ucapan sesuai waktu
        function getGreeting() {
          const now = new Date();
          const hour = now.getHours();
          
          if (hour >= 5 && hour < 11) {
            return "Selamat Pagi";
          } else if (hour >= 11 && hour < 15) {
            return "Selamat Siang";
          } else if (hour >= 15 && hour < 18) {
            return "Selamat Sore";
          } else {
            return "Selamat Malam";
          }
        }
        
        // Array sub-text random dalam bahasa Indonesia
        const subTexts = [
          "Ringkasan performa Anda minggu ini",
          "Mari kita lihat pencapaian hari ini",
          "Semoga hari Anda penuh produktivitas",
          "Waktunya untuk mencapai target baru",
          "Dashboard Anda siap untuk dieksplorasi",
          "Data terbaru telah tersedia untuk Anda",
          "Saatnya menganalisis perkembangan bisnis",
          "Jangan lupa cek laporan terbaru",
          "Terima kasih telah bergabung dengan kami",
          "Semangat menjalankan aktivitas hari ini",
          "Pantau terus perkembangan sistem Anda",
          "Ayo optimalkan strategi bisnis Anda",
          "Kelola data dengan lebih efisien",
          "Raih kesuksesan dengan analisis yang tepat",
          "Transformasi digital dimulai dari sini",
          "Buat keputusan terbaik berdasarkan data",
          "Tingkatkan performa dengan insight mendalam",
          "Jelajahi fitur-fitur terbaru kami",
          "Maksimalkan potensi bisnis Anda",
          "Wujudkan visi melalui data akurat"
        ];
        
        // Fungsi untuk mendapatkan sub-text random
        function getRandomSubText() {
          const randomIndex = Math.floor(Math.random() * subTexts.length);
          return subTexts[randomIndex];
        }
        
        // Fungsi untuk update greeting dan sub-text
        function updateGreeting() {
          const greeting = getGreeting();
          const subText = getRandomSubText();
          
          document.getElementById('greeting').innerHTML = `${greeting}, <span class="text-black fw-bold">{{ auth::user()->fullName }}</span>`;
          document.getElementById('subText').textContent = subText;
        }
        
        // Update greeting saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
          updateGreeting();
        });
        
        // Optional: Update greeting setiap 1 menit untuk memastikan waktu selalu akurat
        setInterval(updateGreeting, 60000);
    </script>
    <script src="{{ asset('assets-admin/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets-admin/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ asset('assets-admin/vendors/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets-admin/vendors/progressbar.js/progressbar.min.js') }}"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('assets-admin/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets-admin/js/template.js') }}"></script>
    <script src="{{ asset('assets-admin/js/settings.js') }}"></script>
    <script src="{{ asset('assets-admin/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets-admin/js/todolist.js') }}"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="{{ asset('assets-admin/js/jquery.cookie.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets-admin/js/dashboard.js') }}"></script>
    <!-- <script src="assets/js/Chart.roundedBarCharts.js"></script> -->
    <!-- End custom js for this page-->
    
    <script>
      // Mendapatkan elemen sidebar
      const sidebar = document.querySelector('.sidebar');
    
      // Menambahkan event listener untuk hover
      sidebar.addEventListener('mouseenter', function() {
        document.body.classList.remove('sidebar-icon-only'); // Menghapus kelas saat hover
      });
    
      sidebar.addEventListener('mouseleave', function() {
        document.body.classList.add('sidebar-icon-only'); // Menambah kelas kembali saat hover keluar
      });
      
      // Mendapatkan elemen sidebar dan collapse menu
const sidebar = document.querySelector('.sidebar');
const collapseMenus = document.querySelectorAll('.collapse');

// Ketika sidebar di-hover, tampilkan menu collapse
sidebar.addEventListener('mouseenter', function() {
    collapseMenus.forEach(function(menu) {
        menu.style.display = 'block';  // Menampilkan menu collapse
    });
});

// Ketika mouse keluar dari sidebar, sembunyikan menu collapse
sidebar.addEventListener('mouseleave', function() {
    collapseMenus.forEach(function(menu) {
        menu.style.display = 'none';  // Menyembunyikan menu collapse
    });
});

    </script>

  </body>
</html>