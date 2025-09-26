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

        /*.site-wrapper {*/
            /* Background */
        /*    background-image: url('assets/brand/banner-16_9--new-2025.png');*/
        /*    background-repeat: no-repeat;*/
        /*    background-size: cover;*/
            /* selalau cover */
        /*    background-position: center center;*/
            /* pusat vertical & horizontal */

            /* Full viewport height */
        /*    width: 100%;*/
        /*    min-height: 100vh;*/
            /* minimal setinggi viewport */
        /*    display: flex;*/
        /*    flex-direction: column;*/
            /* agar nav + hero bisa di-stretch */
        /*}*/

        /* Agar hero (bagian konten) vertikal center */
        /*.site-wrapper .hero {*/
        /*    flex: 1;*/
            /* isi mengambil sisa ruang */
        /*    display: flex;*/
        /*    align-items: center;*/
        /*}*/

        /* Optional: jika ingin overlay gelap di atas gambar */
        /*.site-wrapper::before {*/
        /*    content: "";*/
        /*    position: absolute;*/
        /*    inset: 0;*/
        /*    z-index: 0;*/
        /*}*/

        /*.site-wrapper>* {*/
        /*    position: relative;*/
        /*    z-index: 1;*/
            /* agar konten di atas overlay */
        /*}*/

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
            width: 260px;
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
    </style>
    
@endsection

@section('style')
    color: #0E6A39 !important;"
@endsection

@section('style-navbar')
    bg-white
@endsection


@section('content')
<!-- Hero Carousel -->
    <section class="hero position-relative" style="margin-bottom: 190px;">
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
                                 style=" z-index: 1;"></div>
            
                            <!-- Text Layer on Top -->
                            <div class="container text-white position-relative" style="z-index: 2;">
                                <!-- Statistik: dengan border-start tebal -->
                                <!--<div class="position-absolute top-0 end-0" style="z-index: 3;">-->
                                <!--    <div class="d-flex align-items-center text-center text-md-end">-->
                                <!--        <div class="pe-3">-->
                                <!--            <h3 class="fw-bold mb-0" style="color: #a8de66;">250+</h3>-->
                                <!--            <p class="small mb-0" style="color: #a8de66;">Gondowangi Crews</p>-->
                                <!--        </div>-->
                                <!--        <div class="border-start border-3 mx-3" style="height: 3rem; border-left-color: #a8de66 !important;"></div>-->
                                <!--        <div class="px-3">-->
                                <!--            <h3 class="fw-bold mb-0" style="color: #a8de66;">50+</h3>-->
                                <!--            <p class="small mb-0" style="color: #a8de66;">Distribution Center</p>-->
                                <!--        </div>-->
                                <!--        <div class="border-start border-3 border-success mx-3" style="height: 3rem; border-left-color: #a8de66 !important;"></div>-->
                                <!--        <div class="px-3">-->
                                <!--            <h3 class="fw-bold mb-0" style="color: #a8de66;">40+</h3>-->
                                <!--            <p class="small mb-0" style="color: #a8de66;">CSR Partnership</p>-->
                                <!--        </div>-->
                                <!--        <div class="border-start border-3 border-success mx-3" style="height: 3rem; border-left-color: #a8de66 !important;"></div>-->
                                <!--        <div class="ps-3">-->
                                <!--            <h3 class="fw-bold mb-0" style="color: #a8de66;">100+</h3>-->
                                <!--            <p class="small mb-0" style="color: #a8de66;">SKU Produk</p>-->
                                <!--        </div>-->
                                <!--    </div>-->
                                <!--</div>-->
            
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

    <section class="events-section position-relative text-white mt-5 pb-0">
        
        <!--style="background: url('assets/background-all-brand.png') no-repeat center/cover;"-->

        <!-- header section -->
        <div class="pt-4">
            <div class="container text-center position-relative">
                <p class="mb-5" style="color: #0E6A39; font-size: 2rem;">
                    "Kami percaya, yang alami selalu lebih baik untuk kulit, rambut, dan bumi. Temukan keindahan aslimu,
                    karena kecantikan sejati dimulai dari alam."
                </p>

                <div class="align-items-center">
                    <button class="btn btn-warning text-white me-4" type="submit" style="border-radius: 14px;">Beli
                        Sekarang <i class='bx bx-right-arrow-alt'></i></button>
                    <button class="btn btn-outline-success " type="submit" style="border-radius: 14px;">Tentang
                        Produk</button>
                </div>
            </div>
        </div>

        <div class="container-sm mt-5 position-relative"> <!-- pastikan posisi relatif -->
            <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="true" data-bs-interval="1200">
                @if($carouselbrand->count() > 0)
                    <!-- Indicators -->
                    <div class="carousel-indicators custom-indicators">
                        @foreach($carouselbrand as $key => $brand)
                            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="{{ $key }}"
                                class="{{ $key == 0 ? 'active' : '' }}" aria-current="{{ $key == 0 ? 'true' : 'false' }}" 
                                aria-label="Slide {{ $key + 1 }}"></button>
                        @endforeach
                    </div>
                    
                    <!-- Carousel Items -->
                    <div class="carousel-inner" style="padding: 0 120px;">
                        @foreach($carouselbrand as $key => $brand)
                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                <img src="{{ asset($brand->image_path) }}" 
                                     class="d-block w-100 border border-1 rounded" 
                                     alt="{{ $brand->title ?? 'Brand Image' }}">
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Fallback jika tidak ada data -->
                    <div class="carousel-inner" style="padding: 0 120px;">
                        <div class="carousel-item active">
                            <img src="{{ asset('assets/background-awal.png') }}" 
                                 class="d-block w-100 border border-1 rounded" 
                                 alt="No Brand Available">
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-5">
            <div class="container text-center position-relative">
                <h2 class="fw-bold text-dark-green mb-3" style="color: #0E6A39; font-size: 3.2rem;">“Unsleashed Natural
                    Beauty”</h2>
                <p class="mt-5 text-dark" style="font-size: 1.4rem;">
                    Discover the power of nature with our skincare products. Crafted with the finest <br> natural
                    ingredients, our formulas provide gentle and effective care for your skin.
                </p>
            </div>
        </div>
        
       @php
            use Illuminate\Support\Str;
        @endphp

        <!-- BRAND CAROUSEL -->
        <div class="container-sm mt-5" style="padding: 0 100px; cursor: pointer;">
            <div id="brandCarousel" class="carousel slide brand-carousel" data-bs-ride="carousel">
                <div id="brandCarousel" class="carousel slide brand-carousel" data-bs-ride="carousel">
                    <button class="carousel-control-prev" type="button" data-bs-target="#brandCarousel" data-bs-slide="prev">
                        <i class="bx bx-chevron-left"></i>
                    </button>
                    
                    <div class="carousel-inner">
                        @if($brandChunks->count() > 0)
                            @foreach($brandChunks as $chunkIndex => $brandChunk)
                                <div class="carousel-item {{ $chunkIndex == 0 ? 'active' : '' }}">
                                    @foreach($brandChunk as $brand)
                                        @php
                                            $brandName = strtolower($brand->brand_name);
                                            $customUrl = '';
                
                                            if (Str::slug($brandName) === 'hg-for-men') {
                                                $customUrl = 'hgforman';
                                            } else {
                                                $firstWord = explode(' ', $brandName)[0];
                                                $customUrl = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($firstWord));
                                            }
                                        @endphp
                
                                        <div class="brand-card mx-auto">
                                            <img src="{{ asset('assets/brand/' . $brand->brand_img) }}" 
                                                 class="d-block" 
                                                 alt="{{ $brand->brand_name }}" />
                                            <h5>{{ $brand->brand_name }}</h5>
                                            <div class="btns mt-4">
                                                <a href="https://gondowangi.com/{{ $customUrl }}" 
                                                   class="btn btn-success">Selengkapnya</a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        @else
                            <div class="carousel-item active">
                                <div class="brand-card mx-auto">
                                    <img src="{{ asset('assets/brand/default-brand.png') }}" 
                                         class="d-block" 
                                         alt="No Brand Available" />
                                    <h5>No Brand Available</h5>
                                    <div class="btns mt-4">
                                        <a href="#" class="btn btn-success">Selengkapnya</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <button class="carousel-control-next" type="button" data-bs-target="#brandCarousel" data-bs-slide="next">
                        <i class="bx bx-chevron-right"></i>
                    </button>
                </div>
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