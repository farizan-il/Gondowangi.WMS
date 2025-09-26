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
    
    <style>
        body {
            overflow-x: hidden;
        }

        /* Hero Section */
        

        /* Produk Section */
        .produk {
            padding: 4rem 0;
        }

        .produk h2 {
            font-size: 2rem;
            font-weight: 600;
        }

        .produk .card {
            border: none;
            background: none;
            text-align: center;
        }

        .bg-dark-green {
            background-color: #12592c !important;
        }

        .bg-yellow {
            background-color: #fcd535 !important;
        }

        .date-box {
            position: absolute;
            top: 0;
            left: 0;
            width: 80px;
            height: 60px;
            /* tinggi header kira-kira */
            background: #71A586;
            /* hanya sudut kiri atas yang bulat */
            border-top-left-radius: .75rem;
            border-bottom-left-radius: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .events-section .bg-dark-green {
            position: relative;
            height: 227px;
            z-index: 2;
        }

        /* ===== AWAL STYLE UNTUK SECTION BRAND */
        .brand-section {
            /* letak dan ukuran background */
            flex-shrink: 0;
            background: url('assets/background-web/Rectangle.png');
            background-size: cover;
        }

        .logo-card {
            display: flex;
            width: 220.139px;
            height: 220.139px;
            padding: 73px 34.748px 73.139px 35.391px;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            /* membuat bulat sempurna */
            background: #FFF;
            box-shadow: 0px 30.284px 40.379px -15.142px rgba(0, 0, 0, 0.26);
            margin: 0 auto 1rem;
            /* center dan jarak bawah */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Animasi hover untuk logo card */
        .logo-card:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0px 40px 50px -10px rgba(0, 0, 0, 0.3);
        }

        /* Supaya logo tidak melebihi card */
        .logo-card img {
            max-width: 100%;
            height: auto;
            display: block;
            border-radius: 0%;
            transition: transform 0.5s ease;
        }

        /* Rotasi logo saat hover */
        .logo-card:hover img {
            transform: rotate(20deg);
        }

        /* ===== AKHIR STYLE UNTUK SECTION BRAND */

        /* ===== AWAL STYLE UNTUK SECTION AWARD SECTION */


        /* ===== AWAL STYLE UNTUK SECTION footer */
        /* warna dasar */
        .bg-dark-green {
            background-color: #0B5932 !important;
        }

        .bg-yellow {
            background-color: #FFD717 !important;
        }

        /* kartu event */
        .event-card {
            background: #FFCE00;
            border-radius: 30px;
            overflow: hidden;
            /* agar date-box bisa absolute */
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        /* kotak tanggal */
        .event-card .date-box {
            position: absolute;
            top: 0;
            left: 0;
            width: 80px;
            height: 100%;
            background: #71A586;
            /* rounding kiri saja */
            border-top-left-radius: 0.75rem;
            border-bottom-left-radius: 0.75rem;
            padding-top: 1rem;
            transition: background 0.3s ease, width 0.3s ease;
        }

        .event-card:hover .date-box {
            background: #5a8d6f;
            width: 85px;
        }

        .date-box .date-day {
            font-size: 2rem;
            font-weight: bold;
            line-height: 1;
            transition: transform 0.3s ease;
        }

        .event-card:hover .date-box .date-day {
            transform: scale(1.1);
        }

        .date-box .date-month {
            font-size: 1.2rem;
            text-transform: capitalize;
        }

        /* bagian atas kartu (judul) */
        .event-card .card-top {
            /* hanya rounding kanan & kiri atas */
            border-top-right-radius: 0.75rem;
            /* agar date-box tak menutupi */
            margin-left: 0;
            transition: background-color 0.3s ease;
        }

        .event-card:hover .card-top {
            background-color: #FFE47A !important;
        }

        .event-card .card-top h5 {
            color: #0B5932;
            font-weight: bold;
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .event-card:hover .card-top h5 {
            transform: translateX(5px);
            color: #064026;
        }

        /* bagian bawah kartu (deskripsi) */
        .event-card .card-bottom {
            /* hanya rounding kanan & kiri bawah */
            border-bottom-right-radius: 0.75rem;
            border-bottom-left-radius: 0.75rem;
            background: #FFCE00;
            transition: background-color 0.3s ease;
        }

        /* pattern background */
        .pattern-bg {
            top: 75%;
            height: 360px;
            background-image: url('assets/background-web/Rectangle.png');
            background-repeat: repeat;
            /* opacity: 0.5; */
            z-index: -1;
        }

        /* animasi interaktif scroll */
        .stat-item {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .stat-item.visible {
            opacity: 1;
            transform: translateY(0);
        }


        /* AWAL ANIMASI SCROLL WEB */
        /* Animation classes */
        .fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .fade-in-left {
            opacity: 0;
            transform: translateX(-30px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .fade-in-right {
            opacity: 0;
            transform: translateX(30px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .zoom-in {
            opacity: 0;
            transform: scale(0.8);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        /* When element is visible */
        .visible {
            opacity: 1;
            transform: translate(0) scale(1);
        }

        /* Staggered animation delay for stats */
        .stat-item:nth-child(1) {
            transition-delay: 0s;
        }

        .stat-item:nth-child(2) {
            transition-delay: 0.2s;
        }

        .stat-item:nth-child(3) {
            transition-delay: 0.4s;
        }

        .stat-item:nth-child(4) {
            transition-delay: 0.6s;
        }

        .scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 4px;
            background: linear-gradient(to right, #198754, #ffc107);
            z-index: 1000;
            transition: width 0.1s ease-out;
        }

        /* Additional content section for scroll demonstration */
        .content-section {
            min-height: 100vh;
            padding: 100px 0;
        }

        .stats-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        @media (max-width: 768px) {
            .stats-container {
                justify-content: center;
            }
        }

        .stat-group {
            display: flex;
            align-items: center;
        }

        /* Scroll indicator animation */
        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            color: white;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0) translateX(-50%);
            }

            40% {
                transform: translateY(-20px) translateX(-50%);
            }

            60% {
                transform: translateY(-10px) translateX(-50%);
            }
        }

        /* ===== TAMBAHAN ANIMASI ===== */

        /* Animasi untuk navbar */
        .navbar {
            transition: background-color 0.4s ease, padding 0.4s ease;
        }

        .navbar.scrolled {
            background-color: rgba(11, 89, 50, 0.95) !important;
            padding-top: 10px;
            padding-bottom: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand img {
            transition: max-height 0.3s ease;
        }

        .navbar.scrolled .navbar-brand img {
            max-height: 40px;
        }

        /* Animasi untuk menu navbar */
        .nav-item {
            position: relative;
        }

        .nav-link {
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .nav-link:hover {
            color: #FFD717 !important;
            transform: translateY(-2px);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: #FFD717;
            transition: width 0.3s ease, left 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
            left: 0;
        }

        /* Animasi untuk tombol kontak kami */
        .btn {
            transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease, color 0.3s ease;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transition: left 0.3s ease;
            z-index: -1;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .btn:hover::before {
            left: 100%;
        }

        /* Animasi untuk hero section */
        .hero h1 {
            transition: transform 0.5s ease;
        }

        .hero h1:hover {
            transform: scale(1.02);
        }

        /* Animasi untuk statistik di hero */
        .hero .position-absolute h3 {
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .hero .position-absolute h3:hover {
            transform: scale(1.1);
            color: #FFD717 !important;
        }

        /* Animasi untuk award cards */
        .border.border-2 {
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            overflow: hidden;
        }

        .border.border-2:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .border.border-2 img {
            transition: transform 0.5s ease;
        }

        .border.border-2:hover img {
            transform: scale(1.05);
            border-radius: 0 0 32px 32px;
        }

        /* Animasi untuk berita cards */
        .card-img-top {
            transition: transform 0.5s ease;
            overflow: hidden;
        }

        .header-news:hover .card-img-top {
            transform: scale(1.05);
            border-radius: 0 0 32px 32px;
        }

        /* Animasi overlay teks berita */
        .position-absolute.bottom-0 {
            background: linear-gradient(to bottom,
                    rgba(11, 89, 50, 0) 0%,
                    rgba(11, 89, 50, 0.5) 50%,
                    rgba(11, 89, 50, 0.828) 100%);
            transform: translateY(100%);
            transition: transform 0.4s ease, background 0.4s ease;
            border-radius: 0 0 32px 32px;
        }

        .header-news:hover .position-absolute.bottom-0 {
            transform: translateY(0);
            /* gradasi dari transparan ke rgba(11,89,50,0.828) */
            background: linear-gradient(to bottom,
                    rgba(11, 89, 50, 0) 0%,
                    rgba(11, 89, 50, 0.5) 50%,
                    rgba(11, 89, 50, 0.828) 100%);
            border-radius: 0 0 32px 32px;
        }


        /* Animasi untuk footer */
        footer .bx {
            transition: transform 0.3s ease, color 0.3s ease;
        }

        footer a:hover .bx {
            transform: scale(1.3);
            color: #FFD717;
        }

        /* Animation for social media icons */
        footer .d-flex a {
            display: inline-block;
            font-size: 1.5rem;
            transition: transform 0.3s ease, color 0.3s ease;
        }

        footer .d-flex a:hover {
            transform: translateY(-5px);
            color: #FFD717 !important;
        }

        /* Pulse animation for award titles */
        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .carousel-item .card-title {
            transition: color 0.3s ease;
        }

        .carousel-item:hover .card-title {
            color: #0B5932;
            animation: pulse 1.5s infinite;
        }

        /* Ripple effect for buttons */
        .btn-ripple {
            position: relative;
            overflow: hidden;
        }

        .btn-ripple:after {
            content: "";
            display: block;
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            background-image: radial-gradient(circle, #fff 10%, transparent 10.01%);
            background-repeat: no-repeat;
            background-position: 50%;
            transform: scale(10, 10);
            opacity: 0;
            transition: transform .5s, opacity 1s;
        }

        .btn-ripple:active:after {
            transform: scale(0, 0);
            opacity: .3;
            transition: 0s;
        }

        /* Floating animation for special elements */
        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        /* Loading animation */
        /* .loading-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #0B5932;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }

        .loading-animation.hide {
            opacity: 0;
            visibility: hidden;
        } */

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #FFD717;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Color change on scroll for certain elements */
        .color-change-on-scroll {
            transition: color 0.3s ease, background-color 0.3s ease;
        }

        /* Typed text animation */
        .typed-cursor {
            opacity: 1;
            animation: typedjsBlink 0.7s infinite;
        }

        @keyframes typedjsBlink {
            50% {
                opacity: 0.0;
            }
        }

        .card-news-hover-overlay {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
        }

        .card-news-hover-overlay img {
            display: block;
            width: 100%;
            height: auto;
            transition: transform 0.4s ease;
        }

        /* overlay warna yang slide naik */
        .card-news-hover-overlay::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -100%;
            width: 100%;
            height: 100%;
            background: rgba(11, 89, 50, 0.6);
            transition: bottom 0.4s ease;
            z-index: 1;
        }

        /* tombol detail, awalnya tersembunyi di tengah */
        .card-news-hover-overlay .detail-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.8);
            opacity: 0;
            transition: opacity 0.3s ease, transform 0.3s ease;
            z-index: 2;
        }

        /* Hover state: slide overlay, zoom gambar & munculkan tombol */
        .card-news-hover-overlay:hover::after {
            bottom: 0;
        }

        .card-news-hover-overlay:hover img {
            transform: scale(1.05);
        }

        .card-news-hover-overlay:hover .detail-btn {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }

        .awards-carousel {
            cursor: grab;
            scroll-behavior: smooth;
        }

        .awards-carousel.dragging {
            cursor: grabbing;
        }

        .award-item {
            width: calc(33.333% - 16px);
            /* 3 kartu per view minus margin */
        }

        /* style.css (atau di <style> head) */
        .site-wrapper {
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center center;
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Agar hero (bagian konten) vertikal center */
        /*.site-wrapper .hero {*/
        /*    flex: 1;*/
            /* isi mengambil sisa ruang */
        /*    display: flex;*/
        /*    align-items: center;*/
        /*}*/

        /* Optional: jika ingin overlay gelap di atas gambar */
        .site-wrapper::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 0;
        }

        .site-wrapper>* {
            position: relative;
            z-index: 1;
        }
    </style>
    <style>
            @keyframes bounce {
            0%, 20%, 53%, 80%, 100% {
                transform: translate3d(0, 0, 0);
            }
            40%, 43% {
                transform: translate3d(0, -10px, 0);
            }
            70% {
                transform: translate3d(0, -5px, 0);
            }
            90% {
                transform: translate3d(0, -2px, 0);
            }
        }
        
        .animate-bounce {
            animation: bounce 2s ease-in-out infinite;
            animation-delay: 0.1s;
        }
        
        /* Staggered animation untuk card yang berbeda */
        .col-md-3:nth-child(1) .animate-bounce {
            animation-delay: 0.1s;
        }
        
        .col-md-3:nth-child(2) .animate-bounce {
            animation-delay: 0.2s;
        }
        
        .col-md-3:nth-child(3) .animate-bounce {
            animation-delay: 0.3s;
        }
        .hero-slide {
            min-height: 100vh;
            position: relative;
        }
        
        .carousel-fade .carousel-item {
            opacity: 0;
            transition-property: opacity;
            background-image: none;
        }
        
        .carousel-fade .carousel-item.active {
            opacity: 1;
        }
        
        .carousel-fade .carousel-item-next.carousel-item-left,
        .carousel-fade .carousel-item-prev.carousel-item-right {
            opacity: 1;
        }
        
        .carousel-fade .carousel-item-next,
        .carousel-fade .carousel-item-prev {
            transform: none;
        }
        
        .carousel-indicators {
            bottom: 20px;
        }
        
        .carousel-indicators button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.5);
            border: 2px solid #a8de66;
            margin: 0 5px;
        }
        
        .carousel-indicators button.active {
            background-color: #a8de66;
        }
        
        .carousel-control-prev,
        .carousel-control-next {
            width: 60px;
            height: 60px;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 50%;
            border: none;
            opacity: 0.8;
            transition: all 0.3s ease;
        }
        
        .carousel-control-prev:hover,
        .carousel-control-next:hover {
            background-color: rgba(0, 0, 0, 0.5);
            opacity: 1;
        }
        
        .carousel-control-prev {
            left: 30px;
        }
        
        .carousel-control-next {
            right: 30px;
        }
        
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            width: 20px;
            height: 20px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero-slide {
                min-height: 70vh;
            }
            
            .position-absolute.top-0.end-0 {
                position: static !important;
                text-align: center !important;
                margin-bottom: 20px;
            }
            
            .position-absolute.top-0.end-0 .d-flex {
                flex-direction: column !important;
                gap: 15px;
            }
            
            .border-start {
                border-start: none !important;
                border-bottom: 2px solid #a8de66 !important;
                height: auto !important;
                width: 50px;
                margin: 0 auto !important;
            }
            
            .carousel-control-prev,
            .carousel-control-next {
                width: 40px;
                height: 40px;
            }
            
            .carousel-control-prev {
                left: 15px;
            }
            
            .carousel-control-next {
                right: 15px;
            }
        }
        
       

        .event-card-hover:hover img {
            transform: scale(1.05);
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }
        
        
        /*Style animasi brand*/
        .event-overlay {
            opacity: 0;
            transition: all 0.3s ease-in-out;
            transform: translateY(20px);
        }
        
        .brand-card {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .brand-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        
        .brand-card:hover .event-overlay {
            opacity: 1;
            transform: translateY(0);
        }
        
        .event-overlay {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.6));
            backdrop-filter: blur(5px);
        }
        
        .event-overlay .btn {
            transform: scale(0.9);
            transition: transform 0.2s ease;
        }
        
        .event-overlay .btn:hover {
            transform: scale(1);
        }
        
        .event-image {
            transition: transform 0.3s ease;
        }
        
        .brand-card:hover .event-image {
            transform: scale(1.05);
        }
        
        /* Modal enhancements */
        .modal-content {
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        
        .modal-header .btn-close {
            filter: invert(1);
        }
        
         /* Gaya khusus untuk acara kadaluarsa */
        .expired-event .event-card {
            background-color:rgb(132, 127, 127);  /* Warna latar belakang yang menandakan acara kadaluarsa */
            opacity: 0.6;  /* Kurangi opasitas untuk acara kadaluarsa */
            pointer-events: none; /* Menonaktifkan interaksi */
        }
        
        .expired-event .card-top {
            background-color: rgb(132, 127, 127)!important;  /* Gaya yang berbeda untuk card-top acara kadaluarsa */
        }
        
        .expired-event .card-bottom {
            background-color: rgb(132, 127, 127) !important;  /* Gaya yang berbeda untuk card-bottom acara kadaluarsa */
        }
        
        .expired-event .date-box {
            background: rgb(132, 127, 127); /* Warna latar belakang berbeda untuk acara kadaluarsa */
        }
        
        .brand-card:hover .event-overlay {
            opacity: 1 !important;
        }

    </style>
</head>

<body>
    <!-- Scroll progress bar -->
    <div class="scroll-progress"></div>

    <div class="site-wrapper">
        <nav class="navbar navbar-expand-lg navbar-dark pb-0 mb-0 position-absolute top-0 start-0 w-100" style="z-index: 1040;">
            <div class="container">
                <a class="navbar-brand animate__animated animate__fadeIn" href="#">
                    <img src="assets/logo/LOGO_GONDOWANGI.png" alt="Gondowangi" style="max-height: 45px;">
                </a>
                <button class="navbar-toggler animate__animated animate__fadeIn" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navMenu">
                    <ul class="navbar-nav m-auto align-items-center">
                        <li class="nav-item me-4 fade-in-up">
                            <a class="nav-link text-white {{ Request::is('beranda') ? 'active' : '' }}" href="/beranda"
                                style="letter-spacing: 2px; @yield('style')"><strong>Beranda</strong></a>
                        </li>
                        <li class="nav-item me-4 fade-in-up">
                            <a class="nav-link text-white {{ Request::is('tentangkami') ? 'active' : '' }}" href="/tentangkami"
                                style="letter-spacing: 2px; @yield('style')"><strong>Tentang Kami</strong></a>
                        </li>
                        <li class="nav-item me-4 dropdown fade-in-up" style="transition-delay: 0.1s;">
                            <a class="nav-link text-white" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" style="letter-spacing: 2px;">
                                <strong style="@yield('style')">Brands <i class="bx bx-chevron-down"></i></strong>
                            </a>
                            <ul class="dropdown-menu animate__animated animate__fadeIn animate__faster"
                                aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item text-dark" href="/semuabrand">Semua</a></li>
                                <li><a class="dropdown-item text-dark" href="/azalea">Azalea</a></li>
                                <li><a class="dropdown-item text-dark" href="/hgforman">HG</a></li>
                                <li><a class="dropdown-item text-dark" href="#" disabled>Natur</a></li>
                                <li><a class="dropdown-item text-dark" href="#" disabled>Mizzu</a></li>
                            </ul>
                        </li>
                        <li class="nav-item me-4 fade-in-up" style="transition-delay: 0.2s;">
                            <a class="nav-link text-white {{ Request::is('karir') ? 'active' : '' }}" href="/karir"
                                style="letter-spacing: 2px; @yield('style')"><strong>Karir</strong></a>
                        </li>
                        <li class="nav-item me-4 fade-in-up " style="transition-delay: 0.2s;">
                            <a class="nav-link text-white {{ Request::is('berita') ? 'active' : '' }}" href="/beritaclient"
                                style="letter-spacing: 2px; @yield('style')"><strong>Berita</strong></a>
                        </li>
                    </ul>
                    <a href="kontakkami"
                        class="btn btn-success text-white rounded-pill fw-medium px-4 py-2 btn-ripple fade-in-up {{ Request::is('kontakkami') ? 'active' : '' }}"
                        style="transition-delay: 0.4s;">Kontak Kami <i class='bx bx-right-arrow-alt'></i></a>
                </div>
            </div>
        </nav>

        <!-- Hero Carousel -->
        <section class="hero position-relative">
            <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
                <!-- Carousel Indicators -->
                <div class="carousel-indicators">
                    @foreach($banners as $index => $banner)
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}"
                            class="{{ $index === 0 ? 'active' : '' }}"
                            aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                            aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
        
                <!-- Carousel Inner -->
                <div class="carousel-inner">
                    @foreach($banners as $index => $banner)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <!-- Background Image using IMG tag -->
                            <div class="hero-slide d-flex align-items-center position-relative" style="min-height: 100vh;">
                                
                                <!-- Background Image -->
                                <img src="{{ $banner->image_path }}" 
                                     class="carousel-bg-image position-absolute w-100 h-100" 
                                     alt="{{ $banner->image_path ?? 'Banner Image' }}"
                                     style="object-fit: cover; top: 0; left: 0; z-index: 0;">
                
                                <!-- Dark Overlay -->
                                <div class="position-absolute top-0 start-0 w-100 h-100" 
                                     style="background: rgba(0, 0, 0, 0.5); z-index: 1;"></div>
                
                                <!-- Text Layer on Top -->
                                <div class="container text-white position-relative" style="z-index: 2;">
                                    <!-- Statistik: dengan border-start tebal -->
                                    <div class="position-absolute top-0 end-0" style="z-index: 3;">
                                        <div class="d-flex align-items-center text-center text-md-end">
                                            <div class="pe-3">
                                                <h3 class="fw-bold mb-0" style="color: #a8de66;">250+</h3>
                                                <p class="small mb-0" style="color: #a8de66;">Gondowangi Crews</p>
                                            </div>
                                            <div class="border-start border-3 mx-3" style="height: 3rem; border-left-color: #a8de66 !important;"></div>
                                            <div class="px-3">
                                                <h3 class="fw-bold mb-0" style="color: #a8de66;">50+</h3>
                                                <p class="small mb-0" style="color: #a8de66;">Distribution Center</p>
                                            </div>
                                            <div class="border-start border-3 border-success mx-3" style="height: 3rem; border-left-color: #a8de66 !important;"></div>
                                            <div class="px-3">
                                                <h3 class="fw-bold mb-0" style="color: #a8de66;">40+</h3>
                                                <p class="small mb-0" style="color: #a8de66;">CSR Partnership</p>
                                            </div>
                                            <div class="border-start border-3 border-success mx-3" style="height: 3rem; border-left-color: #a8de66 !important;"></div>
                                            <div class="ps-3">
                                                <h3 class="fw-bold mb-0" style="color: #a8de66;">100+</h3>
                                                <p class="small mb-0" style="color: #a8de66;">SKU Produk</p>
                                            </div>
                                        </div>
                                    </div>
                
                                    <!-- Konten Hero -->
                                    <div class="row align-items-center" style="padding-top: 100px; z-index: 3;">
                                        <div class="col-lg-12">
                                            @if($banner->title)
                                                <h1 class="display-4 fw-bold mb-4 fade-in-left" style="width: 600px;">
                                                    {!! nl2br(e($banner->title)) !!}
                                                </h1>
                                            @endif
                
                                            @if($banner->subtitle)
                                                <h2 class="h4 mb-3 fade-in-left" style="transition-delay: 0.1s; color: #a8de66;">
                                                    {{ $banner->subtitle }}
                                                </h2>
                                            @endif
                
                                            @if($banner->description)
                                                <p class="lead mb-4 fade-in-left" style="transition-delay: 0.2s; width: 600px;">
                                                    {{ $banner->description }}
                                                </p>
                                            @endif
                
                                            @if($banner->button_text && $banner->button_url)
                                                <a href="{{ $banner->button_url }}" 
                                                   class="btn btn-warning text-dark fw-medium px-4 py-2 btn-ripple fade-in-left"
                                                   style="transition-delay: 0.4s;">
                                                    {{ $banner->button_text }} <i class='bx bx-right-arrow-alt'></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- Carousel Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </section>
    </div>

    <!-- Brand Kami -->
    <section class="produk text-center brand-section">
        <div class="container">
            <h2 style="font-size: 38px; color: #0E6A39;" class="fade-in-up">Brand Kami</h2>
            <p class="mb-5 fade-in-up" style="font-size: 50px; color: #0E6A39; transition-delay: 0.2s;">
                <strong>"Unleashed Natural Beauty"</strong>
            </p>

            <div class="row justify-content-center g-4">
                @if(isset($brands) && $brands->count() > 0)
                    @foreach($brands as $index => $brand)
                        <div class="col-6 col-sm-4 col-md-3 fade-in-up" style="transition-delay: {{ 0.3 + ($index * 0.1) }}s;">
                            <div class="card border-0 bg-transparent text-center">
                                <div class="logo-card">
                                    <img src="{{ $brand->logo_url ? asset($brand->logo_url) : asset('assets/logo/default-logo.png') }}" 
                                         alt="{{ $brand->brand_name }}"
                                         onerror="this.src='{{ asset('assets/logo/default-logo.png') }}'">
                                </div>
                                <div class="card-body p-0 mt-3">
                                    <h5 class="card-title">{{ $brand->brand_name }}</h5>
                                    <!--@if($brand->description)-->
                                    <!--    <p class="card-text text-muted small">{{ Str::limit($brand->description, 50) }}</p>-->
                                    <!--@endif-->
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Fallback jika tidak ada data brand -->
                    <div class="col-12">
                        <p class="text-muted">Belum ada data brand yang tersedia.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Awards & News Section -->
    <section id="awards" class="py-5 position-relative"
        style="background: url('assets/background-web/green-leaves-water.png') no-repeat center/cover;">
        <div class="overlay position-absolute top-0 start-0 w-100 h-100">
        </div>
        
        <div class="container position-relative" style="margin-bottom: 70px;">
            <div class="row align-items-center mb-4">
                <!-- Judul & Controls -->
                <div class="col-lg-8">
                    <h6 style="font-size: 30px;" class="fade-in-left"><strong>Award kami</strong></h6>
                    <h2 class="fw-bold fade-in-left"
                        style="color: #0E6A39; font-size: 53.658px; transition-delay: 0.2s;">
                        Penghargaan &amp;<br>Pencapaian
                    </h2>
                </div>
                <div class="col-lg-4 text-lg-end fade-in-right">
                    <button id="awardsPrev" class="btn btn-outline-success me-3 pb-2 btn-ripple"
                        style="border-radius: 92px; padding: 0 30px; font-size: 30px;">
                        &larr;
                    </button>
                    <span id="awardsIndex" class="me-3" style="color: #0E6A39; font-size: 20px;">
                        <strong>01</strong>/<span id="awardsTotal">{{ str_pad($awards->count(), 2, '0', STR_PAD_LEFT) }}</span>
                    </span>
                    <button id="awardsNext" class="btn btn-outline-success pb-2 btn-ripple"
                        style="border-radius: 92px; padding: 0 30px; font-size: 30px;">
                        &rarr;
                    </button>
                </div>
            </div>
        
            <!-- Carousel Track -->
            <div class="awards-carousel d-flex overflow-hidden position-relative">
                @if(isset($awards) && $awards->count() > 0)
                    @foreach($awards as $index => $award)
                        <a href="{{ route('berita.show', $award->slug) }}"
                           class="award-item flex-shrink-0 px-2 fade-in-up text-reset text-decoration-none"
                           style="transition-delay: {{ 0.2 * $index }}s; max-width: 398px; width: 100%;">
                            <div class="border border-2 w-100" style="border-radius: 31px; border: 2px solid #000; overflow: hidden;">
                                <img src="{{ $award->featured_image }}"
                                     class="card-img-top w-100"
                                     alt="{{ $award->award_name }}"
                                     style="aspect-ratio: 16 / 9; object-fit: cover; border-radius: 31px 31px 0 0;"
                                     onerror="this.src='{{ asset('assets/award/default-award.png') }}'">
                                <div class="card-body p-3">
                                    <small class="text-muted d-block mb-1" style="font-size: 14px;">
                                        {{ $award->published_at ? $award->published_at->format('M d, Y') : 'Date not specified' }}
                                    </small>
                                    <h5 class="card-title mb-2" style="font-size: 18px; line-height: 1.2;">
                                        <strong>
                                        {{ Str::limit(strip_tags($award->title), 76) }}</strong>
                                    </h5>
                                    <p class="card-text mb-1" style="font-size: 14px;">
                                        {{ Str::limit(strip_tags($award->content), 100) }}
                                    </p>
                                    @if($award->awarding_body)
                                        <small class="text-muted d-block" style="font-size: 13px;">
                                            <strong>Pemberi:</strong> {{ $award->awarding_body }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                @else
                    <!-- Fallback jika tidak ada data award -->
                    <div class="award-item flex-shrink-0 px-2 fade-in-up">
                        <div class="border border-2" style="border-radius: 31px; border: 2px solid #000;">
                            <!--<img src="{{ asset('assets/award/default-award.png') }}" -->
                            <!--     class="card-img-top" -->
                            <!--     alt="No Award"-->
                            <!--     style="border-radius: 31px 31px 0 0;">-->
                            <div class="card-body p-3">
                                <small class="text-muted" style="font-size: 21px;">Coming Soon</small>
                                <h5 class="card-title mt-3 mb-2" style="font-size: 26px;">
                                    Belum Ada Penghargaan
                                </h5>
                                <p class="card-text">
                                    Penghargaan akan segera ditampilkan di sini.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>


        <hr>
        <!-- SECTION BERITA -->
        <div class="container" style="margin-top: 70px;">
            <div class="text-center mb-5">
                <h6 class="text-dark fade-in-up" style="font-size: 30px;">Berita & artikel</h6>
                <h2 class="fw-bold fade-in-up" style="font-size: 50px; color: #0E6A39; transition-delay: 0.2s;">Berita
                    Tentang kami</h2>
            </div>
            <div class="row g-4">
                @if($featuredNews)
                <!-- Featured News -->
                <div class="col-lg-12 zoom-in">
                    <div class="border-0 overflow-hidden" style="border-radius: 32px;">
                        <div class="position-relative header-news">
                            <img src="{{ $featuredNews->featured_image }}" class="card-img-top"
                                alt="{{ $featuredNews->title }}">
        
                            <!-- Overlay teks di bagian bawah foto -->
                            <div class="position-absolute bottom-0 start-0 w-100 text-white p-4">
                                <p class="text-white pt-1 pb-3">
                                    <span class="badge bg-warning text-dark me-2"
                                        style="top: 1rem; left: 1rem; border-radius: 20px;">
                                        <span style="color: #0b6435;">{{ $featuredNews->category->category_name ?? 'Artikel' }}</span>
                                    </span>
                                    {{ \Carbon\Carbon::parse($featuredNews->published_at)->translatedFormat('j F Y') }}
                                </p>
                                <h5 class="mt-1 mb-2">
                                    <strong>
                                    {{ $featuredNews->title }}</h5>
                                    </strong>
                                <p class="mb-0 w-50">
                                    {{ $featuredNews->excerpt }}
                                </p>
                                <a href="{{ route('berita.show', $featuredNews->slug) }}" class="btn btn-warning p-2 mt-3 btn-sm detail-btn">
                                    <i class="bx bx-info-circle"></i> Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
        
                <!-- List kecil -->
                <div class="col-lg-12">
                    <div class="row g-3">
                        @foreach($otherNews as $index => $news)
                        <div class="col-4 fade-in-right" style="transition-delay: {{ ($index + 1) * 0.2 }}s;">
                            <div class="border-0">
                                <div class="position-relative card-news-hover-overlay">
                                    <img src="{{ $news->featured_image }}" class="card-img-top" alt="{{ $news->title }}">
                                    <a href="{{ route('berita.show', $news->slug) }}" class="btn btn-warning p-2 btn-sm detail-btn">
                                        <i class="bx bx-info-circle"></i> Detail
                                    </a>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted pt-3 pb-2">
                                        <span class="badge bg-warning text-dark me-2"
                                            style="top: 1rem; left: 1rem; border-radius: 20px;">
                                            <span style="color: #0b6435;">{{ $news->category->category_name ?? 'Artikel' }}</span>
                                        </span>
                                        {{ \Carbon\Carbon::parse($news->published_at)->translatedFormat('j F Y') }}
                                    </p>
                                    <h5 class="card-title mb-2">
                                        <strong>{{ $news->title }}</strong></h5>
                                    <p class="card-text">{{ $news->excerpt }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        
                        @if($otherNews->count() == 0)
                        <div class="col-12 text-center">
                            <p class="text-muted">Belum ada berita yang tersedia.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        
            <div class="text-center mt-5">
                <a href="/beritaclient" class="btn btn-warning text-white px-4 py-2 btn-ripple fade-in-up">Lihat Semua Berita &rarr;</a>
            </div>
        </div>
    </section>


    <!-- Upcoming Events Section -->
    <section class="events-section position-relative text-white">
        <div class="bg-dark-green pt-4">
            <div class="container position-relative">
                <p class="mb-1 fade-in-left">Siapkah kamu?</p>
                <h2 class="fw-bold fade-in-left" style="transition-delay: 0.2s;">Acara Mendatang</h2>
                <div class="container-sm mt-5" style="padding: 0 50px;">
                    <div id="brandCarousel" class="carousel slide brand-carousel" data-bs-ride="carousel">
                        
                        <div class="carousel-inner bounce-card animate-bounce">
                            @forelse ($events->chunk(3) as $chunkIndex => $eventChunk)
                                <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
                                    <div class="row justify-content-center">
                                        @foreach ($eventChunk as $event)
                                            @php
                                                $isExpired = \Carbon\Carbon::parse($event->event_date)->isPast();
                                                $imgStyle = $isExpired ? 'filter: grayscale(100%) brightness(0.8); opacity: 8;' : '';
                                            @endphp
                                            <div class="col-md-3 col-sm-12 mb-4">
                                                <div class="brand-card shadow position-relative" style="overflow: hidden;">
                                                    <img src="{{ asset($event->image_url) }}"
                                                         class="img-fluid event-image"
                                                         style="height: 100%; width: 100%; object-fit: cover; object-position: center; {{ $imgStyle }}"
                                                         alt="{{ $event->event_name }}">
                
                                                    @if($isExpired)
                                                        <div class="position-absolute top-0 start-0 m-2 z-3">
                                                            <span class="badge bg-warning text-dark px-3 py-2 fw-bold">Sudah Berakhir</span>
                                                        </div>
                                                    @endif
                
                                                    <div class="event-overlay position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center text-center"
                                                         style="background: rgba(0, 0, 0, 0.4); opacity: 0; transition: opacity 0.3s;">
                                                        <div class="px-3 text-white">
                                                            <h5 class="fw-bold mb-2">{{ $event->event_name }}</h5>
                                                            <p class="text-white-50 small mb-3">
                                                                <i class="bx bx-calendar me-1"></i>
                                                                {{ \Carbon\Carbon::parse($event->event_date)->translatedFormat('d F Y') }}
                                                            </p>
                                                            <p class="text-white-50 small mb-3">
                                                                <i class="bx bx-map me-1"></i>
                                                                {{ $event->location }}
                                                            </p>
                                                            <button class="btn btn-success btn-sm px-4 py-2" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#eventModal{{ $event->id }}">
                                                                <i class="bx bx-info-circle me-1"></i>
                                                                Selengkapnya
                                                            </button>
                                                        </div>
                                                    </div>
                
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="carousel-item active">
                                    <div class="brand-card mx-auto text-center">
                                        <h5>Belum ada acara mendatang.</h5>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                
                        <!-- Prev & Next button dipindah keluar dari .carousel-inner -->
                        <button class="carousel-control-prev" type="button" data-bs-target="#brandCarousel" data-bs-slide="prev">
                            <i class="bx bx-chevron-left"></i>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#brandCarousel" data-bs-slide="next">
                            <i class="bx bx-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pattern Background -->
        <div class="pattern-bg position-absolute start-0 w-100"></div>
    </section>

    <!-- Event Modals - Placed outside foreach loop -->
    @foreach ($events as $event)
    <div class="modal fade" id="eventModal{{ $event->id }}" tabindex="-1" aria-labelledby="eventModalLabel{{ $event->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content text-dark">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="eventModalLabel{{ $event->id }}">
                        <i class="bx bx-calendar-event me-2"></i>
                        {{ $event->event_name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <img src="{{ asset($event->image_url) }}" 
                                 class="img-fluid rounded shadow" 
                                 alt="{{ $event->event_name }}"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="col-md-6">
                            <div class="event-details">
                                <div class="mb-3">
                                    <h6 class="text-success fw-bold">
                                        <i class="bx bx-calendar me-2"></i>Tanggal
                                    </h6>
                                    <p class="mb-0">{{ \Carbon\Carbon::parse($event->event_date)->translatedFormat('d F Y') }}</p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-success fw-bold">
                                        <i class="bx bx-time me-2"></i>Waktu
                                    </h6>
                                    <p class="mb-0">{{ $event->event_time }}</p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-success fw-bold">
                                        <i class="bx bx-map me-2"></i>Lokasi
                                    </h6>
                                    <p class="mb-0">{{ $event->location }}</p>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-success fw-bold">
                                        <i class="bx bx-info-circle me-2"></i>Deskripsi
                                    </h6>
                                    <p class="mb-0">{!! nl2br(e($event->description)) !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--<div class="mt-4">-->
                    <!--    <h6 class="text-success fw-bold">-->
                    <!--        <i class="bx bx-info-circle me-2"></i>Deskripsi-->
                    <!--    </h6>-->
                    <!--    <p class="text-muted">{!! nl2br(e($event->description)) !!}</p>-->
                    <!--</div>-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Tutup
                    </button>
                    <!--<button type="button" class="btn btn-success">-->
                    <!--    <i class="bx bx-bookmark me-1"></i>Simpan Event-->
                    <!--</button>-->
                </div>
            </div>
        </div>
    </div>
    @endforeach
    
    @php
        // Get footer data (you can call this from any controller)
        $footerData = App\Http\Controllers\Gondowangi\AdminController\Footer\FooterController::getFooterData();
    @endphp
    
    @if($footerData['status'])
    <!-- Footer -->
    <footer class="bg-dark-green text-white pt-5" style="margin-top: 300px;">
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
                        
                        @if($footerData['phone'])
                        <li class="d-flex mb-3">
                            <i class='bx bx-phone me-2 mt-1'></i>
                            <span>{{ $footerData['phone'] }}</span>
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

    <!-- Bootstrap 5 JS -->
    <!-- Bootstrap 5 JS (dengan Popper) -->
    <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.0/dist/boxicons.min.js"></script>
    <script>
        // Trigger animations on page load for initial elements
        document.addEventListener('DOMContentLoaded', function () {
            // Animate nav items on load
            setTimeout(() => {
                document.querySelectorAll('.navbar .fade-in-up').forEach(el => {
                    el.classList.add('visible');
                });
            }, 300);

            // Set up intersection observer for scroll animations
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                root: null,
                threshold: 0.1,
                rootMargin: '-50px'
            });

            // Observe all elements with animation classes
            document.querySelectorAll('.fade-in-up, .fade-in-left, .fade-in-right, .zoom-in, .stat-item').forEach(el => {
                observer.observe(el);
            });

            // Counter animation for statistics
            const startCounters = () => {
                document.querySelectorAll('.counter').forEach(counter => {
                    const target = +counter.getAttribute('data-target');
                    const duration = 2000; // 2 seconds
                    const increment = target / (duration / 16); // 60fps

                    let current = 0;
                    const updateCounter = () => {
                        current += increment;
                        if (current < target) {
                            counter.textContent = Math.ceil(current);
                            requestAnimationFrame(updateCounter);
                        } else {
                            counter.textContent = target + '+';
                        }
                    };

                    updateCounter();
                });
            };

            // Trigger counters when stat section is visible
            const statsObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        startCounters();
                        statsObserver.unobserve(entry.target);
                    }
                });
            });

            statsObserver.observe(document.querySelector('.stats-container'));

            // Scroll progress bar
            window.addEventListener('scroll', () => {
                const scrollTop = window.scrollY;
                const docHeight = document.body.offsetHeight - window.innerHeight;
                const scrollPercent = scrollTop / docHeight * 100;
                document.querySelector('.scroll-progress').style.width = scrollPercent + '%';
            });
        });
    </script>

    <!--<div class="container position-relative" style="margin-bottom: 70px;">-->
    <!--    <div class="row align-items-center mb-4">-->
            <!-- Judul & Controls -->
    <!--        <div class="col-lg-8">-->
    <!--            <h6 style="font-size: 30px;" class="fade-in-left"><strong>Award kami</strong></h6>-->
    <!--            <h2 class="fw-bold fade-in-left"-->
    <!--                style="color: #0E6A39; font-size: 53.658px; transition-delay: 0.2s;">-->
    <!--                Penghargaan &amp;<br>Pencapaian-->
    <!--            </h2>-->
    <!--        </div>-->
    <!--        <div class="col-lg-4 text-lg-end fade-in-right">-->
    <!--            <button id="awardsPrev" class="btn btn-outline-success me-3 pb-2 btn-ripple"-->
    <!--                style="border-radius: 92px; padding: 0 30px; font-size: 30px;">-->
    <!--                &larr;-->
    <!--            </button>-->
    <!--            <span id="awardsIndex" class="me-3" style="color: #0E6A39; font-size: 20px;">-->
    <!--                <strong>01</strong>/<span id="awardsTotal">{{ str_pad($awards->count(), 2, '0', STR_PAD_LEFT) }}</span>-->
    <!--            </span>-->
    <!--            <button id="awardsNext" class="btn btn-outline-success pb-2 btn-ripple"-->
    <!--                style="border-radius: 92px; padding: 0 30px; font-size: 30px;">-->
    <!--                &rarr;-->
    <!--            </button>-->
    <!--        </div>-->
    <!--    </div>-->
    
        <!-- Carousel Track -->
    <!--    <div class="awards-carousel d-flex overflow-hidden position-relative">-->
    <!--        @if(isset($awards) && $awards->count() > 0)-->
    <!--            @foreach($awards as $index => $award)-->
    <!--                <div class="award-item flex-shrink-0 px-2 fade-in-up" style="transition-delay: {{ 0.2 * $index }}s;">-->
    <!--                    <div class="border border-2" style="border-radius: 31px; border: 2px solid #000;">-->
    <!--                        <img src="{{ $award->image_url ? asset($award->image_url) : asset('assets/award/default-award.png') }}" -->
    <!--                             class="card-img-top" -->
    <!--                             alt="{{ $award->award_name }}"-->
    <!--                             style="border-radius: 31px 31px 0 0;"-->
    <!--                             onerror="this.src='{{ asset('assets/award/default-award.png') }}'">-->
    <!--                        <div class="card-body p-3">-->
    <!--                            <small class="text-muted" style="font-size: 21px;">-->
    <!--                                {{ $award->award_date ? $award->award_date->format('M d, Y') : 'Date not specified' }}-->
    <!--                            </small>-->
    <!--                            <h5 class="card-title mt-3 mb-2" style="font-size: 26px;">-->
    <!--                                {{ $award->award_name }}-->
    <!--                            </h5>-->
    <!--                            <p class="card-text">-->
    <!--                                {{ Str::limit($award->award_description, 100) }}-->
    <!--                            </p>-->
    <!--                            @if($award->awarding_body)-->
    <!--                                <small class="text-muted d-block mt-2">-->
    <!--                                    <strong>Pemberi:</strong> {{ $award->awarding_body }}-->
    <!--                                </small>-->
    <!--                            @endif-->
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            @endforeach-->
    <!--        @else-->
                <!-- Fallback jika tidak ada data award -->
    <!--            <div class="award-item flex-shrink-0 px-2 fade-in-up">-->
    <!--                <div class="border border-2" style="border-radius: 31px; border: 2px solid #000;">-->
    <!--                    <img src="{{ asset('assets/award/default-award.png') }}" -->
    <!--                         class="card-img-top" -->
    <!--                         alt="No Award"-->
    <!--                         style="border-radius: 31px 31px 0 0;">-->
    <!--                    <div class="card-body p-3">-->
    <!--                        <small class="text-muted" style="font-size: 21px;">Coming Soon</small>-->
    <!--                        <h5 class="card-title mt-3 mb-2" style="font-size: 26px;">-->
    <!--                            Belum Ada Penghargaan-->
    <!--                        </h5>-->
    <!--                        <p class="card-text">-->
    <!--                            Penghargaan akan segera ditampilkan di sini.-->
    <!--                        </p>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        @endif-->
    <!--    </div>-->
    <!--</div>-->

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const carousel = document.querySelector('.awards-carousel');
            const prevBtn = document.getElementById('awardsPrev');
            const nextBtn = document.getElementById('awardsNext');
            const items = Array.from(document.querySelectorAll('.award-item'));
            const indexLbl = document.getElementById('awardsIndex');
            const totalLbl = document.getElementById('awardsTotal');
            // Hitung jumlah page berdasarkan view 3 per page
            const perPage = 3;
            const totalPages = Math.ceil(items.length / perPage);
            let currentPage = 0;
            totalLbl.textContent = totalPages.toString().padStart(2, '0');
            // Fungsi update index label
            function updateIndex() {
                indexLbl.querySelector('strong').textContent =
                    (currentPage + 1).toString().padStart(2, '0');
            }
            // Scroll ke halaman tertentu
            function goToPage(page) {
                currentPage = Math.max(0, Math.min(page, totalPages - 1));
                const scrollX = carousel.clientWidth * currentPage;
                carousel.scrollTo({ left: scrollX, behavior: 'smooth' });
                updateIndex();
            }
            // Tombol prev/next
            prevBtn.addEventListener('click', () => goToPage(currentPage - 1));
            nextBtn.addEventListener('click', () => goToPage(currentPage + 1));
            // Drag / swipe support
            let isDown = false, startX, scrollLeft;
            carousel.addEventListener('mousedown', e => {
                isDown = true;
                carousel.classList.add('dragging');
                startX = e.pageX - carousel.offsetLeft;
                scrollLeft = carousel.scrollLeft;
            });
            ['mouseup', 'mouseleave'].forEach(evt =>
                carousel.addEventListener(evt, () => {
                    isDown = false;
                    carousel.classList.remove('dragging');
                    // Setelah drag, hitung page baru
                    const newPage = Math.round(carousel.scrollLeft / carousel.clientWidth);
                    goToPage(newPage);
                })
            );
            carousel.addEventListener('mousemove', e => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - carousel.offsetLeft;
                carousel.scrollLeft = scrollLeft - (x - startX);
            });
            // IntersectionObserver untuk fade-in-up
            const io = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.3 });
            items.forEach(item => io.observe(item));
            // Inisialisasi ke page 0
            goToPage(0);
        });
    </script>

    <script>
        document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
            toggle.addEventListener('click', function (e) {
                const menu = bootstrap.Dropdown.getOrCreateInstance(this);
                if (this.classList.contains('show')) {
                    // Jika sudah terbuka, maka klik kedua kali navigasi
                    window.location.href = this.getAttribute('href');
                } else {
                    // Buka dropdown
                    menu.toggle();
                }
            });
        });
    </script>
</body>

</html>