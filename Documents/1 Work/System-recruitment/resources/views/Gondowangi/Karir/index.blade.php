@extends('Gondowangi.Main.main')

@section('head')
    <title>{{ $title }}</title>
    <style>
        body {
            overflow-x: hidden;
        }

        /* Hero Section */
        .hero {
            position: relative;
            background-size: cover;
            background-position: center;
            color: white;
            height: 80vh;
        }

        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('your-background-image.jpg');
            background-size: cover;
            background-position: center;
            opacity: 0.2;
            z-index: 0;
        }

        .hero .container {
            position: relative;
            z-index: 1;
            /* height: 100%; */
        }

        .dropdown-toggle::after {
            vertical-align: middle;
        }

        .hero h1 {
            font-size: clamp(2rem, 8vw, 5rem); /* Responsive font size */
            line-height: 1.1;
        }
        
        .hero .btn {
            font-size: clamp(0.9rem, 2vw, 1.1rem);
            padding: clamp(0.5rem, 2vw, 1rem) clamp(1rem, 3vw, 2rem);
        }

        .events-section h2 {
            font-size: clamp(2rem, 6vw, 3.2rem);
            margin-bottom: 1.5rem;
        }
        
        .events-section p {
            font-size: clamp(1rem, 3vw, 1.4rem);
            line-height: 1.6;
        }
        
        .hero p {
            font-size: 1.25rem;
            max-width: 600px;
        }

        .stats .stat {
            text-align: center;
        }

        .stats .stat h3 {
            font-size: 1.5rem;
            margin-bottom: 0;
        }

        .stats .stat p {
            margin: 0;
            color: #ddd;
        }

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
            height: 400px;
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
            width: 228.139px;
            height: 228.139px;
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
            border: 0;
            /*padding: 1rem 0;*/
            overflow: visible;
            /* agar date-box bisa absolute */
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-8px);
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
            top: 66%;
            height: 400px;
            background-color: #0B5932;
            background-repeat: repeat;
            /* opacity: 0.5; */
            z-index: -1;
            margin-bottom: 1000px;
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


        /* style untuk perjalanan perusahaan */
        .timeline-container {
            display: flex;
            align-items: flex-start;
            overflow-x: auto;
            scroll-behavior: smooth;
            cursor: grab;
            user-select: none;
            padding-bottom: 1rem;
        }

        .timeline-item {
            flex: 0 0 280px;
            margin-right: 4rem;
            position: relative;
            text-align: center;
            scroll-snap-align: start;
        }

        .timeline-container::-webkit-scrollbar {
            height: 8px;
        }

        .timeline-container::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            top: 2rem;
            left: -4rem;
            width: 4rem;
            height: 2px;
            background: #8cbf88;
        }

        .timeline-item:first-child::before {
            display: none;
        }

        .timeline-year {
            font-size: 1.5rem;
            font-weight: bold;
            color: #666;
        }

        .timeline-dot {
            width: 16px;
            height: 16px;
            border: 3px solid #8cbf88;
            background: white;
            border-radius: 50%;
            margin: 0.5rem auto 0;
            position: relative;
            z-index: 2;
            transition: background 0.3s, transform 0.3s;
            cursor: pointer;
        }

        .timeline-item.active .timeline-dot {
            background: #8cbf88;
            transform: scale(1.2);
        }

        .timeline-card {
            margin-top: 1rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 1rem;
        }

        .timeline-card img {
            width: 100%;
            border-radius: 8px;
            display: block;
        }

        .timeline-card h4 {
            margin: 0.5rem 0 0.25rem;
            color: #236e36;
        }

        .timeline-card p {
            font-size: 0.9rem;
            color: #444;
        }

        /* style untuk animasi catur pilar */
        .intro {
            font-size: 1.1rem;
            max-width: 800px;
            transform: translateY(20px);
            opacity: 0;
            animation: fadeInUp 1s 0.3s forwards;
        }

        .pilars-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .pilar-card {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            padding: 20px;
            transform: translateY(30px);
            opacity: 0;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }

        .pilar-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .pilar-icon {
            background-color: #f3f9f5;
            width: 100px;
            height: 100px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            flex-shrink: 0;
            border: 2px solid #0a6e31;
        }

        .pilar-icon svg {
            width: 60px;
            height: 60px;
            color: #0a6e31;
        }

        .pilar-content h3 {
            color: #0a6e31;
            font-size: 1.5rem;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .pilar-content p {
            font-size: 0.95rem;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes floatIcon {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        /* Animation delays for each card */
        .pilar-card:nth-child(1) {
            animation: fadeInUp 0.7s 0.6s forwards;
        }

        .pilar-card:nth-child(2) {
            animation: fadeInUp 0.7s 0.8s forwards;
        }

        .pilar-card:nth-child(3) {
            animation: fadeInUp 0.7s 1s forwards;
        }

        .pilar-card:nth-child(4) {
            animation: fadeInUp 0.7s 1.2s forwards;
        }

        /* Floating animation for icons */
        .pilar-card:hover .pilar-icon {
            animation: floatIcon 2s ease-in-out infinite;
        }

        @media (max-width: 1024px) {
            .pilars-container {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 576px) {
            .pilar-card {
                flex-direction: column;
                text-align: center;
            }

            .pilar-icon {
                margin-right: 0;
                margin-bottom: 15px;
            }
        }

        .job-carousel {
            cursor: grab;
            scroll-behavior: smooth;
            gap: clamp(0.5rem, 2vw, 1rem);
        }

        .job-carousel.dragging {
            cursor: grabbing;
        }
        
        /*.job-item {*/
        /*    flex: 0 0 clamp(280px, 40vw, 350px);*/
        /*    max-width: 350px;*/
        /*    cursor: pointer;*/
        /*}*/
        
        /* style.css (atau di <style> head) */
        .site-wrapper {
            /* Background */
            background-image: url('assets/background-tentang-kami.png');
            background-repeat: no-repeat;
            background-size: cover;
            /* selalau cover */
            background-position: center center;
            /* pusat vertical & horizontal */

            /* Full viewport height */
            width: 100%;
            min-height: 100vh;
            /* minimal setinggi viewport */
            display: flex;
            flex-direction: column;
            /* agar nav + hero bisa di-stretch */
        }

        /* Agar hero (bagian konten) vertikal center */
        .site-wrapper .hero {
            flex: 1;
            /* isi mengambil sisa ruang */
            display: flex;
            align-items: center;
        }

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
            /* agar konten di atas overlay */
        }
        
        /* Posisi dan spacing untuk arrow controls */
        .nav-wrapper {
            padding: 0 clamp(20px, 5vw, 40px);
        }
        
        /* Posisi vertikal tengah dan z-index */
        .nav-arrow {
          position: absolute;
          top: 50%;
          transform: translateY(-50%);
          z-index: 10;    /* pastikan di atas carousel */
          cursor: pointer;
        }
        
        /* Spasi dari tepi wrapper */
        .nav-arrow.left {
          left: -3rem;        /* tepat di padding wrapper kiri */
        }
        
        .nav-arrow.right {
          right: -3rem;       /* tepat di padding wrapper kanan */
        }
        
        /* Area klik lebih lega dan hover effect */
        .nav-arrow i {
          padding: 8px;   /* memperbesar area klik */
          background: rgba(255,255,255,0.6);
          border-radius: 4px;
          transition: background .2s ease;
        }
        
        .nav-arrow i:hover {
          background: rgba(255,255,255,0.9);
        }
        
        .job-item:hover .hover-overlay {
            opacity: 1 !important;
        }
        
        .event-card {
            transition: transform 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            background: #0b5932;
            min-height: 400px;
        }
        
        .event-card img {
            width: 100%;
            /*height: clamp(200px, 30vw, 250px);*/
            object-fit: cover;
        }
        
        .event-card .card-body {
            padding: clamp(1rem, 3vw, 1.5rem);
        }
        
        .event-card .card-title {
            font-size: clamp(1.2rem, 3vw, 1.7rem);
            margin-bottom: 0.75rem;
        }
        
        .event-card .card-text {
            font-size: clamp(1rem, 2.5vw, 1.1rem);
            margin-bottom: 1rem;
        }
        
        /* Navigation Arrows Responsive */
        .nav-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            cursor: pointer;
            transition: opacity 0.3s ease;
        }
        
        .nav-arrow.left {
            left: clamp(-2rem, -5vw, -3rem);
        }
        
        .nav-arrow.right {
            right: clamp(-2rem, -5vw, -3rem);
        }
        
        .nav-arrow i {
            padding: clamp(6px, 2vw, 12px);
            background: rgba(255,255,255,0.8);
            border-radius: 50%;
            font-size: clamp(1.5rem, 4vw, 2rem);
            transition: all 0.2s ease;
            color: #0b5932 !important;
        }
        
        .nav-arrow i:hover {
            background: rgba(255,255,255,0.95);
            transform: scale(1.1);
        }
        
        /* Hover Overlay Responsive */
        .hover-overlay {
            border-radius: 12px;
        }
        
        .hover-overlay .btn {
            font-size: clamp(0.9rem, 2.5vw, 1.1rem);
            padding: clamp(0.5rem, 2vw, 0.75rem) clamp(1rem, 3vw, 1.5rem);
        }
        
        /* Modal Responsive */
        .modal-dialog {
            margin: clamp(0.5rem, 3vw, 1.75rem);
            max-width: clamp(300px, 90vw, 800px);
        }
        
        .modal-header h5 {
            font-size: clamp(1.1rem, 3vw, 1.5rem);
        }
        
        .modal-body {
            padding: clamp(1rem, 3vw, 1.5rem);
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .job-meta-modal {
            padding: clamp(0.75rem, 2vw, 1rem);
            font-size: clamp(0.85rem, 2vw, 0.95rem);
        }
        
        .job-detail-section h6 {
            font-size: clamp(1rem, 2.5vw, 1.2rem);
        }
        
        .job-detail-section div {
            font-size: clamp(0.85rem, 2vw, 0.95rem);
        }
        
        /* ===== MEDIA QUERIES FOR SPECIFIC BREAKPOINTS ===== */
        
        /* Large screens (1200px and up) */
        @media (min-width: 1200px) {
            .container {
                max-width: 1140px;
            }
            
            .job-item {
                flex: 0 0 320px;
            }
            
            .nav-arrow {
                opacity: 0.7;
            }
            
            .nav-wrapper:hover .nav-arrow {
                opacity: 1;
            }
        }
        
        /* Medium screens (768px to 1199px) */
        @media (min-width: 768px) and (max-width: 1199.98px) {
            .hero .row {
                margin-top: 10rem;
            }
            
            .hero h1 {
                font-size: 3.5rem;
            }
            
            .job-item {
                flex: 0 0 300px;
            }
            
            .events-section h2 {
                font-size: 2.5rem;
            }
            
            .events-section p {
                font-size: 1.2rem;
            }
        }
        
        /* Small screens (576px to 767px) */
        @media (min-width: 576px) and (max-width: 767.98px) {
            .hero .row {
                margin-top: 8rem;
            }
            
            .hero .col-lg-12 {
                flex-direction: column;
                text-align: center;
                gap: 2rem;
            }
            
            .hero h1 {
                font-size: 2.8rem;
                margin-bottom: 1.5rem;
            }
            
            .hero .btn {
                align-self: center;
                margin-top: 0;
            }
            
            .job-item {
                flex: 0 0 280px;
            }
            
            .nav-arrow {
                display: none; /* Hide arrows on small screens, rely on swipe */
            }
            
            .job-carousel {
                scroll-snap-type: x mandatory;
                -webkit-overflow-scrolling: touch;
            }
            
            .job-item {
                scroll-snap-align: center;
            }
            
            .events-section {
                padding: 3rem 0;
            }
            
            .modal-dialog {
                margin: 0.5rem;
            }
        }
        
        /* Extra small screens (less than 576px) */
        @media (max-width: 575.98px) {
            .hero {
                height: auto;
                min-height: 70vh;
                padding: 2rem 0;
            }
            
            .hero .row {
                margin-top: 4rem;
            }
            
            .hero .col-lg-12 {
                flex-direction: column;
                text-align: center;
                gap: 1.5rem;
            }
            
            .hero h1 {
                font-size: 2.2rem;
                margin-bottom: 1rem;
                line-height: 1.2;
            }
            
            .hero .btn {
                align-self: center;
                margin-top: 0;
                padding: 0.75rem 1.5rem;
                font-size: 0.9rem;
            }
            
            .events-section {
                padding: 2rem 0;
                margin-top: 2rem;
            }
            
            .events-section h2 {
                font-size: 2rem;
                margin-bottom: 1rem;
            }
            
            .events-section p {
                font-size: 1rem;
                margin-bottom: 2rem;
            }
            
            .nav-wrapper {
                padding: 0 15px;
            }
            
            .nav-arrow {
                display: none;
            }
            
            .job-carousel {
                scroll-snap-type: x mandatory;
                -webkit-overflow-scrolling: touch;
                padding: 0 0.5rem;
            }
            
            .job-item {
                flex: 0 0 260px;
                margin: 0 0.5rem;
                scroll-snap-align: center;
            }
            
            .event-card {
                min-height: 350px;
            }
            
            .event-card img {
                height: 180px;
            }
            
            .event-card .card-body {
                padding: 1rem;
            }
            
            .event-card .card-title {
                font-size: 1.3rem;
            }
            
            .event-card .card-text {
                font-size: 0.95rem;
            }
            
            .hover-overlay .btn {
                font-size: 0.9rem;
                padding: 0.5rem 1rem;
            }
            
            /* Modal adjustments for mobile */
            .modal-dialog {
                margin: 0.25rem;
                width: calc(100% - 0.5rem);
            }
            
            .modal-header {
                padding: 1rem;
            }
            
            .modal-body {
                padding: 1rem;
                max-height: 60vh;
            }
            
            .modal-footer {
                padding: 1rem;
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .modal-footer .btn {
                width: 100%;
            }
            
            .job-meta-modal .row > div {
                margin-bottom: 0.75rem;
            }
            
            .job-detail-section {
                margin-bottom: 1.25rem;
            }
        }
        
        /* ===== ADDITIONAL RESPONSIVE UTILITIES ===== */
        
        /* Touch-friendly scrolling indicators */
        @media (max-width: 767.98px) {
            .job-carousel {
                scrollbar-width: thin;
                scrollbar-color: rgba(11, 89, 50, 0.3) transparent;
            }
            
            .job-carousel::-webkit-scrollbar {
                height: 4px;
            }
            
            .job-carousel::-webkit-scrollbar-track {
                background: rgba(0, 0, 0, 0.1);
                border-radius: 2px;
            }
            
            .job-carousel::-webkit-scrollbar-thumb {
                background: rgba(11, 89, 50, 0.5);
                border-radius: 2px;
            }
            
            /* Add scroll hint */
            .nav-wrapper::after {
                content: "← Geser untuk melihat lebih banyak →";
                position: absolute;
                bottom: -2rem;
                left: 50%;
                transform: translateX(-50%);
                font-size: 0.8rem;
                color: #6EA688;
                text-align: center;
                width: 100%;
                opacity: 0.8;
            }
        }
        
        /* Landscape orientation adjustments for mobile */
        @media (max-width: 767.98px) and (orientation: landscape) {
            .hero {
                height: auto;
                min-height: 60vh;
            }
            
            .hero .row {
                margin-top: 2rem;
            }
            
            .events-section {
                padding: 1.5rem 0;
            }
        }
        
        /* Print styles (optional) */
        @media print {
            .hero,
            .events-section {
                break-inside: avoid;
            }
            
            .nav-arrow,
            .hover-overlay,
            .modal {
                display: none;
            }
        }
        
        /* High DPI displays */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .event-card img {
                image-rendering: -webkit-optimize-contrast;
            }
        }
        
        /* Reduced motion preferences */
        @media (prefers-reduced-motion: reduce) {
            .event-card,
            .nav-arrow i,
            .hover-overlay,
            .job-carousel {
                transition: none;
            }
            
            .job-carousel {
                scroll-behavior: auto;
            }
        }
        
        .job-item:hover .event-card {
            transform: translateY(-5px);
        }
        
        .hover-overlay .btn:hover {
            background-color: #084128 !important;
            border-color: #084128 !important;
            transform: scale(1.05);
        }
        
        /* Modal Styling */
        .modal-header {
            background-color: #0b5932;
            color: #fff;
        }
        
        .modal-title {
            font-weight: bold;
            color: #fff;
        }
        
        .modal-body {
            color: #333;
            text-align: left;
        }
        
        .job-detail-section {
            margin-bottom: 1.5rem;
        }
        
        .job-detail-section h6 {
            color: #0b5932;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .job-detail-section div {
            color: #444;
            line-height: 1.6;
            text-align: left;
        }
        
        .job-meta-modal {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #e9ecef;
        }
        
        .job-meta-modal .row > div {
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .job-meta-modal strong {
            color: #0b5932;
        }
        
        .btn-apply {
            background-color: #0b5932;
            border-color: #0b5932;
            color: #fff;
            font-weight: bold;
        }
        
        .btn-apply:hover {
            background-color: #084128;
            border-color: #084128;
            color: #fff;
        }
        
        .btn-close {
            filter: invert(1);
        }
        /* animasi ketika hover card sudah Anda punya: .event-card:hover {...} */
    </style>
@endsection

@section('hero')
    <!-- Hero -->
    <section class="hero d-flex" style="height: 100%;">
        <div class="container h-100 d-flex justify-content-center align-items-center text-center text-white">
            <!-- Konten Hero -->
            <div class="row w-100" style="margin-top: 18rem;">
                <div class="col-lg-12 d-flex justify-content-between">
                    <h1 class="display-4 fw-bold mb-4 fade-in-left" style="font-size: 5rem; color: #FFCE00;">Let’s
                        Grow Together</h1>
                    <a href="#"
                        class="btn btn-warning text-white h-50 pt-3 mt-4 fw-medium px-4 btn-ripple fade-in-left"
                        style="transition-delay: 0.4s; letter-spacing: 2px; border-radius: 50px;">
                        <strong>Kenal Lebih Dekat</strong> <i class='bx bx-right-arrow-alt'></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <section class="events-section position-relative text-white pb-5 mt-5">
        <div class="pt-4">
            <div class="container text-center position-relative">
                <h2 class="fw-bold text-dark-green mb-3" style="color: #0E6A39; font-size: 3.2rem;">Grow With Gondowangi
                </h2>
                <p class="mb-5" style="color: #6EA688; font-size: 1.4rem;">
                    Bersama Gondowangi, Anda tidak hanya membangun karir, tetapi juga menjadi bagian dari misi <br>
                    besar
                    untuk menciptakan kebaikan melalui produk-produk alami yang ramah lingkungan. Mari <br> tumbuh,
                    berkembang, dan berkarya bersama kami!
                </p>
                <div class="row g-4 mt-4 p-3">
                    <div class="position-relative nav-wrapper">
                        <!-- Arrow Controls -->
                        <div class="nav-arrow left">
                            <i class='bx bx-chevron-left text-success bx-lg'></i>
                        </div>
                        <div class="nav-arrow right">
                            <i class='bx bx-chevron-right text-success bx-lg'></i>
                        </div>
                        
                        <!-- Carousel Track -->
                        <div class="job-carousel d-flex overflow-hidden">
                            @forelse($careerPositions as $position)
                            <!-- Job Item -->
                            <div class="job-item flex-shrink-0 mx-2 position-relative">
                                <div class="event-card h-100 animate__animated position-relative overflow-hidden">
                                    <!-- Default poster image - bisa diganti dengan gambar dari database jika ada field image -->
                                    <img src="{{ $position->image_url ?? 'assets/karir/BE slide 1 1.png' }}" class="card-img-top" alt="{{ $position->position_title }}">
                                    
                                    <!-- Overlay untuk hover effect -->
                                    <div class="hover-overlay rounded position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" 
                                         style="background: rgba(0,0,0,0.7); opacity: 0; transition: opacity 0.3s ease;">
                                        <button type="button" 
                                                class="btn btn-primary btn-lg text-white"
                                                style="background-color: #0b5932; border-color: #0b5932; color: #fff !important;"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#jobModal{{ $position->id }}">
                                            <i class="fas fa-eye me-2"></i>Detail
                                        </button>
                                    </div>
                                    
                                    <div class="card-body mt-4">
                                        <h5 class="card-title" style="color: #ffca2c; font-size: 1.7rem;">
                                            <strong>{{ $position->department }}</strong>
                                        </h5>
                                        <p class="card-text" style="color: #ffca2c;">{{ $position->position_title }}</p>
                                        <div class="job-meta mt-2">
                                            <small class="d-block text-white">{{ $position->job_type }}</small>
                                            <small class="d-block text-white">{{ $position->location }}</small>
                                            @if($position->deadline)
                                                <small class="d-block text-white">
                                                    Deadline: {{ $position->deadline->format('d M Y') }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            <!-- Modal Detail untuk setiap job -->
                            <div class="modal fade" id="jobModal{{ $position->id }}" tabindex="-1" aria-labelledby="jobModalLabel{{ $position->id }}" aria-hidden="true">
                                <div class="modal-dialog m-auto modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="jobModalLabel{{ $position->id }}">
                                                {{ $position->position_title }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Job Meta Information -->
                                            <div class="job-meta-modal">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>Posisi:</strong> {{ $position->position_title }}
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Tipe Pekerjaan:</strong> {{ $position->job_type }}
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Lokasi:</strong> {{ $position->location }}
                                                    </div>
                                                    <div class="col-md-6">
                                                        @if($position->deadline)
                                                            <strong>Deadline:</strong> {{ $position->deadline->format('d M Y') }}
                                                        @endif
                                                    </div>
                                                    @if($position->salary_range)
                                                    <!--<div class="col-md-12">-->
                                                    <!--    <strong>Rentang Gaji:</strong> {{ $position->salary_range }}-->
                                                    <!--</div>-->
                                                    @endif
                                                </div>
                                            </div>
                        
                                            <!-- Job Description -->
                                            @if($position->description)
                                            <div class="job-detail-section">
                                                <h6>Deskripsi Pekerjaan</h6>
                                                <div>{!! nl2br(e($position->description)) !!}</div>
                                            </div>
                                            @endif
                        
                                            <!-- Requirements -->
                                            @if($position->requirements)
                                            <div class="job-detail-section">
                                                <h6>Persyaratan</h6>
                                                <div>{!! nl2br(e($position->requirements)) !!}</div>
                                            </div>
                                            @endif
                        
                                            <!-- Benefits -->
                                            @if($position->benefits)
                                            <div class="job-detail-section">
                                                <h6>Benefit & Fasilitas</h6>
                                                <div>{!! nl2br(e($position->benefits)) !!}</div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                            <!--<a href="{{ $position->application_link }}" -->
                                            <!--   class="btn btn-apply" -->
                                            <!--   target="_blank">-->
                                            <!--    <i class="fas fa-paper-plane me-2"></i>Daftar Sekarang-->
                                            <!--</a>-->
                                            
                                            <!--<a href="{{ $position->application_link }}" -->
                                            <!--   class="btn btn-apply" -->
                                            <!--   target="_blank">-->
                                            <!--    <i class="fas fa-paper-plane me-2"></i>Daftar Sekarang-->
                                            <!--</a>-->
                                            
                                            <a href="{{ auth()->check() ? url('kandidat/form-pengisian') : url('/masuk') }}"
                                               class="btn btn-apply">
                                                <i class="fas fa-paper-plane me-2"></i>Daftar Sekarang
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <!-- Jika tidak ada data lowongan -->
                            <div class="job-item flex-shrink-0 mx-2">
                                <div class="event-card h-100 animate__animated">
                                    <div class="card-body text-center py-5">
                                        <h5 class="card-title" style="color: #0b5932;">
                                            <strong>Tidak Ada Lowongan</strong>
                                        </h5>
                                        <p class="card-text" style="color: #0b5932;">
                                            Saat ini belum ada lowongan kerja yang tersedia
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pattern Background -->
        <div class="pattern-bg position-absolute start-0 w-100"></div>
        <hr>
    </section>
@endsection

@section('script')
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const timeline = document.getElementById('timeline');
            let isDown = false, startX = 0, scrollLeft = 0;

            // Drag to scroll
            timeline.addEventListener('mousedown', (e) => {
                isDown = true;
                startX = e.pageX - timeline.offsetLeft;
                scrollLeft = timeline.scrollLeft;
                timeline.style.cursor = 'grabbing';
            });
            timeline.addEventListener('mouseup', () => { isDown = false; timeline.style.cursor = 'grab'; });
            timeline.addEventListener('mouseleave', () => { isDown = false; timeline.style.cursor = 'grab'; });
            timeline.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - timeline.offsetLeft;
                const walk = (x - startX) * 1.5;
                timeline.scrollLeft = scrollLeft - walk;
            });

            // Clickable dots
            document.querySelectorAll('.timeline-dot').forEach((dot) => {
                dot.addEventListener('click', () => {
                    const item = dot.closest('.timeline-item');
                    item.scrollIntoView({ behavior: 'smooth', block: 'nearest', 'inline': 'center' });
                });
            });

            // Arrow navigation
            document.getElementById('arrow-left').addEventListener('click', () => {
                timeline.scrollBy({ left: -300, behavior: 'smooth' });
            });
            document.getElementById('arrow-right').addEventListener('click', () => {
                timeline.scrollBy({ left: 300, behavior: 'smooth' });
            });

            // IntersectionObserver untuk highlight
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        entry.target.classList.toggle('active', entry.isIntersecting);
                    });
                }, { root: timeline, threshold: 0.6 });
                document.querySelectorAll('.timeline-item').forEach(item => observer.observe(item));
            }
        });
    </script>

    <script>
        // Intersection Observer for additional scroll animations
        document.addEventListener('DOMContentLoaded', function () {
            // Add hover effect for each card
            const cards = document.querySelectorAll('.pilar-card');

            cards.forEach(card => {
                card.addEventListener('mouseenter', function () {
                    this.style.backgroundColor = '#f9fff9';
                });

                card.addEventListener('mouseleave', function () {
                    this.style.backgroundColor = 'white';
                });
            });

            // Add click interaction
            cards.forEach(card => {
                card.addEventListener('click', function () {
                    // Add a pulse effect when clicked
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 300);

                    // Rotate the icon when clicked
                    const icon = this.querySelector('.pilar-icon');
                    icon.style.transition = 'transform 0.5s ease';
                    icon.style.transform = 'rotate(360deg)';

                    setTimeout(() => {
                        icon.style.transform = '';
                    }, 500);
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const carousel = document.querySelector('.job-carousel');
            const leftBtn = document.querySelector('.nav-arrow.left');
            const rightBtn = document.querySelector('.nav-arrow.right');
            const items = document.querySelectorAll('.job-item');

            // Hitung lebar satu item + margin
            const itemWidth = items[0].getBoundingClientRect().width + 16 /* mx-2 */;

            // Tombol geser
            leftBtn.addEventListener('click', () => {
                carousel.scrollBy({ left: -itemWidth * 1.2, behavior: 'smooth' });
            });
            rightBtn.addEventListener('click', () => {
                carousel.scrollBy({ left: itemWidth * 1.2, behavior: 'smooth' });
            });

            // Drag / Swipe support
            let isDown = false, startX, scrollLeft;
            carousel.addEventListener('mousedown', e => {
                isDown = true;
                carousel.classList.add('dragging');
                startX = e.pageX - carousel.offsetLeft;
                scrollLeft = carousel.scrollLeft;
            });
            carousel.addEventListener('mouseleave', () => { isDown = false; carousel.classList.remove('dragging'); });
            carousel.addEventListener('mouseup', () => { isDown = false; carousel.classList.remove('dragging'); });
            carousel.addEventListener('mousemove', e => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - carousel.offsetLeft;
                const walk = (x - startX) * 1.2; // kecepatan drag
                carousel.scrollLeft = scrollLeft - walk;
            });

            // IntersectionObserver untuk animate on scroll
            const io = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate__fadeInUp', 'visible');
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.3 });

            items.forEach(item => io.observe(item));
        });
    </script>
@endsection