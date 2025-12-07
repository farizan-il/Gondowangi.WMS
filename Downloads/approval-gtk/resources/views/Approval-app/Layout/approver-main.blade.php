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
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
	<!-- [ navigation menu ] start 9000 -->
	<nav class="pcoded-navbar menu-light navbar-collapsed"> <!-- ubah jadi dark agar mode malam -->
		<div class="navbar-wrapper  ">
			<div class="navbar-content scroll-div">
				<div class="">
					<div class="main-menu-header">
						<img class="img-radius" src="https://img.freepik.com/premium-photo/male-female-profile-avatar-user-avatars-gender-icons_1020867-74972.jpg" alt="User-Profile-Image">
						<div class="user-details mt-2">
							<div id="more-details"><strong>{{ Auth::user()->nama }} <br> {{ Auth::user()->jabatan }}</strong></div>
						</div>
					</div>
				</div>
				<hr>
				<ul class="nav pcoded-inner-navbar mt-3">
					<li class="nav-item pcoded-menu-caption">
					    <label>UTAMA</label>
					</li>
					
					@php
                        $userDepartment = strtolower(auth()->user()->department->nama);
                    @endphp
                    
                    @if(in_array($userDepartment, ['finance', 'bod', 'direktur']))
                        <li class="nav-item pcoded-hasmenu">
                            <a class="nav-link" style="cursor: pointer">
                                <span class="pcoded-micon">
                                    <i class="feather icon-home"></i>
                                </span>
                                <span class="pcoded-mtext">Dashboard</span>
                            </a>
                            <ul class="pcoded-submenu">
                                <li><a href="/overview">Overview</a></li>
                                <li><a href="/dashboard">Personal</a></li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a href="/dashboard" class="nav-link ">
                                <span class="pcoded-micon">
                                    <i class="feather icon-home"></i>
                                </span>
                                <span class="pcoded-mtext">Dashboard</span>
                            </a>
                        </li>
                    @endif

					<li class="nav-item pcoded-hasmenu">
					    <a class="nav-link" style="cursor: pointer">
                            <span class="pcoded-micon">
                                <i class="feather icon-layout"></i>
                            </span>
                            <span class="pcoded-mtext">Data Pengajuan</span>
                        </a>
					    <ul class="pcoded-submenu">
					        <li><a href="/BuatPengajuan">Buat Pengajuan</a></li>
					        <li><a href="/PengajuanSelesai">Pengajuan Selesai</a></li>
					    </ul>
					</li>
					
					<li class="nav-item">
					    <a href="/settlements" class="nav-link ">
					        <span class="pcoded-micon">
					            <i class="feather icon-inbox"></i>
					        </span>
					        <span class="pcoded-mtext">Settlement</span>
					   </a>
					</li>
					
					<li class="nav-item pcoded-menu-caption">
					    <label>Laporan</label>
					</li>
                    <li class="nav-item">
					    <a href="/LaporanPengajuan" class="nav-link ">
					        <span class="pcoded-micon">
					            <i class="feather icon-pie-chart"></i>
					        </span>
					        <span class="pcoded-mtext">Laporan Pengajuan</span>
					   </a>
					</li>
					@php
                        $userDepartment = strtolower(auth()->user()->department->nama);
                    @endphp
                    
                    @if($userDepartment === 'finance')
    					<!--<li class="nav-item">-->
    					<!--    <a href="/RiwayatPengajuan" class="nav-link ">-->
    					<!--        <span class="pcoded-micon">-->
    					<!--            <i class="feather icon-clock"></i>-->
    					<!--        </span>-->
    					<!--        <span class="pcoded-mtext">Riwayat Pengajuan</span>-->
    					<!--   </a>-->
    					<!--</li>-->
                    
             <!--           <li class="nav-item pcoded-hasmenu">-->
             <!--               <a class="nav-link" style="cursor: pointer">-->
             <!--                   <span class="pcoded-micon">-->
             <!--                       <i class="feather icon-file-text"></i>-->
             <!--                   </span>-->
             <!--                   <span class="pcoded-mtext">Transaction Reqeust</span>-->
             <!--               </a>-->
             <!--               <ul class="pcoded-submenu">-->
             <!--                 <li><a href="/TransactionRequest">Buat TR</a></li>-->
					        <!--</ul>-->
             <!--           </li>-->
                     <li class="nav-item pcoded-hasmenu">
                        <a class="nav-link" style="cursor: pointer">
                            <span class="pcoded-micon">
                                <i class="feather icon-file-text"></i>
                            </span>
                            <span class="pcoded-mtext">
                                Transaction Reqeust
                                {{-- LOGIKA BADGE MERAH --}}
                                @if(isset($readyForTRCount) && $readyForTRCount > 0)
                                    <span class="badge badge-danger badge-pill" 
                                          style="margin-left: 10px; font-size: 10px; position: relative; top: -2px;">
                                        {{ $readyForTRCount > 99 ? '99+' : $readyForTRCount }}
                                    </span>
                                @endif
                            </span>
                        </a>
                        <ul class="pcoded-submenu">
                            <li>
                                <a href="/TransactionRequest">
                                    Buat TR
                                    {{-- Opsional: Tampilkan juga di submenu --}}
                                    @if(isset($readyForTRCount) && $readyForTRCount > 0)
                                        <span class="badge badge-danger badge-pill float-right" style="font-size: 9px;">
                                            {{ $readyForTRCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
				
					<li class="nav-item pcoded-menu-caption">
					    <label>Auth</label>
					</li> 
    				<li class="nav-item">
                            <a href="#" class="nav-link" onclick="logout()">
                            <span class="pcoded-micon"><i class="feather icon-log-out"></i></span>
                            <span class="pcoded-mtext">Keluar</span>
                        </a>
                    </li>
					<!--<li class="nav-item">-->
					<!--    <a href="/Pengajuan" class="nav-link"><span class="pcoded-micon"><i class="feather icon-user"></i></span><span class="pcoded-mtext">Simulasi Requester</span></a>-->
					<!--</li>-->
					<!--<li class="nav-item">-->
					<!--    <a href="/admin/kelola-otoritasi" class="nav-link "><span class="pcoded-micon"><i class="feather icon-settings"></i></span><span class="pcoded-mtext">Simulasi Admin</span></a>-->
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
                    @if($userDepartment === 'finance')
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm d-flex align-items-center text-white" 
                               href="/dashboardadmin" 
                               style="margin: 8px 15px; border-radius: 20px; padding: 8px 16px; text-decoration: none; background-color: #07371d;"
                               role="button">
                                <i class="feather icon-settings mr-2"></i>
                                <span>Pindah ke Admin</span>
                            </a>
                        </li>
                    @endif
                    <li>
                        <div class="dropdown">
                            <a class="dropdown-toggle position-relative" href="#" data-toggle="dropdown">
                                <i class="icon feather icon-bell"></i>
                                @if(isset($unreadCount) && $unreadCount > 0)
                                    <span class="badge badge-danger badge-pill position-absolute" style="top: -8px; right: -8px; font-size: 10px;">
                                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                    </span>
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-menu-right notification" style="width: 350px; max-height: 400px; overflow-y: auto;">
                                <div class="noti-head">
                                    <h6 class="d-inline-block m-b-0">Notifications</h6>
                                    <div class="float-right">
                                        <a href="#!" class="m-r-10" onclick="markAllAsRead()">mark as read</a>
                                        <a href="#!" onclick="clearAllNotifications()">clear all</a>
                                    </div>
                                </div>
                                <ul class="noti-body">
                                    @if(isset($newNotifications) && $newNotifications->count() > 0)
                                        <li class="n-title">
                                            <p class="m-b-0">NEW</p>
                                        </li>
                                        @foreach($newNotifications as $notification)
                                            <li class="notification {{ $notification->is_read ? '' : 'unread-notification' }}" 
                                                onclick="markNotificationAsRead({{ $notification->id }})">
                                                <div class="media">
                                                    <div class="media-object">
                                                        <div class="notification-icon {{ $notification->getNotificationTypeClass() }}">
                                                            <i class="feather {{ $notification->getNotificationIcon() }}"></i>
                                                        </div>
                                                    </div>
                                                    <div class="media-body">
                                                        <p>
                                                            <strong>{{ $notification->actor_name ?? 'System' }}</strong>
                                                            <span class="n-time text-muted">
                                                                <i class="icon feather icon-clock m-r-10"></i>
                                                                {{ $notification->getTimeAgo() }}
                                                            </span>
                                                        </p>
                                                        <p class="mb-0">{{ Str::limit($notification->getNotificationMessage(), 60) }}</p>
                                                        @if($notification->pengajuan)
                                                            <small class="text-muted">{{ $notification->pengajuan->nomor_pengajuan }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    @endif
            
                                    @if(isset($earlierNotifications) && $earlierNotifications->count() > 0)
                                        <li class="n-title">
                                            <p class="m-b-0">EARLIER</p>
                                        </li>
                                        @foreach($earlierNotifications as $notification)
                                            <li class="notification {{ $notification->is_read ? '' : 'unread-notification' }}" 
                                                onclick="markNotificationAsRead({{ $notification->id }})">
                                                <div class="media">
                                                    <div class="media-object">
                                                        <div class="notification-icon {{ $notification->getNotificationTypeClass() }}">
                                                            <i class="feather {{ $notification->getNotificationIcon() }}"></i>
                                                        </div>
                                                    </div>
                                                    <div class="media-body">
                                                        <p>
                                                            <strong>{{ $notification->actor_name ?? 'System' }}</strong>
                                                            <span class="n-time text-muted">
                                                                <i class="icon feather icon-clock m-r-10"></i>
                                                                {{ $notification->getTimeAgo() }}
                                                            </span>
                                                        </p>
                                                        <p class="mb-0">{{ Str::limit($notification->getNotificationMessage(), 60) }}</p>
                                                        @if($notification->pengajuan)
                                                            <small class="text-muted">{{ $notification->pengajuan->nomor_pengajuan }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    @endif
            
                                    @if((!isset($newNotifications) || $newNotifications->count() == 0) && (!isset($earlierNotifications) || $earlierNotifications->count() == 0))
                                        <li class="notification">
                                            <div class="media">
                                                <div class="media-body text-center py-3">
                                                    <i class="feather icon-bell text-muted" style="font-size: 2rem;"></i>
                                                    <p class="text-muted mb-0">Belum Ada Notifikasi Hari ini</p>
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                </ul>
                                <div class="noti-footer">
                                    <a href="#!" onclick="showAllNotifications()">show all</a>
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
                                    <a href="/login" class="dud-logout" title="Logout">
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
    
    <!-- Modal Show All Notifications -->
    <div class="modal fade" id="allNotificationsModal" tabindex="-1" role="dialog" aria-labelledby="allNotificationsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="allNotificationsModalLabel">
                        <i class="feather icon-bell mr-2"></i>All Notifications
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                    <div id="allNotificationsContent">
                        <div class="text-center py-3">
                            <i class="feather icon-loader spin"></i> Loading notifications...
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="markAllModalAsRead()">
                        <i class="feather icon-check mr-1"></i>Mark All as Read
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
            <div class="modal-content" style="border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);">
                <!-- Modal Header -->
                <div class="modal-header" style="border-bottom: none; padding: 30px 30px 15px 30px; text-align: center;">
                    <div style="width: 100%;">
                        <div style="width: 60px; height: 60px; margin: 0 auto 15px; background: linear-gradient(135deg, #ff6b6b, #ee5a24); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-sign-out-alt" style="color: white; font-size: 24px;"></i>
                        </div>
                        <h5 class="modal-title" id="logoutModalLabel" style="color: #2c3e50; font-weight: 600; margin: 0;">Konfirmasi Logout</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; top: 15px; right: 15px; opacity: 0.5;"></button>
                </div>
                
                <!-- Modal Body -->
                <div class="modal-body" style="padding: 15px 30px; text-align: center;">
                    <p style="color: #6c757d; margin-bottom: 0; font-size: 16px; line-height: 1.5;">
                        Apakah Anda yakin ingin keluar dari akun Anda?
                    </p>
                </div>
                
                <!-- Modal Footer -->
                <div class="modal-footer" style="border-top: none; padding: 15px 30px 30px 30px; justify-content: center; gap: 15px;">
                    <button type="button" class="btn" data-bs-dismiss="modal" 
                            style="background-color: #f8f9fa; color: #6c757d; border: 1px solid #dee2e6; padding: 12px 30px; border-radius: 8px; font-weight: 500; transition: all 0.3s ease; min-width: 100px;"
                            onmouseover="this.style.backgroundColor='#e9ecef'; this.style.transform='translateY(-1px)'"
                            onmouseout="this.style.backgroundColor='#f8f9fa'; this.style.transform='translateY(0)'">
                        Batal
                    </button>
                    <button type="button" class="btn" onclick="confirmLogout()" 
                            style="background: linear-gradient(135deg, #ff6b6b, #ee5a24); color: white; border: none; padding: 12px 30px; border-radius: 8px; font-weight: 500; transition: all 0.3s ease; min-width: 100px; box-shadow: 0 4px 15px rgba(238, 90, 36, 0.3);"
                            onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 20px rgba(238, 90, 36, 0.4)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(238, 90, 36, 0.3)'">
                        Ya, Keluar
                    </button>
                </div>
            </div>
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
    
    <script>
        function logout() {
            // Tampilkan modal alih-alih confirm dialog
            const modal = new bootstrap.Modal(document.getElementById('logoutModal'));
            modal.show();
        }
        
        function confirmLogout() {
            // Tutup modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('logoutModal'));
            modal.hide();
            
            // Buat form dinamis untuk logout
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("logout") }}';
            
            // Tambahkan CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Submit form
            document.body.appendChild(form);
            form.submit();
        }
    </script>
    
    <!--// SCRIPT JS UNTUK NOTIFIKASI - Letakkan di bagian bawah view atau dalam file terpisah-->
    <script>
        $(document).ready(function() {
            // Inisialisasi
            updateNotificationBadge();
        });
        
        // Global notification functions
        function markNotificationAsRead(notificationId) {
            $.ajax({
                url: '{{ route("notifications.mark-read") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    notification_ids: [notificationId]
                },
                success: function(response) {
                    if (response.success) {
                        // Remove unread styling
                        $(`li[onclick="markNotificationAsRead(${notificationId})"]`).removeClass('unread-notification');
                        // Update badge count
                        updateNotificationBadge();
                        
                        // Tampilkan pesan sukses
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Notifikasi telah ditandai sebagai dibaca');
                        }
                    } else {
                        console.error('Error:', response.message);
                        if (typeof toastr !== 'undefined') {
                            toastr.error(response.message || 'Gagal menandai notifikasi');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Terjadi kesalahan saat menandai notifikasi');
                    }
                }
            });
        }
        
        function markAllAsRead() {
            $.ajax({
                url: '{{ route("notifications.mark-read") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Remove all unread styling
                        $('.unread-notification').removeClass('unread-notification');
                        // Hide badge
                        $('.badge-danger').hide();
                        
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message || 'Semua notifikasi telah ditandai sebagai dibaca');
                        }
                    } else {
                        console.error('Error:', response.message);
                        if (typeof toastr !== 'undefined') {
                            toastr.error(response.message || 'Gagal menandai notifikasi');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Terjadi kesalahan saat menandai semua notifikasi');
                    }
                }
            });
        }
        
        function clearAllNotifications() {
            if (confirm('Apakah Anda yakin ingin menghapus semua notifikasi? Tindakan ini tidak dapat dibatalkan.')) {
                $.ajax({
                    url: '{{ route("notifications.clear-all") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Clear notification dropdown
                            $('.noti-body').html(`
                                <li class="notification">
                                    <div class="media">
                                        <div class="media-body text-center py-3">
                                            <i class="feather icon-bell text-muted" style="font-size: 2rem;"></i>
                                            <p class="text-muted mb-0">Belum ada notifikasi</p>
                                        </div>
                                    </div>
                                </li>
                            `);
                            // Hide badge
                            $('.badge-danger').hide();
                            
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message || 'Semua notifikasi telah dihapus');
                            }
                        } else {
                            console.error('Error:', response.message);
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message || 'Gagal menghapus notifikasi');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Terjadi kesalahan saat menghapus notifikasi');
                        }
                    }
                });
            }
        }
        
        function showAllNotifications() {
            $('#allNotificationsModal').modal('show');
            loadAllNotifications();
        }
        
        function loadAllNotifications(page = 1) {
            // Tampilkan loading
            $('#allNotificationsContent').html(`
                <div class="text-center py-3">
                    <i class="feather icon-loader spin"></i> Memuat notifikasi...
                </div>
            `);
            
            $.ajax({
                url: '{{ route("notifications.all") }}',
                method: 'GET',
                data: { page: page },
                success: function(response) {
                    if (response.success) {
                        renderAllNotifications(response.data, response.pagination);
                    } else {
                        $('#allNotificationsContent').html(`
                            <div class="text-center py-3 text-danger">
                                <i class="feather icon-alert-circle"></i> ${response.message || 'Gagal memuat notifikasi'}
                            </div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    $('#allNotificationsContent').html(`
                        <div class="text-center py-3 text-danger">
                            <i class="feather icon-alert-circle"></i> Terjadi kesalahan saat memuat notifikasi
                        </div>
                    `);
                }
            });
        }
        
        function renderAllNotifications(notifications, pagination) {
            let html = '';
            
            if (notifications.length === 0) {
                html = `
                    <div class="text-center py-3">
                        <i class="feather icon-bell text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted">Belum ada notifikasi</p>
                    </div>
                `;
            } else {
                notifications.forEach(function(notification) {
                    const isUnread = !notification.is_read ? 'unread-notification' : '';
                    const pengajuanInfo = notification.pengajuan ? notification.pengajuan.nomor_pengajuan : 'N/A';
                    const actorName = notification.actor_name || 'System';
                    const description = notification.description || getNotificationMessage(notification);
                    const catatan = notification.catatan ? `<div class="mt-2"><small class="text-info"><strong>Catatan:</strong> ${notification.catatan}</small></div>` : '';
                    
                    html += `
                        <div class="card mb-2 ${isUnread}" onclick="markNotificationAsRead(${notification.id})">
                            <div class="card-body p-3">
                                <div class="d-flex">
                                    <div class="notification-icon ${getNotificationClass(notification.status_after)} mr-3">
                                        <i class="feather ${getNotificationIcon(notification.status_after)}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h6 class="mb-1">${actorName}</h6>
                                            <small class="text-muted">${formatTimeAgo(notification.created_at)}</small>
                                        </div>
                                        <p class="mb-1">${description}</p>
                                        <small class="text-muted">${pengajuanInfo}</small>
                                        ${catatan}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                // Add pagination if needed
                if (pagination.last_page > 1) {
                    html += renderPagination(pagination);
                }
            }
            
            $('#allNotificationsContent').html(html);
        }
        
        function getNotificationClass(status) {
            const classes = {
                'approved': 'text-success',
                'rejected': 'text-danger',
                'revision': 'text-info'
            };
            return classes[status] || 'text-muted';
        }
        
        function getNotificationIcon(status) {
            const icons = {
                'approved': 'icon-check-circle',
                'rejected': 'icon-x-circle',
                'revision': 'icon-edit'
            };
            return icons[status] || 'icon-bell';
        }
        
        function getNotificationMessage(notification) {
            if (notification.description) {
                return notification.description;
            }
            
            const pengajuanNumber = notification.pengajuan ? notification.pengajuan.nomor_pengajuan : '';
            return `Pengajuan ${pengajuanNumber} telah diperbarui.`;
        }
        
        function formatTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = now - date;
            
            const minutes = Math.floor(diff / 60000);
            const hours = Math.floor(diff / 3600000);
            const days = Math.floor(diff / 86400000);
            
            if (minutes < 1) return 'Baru saja';
            if (minutes < 60) return `${minutes} menit yang lalu`;
            if (hours < 24) return `${hours} jam yang lalu`;
            return `${days} hari yang lalu`;
        }
        
        function renderPagination(pagination) {
            let html = '<nav class="mt-3"><ul class="pagination pagination-sm justify-content-center">';
            
            // Previous button
            if (pagination.current_page > 1) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="loadAllNotifications(${pagination.current_page - 1})">Sebelumnya</a></li>`;
            }
            
            // Page numbers (show current and surrounding pages)
            const start = Math.max(1, pagination.current_page - 2);
            const end = Math.min(pagination.last_page, pagination.current_page + 2);
            
            for (let i = start; i <= end; i++) {
                const active = i === pagination.current_page ? 'active' : '';
                html += `<li class="page-item ${active}"><a class="page-link" href="#" onclick="loadAllNotifications(${i})">${i}</a></li>`;
            }
            
            // Next button
            if (pagination.current_page < pagination.last_page) {
                html += `<li class="page-item"><a class="page-link" href="#" onclick="loadAllNotifications(${pagination.current_page + 1})">Selanjutnya</a></li>`;
            }
            
            html += '</ul></nav>';
            return html;
        }
        
        function markAllModalAsRead() {
            markAllAsRead();
            // Remove unread styling from modal
            $('.unread-notification').removeClass('unread-notification');
        }
        
        function updateNotificationBadge() {
            // Hitung ulang jumlah notifikasi yang belum dibaca
            const unreadCount = $('.unread-notification').length;
            const badge = $('.badge-danger');
            
            if (unreadCount > 0) {
                badge.text(unreadCount > 99 ? '99+' : unreadCount).show();
            } else {
                badge.hide();
            }
        }
        
        // Auto-refresh notifikasi setiap 30 detik (opsional)
        setInterval(function() {
            // Refresh notification badge dan dropdown
            refreshNotificationDropdown();
        }, 30000);
        
        // Function untuk refresh notification dropdown
        function refreshNotificationDropdown() {
            $.ajax({
                url: '{{ route("notifications.all") }}',
                method: 'GET',
                data: { page: 1, limit: 10 }, // Ambil 10 notifikasi terbaru
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        // Update badge count
                        const unreadCount = response.data.filter(n => !n.is_read).length;
                        const badge = $('.badge-danger');
                        
                        if (unreadCount > 0) {
                            badge.text(unreadCount > 99 ? '99+' : unreadCount).show();
                        } else {
                            badge.hide();
                        }
                        
                        // Optional: Update dropdown content jika diperlukan
                        // updateDropdownContent(response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Auto-refresh error:', error);
                }
            });
        }
        
        // CSS untuk styling notifikasi yang belum dibaca
        const notificationStyles = `
        <style>
        .unread-notification {
            background-color: #f8f9fa !important;
            border-left: 3px solid #007bff !important;
        }
        
        .unread-notification .media-body {
            font-weight: 600;
        }
        
        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        
        .notification-icon.text-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .notification-icon.text-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .notification-icon.text-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .notification-icon.text-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .notification:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }
        
        .spin {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .badge-pill {
            border-radius: 10px;
            min-width: 18px;
            height: 18px;
            font-size: 10px;
            line-height: 16px;
        }
        </style>
        `;
        
        // Inject CSS ke halaman
        $('head').append(notificationStyles);
</script>

    @yield('script')
</body>

</html>
