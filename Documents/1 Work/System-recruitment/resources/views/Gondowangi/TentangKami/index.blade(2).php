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
            flex: 0 0 380px;
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
            top: 3.3rem;
            left: -111%;
            width: 116%;
            height: 0px;
            border-top: 2px dashed #8cbf88;
        }

        .timeline-item:first-child::before {
            display: none;
        }

        .timeline-year {
            width: 16px;
            font-size: 1.5rem;
            font-weight: bold;
            color: #666;
            height: 16px;
            margin-bottom: 30px;
            position: relative;
            z-index: 2;
            transition: background 0.3s, transform 0.3s;
            cursor: pointer;
        }

        .timeline-dot {
            width: 16px;
            height: 16px;
            border: 3px solid #8cbf88;
            background: white;
            border-radius: 50%;
            margin: 0.5rem auto 1;
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
            border-radius: 8px;
            padding: 0;
        }

        .timeline-card img {
            width: 100%;
            border-radius: 8px;
            display: block;
        }

        .timeline-card h5 {
            margin: 0.5rem 0 0.25rem;
            color: #236e36;
            text-align: start;
        }

        .timeline-card p {
            font-size: 0.9rem;
            color: #444;
            text-align: start;
        }

        .nav-arrow {
            position: absolute;
            top: 21%;
            transform: translateY(-50%);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 5;
            transition: background 0.2s;
        }

        .nav-arrow.left {
            left: -3rem;
        }

        .nav-arrow.right {
            right: -3rem;
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
            border-radius: 12px;
            overflow: hidden;
            
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

        .timeline-carousel {
            cursor: grab;
            scroll-behavior: smooth;
        }

        .timeline-carousel.dragging {
            cursor: grabbing;
        }

        /* Atur lebar tiap item, misal tampil 3 per view */
        .timeline-item {
            width: calc(33.333% - 16px);
            /* sesuaikan margin px */
        }

        /* style.css (atau di <style> head) */
        /*.site-wrapper {*/
            /* Background */
        /*    background-image: url('assets/banners/');*/
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

        .hero-section {
            background-color: #0e6437;
            color: white;
            height: 50vh;
            padding: 50px 20px;
            text-align: center;
            position: relative;
        }

        .hero-title {
            font-size: 48px;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
        }
        
        .hero-subtitle {
            font-size: 18px;
            max-width: 800px;
            margin: 0 auto 40px;
            position: relative;
            z-index: 2;
            line-height: 1.6;
        }
        
        .plus-pattern {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
        }
        
        .plus {
            position: absolute;
            color: rgba(255,255,255,0.2);
            font-size: 24px;
        }
        
        .products {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 3;
            margin-top: -130px;
        }
        
        .product-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            width: 300px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.4s ease;
            position: relative;
            top: 0;
        }
        
        .product-card:hover {
            transform: translateY(-15px) scale(1.03);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .product-image {
            height: 300px;
            overflow: hidden;
            position: relative;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s ease;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.08);
        }
        
        .product-info {
            padding: 20px;
            text-align: center;
        }
        
        .product-name {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }
        
        .product-description {
            color: #777;
            font-size: 14px;
            font-style: italic;
        }
        
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(14, 100, 55, 0.8);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.4s ease;
            padding: 20px;
        }
        
        .product-card:hover .overlay {
            opacity: 1;
        }
        
        .btn {
            /*background-color: white;*/
            color: #0e6437;
            border: none;
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s;
        }
        
        .btn:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .footer {
            background-color: #0e6437;
            color: white;
            text-align: center;
            padding: 30px 20px;
            margin-top: 50px;
        }
        
        .footer p {
            margin: 10px 0;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        
        .social-links a {
            color: white;
            font-size: 20px;
            transition: transform 0.3s;
        }
        
        .social-links a:hover {
            transform: translateY(-5px);
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            z-index: 100;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .modal.active {
            display: flex;
            opacity: 1;
        }
        
        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 800px;
            width: 90%;
            position: relative;
            transform: translateY(50px);
            opacity: 0;
            transition: all 0.4s ease;
        }
        
        .modal.active .modal-content {
            transform: translateY(0);
            opacity: 1;
        }
        
        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #777;
            transition: color 0.3s;
        }
        
        .close-btn:hover {
            color: #0e6437;
        }
        
        .product-details {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }
        
        .product-details-image {
            flex: 1;
            min-width: 300px;
        }
        
        .product-details-image img {
            width: 100%;
            border-radius: 8px;
        }
        
        .product-details-info {
            flex: 1;
            min-width: 300px;
        }
        
        .product-details-name {
            font-size: 28px;
            margin-bottom: 15px;
            color: #0e6437;
        }
        
        .product-details-description {
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .features {
            margin-bottom: 20px;
        }
        
        .feature {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .feature-icon {
            color: #0e6437;
            margin-right: 10px;
            font-size: 18px;
        }
        
        .price {
            font-size: 24px;
            font-weight: bold;
            color: #0e6437;
            margin-bottom: 20px;
        }
        
        /* Animations */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
        
        .floating {
            animation: float 5s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .pulsing {
            animation: pulse 2s ease-in-out infinite;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 36px;
            }
            
            .hero-subtitle {
                font-size: 16px;
            }
            
            .nav {
                flex-direction: column;
                gap: 10px;
            }
            
            .product-details {
                flex-direction: column;
            }
        }
    </style>
    
    <style>
        .hero-slide {
            min-height: 100vh;
            position: relative;
        }
        
        /*.carousel-fade .carousel-item {*/
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
        
        /* Gaya khusus untuk acara kadaluarsa */
        .expired-event .event-card {
            background-color:rgb(132, 127, 127);  /* Warna latar belakang yang menandakan acara kadaluarsa */
            opacity: 0.6;  /* Kurangi opasitas untuk acara kadaluarsa */
            pointer-events: none; /* Menonaktifkan interaksi */
        }
    </style>
@endsection

@section('hero')

    <!--carousel-->
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

    
@endsection

@section('content')
<!-- Hero -->
    <!--<section class="hero d-flex" style="height: 100%; margin-top: 6rem;">-->
    <!--    <div class="container h-100 d-flex justify-content-center align-items-center text-center text-white">-->
            <!-- Konten Hero -->
    <!--        <div class="row w-100">-->
    <!--            <div class="col-lg-12">-->
    <!--                <h1 class="display-4 fw-bold mb-4 fade-in-left" style="font-size: 4rem;">Alami Lebih Baik</h1>-->
    <!--                <h5 class="lead text-center fw-medium mb-4 fade-in-left"-->
    <!--                    style="transition-delay: 0.2s; font-size: 1.3rem;">-->
    <!--                    Visi kami sejak awal adalah menghadirkan produk perawatan yang tidak hanya efektif tetapi-->
    <!--                    juga aman dan ramah lingkungan. Kami percaya bahwa kekayaan alam Indonesia, mulai dari lidah-->
    <!--                    buaya, jahe, hingga urang-aring, memiliki potensi luar biasa untuk kesehatan dan kecantikan.-->
    <!--                </h5>-->
    <!--                <a href="#" class="btn btn-warning  fw-medium px-4 py-2 btn-ripple fade-in-left"-->
    <!--                    style="transition-delay: 0.4s;">-->
    <!--                    Beli Sekarang <i class='bx bx-right-arrow-alt'></i>-->
    <!--                </a>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</section>-->
    <!-- perjalanan perusahaan -->
    <section id="awards" class="py-5 position-relative"
        style="background: url('assets/background-web/green-leaves-water.png') no-repeat center/cover; margin-top: 10rem;">
        <div class="overlay position-absolute top-0 start-0 w-100 h-100">
        </div>

        <!-- Perjalana perusahaan -->
        <div class="container position-relative" style="margin-bottom: 70px;">
            <div class="align-items-center mb-4">
                <div class="row">
                    <div class="col-lg-8">
                        <h6 style="font-size: 30px;" class="fade-in-left">Cerita Perjalanan PT Gondowangi</h6>
                        <h2 class="fw-bold fade-in-left"
                            style="color: #0E6A39; font-size: 53.658px; transition-delay: 0.2s;">Perjalanan Bapak Liem
                            Soedarno memulai bisnisnya</h2>
                    </div>
                    <div class="col-lg-4 text-lg-end fade-in-right">
                        <img src="assets/perjalanan/mr-liem.png" alt="" srcset="" style="width: 369px; height: 385px;">
                    </div>
                </div>

                <div class="col-12 mt-5">
                    <div class="timeline-wrapper position-relative">
                        <!-- Panah kiri -->
                        <div class="nav-arrow left" id="tl-prev">
                            <i class="bx bx-chevron-left text-success bx-md"></i>
                        </div>
                        
                        <div class="timeline-carousel d-flex overflow-hidden" id="timeline">
                            @foreach($timelines as $timeline)
                                <div class="timeline-item flex-shrink-0 px-2" data-year="{{ $timeline->year }}">
                                    <div class="timeline-year">{{ $timeline->year }}</div>
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-card">
                                        <h5>{{ $timeline->title }}</h5>
                                        <p>{{ $timeline->description }}</p>
                                        <div class="position-relative card-news-hover-overlay">
                                            <img src="{{ $timeline->image_url }}"
                                                 class="card-img-top" alt="{{ $timeline->title }}">
                                            <a href="#" class="btn btn-warning p-2 btn-sm detail-btn">
                                                <i class="bx bx-info-circle"></i> Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Panah kanan -->
                        <div class="nav-arrow right" id="tl-next">
                            <i class="bx bx-chevron-right text-success bx-md"></i>
                        </div>
                    </div>
                </div>

                <!--<div class="col-lg-12 mt-3">-->
                <!--    <div class="row g-3">-->
                <!--        <div class="col-4 fade-in-right" style="transition-delay: 0.1s;">-->
                <!--            <div class="border-0">-->
                <!--                <div class="position-relative card-news-hover-overlay">-->
                <!--                    <img src="assets/perjalanan/Frame1.png"-->
                <!--                        class="card-img-top" alt="Networking session">-->
                <!--                    <a href="#" class="btn btn-warning p-2 btn-sm detail-btn">-->
                <!--                        <i class="bx bx-info-circle"></i> Detail-->
                <!--                    </a>-->
                <!--                </div>-->
                <!--                <div class="card-body">-->
                <!--                    <h5 class="card-title mt-4" style="color: #0a6e31">Jamu Tradisional-->
                <!--                    </h5>-->
                <!--                    {{-- <p class="card-text">Terima kasih untuk semua yang sudah hadir dan meramaikan Natur-->
                <!--                        Hair Beauty Dating! 💚</p> --}}-->
                <!--                </div>-->
                <!--            </div>-->
                <!--        </div>-->
                        
                        
                <!--        <div class="col-4 fade-in-right" style="transition-delay: 0.3s;">-->
                <!--            <div class="border-0">-->
                <!--                <div class="position-relative card-news-hover-overlay">-->
                <!--                    <img src="assets/perjalanan/Frame2.png"-->
                <!--                        class="card-img-top" alt="Networking session">-->
                <!--                    <a href="#" class="btn btn-warning p-2 btn-sm detail-btn">-->
                <!--                        <i class="bx bx-info-circle"></i> Detail-->
                <!--                    </a>-->
                <!--                </div>-->
                <!--                <div class="card-body">-->
                <!--                    <h5 class="card-title mt-4" style="color: #0a6e31">Kemasan Natur</h5>-->
                <!--                    {{-- <p class="card-text">Gondowangi berkesempatan mengunjungi LPPOM yang berada di-->
                <!--                        Gedung Global Halal Centre Bogor.</p> --}}-->
                <!--                </div>-->
                <!--            </div>-->
                <!--        </div>-->
                <!--        <div class="col-4 fade-in-right" style="transition-delay: 0.5s;">-->
                <!--            <div class="border-0">-->
                <!--                <div class="position-relative card-news-hover-overlay">-->
                <!--                    <img src="assets/perjalanan/Frame3.png"-->
                <!--                        class="card-img-top" alt="Networking session">-->
                <!--                    <a href="#" class="btn btn-warning p-2 btn-sm detail-btn">-->
                <!--                        <i class="bx bx-info-circle"></i> Detail-->
                <!--                    </a>-->
                <!--                </div>-->
                <!--                <div class="card-body">-->
                <!--                    <h5 class="card-title mt-4" style="color: #0a6e31">Produksi Masal</h5>-->
                <!--                    {{-- <p class="card-text">Kolaborasi ini memperkuat komitmen kami untuk selalu mendukung-->
                <!--                        usaha lokal dan menghadirkan...</p> --}}-->
                <!--                </div>-->
                <!--            </div>-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->
            </div>
        </div>

        <hr>

        <!-- section untuk catur pilar -->
        <div class="container" style="margin-top: 70px;">
            <header>
                <h1 style="color: #0E6A39; font-size: 3rem;"><strong>Catur Pilar Gondowangi</strong></h1>
                <p class="intro">"Catur Pilar" merupakan nilai-nilai inti yang menjadi pedoman bagi seluruh karyawan
                    Gondowangi dalam bekerja dan berkontribusi untuk kemajuan perusahaan. Keempat pilar ini mencerminkan
                    komitmen Gondowangi dalam menjalankan bisnis secara profesional, berorientasi pada kualitas, dan
                    berfokus pada kepuasan pelanggan.</p>
            </header>

            <div class="pilars-container">
                @foreach($caturPilar as $pilar)
                <div class="pilar-card">
                    <div class="pilar-icon">
                        @if($pilar->icon == 'shield')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        @elseif($pilar->icon == 'award')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="7"></circle>
                                <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                            </svg>
                        @elseif($pilar->icon == 'users')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        @elseif($pilar->icon == 'handshake')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        @else
                            <!-- Default icon jika tidak ada yang cocok -->
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        @endif
                    </div>
                    <div class="pilar-content">
                        <h3>{{ $pilar->judul }}</h3>
                        <p>{{ $pilar->deskripsi }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mb-5">
        <section class="hero-section" id="home">
            <div class="plus-pattern" id="plusPattern"></div>
            <h1 class="hero-title">Produk Berbahan Alami</h1>
            <p class="hero-subtitle">"Kami percaya, yang alami selalu lebih baik untuk kulit, rambut, dan bumi. Temukan keindahan aslimu, karena kecantikan sejati dimulai dari alam."</p>
        </section>
        
        <section class="products" id="products">
            <div class="product-card">
                <div class="product-image">
                    <img src="assets/perjalanan/5-September-Baru-Slide-2 1.png" alt="Perfect Wear Makeup">
                    <div class="overlay">
                        <button class="btn" onclick="openModal('perfect-wear')">Selengkapnya</button>
                    </div>
                </div>
                <div class="product-info">
                    <h3 class="product-name">Perfect Wear</h3>
                    <p class="product-description">Make-up dekoratif</p>
                </div>
            </div>
            
            <div class="product-card">
                <div class="product-image">
                    <img src="assets/perjalanan/WEB - MIZZU All Products 3.png" alt="Beautiful Youth">
                    <div class="overlay">
                        <button class="btn" onclick="openModal('beautiful-youth')">Selengkapnya</button>
                    </div>
                </div>
                <div class="product-info">
                    <h3 class="product-name">Beautiful Youth</h3>
                    <p class="product-description">It's your true color</p>
                </div>
            </div>
            
            <div class="product-card">
                <div class="product-image">
                    <img src="assets/perjalanan/WEB - MIZZU All Products 4.png" alt="Natur Beauty">
                    <div class="overlay">
                        <button class="btn" onclick="openModal('natur-beauty')">Selengkapnya</button>
                    </div>
                </div>
                <div class="product-info">
                    <h3 class="product-name">Natur Beauty</h3>
                    <p class="product-description">Chief Executive Operations</p>
                </div>
            </div>
        </section>
        
        <!-- Modals -->
        <div class="modal" id="perfect-wear-modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal('perfect-wear')">&times;</span>
                <div class="product-details">
                    <div class="product-details-image">
                        <img src="assets/perjalanan/5-September-Baru-Slide-2 1.png" alt="Perfect Wear Detail">
                    </div>
                    <div class="product-details-info">
                        <h2 class="product-details-name">Perfect Wear</h2>
                        <p class="product-details-description">Koleksi makeup dekoratif Mizzu yang dirancang untuk memberikan tampilan sempurna yang tahan lama. Terbuat dari bahan-bahan alami yang aman untuk kulit dan ramah lingkungan.</p>
                        
                        <div class="features">
                            <div class="feature">
                                <span class="feature-icon">✓</span>
                                <span>Perfect Wear - Foundation tahan lama dengan perlindungan kulit</span>
                            </div>
                            <div class="feature">
                                <span class="feature-icon">✓</span>
                                <span>Pro Liner - Eyeliner presisi tinggi dengan aplikasi mudah</span>
                            </div>
                            <div class="feature">
                                <span class="feature-icon">✓</span>
                                <span>Hide'em Concealer Orange - Concealer untuk menutupi lingkaran hitam</span>
                            </div>
                            <div class="feature">
                                <span class="feature-icon">✓</span>
                                <span>Airblush - Perona pipi alami dengan efek matte yang segar</span>
                            </div>
                        </div>
                        
                        <p class="price">Rp 50.000</p>
                       <a href="https://perfectbeauty.id/385_mizzu" class="btn pulsing" target="_blank">Beli Sekarang</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal" id="beautiful-youth-modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal('beautiful-youth')">&times;</span>
                <div class="product-details">
                    <div class="product-details-image">
                        <img src="assets/perjalanan/WEB - MIZZU All Products 3.png" alt="Beautiful Youth Detail">
                    </div>
                    <div class="product-details-info">
                        <h2 class="product-details-name">Beautiful Youth</h2>
                        <p class="product-details-description">Koleksi makeup Mizzu yang menampilkan warna-warna true color untuk mengekspresikan kecantikan alami dan kepribadianmu. Terbuat dari bahan-bahan berkualitas yang terinspirasi oleh alam.</p>
                        
                        <div class="features">
                            <div class="feature">
                                <span class="feature-icon">✓</span>
                                <span>Lipstik berbahan alami dengan warna tahan lama</span>
                            </div>
                            <div class="feature">
                                <span class="feature-icon">✓</span>
                                <span>Eye shadow palet dengan warna yang dapat dibangun</span>
                            </div>
                            <div class="feature">
                                <span class="feature-icon">✓</span>
                                <span>Mascara yang menebalkan dan memanjangkan bulu mata</span>
                            </div>
                            <div class="feature">
                                <span class="feature-icon">✓</span>
                                <span>Pensil alis dengan aplikator sikat untuk alis sempurna</span>
                            </div>
                        </div>
                        
                        <p class="price">Rp 40.000</p>
                        <a href="https://perfectbeauty.id/385_mizzu" class="btn pulsing" target="_blank">Beli Sekarang</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal" id="natur-beauty-modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal('natur-beauty')">&times;</span>
                <div class="product-details">
                    <div class="product-details-image">
                        <img src="assets/perjalanan/WEB - MIZZU All Products 4.png" alt="Natur Beauty Detail">
                    </div>
                    <div class="product-details-info">
                        <h2 class="product-details-name">Natur Beauty</h2>
                        <p class="product-details-description">Rangkaian produk premium Mizzu yang terbuat dari bahan-bahan alami pilihan untuk merawat kecantikanmu dari dalam. Dirancang oleh Chief Executive Operations untuk memenuhi standar kualitas tertinggi.</p>
                        
                        <div class="features">
                            <div class="feature">
                                <span class="feature-icon">✓</span>
                                <span>Foundation ringan dengan ekstrak tumbuhan</span>
                            </div>
                            <div class="feature">
                                <span class="feature-icon">✓</span>
                                <span>Lip tint organik dengan efek lembab tahan lama</span>
                            </div>
                            <div class="feature">
                                <span class="feature-icon">✓</span>
                                <span>Eye shadow palet dengan antioksidan</span>
                            </div>
                            <div class="feature">
                                <span class="feature-icon">✓</span>
                                <span>Setting powder dengan mineral alami</span>
                            </div>
                        </div>
                        
                        <p class="price">Rp 80.000</p>
                        <a href="https://perfectbeauty.id/385_mizzu" class="btn pulsing" target="_blank">Beli Sekarang</a>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
<script>
        // Generate plus signs for the pattern
        document.addEventListener('DOMContentLoaded', function() {
            const plusPattern = document.getElementById('plusPattern');
            for (let i = 0; i < 30; i++) {
                const plus = document.createElement('div');
                plus.className = 'plus';
                plus.textContent = '+';
                plus.style.top = `${Math.random() * 100}%`;
                plus.style.left = `${Math.random() * 100}%`;
                plus.style.opacity = Math.random() * 0.5 + 0.1;
                plusPattern.appendChild(plus);
            }
            
            // Add floating animation to product cards
            const productCards = document.querySelectorAll('.product-card');
            productCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.2}s`;
                card.classList.add('floating');
            });
        });
        
        // Modal functions
        function openModal(id) {
            document.getElementById(`${id}-modal`).classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeModal(id) {
            document.getElementById(`${id}-modal`).classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });
        });
        
        // Add scroll reveal effect
        document.addEventListener('scroll', function() {
            const productCards = document.querySelectorAll('.product-card');
            
            productCards.forEach(card => {
                const cardTop = card.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;
                
                if (cardTop < windowHeight - 100) {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }
            });
        });
        
        // Interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            const btns = document.querySelectorAll('.btn');
            
            btns.forEach(btn => {
                btn.addEventListener('mouseover', function() {
                    this.style.backgroundColor = '#0e6437';
                    this.style.color = 'white';
                });
                
                btn.addEventListener('mouseout', function() {
                    this.style.backgroundColor = 'white';
                    this.style.color = '#0e6437';
                });
            });
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const carousel = document.getElementById('timeline');
            const prevBtn = document.getElementById('tl-prev');
            const nextBtn = document.getElementById('tl-next');
            const items = Array.from(carousel.children);
            const perView = 2;
            const totalPages = Math.ceil(items.length / perView);
            let currentPage = 0;

            function goTo(page) {
                currentPage = Math.max(0, Math.min(page, totalPages - 1));
                const scrollX = carousel.clientWidth * currentPage;
                carousel.scrollTo({ left: scrollX, behavior: 'smooth' });
            }

            prevBtn.addEventListener('click', () => goTo(currentPage - 1));
            nextBtn.addEventListener('click', () => goTo(currentPage + 1));

            // Drag / Swipe
            let isDown = false, startX, scrollStart;
            carousel.addEventListener('mousedown', e => {
                isDown = true;
                carousel.classList.add('dragging');
                startX = e.pageX - carousel.offsetLeft;
                scrollStart = carousel.scrollLeft;
            });
            ['mouseup', 'mouseleave'].forEach(ev =>
                carousel.addEventListener(ev, () => {
                    if (!isDown) return;
                    isDown = false;
                    carousel.classList.remove('dragging');
                    const newPage = Math.round(carousel.scrollLeft / carousel.clientWidth);
                    goTo(newPage);
                })
            );
            carousel.addEventListener('mousemove', e => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - carousel.offsetLeft;
                carousel.scrollLeft = scrollStart - (x - startX);
            });

            // Inisialisasi
            goTo(0);
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.getElementById('timeline');
            const btnPrev  = document.getElementById('tl-prev');
            const btnNext  = document.getElementById('tl-next');

            // Fungsi untuk cek dan update visibilitas tombol
            function updateArrows() {
            const scrollLeft = carousel.scrollLeft;
            const maxScroll  = carousel.scrollWidth - carousel.clientWidth;

            // Jika scrollLeft > 0, tampilkan prev, else sembunyikan
            if (scrollLeft > 0) {
                btnPrev.style.display = 'block';
            } else {
                btnPrev.style.display = 'none';
            }

            // Jika scrollLeft < maxScroll, tampilkan next, else sembunyikan
            if (scrollLeft < maxScroll) {
                btnNext.style.display = 'block';
            } else {
                btnNext.style.display = 'none';
            }
            }

            // Scroll carousel saat tombol diklik
            const scrollAmount = 300; // sesuaikan jarak scroll per klik
            btnPrev.addEventListener('click', () => {
            carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });
            btnNext.addEventListener('click', () => {
            carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });

            // Update tombol ketika carousel di-scroll
            carousel.addEventListener('scroll', updateArrows);

            // Inisialisasi saat halaman load
            updateArrows();
        });
    </script>

@endsection