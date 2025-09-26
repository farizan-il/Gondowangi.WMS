@extends('Gondowangi.Main.main')


@section('head')
    <title>{{ $title }}</title>
    @php
        $bgUrl = (isset($backgroundBrand) && !empty($backgroundBrand->img))
            ? asset($backgroundBrand->img)
            : 'https://png.pngtree.com/png-clipart/20210523/original/pngtree-image-file-icon-that-is-damaged-or-corrupted-png-image_6315903.png';
    @endphp
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

        /* .hero::before {
      content: "";
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0, 100, 0, 0.6);
    } */
        .hero .container {
            position: relative;
            z-index: 1;
            /* height: 100%; */
        }

        .dropdown-toggle::after {
            vertical-align: middle;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
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

        .bg-dark-hg {
            background-color: #1B1B1B !important;
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
            height: 260px;
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

        .nav-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 32px;
            height: 32px;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 5;
            transition: background 0.2s;
        }

        .nav-arrow:hover {
            background: rgba(0, 0, 0, 0.2);
        }

        .nav-arrow.left {
            left: 1rem;
        }

        .nav-arrow.right {
            right: 1rem;
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

        .site-wrapper {
            /* Background */
            background-image: url('{{ $bgUrl }}');
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
            z-index: 0;
        }

        .site-wrapper>* {
            position: relative;
            z-index: 1;
            /* agar konten di atas overlay */
        }

        .custom-indicators {
            position: absolute;
            top: 50%;
            left: 85%;
            /* sesuaikan jarak dari kanan sesuai kebutuhan */
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            /* ubah ke vertikal */
            gap: 0.5rem;
            /* spasi antar indikator */
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .custom-indicators [data-bs-target] {
            width: 12px;
            /* ukuran bulatan */
            height: 12px;
            border-radius: 50%;
            /* bentuk bulat */
            border: 2px solid #0E6A39;
            /* warna border */
            opacity: 0.5;
            transition: opacity 0.3s;
        }

        .custom-indicators .active {
            background-color: #0E6A39;
            /* warna indikator aktif */
            opacity: 1;
            border: none;
            width: 16px;
            /* ukuran bulatan */
            height: 16px;
        }

        /* Wrapper & positioning */
        .brand-carousel {
            position: relative;
            padding: 2rem 0;
        }

        .brand-carousel .carousel-control-prev,
        .brand-carousel .carousel-control-next {
            width: 3rem;
            height: 3rem;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 50%;
            transition: background 0.3s ease;
        }

        .brand-carousel .carousel-control-prev:hover,
        .brand-carousel .carousel-control-next:hover {
            background: rgba(0, 0, 0, 0.2);
        }

        .brand-carousel .carousel-control-prev i,
        .brand-carousel .carousel-control-next i {
            font-size: 2rem;
            color: #0E6A39;
        }

        /* Brand Card */
        .brand-card {
            width: 300px;
            background: rgba(240, 12, 12, 0);
            overflow: hidden;
            text-align: center;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            position: relative;
        }

        .brand-card img {
            width: 100%;
            height: 180px;
            display: block;
            transition: transform 0.5s ease;
        }

        .brand-card h5 {
            margin: 1rem 0 0.5rem;
            color: #0E6A39;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .brand-card .btns {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.4s ease, transform 0.4s ease;
        }

        /* Hover Effects */
        .brand-card:hover {
            transform: translateY(-10px) scale(1.03);
        }

        .brand-card:hover h5 {
            color: #1a6e2b;
        }

        .brand-card:hover .btns {
            opacity: 1;
            transform: translateY(0);
        }

        /* Carousel-item centering */
        .brand-carousel .carousel-item {
            display: flex;
            justify-content: center;
        }

        /* Make carousel loop continuously */
        .brand-carousel .carousel-inner {
            overflow: visible;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .brand-card {
                width: 90%;
            }
        }

        /* ===== OUR NATURAL PRODUCT ===== */
        .natural-product {
            padding: 4rem 1rem;
            background: #fff;
        }

        .natural-product .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            color: #0d1b2a;
            margin-bottom: 3rem;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-out forwards;
            animation-delay: 0s;
        }

        .product-list {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        /* ===== PRODUCT CARD ===== */
        .product-card {
            display: flex;
            background: #f9fafb;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
            transform: translateY(20px);
            opacity: 0;
            animation: fadeInUp 0.6s ease-out forwards;
            animation-delay: var(--delay);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            transition:  0.3s ease, box-shadow 0.3s ease
        }

        .product-card:hover {
            transform: translateY(-6px) scale(1.02);
            background: #1B1B1B;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
        }

        .product-img {
            flex: 0 0 40%;
            overflow: hidden;
        }

        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-img img {
            transform: scale(1.1);
        }

        .product-info {
            flex: 1;
            padding: 1.5rem 2rem;
        }

        .product-info h3 {
            margin-bottom: 1rem;
            font-size: 1.25rem;
            color: #0d1b2a;
            transition: color 0.3s ease;
        }

        .product-card:hover .product-info h3 {
            color: #3cae53;
        }

        .product-info p {
            line-height: 1.6;
            color: #555;
            transition: color 0.3s ease;
        }

        .product-card:hover .product-info p {
            color: #fff;
        }

        /* ===== ANIMATION KEYFRAMES ===== */
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .product-card {
                flex-direction: column;
            }

            .product-img {
                flex: 0 0 auto;
                height: 200px;
            }

            .product-info {
                padding: 1rem;
            }
        }
    </style>
@endsection

@section('style')
    color: #0E6A39 !important;"
@endsection

@section('style-navbar')
    bg-white
@endsection

@section('content')

    <section class="events-section position-relative text-white mb-0 pb-0"
        style="background: url('assets/background-all-brand.png') no-repeat center/cover;">
        <div class="align-items-center  pt-5">
            <div class="row">
                <div class="col-lg-7 fade-in-left"
                    style="background-image: url('{{ asset($bannerBrand->img) }}'); height: 100vh; background-size: cover; background-repeat: no-repeat;">
                </div>

                <div class="col-lg-5 d-flex flex-column text-center justify-content-center align-items-center bg-dark fade-in-right">
                    <h2 style="font-size: 40px; color: #AADB67;" class="fade-in-left fw-bold">
                        {{ $bannerBrand->brand_name }}
                    </h2>
                    <h6 class="fade-in-left p-3" style="font-size: 23px; transition-delay: 0.2s;">
                        {{ $bannerBrand->deksripsi }}
                    </h6>
                </div>
            </div>
        </div>
    </section>

    <!-- OUR NATURAL PRODUCT -->
    <section class="natural-product mt-0 pt-0" style="background-image: url(assets/background-all-brand.png);">
        <div class="container">
            <h2 class="section-title pt-5">Our Natural Product HG</h2>
    
            <div class="product-list">
                @if($detailBrands && $detailBrands->count() > 0)
                    @foreach($detailBrands as $index => $product)
                        <div class="product-card" style="--delay: {{ $index * 0.1 }}s">
                            <div class="product-img">
                                <img src="{{ asset($product->img) }}" 
                                     alt="{{ $product->brand_name }} {{ $product->type }}">
                            </div>
                            <div class="product-info my-auto">
                                <h3>{{ $product->title }}</h3>
                                <p>{{ strip_tags($product->deksripsi) }}</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Fallback produk jika data tidak ada -->
                    <div class="product-card" style="--delay: 0s">
                        <div class="product-img">
                            <img src="https://media.istockphoto.com/id/1399588872/id/vektor/ikon-file-piksel-rusak-merusak-simbol-dokumen-tandatangani-vektor-data-yang-rusak.jpg?s=170667a&w=0&k=20&c=6haJtaspKQN653o3RQZq8oo_u5jRTX8reckO8H4TlkM=" alt="HG FOR MEN Shampoo">
                        </div>
                        <div class="product-info">
                            <!--<h3>HG FOR MEN Shampoo</h3>-->
                            <!--<p>-->
                            <!--    Shampoo yang terbuat dari bahan berkualitas serta asam amino-->
                            <!--    yang dapat membantu mencegah kerontokan berlebih dan-->
                            <!--    mempercepat pertumbuhan rambut.-->
                            <!--</p>-->
                        </div>
                    </div>
    
                    <div class="product-card" style="--delay: 0.1s">
                        <div class="product-img">
                            <img src="https://media.istockphoto.com/id/1399588872/id/vektor/ikon-file-piksel-rusak-merusak-simbol-dokumen-tandatangani-vektor-data-yang-rusak.jpg?s=170667a&w=0&k=20&c=6haJtaspKQN653o3RQZq8oo_u5jRTX8reckO8H4TlkM=" alt="HG For Men Brightening Facial Wash">
                        </div>
                        <div class="product-info">
                            <!--<h3>HG For Men Brightening Facial Wash</h3>-->
                            <!--<p>-->
                            <!--    Diformulasikan khusus untuk semua jenis kulit pria yang-->
                            <!--    berkhasiat bagi konsumen pria profesional dalam menjaga-->
                            <!--    kesehatan kulit wajah.-->
                            <!--</p>-->
                        </div>
                    </div>
    
                    <div class="product-card" style="--delay: 0.2s">
                        <div class="product-img">
                            <img src="https://media.istockphoto.com/id/1399588872/id/vektor/ikon-file-piksel-rusak-merusak-simbol-dokumen-tandatangani-vektor-data-yang-rusak.jpg?s=170667a&w=0&k=20&c=6haJtaspKQN653o3RQZq8oo_u5jRTX8reckO8H4TlkM=" alt="HG FOR MEN Hair Tonic">
                        </div>
                        <div class="product-info">
                            <!--<h3>HG FOR MEN Hair Tonic</h3>-->
                            <!--<p>-->
                            <!--    Vitamin rambut yang diformulasikan dari bahan berkualitas dan-->
                            <!--    asam amino yang can membantu menutrisi rambut,-->
                            <!--    menguatkan akar rambut.-->
                            <!--</p>-->
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        const carouselEl = document.querySelector('#brandCarousel');
        new bootstrap.Carousel(carouselEl, {
            interval: 2000,    // geser tiap 2 detik
            ride: 'carousel'
        });

    </script>

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
@endsection