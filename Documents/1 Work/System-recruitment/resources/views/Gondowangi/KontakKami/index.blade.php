@extends('Gondowangi.Main.main')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            border-radius: 0 0 10px 10px;
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
            border-radius: 0 0 10px 10px;
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

        /* CSS (hanya untuk .contact-section) */
        .contact-section {
            padding: 4rem 0;
            background: radial-gradient(circle at center, rgba(200, 200, 255, 0.3), transparent);
        }

        .contact-section .container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .contact-section .card {
            background: #fff;
            border-radius: 1rem;
            padding: 2rem 1.5rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .contact-section .card .icon {
            font-size: 2rem;
            color: #80be34;
            margin-bottom: 1rem;
            display: inline-block;
            transition: transform 0.3s ease;
        }

        .contact-section .card h3 {
            margin-bottom: 0.5rem;
            font-size: 1.25rem;

        }

        .contact-section .card p {

            line-height: 1.6;
        }

        /* Hover Effects */
        .contact-section .card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            background: #1a6e2b;
            color: #fff;
        }

        .contact-section .card:hover .icon {
            transform: translateY(-5px) rotate(10deg);
            animation: bounce 0.6s ease;
        }

        /* Active card style */

        .contact-section .card.active .icon,
        .contact-section .card.active h3,
        .contact-section .card.active p {
            color: #e0f4d9;
        }

        /* Icon bounce animation */
        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0) rotate(10deg);
            }

            50% {
                transform: translateY(-10px) rotate(-10deg);
            }
        }

        /* HANYA UNTUK .contact-us */
        .contact-us {
            padding: 4rem 1rem;
            background: #f9fafb;
        }

        .contact-us .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            align-items: start;
        }

        /* ----- MAP WRAPPER ----- */
        .map-wrapper {
            height: 100%;
            width: 100%;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .map-wrapper:hover {
            transform: scale(1.03);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }

        .map-wrapper iframe {
            width: 100%;
            height: 100%;
            min-height: 420px;
            border: 0;
        }

        /* ----- FORM STYLING ----- */
        .contact-form h2 {
            font-size: 2rem;
            color: #0d1b2a;
            text-align: center;
            margin-bottom: 0.5rem;
            animation: fadeInUp 0.6s ease-out both;
        }

        .contact-form p {
            text-align: center;
            color: #4a4a4a;
            margin-bottom: 2rem;
            animation: fadeInUp 0.6s ease-out both;
            animation-delay: 0.1s;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 1.5rem;
            opacity: 0;
            animation: fadeInUp 0.6s ease-out both;
            animation-delay: var(--delay);
        }

        .form-group label {
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #0d1b2a;
        }

        .form-group label span {
            color: #e63946;
        }

        .form-group input,
        .form-group textarea {
            padding: 0.75rem 1rem;
            border: 2px solid #ccc;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #1a6e2b;
            box-shadow: 0 0 8px rgba(26, 110, 43, 0.4);
        }

        /* BUTTON */
        .contact-form button {
            display: block;
            margin: 1rem auto 0;
            padding: 0.75rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            background: #1a6e2b;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeInUp 0.6s ease-out both;
            animation-delay: 0.5s;
        }

        .contact-form button:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        /* KEYFRAMES */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .contact-us .container {
                grid-template-columns: 1fr;
            }

            .map-wrapper {
                margin-bottom: 2rem;
            }
        }

        .contact-us .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            align-items: stretch;
            /* ← PENTING: bikin semua anak ikut penuh tinggi */
        }

        /* style.css (atau di <style> head) */
        .site-wrapper {
            /* Background */
            background-image: url('assets/kontak/hr_sales\ -\ 4\ desember\ 2024\ -\ banner\ web\ homepage\ 9.png');
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
        .header-news {
          position: relative;
        }
        
        .header-news .overlay {
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background-color: #08301ac3;
          opacity: 0.76;
          z-index: 1; /* di bawah teks (teks z-index default  auto > 1) */
        }
        
        .header-news img {
          display: block;
          width: 100%;
          height: auto;
        }
        
        /* Pastikan teks berada di atas overlay */
        .header-news .position-absolute {
          z-index: 2;
        }
        .form-selection .nav-link {
            transition: all 0.3s ease;
            font-weight: 500;
            border: none;
        }
        
        .form-selection .nav-link:hover {
            transform: translateY(-2px);
        }
        
        .form-selection .nav-link.active {
            background-color: #0E6A39 !important;
            color: white !important;
            box-shadow: 0 4px 8px rgba(14, 106, 57, 0.3);
        }
        
        .form-selection .nav-link:not(.active) {
            background-color: #f8f9fa;
            color: #0E6A39;
            border: 2px solid #0E6A39;
        }
        
        .form-selection .nav-link:not(.active):hover {
            background-color: #e9ecef;
        }
        
        .contact-form button[type="submit"] {
            transition: all 0.3s ease;
        }
        
        .contact-form button[type="submit"]:hover {
            background-color: #0a5530 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(14, 106, 57, 0.3);
        }

        /* Map and Location Info Styling */
        .location-map {
            transition: opacity 0.3s ease;
            border-radius: 8px;
        }
        
        .location-map.active {
            display: block !important;
            opacity: 1;
        }
        
        .location-details {
            transition: opacity 0.3s ease;
        }
        
        .location-details.active {
            display: block !important;
            opacity: 1;
        }
        
        .location-info {
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

    </style>
@endsection

@section('hero')
    <section class="hero d-flex align-items-center position-relative">
        <div class="container text-white position-relative">

            <!-- Konten Hero -->
            <div class="row align-items-center" style="padding-top: 100px;">
                <div class="col-lg-12">
                    <h1 class="display-4 fw-bold mb-4 fade-in-left">Kirimkan feedback <br> anda, kami senang <br>
                        mendengarnya</h1>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <!-- Lokasi HO dan Factory -->
    <section id="awards" class="py-5 position-relative"
        style="background: url('assets/background-web/green-leaves-water.png') no-repeat center/cover;">
        <div class="overlay position-absolute top-0 start-0 w-100 h-100">
        </div>

        <div class="container" style="margin-top: 70px;">
            <div class="row g-4 align-items-stretch">
                <!-- Featured -->
                <div class="col-lg-6 zoom-in">
                    <div class="border-0 overflow-hidden" style="border-radius: 8px;">
                        <div class="position-relative header-news h-100">
                            <img src="assets/kontak/Gondowangi-Head-Office.jpg"
                                class="card-img-top" alt="Gondowangi's Head Office">
        
                            <!-- Overlay semi-transparent -->
                            <div class="overlay"></div>
        
                            <!-- Overlay teks di bagian bawah foto -->
                            <div class="position-absolute bottom-0 start-0 w-100 text-white p-4">
                                <h5 class="mt-1 mb-2">HEAD OFFICE</h5>
                                <p class="mb-0 w-50">
                                    Jl. Raya Pemuda Kav.713, Rawamangun<br>
                                    Pulogadung, Jakarta Timur – 13220 <br>
                                    Tel. (021) 4898016 <br>
                                    cs@gondowangi.com
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 zoom-in">
                    <div class="border-0 overflow-hidden " style="border-radius: 8px;">
                        <div class="position-relative header-news h-100">
                            <img src="assets/kontak/Gondowangi-Factoryy.png"
                                class="card-img-top" alt="Gondowangi's Factory">
                                <!-- Overlay semi-transparent -->
                            <div class="overlay"></div>
                            <!-- Overlay teks di bagian bawah foto -->
                            <div class="position-absolute bottom-0 start-0 w-100 text-white p-4">
                                <h5 class="mt-1 mb-2">FACTORY</h5>
                                <p class="mb-0 w-50">
                                    JL. Jababeka XVII D, Blok U No. 29C
                                    Cikarang, Bekasi – 17530<br>
                                    Tel. (021) 89107915-17
                                    factory@gondowangi.com
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Info -->
    <section class="contact-section">
        <div class="container">
            <div class="card">
                <div class="icon">
                    <i class="bx bx-map"></i>
                </div>
                <h3>Head Office</h3>
                <p>Jl. Raya Pemuda Kav.713, Jakarta Timur</p>
            </div>

            <div class="card">
                <div class="icon">
                    <i class="bx bx-time"></i>
                </div>
                <h3>Jam Kerja</h3>
                <p>Senin - Jumat : 09.00 - 17.00<br>Sabtu : 09.00 - 14.00</p>
            </div>

            <div class="card">
                <div class="icon">
                    <i class="bx bx-envelope"></i>
                </div>
                <h3>Email</h3>
                <p>cs@gondowangi.com<br>factory@gondowangi.com</p>
            </div>

            <div class="card">
                <div class="icon">
                    <i class="bx bx-phone"></i>
                </div>
                <h3>Telephone</h3>
                <p>1-555-123-4567<br>1-800-123-4567</p>
            </div>
        </div>
    </section>

    <!-- SECTION HUBUNGI KAMI -->
    <section class="contact-us">
        <div class="text-center mb-5">
            <h6 class="text-dark fade-in-up" style="font-size: 30px;"><strong>Hubungi Kami</strong></h6>
            <h2 class="fade-in-up" style="font-size: 17px; color: #0E6A39; transition-delay: 0.2s;">
                Sampaikan pertanyaan, komentar, ataupun keluhan mengenai produk <br> ataupun perusahaan pada kolom pesan
            </h2>
        </div>
        <div class="container-xxl">
            <div class="row g-4">
                <div class="col-lg-6 zoom-in">
                    <!-- Google Maps Embed - Dynamic based on selected tab -->
                    <div class="map-wrapper">
                        <!-- Head Office Map -->
                        <iframe id="hoMap" class="location-map"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.528007125604!2d106.89440981029574!3d-6.193843460654762!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f4c77a803985%3A0x2405a100ad134df0!2sPT%20Gondowangi%20Tradisional%20Kosmetika!5e0!3m2!1sid!2sid!4v1747236470488!5m2!1sid!2sid"
                            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                        
                        <!-- Factory Map -->
                        <iframe id="factoryMap" class="location-map"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.947506762842!2d107.15416881029623!3d-6.23639836242!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6981c84e9c7b85%3A0x123456789abcdef0!2sJl.%20Jababeka%20XVIID%20No.29-C%20Blok%20U%2C%20Gandasari%2C%20Cikarang%20Barat%2C%20Bekasi%20Regency%2C%20West%20Java%2017530!5e0!3m2!1sen!2sid!4v1747236470488!5m2!1sen!2sid"
                            width="600" height="450" style="border:0; display: none;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
                
                <div class="col-lg-6 zoom-in">
                    <!-- Form Selection Tabs -->
                    <div class="form-selection mb-4">
                        <div class="nav nav-pills justify-content-center" id="pills-tab" role="tablist">
                            <button class="nav-link  me-2" id="pills-ho-tab" data-bs-toggle="pill" 
                                data-bs-target="#pills-ho" type="button" role="tab" 
                                aria-controls="pills-ho" aria-selected="true"
                                style="background-color: #0E6A39; color: white; border-radius: 25px; padding: 10px 20px;">
                                <i class="bx bx-building me-2"></i>Head Office
                            </button>
                            <button class="nav-link" id="pills-factory-tab" data-bs-toggle="pill" 
                                data-bs-target="#pills-factory" type="button" role="tab" 
                                aria-controls="pills-factory" aria-selected="false"
                                style="background-color: #f8f9fa; color: #0E6A39; border: 2px solid #0E6A39; border-radius: 25px; padding: 10px 20px;">
                                <i class="bx bx-factory me-2"></i>Factory
                            </button>
                        </div>
                    </div>

                    <!-- Form Content -->
                    <div class="tab-content" id="pills-tabContent">
                        <!-- Head Office Form -->
                        <div class="tab-pane fade show active" id="pills-ho" role="tabpanel" 
                            aria-labelledby="pills-ho-tab" tabindex="0">
                            <div class="text-center mb-3">
                                <h5 style="color: #0E6A39;">
                                    <i class="bx bx-building me-2"></i>Kirim ke Head Office
                                </h5>
                                <small class="text-muted">cs@gondowangi.com</small>
                            </div>
                            <form class="contact-form" id="hoForm">
                                @csrf
                                <input type="hidden" name="destination" value="head_office">
                                
                                <div class="form-group" style="--delay: 0.1s">
                                    <label for="hoFullName">Nama Lengkap <span>*</span></label>
                                    <input id="hoFullName" name="fullName" type="text" placeholder="Masukan Nama Lengkap" required />
                                </div>
        
                                <div class="form-group" style="--delay: 0.2s">
                                    <label for="hoEmail">Alamat Email<span>*</span></label>
                                    <input id="hoEmail" name="email" type="email" placeholder="emailkamu@example.com" required />
                                </div>
        
                                <div class="form-group" style="--delay: 0.3s">
                                    <label for="hoSubject">Subjek</label>
                                    <input id="hoSubject" name="subject" type="text" placeholder="Subjek (optional)" />
                                </div>
        
                                <div class="form-group" style="--delay: 0.4s">
                                    <label for="hoMessage">Komentar atau Pesan <span>*</span></label>
                                    <textarea id="hoMessage" name="message" rows="6" placeholder="Tulis pesan untuk Head Office..." required></textarea>
                                </div>
        
                                <button type="submit" style="background-color: #0E6A39;">
                                    <i class="bx bx-send me-2"></i>Kirim ke Head Office
                                </button>
                            </form>
                        </div>

                        <!-- Factory Form -->
                        <div class="tab-pane fade" id="pills-factory" role="tabpanel" 
                            aria-labelledby="pills-factory-tab" tabindex="0">
                            <div class="text-center mb-3">
                                <h5 style="color: #0E6A39;">
                                    <i class="bx bx-factory me-2"></i>Kirim ke Factory
                                </h5>
                                <small class="text-muted">factory@gondowangi.com</small>
                            </div>
                            <form class="contact-form" id="factoryForm">
                                @csrf
                                <input type="hidden" name="destination" value="factory">
                                
                                <div class="form-group" style="--delay: 0.1s">
                                    <label for="factoryFullName">Nama Lengkap <span>*</span></label>
                                    <input id="factoryFullName" name="fullName" type="text" placeholder="Masukan Nama Lengkap" required />
                                </div>
        
                                <div class="form-group" style="--delay: 0.2s">
                                    <label for="factoryEmail">Alamat Email<span>*</span></label>
                                    <input id="factoryEmail" name="email" type="email" placeholder="emailkamu@example.com" required />
                                </div>
        
                                <div class="form-group" style="--delay: 0.3s">
                                    <label for="factorySubject">Subjek</label>
                                    <input id="factorySubject" name="subject" type="text" placeholder="Subjek (optional)" />
                                </div>
        
                                <div class="form-group" style="--delay: 0.4s">
                                    <label for="factoryMessage">Komentar atau Pesan <span>*</span></label>
                                    <textarea id="factoryMessage" name="message" rows="6" placeholder="Tulis pesan untuk Factory..." required></textarea>
                                </div>
        
                                <button type="submit" style="background-color: #0E6A39;">
                                    <i class="bx bx-send me-2"></i>Kirim ke Factory
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    
    <!-- JavaScript for Form Handling -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle Head Office Form
            const hoForm = document.getElementById('hoForm');
            const factoryForm = document.getElementById('factoryForm');
        
            if (hoForm) {
                hoForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleFormSubmit(this);
                });
            }
        
            if (factoryForm) {
                factoryForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    handleFormSubmit(this);
                });
            }
        
            // Handle tab switching and map display
            const pillsTab = document.querySelectorAll('[data-bs-toggle="pill"]');
            pillsTab.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(event) {
                    const target = event.target.getAttribute('data-bs-target');
                    const hoMap = document.getElementById('hoMap');
                    const factoryMap = document.getElementById('factoryMap');
                    
                    if (target === '#pills-ho') {
                        hoMap.style.display = 'block';
                        factoryMap.style.display = 'none';
                    } else if (target === '#pills-factory') {
                        hoMap.style.display = 'none';
                        factoryMap.style.display = 'block';
                    }
                });
            });
        
            function handleFormSubmit(form) {
                const submitButton = form.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                
                // Disable button dan show loading
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="bx bx-loader-alt bx-spin me-2"></i>Mengirim...';
                
                // Clear previous error messages
                clearErrorMessages(form);
                
                // Get form data
                const formData = new FormData(form);
                
                // Add CSRF token - try multiple methods
                let csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    formData.append('_token', csrfToken.getAttribute('content'));
                } else {
                    // Fallback: try to get from existing form token
                    const tokenInput = document.querySelector('input[name="_token"]');
                    if (tokenInput) {
                        formData.append('_token', tokenInput.value);
                    }
                }
                
                // Send data to server
                fetch('/kontakkami', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        showSuccessMessage(data.message);
                        // Reset form
                        form.reset();
                    } else {
                        // Show error messages
                        if (data.errors) {
                            showValidationErrors(form, data.errors);
                        } else {
                            showErrorMessage(data.message || 'Terjadi kesalahan saat mengirim pesan');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorMessage('Terjadi kesalahan jaringan. Silakan coba lagi.');
                })
                .finally(() => {
                    // Re-enable button
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                });
            }
        
            function showSuccessMessage(message) {
                // Create success alert
                const alert = document.createElement('div');
                alert.className = 'alert alert-success alert-dismissible fade show';
                alert.innerHTML = `
                    <i class="bx bx-check-circle me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                // Insert at top of container
                const container = document.querySelector('.container-xxl');
                container.insertBefore(alert, container.firstChild);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (alert && alert.parentNode) {
                        alert.remove();
                    }
                }, 5000);
            }
        
            function showErrorMessage(message) {
                // Create error alert
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger alert-dismissible fade show';
                alert.innerHTML = `
                    <i class="bx bx-error-circle me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                // Insert at top of container
                const container = document.querySelector('.container-xxl');
                container.insertBefore(alert, container.firstChild);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (alert && alert.parentNode) {
                        alert.remove();
                    }
                }, 5000);
            }
        
            function showValidationErrors(form, errors) {
                // Show each validation error below the respective field
                for (const [field, messages] of Object.entries(errors)) {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input) {
                        const formGroup = input.closest('.form-group');
                        if (formGroup) {
                            // Remove existing error message
                            const existingError = formGroup.querySelector('.error-message');
                            if (existingError) {
                                existingError.remove();
                            }
                            
                            // Add new error message
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'error-message text-danger mt-1';
                            errorDiv.innerHTML = `<small><i class="bx bx-error-circle me-1"></i>${messages[0]}</small>`;
                            formGroup.appendChild(errorDiv);
                            
                            // Add error styling to input
                            input.classList.add('is-invalid');
                        }
                    }
                }
                
                // Show general error message
                showErrorMessage('Mohon periksa kembali data yang Anda masukkan');
            }
        
            function clearErrorMessages(form) {
                // Remove all error messages
                const errorMessages = form.querySelectorAll('.error-message');
                errorMessages.forEach(error => error.remove());
                
                // Remove error styling from inputs
                const invalidInputs = form.querySelectorAll('.is-invalid');
                invalidInputs.forEach(input => input.classList.remove('is-invalid'));
            }
        });
    </script>
@endsection