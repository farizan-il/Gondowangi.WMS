<!DOCTYPE html>
<html lang="en">

<head>
    <title>Approval-app</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="Phoenixcoded" />
    <!-- Favicon icon -->
    <link rel="icon" href="https://www.openkerja.id/wp-content/uploads/2024/02/Lowongan-Kerja-PT-Gondowangi-Tradisional-Kosmetika.webp" type="image/x-icon">

    <!-- vendor css -->
    <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}"> 
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" /> 
    @yield('head')
    <style>
        .nav-link.active {
            background-color: #f1f1f1; /* Ganti dengan warna aktif yang diinginkan */
            color: #000; /* Warna teks aktif */
        }
        
        .pcoded-hasmenu.active .nav-link {
            font-weight: bold;
        }
    </style>
    
</head>
<body class="">
	<!-- [ Pre-loader ] start -->
	<div class="loader-bg">
		<div class="loader-track">
			<div class="loader-fill"></div>
		</div>
	</div>
	<!-- [ navigation menu ] start -->
	<nav class="pcoded-navbar menu-light navbar-collapsed"> <!-- ubah jadi dark agar mode malam -->
		<div class="navbar-wrapper  ">
			<div class="navbar-content scroll-div">
				<div class="">
					<div class="main-menu-header">
						<img class="img-radius" src="https://img.freepik.com/premium-photo/male-female-profile-avatar-user-avatars-gender-icons_1020867-74972.jpg" alt="User-Profile-Image">
						<div class="user-details">
							<div id="more-details"><strong>Admin</strong></div>
						</div>
					</div>
				</div>
				
				<ul class="nav pcoded-inner-navbar ">
					<li class="nav-item pcoded-menu-caption">
					    <label>Navigasi</label>
					</li>
					<li class="nav-item">
					    <a href="/dashboardadmin" class="nav-link "><span class="pcoded-micon"><i class="feather icon-home"></i></span><span class="pcoded-mtext">Dashboard</span></a>
					</li>
					<li class="nav-item">
					    <a href="/kelola-pengguna" class="nav-link "><span class="pcoded-micon"><i class="feather icon-user"></i></span><span class="pcoded-mtext">Kelola Pengguna</span></a>
					</li>
					<li class="nav-item"> <a href="/kelola-nominal" class="nav-link"><span class="pcoded-micon"> <i class="feather icon-credit-card"></i></span><span class="pcoded-mtext">Kelola Nominal</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.activity-logs.index') }}" class="nav-link">
                            <span class="pcoded-micon"><i class="feather icon-activity"></i></span>
                            <span class="pcoded-mtext">Activity Logs</span>
                        </a>
                    </li>

                    <li class="nav-item">
                      <a href="/kelola-flow-approval" class="nav-link">
                        <span class="pcoded-micon">
                          <!-- contoh: ikon perisai -->
                          <i class="feather icon-shield"></i>
                          <!-- atau kalau ingin ikon kunci: <i class="feather icon-lock"></i> -->
                        </span>
                        <span class="pcoded-mtext">Layer Pengajuan</span>
                      </a>
                    </li>

					<li class="nav-item pcoded-hasmenu">
					    <a class="nav-link" style="cursor: pointer">
                            <span class="pcoded-micon">
                                <i class="feather icon-file-text"></i>
                            </span>
                            <span class="pcoded-mtext">Kelola Pengajuan</span>
                        </a>
					    <ul class="pcoded-submenu">
					        <li><a href="{{ route('admin.kategori.index') }}">Kategori Pengajuan</a></li>
					        <li><a href="/kelola-form-pengajuan">Form Pengajuan</a></li>
					    </ul>
					</li>
					<li class="nav-item pcoded-menu-caption">
					    <label>Auth</label>
					</li>
					<li class="nav-item">
					    <a href="/" class="nav-link "><span class="pcoded-micon"><i class="feather icon-log-out"></i></span><span class="pcoded-mtext">Keluar</span></a>
					</li>
					<!--<li class="nav-item">-->
					<!--    <a href="/Pengajuan" class="nav-link"><span class="pcoded-micon"><i class="feather icon-user"></i></span><span class="pcoded-mtext">Simulasi Requester</span></a>-->
					<!--</li>-->
					<!--<li class="nav-item">-->
					<!--    <a href="/adminPengajuan" class="nav-link "><span class="pcoded-micon"><i class="feather icon-lock"></i></span><span class="pcoded-mtext">Simulasi Approver</span></a>-->
					<!--</li>-->
				</ul>
			</div>
		</div>
	</nav>
	<!-- [ navigation menu ] end -->
	<!-- [ Header ] start -->
	<header class="navbar pcoded-header navbar-expand-lg navbar-light header-blue">
        <div class="m-header">
            <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
            <a href="#!" class="b-brand">
                <!-- ========   change your logo hear   ============ -->
                 <strong>Gondowangi Approval</strong>
                <!-- <img src="assets/images/logo.png" alt="" class="logo"> -->
                <img src="assets/images/logo-icon.png" alt="" class="logo-thumb">
            </a>
            <a href="#!" class="mob-toggler">
                <i class="feather icon-more-vertical"></i>
            </a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                @if(Auth::user()->department->nama === 'Finance')
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm d-flex align-items-center text-white" 
                           href="/dashboard" 
                           style="margin: 8px 15px; border-radius: 20px; padding: 8px 16px; text-decoration: none; background-color: #07371d;"
                           role="button">
                            <i class="feather icon-settings mr-2"></i>
                            <span>Pindah ke Halaman Awal</span>
                        </a>
                    </li>
                @endif
                <li>
                    <div class="dropdown">
                        <a class="dropdown-toggle" href="#" data-toggle="dropdown"><i class="icon feather icon-bell"></i></a>
                        <div class="dropdown-menu dropdown-menu-right notification">
                            <div class="noti-head">
                                <h6 class="d-inline-block m-b-0">Notifications</h6>
                                <div class="float-right">
                                    <a href="#!" class="m-r-10">mark as read</a>
                                    <a href="#!">clear all</a>
                                </div>
                            </div>
                            <ul class="noti-body">
                                <li class="n-title">
                                    <p class="m-b-0">NEW</p>
                                </li>
                                <li class="notification">
                                    <div class="media">
                                        <img class="img-radius" src="assets/images/user/avatar-1.jpg" alt="Generic placeholder image">
                                        <div class="media-body">
                                            <p><strong>John Doe</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>5 min</span></p>
                                            <p>New ticket Added</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="n-title">
                                    <p class="m-b-0">EARLIER</p>
                                </li>
                                <li class="notification">
                                    <div class="media">
                                        <img class="img-radius" src="assets/images/user/avatar-2.jpg" alt="Generic placeholder image">
                                        <div class="media-body">
                                            <p><strong>Joseph William</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>10 min</span></p>
                                            <p>Prchace New Theme and make payment</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="notification">
                                    <div class="media">
                                        <img class="img-radius" src="assets/images/user/avatar-1.jpg" alt="Generic placeholder image">
                                        <div class="media-body">
                                            <p><strong>Sara Soudein</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>12 min</span></p>
                                            <p>currently login</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="notification">
                                    <div class="media">
                                        <img class="img-radius" src="assets/images/user/avatar-2.jpg" alt="Generic placeholder image">
                                        <div class="media-body">
                                            <p><strong>Joseph William</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>30 min</span></p>
                                            <p>Prchace New Theme and make payment</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div class="noti-footer">
                                <a href="#!">show all</a>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="dropdown drp-user">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="feather icon-user"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right profile-notification">
                            <div class="pro-head">
                                <img src="assets/images/user/avatar-1.jpg" class="img-radius" alt="User-Profile-Image">
                                <span>Farizan Ilham</span>
                                <a href="auth-signin.html" class="dud-logout" title="Logout">
                                    <i class="feather icon-log-out"></i>
                                </a>
                            </div>
                            <ul class="pro-body">
                                <li><a href="user-profile.html" class="dropdown-item"><i class="feather icon-user"></i> Profile</a></li>
                                <li><a href="email_inbox.html" class="dropdown-item"><i class="feather icon-mail"></i> My Messages</a></li>
                                <li><a href="auth-signin.html" class="dropdown-item"><i class="feather icon-lock"></i> Lock Screen</a></li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
	</header>
	<!-- [ Header ] end -->

    <!-- [ Main Content ] start -->
    <div class="pcoded-main-container">
        <div class="pcoded-content">
        
            <!-- [ Main Content ] start -->
            @yield('content')
            <!-- [ Main End ] start -->
        </div>
    </div>

    <!-- Required Js -->
    <script src="{{ asset('assets/js/vendor-all.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/ripple.js') }}"></script>
    <script src="{{ asset('assets/js/pcoded.min.js') }}"></script>

    <!-- Apex Chart -->
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- custom-chart js -->
    <script src="{{ asset('assets/js/pages/dashboard-main.js') }}"></script>

    @yield('script')
</body>

</html>
