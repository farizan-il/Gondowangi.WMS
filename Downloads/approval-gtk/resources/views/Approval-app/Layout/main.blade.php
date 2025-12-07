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
</head>
<body class="">
	<!-- [ Pre-loader ] start -->
	<div class="loader-bg">
		<div class="loader-track">
			<div class="loader-fill"></div>
		</div>
	</div>
	<!-- [ Pre-loader ] End -->
	<!-- [ navigation menu ] start -->
	<nav class="pcoded-navbar menu-light"> <!-- ubah jadi dark agar mode malam -->
		<div class="navbar-wrapper  ">
			<div class="navbar-content scroll-div">
				<div class="">
					<div class="main-menu-header">
						<img class="img-radius" src="https://static.vecteezy.com/system/resources/previews/017/779/366/original/cartoon-avatar-of-young-male-businessman-wearing-suit-and-tie-free-vector.jpg" alt="User-Profile-Image">
						<div class="user-details">
							<div id="more-details">Requester</div>
						</div>
					</div>
				</div>
				
				<ul class="nav pcoded-inner-navbar ">
					<li class="nav-item pcoded-menu-caption">
					    <label>Navigasi</label>
					</li>
					<li class="nav-item">
					    <a href="/beranda" class="nav-link "><span class="pcoded-micon"><i class="feather icon-home"></i></span><span class="pcoded-mtext">Beranda</span></a>
					</li>
					<li class="nav-item pcoded-hasmenu">
					    <a class="nav-link" style="cursor: pointer">
                            <span class="pcoded-micon">
                                <i class="feather icon-layout"></i>
                            </span>
                            <span class="pcoded-mtext">Data Pengajuan</span>
                        </a>
					    <ul class="pcoded-submenu">
					        <li><a href="/kategori-pengajuan">Buat Pengajuan</a></li>
					        <li><a href="/PengajuanSelesai">Selesai</a></li>
					    </ul>
					</li>
					<!-- <li class="nav-item pcoded-menu-caption">
					    <label>UI Element</label>
					</li>
					<li class="nav-item pcoded-hasmenu">
					    <a href="#!" class="nav-link "><span class="pcoded-micon"><i class="feather icon-box"></i></span><span class="pcoded-mtext">Basic</span></a>
					    <ul class="pcoded-submenu">
					        <li><a href="bc_alert.html">Alert</a></li>
					        <li><a href="bc_button.html">Button</a></li>
					        <li><a href="bc_badges.html">Badges</a></li>
					        <li><a href="bc_breadcrumb-pagination.html">Breadcrumb & paggination</a></li>
					        <li><a href="bc_card.html">Cards</a></li>
					        <li><a href="bc_collapse.html">Collapse</a></li>
					        <li><a href="bc_carousel.html">Carousel</a></li>
					        <li><a href="bc_grid.html">Grid system</a></li>
					        <li><a href="bc_progress.html">Progress</a></li>
					        <li><a href="bc_modal.html">Modal</a></li>
					        <li><a href="bc_spinner.html">Spinner</a></li>
					        <li><a href="bc_tabs.html">Tabs & pills</a></li>
					        <li><a href="bc_typography.html">Typography</a></li>
					        <li><a href="bc_tooltip-popover.html">Tooltip & popovers</a></li>
					        <li><a href="bc_toasts.html">Toasts</a></li>
					        <li><a href="bc_extra.html">Other</a></li>
					    </ul>
					</li> -->
					<li class="nav-item pcoded-menu-caption">
					    <label>Auth</label>
					</li> 
					<!--<li class="nav-item">-->
					<!--    <a href="/" class="nav-link"><span class="pcoded-micon"><i class="feather icon-log-out"></i></span><span class="pcoded-mtext">Keluar</span></a>-->
					<!--</li>-->
					<li class="nav-item">
					    <a href="/adminPengajuan" class="nav-link "><span class="pcoded-micon"><i class="feather icon-lock"></i></span><span class="pcoded-mtext">Simulasi Approver</span></a>
					</li>
					<li class="nav-item">
					    <a href="/admin/kelola-otoritasi" class="nav-link "><span class="pcoded-micon"><i class="feather icon-settings"></i></span><span class="pcoded-mtext">Simulasi Admin</span></a>
					</li>
					<!--<li class="nav-item pcoded-menu-caption">-->
					<!--    <label>Chart & Maps</label>-->
					<!--</li>-->
					<!--<li class="nav-item">-->
					<!--    <a href="chart-apex.html" class="nav-link "><span class="pcoded-micon"><i class="feather icon-pie-chart"></i></span><span class="pcoded-mtext">Chart</span></a>-->
					<!--</li>-->
					<!--<li class="nav-item">-->
					<!--    <a href="map-google.html" class="nav-link "><span class="pcoded-micon"><i class="feather icon-map"></i></span><span class="pcoded-mtext">Maps</span></a>-->
					<!--</li>-->
					<!--<li class="nav-item pcoded-menu-caption">-->
					<!--    <label>Pages</label>-->
					<!--</li>-->
					<!--<li class="nav-item pcoded-hasmenu">-->
					<!--    <a href="#!" class="nav-link "><span class="pcoded-micon"><i class="feather icon-lock"></i></span><span class="pcoded-mtext">Authentication</span></a>-->
					<!--    <ul class="pcoded-submenu">-->
					<!--        <li><a href="auth-signup.html" target="_blank">Sign up</a></li>-->
					<!--        <li><a href="auth-signin.html" target="_blank">Sign in</a></li>-->
					<!--    </ul>-->
					<!--</li>-->
					<!--<li class="nav-item"><a href="sample-page.html" class="nav-link "><span class="pcoded-micon"><i class="feather icon-sidebar"></i></span><span class="pcoded-mtext">Sample page</span></a></li> -->
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
