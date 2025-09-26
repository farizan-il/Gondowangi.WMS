<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form Data Pelamar</title>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
     <style>
        /* CSS Enhanced Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            /*background: linear-gradient(135deg, #0E6A39 0%, #6EAA36 50%, #FFCE00 100%);  */
            background: #0f6a39;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        .ini-saya-btn {
            background: linear-gradient(135deg, #FFCE00, #FFA500);
            color: #000;
            padding: 8px 15px;
            font-size: 12px;
            border-radius: 15px;
            margin: 2px;
            position: relative;
            overflow: hidden;
        }

        .ini-saya-btn:hover {
            background: linear-gradient(135deg, #FFD700, #FFCE00);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 206, 0, 0.4);
        }

        .ini-saya-btn.active {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            font-weight: bold;
        }

        .ini-saya-btn.active::after {
            content: " ✓";
            font-weight: bold;
        }

        .ini-saya-btn:disabled {
            background: #cccccc;
            color: #666;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Highlight row ketika "Ini Saya" dipilih */
        .saudara-row.is-me {
            background: rgba(40, 167, 69, 0.1);
            border-left: 4px solid #28a745;
        }

        .remove-btn {
            background: linear-gradient(135deg, #dc3545, #c82333);
            padding: 8px 15px;
            font-size: 12px;
        }

        .remove-btn:hover {
            background: linear-gradient(135deg, #c82333, #bd2130);
        }

        @media (max-width: 768px) {
            table {
                font-size: 12px;
            }
            
            input, textarea, select {
                padding: 10px;
                font-size: 12px;
            }
            
            button {
                padding: 10px 20px;
                font-size: 12px;
            }
        }

        /* Animated Background Elements */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
            animation: floatingDots 20s linear infinite;
            pointer-events: none;
            z-index: -1;
        }

        @keyframes floatingDots {
            0% { transform: translateY(0px) translateX(0px); }
            25% { transform: translateY(-10px) translateX(5px); }
            50% { transform: translateY(0px) translateX(-5px); }
            75% { transform: translateY(10px) translateX(3px); }
            100% { transform: translateY(0px) translateX(0px); }
        }

        /* Form Container */
        section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            transform: translateY(20px);
            opacity: 0;
            animation: slideInUp 0.8s ease-out forwards;
            margin-bottom: 1rem;
        }

        @keyframes slideInUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Headers */
        h1 {
            text-align: center;
            color: #000000;
            font-size: 2.5rem;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #0E6A39, #6EAA36, #FFCE00);
            animation: expandLine 2s ease-out 0.5s forwards;
            transform: translateX(-50%);
        }

        @keyframes expandLine {
            to { width: 200px; }
        }

        h2 {
            color: #0E6A39;
            font-size: 1.8rem;
            margin: 30px 0 20px 0;
            padding: 15px;
            background: linear-gradient(135deg, rgba(14, 106, 57, 0.1), rgba(110, 170, 54, 0.1));
            border-radius: 10px;
            border-left: 5px solid #6EAA36;
            position: relative;
            transform: translateX(-20px);
            opacity: 0;
            transition: all 0.6s ease;
        }

        h2.animate {
            transform: translateX(0);
            opacity: 1;
        }

        h3 {
            color: #6EAA36;
            font-size: 1.3rem;
            margin: 20px 0 15px 0;
            position: relative;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transform: scale(0.95);
            opacity: 0;
            transition: all 0.6s ease;
        }

        table.animate {
            transform: scale(1);
            opacity: 1;
        }

        table td, table th {
            padding: 15px;
            border: 1px solid #e0e0e0;
            position: relative;
            transition: all 0.3s ease;
        }

        table td:hover {
            background: rgba(110, 170, 54, 0.05);
            transform: translateY(-1px);
        }

        table th {
            background: linear-gradient(135deg, #0E6A39);
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Input Fields */
        input, textarea, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
            position: relative;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #6EAA36;
            box-shadow: 0 0 0 3px rgba(110, 170, 54, 0.2);
            transform: translateY(-2px);
        }

        input:hover, textarea:hover, select:hover {
            border-color: #FFCE00;
            box-shadow: 0 2px 8px rgba(255, 206, 0, 0.3);
        }

        /* Radio and Checkbox */
        input[type="radio"], input[type="checkbox"] {
            width: auto;
            margin-right: 8px;
            transform: scale(1.2);
            accent-color: #6EAA36;
        }

        input[type="radio"]:checked, input[type="checkbox"]:checked {
            animation: checkPulse 0.3s ease;
        }

        @keyframes checkPulse {
            0% { transform: scale(1.2); }
            50% { transform: scale(1.5); }
            100% { transform: scale(1.2); }
        }

        /* Buttons */
        button {
            background: linear-gradient(135deg, #0E6A39, #6EAA36);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: all 0.6s ease;
        }

        button:hover::before {
            width: 300px;
            height: 300px;
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(14, 106, 57, 0.4);
        }

        button:active {
            transform: translateY(-1px);
        }

        /* Submit Button */
        button[type="submit"] {
            background: linear-gradient(135deg, #FFCE00, #6EAA36);
            color: #000000;
            font-size: 18px;
            padding: 15px 40px;
            margin: 30px auto;
            display: block;
            position: relative;
            animation: submitButtonGlow 2s ease-in-out infinite alternate;
        }

        @keyframes submitButtonGlow {
            0% { box-shadow: 0 8px 25px rgba(255, 206, 0, 0.4); }
            100% { box-shadow: 0 8px 35px rgba(255, 206, 0, 0.7); }
        }

        /* Success/Error Messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            position: relative;
            animation: alertSlide 0.5s ease-out;
        }

        @keyframes alertSlide {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Progress Bar */
        .progress-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
            z-index: 1000;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #0E6A39, #6EAA36, #FFCE00);
            width: 0%;
            transition: width 0.3s ease;
            position: relative;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 20px;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6));
            animation: shimmer 1.5s ease-in-out infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-20px); }
            100% { transform: translateX(20px); }
        }

        /* Floating Action Button */
        .fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #0E6A39, #6EAA36);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s ease;
            animation: fabPulse 2s ease-in-out infinite;
        }

        @keyframes fabPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .fab:hover {
            transform: scale(1.2);
            box-shadow: 0 8px 25px rgba(14, 106, 57, 0.5);
        }

        /* Tooltip */
        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .tooltip:hover::after {
            opacity: 1;
            visibility: visible;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            form {
                padding: 20px;
                margin: 10px;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            table {
                font-size: 12px;
            }
            
            input, textarea, select {
                padding: 10px;
                font-size: 12px;
            }
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #0E6A39, #6EAA36);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #6EAA36, #FFCE00);
        }
        
        /* Container layout utama, sesuaikan jika sudah ada */
        .form-container {
            
            grid-template-columns: 1fr 300px;
            gap: 2rem;
           
            margin: 0 auto;
            align-items: start;
            transition: grid-template-columns 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* State ketika navigasi di-minimize */
        .form-container.nav-minimized {
            grid-template-columns: 1fr 60px;
        }
        
        /* Sticky Navigation Styling */
       .sticky-nav-container {
            position: sticky;
            top: 2rem;
            height: fit-content;
            max-height: calc(100vh - 4rem);
            overflow: hidden;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(14, 106, 57, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* State minimized untuk sticky nav */
        .sticky-nav-container.minimized {
            width: 60px;
            padding: 10px;
            border-radius: 30px;
        }
        
        .toggle-nav-btn {
            position: absolute;
            top: 10px;
            right: 20px;
            background: linear-gradient(135deg, #0E6A39, #6EAA36);
            color: white;
            border: none;
            /*border-radius: 50%;*/
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .toggle-nav-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(14, 106, 57, 0.4);
        }

        /* State minimized untuk toggle button */
        .sticky-nav-container.minimized .toggle-nav-btn {
            position: static;
            width: 10px;
            height: 50px;
            margin: 0 -5px;
            display: block;
        }

        /* Form Navigation dengan transisi */
        .form-navigation {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 1;
            visibility: visible;
        }

        /* State minimized untuk form navigation */
        .sticky-nav-container.minimized .form-navigation {
            opacity: 0;
            visibility: hidden;
            transform: translateX(-20px);
        }

        /* Tambahan CSS untuk animasi yang smooth */
        .nav-sections {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Form content area dengan transisi */
        .form-content {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        /* Overall Progress Bar */
        .overall-progress {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid rgba(14, 106, 57, 0.1);
        }
        
        .progress-label {
            font-weight: bold;
            color: #0E6A39;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .progress-bar-container {
            background: #f0f0f0;
            border-radius: 25px;
            overflow: hidden;
            position: relative;
            height: 25px;
        }
        
        .progress-bar {
            background: linear-gradient(135deg, #0E6A39, #6EAA36, #FFCE00);
            height: 100%;
            width: 0%;
            border-radius: 25px;
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .progress-text {
            color: white;
            font-weight: bold;
            font-size: 12px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }
        
        /* Navigation Menu */
        .form-nav-menu {
            margin-top: 15px;
        }
        
        .nav-sections {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .nav-item {
            margin-bottom: 8px;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .nav-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(14, 106, 57, 0.2);
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            text-decoration: none;
            color: #333;
            background: white;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
            border-radius: 10px;
        }
        
        .nav-link:hover {
            background: linear-gradient(135deg, rgba(14, 106, 57, 0.05), rgba(110, 170, 54, 0.05));
            border-color: #6EAA36;
            color: #0E6A39;
        }
        
        .nav-link.active {
            background: linear-gradient(135deg, #0E6A39, #6EAA36);
            color: white;
            border-color: #0E6A39;
        }
        
        .section-number {
            background: rgba(14, 106, 57, 0.1);
            color: #0E6A39;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        
        .nav-link.active .section-number {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        .section-name {
            flex: 1;
            font-size: 13px;
            font-weight: 500;
        }
        
        .section-progress {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 3px;
        }
        
        .mini-progress-bar {
            width: 50px;
            height: 4px;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 2px;
            overflow: hidden;
        }
        
        .mini-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #0E6A39, #6EAA36);
            width: 0%;
            transition: width 0.4s ease;
            border-radius: 2px;
        }
        
        .progress-percentage {
            font-size: 10px;
            font-weight: bold;
            color: #666;
        }
        
        .nav-link.active .progress-percentage {
            color: white;
        }
        @media (max-width: 768px) {
            .form-container {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .sticky-nav-container {
                position: fixed;
                bottom: 20px;
                right: 20px;
                width: 60px;
                height: 60px;
                border-radius: 50%;
                padding: 0;
                overflow: hidden;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                z-index: 1000;
            }
            
            .sticky-nav-container.expanded {
                width: 280px;
                height: auto;
                border-radius: 15px;
                padding: 15px;
                bottom: 20px;
                right: 20px;
            }
            
            .mobile-toggle {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: linear-gradient(135deg, #0E6A39, #6EAA36);
                color: white;
                border: none;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .form-navigation {
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }
            
            .sticky-nav-container.expanded .form-navigation {
                opacity: 1;
                visibility: visible;
            }
            
            .sticky-nav-container.expanded .mobile-toggle {
                display: none;
            }
        }
        /* Tambahkan asterisk merah setelah label required */
        .required-label::after {
          content: " *";
          color: red;
          font-weight: bold;
        }
        
        /* Atau jika menggunakan pseudo-class :has (browser modern) */
        td:has(+ td input[required])::after {
          content: " *";
          color: red;
          font-weight: bold;
        }

        @media (max-width: 768px) {
            /* Form Container: Mengubah grid menjadi satu kolom pada tampilan mobile */
            .form-container {
                grid-template-columns: 1fr; /* Menjadi satu kolom */
                gap: 1rem; /* Memberi jarak antar elemen */
            }
        
            /* Table: Menyesuaikan ukuran font agar mudah dibaca pada mobile */
            table {
                font-size: 12px;
            }
        
            /* Ukuran Input Fields lebih kecil di mobile */
            input, textarea, select {
                padding: 10px;
                font-size: 12px;
            }
        
            /* Menyesuaikan ukuran font untuk judul */
            h1 {
                font-size: 2rem; /* Mengurangi ukuran judul */
            }
        
            /* Mengurangi ukuran pada button */
            button {
                padding: 10px 20px;
                font-size: 12px;
            }
        
            /* Menambahkan Scrollable pada Table besar */
            table {
                overflow-x: auto;
                display: block;
                white-space: nowrap;
            }
        
            /* Menyesuaikan Sticky Navigation */
            .sticky-nav-container {
                position: fixed;
                bottom: 20px;
                right: 20px;
                width: 60px;
                height: 60px;
                border-radius: 50%;
                padding: 0;
                overflow: hidden;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                z-index: 1000;
            }
        
            /* Menyembunyikan tombol navigasi untuk tampilan mobile dan hanya tampilkan ketika tombol di-klik */
            .sticky-nav-container.expanded {
                width: 280px;
                height: auto;
                border-radius: 15px;
                padding: 15px;
                bottom: 20px;
                right: 20px;
            }
        
            .mobile-toggle {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: linear-gradient(135deg, #0E6A39, #6EAA36);
                color: white;
                border: none;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
            }
        
            /* Navigasi sticky toggle di mobile */
            .form-navigation {
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }
        
            /* Membuka form navigation ketika tombol mobile toggle diklik */
            .sticky-nav-container.expanded .form-navigation {
                opacity: 1;
                visibility: visible;
            }
        
            .sticky-nav-container.expanded .mobile-toggle {
                display: none;
            }
            
            /* Tambahan Spasi untuk elemen dalam form */
            input[type="text"], input[type="email"], input[type="number"], textarea, select {
                font-size: 14px;
            }
        
            /* Mengatur ulang tampilan gambar preview */
            #photo-preview {
                max-width: 100px;
                max-height: 150px;
            }
        }
        
        /* Menjaga Tampilan Desktop Seperti Semula */
        @media (min-width: 769px) {
            /* Membuat container untuk desktop lebih lebar */
            .form-container {
                max-width: 1200px;
            }
        
            /* Tombol dan ukuran font tetap seperti sebelumnya pada desktop */
            button {
                padding: 12px 25px;
                font-size: 14px;
            }
        
            h1 {
                font-size: 2.5rem;
            }
        
            table {
                font-size: 14px;
            }
        }
        /* CSS untuk Struktur Organisasi */
        .org-structure-container {
            margin-top: 20px;
            padding: 20px;
            border: 2px dashed #0E6A39;
            border-radius: 15px;
            background: rgba(14, 106, 57, 0.02);
            min-height: 400px;
            position: relative;
        }

        .org-elements-panel {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            flex-wrap: wrap;
        }

        .org-element {
            padding: 10px 15px;
            background: linear-gradient(135deg, #0E6A39, #6EAA36);
            color: white;
            border-radius: 8px;
            cursor: grab;
            user-select: none;
            transition: all 0.3s ease;
            font-size: 12px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .org-element:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(14, 106, 57, 0.4);
        }

        .org-element:active {
            cursor: grabbing;
            transform: scale(0.95);
        }

        .org-canvas {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            min-height: 400px;
            position: relative;
            overflow: hidden;
            background-image: radial-gradient(circle, #f0f0f0 1px, transparent 1px);
            background-size: 20px 20px;
            transform-origin: 0 0;
            transition: transform 0.3s ease;
        }

        .canvas-container {
            overflow: auto;
            max-height: 600px;
            border-radius: 10px;
        }

        .org-node {
            position: absolute;
            padding: 8px 12px;
            background: white;
            border: 2px solid #0E6A39;
            border-radius: 8px;
            cursor: move;
            user-select: none;
            font-size: 11px;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            z-index: 10;
        }

        .org-node:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(14, 106, 57, 0.3);
        }

        .org-node.selected {
            border-color: #FFCE00;
            background: rgba(255, 206, 0, 0.1);
        }

        .org-node.ceo {
            background: linear-gradient(135deg, #0E6A39, #6EAA36);
            color: white;
            border-color: #0E6A39;
        }

        .org-node.manager {
            background: linear-gradient(135deg, #6EAA36, #FFCE00);
            color: #000;
            border-color: #6EAA36;
        }

        .org-node.staff {
            background: rgba(255, 206, 0, 0.2);
            border-color: #FFCE00;
        }

        .connection-line {
            position: absolute;
            background: #0E6A39;
            height: 2px;
            z-index: 1;
            pointer-events: none;
        }

        .connection-line.vertical {
            width: 2px;
            height: auto;
        }

        .org-controls {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .zoom-controls {
            display: flex;
            gap: 5px;
            align-items: center;
            margin-left: auto;
        }

        .zoom-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
        }

        .control-btn {
            padding: 8px 15px;
            background: linear-gradient(135deg, #0E6A39, #6EAA36);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .control-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(14, 106, 57, 0.4);
        }

        .control-btn.danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        .node-info-panel {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ffffff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 60, 60, 0.9);
            width: 200px;
            display: none;
        }

        .node-info-panel.active {
            display: block;
        }

        .node-info-panel input {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 12px;
        }

        .org-json-output {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }

        .org-json-output textarea {
            width: 100%;
            height: 100px;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            resize: vertical;
        }

        @media (max-width: 768px) {
            .org-elements-panel {
                flex-direction: column;
            }
            
            .org-element {
                text-align: center;
            }
            
            .node-info-panel {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                z-index: 1000;
                width: 90%;
                max-width: 300px;
            }
        }
        
        /* Org Chart Styles */
        .org-chart {
            text-align: center;
            padding: 20px;
        }

        .org-node {
            position: relative;
            display: inline-block;
            margin: 10px;
            vertical-align: top;
        }

        .org-box {
            background: white;
            border: 2px solid #3498db;
            border-radius: 8px;
            padding: 12px;
            min-width: 160px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .org-box.current-user {
            background: #e8f5e8;
            border-color: #27ae60;
            box-shadow: 0 0 10px rgba(39, 174, 96, 0.3);
        }

        .org-box.current-user::before {
            content: "👤";
            position: absolute;
            top: -10px;
            right: -10px;
            background: #27ae60;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .org-name {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .org-position {
            color: #7f8c8d;
            font-size: 12px;
        }

        .org-children {
            margin-top: 30px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .org-line {
            position: absolute;
            background: #3498db;
        }

        .org-line.vertical {
            width: 2px;
            height: 20px;
            left: 50%;
            top: 100%;
            transform: translateX(-50%);
        }

        .org-line.horizontal {
            height: 2px;
            top: 20px;
            left: 0;
            right: 0;
        }

        .empty-state {
            text-align: center;
            color: #7f8c8d;
            font-style: italic;
            padding: 40px;
        }

        .employee-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .employee-info {
            flex: 1;
        }

        .employee-name {
            font-weight: bold;
            color: #2c3e50;
        }

        .employee-position {
            color: #6c757d;
            font-size: 12px;
        }

        .form-actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        
        .suggestion-item:hover,
        .suggestion-item.active {
            background-color: #f8f9fa;
        }
        
        .suggestion-item {
            transition: background-color 0.15s ease-in-out;
        }

    </style>
</head>
<body>
    <h1 style="color: white;">FORM DATA PELAMAR</h1>
    
    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <!-- Wrap existing form in container -->
    <div class="form-container" id="formContainer">
        <div class="form-content">
            <div class="alert alert-warning d-flex align-items-center" role="alert" style="background-color: #fff3cd; border-left: 6px solid #ffc107; color: #856404;">
              <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.964 0L.165 13.233c-.457.778.091 1.767.982 1.767h13.707c.89 0 1.438-.99.982-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
              </svg>
              <div>
                Mohon aktifkan lokasi Anda untuk melanjutkan pendaftaran!
              </div>
            </div>

            <form action="{{ route('kandidat.daftar.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <!-- I. DATA PRIBADI -->
                <section id="dataPribadi">
                    <h2>I. DATA PRIBADI</h2>
                    <table border="1">
                        <tr>
                            <td>1. Posisi yang Dilamar</td>
                            <td>
                                <input type="hidden" name="asal_daerah" id="asal_daerah" class="form-control" readonly>
                                <select name="posisi_dilamar_id" class="form-control" required>
                                    <option value="">-- Pilih Posisi --</option>
                                    @foreach($availablePositions as $posisi)
                                        <option value="{{ $posisi->id }}">{{ $posisi->position_title }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td rowspan="12" style="text-align: center; vertical-align: top; padding: 10px; width: 150px;">
                                <!--UPLOAD PAS FOTO TERBARU-->
                                <div style="border: 2px dashed #ccc; padding: 20px; min-height: 200px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                    <div id="photo-preview" style="display: none;">
                                        <img id="preview-image" src="" alt="Preview Foto" style="max-width: 120px; max-height: 160px; object-fit: cover; border: 1px solid #ddd;">
                                    </div>
                                    <button type="button" id="remove-photo" style="margin-top: 5px; background: #dc3545; color: white; border: none; padding: 2px 8px; font-size: 12px; cursor: pointer; display: none;">Hapus</button>
                                    <div id="photo-placeholder">
                                        <p style="margin: 0; font-size: 14px; color: #666;">Pas Foto 3x4</p>
                                        <label for="foto" style="display: inline-block; background: #007bff; color: white; padding: 8px 16px; margin-top: 10px; cursor: pointer; border-radius: 4px; font-size: 12px;">
                                            Pilih Foto
                                        </label>
                                    </div>
                                    <input type="file" id="foto" name="foto" accept="image/*" style="display: none;">
                                    <div id="foto-alert" style="color: red; font-size: 12px; margin-top: 5px; display: none;"></div>
                                </div>
                                <div style="color: red; font-size: 12px; margin-top: 5px; display: none;" id="foto-error">
                                    <!-- Error message for foto will be displayed here -->
                                </div>
                                
                                <!--UPLOAD CV TERBARU-->
                                <div style="border: 2px dashed #ccc; padding: 20px; min-height: 200px; display: flex; flex-direction: column; align-items: center; justify-content: center; margin-top: 8px;">
                                    <div id="cv-preview" style="display: none;">
                                        <div id="cv-file-info" style="max-width: 120px; min-height: 80px; display: flex; flex-direction: column; align-items: center; justify-content: center; border: 1px solid #ddd; padding: 10px; background: #f8f9fa;">
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <polyline points="14,2 14,8 20,8" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <line x1="16" y1="13" x2="8" y2="13" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <line x1="16" y1="17" x2="8" y2="17" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <polyline points="10,9 9,9 8,9" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <p id="cv-filename" style="margin: 5px 0 0 0; font-size: 10px; color: #666; text-align: center; word-break: break-all;"></p>
                                        </div>
                                    </div>
                                    <button type="button" id="remove-cv" style="margin-top: 5px; background: #dc3545; color: white; border: none; padding: 2px 8px; font-size: 12px; cursor: pointer; display: none;">Hapus</button>
                                    <div id="cv-placeholder">
                                        <p style="margin: 0; font-size: 14px; color: #666;">CV Terbaru</p>
                                        <label for="cv" style="display: inline-block; background: #007bff; color: white; padding: 8px 16px; margin-top: 10px; cursor: pointer; border-radius: 4px; font-size: 12px;">
                                            Pilih CV
                                        </label>
                                    </div>
                                    <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx" style="display: none;">
                                    <div id="cv-alert" style="color: red; font-size: 12px; margin-top: 5px; display: none;"></div>
                                </div>
                                <div style="color: red; font-size: 12px; margin-top: 5px; display: none;" id="cv-error">
                                    <!-- Error message for CV will be displayed here -->
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>2. Nama Lengkap</td>
                            <td><input type="text" name="nama" value="{{ old('nama', $prefill->nama ?? '') }}" placeholder="Nama Lengkap" required></td>
                        </tr>
                        <tr>
                            <td>3. Tanggal Lahir</td>
                            <td><input type="date" name="tanggal_lahir" placeholder="tanggal lahir" value=" {{ old('tanggal_lahir', $prefill->tanggal_lahir ?? '') }}" required></td>
                        </tr>
                        <tr>
                            <td>4. Kota Domisili</td>
                            <td class="position-relative">
                                <input type="text" 
                                       name="kota_domisili" 
                                       id="kota_domisili" 
                                       class="form-control" 
                                       value="{{ old('kota_domisili', $prefill->kota_domisili ?? '') }}" 
                                       placeholder="Kota Domisili"
                                       autocomplete="on"
                                       required>
                                <div id="city-suggestions" class="position-absolute w-100 bg-white border border-top-0 rounded-bottom shadow-sm" style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;"></div>
                            </td>
                            
                            
                        </tr>
                        <tr>
                            <td>5. No Telepon/Handphone</td>
                            <td><input type="text" name="no_telepon" placeholder="No Telpon/Handphone" value="{{ old('no_telepon', $prefill->no_telepon ?? '') }}" required></td>
                        </tr>
                        <tr>
                            <td>6. Email</td>
                            <td><input type="email" name="email" value=" {{ old('email', $prefill->email ?? '') }}" placeholder="Email anda" required></td>
                        </tr>
                    </table>
                </section>
        
                <!-- III. PENDIDIKAN -->
                <section id="pendidikan">
                    <h2>II. PENDIDIKAN</h2>
                    
                    <h3>Pendidikan Formal</h3>
                    <div id="pendidikan-container">
                        <table border="1" id="pendidikan-table">
                            <tr>
                                <th>Jenjang</th>
                                <th>Nama Sekolah/Universitas/Jurusan</th>
                                <th>Masuk</th>
                                <th>Keluar</th>
                                <th>NEM/IPK</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                            @php $i = 0; @endphp
                            @foreach($pendidikanFormal as $pendidikan)
                            <tr class="pendidikan-row">
                                <td>
                                    <select name="pendidikan_formal[{{ $i }}][jenjang]">
                                        <option value="">Pilih Jenjang</option>
                                        <option value="SD" {{ $pendidikan['jenjang'] == 'SD' ? 'selected' : '' }}>SD</option>
                                        <option value="SMP" {{ $pendidikan['jenjang'] == 'SMP' ? 'selected' : '' }}>SMP</option>
                                        <option value="SMU/SMK" {{ $pendidikan['jenjang'] == 'SMU/SMK' ? 'selected' : '' }}>SMU/SMK</option>
                                        <option value="Akademi/Diploma" {{ $pendidikan['jenjang'] == 'Akademi/Diploma' ? 'selected' : '' }}>Akademi/Diploma</option>
                                        <option value="S1" {{ $pendidikan['jenjang'] == 'S1' ? 'selected' : '' }}>S1</option>
                                        <option value="S2" {{ $pendidikan['jenjang'] == 'S2' ? 'selected' : '' }}>S2</option>
                                    </select>
                                </td>
                                <td><input type="text" name="pendidikan_formal[{{ $i }}][nama_sekolah]" value="{{ $pendidikan['nama_sekolah'] }}"></td>
                                <td><input type="number" name="pendidikan_formal[{{ $i }}][tahun_masuk]" value="{{ $pendidikan['tahun_masuk'] }}"></td>
                                <td><input type="number" name="pendidikan_formal[{{ $i }}][tahun_keluar]" value="{{ $pendidikan['tahun_keluar'] }}"></td>
                                <td><input type="text" name="pendidikan_formal[{{ $i }}][nilai]" value="{{ $pendidikan['nilai'] }}"></td>
                                <td></td>
                                <td><button type="button" onclick="removePendidikan(this)">Hapus</button></td>
                            </tr>
                            @php $i++; @endphp
                            @endforeach
                        </table>
                        <button type="button" onclick="addPendidikan()">Tambah Pendidikan</button>
                    </div>
                </section>
        
        
                <!-- V. PENGALAMAN KERJA -->
                <section id="pengalamanKerja">
                    <h2>III. PENGALAMAN KERJA (DIMULAI DARI PEKERJAAN TERAKHIR)</h2>
                    <div id="pengalaman-container">
                        @php $j = 0; @endphp
                        @foreach($pengalamanKerja as $pengalaman)
                        <div class="pengalaman-item">
                            <table border="1">
                                <tr>
                                    <td>Nama Perusahaan</td>
                                    <td><input type="text" name="pengalaman_kerja[{{ $j }}][nama_perusahaan]" value="{{ $pengalaman['nama_perusahaan'] }}"></td>
                                    <td>Jabatan</td>
                                    <td><input type="text" name="pengalaman_kerja[{{ $j }}][jabatan]" value="{{ $pengalaman['jabatan'] }}"></td>
                                </tr>
                                <tr>
                                    <td>Masa Kerja (Dari)</td>
                                    <td><input type="date" name="pengalaman_kerja[{{ $j }}][masa_kerja_dari]" value="{{ $pengalaman['masa_kerja_dari'] }}"></td>
                                    <td>Masa Kerja (Sampai)</td>
                                    <td><input type="date" name="pengalaman_kerja[{{ $j }}][masa_kerja_sampai]" value="{{ $pengalaman['masa_kerja_sampai'] ?? '' }}"></td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td colspan="2">
                                        <label>
                                            <input type="checkbox" name="pengalaman_kerja[{{ $j }}][masih_bekerja]" value="1" {{ !empty($pengalaman['masih_bekerja']) ? 'checked' : '' }}>
                                            Saat Ini Masih Bekerja Disini
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Uraian Pekerjaan</td>
                                    <td colspan="3">
                                        <textarea name="pengalaman_kerja[{{ $j }}][uraian_pekerjaan]">{{ $pengalaman['uraian_pekerjaan'] }}</textarea>
                                    </td>
                                </tr>
                                <tr class="alasan-berhenti-row">
                                    <td>Alasan Berhenti</td>
                                    <td colspan="3">
                                        <textarea name="pengalaman_kerja[{{ $j }}][alasan_berhenti]">{{ $pengalaman['alasan_berhenti'] }}</textarea>
                                    </td>
                                </tr>
                            </table>
                            <button type="button" onclick="removePengalaman(this)">Hapus Pengalaman</button>
                        </div>
                        @php $j++; @endphp
                        @endforeach
                    </div>
                    <button type="button" onclick="addPengalaman()">Tambah Pengalaman Kerja</button>
                </section>
              
                <section  id="informasiPekerjaan" >
                    <h2>IV. INFORMASI PEKERJAAN <br> (Bagian ini bersifat opsional dan dapat diabaikan apabila tidak relevan)</h2>

                    <h3>a. Yang diterima dari perusahaan terakhir</h3>
                    <table border="1">
                        <tr>
                            <td>1. Gaji (take home pay) yang diterima <span style="color: red;"></span></td>
                            <td><input type="number" name="gaji_terakhir" step="0.01" placeholder="Misal: Rp5.000.000" value="{{ old('gaji_terakhir') }}"></td>
                        </tr>
                        <tr>
                            <td>2. Tunjangan terakhir yang diterima <span style="color: red;"></span></td>
                            <td><textarea name="tunjangan_terakhir" placeholder="Tunjangan Terakhir Anda">{{ old('tunjangan_terakhir') }}</textarea></td>
                        </tr>
                        <tr>
                            <td>3. Fasilitas terakhir yang diterima <span style="color: red;"></span></td>
                            <td><textarea name="fasilitas_terakhir" placeholder="Fasilitas Terakhir Anda">{{ old('fasilitas_terakhir') }}</textarea></td>
                        </tr>
                        <tr>
                            <td>4. Lain-lain (kendaraan dinas) <span style="color: red;"></span></td>
                            <td><textarea name="fasilitas_lain" placeholder="Lain-lainnya">{{ old('fasilitas_lain') }}</textarea></td>
                        </tr>
                    </table>
            
                    <h3>b. Hal-hal yang berhubungan dengan lamaran</h3>
                    <table border="1">
                        <tr>
                            <td>1. Jabatan yang diminati <span style="color: red;"></span></td>
                            <td><input type="text" name="jabatan_diminati" value="{{ old('jabatan_diminati') }}" placeholder="Jabatan yang diminati"></td>
                        </tr>
                        <tr>
                            <td>2. Besar Gaji yang diharapkan (dalam Rp) <span style="color: red;"></span></td>
                            <td><input type="number" name="gaji_diharapkan" step="0.01" value="{{ old('gaji_diharapkan') }}" placeholder="Gaji yang diharapkan" ></td>
                        </tr>
                        <tr>
                            <td>3. Tunjangan yang diharapkan <span style="color: red;"></span></td>
                            <td><textarea name="tunjangan_diharapkan" placeholder="Tunjangan yang diharapkan">{{ old('tunjangan_diharapkan') }}</textarea></td>
                        </tr>
                        <tr>
                            <td>4. Fasilitas yang diharapkan <span style="color: red;"></span></td>
                            <td><textarea name="fasilitas_diharapkan" placeholder="Fasilitas yang diharapkan">{{ old('fasilitas_diharapkan') }}</textarea></td>
                        </tr>
                        <tr>
                            <td>5. Jaminan yang diharapkan <span style="color: red;"></span></td>
                            <td><textarea name="jaminan_diharapkan" placeholder="Jaminan yang diharapkan">{{ old('jaminan_diharapkan') }}</textarea></td>
                        </tr>
                        <tr>
                            <td>6. Lain-lain yang diharapkan <span style="color: red;"></span></td>
                            <td><textarea name="lain_diharapkan" placeholder="Lain-lain yang diharapkan">{{ old('lain_diharapkan') }}</textarea></td>
                        </tr>
                    </table>
            
                </section>
        
                <!-- IX. INFORMASI/CATATAN LAIN -->
                <section >
                    <h2>V. INFORMASI/CATATAN LAIN YANG INGIN DIKEMUKAKAN</h2>
                    <textarea name="informasi_tambahan" rows="5" cols="80">{{ old('informasi_tambahan') }} </textarea>
            
                    <br><br>
                    <p>Demikian data ini saya isi dengan sejujurnya sesuai dengan keadaan yang sebenarnya. Apabila 
                    dikemudian hari ada data yang tidak sesuai atau tidak benar, maka saya bersedia dikenakan sanksi
                    sesuai dengan peraturan perusahaan atau dilaporkan kepada pihak berwajib.</p>
            
                    <p>"Untuk mengetahui informasi seputar Gondowangi, silakan akses melalui link berikut :"</p>
                    <p><a href="https://linktr.ee/PT.Gondowangi" target="_blank">https://linktr.ee/PT.Gondowangi</a></p>
            
                    <br>
                    <button type="submit">Simpan Data</button>
                </section>
            </form>
        </div>
    </div>
    
    <script>
        function toCamelCase(str) {
            return str
                .toLowerCase()
                .replace(/[^a-zA-Z0-9 ]/g, '') // hapus karakter khusus jika perlu
                .replace(/\s+(.)/g, function(match, group1) {
                    return group1.toUpperCase();
                })
                .replace(/^./, function(match) {
                    return match.toUpperCase();
                })
                .replace(/\s/g, ''); // hilangkan semua spasi
        }
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const koordinatKota = {
                // DKI Jakarta
                'Jakarta': {lat: -6.2088, lng: 106.8456},
                'Jakarta Pusat': {lat: -6.1745, lng: 106.8227},
                'Jakarta Utara': {lat: -6.1384, lng: 106.8631},
                'Jakarta Barat': {lat: -6.1666, lng: 106.7593},
                'Jakarta Selatan': {lat: -6.2615, lng: 106.8106},
                'Jakarta Timur': {lat: -6.2250, lng: 106.9004},
                'Kepulauan Seribu': {lat: -5.6667, lng: 106.5833},

                // Jawa Barat
                'Bandung': {lat: -6.9175, lng: 107.6191},
                'Bekasi': {lat: -6.2383, lng: 106.9756},
                'Bogor': {lat: -6.5950, lng: 106.7966},
                'Cimahi': {lat: -6.8667, lng: 107.5333},
                'Cirebon': {lat: -6.7063, lng: 108.5571},
                'Depok': {lat: -6.4025, lng: 106.7942},
                'Sukabumi': {lat: -6.9178, lng: 106.9269},
                'Tasikmalaya': {lat: -7.3506, lng: 108.2111},
                'Banjar': {lat: -7.3667, lng: 108.5333},
                'Kabupaten Bandung': {lat: -7.0167, lng: 107.5000},
                'Kabupaten Bandung Barat': {lat: -6.8667, lng: 107.4833},
                'Kabupaten Bekasi': {lat: -6.2667, lng: 107.1333},
                'Kabupaten Bogor': {lat: -6.6000, lng: 106.8000},
                'Kabupaten Ciamis': {lat: -7.3333, lng: 108.3500},
                'Kabupaten Cianjur': {lat: -6.8167, lng: 107.1333},
                'Kabupaten Cirebon': {lat: -6.7333, lng: 108.4167},
                'Kabupaten Garut': {lat: -7.2167, lng: 107.9000},
                'Kabupaten Indramayu': {lat: -6.3333, lng: 108.3333},
                'Kabupaten Karawang': {lat: -6.3000, lng: 107.3000},
                'Kabupaten Kuningan': {lat: -6.9833, lng: 108.4833},
                'Kabupaten Majalengka': {lat: -6.8333, lng: 108.2333},
                'Kabupaten Pangandaran': {lat: -7.6833, lng: 108.6500},
                'Kabupaten Purwakarta': {lat: -6.5667, lng: 107.4333},
                'Kabupaten Subang': {lat: -6.5667, lng: 107.7500},
                'Kabupaten Sukabumi': {lat: -6.9333, lng: 106.9333},
                'Kabupaten Sumedang': {lat: -6.8667, lng: 107.9167},
                'Kabupaten Tasikmalaya': {lat: -7.3333, lng: 108.2167},

                // Jawa Tengah
                'Semarang': {lat: -6.9667, lng: 110.4167},
                'Magelang': {lat: -7.4667, lng: 110.2167},
                'Pekalongan': {lat: -6.8833, lng: 109.6667},
                'Salatiga': {lat: -7.3333, lng: 110.5000},
                'Surakarta': {lat: -7.5667, lng: 110.8333},
                'Tegal': {lat: -6.8667, lng: 109.1333},
                'Kabupaten Banjarnegara': {lat: -7.3167, lng: 109.6833},
                'Kabupaten Banyumas': {lat: -7.5167, lng: 109.2833},
                'Kabupaten Batang': {lat: -6.9167, lng: 109.7333},
                'Kabupaten Blora': {lat: -6.9667, lng: 111.4167},
                'Kabupaten Boyolali': {lat: -7.5333, lng: 110.5833},
                'Kabupaten Brebes': {lat: -6.8667, lng: 109.0333},
                'Kabupaten Cilacap': {lat: -7.7167, lng: 109.0167},
                'Kabupaten Demak': {lat: -6.8833, lng: 110.6333},
                'Kabupaten Grobogan': {lat: -7.0667, lng: 110.9167},
                'Kabupaten Jepara': {lat: -6.5833, lng: 110.6667},
                'Kabupaten Karanganyar': {lat: -7.6167, lng: 111.0333},
                'Kabupaten Kebumen': {lat: -7.6667, lng: 109.6667},
                'Kabupaten Kendal': {lat: -6.9167, lng: 110.2000},
                'Kabupaten Klaten': {lat: -7.7167, lng: 110.6000},
                'Kabupaten Kudus': {lat: -6.8167, lng: 110.8333},
                'Kabupaten Magelang': {lat: -7.4667, lng: 110.2167},
                'Kabupaten Pati': {lat: -6.7500, lng: 111.0333},
                'Kabupaten Pekalongan': {lat: -7.0000, lng: 109.6167},
                'Kabupaten Pemalang': {lat: -6.8833, lng: 109.3833},
                'Kabupaten Purbalingga': {lat: -7.3833, lng: 109.3667},
                'Kabupaten Purworejo': {lat: -7.7167, lng: 110.0167},
                'Kabupaten Rembang': {lat: -6.7000, lng: 111.3500},
                'Kabupaten Semarang': {lat: -7.1500, lng: 110.5000},
                'Kabupaten Sragen': {lat: -7.4167, lng: 111.0000},
                'Kabupaten Sukoharjo': {lat: -7.6833, lng: 110.8333},
                'Kabupaten Tegal': {lat: -6.9167, lng: 109.1000},
                'Kabupaten Temanggung': {lat: -7.3167, lng: 110.1667},
                'Kabupaten Wonogiri': {lat: -7.8167, lng: 110.9167},
                'Kabupaten Wonosobo': {lat: -7.3667, lng: 109.9000},

                // DI Yogyakarta
                'Yogyakarta': {lat: -7.7956, lng: 110.3695},
                'Kabupaten Bantul': {lat: -7.8833, lng: 110.3333},
                'Kabupaten Gunungkidul': {lat: -7.9667, lng: 110.6000},
                'Kabupaten Kulon Progo': {lat: -7.8333, lng: 110.1667},
                'Kabupaten Sleman': {lat: -7.7167, lng: 110.3500},

                // Jawa Timur
                'Surabaya': {lat: -7.2504, lng: 112.7688},
                'Malang': {lat: -7.9797, lng: 112.6304},
                'Batu': {lat: -7.8667, lng: 112.5167},
                'Blitar': {lat: -8.0667, lng: 112.1667},
                'Kediri': {lat: -7.8167, lng: 112.0167},
                'Madiun': {lat: -7.6333, lng: 111.5333},
                'Mojokerto': {lat: -7.4667, lng: 112.4333},
                'Pasuruan': {lat: -7.6333, lng: 112.9000},
                'Probolinggo': {lat: -7.7500, lng: 113.2167},
                'Kabupaten Bangkalan': {lat: -7.0333, lng: 112.7500},
                'Kabupaten Banyuwangi': {lat: -8.2167, lng: 114.3667},
                'Kabupaten Blitar': {lat: -8.1000, lng: 112.1667},
                'Kabupaten Bojonegoro': {lat: -7.1500, lng: 111.8833},
                'Kabupaten Bondowoso': {lat: -7.9167, lng: 113.8167},
                'Kabupaten Gresik': {lat: -7.1667, lng: 112.6167},
                'Kabupaten Jember': {lat: -8.1667, lng: 113.7000},
                'Kabupaten Jombang': {lat: -7.5500, lng: 112.2333},
                'Kabupaten Kediri': {lat: -7.8333, lng: 112.0167},
                'Kabupaten Lamongan': {lat: -7.1167, lng: 112.4167},
                'Kabupaten Lumajang': {lat: -8.1333, lng: 113.2167},
                'Kabupaten Madiun': {lat: -7.6333, lng: 111.5000},
                'Kabupaten Magetan': {lat: -7.6500, lng: 111.3500},
                'Kabupaten Malang': {lat: -8.0167, lng: 112.6333},
                'Kabupaten Mojokerto': {lat: -7.4667, lng: 112.4333},
                'Kabupaten Nganjuk': {lat: -7.6000, lng: 111.9000},
                'Kabupaten Ngawi': {lat: -7.4000, lng: 111.4500},
                'Kabupaten Pacitan': {lat: -8.2000, lng: 111.0833},
                'Kabupaten Pamekasan': {lat: -7.1667, lng: 113.4833},
                'Kabupaten Pasuruan': {lat: -7.7333, lng: 112.9000},
                'Kabupaten Ponorogo': {lat: -7.8667, lng: 111.4667},
                'Kabupaten Probolinggo': {lat: -7.8833, lng: 113.2167},
                'Kabupaten Sampang': {lat: -7.1833, lng: 113.2333},
                'Kabupaten Sidoarjo': {lat: -7.4500, lng: 112.7167},
                'Kabupaten Situbondo': {lat: -7.7167, lng: 114.0167},
                'Kabupaten Sumenep': {lat: -7.0167, lng: 113.8667},
                'Kabupaten Trenggalek': {lat: -8.0500, lng: 111.7167},
                'Kabupaten Tuban': {lat: -6.9000, lng: 111.9667},
                'Kabupaten Tulungagung': {lat: -8.0667, lng: 111.9000},

                // Banten
                'Tangerang': {lat: -6.1783, lng: 106.6319},
                'Tangerang Selatan': {lat: -6.2875, lng: 106.7175},
                'Cilegon': {lat: -6.0167, lng: 106.0333},
                'Serang': {lat: -6.1167, lng: 106.1500},
                'Kabupaten Lebak': {lat: -6.5667, lng: 106.2500},
                'Kabupaten Pandeglang': {lat: -6.3167, lng: 106.1000},
                'Kabupaten Serang': {lat: -6.1167, lng: 106.1500},
                'Kabupaten Tangerang': {lat: -6.1783, lng: 106.6319},

                // Sumatera Utara
                'Medan': {lat: 3.5952, lng: 98.6722},
                'Binjai': {lat: 3.6000, lng: 98.4833},
                'Gunungsitoli': {lat: 1.2833, lng: 97.6167},
                'Padangsidimpuan': {lat: 1.3833, lng: 99.2667},
                'Pematangsiantar': {lat: 2.9667, lng: 99.0667},
                'Sibolga': {lat: 1.7333, lng: 98.7833},
                'Tanjungbalai': {lat: 2.9667, lng: 99.8000},
                'Tebing Tinggi': {lat: 3.3167, lng: 99.1500},

                // Sumatera Barat
                'Padang': {lat: -0.9471, lng: 100.4172},
                'Bukittinggi': {lat: -0.3000, lng: 100.3667},
                'Padangpanjang': {lat: -0.4667, lng: 100.4000},
                'Pariaman': {lat: -0.6167, lng: 100.1167},
                'Payakumbuh': {lat: -0.2167, lng: 100.6333},
                'Sawahlunto': {lat: -0.6833, lng: 100.7833},
                'Solok': {lat: -0.8000, lng: 100.6500},

                // Riau
                'Pekanbaru': {lat: 0.5071, lng: 101.4478},
                'Dumai': {lat: 1.6833, lng: 101.4500},

                // Kepulauan Riau
                'Batam': {lat: 1.1304, lng: 104.0530},
                'Tanjungpinang': {lat: 0.9167, lng: 104.4500},

                // Jambi
                'Jambi': {lat: -1.6101, lng: 103.6131},
                'Sungai Penuh': {lat: -2.0667, lng: 101.3833},

                // Sumatera Selatan
                'Palembang': {lat: -2.9761, lng: 104.7754},
                'Lubuklinggau': {lat: -3.3000, lng: 102.8667},
                'Pagar Alam': {lat: -4.0667, lng: 103.2333},
                'Prabumulih': {lat: -3.4333, lng: 104.2333},

                // Bengkulu
                'Bengkulu': {lat: -3.8004, lng: 102.2655},

                // Lampung
                'Bandar Lampung': {lat: -5.4292, lng: 105.2610},
                'Metro': {lat: -5.1133, lng: 105.3067},

                // Bangka Belitung
                'Pangkalpinang': {lat: -2.1333, lng: 106.1167},

                // Aceh
                'Banda Aceh': {lat: 5.5483, lng: 95.3238},
                'Langsa': {lat: 4.4667, lng: 97.9667},
                'Lhokseumawe': {lat: 5.1833, lng: 97.1500},
                'Sabang': {lat: 5.8833, lng: 95.3167},
                'Subulussalam': {lat: 2.6833, lng: 97.9500},

                // Kalimantan Barat
                'Pontianak': {lat: -0.0263, lng: 109.3425},
                'Singkawang': {lat: 0.9167, lng: 108.9833},

                // Kalimantan Tengah
                'Palangkaraya': {lat: -2.2135, lng: 113.9213},

                // Kalimantan Selatan
                'Banjarmasin': {lat: -3.3194, lng: 114.5906},
                'Banjarbaru': {lat: -3.4167, lng: 114.8333},

                // Kalimantan Timur
                'Samarinda': {lat: -0.4978, lng: 117.1436},
                'Balikpapan': {lat: -1.2379, lng: 116.8289},
                'Bontang': {lat: 0.1333, lng: 117.4833},

                // Kalimantan Utara
                'Tarakan': {lat: 3.3000, lng: 117.6333},

                // Sulawesi Utara
                'Manado': {lat: 1.4748, lng: 124.8421},
                'Bitung': {lat: 1.4500, lng: 125.1833},
                'Kotamobagu': {lat: 0.7167, lng: 124.3167},
                'Tomohon': {lat: 1.3333, lng: 124.8333},

                // Sulawesi Tengah
                'Palu': {lat: -0.8917, lng: 119.8707},

                // Sulawesi Selatan
                'Makassar': {lat: -5.1477, lng: 119.4327},
                'Palopo': {lat: -2.9833, lng: 120.2000},
                'Parepare': {lat: -4.0167, lng: 119.6167},

                // Sulawesi Tenggara
                'Kendari': {lat: -3.9450, lng: 122.5986},
                'Baubau': {lat: -5.4667, lng: 122.6000},

                // Gorontalo
                'Gorontalo': {lat: 0.5435, lng: 123.0596},

                // Sulawesi Barat
                'Mamuju': {lat: -2.6833, lng: 119.4167},

                // Bali
                'Denpasar': {lat: -8.6500, lng: 115.2167},

                // Nusa Tenggara Barat
                'Mataram': {lat: -8.5833, lng: 116.1167},
                'Bima': {lat: -8.4667, lng: 118.7167},

                // Nusa Tenggara Timur
                'Kupang': {lat: -10.1718, lng: 123.6075},

                // Maluku
                'Ambon': {lat: -3.6954, lng: 128.1814},
                'Tual': {lat: -5.6333, lng: 132.7500},

                // Maluku Utara
                'Ternate': {lat: 0.7833, lng: 127.3667},
                'Tidore Kepulauan': {lat: 0.6833, lng: 127.4000},

                // Papua Barat
                'Manokwari': {lat: -0.8667, lng: 134.0833},
                'Sorong': {lat: -0.8667, lng: 131.2500},

                // Papua
                'Jayapura': {lat: -2.5489, lng: 140.7197}
            };

            const input = document.getElementById('kota_domisili');
            const suggestionsDiv = document.getElementById('city-suggestions');
            let cities = Object.keys(koordinatKota);

            input.addEventListener('input', function() {
                const value = this.value.toUpperCase();
                
                if (value.length < 2) {
                    hideSuggestions();
                    return;
                }

                const filteredCities = cities.filter(city => 
                    city.toUpperCase().includes(value)
                ).slice(0, 10); // Limit to 10 suggestions

                if (filteredCities.length > 0) {
                    showSuggestions(filteredCities, value);
                } else {
                    hideSuggestions();
                }
            });

            input.addEventListener('keydown', function(e) {
                const items = suggestionsDiv.querySelectorAll('.suggestion-item');
                const activeItem = suggestionsDiv.querySelector('.suggestion-item.active');
                
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (activeItem) {
                        activeItem.classList.remove('active');
                        const nextItem = activeItem.nextElementSibling;
                        if (nextItem) {
                            nextItem.classList.add('active');
                        } else {
                            items[0].classList.add('active');
                        }
                    } else if (items.length > 0) {
                        items[0].classList.add('active');
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (activeItem) {
                        activeItem.classList.remove('active');
                        const prevItem = activeItem.previousElementSibling;
                        if (prevItem) {
                            prevItem.classList.add('active');
                        } else {
                            items[items.length - 1].classList.add('active');
                        }
                    } else if (items.length > 0) {
                        items[items.length - 1].classList.add('active');
                    }
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (activeItem) {
                        selectCity(activeItem.textContent);
                    }
                } else if (e.key === 'Escape') {
                    hideSuggestions();
                }
            });

            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                    hideSuggestions();
                }
            });

            function showSuggestions(cities, searchTerm) {
                suggestionsDiv.innerHTML = '';
                
                cities.forEach((city, index) => {
                    const div = document.createElement('div');
                    div.className = 'suggestion-item px-3 py-2 cursor-pointer';
                    div.style.cursor = 'pointer';
                    
                    // Highlight matching text
                    const regex = new RegExp(`(${searchTerm})`, 'gi');
                    const highlightedText = city.replace(regex, '<strong>$1</strong>');
                    div.innerHTML = highlightedText;
                    
                    div.addEventListener('click', function() {
                        selectCity(city);
                    });
                    
                    div.addEventListener('mouseenter', function() {
                        suggestionsDiv.querySelectorAll('.suggestion-item').forEach(item => {
                            item.classList.remove('active');
                        });
                        this.classList.add('active');
                    });
                    
                    suggestionsDiv.appendChild(div);
                });
                
                suggestionsDiv.style.display = 'block';
            }

            function toTitleCase(str) {
                return str
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ');
            }
            
            function selectCity(city) {
                input.value = toTitleCase(city);
                hideSuggestions();
                input.focus();
            
                // Trigger input event to ensure value update
                const event = new Event('input', { bubbles: true });
                input.dispatchEvent(event);
            }


            function hideSuggestions() {
                suggestionsDiv.style.display = 'none';
                suggestionsDiv.querySelectorAll('.suggestion-item').forEach(item => {
                    item.classList.remove('active');
                });
            }
        });
    </script>
  
    <script>
        // JavaScript untuk toggle navigation
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggleNavBtn');
            const navContainer = document.getElementById('stickyNavContainer');
            const formContainer = document.getElementById('formContainer');
            let isMinimized = false;

            toggleBtn.addEventListener('click', function() {
                isMinimized = !isMinimized;
                
                if (isMinimized) {
                    // Minimize navigation
                    navContainer.classList.add('minimized');
                    formContainer.classList.add('nav-minimized');
                    toggleBtn.innerHTML = '⇦';
                    toggleBtn.title = 'Maximize Navigation';
                } else {
                    // Maximize navigation
                    navContainer.classList.remove('minimized');
                    formContainer.classList.remove('nav-minimized');
                    toggleBtn.innerHTML = '⇨';
                    toggleBtn.title = 'Minimize Navigation';
                }
            });

            // Smooth scrolling untuk navigation links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Update active navigation berdasarkan scroll position
            function updateActiveNavigation() {
                const sections = document.querySelectorAll('section[id]');
                const navLinks = document.querySelectorAll('.nav-link');
                
                let currentSection = '';
                sections.forEach(section => {
                    const rect = section.getBoundingClientRect();
                    if (rect.top <= 100 && rect.bottom >= 100) {
                        currentSection = section.id;
                    }
                });

                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '#' + currentSection) {
                        link.classList.add('active');
                    }
                });
            }

            window.addEventListener('scroll', updateActiveNavigation);
            updateActiveNavigation(); // Initial call
        });
    </script>
    
    <script>
        // JavaScript untuk Drag & Drop Struktur Organisasi
        let draggedElement = null;
        let selectedNode = null;
        let nodeCounter = 0;
        let orgNodes = [];
        let currentZoom = 1;
        
        // Event listeners untuk drag & drop
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.org-element');
            const canvas = document.getElementById('orgCanvas');
        
            elements.forEach(element => {
                element.addEventListener('dragstart', handleDragStart);
            });
        
            canvas.addEventListener('dragover', handleDragOver);
            canvas.addEventListener('drop', handleDrop);
        });
        
        // Zoom functions
        function zoomIn() {
            currentZoom = Math.min(currentZoom + 0.2, 3);
            applyZoom();
        }
        
        function zoomOut() {
            currentZoom = Math.max(currentZoom - 0.2, 0.5);
            applyZoom();
        }
        
        function resetZoom() {
            currentZoom = 1;
            applyZoom();
        }
        
        function applyZoom() {
            const canvas = document.getElementById('orgCanvas');
            canvas.style.transform = `scale(${currentZoom})`;
            document.getElementById('zoomLevel').textContent = `${Math.round(currentZoom * 100)}%`;
        }
        
        // Draw connections between nodes
        function drawConnections() {
            // Remove existing lines
            document.querySelectorAll('.connection-line').forEach(line => line.remove());
            
            const canvas = document.getElementById('orgCanvas');
            const nodes = Array.from(canvas.querySelectorAll('.org-node'));
            
            // Group nodes by hierarchy
            const ceoNodes = nodes.filter(n => n.classList.contains('ceo'));
            const managerNodes = nodes.filter(n => n.classList.contains('manager'));
            const supervisorNodes = nodes.filter(n => n.classList.contains('supervisor'));
            const staffNodes = nodes.filter(n => n.classList.contains('staff'));
            
            // Draw lines from CEO to Managers
            ceoNodes.forEach(ceo => {
                managerNodes.forEach(manager => {
                    drawLine(ceo, manager);
                });
            });
            
            // Draw lines from Managers to Supervisors
            managerNodes.forEach(manager => {
                supervisorNodes.forEach(supervisor => {
                    drawLine(manager, supervisor);
                });
            });
            
            // Draw lines from Supervisors to Staff
            supervisorNodes.forEach(supervisor => {
                staffNodes.forEach(staff => {
                    drawLine(supervisor, staff);
                });
            });
        }
        
        function drawLine(fromNode, toNode) {
            const canvas = document.getElementById('orgCanvas');
            const fromRect = fromNode.getBoundingClientRect();
            const toRect = toNode.getBoundingClientRect();
            const canvasRect = canvas.getBoundingClientRect();
            
            const fromX = fromRect.left + fromRect.width / 2 - canvasRect.left;
            const fromY = fromRect.bottom - canvasRect.top;
            const toX = toRect.left + toRect.width / 2 - canvasRect.left;
            const toY = toRect.top - canvasRect.top;
            
            // Vertical line from fromNode
            const vLine = document.createElement('div');
            vLine.className = 'connection-line vertical';
            vLine.style.left = `${fromX}px`;
            vLine.style.top = `${fromY}px`;
            vLine.style.height = `${Math.abs(toY - fromY) / 2}px`;
            canvas.appendChild(vLine);
            
            // Horizontal line
            const midY = fromY + Math.abs(toY - fromY) / 2;
            const hLine = document.createElement('div');
            hLine.className = 'connection-line';
            hLine.style.left = `${Math.min(fromX, toX)}px`;
            hLine.style.top = `${midY}px`;
            hLine.style.width = `${Math.abs(toX - fromX)}px`;
            canvas.appendChild(hLine);
            
            // Vertical line to toNode
            const vLine2 = document.createElement('div');
            vLine2.className = 'connection-line vertical';
            vLine2.style.left = `${toX}px`;
            vLine2.style.top = `${midY}px`;
            vLine2.style.height = `${Math.abs(toY - midY)}px`;
            canvas.appendChild(vLine2);
        }
        
        function handleDragStart(e) {
            draggedElement = e.target;
            e.dataTransfer.effectAllowed = 'copy';
        }
        
        function handleDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'copy';
        }
        
        function handleDrop(e) {
            e.preventDefault();
            
            if (!draggedElement) return;
        
            const canvas = document.getElementById('orgCanvas');
            const canvasRect = canvas.getBoundingClientRect();
            
            const x = e.clientX - canvasRect.left;
            const y = e.clientY - canvasRect.top;
        
            createOrgNode(draggedElement.dataset.type, x, y, draggedElement.textContent);
            
            draggedElement = null;
            updateStructureData();
        }
        
        function createOrgNode(type, x, y, title) {
            const canvas = document.getElementById('orgCanvas');
            const node = document.createElement('div');
            
            nodeCounter++;
            const nodeId = `node_${nodeCounter}`;
            
            node.className = `org-node ${type}`;
            node.id = nodeId;
            node.style.left = `${x - 50}px`;
            node.style.top = `${y - 20}px`;
            node.textContent = title;
            
            // Event listeners untuk node
            node.addEventListener('click', selectNode);
            node.addEventListener('mousedown', startDrag);
            node.addEventListener('dblclick', editNode);
            
            canvas.appendChild(node);
            
            // Simpan data node
            orgNodes.push({
                id: nodeId,
                type: type,
                title: title,
                name: '',
                x: x - 50,
                y: y - 20
            });
        }
        
        function selectNode(e) {
            e.stopPropagation();
            
            // Hapus seleksi sebelumnya
            document.querySelectorAll('.org-node').forEach(node => {
                node.classList.remove('selected');
            });
            
            // Pilih node ini
            e.target.classList.add('selected');
            selectedNode = e.target;
        }
        
        function editNode(e) {
            e.stopPropagation();
            
            const nodeId = e.target.id;
            const nodeData = orgNodes.find(n => n.id === nodeId);
            
            if (nodeData) {
                document.getElementById('nodeTitle').value = nodeData.title;
                document.getElementById('nodeName').value = nodeData.name;
                document.getElementById('nodeInfoPanel').classList.add('active');
                selectedNode = e.target;
            }
        }
        
        function updateNode() {
            if (!selectedNode) return;
            
            const nodeId = selectedNode.id;
            const nodeData = orgNodes.find(n => n.id === nodeId);
            
            if (nodeData) {
                const newTitle = document.getElementById('nodeTitle').value;
                const newName = document.getElementById('nodeName').value;
                
                nodeData.title = newTitle;
                nodeData.name = newName;
                
                selectedNode.textContent = newTitle + (newName ? `\n(${newName})` : '');
                
                closeNodePanel();
                updateStructureData();
            }
        }
        
        function closeNodePanel() {
            document.getElementById('nodeInfoPanel').classList.remove('active');
        }
        
        let isDragging = false;
        let dragOffset = { x: 0, y: 0 };
        
        function startDrag(e) {
            if (e.target.classList.contains('org-node')) {
                isDragging = true;
                selectedNode = e.target;
                
                const rect = e.target.getBoundingClientRect();
                dragOffset.x = e.clientX - rect.left;
                dragOffset.y = e.clientY - rect.top;
                
                document.addEventListener('mousemove', dragNode);
                document.addEventListener('mouseup', stopDrag);
                
                e.preventDefault();
            }
        }
        
        function dragNode(e) {
            if (!isDragging || !selectedNode) return;
            
            const canvas = document.getElementById('orgCanvas');
            const canvasRect = canvas.getBoundingClientRect();
            
            const x = e.clientX - canvasRect.left - dragOffset.x;
            const y = e.clientY - canvasRect.top - dragOffset.y;
            
            selectedNode.style.left = `${x}px`;
            selectedNode.style.top = `${y}px`;
            
            // Update data node
            const nodeData = orgNodes.find(n => n.id === selectedNode.id);
            if (nodeData) {
                nodeData.x = x;
                nodeData.y = y;
            }
        }
        
        function stopDrag() {
            isDragging = false;
            document.removeEventListener('mousemove', dragNode);
            document.removeEventListener('mouseup', stopDrag);
            updateStructureData();
        }
        
        function clearCanvas() {
            if (confirm('Apakah Anda yakin ingin menghapus semua elemen?')) {
                document.getElementById('orgCanvas').innerHTML = '';
                orgNodes = [];
                selectedNode = null;
                nodeCounter = 0;
                updateStructureData();
            }
        }
        
        function deleteSelected() {
            if (selectedNode) {
                const nodeId = selectedNode.id;
                selectedNode.remove();
                orgNodes = orgNodes.filter(n => n.id !== nodeId);
                selectedNode = null;
                updateStructureData();
            }
        }
        
        function autoAlign() {
            const canvas = document.getElementById('orgCanvas');
            const nodes = canvas.querySelectorAll('.org-node');
            
            let currentY = 50;
            const levelGap = 100;
            const nodeGap = 120;
            
            // Kelompokkan berdasarkan tipe
            const ceoNodes = Array.from(nodes).filter(n => n.classList.contains('ceo'));
            const managerNodes = Array.from(nodes).filter(n => n.classList.contains('manager'));
            const supervisorNodes = Array.from(nodes).filter(n => n.classList.contains('supervisor'));
            const staffNodes = Array.from(nodes).filter(n => n.classList.contains('staff'));
            const internNodes = Array.from(nodes).filter(n => n.classList.contains('intern'));
            
            // Posisikan CEO di atas
            alignNodesInRow(ceoNodes, currentY, nodeGap);
            currentY += levelGap;
            
            // Posisikan Manager
            alignNodesInRow(managerNodes, currentY, nodeGap);
            currentY += levelGap;
            
            // Posisikan Supervisor
            alignNodesInRow(supervisorNodes, currentY, nodeGap);
            currentY += levelGap;
            
            // Posisikan Staff
            alignNodesInRow(staffNodes, currentY, nodeGap);
            currentY += levelGap;
            
            // Posisikan Intern
            alignNodesInRow(internNodes, currentY, nodeGap);
            
            updateStructureData();
        }
        
        function alignNodesInRow(nodes, y, gap) {
            const canvasWidth = document.getElementById('orgCanvas').offsetWidth;
            const totalWidth = (nodes.length - 1) * gap;
            const startX = (canvasWidth - totalWidth) / 2;
            
            nodes.forEach((node, index) => {
                const x = startX + (index * gap);
                node.style.left = `${x}px`;
                node.style.top = `${y}px`;
                
                // Update data
                const nodeData = orgNodes.find(n => n.id === node.id);
                if (nodeData) {
                    nodeData.x = x;
                    nodeData.y = y;
                }
            });
        }
        
        function updateStructureData() {
            const structureData = {
                nodes: orgNodes,
                created_at: new Date().toISOString()
            };
            
            document.getElementById('orgStructureData').value = JSON.stringify(structureData, null, 2);
        }
        
        function exportStructure() {
            const data = document.getElementById('orgStructureData').value;
            if (!data) {
                alert('Tidak ada data struktur organisasi untuk diekspor.');
                return;
            }
            
            const blob = new Blob([data], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'struktur_organisasi.json';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
        
        // Tutup panel info ketika klik di luar
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.node-info-panel') && !e.target.closest('.org-node')) {
                closeNodePanel();
                
                // Hapus seleksi
                document.querySelectorAll('.org-node').forEach(node => {
                    node.classList.remove('selected');
                });
                selectedNode = null;
            }
        });
        </script>

    <script>
        let saudaraIndex = 1;
        let anakIndex = 1;
        let pendidikanIndex = 1;
        let kursusIndex = 1;
        let pengalamanIndex = 1;
        let organisasiIndex = 1;
        let referensiIndex = 1;
        let kontakDaruratIndex = 1;
        
        // Function to toggle pendidikan current status
        function togglePendidikanCurrent(checkbox) {
            const row = checkbox.closest('tr');
            const tahunKeluarInput = row.querySelector('.tahun-keluar-input');
            
            if (checkbox.checked) {
                tahunKeluarInput.value = '';
                tahunKeluarInput.disabled = true;
                tahunKeluarInput.placeholder = 'Sedang Bersekolah/Kuliah';
            } else {
                tahunKeluarInput.disabled = false;
                tahunKeluarInput.placeholder = '';
            }
        }
        
        // Function to toggle pekerjaan current status
        function togglePekerjaanCurrent(checkbox) {
            const table = checkbox.closest('table');
            const masaKerjaSampaiInput = table.querySelector('.masa-kerja-sampai-input');
            const alasanBerhentiRow = table.querySelector('.alasan-berhenti-row');
            const alasanBerhentiTextarea = alasanBerhentiRow.querySelector('textarea');
            
            if (checkbox.checked) {
                masaKerjaSampaiInput.value = '';
                masaKerjaSampaiInput.disabled = true;
                alasanBerhentiRow.style.display = 'none';
                alasanBerhentiTextarea.required = false;
            } else {
                masaKerjaSampaiInput.disabled = false;
                alasanBerhentiRow.style.display = '';
                alasanBerhentiTextarea.required = true;
            }
        }
        
        // Functions for Saudara Kandung
        function addSaudara() {
            const table = document.getElementById('saudara-table');
            const newRow = table.insertRow();
            newRow.className = 'saudara-row';
            newRow.setAttribute('data-index', saudaraIndex);
            newRow.innerHTML = `
                <td>${saudaraIndex + 1}</td>
                <td><input type="text" name="data_saudara[${saudaraIndex}][nama]" placeholder="Nama"></td>
                <td><input type="text" name="data_saudara[${saudaraIndex}][pekerjaan]" placeholder="Pekerjaan"></td>
                <td><input type="text" name="data_saudara[${saudaraIndex}][pendidikan]" placeholder="Pendidikan"></td>
                <td><input type="date" name="data_saudara[${saudaraIndex}][tanggal_lahir]"></td>
                <td>
                    <button type="button" class="ini-saya-btn" onclick="selectIniSaya(this, ${saudaraIndex})">
                        Ini Saya
                    </button>
                    <input type="hidden" name="data_saudara[${saudaraIndex}][is_me]" value="0">
                </td>
                <td><button type="button" class="remove-btn" onclick="removeSaudara(this)">Hapus</button></td>
            `;
            saudaraIndex++;
        }
        
        function removeSaudara(button) {
            const row = button.closest('tr');
            const rowIndex = parseInt(row.getAttribute('data-index'));
            
            // Jika row yang dihapus adalah yang dipilih sebagai "Ini Saya", reset selection
            if (selectedMeIndex === rowIndex) {
                selectedMeIndex = null;
            }
            
            row.remove();
        }

        function selectIniSaya(clickedButton, index) {
            // Reset semua tombol "Ini Saya"
            const allIniSayaButtons = document.querySelectorAll('.ini-saya-btn');
            const allHiddenInputs = document.querySelectorAll('input[name*="[is_me]"]');
            const allRows = document.querySelectorAll('.saudara-row');
            
            // Reset semua button dan row
            allIniSayaButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.disabled = false;
                btn.textContent = 'Ini Saya';
            });
            
            allHiddenInputs.forEach(input => {
                input.value = '0';
            });
            
            allRows.forEach(row => {
                row.classList.remove('is-me');
            });
            
            // Set button yang diklik sebagai active
            clickedButton.classList.add('active');
            clickedButton.textContent = 'Ini Saya';
            
            // Set hidden input value
            const hiddenInput = clickedButton.parentElement.querySelector('input[type="hidden"]');
            hiddenInput.value = '1';
            
            // Highlight row
            const currentRow = clickedButton.closest('tr');
            currentRow.classList.add('is-me');
            
            // Disable tombol lainnya
            allIniSayaButtons.forEach(btn => {
                if (btn !== clickedButton) {
                    btn.disabled = true;
                    btn.style.opacity = '0.5';
                }
            });
            
            // Update selected index
            selectedMeIndex = index;
            
            console.log(`Anak ke-${index + 1} dipilih sebagai "Ini Saya"`);
        }
        
        // Functions for Data Anak
        function addAnak() {
            const table = document.getElementById('anak-table');
            const newRow = table.insertRow();
            newRow.className = 'anak-row';
            newRow.innerHTML = `
                <td>Anak ke ${anakIndex + 1}</td>
                <td><input type="text" name="data_anak[${anakIndex}][nama]"></td>
                <td><input type="text" name="data_anak[${anakIndex}][pekerjaan]"></td>
                <td><input type="text" name="data_anak[${anakIndex}][pendidikan]"></td>
                <td><input type="date" name="data_anak[${anakIndex}][tanggal_lahir]"></td>
                <td><button type="button" onclick="removeAnak(this)">Hapus</button></td>
            `;
            anakIndex++;
        }
        
        function removeAnak(button) {
            button.closest('tr').remove();
        }
        
        // Functions for Pendidikan Formal
        function addPendidikan() {
            const table = document.getElementById('pendidikan-table');
            const newRow = table.insertRow();
            newRow.className = 'pendidikan-row';
            newRow.innerHTML = `
                <td>
                    <select name="pendidikan_formal[${pendidikanIndex}][jenjang]">
                        <option value="">Pilih Jenjang</option>
                        <option value="SD">SD</option>
                        <option value="SMP">SMP</option>
                        <option value="SMU/SMK">SMU/SMK</option>
                        <option value="Akademi/Diploma">Akademi/Diploma</option>
                        <option value="S1">S1</option>
                        <option value="S2">S2</option>
                    </select>
                </td>
                <td><input type="text" name="pendidikan_formal[${pendidikanIndex}][nama_sekolah]"></td>
                <td><input type="number" name="pendidikan_formal[${pendidikanIndex}][tahun_masuk]"></td>
                <td><input type="number" name="pendidikan_formal[${pendidikanIndex}][tahun_keluar]" class="tahun-keluar-input"></td>
                <td><input type="text" name="pendidikan_formal[${pendidikanIndex}][nilai]"></td>
                <td>
                    <label>
                        <input type="checkbox" name="pendidikan_formal[${pendidikanIndex}][sedang_kuliah]" value="1" onchange="togglePendidikanCurrent(this)">
                        Saat Ini Kuliah/Sekolah Disini
                    </label>
                </td>
                <td><button type="button" onclick="removePendidikan(this)">Hapus</button></td>
            `;
            pendidikanIndex++;
        }
        
        function removePendidikan(button) {
            button.closest('tr').remove();
        }
        
        // Functions for Pendidikan Non Formal (Kursus)
        function addKursus() {
            const table = document.getElementById('kursus-table');
            const newRow = table.insertRow();
            newRow.className = 'kursus-row';
            newRow.innerHTML = `
                <td>${kursusIndex + 1}</td>
                <td><input type="text" name="pendidikan_non_formal[${kursusIndex}][nama_kursus]"></td>
                <td><input type="text" name="pendidikan_non_formal[${kursusIndex}][lama_pendidikan]"></td>
                <td><input type="text" name="pendidikan_non_formal[${kursusIndex}][keterangan]"></td>
                <td><button type="button" onclick="removeKursus(this)">Hapus</button></td>
            `;
            kursusIndex++;
        }
        
        function removeKursus(button) {
            button.closest('tr').remove();
            updateKursusNumbers();
        }
        
        function updateKursusNumbers() {
            const rows = document.querySelectorAll('#kursus-table .kursus-row');
            rows.forEach((row, index) => {
                row.cells[0].textContent = index + 1;
            });
        }
        
        // Functions for Pengalaman Kerja
        function addPengalaman() {
            const container = document.getElementById('pengalaman-container');
            const newDiv = document.createElement('div');
            newDiv.className = 'pengalaman-item';
            newDiv.innerHTML = `
                <table border="1" class="animate">
                    <tr>
                        <td>Nama Perusahaan</td>
                        <td><input type="text" name="pengalaman_kerja[${pengalamanIndex}][nama_perusahaan]" placeholder="Nama Perusahaan"></td>
                        <td>Jabatan</td>
                        <td><input type="text" name="pengalaman_kerja[${pengalamanIndex}][jabatan]" placeholder="Jabatan Anda"></td>
                    </tr>
                    <tr>
                        <td>Masa Kerja (Dari)</td>
                        <td><input type="date" name="pengalaman_kerja[${pengalamanIndex}][masa_kerja_dari]"></td>
                        <td>Masa Kerja (Sampai)</td>
                        <td><input type="date" name="pengalaman_kerja[${pengalamanIndex}][masa_kerja_sampai]" class="masa-kerja-sampai-input"></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2">
                            <label>
                                <input type="checkbox" name="pengalaman_kerja[${pengalamanIndex}][masih_bekerja]" value="1" onchange="togglePekerjaanCurrent(this)">
                                Saat Ini Masih Bekerja Disini
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>Uraian Pekerjaan</td>
                        <td colspan="3"><textarea name="pengalaman_kerja[${pengalamanIndex}][uraian_pekerjaan]" rows="4" cols="50" placeholder="Uraian Pekerjaan Anda"></textarea></td>
                    </tr>
                    <tr class="alasan-berhenti-row">
                        <td>Alasan Berhenti</td>
                        <td colspan="3"><textarea name="pengalaman_kerja[${pengalamanIndex}][alasan_berhenti]" rows="2" cols="50" placeholder="Alasan Berhenti"></textarea></td>
                    </tr>
                </table>
                <button type="button" onclick="removePengalaman(this)">Hapus Pengalaman</button>
                <hr>
            `;
            container.appendChild(newDiv);
            pengalamanIndex++;
        }
        
        function removePengalaman(button) {
            button.closest('.pengalaman-item').remove();
        }
        
        // Functions for Aktivitas Sosial/Organisasi
        function addOrganisasi() {
            const table = document.getElementById('organisasi-table');
            const newRow = table.insertRow();
            newRow.className = 'organisasi-row';
            newRow.innerHTML = `
                <td><input type="text" name="aktivitas_sosial[${organisasiIndex}][waktu]" placeholder="contoh: Juni 2023 - Maret 2024"></td>
                <td><input type="text" name="aktivitas_sosial[${organisasiIndex}][nama_organisasi]" placeholder="Nama Organisasi"></td>
                <td><input type="text" name="aktivitas_sosial[${organisasiIndex}][bidang]" placeholder="Bergerak dibidang"></td>
                <td><input type="text" name="aktivitas_sosial[${organisasiIndex}][jabatan]" placeholder="Jabatan Anda"></td>
                <td><button type="button" onclick="removeOrganisasi(this)">Hapus</button></td>
            `;
            organisasiIndex++;
        }
        
        function removeOrganisasi(button) {
            button.closest('tr').remove();
        }
        
        // Functions for Referensi
        function addReferensi() {
            const table = document.getElementById('referensi-table');
            const newRow = table.insertRow();
            newRow.className = 'referensi-row';
            newRow.innerHTML = `
                <td><input type="text" name="referensi[${referensiIndex}][nama]" required></td>
                <td><input type="text" name="referensi[${referensiIndex}][no_telepon]" required></td>
                <td><input type="text" name="referensi[${referensiIndex}][jabatan]" required></td>
                <td><input type="text" name="referensi[${referensiIndex}][hubungan]" required></td>
                <td><button type="button" onclick="removeReferensi(this)">Hapus</button></td>
            `;
            referensiIndex++;
        }
        
        function removeReferensi(button) {
            button.closest('tr').remove();
        }
        
        // Functions for Kontak Darurat
        function addKontakDarurat() {
            const table = document.getElementById('darurat-table');
            const newRow = table.insertRow();
            newRow.className = 'darurat-row';
            newRow.innerHTML = `
                <td><input type="text" name="kontak_darurat[${kontakDaruratIndex}][nama]" required></td>
                <td><input type="text" name="kontak_darurat[${kontakDaruratIndex}][no_telepon]" required></td>
                <td><input type="text" name="kontak_darurat[${kontakDaruratIndex}][alamat]" required></td>
                <td><input type="text" name="kontak_darurat[${kontakDaruratIndex}][hubungan]" required></td>
                <td><button type="button" onclick="removeKontakDarurat(this)">Hapus</button></td>
            `;
            kontakDaruratIndex++;
        }
        
        function removeKontakDarurat(button) {
            button.closest('tr').remove();
        }
        
        // Initialize form when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial index based on existing rows if form is reloaded with old input
            const existingSaudara = document.querySelectorAll('#saudara-table .saudara-row');
            if (existingSaudara.length > 1) {
                saudaraIndex = existingSaudara.length;
            }
    
            const existingAnak = document.querySelectorAll('#anak-table .anak-row');
            if (existingAnak.length > 1) {
                anakIndex = existingAnak.length;
            }
    
            const existingPendidikan = document.querySelectorAll('#pendidikan-table .pendidikan-row');
            if (existingPendidikan.length > 1) {
                pendidikanIndex = existingPendidikan.length;
            }
    
            const existingKursus = document.querySelectorAll('#kursus-table .kursus-row');
            if (existingKursus.length > 1) {
                kursusIndex = existingKursus.length;
            }
    
            const existingPengalaman = document.querySelectorAll('#pengalaman-container .pengalaman-item');
            if (existingPengalaman.length > 1) {
                pengalamanIndex = existingPengalaman.length;
            }
    
            const existingOrganisasi = document.querySelectorAll('#organisasi-table .organisasi-row');
            if (existingOrganisasi.length > 1) {
                organisasiIndex = existingOrganisasi.length;
            }
    
            const existingReferensi = document.querySelectorAll('#referensi-table .referensi-row');
            if (existingReferensi.length > 1) {
                referensiIndex = existingReferensi.length;
            }
    
            const existingKontakDarurat = document.querySelectorAll('#darurat-table .darurat-row');
            if (existingKontakDarurat.length > 1) {
                kontakDaruratIndex = existingKontakDarurat.length;
            }
    
            // Initialize existing checkboxes on page load
            document.querySelectorAll('input[name*="[sedang_kuliah]"]').forEach(function(checkbox) {
                if (checkbox.checked) {
                    togglePendidikanCurrent(checkbox);
                }
            });
    
            document.querySelectorAll('input[name*="[masih_bekerja]"]').forEach(function(checkbox) {
                if (checkbox.checked) {
                    togglePekerjaanCurrent(checkbox);
                }
            });
        });
    </script>
    
    <script>
        // Enhanced JavaScript for Interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize animations
            initializeAnimations();
            
            // Setup progress tracking
            setupProgressTracking();
            
            // Setup form enhancements
            setupFormEnhancements();
            
            // Setup scroll animations
            setupScrollAnimations();
        });

        function initializeAnimations() {
            // Animate elements on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            // Observe all h2 and table elements
            document.querySelectorAll('h2, table').forEach(el => {
                observer.observe(el);
            });
        }

        function setupProgressTracking() {
            const progressBar = document.getElementById('progressBar');
            const requiredFields = document.querySelectorAll('input[required], textarea[required], select[required]');
            
            function updateProgress() {
                let filledFields = 0;
                requiredFields.forEach(field => {
                    if (field.value.trim() !== '') {
                        filledFields++;
                    }
                });
                
                const progress = (filledFields / requiredFields.length) * 100;
                progressBar.style.width = progress + '%';
            }

            // Update progress on input change
            requiredFields.forEach(field => {
                field.addEventListener('input', updateProgress);
                field.addEventListener('change', updateProgress);
            });
            
            // Initial progress calculation
            updateProgress();
        }

        function setupFormEnhancements() {
            // Add floating labels effect
            const inputs = document.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                // Add focus/blur effects
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                    this.parentElement.style.transition = 'all 0.3s ease';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });

                // Add input validation effects
                input.addEventListener('input', function() {
                    if (this.value.trim() !== '') {
                        this.style.borderColor = '#6EAA36';
                        this.style.backgroundColor = 'rgba(110, 170, 54, 0.05)';
                    } else if (this.hasAttribute('required')) {
                        this.style.borderColor = '#e0e0e0';
                        this.style.backgroundColor = 'white';
                    }
                });
            });

            // Enhanced button interactions
            const buttons = document.querySelectorAll('button');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // Create ripple effect
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        border-radius: 50%;
                        background: rgba(255, 255, 255, 0.6);
                        transform: scale(0);
                        animation: ripple 0.6s linear;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                    `;
                    
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        }

        function setupScrollAnimations() {
            // Smooth scroll behavior
            document.documentElement.style.scrollBehavior = 'smooth';
            
            // Add scroll-based animations
            window.addEventListener('scroll', function() {
                const sections = document.querySelectorAll('h2');
                const scrollTop = window.pageYOffset;
                
                sections.forEach((section, index) => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.offsetHeight;
                    
                    if (scrollTop > sectionTop - window.innerHeight + 100) {
                        section.style.animation = `slideInLeft 0.6s ease forwards ${index * 0.1}s`;
                    }
                });
            });
        }

        // Enhanced add/remove functions with animations
        function enhancedAddRow(originalFunction, tableId) {
            return function(...args) {
                const result = originalFunction.apply(this, args);
                const table = document.getElementById(tableId);
                const newRow = table.querySelector('tr:last-child');
                
                if (newRow) {
                    newRow.style.opacity = '0';
                    newRow.style.transform = 'translateY(20px)';
                    
                    setTimeout(() => {
                        newRow.style.transition = 'all 0.5s ease';
                        newRow.style.opacity = '1';
                        newRow.style.transform = 'translateY(0)';
                    }, 50);
                }
                
                return result;
            };
        }

        // Scroll to top function
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Form submission enhancement
        document.addEventListener('submit', function(e) {
            const submitBtn = document.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.innerHTML = '<span class="loading"></span> Menyimpan...';
            submitBtn.disabled = true;
            
            // Re-enable after 3 seconds (adjust based on your needs)
            setTimeout(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });

        // Add CSS animations keyframes
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInLeft {
                from {
                    transform: translateX(-50px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Enhanced input interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add shake animation for empty required fields on form attempt
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const requiredFields = this.querySelectorAll('[required]');
                let hasEmpty = false;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        hasEmpty = true;
                        field.style.animation = 'shake 0.5s ease-in-out';
                        field.style.borderColor = '#ff4444';
                        
                        setTimeout(() => {
                            field.style.animation = '';
                            field.style.borderColor = '#e0e0e0';
                        }, 500);
                    }
                });
            });
        });

        // Add shake keyframe
        const shakeStyle = document.createElement('style');
        shakeStyle.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }
        `;
        document.head.appendChild(shakeStyle);
    </script>

    // <script>
    //     function previewPhoto(event) {
    //         const file = event.target.files[0];
    //         const preview = document.getElementById('preview-image');
    //         const photoPreview = document.getElementById('photo-preview');
    //         const placeholder = document.getElementById('photo-placeholder');
            
    //         if (file) {
    //             const reader = new FileReader();
    //             reader.onload = function(e) {
    //                 preview.src = e.target.result;
    //                 photoPreview.style.display = 'block';
    //                 placeholder.style.display = 'none';
    //             };
    //             reader.readAsDataURL(file);
    //         }
    //     }

    //     document.getElementById('remove-photo').addEventListener('click', function() {
    //         document.getElementById('foto').value = '';
    //         document.getElementById('photo-preview').style.display = 'none';
    //         document.getElementById('photo-placeholder').style.display = 'block';
    //     });
    // </script>
    
    <script>
        class FormProgressTracker {
            constructor() {
                this.sections = [
                    'dataPribadi', 'dataKeluarga', 'pendidikan', 
                    'pendidikanNonFormal', 'pengalamanKerja', 
                    'aktivitas', 'informasiPekerjaan', 'informasiTambahan'
                ];
                this.currentSection = null;
                this.progressData = {};
                this.init();
            }
        
            init() {
                this.setupIntersectionObserver();
                this.setupProgressTracking();
                this.setupSmoothScrolling();
                this.setupInteractiveFeatures();
                this.calculateInitialProgress();
            }
        
            setupIntersectionObserver() {
                const options = {
                    root: null,
                    rootMargin: '-20% 0px -60% 0px',
                    threshold: [0, 0.25, 0.5, 0.75, 1]
                };
        
                this.observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            this.setActiveSection(entry.target.id);
                        }
                    });
                }, options);
        
                this.sections.forEach(sectionId => {
                    const section = document.getElementById(sectionId);
                    if (section) {
                        this.observer.observe(section);
                    }
                });
            }
        
            setupProgressTracking() {
                const form = document.querySelector('form');
                const inputs = form.querySelectorAll('input, textarea, select');
                
                inputs.forEach(input => {
                    ['input', 'change', 'blur'].forEach(eventType => {
                        input.addEventListener(eventType, () => {
                            this.updateProgress();
                        });
                    });
                });
            }
        
            calculateSectionProgress(sectionId) {
                const section = document.getElementById(sectionId);
                if (!section) return 0;
        
                const inputs = section.querySelectorAll('input, textarea, select');
                const totalFields = inputs.length;
                let filledFields = 0;
        
                inputs.forEach(input => {
                    if (this.isFieldFilled(input)) {
                        filledFields++;
                    }
                });
        
                return totalFields > 0 ? Math.round((filledFields / totalFields) * 100) : 0;
            }
        
            isFieldFilled(input) {
                const type = input.type;
                const value = input.value.trim();
        
                if (type === 'radio' || type === 'checkbox') {
                    const name = input.name;
                    const section = input.closest('section');
                    return section ? section.querySelector(`input[name="${name}"]:checked`) !== null : false;
                }
        
                return value !== '';
            }
        
            updateProgress() {
                let totalProgress = 0;
                let sectionCount = 0;
        
                this.sections.forEach(sectionId => {
                    const progress = this.calculateSectionProgress(sectionId);
                    this.progressData[sectionId] = progress;
                    
                    // Update mini progress bar
                    const miniBar = document.querySelector(`[data-section="${sectionId}"] .mini-progress-fill`);
                    const percentage = document.querySelector(`[data-section="${sectionId}"] .progress-percentage`);
                    
                    if (miniBar && percentage) {
                        miniBar.style.width = `${progress}%`;
                        percentage.textContent = `${progress}%`;
                    }
        
                    totalProgress += progress;
                    sectionCount++;
                });
        
                // Update overall progress
                const overallProgress = Math.round(totalProgress / sectionCount);
                const overallBar = document.getElementById('overallProgress');
                const overallText = overallBar.querySelector('.progress-text');
                
                if (overallBar && overallText) {
                    overallBar.style.width = `${overallProgress}%`;
                    overallText.textContent = `${overallProgress}%`;
                }
        
                // Add celebration effect for 100% completion
                if (overallProgress === 100) {
                    this.showCompletionCelebration();
                }
            }
        
            setActiveSection(sectionId) {
                // Remove active class from all nav items
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                });
        
                // Add active class to current section
                const activeNavItem = document.querySelector(`[data-section="${sectionId}"] .nav-link`);
                if (activeNavItem) {
                    activeNavItem.classList.add('active');
                }
        
                this.currentSection = sectionId;
            }
        
            setupSmoothScrolling() {
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        const targetId = link.getAttribute('href').substring(1);
                        const targetElement = document.getElementById(targetId);
                        
                        if (targetElement) {
                            targetElement.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    });
                });
            }
        
            setupInteractiveFeatures() {
                // Add hover effects for sections
                this.sections.forEach(sectionId => {
                    const navItem = document.querySelector(`[data-section="${sectionId}"]`);
                    if (navItem) {
                        navItem.addEventListener('mouseenter', () => {
                            this.highlightSection(sectionId);
                        });
                        
                        navItem.addEventListener('mouseleave', () => {
                            this.removeHighlight(sectionId);
                        });
                    }
                });
            }
        
            highlightSection(sectionId) {
                const section = document.getElementById(sectionId);
                if (section) {
                    section.style.background = 'rgba(110, 170, 54, 0.05)';
                    section.style.transition = 'background 0.3s ease';
                }
            }
        
            removeHighlight(sectionId) {
                const section = document.getElementById(sectionId);
                if (section) {
                    section.style.background = '';
                }
            }
        
            showCompletionCelebration() {
                const navigation = document.querySelector('.form-navigation');
                navigation.classList.add('completion-celebration');
                
                setTimeout(() => {
                    navigation.classList.remove('completion-celebration');
                }, 2000);
            }
        
            calculateInitialProgress() {
                // Calculate progress on page load
                setTimeout(() => {
                    this.updateProgress();
                }, 500);
            }
        }
        
        // Celebration effect CSS
        const celebrationCSS = `
            .completion-celebration {
                animation: celebrationPulse 2s ease-in-out;
            }
            
            @keyframes celebrationPulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); box-shadow: 0 0 30px rgba(255, 206, 0, 0.6); }
            }
        `;
        
        // Add celebration styles
        const styleSheet = document.createElement('style');
        styleSheet.textContent = celebrationCSS;
        document.head.appendChild(styleSheet);
        
        // Initialize the progress tracker
        document.addEventListener('DOMContentLoaded', () => {
            new FormProgressTracker();
        });
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // SCRIPT UNTUK UPLOAD FOTO
            const inputFoto = document.getElementById('foto');
            const previewDiv = document.getElementById('photo-preview');
            const placeholderDiv = document.getElementById('photo-placeholder');
            const previewImg = document.getElementById('preview-image');
            const removeBtn = document.getElementById('remove-photo');
            const fotoAlert = document.getElementById('foto-alert');
        
            inputFoto.addEventListener('change', function(event) {
                const file = event.target.files[0];
                fotoAlert.style.display = 'none';
                
                if (file) {
                    // Validasi ukuran file (maksimal 5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        fotoAlert.textContent = "Ukuran pas foto maksimal 5MB. Silakan pilih file lain.";
                        fotoAlert.style.display = 'block';
                        inputFoto.value = "";
                        previewImg.src = "";
                        previewDiv.style.display = "none";
                        placeholderDiv.style.display = "block";
                        removeBtn.style.display = "none";
                        return;
                    }
                    
                    // Validasi tipe file
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                    if (!allowedTypes.includes(file.type)) {
                        fotoAlert.textContent = "Format file tidak didukung. Gunakan JPG, JPEG, PNG, atau GIF.";
                        fotoAlert.style.display = 'block';
                        inputFoto.value = "";
                        previewImg.src = "";
                        previewDiv.style.display = "none";
                        placeholderDiv.style.display = "block";
                        removeBtn.style.display = "none";
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        previewDiv.style.display = "flex";
                        placeholderDiv.style.display = "none";
                        removeBtn.style.display = "block";
                    }
                    reader.readAsDataURL(file);
                } else {
                    previewImg.src = "";
                    previewDiv.style.display = "none";
                    placeholderDiv.style.display = "block";
                    removeBtn.style.display = "none";
                }
            });
        
            removeBtn.addEventListener('click', function() {
                inputFoto.value = "";
                previewImg.src = "";
                previewDiv.style.display = "none";
                placeholderDiv.style.display = "block";
                removeBtn.style.display = "none";
                fotoAlert.style.display = "none";
            });

            // SCRIPT UNTUK UPLOAD CV
            const inputCV = document.getElementById('cv');
            const cvPreviewDiv = document.getElementById('cv-preview');
            const cvPlaceholderDiv = document.getElementById('cv-placeholder');
            const cvFilename = document.getElementById('cv-filename');
            const removeCVBtn = document.getElementById('remove-cv');
            const cvAlert = document.getElementById('cv-alert');
        
            inputCV.addEventListener('change', function(event) {
                const file = event.target.files[0];
                cvAlert.style.display = 'none';
                
                if (file) {
                    // Validasi ukuran file (maksimal 10MB untuk CV)
                    if (file.size > 10 * 1024 * 1024) {
                        cvAlert.textContent = "Ukuran CV maksimal 10MB. Silakan pilih file lain.";
                        cvAlert.style.display = 'block';
                        inputCV.value = "";
                        cvPreviewDiv.style.display = "none";
                        cvPlaceholderDiv.style.display = "block";
                        removeCVBtn.style.display = "none";
                        return;
                    }
                    
                    // Validasi tipe file
                    const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                    if (!allowedTypes.includes(file.type)) {
                        cvAlert.textContent = "Format file tidak didukung. Gunakan PDF, DOC, atau DOCX.";
                        cvAlert.style.display = 'block';
                        inputCV.value = "";
                        cvPreviewDiv.style.display = "none";
                        cvPlaceholderDiv.style.display = "block";
                        removeCVBtn.style.display = "none";
                        return;
                    }
                    
                    // Tampilkan preview CV
                    cvFilename.textContent = file.name;
                    cvPreviewDiv.style.display = "flex";
                    cvPlaceholderDiv.style.display = "none";
                    removeCVBtn.style.display = "block";
                } else {
                    cvPreviewDiv.style.display = "none";
                    cvPlaceholderDiv.style.display = "block";
                    removeCVBtn.style.display = "none";
                }
            });
        
            removeCVBtn.addEventListener('click', function() {
                inputCV.value = "";
                cvPreviewDiv.style.display = "none";
                cvPlaceholderDiv.style.display = "block";
                removeCVBtn.style.display = "none";
                cvAlert.style.display = "none";
            });
        });
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
        
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                        .then(response => response.json())
                        .then(data => {
                            const address = data.address;
                            // Ambil kota/kabupaten
                            const kota = address.city || address.town || address.village || address.county || '';
                            document.getElementById('asal_daerah').value = kota;
                        })
                        .catch(error => {
                            console.error('Gagal mendapatkan lokasi:', error);
                        });
                }, function (error) {
                    console.warn('Izin lokasi ditolak atau error:', error.message);
                });
            } else {
                console.warn('Geolocation tidak didukung di browser ini.');
            }
        });
    </script>
</body>