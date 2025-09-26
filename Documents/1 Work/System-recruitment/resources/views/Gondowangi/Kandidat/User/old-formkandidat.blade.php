<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Form Data Pelamar</title>
    
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

            <form action="{{ route('kandidat.daftar.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <!-- I. DATA PRIBADI -->
                <section id="dataPribadi">
                    <h2>I. DATA PRIBADI</h2>
                    <table border="1">
                        <tr>
                            <td>1. Posisi yang Dilamar</td>
                            <td>
                                <input type="hidden" name="credentials_id" value="{{ Auth::user()->id }}" required readonly>
                                <input type="hidden" name="asal_daerah" id="asal_daerah" class="form-control" readonly>
                                <select name="posisi_dilamar_id" class="form-control" required>
                                    <option value="">-- Pilih Posisi --</option>
                                    @foreach($availablePositions as $posisi)
                                        <option value="{{ $posisi->id }}">{{ $posisi->position_title }}</option>
                                    @endforeach
                                </select>

                            </td>
                            <td rowspan="12" style="text-align: center; vertical-align: top; padding: 10px; width: 150px;">
                                <div style="border: 2px dashed #ccc; padding: 20px; min-height: 200px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                    <div id="photo-preview" style="display: none;">
                                        <img id="preview-image" src="" alt="Preview Foto" style="max-width: 120px; max-height: 160px; object-fit: cover; border: 1px solid #ddd;">
                                    </div>
                                    <button type="button" id="remove-photo" style="margin-top: 5px; background: #dc3545; color: white; border: none; padding: 2px 8px; font-size: 12px; cursor: pointer;">Hapus</button>
                                    <div id="photo-placeholder">
                                        <p style="margin: 0; font-size: 14px; color: #666;">Pas Foto 3x4</p>
                                        <label for="foto" style="display: inline-block; background: #007bff; color: white; padding: 8px 16px; margin-top: 10px; cursor: pointer; border-radius: 4px; font-size: 12px;">
                                            Pilih Foto
                                        </label>
                                    </div>
                                    <input type="file" id="foto" name="foto" accept="image/*" style="display: none;">
                                    <div id="foto-alert" style="color: red; font-size: 12px; margin-top: 5px; display: none;"></div>
                                </div>
                                @error('foto')
                                    <div style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <td>2. Nama Lengkap</td>
                            <td><input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" placeholder="Nama Lengkap" required></td>
                        </tr>
                        <!--<tr>-->
                        <!--    <td>3. Tempat & Tanggal Lahir</td>-->
                        <!--    <td style="display: flex; gap: 10px;">-->
                        <!--        <input type="text" name="tempat_lahir" placeholder="Tempat Lahir" value="{{ old('tempat_lahir') }}" required >-->
                        <!--        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>-->
                        <!--    </td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>4. Alamat KTP <span style="color: red;">*</span></td>-->
                        <!--    <td><textarea name="alamat_ktp" placeholder="Alamat KTP" required>{{ old('alamat_ktp') }}</textarea></td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>5. Alamat Tempat Tinggal <span style="color: red;">*</span></td>-->
                        <!--    <td><textarea name="alamat_tinggal" placeholder="Alamat tempat tinggal" required>{{ old('alamat_tinggal') }}</textarea></td>-->
                        <!--</tr>-->
                        <tr>
                            <td>3. Kota Domisili</td>
                            <td>
                                <select name="kota_domisili" id="kota_domisili" class="form-select" required>
                                    <option value="">-- Pilih Kota Domisili --</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>6. No Telepon/Handphone</td>
                            <td><input type="text" name="no_telepon" placeholder="No Telpon/Handphone" value="{{ old('no_telepon') }}" required></td>
                        </tr>
                        <tr>
                            <td>7. Email</td>
                            <td><input type="email" name="email" value="{{ old('email') }}" placeholder="Email anda" required></td>
                        </tr>
                        <!--<tr>-->
                        <!--    <td>8. Agama</td>-->
                        <!--    <td><input type="text" name="agama" value="{{ old('agama') }}" placeholder="Agama anda" required></td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>9. Tinggi & Berat Badan</td>-->
                        <!--    <td style="display: flex; gap: 10px;">-->
                        <!--        <input type="number" name="tinggi_badan" placeholder="Tinggi (cm)" step="0.01" value="{{ old('tinggi_badan') }}" required>-->
                        <!--        <input type="number" name="berat_badan" placeholder="Berat (kg)" step="0.01" value="{{ old('berat_badan') }}" required>-->
                        <!--    </td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>10. Status Pernikahan <span style="color: red;">*</span></td>-->
                        <!--    <td style="display: flex; gap: 10px;">-->
                        <!--        <select name="status_pernikahan" required>-->
                        <!--            <option value="">Pilih Status</option>-->
                        <!--            <option value="Belum Nikah" {{ old('status_pernikahan') == 'Belum Nikah' ? 'selected' : '' }}>Belum Nikah</option>-->
                        <!--            <option value="Nikah" {{ old('status_pernikahan') == 'Nikah' ? 'selected' : '' }}>Nikah</option>-->
                        <!--        </select>-->
                        <!--        <input type="text" name="nama_pasangan" placeholder="Nama Pasangan" value="{{ old('nama_pasangan') }}">-->
                        <!--        <input type="number" name="jumlah_anak" placeholder="Jumlah Anak" value="{{ old('jumlah_anak') }}">-->
                        <!--    </td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>11. Penyakit Yang Pernah Dialami <span style="color: red;">*</span></td>-->
                        <!--    <td><textarea name="riwayat_penyakit" placeholder="Ashma/Bronchitis/TBC/Liver/Diabetes/Tekanan darah tinggi/Syaraf/Reumatik/Alergi">{{ old('riwayat_penyakit') }}</textarea></td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>12. Golongan Darah</td>-->
                        <!--    <td>-->
                        <!--        <input type="radio" name="golongan_darah" value="A" {{ old('golongan_darah') == 'A' ? 'checked' : '' }}>A-->
                        <!--        <input type="radio" name="golongan_darah" value="B" {{ old('golongan_darah') == 'B' ? 'checked' : '' }}>B-->
                        <!--        <input type="radio" name="golongan_darah" value="AB" {{ old('golongan_darah') == 'AB' ? 'checked' : '' }}>AB-->
                        <!--        <input type="radio" name="golongan_darah" value="O" {{ old('golongan_darah') == 'O' ? 'checked' : '' }}>O-->
                        <!--    </td>-->
                        <!--</tr>-->
                    </table>
            
                    <!--<h3>13. Media sosial yang aktif digunakan:</h3>-->
                    <!--<table border="1">-->
                    <!--    <tr>-->
                    <!--        <td>a. Facebook:</td>-->
                    <!--        <td><input type="text" name="facebook" placeholder="Akun facebook anda" value="{{ old('facebook') }}"></td>-->
                    <!--        <td>c. LinkedIn:</td>-->
                    <!--        <td><input type="text" name="linkedin" value="{{ old('linkedin') }}" placeholder="Akun linkedin anda"></td>-->
                    <!--        <td>e. Tik-tok:</td>-->
                    <!--        <td><input type="text" name="tiktok" value="{{ old('tiktok') }}" placeholder="Akun tiktok anda"></td>-->
                    <!--    </tr>-->
                    <!--    <tr>-->
                    <!--        <td>b. Twitter:</td>-->
                    <!--        <td><input type="text" name="twitter" value="{{ old('twitter') }}" placeholder="Akun twitter anda "></td>-->
                    <!--        <td>d. Instagram:</td>-->
                    <!--        <td><input type="text" name="instagram" value="{{ old('instagram') }}" placeholder="Akun instagram anda"></td>-->
                    <!--        <td>f. Lain-lain:</td>-->
                    <!--        <td><input type="text" name="medsos_lain" value="{{ old('medsos_lain') }}" placeholder="Contoh: YouTube @channelname"></td>-->
                    <!--    </tr>-->
                    <!--</table>-->
                </section>
        
                <!-- II. DATA KELUARGA -->
                <!--<section id="dataKeluarga">-->
                <!--    <h2>II. DATA KELUARGA</h2>-->
                    
                <!--    <h3>A. Ayah/Ibu</h3>-->
                <!--    <table border="1">-->
                <!--        <tr>-->
                <!--            <th></th>-->
                <!--            <th>Nama</th>-->
                <!--            <th>Pekerjaan</th>-->
                <!--            <th>Pendidikan</th>-->
                <!--            <th>Tanggal Lahir</th>-->
                <!--        </tr>-->
                <!--        <tr>-->
                <!--            <td>Ayah</td>-->
                <!--            <td><input type="text" name="nama_ayah" value="{{ old('nama_ayah') }}" placeholder="Nama Ayah"></td>-->
                <!--            <td><input type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah') }}" placeholder="Pekerjaan Ayah"></td>-->
                <!--            <td><input type="text" name="pendidikan_ayah" value="{{ old('pendidikan_ayah') }}" placeholder="Pendidikan Terakhir Ayah"></td>-->
                <!--            <td><input type="date" name="tanggal_lahir_ayah" value="{{ old('tanggal_lahir_ayah') }}"></td>-->
                <!--        </tr>-->
                <!--        <tr>-->
                <!--            <td>Ibu</td>-->
                <!--            <td><input type="text" name="nama_ibu" value="{{ old('nama_ibu') }}" placeholder="Nama Ibu"></td>-->
                <!--            <td><input type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu') }}" placeholder="Pekerjaan Ibu"></td>-->
                <!--            <td><input type="text" name="pendidikan_ibu" value="{{ old('pendidikan_ibu') }}" placeholder="Pendidikan Terakhir Ibu"></td>-->
                <!--            <td><input type="date" name="tanggal_lahir_ibu" value="{{ old('tanggal_lahir_ibu') }}"></td>-->
                <!--        </tr>-->
                <!--    </table>-->
            
                <!--    <h3>Saudara Kandung</h3>-->
                <!--    <div id="saudara-container">-->
                <!--        <table border="1" id="saudara-table">-->
                <!--            <tr>-->
                <!--                <th>Anak ke</th>-->
                <!--                <th>Nama</th>-->
                <!--                <th>Pekerjaan</th>-->
                <!--                <th>Pendidikan</th>-->
                <!--                <th>Tanggal Lahir</th>-->
                <!--                <th>Ini Saya?</th>-->
                <!--                <th>Aksi</th>-->
                <!--            </tr>-->
                <!--            <tr class="saudara-row" data-index="0">-->
                <!--                <td>1</td>-->
                <!--                <td><input type="text" name="data_saudara[0][nama]" placeholder="Nama"></td>-->
                <!--                <td><input type="text" name="data_saudara[0][pekerjaan]" placeholder="Pekerjaan"></td>-->
                <!--                <td><input type="text" name="data_saudara[0][pendidikan]" placeholder="Pendidikan"></td>-->
                <!--                <td><input type="date" name="data_saudara[0][tanggal_lahir]"></td>-->
                <!--                <td>-->
                <!--                    <button type="button" class="ini-saya-btn" onclick="selectIniSaya(this, 0)">-->
                <!--                        Ini Saya-->
                <!--                    </button>-->
                <!--                    <input type="hidden" name="data_saudara[0][is_me]" value="0">-->
                <!--                </td>-->
                <!--                <td><button type="button" class="remove-btn" onclick="removeSaudara(this)">Hapus</button></td>-->
                <!--            </tr>-->
                <!--        </table>-->
                <!--        <button type="button" onclick="addSaudara()">Tambah Saudara</button>-->
                <!--    </div>-->
            
                <!--    <h3>B. Data Anak</h3>-->
                <!--    <div id="anak-container">-->
                <!--        <table border="1" id="anak-table">-->
                <!--            <tr>-->
                <!--                <th></th>-->
                <!--                <th>Nama</th>-->
                <!--                <th>Pekerjaan</th>-->
                <!--                <th>Pendidikan</th>-->
                <!--                <th>Tanggal Lahir</th>-->
                <!--                <th>Aksi</th>-->
                <!--            </tr>-->
                <!--            <tr class="anak-row">-->
                <!--                <td>Anak ke 1</td>-->
                <!--                <td><input type="text" name="data_anak[0][nama]" value="{{ old('data_anak.0.nama') }}" placeholder="Nama Anak"></td>-->
                <!--                <td><input type="text" name="data_anak[0][pekerjaan]" value="{{ old('data_anak.0.pekerjaan') }}"  placeholder="Pekerjaan Anak"></td>-->
                <!--                <td><input type="text" name="data_anak[0][pendidikan]" value="{{ old('data_anak.0.pendidikan') }}"  placeholder="Pendidikan Terakhir"></td>-->
                <!--                <td><input type="date" name="data_anak[0][tanggal_lahir]" value="{{ old('data_anak.0.tanggal_lahir') }}"></td>-->
                <!--                <td><button type="button" onclick="removeAnak(this)">Hapus</button></td>-->
                <!--            </tr>-->
                <!--        </table>-->
                <!--        <button type="button" onclick="addAnak()">Tambah Anak</button>-->
                <!--    </div>-->
                <!--</section>-->
        
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
                            <tr class="pendidikan-row">
                                <td>
                                    <select name="pendidikan_formal[0][jenjang]">
                                        <option value="">Pilih Jenjang</option>
                                        <option value="SD" {{ old('pendidikan_formal.0.jenjang') == 'SD' ? 'selected' : '' }}>SD</option>
                                        <option value="SMP" {{ old('pendidikan_formal.0.jenjang') == 'SMP' ? 'selected' : '' }}>SMP</option>
                                        <option value="SMU/SMK" {{ old('pendidikan_formal.0.jenjang') == 'SMU/SMK' ? 'selected' : '' }}>SMU/SMK</option>
                                        <option value="Akademi/Diploma" {{ old('pendidikan_formal.0.jenjang') == 'Akademi/Diploma' ? 'selected' : '' }}>Akademi/Diploma</option>
                                        <option value="S1" {{ old('pendidikan_formal.0.jenjang') == 'S1' ? 'selected' : '' }}>S1</option>
                                        <option value="S2" {{ old('pendidikan_formal.0.jenjang') == 'S2' ? 'selected' : '' }}>S2</option>
                                    </select>
                                </td>
                                <td><input type="text" name="pendidikan_formal[0][nama_sekolah]" value="{{ old('pendidikan_formal.0.nama_sekolah') }}"></td>
                                <td><input type="number" name="pendidikan_formal[0][tahun_masuk]" value="{{ old('pendidikan_formal.0.tahun_masuk') }}"></td>
                                <td>
                                    <input type="number" name="pendidikan_formal[0][tahun_keluar]" value="{{ old('pendidikan_formal.0.tahun_keluar') }}" class="tahun-keluar-input">
                                </td>
                                <td><input type="text" name="pendidikan_formal[0][nilai]" value="{{ old('pendidikan_formal.0.nilai') }}"></td>
                                <td>
                                    <label>
                                        <input type="checkbox" name="pendidikan_formal[0][sedang_kuliah]" value="1" onchange="togglePendidikanCurrent(this)" {{ old('pendidikan_formal.0.sedang_kuliah') ? 'checked' : '' }}>
                                        Sampai sekarang
                                    </label>
                                </td>
                                <td><button type="button" onclick="removePendidikan(this)">Hapus</button></td>
                            </tr>
                        </table>
                        <button type="button" onclick="addPendidikan()">Tambah Pendidikan</button>
                    </div>
                </section>
        
                <!-- IV. PENDIDIKAN NON FORMAL -->
                <!--<section id="pendidikanNonFormal">-->
                <!--    <h2>III. PENDIDIKAN NON FORMAL</h2>-->
                <!--    <div id="kursus-container">-->
                <!--        <table border="1" id="kursus-table">-->
                <!--            <tr>-->
                <!--                <th>No</th>-->
                <!--                <th>Nama Kursus/Training</th>-->
                <!--                <th>Lama Pendidikan</th>-->
                <!--                <th>Keterangan</th>-->
                <!--                <th>Aksi</th>-->
                <!--            </tr>-->
                <!--            <tr class="kursus-row">-->
                <!--                <td>1</td>-->
                <!--                <td><input type="text" name="pendidikan_non_formal[0][nama_kursus]" value="{{ old('pendidikan_non_formal.0.nama_kursus') }}" placeholder="Nama Kursus"></td>-->
                <!--                <td><input type="text" name="pendidikan_non_formal[0][lama_pendidikan]" value="{{ old('pendidikan_non_formal.0.lama_pendidikan') }}" placeholder="Lama Menjalani Kursus/Training"></td>-->
                <!--                <td><input type="text" name="pendidikan_non_formal[0][keterangan]" value="{{ old('pendidikan_non_formal.0.keterangan') }}" placeholder="Informasi lainya tentang kursus yang anda ikutin"></td>-->
                <!--                <td><button type="button" onclick="removeKursus(this)">Hapus</button></td>-->
                <!--            </tr>-->
                <!--        </table>-->
                <!--        <button type="button" onclick="addKursus()">Tambah Kursus</button>-->
                <!--    </div>-->
            
                <!--    <h3>Keterampilan Khusus</h3>-->
                <!--    <table border="1">-->
                <!--        <tr>-->
                <!--            <td>a. Bahasa Inggris</td>-->
                <!--            <td>-->
                <!--                <input type="radio" name="bahasa_inggris" value="Kurang" {{ old('bahasa_inggris') == 'Kurang' ? 'checked' : '' }} required> Kurang-->
                <!--                <input type="radio" name="bahasa_inggris" value="Cukup" {{ old('bahasa_inggris') == 'Cukup' ? 'checked' : '' }} required> Cukup-->
                <!--                <input type="radio" name="bahasa_inggris" value="Baik" {{ old('bahasa_inggris') == 'Baik' ? 'checked' : '' }} required> Baik-->
                <!--            </td>-->
                <!--        </tr>-->
                <!--        <tr>-->
                <!--            <td>b. Bahasa asing lainnya</td>-->
                <!--            <td><input type="text" name="bahasa_asing_lain" value="{{ old('bahasa_asing_lain') }}" placeholder="Bahasa Asing lainnya"></td>-->
                <!--        </tr>-->
                <!--        <tr>-->
                <!--            <td>d. Komputer <span style="color: red;">*</span></td>-->
                <!--            <td><textarea name="kemampuan_komputer" placeholder="Ms Office, dll">{{ old('kemampuan_komputer') }}</textarea></td>-->
                <!--        </tr>-->
                <!--        <tr>-->
                <!--            <td>e. Keterampilan Lain</td>-->
                <!--            <td><textarea name="keterampilan_lain" placeholder="Keterampilan lainnya">{{ old('keterampilan_lain') }}</textarea></td>-->
                <!--        </tr>-->
                <!--    </table>-->
                <!--</section>-->
        
                <!-- V. PENGALAMAN KERJA -->
                <section id="pengalamanKerja">
                    <h2>III. PENGALAMAN KERJA (DIMULAI DARI PEKERJAAN TERAKHIR)</h2>
                    <div id="pengalaman-container">
                        <div class="pengalaman-item">
                            <table border="1">
                                <tr>
                                    <td>Nama Perusahaan <span style="color: red;">*</span></td>
                                    <td><input type="text" name="pengalaman_kerja[0][nama_perusahaan]" placeholder="Nama Perusahaan" value="{{ old('pengalaman_kerja.0.nama_perusahaan') }}"></td>
                                    <td>Jabatan <span style="color: red;">*</span></td>
                                    <td><input type="text" name="pengalaman_kerja[0][jabatan]" placeholder="Jabatan Anda" value="{{ old('pengalaman_kerja.0.jabatan') }}"></td>
                                </tr>
                                <tr>
                                    <td>Masa Kerja (Dari) <span style="color: red;">*</span></td>
                                    <td><input type="date" name="pengalaman_kerja[0][masa_kerja_dari]" value="{{ old('pengalaman_kerja.0.masa_kerja_dari') }}"></td>
                                    <td>Masa Kerja (Sampai) <span style="color: red;">*</span></td>
                                    <td>
                                        <input type="date" name="pengalaman_kerja[0][masa_kerja_sampai]" value="{{ old('pengalaman_kerja.0.masa_kerja_sampai') }}" class="masa-kerja-sampai-input">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td colspan="2">
                                        <label>
                                            <input type="checkbox" name="pengalaman_kerja[0][masih_bekerja]" value="1" onchange="togglePekerjaanCurrent(this)" {{ old('pengalaman_kerja.0.masih_bekerja') ? 'checked' : '' }}>
                                            Saat Ini Masih Bekerja Disini
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Uraian Pekerjaan <span style="color: red;">*</span></td>
                                    <td colspan="3"><textarea name="pengalaman_kerja[0][uraian_pekerjaan]" placeholder="Uraian terkait pekerjaan anda" rows="4" cols="50">{{ old('pengalaman_kerja.0.uraian_pekerjaan') }}</textarea></td>
                                </tr>
                                <tr class="alasan-berhenti-row">
                                    <td>Alasan Berhenti <span style="color: red;">*</span></td>
                                    <td colspan="3"><textarea name="pengalaman_kerja[0][alasan_berhenti]" placeholder="Alasan anda berhenti" rows="2" cols="50">{{ old('pengalaman_kerja.0.alasan_berhenti') }}</textarea></td>
                                </tr>
                            </table>
                            <button type="button" onclick="removePengalaman(this)">Hapus Pengalaman</button>
                        </div>
                    </div>
                    <button type="button" onclick="addPengalaman()">Tambah Pengalaman Kerja</button>
                </section>
                
                <!-- Section Struktur Organisasi (Tambahkan setelah section Pengalaman Kerja) -->
                <!--<section id="strukturOrganisasi">-->
                <!--    <h2>VI. STRUKTUR ORGANISASI PERUSAHAAN TERAKHIR</h2>-->
                    
                <!--    <div class="form-section">-->
                <!--    <section id="strukturOrganisasi">-->
                <!--        <h2>VI. STRUKTUR ORGANISASI PERUSAHAAN TERAKHIR</h2>-->
                        
                <!--        <div class="form-group">-->
                <!--            <label for="nama">Nama Lengkap</label>-->
                <!--            <input type="text" id="nama" name="nama" required>-->
                <!--        </div>-->

                <!--        <div class="form-group">-->
                <!--            <label for="jabatan">Jabatan</label>-->
                <!--            <input type="text" id="jabatan" name="jabatan" required>-->
                <!--        </div>-->

                <!--        <div class="form-group">-->
                <!--            <label for="atasan">Atasan Langsung</label>-->
                <!--            <select id="atasan" name="atasan">-->
                <!--                <option value="">-- Pilih Atasan (Kosong jika Top Level) --</option>-->
                <!--            </select>-->
                <!--        </div>-->

                <!--        <div class="checkbox-group">-->
                <!--            <input type="checkbox" id="isCurrentUser" name="isCurrentUser">-->
                <!--            <label for="isCurrentUser">Ini adalah posisi saya</label>-->
                <!--        </div>-->

                <!--        <div style="margin-top: 20px;">-->
                <!--            <button type="button" class="btn" id="addEmployee">Tambah Karyawan</button>-->
                <!--            <button type="button" class="btn btn-danger" id="clearForm">Clear Form</button>-->
                <!--        </div>-->

                        <!-- Employee List -->
                <!--        <div style="margin-top: 30px;">-->
                <!--            <h3>Daftar Karyawan yang Ditambahkan:</h3>-->
                <!--            <div id="employeeList"></div>-->
                <!--        </div>-->

                        <!-- Hidden input for storing organizational data -->
                <!--        <input type="hidden" id="strukturOrganisasiData" name="struktur_organisasi">-->
                <!--    </section>-->

                <!--    <div class="form-actions">-->
                <!--        <button type="submit" class="btn btn-success">Simpan Data</button>-->
                <!--    </div>-->
                <!--</div>-->

                <!-- Preview Section -->
                <!--<div class="preview-section">-->
                <!--    <h2>Preview Struktur Organisasi</h2>-->
                <!--    <div id="orgChart">-->
                <!--        <div class="empty-state">-->
                <!--            Belum ada data struktur organisasi.<br>-->
                <!--            Mulai tambahkan karyawan untuk melihat preview hirarki.-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->
                <!--</section>-->
                <!--<section id="strukturOrganisasi">-->
                <!--    <h2>VI. STRUKTUR ORGANISASI PERUSAHAAN TERAKHIR</h2>-->
                <!--    <p style="color: #666; font-size: 14px; margin-bottom: 20px;">-->
                <!--        Buatlah struktur organisasi perusahaan terakhir tempat Anda bekerja dengan cara drag & drop elemen di bawah ini ke canvas.-->
                <!--    </p>-->
                <!--    <p style="color: #6b360b; font-size: 14px; margin-bottom: 20px;">-->
                <!--        <strong>*Klik 2x pada strukur yang anda buat, untuk mengeditnya.</strong>-->
                <!--    </p>-->
                
                    <!-- Panel Elemen Organisasi -->
                <!--    <div class="org-elements-panel">-->
                <!--        <div class="org-element" draggable="true" data-type="ceo">CEO/Direktur</div>-->
                <!--        <div class="org-element" draggable="true" data-type="manager">Manager</div>-->
                <!--        <div class="org-element" draggable="true" data-type="supervisor">Supervisor</div>-->
                <!--        <div class="org-element" draggable="true" data-type="staff">Staff/Karyawan</div>-->
                <!--        <div class="org-element" draggable="true" data-type="intern">Intern/Magang</div>-->
                <!--    </div>-->
                
                    <!-- Kontrol Canvas -->
                <!--    <div class="org-controls">-->
                <!--        <button type="button" class="control-btn" onclick="clearCanvas()">Bersihkan Canvas</button>-->
                <!--        <button type="button" class="control-btn" onclick="autoAlign()">Rata Tengah</button>-->
                <!--        <button type="button" class="control-btn" onclick="drawConnections()">Tampilkan Garis</button>-->
                        <!--<button type="button" class="control-btn" onclick="exportStructure()">Export JSON</button>-->
                <!--        <button type="button" class="control-btn danger" onclick="deleteSelected()">Hapus Terpilih</button>-->
                        
                        <!-- Zoom Controls -->
                        <!--<div class="zoom-controls">-->
                        <!--    <button type="button" class="control-btn zoom-btn" onclick="zoomOut()">-</button>-->
                        <!--    <span id="zoomLevel">100%</span>-->
                        <!--    <button type="button" class="control-btn zoom-btn" onclick="zoomIn()">+</button>-->
                        <!--    <button type="button" class="control-btn" onclick="resetZoom()">Reset</button>-->
                        <!--</div>-->
                <!--    </div>-->
                
                    <!-- Canvas Struktur Organisasi -->
                <!--    <div class="org-structure-container">-->
                <!--        <div class="canvas-container">-->
                <!--            <div class="org-canvas" id="orgCanvas"></div>-->
                <!--        </div>-->
                        
                        <!-- Panel Info Node -->
                <!--        <div class="node-info-panel" id="nodeInfoPanel">-->
                <!--            <h4 style="margin-bottom: 10px; color: #0E6A39;">Edit Posisi</h4>-->
                <!--            <input type="text" id="nodeTitle" placeholder="Nama Jabatan">-->
                <!--            <input type="text" id="nodeName" placeholder="Nama Orang">-->
                <!--            <button type="button" class="control-btn" onclick="updateNode()">Update</button>-->
                <!--            <button type="button" class="control-btn" onclick="closeNodePanel()">Tutup</button>-->
                <!--        </div>-->
                <!--    </div>-->
                
                    <!-- Output JSON Structure -->
                    <!--<div class="org-json-output">-->
                    <!--    <h4 style="margin-bottom: 10px; color: #0E6A39;">Data Struktur Organisasi:</h4>-->
                    <!--    <textarea id="orgStructureData" name="struktur_organisasi" readonly placeholder="Data struktur organisasi akan muncul di sini..."></textarea>-->
                    <!--</div>-->
                <!--</section>-->
        
                <!-- VI. AKTIVITAS SOSIAL -->
                <!--<section  id="aktivitas">-->
                <!--    <h2>VI. AKTIVITAS SOSIAL DAN KEGIATAN LAIN</h2>-->
                <!--    <p>Bila Anda pernah aktif di suatu organisasi, isilah kolom ini:</p>-->
                <!--    <div id="organisasi-container" style="margin-top: 6px;">-->
                <!--        <table border="1" id="organisasi-table">-->
                <!--            <tr>-->
                <!--                <th>Waktu</th>-->
                <!--                <th>Nama Organisasi</th>-->
                <!--                <th>Bergerak di bidang</th>-->
                <!--                <th>Jabatan</th>-->
                <!--                <th>Aksi</th>-->
                <!--            </tr>-->
                <!--            <tr class="organisasi-row">-->
                <!--                <td><input type="text" name="aktivitas_sosial[0][waktu]" value="{{ old('aktivitas_sosial.0.waktu') }}" placeholder="contoh: Juni 2023 - Maret 2024"></td>-->
                <!--                <td><input type="text" name="aktivitas_sosial[0][nama_organisasi]" value="{{ old('aktivitas_sosial.0.nama_organisasi') }}" placeholder="Nama Organisasi"></td>-->
                <!--                <td><input type="text" name="aktivitas_sosial[0][bidang]" value="{{ old('aktivitas_sosial.0.bidang') }}" placeholder="Bergerak dibidang"></td>-->
                <!--                <td><input type="text" name="aktivitas_sosial[0][jabatan]" value="{{ old('aktivitas_sosial.0.jabatan') }}" placeholder="Jabatan anda"></td>-->
                <!--                <td><button type="button" onclick="removeOrganisasi(this)">Hapus</button></td>-->
                <!--            </tr>-->
                <!--        </table>-->
                <!--        <button type="button" onclick="addOrganisasi()">Tambah Organisasi</button>-->
                <!--    </div>-->
            
                <!--    <h3>Kegiatan Waktu Luang & Hobi</h3>-->
                <!--    <table border="1">-->
                <!--        <tr>-->
                <!--            <td>Kegiatan Waktu Luang <span style="color: red;">*</span></td>-->
                <!--            <td><textarea name="kegiatan_waktu_luang" placeholder="Kegiatan Waktu Luang Anda">{{ old('kegiatan_waktu_luang') }}</textarea></td>-->
                <!--        </tr>-->
                <!--        <tr>-->
                <!--            <td>Hobi <span style="color: red;">*</span></td>-->
                <!--            <td><textarea name="hobi" placeholder="Hobi Anda">{{ old('hobi') }}</textarea></td>-->
                <!--        </tr>-->
                <!--    </table>-->
                <!--</section>-->
        
                <!-- VII. INFORMASI PEKERJAAN -->
                <section  id="informasiPekerjaan" >
                    <h2>IV. INFORMASI PEKERJAAN <br> (Bagian ini bersifat opsional dan dapat diabaikan apabila tidak relevan)</h2>
                    <!--<table border="1">-->
                    <!--    <tr>-->
                    <!--        <h3>a. Sebutkan prestasi/karya luar biasa yang dilakukan selama bekerja</h3>-->
                    <!--        <td><textarea name="prestasi_karya" rows="3" placeholder="Prestasi Anda">{{ old('prestasi_karya') }}</textarea></td>-->
                    <!--    </tr>-->
                    <!--</table>-->
                    
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
            
                    <!--<h3>c. Kartu Identitas/anggota yang dimiliki</h3>-->
                    <!--<table border="1">-->
                    <!--    <tr>-->
                    <!--        <td>1. Surat Izin Mengemudi (SIM) </td>-->
                    <!--        <td>-->
                    <!--            <input type="checkbox" name="sim[]" value="C" {{ in_array('C', old('sim', [])) ? 'checked' : '' }}> C-->
                    <!--            <input type="checkbox" name="sim[]" value="A" {{ in_array('A', old('sim', [])) ? 'checked' : '' }}> A-->
                    <!--            <input type="checkbox" name="sim[]" value="B" {{ in_array('B', old('sim', [])) ? 'checked' : '' }}> B-->
                    <!--            <input type="checkbox" name="sim[]" value="B1" {{ in_array('B1', old('sim', [])) ? 'checked' : '' }}> B1-->
                    <!--            <input type="checkbox" name="sim[]" value="B Umum" {{ in_array('B Umum', old('sim', [])) ? 'checked' : '' }}> B Umum-->
                    <!--        </td>-->
                    <!--    </tr>-->
                    <!--    <tr>-->
                    <!--        <td>2. Kartu BPJS TK (No & Nama Perusahaan)</td>-->
                    <!--        <td><input type="text" name="bpjs_tk" value="{{ old('bpjs_tk') }}" placeholder="Kartu BPJS TK"></td>-->
                    <!--    </tr>-->
                    <!--    <tr>-->
                    <!--        <td>3. Kartu BPJS Kesehatan</td>-->
                    <!--        <td><input type="text" name="bpjs_kesehatan" value="{{ old('bpjs_kesehatan') }}" placeholder="Kartu BPJS Kesehatan"></td>-->
                    <!--    </tr>-->
                    <!--    <tr>-->
                    <!--        <td>4. No. NPWP</td>-->
                    <!--        <td><input type="text" name="npwp" value="{{ old('npwp') }}" placeholder="No NPWP"></td>-->
                    <!--    </tr>-->
                    <!--</table>-->
            
                    <h3>b. Hal-hal yang berhubungan dengan lamaran</h3>
                    <table border="1">
                        <tr>
                            <td>1. Bidang Pekerjaan yang diminati (boleh lebih dari 1) <span style="color: red;">*</span></td>
                            <td><textarea name="bidang_pekerjaan_diminati" placeholder="Bidang Pekerjaan yang Diminati">{{ old('bidang_pekerjaan_diminati') }}</textarea></td>
                        </tr>
                        <tr>
                            <td>2. Jabatan yang diminati <span style="color: red;">*</span></td>
                            <td><input type="text" name="jabatan_diminati" value="{{ old('jabatan_diminati') }}" placeholder="Jabatan yang diminati"></td>
                        </tr>
                        <tr>
                            <td>3. Besar Gaji yang diharapkan (dalam Rp) <span style="color: red;">*</span></td>
                            <td><input type="number" name="gaji_diharapkan" step="0.01" value="{{ old('gaji_diharapkan') }}" placeholder="Gaji yang diharapkan" required></td>
                        </tr>
                        <tr>
                            <td>4. Tunjangan yang diharapkan <span style="color: red;">*</span></td>
                            <td><textarea name="tunjangan_diharapkan" placeholder="Tunjangan yang diharapkan">{{ old('tunjangan_diharapkan') }}</textarea></td>
                        </tr>
                        <tr>
                            <td>5. Fasilitas yang diharapkan <span style="color: red;">*</span></td>
                            <td><textarea name="fasilitas_diharapkan" placeholder="Fasilitas yang diharapkan">{{ old('fasilitas_diharapkan') }}</textarea></td>
                        </tr>
                        <tr>
                            <td>6. Jaminan yang diharapkan <span style="color: red;">*</span></td>
                            <td><textarea name="jaminan_diharapkan" placeholder="Jaminan yang diharapkan">{{ old('jaminan_diharapkan') }}</textarea></td>
                        </tr>
                        <tr>
                            <td>7. Lain-lain yang diharapkan <span style="color: red;">*</span></td>
                            <td><textarea name="lain_diharapkan" placeholder="Lain-lain yang diharapkan">{{ old('lain_diharapkan') }}</textarea></td>
                        </tr>
                    </table>
            
                    <!--<h3>f. Hal-hal mengenai kesediaan</h3>-->
                    <!--<table border="1">-->
                    <!--    <tr>-->
                    <!--        <td>1. Melaksanakan pemeriksaan Kesehatan</td>-->
                    <!--        <td>-->
                    <!--            <input type="radio" name="kesediaan_medical_checkup" value="1" {{ old('kesediaan_medical_checkup') == '1' ? 'checked' : '' }} required> Bersedia-->
                    <!--            <input type="radio" name="kesediaan_medical_checkup" value="0" {{ old('kesediaan_medical_checkup') == '0' ? 'checked' : '' }} required> Tidak Bersedia-->
                    <!--        </td>-->
                    <!--    </tr>-->
                    <!--    <tr>-->
                    <!--        <td>2. Melaksanakan pemeriksaan Psikologi</td>-->
                    <!--        <td>-->
                    <!--            <input type="radio" name="kesediaan_psikologi" value="1" {{ old('kesediaan_psikologi') == '1' ? 'checked' : '' }} required> Bersedia-->
                    <!--            <input type="radio" name="kesediaan_psikologi" value="0" {{ old('kesediaan_psikologi') == '0' ? 'checked' : '' }} required> Tidak Bersedia-->
                    <!--        </td>-->
                    <!--    </tr>-->
                    <!--    <tr>-->
                    <!--        <td>3. Menjalani masa percobaan/evaluasi</td>-->
                    <!--        <td>-->
                    <!--            <input type="radio" name="kesediaan_masa_percobaan" value="1" {{ old('kesediaan_masa_percobaan') == '1' ? 'checked' : '' }} required> Bersedia-->
                    <!--            <input type="radio" name="kesediaan_masa_percobaan" value="0" {{ old('kesediaan_masa_percobaan') == '0' ? 'checked' : '' }} required> Tidak Bersedia-->
                    <!--        </td>-->
                    <!--    </tr>-->
                    <!--    <tr>-->
                    <!--        <td>4. Apakah anda bersedia melakukan perjalanan keluar kota?</td>-->
                    <!--        <td>-->
                    <!--            <input type="radio" name="kesediaan_perjalanan_dinas" value="1" {{ old('kesediaan_perjalanan_dinas') == '1' ? 'checked' : '' }} required> Bersedia-->
                    <!--            <input type="radio" name="kesediaan_perjalanan_dinas" value="0" {{ old('kesediaan_perjalanan_dinas') == '0' ? 'checked' : '' }} required> Tidak Bersedia-->
                    <!--            <br>Bila Bersedia, Maksimum <input type="number" name="maksimum_hari_dinas" value="{{ old('maksimum_hari_dinas') }}"> Hari-->
                    <!--        </td>-->
                    <!--    </tr>-->
                    <!--    <tr>-->
                    <!--        <td>5. Di kota-kota mana sajakah Anda bersedia ditempatkan untuk bekerja (silahkan isi lebih dari 1) <span style="color: red;">*</span></td>-->
                    <!--        <td>-->
                    <!--            <input type="checkbox" name="kesediaan_penempatan[]" value="Pulau Jawa" {{ in_array('Pulau Jawa', old('kesediaan_penempatan', [])) ? 'checked' : '' }}> Pulau Jawa<br>-->
                    <!--            <input type="checkbox" name="kesediaan_penempatan[]" value="Luar Pulau Jawa" {{ in_array('Luar Pulau Jawa', old('kesediaan_penempatan', [])) ? 'checked' : '' }}> Luar Pulau Jawa<br>-->
                    <!--            <input type="checkbox" name="kesediaan_penempatan[]" value="Luar Indonesia" {{ in_array('Luar Indonesia', old('kesediaan_penempatan', [])) ? 'checked' : '' }}> Luar Indonesia-->
                    <!--        </td>-->
                    <!--    </tr>-->
                    <!--    <tr>-->
                    <!--        <td>6. Bila perusahaan membutuhkan, Apakah Anda bersedia dipindahkan keluar kota</td>-->
                    <!--        <td>-->
                    <!--            <input type="radio" name="kesediaan_pindah_kota" value="1" {{ old('kesediaan_pindah_kota') == '1' ? 'checked' : '' }} required> Bersedia-->
                    <!--            <input type="radio" name="kesediaan_pindah_kota" value="0" {{ old('kesediaan_pindah_kota') == '0' ? 'checked' : '' }} required> Tidak Bersedia-->
                    <!--        </td>-->
                    <!--    </tr>-->
                    <!--    <tr>-->
                    <!--        <td>7. Bila Anda diterima, kapan Anda dapat mulai bekerja?</td>-->
                    <!--        <td><input type="date" name="kapan_mulai_kerja" value="{{ old('kapan_mulai_kerja') }}" required></td>-->
                    <!--    </tr>-->
                    <!--</table>-->
                </section>
        
                <!-- VIII. INFORMASI TAMBAHAN -->
                <!--<section id="informasiTambahan">-->
                <!--    <h2>VIII. INFORMASI TAMBAHAN</h2>-->
                    
                <!--    <h3>A. Sebutkan Nama Atasan dan HRD yang Dapat Dihubungi untuk Dijadikan Sebagai Referensi Selain Keluarga (Wajib Diisi)</h3>-->
                <!--    <div id="referensi-container">-->
                <!--        <table border="1" id="referensi-table">-->
                <!--            <tr>-->
                <!--                <th>Nama</th>-->
                <!--                <th>No. Telepon</th>-->
                <!--                <th>Jabatan</th>-->
                <!--                <th>Hubungan</th>-->
                <!--                <th>Aksi</th>-->
                <!--            </tr>-->
                <!--            <tr class="referensi-row">-->
                <!--                <td><input type="text" name="referensi[0][nama]" value="{{ old('referensi.0.nama') }}" ></td>-->
                <!--                <td><input type="text" name="referensi[0][no_telepon]" value="{{ old('referensi.0.no_telepon') }}"></td>-->
                <!--                <td><input type="text" name="referensi[0][jabatan]" value="{{ old('referensi.0.jabatan') }}" ></td>-->
                <!--                <td><input type="text" name="referensi[0][hubungan]" value="{{ old('referensi.0.hubungan') }}"></td>-->
                <!--                <td><button type="button" onclick="removeReferensi(this)">Hapus</button></td>-->
                <!--            </tr>-->
                <!--        </table>-->
                <!--        <button type="button" onclick="addReferensi()">Tambah Referensi</button>-->
                <!--    </div>-->
            
                <!--    <h3>B. Sebutkan Nama Orang yang Dapat Dihubungi Dalam Keadaan Darurat (Wajib Diisi)</h3>-->
                <!--    <div id="darurat-container">-->
                <!--        <table border="1" id="darurat-table">-->
                <!--            <tr>-->
                <!--                <th>Nama</th>-->
                <!--                <th>No. Telepon</th>-->
                <!--                <th>Alamat</th>-->
                <!--                <th>Hubungan</th>-->
                <!--                <th>Aksi</th>-->
                <!--            </tr>-->
                <!--            <tr class="darurat-row">-->
                <!--                <td><input type="text" name="kontak_darurat[0][nama]" value="{{ old('kontak_darurat.0.nama') }}" ></td>-->
                <!--                <td><input type="text" name="kontak_darurat[0][no_telepon]" value="{{ old('kontak_darurat.0.no_telepon') }}" ></td>-->
                <!--                <td><input type="text" name="kontak_darurat[0][alamat]" value="{{ old('kontak_darurat.0.alamat') }}" ></td>-->
                <!--                <td><input type="text" name="kontak_darurat[0][hubungan]" value="{{ old('kontak_darurat.0.hubungan') }}"></td>-->
                <!--                <td><button type="button" onclick="removeKontakDarurat(this)">Hapus</button></td>-->
                <!--            </tr>-->
                <!--        </table>-->
                <!--        <button type="button" onclick="addKontakDarurat()">Tambah Kontak Darurat</button>-->
                <!--    </div>-->
                <!--</section>-->
        
                <!-- IX. INFORMASI/CATATAN LAIN -->
                <section >
                    <h2>V. INFORMASI/CATATAN LAIN YANG INGIN DIKEMUKAKAN</h2>
                    <textarea name="informasi_tambahan" rows="5" cols="80">{{ old('informasi_tambahan') }}</textarea>
            
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
        
        <!-- Sticky Navigation Container -->
        <!--<div class="sticky-nav-container" id="stickyNavContainer"> -->
        <!--    <button class="toggle-nav-btn" id="toggleNavBtn" title="Minimize/Maximize Navigation">⇨</button>-->
        <!--    <div class="form-navigation"  id="formNavigation" style="padding-top: 20px;">-->
        <!--        <h3>Progress Pengisian Form</h3>-->
                
                <!-- Overall Progress Bar -->
        <!--        <div class="overall-progress">-->
        <!--            <div class="progress-label">Progress Keseluruhan</div>-->
        <!--            <div class="progress-bar-container">-->
        <!--                <div class="progress-bar" id="overallProgress">-->
        <!--                    <span class="progress-text">0%</span>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
                
                <!-- Navigation Menu -->
        <!--        <nav class="form-nav-menu">-->
        <!--            <ul class="nav-sections">-->
        <!--                <li class="nav-item" data-section="dataPribadi">-->
        <!--                    <a href="#dataPribadi" class="nav-link">-->
        <!--                        <span class="section-number">I</span>-->
        <!--                        <span class="section-name">Data Pribadi</span>-->
        <!--                        <div class="section-progress">-->
        <!--                            <div class="mini-progress-bar">-->
        <!--                                <div class="mini-progress-fill" data-section="dataPribadi" style="width: 0%;"></div>-->
        <!--                            </div>-->
        <!--                            <span class="progress-percentage">0%</span>-->
        <!--                        </div>-->
        <!--                    </a>-->
        <!--                </li>-->
        <!--                <li class="nav-item" data-section="dataKeluarga">-->
        <!--                    <a href="#dataKeluarga" class="nav-link">-->
        <!--                        <span class="section-number">II</span>-->
        <!--                        <span class="section-name">Data Keluarga</span>-->
        <!--                        <div class="section-progress">-->
        <!--                            <div class="mini-progress-bar">-->
        <!--                                <div class="mini-progress-fill" data-section="dataKeluarga" style="width: 0%;"></div>-->
        <!--                            </div>-->
        <!--                            <span class="progress-percentage">0%</span>-->
        <!--                        </div>-->
        <!--                    </a>-->
        <!--                </li>-->
        <!--                <li class="nav-item" data-section="pendidikan">-->
        <!--                    <a href="#pendidikan" class="nav-link">-->
        <!--                        <span class="section-number">III</span>-->
        <!--                        <span class="section-name">Pendidikan</span>-->
        <!--                        <div class="section-progress">-->
        <!--                            <div class="mini-progress-bar">-->
        <!--                                <div class="mini-progress-fill" data-section="pendidikan" style="width: 0%;"></div>-->
        <!--                            </div>-->
        <!--                            <span class="progress-percentage">0%</span>-->
        <!--                        </div>-->
        <!--                    </a>-->
        <!--                </li>-->
        <!--                <li class="nav-item" data-section="pendidikanNonFormal">-->
        <!--                    <a href="#pendidikanNonFormal" class="nav-link">-->
        <!--                        <span class="section-number">IV</span>-->
        <!--                        <span class="section-name">Pendidikan Non Formal</span>-->
        <!--                        <div class="section-progress">-->
        <!--                            <div class="mini-progress-bar">-->
        <!--                                <div class="mini-progress-fill" data-section="pendidikanNonFormal" style="width: 0%;"></div>-->
        <!--                            </div>-->
        <!--                            <span class="progress-percentage">0%</span>-->
        <!--                        </div>-->
        <!--                    </a>-->
        <!--                </li>-->
        <!--                <li class="nav-item" data-section="pengalamanKerja">-->
        <!--                    <a href="#pengalamanKerja" class="nav-link">-->
        <!--                        <span class="section-number">V</span>-->
        <!--                        <span class="section-name">Pengalaman Kerja</span>-->
        <!--                        <div class="section-progress">-->
        <!--                            <div class="mini-progress-bar">-->
        <!--                                <div class="mini-progress-fill" data-section="pengalamanKerja" style="width: 0%;"></div>-->
        <!--                            </div>-->
        <!--                            <span class="progress-percentage">0%</span>-->
        <!--                        </div>-->
        <!--                    </a>-->
        <!--                </li>-->
        <!--                <li class="nav-item" data-section="aktivitas">-->
        <!--                    <a href="#aktivitas" class="nav-link">-->
        <!--                        <span class="section-number">VI</span>-->
        <!--                        <span class="section-name">Aktivitas</span>-->
        <!--                        <div class="section-progress">-->
        <!--                            <div class="mini-progress-bar">-->
        <!--                                <div class="mini-progress-fill" data-section="aktivitas" style="width: 0%;"></div>-->
        <!--                            </div>-->
        <!--                            <span class="progress-percentage">0%</span>-->
        <!--                        </div>-->
        <!--                    </a>-->
        <!--                </li>-->
        <!--                <li class="nav-item" data-section="informasiPekerjaan">-->
        <!--                    <a href="#informasiPekerjaan" class="nav-link">-->
        <!--                        <span class="section-number">VII</span>-->
        <!--                        <span class="section-name">Informasi Pekerjaan</span>-->
        <!--                        <div class="section-progress">-->
        <!--                            <div class="mini-progress-bar">-->
        <!--                                <div class="mini-progress-fill" data-section="informasiPekerjaan" style="width: 0%;"></div>-->
        <!--                            </div>-->
        <!--                            <span class="progress-percentage">0%</span>-->
        <!--                        </div>-->
        <!--                    </a>-->
        <!--                </li>-->
        <!--                <li class="nav-item" data-section="informasiTambahan">-->
        <!--                    <a href="#informasiTambahan" class="nav-link">-->
        <!--                        <span class="section-number">VIII</span>-->
        <!--                        <span class="section-name">Informasi Tambahan</span>-->
        <!--                        <div class="section-progress">-->
        <!--                            <div class="mini-progress-bar">-->
        <!--                                <div class="mini-progress-fill" data-section="informasiTambahan" style="width: 0%;"></div>-->
        <!--                            </div>-->
        <!--                            <span class="progress-percentage">0%</span>-->
        <!--                        </div>-->
        <!--                    </a>-->
        <!--                </li>-->
        <!--            </ul>-->
        <!--        </nav>-->
        <!--    </div>-->
        <!--</div>-->
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetch('https://www.emsifa.com/api-wilayah-indonesia/api/cities.json')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('kota_domisili');
                    const oldValue = "{{ old('kota_domisili') }}";
                    data.forEach(kota => {
                        const option = document.createElement('option');
                        option.value = kota.name;
                        option.text = kota.name;
                        if (oldValue === kota.name) option.selected = true;
                        select.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Gagal memuat data kota:', error);
                });
        });
        </script>

    
    <script>
        class OrganizationChart {
            constructor() {
                this.employees = [];
                this.currentEditIndex = -1;
                this.init();
            }

            init() {
                this.bindEvents();
                this.updatePreview();
            }

            bindEvents() {
                document.getElementById('addEmployee').addEventListener('click', () => this.addEmployee());
                document.getElementById('clearForm').addEventListener('click', () => this.clearForm());
                document.getElementById('strukturOrganisasiForm').addEventListener('submit', (e) => this.handleSubmit(e));
            }

            addEmployee() {
                const nama = document.getElementById('nama').value.trim();
                const jabatan = document.getElementById('jabatan').value.trim();
                const atasan = document.getElementById('atasan').value;
                const isCurrentUser = document.getElementById('isCurrentUser').checked;

                if (!nama || !jabatan) {
                    alert('Nama dan Jabatan harus diisi!');
                    return;
                }

                // Check if editing existing employee
                if (this.currentEditIndex >= 0) {
                    this.employees[this.currentEditIndex] = {
                        id: this.employees[this.currentEditIndex].id,
                        nama,
                        jabatan,
                        atasan: atasan || null,
                        isCurrentUser
                    };
                    this.currentEditIndex = -1;
                    document.getElementById('addEmployee').textContent = 'Tambah Karyawan';
                } else {
                    // Add new employee
                    const newEmployee = {
                        id: Date.now(),
                        nama,
                        jabatan,
                        atasan: atasan || null,
                        isCurrentUser
                    };
                    this.employees.push(newEmployee);
                }

                // Clear current user flag from other employees if set
                if (isCurrentUser) {
                    this.employees.forEach(emp => {
                        if (emp.id !== (this.currentEditIndex >= 0 ? this.employees[this.currentEditIndex].id : this.employees[this.employees.length - 1].id)) {
                            emp.isCurrentUser = false;
                        }
                    });
                }

                this.clearFormFields();
                this.updateAtasanOptions();
                this.updateEmployeeList();
                this.updatePreview();
                this.updateHiddenInput();
            }

            clearFormFields() {
                document.getElementById('nama').value = '';
                document.getElementById('jabatan').value = '';
                document.getElementById('atasan').value = '';
                document.getElementById('isCurrentUser').checked = false;
            }

            clearForm() {
                this.employees = [];
                this.currentEditIndex = -1;
                this.clearFormFields();
                this.updateAtasanOptions();
                this.updateEmployeeList();
                this.updatePreview();
                this.updateHiddenInput();
                document.getElementById('addEmployee').textContent = 'Tambah Karyawan';
            }

            updateAtasanOptions() {
                const atasanSelect = document.getElementById('atasan');
                atasanSelect.innerHTML = '<option value="">-- Pilih Atasan (Kosong jika Top Level) --</option>';
                
                this.employees.forEach(emp => {
                    const option = document.createElement('option');
                    option.value = emp.id;
                    option.textContent = `${emp.nama} (${emp.jabatan})`;
                    atasanSelect.appendChild(option);
                });
            }

            updateEmployeeList() {
                const listContainer = document.getElementById('employeeList');
                listContainer.innerHTML = '';

                this.employees.forEach((emp, index) => {
                    const item = document.createElement('div');
                    item.className = 'employee-item';
                    
                    const atasanInfo = emp.atasan ? this.employees.find(e => e.id == emp.atasan) : null;
                    const atasanText = atasanInfo ? `Atasan: ${atasanInfo.nama}` : 'Top Level';
                    
                    item.innerHTML = `
                        <div class="employee-info">
                            <div class="employee-name">${emp.nama} ${emp.isCurrentUser ? '(Saya)' : ''}</div>
                            <div class="employee-position">${emp.jabatan} - ${atasanText}</div>
                        </div>
                        <div>
                            <button type="button" class="btn" onclick="orgChart.editEmployee(${index})">Edit</button>
                            <button type="button" class="btn btn-danger" onclick="orgChart.deleteEmployee(${index})">Hapus</button>
                        </div>
                    `;
                    
                    listContainer.appendChild(item);
                });
            }

            editEmployee(index) {
                const emp = this.employees[index];
                document.getElementById('nama').value = emp.nama;
                document.getElementById('jabatan').value = emp.jabatan;
                document.getElementById('atasan').value = emp.atasan || '';
                document.getElementById('isCurrentUser').checked = emp.isCurrentUser;
                
                this.currentEditIndex = index;
                document.getElementById('addEmployee').textContent = 'Update Karyawan';
            }

            deleteEmployee(index) {
                if (confirm('Apakah Anda yakin ingin menghapus karyawan ini?')) {
                    const deletedId = this.employees[index].id;
                    this.employees.splice(index, 1);
                    
                    // Update references to deleted employee
                    this.employees.forEach(emp => {
                        if (emp.atasan == deletedId) {
                            emp.atasan = null;
                        }
                    });
                    
                    this.updateAtasanOptions();
                    this.updateEmployeeList();
                    this.updatePreview();
                    this.updateHiddenInput();
                }
            }

            buildHierarchy() {
                const hierarchy = {};
                const roots = [];

                // Create hierarchy structure
                this.employees.forEach(emp => {
                    if (!emp.atasan) {
                        roots.push(emp);
                    } else {
                        if (!hierarchy[emp.atasan]) {
                            hierarchy[emp.atasan] = [];
                        }
                        hierarchy[emp.atasan].push(emp);
                    }
                });

                return { roots, hierarchy };
            }

            renderNode(employee, hierarchy, level = 0) {
                const children = hierarchy[employee.id] || [];
                const hasChildren = children.length > 0;
                
                let html = `
                    <div class="org-node" data-level="${level}">
                        <div class="org-box ${employee.isCurrentUser ? 'current-user' : ''}">
                            <div class="org-name">${employee.nama}</div>
                            <div class="org-position">${employee.jabatan}</div>
                        </div>
                `;

                if (hasChildren) {
                    html += '<div class="org-line vertical"></div>';
                    html += '<div class="org-children">';
                    
                    children.forEach(child => {
                        html += this.renderNode(child, hierarchy, level + 1);
                    });
                    
                    html += '</div>';
                }

                html += '</div>';
                return html;
            }

            updatePreview() {
                const chartContainer = document.getElementById('orgChart');
                
                if (this.employees.length === 0) {
                    chartContainer.innerHTML = `
                        <div class="empty-state">
                            Belum ada data struktur organisasi.<br>
                            Mulai tambahkan karyawan untuk melihat preview hirarki.
                        </div>
                    `;
                    return;
                }

                const { roots, hierarchy } = this.buildHierarchy();
                
                let html = '<div class="org-chart">';
                
                roots.forEach(root => {
                    html += this.renderNode(root, hierarchy);
                });
                
                html += '</div>';
                
                chartContainer.innerHTML = html;
            }

            updateHiddenInput() {
                document.getElementById('strukturOrganisasiData').value = JSON.stringify(this.employees);
            }

            handleSubmit(e) {
                if (this.employees.length === 0) {
                    alert('Silakan tambahkan minimal satu karyawan dalam struktur organisasi!');
                    e.preventDefault();
                    return false;
                }

                // Check if current user is marked
                const hasCurrentUser = this.employees.some(emp => emp.isCurrentUser);
                if (!hasCurrentUser) {
                    if (!confirm('Anda belum menandai posisi Anda dalam struktur organisasi. Apakah Anda yakin ingin melanjutkan?')) {
                        e.preventDefault();
                        return false;
                    }
                }

                this.updateHiddenInput();
                return true;
            }
        }

        // Initialize organization chart
        const orgChart = new OrganizationChart();
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

    <script>
        function previewPhoto(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('preview-image');
            const photoPreview = document.getElementById('photo-preview');
            const placeholder = document.getElementById('photo-placeholder');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    photoPreview.style.display = 'block';
                    placeholder.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        }

        document.getElementById('remove-photo').addEventListener('click', function() {
            document.getElementById('foto').value = '';
            document.getElementById('photo-preview').style.display = 'none';
            document.getElementById('photo-placeholder').style.display = 'block';
        });
    </script>
    
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
                    if (file.size > 5 * 1024 * 1024) { // 5MB
                        fotoAlert.textContent = "Ukuran pas foto maksimal 5MB. Silakan pilih file lain.";
                        fotoAlert.style.display = 'block';
                        inputFoto.value = ""; // Reset input
                        previewImg.src = "";
                        previewDiv.style.display = "none";
                        placeholderDiv.style.display = "block";
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        previewDiv.style.display = "flex";
                        placeholderDiv.style.display = "none";
                    }
                    reader.readAsDataURL(file);
                } else {
                    previewImg.src = "";
                    previewDiv.style.display = "none";
                    placeholderDiv.style.display = "block";
                }
            });
        
            removeBtn.addEventListener('click', function() {
                inputFoto.value = "";
                previewImg.src = "";
                previewDiv.style.display = "none";
                placeholderDiv.style.display = "block";
                fotoAlert.style.display = "none";
            });
        });
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil data yang tersimpan dari localStorage dan isi form
            fillFormFromLocalStorage();
        
            // Menyimpan data form ke localStorage setiap kali ada perubahan
            const form = document.querySelector('form');
            form.addEventListener('input', function(event) {
                saveFormData();
            });
        
            // Menyimpan data form ke localStorage
            function saveFormData() {
                const formData = {};
                const formElements = form.elements;
        
                // Ambil data dari semua elemen form
                for (let element of formElements) {
                    if (element.name) {
                        formData[element.name] = element.value;
                    }
                }
        
                // Simpan data ke localStorage (format JSON)
                localStorage.setItem('formData', JSON.stringify(formData));
            }
        
            // Mengisi form dengan data yang tersimpan di localStorage
            function fillFormFromLocalStorage() {
                const savedData = localStorage.getItem('formData');
                
                if (savedData) {
                    const formData = JSON.parse(savedData);
        
                    // Isi form dengan data dari localStorage
                    for (let [name, value] of Object.entries(formData)) {
                        const element = form.querySelector(`[name="${name}"]`);
                        if (element) {
                            element.value = value;
                        }
                    }
                }
            }
        
            // Hapus data localStorage setelah form disubmit
            form.addEventListener('submit', function() {
                localStorage.removeItem('formData');
            });
        });
    </script>
    
    <script>
        // Auto-save functionality for form data
        class FormAutoSave {
            constructor() {
                this.storageKey = 'form_kandidat_data';
                this.saveInterval = 2000; // Save every 2 seconds
                this.debounceTimer = null;
                this.init();
            }
        
            init() {
                this.loadSavedData();
                this.attachEventListeners();
                this.startAutoSave();
            }
        
            // Save form data to localStorage
            saveFormData() {
                const formData = {};
                const form = document.querySelector('form');
                
                if (!form) return;
        
                // Get all form elements
                const elements = form.querySelectorAll('input, select, textarea');
                
                elements.forEach(element => {
                    const name = element.name;
                    if (!name) return;
        
                    if (element.type === 'checkbox') {
                        if (name.includes('[]')) {
                            // Handle checkbox arrays
                            if (!formData[name]) formData[name] = [];
                            if (element.checked) {
                                formData[name].push(element.value);
                            }
                        } else {
                            formData[name] = element.checked;
                        }
                    } else if (element.type === 'radio') {
                        if (element.checked) {
                            formData[name] = element.value;
                        }
                    } else if (element.type === 'file') {
                        // Don't save file inputs
                        return;
                    } else {
                        formData[name] = element.value;
                    }
                });
        
                // Save dynamic arrays (saudara, anak, pendidikan, etc.)
                this.saveDynamicArrays(formData);
        
                // Save to localStorage
                localStorage.setItem(this.storageKey, JSON.stringify(formData));
            }
        
            // Save dynamic arrays data
            saveDynamicArrays(formData) {
                const dynamicSections = [
                    'data_saudara',
                    'data_anak', 
                    'pendidikan_formal',
                    'pendidikan_non_formal',
                    'pengalaman_kerja',
                    'aktivitas_sosial',
                    'referensi',
                    'kontak_darurat'
                ];
        
                dynamicSections.forEach(section => {
                    const sectionData = [];
                    let index = 0;
                    
                    while (true) {
                        const hasData = false;
                        const itemData = {};
                        
                        // Check if there are elements for this index
                        const elements = document.querySelectorAll(`[name^="${section}[${index}]"]`);
                        
                        if (elements.length === 0) break;
                        
                        elements.forEach(element => {
                            const fieldName = element.name.match(/\[([^\]]+)\]$/)?.[1];
                            if (fieldName) {
                                if (element.type === 'checkbox') {
                                    itemData[fieldName] = element.checked;
                                } else if (element.type === 'radio') {
                                    if (element.checked) {
                                        itemData[fieldName] = element.value;
                                    }
                                } else {
                                    itemData[fieldName] = element.value;
                                }
                            }
                        });
                        
                        sectionData.push(itemData);
                        index++;
                    }
                    
                    if (sectionData.length > 0) {
                        formData[section] = sectionData;
                    }
                });
            }
        
            // Load saved data and populate form
            loadSavedData() {
                const savedData = localStorage.getItem(this.storageKey);
                if (!savedData) return;
        
                try {
                    const formData = JSON.parse(savedData);
                    this.populateForm(formData);
                } catch (e) {
                    console.error('Error loading saved data:', e);
                    localStorage.removeItem(this.storageKey);
                }
            }
        
            // Populate form with saved data
            populateForm(formData) {
                Object.keys(formData).forEach(name => {
                    const value = formData[name];
                    
                    // Handle dynamic arrays
                    if (Array.isArray(value) && name.includes('data_') || 
                        name === 'pendidikan_formal' || 
                        name === 'pendidikan_non_formal' || 
                        name === 'pengalaman_kerja' || 
                        name === 'aktivitas_sosial' || 
                        name === 'referensi' || 
                        name === 'kontak_darurat') {
                        
                        this.populateDynamicArray(name, value);
                        return;
                    }
                    
                    // Handle checkbox arrays
                    if (name.includes('[]')) {
                        const elements = document.querySelectorAll(`[name="${name}"]`);
                        elements.forEach(element => {
                            element.checked = Array.isArray(value) && value.includes(element.value);
                        });
                        return;
                    }
                    
                    // Handle regular form elements
                    const elements = document.querySelectorAll(`[name="${name}"]`);
                    elements.forEach(element => {
                        if (element.type === 'checkbox') {
                            element.checked = Boolean(value);
                        } else if (element.type === 'radio') {
                            element.checked = element.value === value;
                        } else if (element.type !== 'file') {
                            element.value = value || '';
                        }
                    });
                });
            }
        
            // Populate dynamic array sections
            populateDynamicArray(sectionName, data) {
                if (!Array.isArray(data) || data.length === 0) return;
        
                data.forEach((item, index) => {
                    // Ensure row exists
                    this.ensureRowExists(sectionName, index);
                    
                    // Populate row data
                    Object.keys(item).forEach(fieldName => {
                        const element = document.querySelector(`[name="${sectionName}[${index}][${fieldName}]"]`);
                        if (element) {
                            if (element.type === 'checkbox') {
                                element.checked = Boolean(item[fieldName]);
                            } else if (element.type === 'radio') {
                                element.checked = element.value === item[fieldName];
                            } else if (element.type !== 'file') {
                                element.value = item[fieldName] || '';
                            }
                        }
                    });
                });
            }
        
            // Ensure dynamic row exists
            ensureRowExists(sectionName, index) {
                const existingRow = document.querySelector(`[name="${sectionName}[${index}][nama]"], [name="${sectionName}[${index}][nama_perusahaan]"], [name="${sectionName}[${index}][jenjang]"], [name="${sectionName}[${index}][nama_kursus]"], [name="${sectionName}[${index}][waktu]"]`);
                
                if (!existingRow) {
                    // Add new row based on section type
                    const addButtons = {
                        'data_saudara': () => addSaudara && addSaudara(),
                        'data_anak': () => addAnak && addAnak(),
                        'pendidikan_formal': () => addPendidikan && addPendidikan(),
                        'pendidikan_non_formal': () => addKursus && addKursus(),
                        'pengalaman_kerja': () => addPengalaman && addPengalaman(),
                        'aktivitas_sosial': () => addOrganisasi && addOrganisasi(),
                        'referensi': () => addReferensi && addReferensi(),
                        'kontak_darurat': () => addKontakDarurat && addKontakDarurat()
                    };
        
                    const addFunction = addButtons[sectionName];
                    if (addFunction) {
                        for (let i = 0; i <= index; i++) {
                            const checkRow = document.querySelector(`[name="${sectionName}[${i}][nama]"], [name="${sectionName}[${i}][nama_perusahaan]"], [name="${sectionName}[${i}][jenjang]"], [name="${sectionName}[${i}][nama_kursus]"], [name="${sectionName}[${i}][waktu]"]`);
                            if (!checkRow) {
                                addFunction();
                            }
                        }
                    }
                }
            }
        
            // Attach event listeners for auto-save
            attachEventListeners() {
                const form = document.querySelector('form');
                if (!form) return;
        
                // Listen for input changes
                form.addEventListener('input', (e) => {
                    this.debouncedSave();
                });
        
                form.addEventListener('change', (e) => {
                    this.debouncedSave();
                });
        
                // Save before page unload
                window.addEventListener('beforeunload', () => {
                    this.saveFormData();
                });
        
                // Save on page visibility change
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        this.saveFormData();
                    }
                });
            }
        
            // Debounced save to prevent excessive saving
            debouncedSave() {
                clearTimeout(this.debounceTimer);
                this.debounceTimer = setTimeout(() => {
                    this.saveFormData();
                }, 1000);
            }
        
            // Start periodic auto-save
            startAutoSave() {
                setInterval(() => {
                    this.saveFormData();
                }, this.saveInterval);
            }
        
            // Clear saved data
            clearSavedData() {
                localStorage.removeItem(this.storageKey);
            }
        
            // Check if there's saved data
            hasSavedData() {
                return localStorage.getItem(this.storageKey) !== null;
            }
        }
        
        // Initialize auto-save when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            const autoSave = new FormAutoSave();
            
            // Remove saved data on successful form submission
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Small delay to allow form validation
                    setTimeout(() => {
                        // Check if form submission was successful (no validation errors)
                        const hasErrors = form.querySelector('.error, .alert-danger, [style*="color: red"]');
                        if (!hasErrors) {
                            autoSave.clearSavedData();
                        }
                    }, 100);
                });
            }
        });
        
        // Progress tracking enhancement
        function updateProgress() {
            const sections = [
                'dataPribadi',
                'dataKeluarga', 
                'pendidikan',
                'pendidikanNonFormal',
                'pengalamanKerja',
                'aktivitas',
                'informasiPekerjaan',
                'informasiTambahan'
            ];
        
            let totalProgress = 0;
            
            sections.forEach(sectionId => {
                const section = document.getElementById(sectionId);
                if (!section) return;
        
                const inputs = section.querySelectorAll('input[required], select[required], textarea[required]');
                const checkboxGroups = section.querySelectorAll('input[type="checkbox"][required]');
                const radioGroups = section.querySelectorAll('input[type="radio"][required]');
                
                let filledCount = 0;
                let totalCount = 0;
        
                // Count regular required inputs
                inputs.forEach(input => {
                    if (input.type !== 'checkbox' && input.type !== 'radio') {
                        totalCount++;
                        if (input.value.trim()) filledCount++;
                    }
                });
        
                // Count required checkbox groups
                const checkboxNames = [...new Set(Array.from(checkboxGroups).map(cb => cb.name))];
                checkboxNames.forEach(name => {
                    totalCount++;
                    const checkedBoxes = section.querySelectorAll(`input[name="${name}"]:checked`);
                    if (checkedBoxes.length > 0) filledCount++;
                });
        
                // Count required radio groups
                const radioNames = [...new Set(Array.from(radioGroups).map(radio => radio.name))];
                radioNames.forEach(name => {
                    totalCount++;
                    const checkedRadio = section.querySelector(`input[name="${name}"]:checked`);
                    if (checkedRadio) filledCount++;
                });
        
                const sectionProgress = totalCount > 0 ? (filledCount / totalCount) * 100 : 0;
                totalProgress += sectionProgress;
        
                // Update section progress bar
                const progressFill = document.querySelector(`[data-section="${sectionId}"]`);
                const progressText = progressFill?.parentElement?.nextElementSibling;
                
                if (progressFill) {
                    progressFill.style.width = `${sectionProgress}%`;
                }
                if (progressText) {
                    progressText.textContent = `${Math.round(sectionProgress)}%`;
                }
            });
        
            // Update overall progress
            const overallProgress = totalProgress / sections.length;
            const overallProgressBar = document.getElementById('overallProgress');
            const overallProgressText = overallProgressBar?.querySelector('.progress-text');
            
            if (overallProgressBar) {
                overallProgressBar.style.width = `${overallProgress}%`;
            }
            if (overallProgressText) {
                overallProgressText.textContent = `${Math.round(overallProgress)}%`;
            }
        }
        
        // Update progress on input change
        document.addEventListener('input', updateProgress);
        document.addEventListener('change', updateProgress);
        
        // Initial progress update
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(updateProgress, 500); // Small delay to ensure saved data is loaded
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