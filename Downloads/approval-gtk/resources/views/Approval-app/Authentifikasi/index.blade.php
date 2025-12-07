<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sistem Approval</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0e6a39;
            --primary-dark: #0a5229;
            --primary-light: #12803f;
            --secondary-color: #f8f9fa;
            --text-dark: #2c3e50;
            --shadow: rgba(14, 106, 57, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            cursor: auto !important;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background Elements */
        .bg-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            cursor: grab;
            user-select: none;
            transition: box-shadow 0.3s ease, background 0.3s ease;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .shape:hover {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.2);
            cursor: grab;
        }

        .shape.dragging {
            cursor: grabbing;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 40px rgba(255, 255, 255, 0.3);
            z-index: 10;
            scale: 1.05;
        }

        .shape.colliding {
            background: rgba(18, 128, 63, 0.3);
            box-shadow: 0 0 20px var(--primary-color);
            animation: collision-pulse 0.3s ease-out;
        }

        @keyframes collision-pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
        }

        .shape:nth-child(4) {
            width: 100px;
            height: 100px;
        }

        .shape:nth-child(5) {
            width: 90px;
            height: 90px;
        }

        .shape:nth-child(6) {
            width: 70px;
            height: 70px;
        }

        /* Login Container */
        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px var(--shadow);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transform: translateY(20px);
            opacity: 0;
            animation: slideUp 0.8s ease-out forwards;
        }

        @keyframes slideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-icon {
            width: 80px;
            height: 80px;
            background: var(--primary-color);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            box-shadow: 0 10px 30px var(--shadow);
            transform: scale(0);
            animation: bounceIn 0.6s ease-out 0.3s forwards;
        }

        @keyframes bounceIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .login-title {
            color: var(--text-dark);
            font-weight: 600;
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .login-subtitle {
            color: #6c757d;
            font-weight: 400;
            font-size: 0.9rem;
        }

        /* Form Styling */
        .form-floating {
            margin-bottom: 20px;
            position: relative;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1rem 1rem 1rem 3rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(14, 106, 57, 0.25);
            background: white;
            transform: translateY(-2px);
        }

        .form-floating label {
            left: 0rem;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 1.1rem;
            z-index: 5;
            transition: all 0.3s ease;
        }

        .form-floating:focus-within .input-icon {
            color: var(--primary-color);
            transform: translateY(-50%) scale(1.1);
        }

        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            font-size: 1.1rem;
            z-index: 5;
            transition: all 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--primary-color);
            transform: translateY(-50%) scale(1.1);
        }

        /* Remember Me */
        .form-check {
            margin-bottom: 25px;
        }

        .form-check-input {
            border-radius: 6px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(14, 106, 57, 0.25);
        }

        .form-check-label {
            color: var(--text-dark);
            font-weight: 500;
            cursor: pointer;
        }

        /* Login Button */
        .btn-login {
            width: 100%;
            padding: 1rem;
            font-size: 1.1rem;
            font-weight: 600;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            border: none;
            border-radius: 12px;
            color: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px var(--shadow);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Loading Animation */
        .btn-login.loading {
            pointer-events: none;
        }

        .btn-login.loading .btn-text {
            opacity: 0;
        }

        .btn-login .spinner {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .btn-login.loading .spinner {
            display: block;
        }

        /* Error Messages */
        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        /* Alert Styling */
        .alert {
            border-radius: 12px;
            border: none;
            margin-bottom: 20px;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        /* Custom Cursor */
        * {
            cursor: none;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .login-card {
                padding: 30px 20px;
                margin: 20px;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
        }

        /* Smooth Entrance Animation */
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Forgot Password Styles */
        .forgot-password-section {
            display: none;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        
        .forgot-password-section.active {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 25px;
            gap: 10px;
        }
        
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            backdrop-filter: blur(10px);
        }
        
        .step i {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.6);
            transition: all 0.3s ease;
        }
        
        .step.active {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border-color: var(--primary-color);
            transform: scale(1.15);
            box-shadow: 0 0 25px rgba(14, 106, 57, 0.6);
        }
        
        .step.active i {
            color: white;
            transform: scale(1.1);
        }
        
        .step.completed {
            background: linear-gradient(135deg, var(--success-color), #26de81);
            border-color: var(--success-color);
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(26, 222, 129, 0.4);
        }
        
        .step.completed i {
            color: white;
        }
        
        .step:hover:not(.completed):not(.active) {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            transform: scale(1.05);
        }
        
        .step:hover:not(.completed):not(.active) i {
            color: rgba(255, 255, 255, 0.8);
        }
        
        .step-content {
            display: none;
            animation: slideInUp 0.5s ease-out;
        }
        
        .step-content.active {
            display: block;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .verification-input {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin: 20px 0;
        }
        
        .verification-digit {
            width: 50px;
            height: 50px;
            text-align: center;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 20px;
            font-weight: bold;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .verification-digit:focus {
            border-color: var(--primary-color);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 20px rgba(14, 106, 57, 0.3);
            transform: scale(1.05);
        }
        
        .password-strength {
            margin: 15px 0;
            padding: 10px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        
        .strength-bar {
            height: 6px;
            border-radius: 3px;
            background: rgba(255, 255, 255, 0.2);
            margin: 10px 0;
            overflow: hidden;
        }
        
        .strength-progress {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 3px;
        }
        
        .strength-weak { width: 25%; background: #ff4757; }
        .strength-fair { width: 50%; background: #ffa502; }
        .strength-good { width: 75%; background: #26de81; }
        .strength-strong { width: 100%; background: #0ea639; }
        
        .password-requirements {
            list-style: none;
            padding: 0;
            font-size: 12px;
        }
        
        .password-requirements li {
            padding: 2px 0;
            transition: all 0.3s ease;
            opacity: 0.6;
        }
        
        .password-requirements li.valid {
            color: var(--success-color);
            opacity: 1;
        }
        
        .password-requirements li.valid::before {
            content: '✓ ';
            font-weight: bold;
        }
        
        .password-requirements li.invalid::before {
            content: '✗ ';
            color: #ff4757;
        }
        
        .back-to-login {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .back-to-login:hover {
            color: white;
            transform: translateX(-3px);
        }
        
        .success-animation {
            text-align: center;
            padding: 20px;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--success-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: successPulse 1s ease-out;
        }
        
        @keyframes successPulse {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        .floating-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }
        
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 50%;
            animation: floatUp 3s linear infinite;
            opacity: 0;
        }
        
        @keyframes floatUp {
            0% { opacity: 0; transform: translateY(100px) rotate(0deg); }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { opacity: 0; transform: translateY(-100px) rotate(360deg); }
        }
        
        .forgot-link {
            text-align: center;
            margin-top: 15px;
        }
        
        .forgot-link a {
            font-size: 14px;
            transition: all 0.3s ease;
            position: relative;
            color: #96a49c;
            text-decoration: none;
        }
        
        .forgot-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1px;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }
        
        .forgot-link a:hover {
            color: var(--primary-color);
        }
        
        .forgot-link a:hover::after {
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    
    <!-- Card Data Autentifikasi -->
    <div class="auth-info-card mb-4" style="position: relative; z-index: 9;">
        <div class="card shadow-sm">
            <div class="card-header" style="background-color: #117b3d;">
                <h5 class="card-title mb-0 text-white p-2">
                    <i class="fas fa-id-card me-2"></i>
                    Data Autentifikasi (dihapus nanti)
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-user text-primary"></i>
                            </div>
                            <div>
                                <small class="text-muted">Nama</small>
                                <div class="fw-bold">Nina Kartika</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-briefcase text-success"></i>
                            </div>
                            <div>
                                <small class="text-muted">Jabatan</small>
                                <div class="fw-bold">Sales Staff</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-id-badge text-warning"></i>
                            </div>
                            <div>
                                <small class="text-muted">NIK</small>
                                <div class="fw-bold">789780</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-user text-primary"></i>
                            </div>
                            <div>
                                <small class="text-muted">Nama</small>
                                <div class="fw-bold">Agus Setiawan</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-briefcase text-success"></i>
                            </div>
                            <div>
                                <small class="text-muted">Jabatan</small>
                                <div class="fw-bold">Sales Supervisor</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-id-badge text-warning"></i>
                            </div>
                            <div>
                                <small class="text-muted">NIK</small>
                                <div class="fw-bold">008908</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-user text-primary"></i>
                            </div>
                            <div>
                                <small class="text-muted">Nama</small>
                                <div class="fw-bold">Lisa Permata</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-briefcase text-success"></i>
                            </div>
                            <div>
                                <small class="text-muted">Jabatan</small>
                                <div class="fw-bold">Manager Sales</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-id-badge text-warning"></i>
                            </div>
                            <div>
                                <small class="text-muted">NIK</small>
                                <div class="fw-bold">898989</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-user text-primary"></i>
                            </div>
                            <div>
                                <small class="text-muted">Nama</small>
                                <div class="fw-bold">Maya Sari</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-briefcase text-success"></i>
                            </div>
                            <div>
                                <small class="text-muted">Jabatan</small>
                                <div class="fw-bold">Finance Staff</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-id-badge text-warning"></i>
                            </div>
                            <div>
                                <small class="text-muted">NIK</small>
                                <div class="fw-bold">108900</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-user text-primary"></i>
                            </div>
                            <div>
                                <small class="text-muted">Nama</small>
                                <div class="fw-bold">Tono Susanto</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-briefcase text-success"></i>
                            </div>
                            <div>
                                <small class="text-muted">Jabatan</small>
                                <div class="fw-bold">Finance Supervisor</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-id-badge text-warning"></i>
                            </div>
                            <div>
                                <small class="text-muted">NIK</small>
                                <div class="fw-bold">001101</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-user text-primary"></i>
                            </div>
                            <div>
                                <small class="text-muted">Nama</small>
                                <div class="fw-bold">Siti Nurhaliza</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-briefcase text-success"></i>
                            </div>
                            <div>
                                <small class="text-muted">Jabatan</small>
                                <div class="fw-bold">Finance Manager</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-id-badge text-warning"></i>
                            </div>
                            <div>
                                <small class="text-muted">NIK</small>
                                <div class="fw-bold">890236</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-user text-primary"></i>
                            </div>
                            <div>
                                <small class="text-muted">Nama</small>
                                <div class="fw-bold">Jokowi</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-briefcase text-success"></i>
                            </div>
                            <div>
                                <small class="text-muted">Jabatan</small>
                                <div class="fw-bold">Direktur</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="auth-icon me-3">
                                <i class="fas fa-id-badge text-warning"></i>
                            </div>
                            <div>
                                <small class="text-muted">NIK</small>
                                <div class="fw-bold">899999</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Unutuk kata sandi, Anda bisa memasukan kata sandi default yaitu gondowangi-123.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Container -->
    <div class="login-container">
        <div class="login-card">
            <!-- Login Header -->
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h2 class="login-title">Sistem Approval</h2>
                <p class="login-subtitle">Masuk dengan NIK dan kata sandi Anda</p>
            </div>

            <!-- Error Alert -->
            @if($errors->any())
                <div class="alert alert-danger fade-in" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login') }}" method="POST" id="loginForm">
                @csrf
                
                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <!-- NIK Input -->
                <div class="form-floating">
                    <input 
                        type="text" 
                        class="form-control @error('nik') is-invalid @enderror" 
                        id="nik" 
                        name="nik" 
                        placeholder="Nomor Induk Kependudukan"
                        value="{{ old('nik') }}"
                        required
                        maxlength="6"
                        pattern="[0-9]{6}"
                        autocomplete="username"
                    >
                    <label for="nik">Nomor Induk Karyawan (NIK)</label>
                    @error('nik')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            
                <!-- Password Input -->
                <div class="form-floating position-relative">
                    <input 
                        type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        id="password" 
                        name="password" 
                        placeholder="Kata Sandi"
                        required
                        autocomplete="current-password"
                    >
                    <label for="password">Kata Sandi</label>
                    <button type="button" class="password-toggle" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            
                <!-- Remember Me -->
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        Ingat saya
                    </label>
                </div>
            
                <!-- Login Button -->
                <button type="submit" class="btn btn-login" id="loginBtn">
                    <span class="btn-text">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Masuk
                    </span>
                    <div class="spinner d-none">
                        <div class="spinner-border spinner-border-sm text-white" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </button>
                
                <div class="forgot-link">
                    <a href="#" id="forgotPasswordLink">
                        <i class="fas fa-key me-1"></i>
                        Lupa kata sandi?
                    </a>
                </div>
            </form>
            
            <!-- Section Forgot Password (tambahkan setelah form login) -->
            <div class="forgot-password-section" id="forgotPasswordSection">
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step active" data-step="1">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="step" data-step="2">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="step" data-step="3">
                        <i class="fas fa-key"></i>
                    </div>
                    <div class="step" data-step="4">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            
                <!-- Step 1: Enter NIK -->
                <div class="step-content active" id="step1">
                    <div class="login-header">
                        <h3>Verifikasi NIK</h3>
                        <p>Masukkan NIK Anda untuk reset kata sandi</p>
                    </div>
                    
                    <div class="form-floating">
                        <input type="text" class="form-control" id="forgotNik" placeholder="NIK" maxlength="16" pattern="[0-9]{16}">
                        <label for="forgotNik">Nomor Induk Karyawan (NIK)</label>
                    </div>
                    
                    <button type="button" class="btn btn-login" id="verifyNikBtn">
                        <span class="btn-text">
                            <i class="fas fa-arrow-right me-2"></i>
                            Verifikasi NIK
                        </span>
                        <div class="spinner">
                            <div class="spinner-border spinner-border-sm text-white" role="status"></div>
                        </div>
                    </button>
                    
                    <div class="text-center mt-3">
                        <a href="#" class="back-to-login" id="backToLoginFromStep1">
                            <i class="fas fa-arrow-left"></i>
                            Kembali ke login
                        </a>
                    </div>
                </div>
            
                <!-- Step 2: Verification Code -->
                <div class="step-content" id="step2">
                    <div class="login-header">
                        <h3>Kode Verifikasi</h3>
                        <p>Masukkan kode 6 digit yang dikirim ke email Anda</p>
                    </div>
                    
                    <div class="verification-input">
                        <input type="text" class="verification-digit" maxlength="1" data-index="0">
                        <input type="text" class="verification-digit" maxlength="1" data-index="1">
                        <input type="text" class="verification-digit" maxlength="1" data-index="2">
                        <input type="text" class="verification-digit" maxlength="1" data-index="3">
                        <input type="text" class="verification-digit" maxlength="1" data-index="4">
                        <input type="text" class="verification-digit" maxlength="1" data-index="5">
                    </div>
                    
                    <button type="button" class="btn btn-login" id="verifyCodeBtn">
                        <span class="btn-text">
                            <i class="fas fa-check me-2"></i>
                            Verifikasi Kode
                        </span>
                        <div class="spinner">
                            <div class="spinner-border spinner-border-sm text-white" role="status"></div>
                        </div>
                    </button>
                    
                    <div class="text-center mt-3">
                        <a href="#" class="back-to-login" id="resendCode">
                            <i class="fas fa-redo"></i>
                            Kirim ulang kode
                        </a>
                    </div>
                </div>
            
                <!-- Step 3: New Password -->
                <div class="step-content" id="step3">
                    <div class="login-header">
                        <h3>Kata Sandi Baru</h3>
                        <p>Buat kata sandi baru yang kuat dan aman</p>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="newPassword" placeholder="Kata Sandi Baru">
                        <label for="newPassword">Kata Sandi Baru</label>
                        <button type="button" class="password-toggle" onclick="togglePasswordVisibility('newPassword', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="confirmPassword" placeholder="Konfirmasi Kata Sandi">
                        <label for="confirmPassword">Konfirmasi Kata Sandi</label>
                        <button type="button" class="password-toggle" onclick="togglePasswordVisibility('confirmPassword', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    
                    <div class="password-strength">
                        <div class="strength-bar">
                            <div class="strength-progress" id="strengthProgress"></div>
                        </div>
                        <ul class="password-requirements" id="passwordRequirements">
                            <li class="invalid">Minimal 8 karakter</li>
                            <li class="invalid">Mengandung huruf besar</li>
                            <li class="invalid">Mengandung huruf kecil</li>
                            <li class="invalid">Mengandung angka</li>
                            <li class="invalid">Mengandung simbol</li>
                        </ul>
                    </div>
                    
                    <button type="button" class="btn btn-login" id="updatePasswordBtn" disabled="">
                        <span class="btn-text">
                            <i class="fas fa-save me-2"></i>
                            Perbarui Kata Sandi
                        </span>
                        <div class="spinner">
                            <div class="spinner-border spinner-border-sm text-white" role="status"></div>
                        </div>
                    </button>
                </div>
            
                <!-- Step 4: Success -->
                <div class="step-content" id="step4">
                    <div class="success-animation">
                        <div class="success-icon">
                            <i class="fas fa-check" style="font-size: 30px; color: white;"></i>
                        </div>
                        <h3>Berhasil!</h3>
                        <p>Kata sandi Anda telah berhasil diperbarui</p>
                    </div>
                    
                    <button type="button" class="btn btn-login" id="backToLoginBtn">
                        <span class="btn-text">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Masuk Sekarang
                        </span>
                    </button>
                    
                    <div class="floating-particles" id="successParticles"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Physics Engine for Interactive Shapes
            class PhysicsShape {
                constructor(element, index) {
                    this.element = element;
                    this.index = index;
                    this.width = parseFloat(getComputedStyle(element).width);
                    this.height = parseFloat(getComputedStyle(element).height);
                    this.radius = this.width / 2;
                    
                    // Position
                    this.x = Math.random() * (window.innerWidth - this.width);
                    this.y = Math.random() * (window.innerHeight - this.height);
                    
                    // Velocity
                    this.vx = (Math.random() - 0.5) * 2;
                    this.vy = (Math.random() - 0.5) * 2;
                    
                    // Physics properties
                    this.mass = this.radius / 10;
                    this.friction = 0.99;
                    this.bounce = 0.8;
                    
                    // Drag properties
                    this.isDragging = false;
                    this.dragOffsetX = 0;
                    this.dragOffsetY = 0;
                    this.lastMouseX = 0;
                    this.lastMouseY = 0;
                    this.mouseVelocityX = 0;
                    this.mouseVelocityY = 0;
                    
                    this.updatePosition();
                    this.setupEvents();
                }
                
                setupEvents() {
                    // Mouse/Touch events
                    this.element.addEventListener('mousedown', (e) => this.startDrag(e));
                    this.element.addEventListener('touchstart', (e) => this.startDrag(e.touches[0]));
                    
                    document.addEventListener('mousemove', (e) => this.drag(e));
                    document.addEventListener('touchmove', (e) => this.drag(e.touches[0]));
                    
                    document.addEventListener('mouseup', () => this.endDrag());
                    document.addEventListener('touchend', () => this.endDrag());
                }
                
                startDrag(e) {
                    this.isDragging = true;
                    this.element.classList.add('dragging');
                    
                    const rect = this.element.getBoundingClientRect();
                    this.dragOffsetX = e.clientX - rect.left - this.radius;
                    this.dragOffsetY = e.clientY - rect.top - this.radius;
                    
                    this.lastMouseX = e.clientX;
                    this.lastMouseY = e.clientY;
                    
                    // Stop current velocity when grabbed
                    this.vx = 0;
                    this.vy = 0;
                }
                
                drag(e) {
                    if (!this.isDragging) return;
                    
                    // Calculate mouse velocity for throwing
                    this.mouseVelocityX = e.clientX - this.lastMouseX;
                    this.mouseVelocityY = e.clientY - this.lastMouseY;
                    
                    this.lastMouseX = e.clientX;
                    this.lastMouseY = e.clientY;
                    
                    // Update position
                    this.x = e.clientX - this.dragOffsetX - this.radius;
                    this.y = e.clientY - this.dragOffsetY - this.radius;
                    
                    this.updatePosition();
                }
                
                endDrag() {
                    if (!this.isDragging) return;
                    
                    this.isDragging = false;
                    this.element.classList.remove('dragging');
                    
                    // Apply throw velocity
                    this.vx = this.mouseVelocityX * 0.3;
                    this.vy = this.mouseVelocityY * 0.3;
                }
                
                update() {
                    if (this.isDragging) return;
                    
                    // Apply friction
                    this.vx *= this.friction;
                    this.vy *= this.friction;
                    
                    // Update position
                    this.x += this.vx;
                    this.y += this.vy;
                    
                    // Boundary collision
                    if (this.x <= 0) {
                        this.x = 0;
                        this.vx *= -this.bounce;
                    }
                    if (this.x >= window.innerWidth - this.width) {
                        this.x = window.innerWidth - this.width;
                        this.vx *= -this.bounce;
                    }
                    if (this.y <= 0) {
                        this.y = 0;
                        this.vy *= -this.bounce;
                    }
                    if (this.y >= window.innerHeight - this.height) {
                        this.y = window.innerHeight - this.height;
                        this.vy *= -this.bounce;
                    }
                    
                    this.updatePosition();
                }
                
                updatePosition() {
                    this.element.style.left = this.x + 'px';
                    this.element.style.top = this.y + 'px';
                }
                
                // Collision detection and response
                checkCollision(other) {
                    const dx = (this.x + this.radius) - (other.x + other.radius);
                    const dy = (this.y + this.radius) - (other.y + other.radius);
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    const minDistance = this.radius + other.radius;
                    
                    if (distance < minDistance) {
                        // Collision detected
                        this.handleCollision(other, dx, dy, distance, minDistance);
                        return true;
                    }
                    return false;
                }
                
                handleCollision(other, dx, dy, distance, minDistance) {
                    // Add collision visual effect
                    this.element.classList.add('colliding');
                    other.element.classList.add('colliding');
                    
                    setTimeout(() => {
                        this.element.classList.remove('colliding');
                        other.element.classList.remove('colliding');
                    }, 300);
                    
                    // Separate overlapping circles
                    const overlap = minDistance - distance;
                    const separationX = (dx / distance) * overlap * 0.5;
                    const separationY = (dy / distance) * overlap * 0.5;
                    
                    this.x += separationX;
                    this.y += separationY;
                    other.x -= separationX;
                    other.y -= separationY;
                    
                    // Calculate collision response
                    const normalX = dx / distance;
                    const normalY = dy / distance;
                    
                    // Relative velocity
                    const relativeVelocityX = this.vx - other.vx;
                    const relativeVelocityY = this.vy - other.vy;
                    const velocityAlongNormal = relativeVelocityX * normalX + relativeVelocityY * normalY;
                    
                    // Don't resolve if velocities are separating
                    if (velocityAlongNormal > 0) return;
                    
                    // Collision impulse
                    const restitution = Math.min(this.bounce, other.bounce);
                    const impulse = -(1 + restitution) * velocityAlongNormal / (1/this.mass + 1/other.mass);
                    
                    // Apply impulse
                    const impulseX = impulse * normalX;
                    const impulseY = impulse * normalY;
                    
                    this.vx += impulseX / this.mass;
                    this.vy += impulseY / this.mass;
                    other.vx -= impulseX / other.mass;
                    other.vy -= impulseY / other.mass;
                }
            }
            
            // Initialize physics shapes
            const shapes = document.querySelectorAll('.shape');
            const physicsShapes = [];
            
            shapes.forEach((shape, index) => {
                physicsShapes.push(new PhysicsShape(shape, index));
            });
            
            // Physics animation loop
            function animate() {
                // Update each shape
                physicsShapes.forEach(shape => {
                    shape.update();
                });
                
                // Check collisions between all shapes
                for (let i = 0; i < physicsShapes.length; i++) {
                    for (let j = i + 1; j < physicsShapes.length; j++) {
                        physicsShapes[i].checkCollision(physicsShapes[j]);
                    }
                }
                
                requestAnimationFrame(animate);
            }
            animate();
            
            // Handle window resize
            window.addEventListener('resize', () => {
                physicsShapes.forEach(shape => {
                    // Keep shapes within new bounds
                    if (shape.x > window.innerWidth - shape.width) {
                        shape.x = window.innerWidth - shape.width;
                    }
                    if (shape.y > window.innerHeight - shape.height) {
                        shape.y = window.innerHeight - shape.height;
                    }
                });
            });

            // Password Toggle
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            
           // Toggle password visibility
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                const icon = this.querySelector('i');
                if (type === 'password') {
                    icon.className = 'fas fa-eye';
                } else {
                    icon.className = 'fas fa-eye-slash';
                }
            });

            // NIK Input Validation (Only Numbers)
            const nikInput = document.getElementById('nik');
            nikInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 16) {
                    this.value = this.value.slice(0, 16);
                }
            });

            // Form Submit Animation
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            
            loginForm.addEventListener('submit', function() {
                loginBtn.classList.add('loading');
            });

            // Input Focus Animation
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                    this.parentElement.style.boxShadow = '0 8px 25px rgba(14, 106, 57, 0.15)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                    this.parentElement.style.boxShadow = 'none';
                });
            });

            // Smooth scroll to error
            const errorAlert = document.querySelector('.alert-danger');
            if (errorAlert) {
                errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            // Add entrance animation to form elements
            const formElements = document.querySelectorAll('.form-floating, .form-check, .btn-login');
            formElements.forEach((element, index) => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    element.style.transition = 'all 0.5s ease';
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, 100 * index);
            });
        });
    </script>
    
    <script>
        // Tambahkan JavaScript ini ke dalam event listener DOMContentLoaded yang sudah ada
        
        // Forgot Password Functionality
        const forgotPasswordLink = document.getElementById('forgotPasswordLink');
        const forgotPasswordSection = document.getElementById('forgotPasswordSection');
        const loginCard = document.querySelector('.login-card');
        
        // Initialize forgot password flow
        forgotPasswordLink.addEventListener('click', (e) => {
            e.preventDefault();
            showForgotPasswordSection();
        });
        
        function showForgotPasswordSection() {
            // Hide login form
            document.querySelector('form').style.display = 'none';
            document.querySelector('.forgot-link').style.display = 'none';
            
            // Show forgot password section
            forgotPasswordSection.classList.add('active');
            
            // Add entrance animation
            setTimeout(() => {
                forgotPasswordSection.style.transform = 'translateY(0)';
                forgotPasswordSection.style.opacity = '1';
            }, 100);
        }
        
        function hideForgotPasswordSection() {
            forgotPasswordSection.classList.remove('active');
            
            setTimeout(() => {
                document.querySelector('form').style.display = 'block';
                document.querySelector('.forgot-link').style.display = 'block';
            }, 300);
        }
        
        // Step Navigation
        let currentStep = 1;
        const totalSteps = 4;
        
        function nextStep() {
            if (currentStep < totalSteps) {
                // Hide current step
                document.getElementById(`step${currentStep}`).classList.remove('active');
                const currentStepEl = document.querySelector(`.step[data-step="${currentStep}"]`);
                currentStepEl.classList.add('completed');
                currentStepEl.classList.remove('active');
                
                // Change icon to checkmark for completed steps
                const currentIcon = currentStepEl.querySelector('i');
                currentIcon.className = 'fas fa-check';
                
                currentStep++;
                
                // Show next step
                setTimeout(() => {
                    document.getElementById(`step${currentStep}`).classList.add('active');
                    document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('active');
                }, 300);
            }
        }
        
        function resetSteps() {
            currentStep = 1;
            
            // Reset all steps
            for (let i = 1; i <= totalSteps; i++) {
                document.getElementById(`step${i}`).classList.remove('active');
                const stepEl = document.querySelector(`.step[data-step="${i}"]`);
                stepEl.classList.remove('active', 'completed');
                
                // Reset icons to original
                const icon = stepEl.querySelector('i');
                switch(i) {
                    case 1: icon.className = 'fas fa-user-check'; break;
                    case 2: icon.className = 'fas fa-shield-alt'; break;
                    case 3: icon.className = 'fas fa-key'; break;
                    case 4: icon.className = 'fas fa-check-circle'; break;
                }
            }
            
            // Activate first step
            document.getElementById('step1').classList.add('active');
            document.querySelector('.step[data-step="1"]').classList.add('active');
        }
        
        // Step 1: NIK Verification
        const forgotNik = document.getElementById('forgotNik');
        const verifyNikBtn = document.getElementById('verifyNikBtn');
        
        // NIK input validation
        forgotNik.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 16) {
                this.value = this.value.slice(0, 16);
            }
        });
        
        verifyNikBtn.addEventListener('click', () => {
            if (forgotNik.value.length === 16) {
                verifyNikBtn.classList.add('loading');
                
                // Simulate API call
                setTimeout(() => {
                    verifyNikBtn.classList.remove('loading');
                    nextStep();
                }, 2000);
            } else {
                showError('NIK harus 16 digit');
            }
        });
        
        // Step 2: Verification Code
        const verificationInputs = document.querySelectorAll('.verification-digit');
        const verifyCodeBtn = document.getElementById('verifyCodeBtn');
        
        // Auto-focus next input
        verificationInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1) {
                    if (index < verificationInputs.length - 1) {
                        verificationInputs[index + 1].focus();
                    }
                }
                
                // Check if all inputs filled
                const allFilled = Array.from(verificationInputs).every(input => input.value.length === 1);
                verifyCodeBtn.disabled = !allFilled;
            });
            
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                    verificationInputs[index - 1].focus();
                }
            });
            
            // Only allow numbers
            input.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            });
        });
        
        verifyCodeBtn.addEventListener('click', () => {
            verifyCodeBtn.classList.add('loading');
            
            // Simulate verification
            setTimeout(() => {
                verifyCodeBtn.classList.remove('loading');
                nextStep();
            }, 1500);
        });
        
        // Resend code
        document.getElementById('resendCode').addEventListener('click', (e) => {
            e.preventDefault();
            // Clear inputs
            verificationInputs.forEach(input => input.value = '');
            verificationInputs[0].focus();
            
            // Show success message (simulate)
            showSuccess('Kode verifikasi telah dikirim ulang');
        });
        
        // Step 3: New Password
        const newPassword = document.getElementById('newPassword');
        const confirmPassword = document.getElementById('confirmPassword');
        const updatePasswordBtn = document.getElementById('updatePasswordBtn');
        const strengthProgress = document.getElementById('strengthProgress');
        const requirements = document.getElementById('passwordRequirements');
        
        // Password strength checker
        newPassword.addEventListener('input', checkPasswordStrength);
        confirmPassword.addEventListener('input', validatePasswordMatch);
        
        function checkPasswordStrength() {
            const password = newPassword.value;
            let strength = 0;
            const requirementsList = requirements.querySelectorAll('li');
            
            // Check requirements
            const checks = [
                { regex: /.{8,}/, element: requirementsList[0] }, // Length
                { regex: /[A-Z]/, element: requirementsList[1] },  // Uppercase
                { regex: /[a-z]/, element: requirementsList[2] },  // Lowercase
                { regex: /[0-9]/, element: requirementsList[3] },  // Numbers
                { regex: /[^A-Za-z0-9]/, element: requirementsList[4] } // Symbols
            ];
            
            checks.forEach(check => {
                if (check.regex.test(password)) {
                    check.element.classList.remove('invalid');
                    check.element.classList.add('valid');
                    strength++;
                } else {
                    check.element.classList.remove('valid');
                    check.element.classList.add('invalid');
                }
            });
            
            // Update strength bar
            const strengthClasses = ['strength-weak', 'strength-fair', 'strength-good', 'strength-strong'];
            strengthProgress.className = 'strength-progress';
            
            if (strength > 0) {
                const strengthIndex = Math.min(strength - 1, 3);
                strengthProgress.classList.add(strengthClasses[strengthIndex]);
            }
            
            validatePasswordMatch();
        }
        
        function validatePasswordMatch() {
            const password = newPassword.value;
            const confirmPass = confirmPassword.value;
            
            // Check all requirements
            const hasMinLength = password.length >= 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSymbol = /[^A-Za-z0-9]/.test(password);
            const passwordsMatch = password === confirmPass && confirmPass.length > 0;
            
            const allRequirementsMet = hasMinLength && hasUppercase && hasLowercase && hasNumber && hasSymbol;
            const isValid = allRequirementsMet && passwordsMatch;
            
            // Visual feedback for password match
            if (confirmPass.length > 0) {
                if (passwordsMatch) {
                    confirmPassword.classList.remove('is-invalid');
                    confirmPassword.classList.add('is-valid');
                } else {
                    confirmPassword.classList.remove('is-valid');
                    confirmPassword.classList.add('is-invalid');
                }
            } else {
                confirmPassword.classList.remove('is-valid', 'is-invalid');
            }
            
            // Enable/disable button
            updatePasswordBtn.disabled = !isValid;
            
            // Add visual feedback to button
            if (isValid) {
                updatePasswordBtn.classList.add('btn-ready');
                updatePasswordBtn.style.opacity = '1';
                updatePasswordBtn.style.cursor = 'pointer';
            } else {
                updatePasswordBtn.classList.remove('btn-ready');
                updatePasswordBtn.style.opacity = '0.6';
                updatePasswordBtn.style.cursor = 'not-allowed';
            }
            
            console.log('Password validation:', {
                hasMinLength,
                hasUppercase, 
                hasLowercase,
                hasNumber,
                hasSymbol,
                passwordsMatch,
                isValid
            });
        }
        
        updatePasswordBtn.addEventListener('click', () => {
            updatePasswordBtn.classList.add('loading');
            
            // Simulate password update
            setTimeout(() => {
                updatePasswordBtn.classList.remove('loading');
                nextStep();
                createSuccessParticles();
            }, 2000);
        });
        
        // Step 4: Success particles
        function createSuccessParticles() {
            const particlesContainer = document.getElementById('successParticles');
            
            for (let i = 0; i < 20; i++) {
                setTimeout(() => {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.animationDelay = Math.random() * 2 + 's';
                    particle.style.animationDuration = (2 + Math.random() * 2) + 's';
                    
                    particlesContainer.appendChild(particle);
                    
                    setTimeout(() => {
                        if (particle.parentNode) {
                            particle.parentNode.removeChild(particle);
                        }
                    }, 4000);
                }, i * 100);
            }
        }
        
        // Back to login handlers
        document.getElementById('backToLoginFromStep1').addEventListener('click', (e) => {
            e.preventDefault();
            hideForgotPasswordSection();
            resetSteps();
        });
        
        document.getElementById('backToLoginBtn').addEventListener('click', () => {
            hideForgotPasswordSection();
            resetSteps();
        });
        
        // Toggle password visibility function
        function togglePasswordVisibility(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Utility functions
        function showError(message) {
            // Create error alert
            const alert = document.createElement('div');
            alert.className = 'alert alert-danger fade-in';
            alert.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>${message}`;
            
            // Insert before current step
            const currentStepEl = document.querySelector('.step-content.active');
            currentStepEl.insertBefore(alert, currentStepEl.firstChild);
            
            // Remove after 3 seconds
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 3000);
        }
        
        function showSuccess(message) {
            // Create success alert
            const alert = document.createElement('div');
            alert.className = 'alert alert-success fade-in';
            alert.innerHTML = `<i class="fas fa-check-circle me-2"></i>${message}`;
            
            // Insert before current step
            const currentStepEl = document.querySelector('.step-content.active');
            currentStepEl.insertBefore(alert, currentStepEl.firstChild);
            
            // Remove after 3 seconds
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 3000);
        }
</script>
</body>
</html>