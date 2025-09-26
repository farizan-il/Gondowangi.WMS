<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Karyawan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f6d3b 0%, #1a8a4f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            min-height: 600px;
            display: flex;
            backdrop-filter: blur(10px);
        }

        .welcome-section {
            background: linear-gradient(135deg, #0f6d3b 0%, #1a8a4f 100%);
            flex: 1;
            padding: 60px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .welcome-content {
            position: relative;
            z-index: 1;
        }

        .welcome-section h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .welcome-section p {
            font-size: 1.1rem;
            line-height: 1.6;
            opacity: 0.9;
        }

        .form-section {
            flex: 1;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-toggle {
            display: flex;
            background: #f0f0f0;
            border-radius: 50px;
            padding: 5px;
            position: relative;
        }

        .toggle-btn {
            flex: 1;
            padding: 12px 20px;
            background: transparent;
            border: none;
            border-radius: 45px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }

        .toggle-btn.active {
            color: white;
        }

        .toggle-slider {
            position: absolute;
            top: 5px;
            left: 5px;
            width: calc(50% - 5px);
            height: calc(100% - 10px);
            background: linear-gradient(135deg, #0f6d3b 0%, #1a8a4f 100%);
            border-radius: 45px;
            transition: transform 0.3s ease;
            box-shadow: 0 2px 10px rgba(15, 109, 59, 0.3);
        }

        .toggle-slider.register {
            transform: translateX(100%);
        }

        .form-container {
            position: relative;
            overflow: hidden;
        }

        .form {
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .form.hidden {
            transform: translateX(100%);
            opacity: 0;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
        }

        .form-group input:focus {
            outline: none;
            border-color: #0f6d3b;
            box-shadow: 0 0 0 3px rgba(15, 109, 59, 0.1);
            transform: translateY(-2px);
        }

        .form-group.error input {
            border-color: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
        }

        .error-message {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .form-group.error .error-message {
            opacity: 1;
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #0f6d3b 0%, #1a8a4f 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 50px rgba(15, 109, 59, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transition: all 0.3s ease;
            transform: translate(-50%, -50%);
        }

        .submit-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .loading {
            position: relative;
            color: transparent;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            transform: translate(-50%, -50%);
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .success-message.show {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                max-width: 400px;
            }

            .welcome-section {
                padding: 40px 30px;
                text-align: center;
            }

            .welcome-section h1 {
                font-size: 2rem;
            }

            .form-section {
                padding: 40px 30px;
            }
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            font-size: 18px;
        }

        .form-group {
            position: relative;
        }

        .form-group.has-toggle input {
            padding-right: 50px;
        }
        
        .custom-alert {
            background-color: #4CAF50; /* Hijau */
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .custom-alert:hover {
            background-color: #45a049; /* Efek hover untuk sedikit perubahan warna */
        }



        /*STYLE BARU UNTUK LUPA KATA SANDI*/
        /* Modal Styles */
.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 0;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-header {
    padding: 20px 25px 15px 25px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: #333;
    font-size: 1.5rem;
    font-weight: 600;
}

.modal-body {
    padding: 25px;
}

.close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
    transition: color 0.3s ease;
}

.close:hover,
.close:focus {
    color: #333;
    text-decoration: none;
}

.step-content {
    animation: stepFadeIn 0.3s ease-in;
}

@keyframes stepFadeIn {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.step-content p {
    color: #666;
    margin-bottom: 20px;
    line-height: 1.6;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    /*color: #333;*/
}

.form-group input[type="email"],
.form-group input[type="text"],
.form-group input[type="password"] {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 16px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    box-sizing: border-box;
}

.form-group input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}


.link-btn {
    background: none;
    border: none;
    color: #007bff;
    text-decoration: underline;
    cursor: pointer;
    font-size: 14px;
    transition: color 0.3s ease;
}

.link-btn:hover {
    color: #0056b3;
}

.error-message {
    color: #dc3545;
    font-size: 14px;
    margin-top: 5px;
    padding: 8px 12px;
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 6px;
}

/* Loading Overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 2000;
    backdrop-filter: blur(3px);
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-overlay p {
    color: white;
    font-size: 16px;
    margin: 0;
    text-align: center;
}

/* Success Message Styles */
.success-message {
    color: #155724;
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 14px;
}

/* OTP Input Styling */
#otpCode {
    text-align: center;
    letter-spacing: 5px;
    font-size: 20px;
    font-weight: bold;
    font-family: 'Courier New', monospace;
}

/* Responsive Design */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: 10% auto;
        max-height: 80vh;
    }
    
    .modal-header {
        padding: 15px 20px 10px 20px;
    }
    
    .modal-header h3 {
        font-size: 1.3rem;
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .form-group input[type="email"],
    .form-group input[type="text"],
    .form-group input[type="password"] {
        font-size: 16px; /* Prevents zoom on iOS */
    }
}

/* Dark Mode Support (Optional) */
@media (prefers-color-scheme: dark) {
    .modal-content {
        background-color: #2d3748;
        color: #e2e8f0;
    }
    
    .modal-header {
        border-bottom-color: #4a5568;
    }
    
    .modal-header h3 {
        color: #e2e8f0;
    }
    
    /*.form-group label {*/
    /*    color: #e2e8f0;*/
    /*}*/
    
    /*.form-group input[type="email"],*/
    /*.form-group input[type="text"],*/
    /*.form-group input[type="password"] {*/
    /*    background-color: #4a5568;*/
    /*    border-color: #718096;*/
        color: #e2e8f0;
    /*}*/
    
    .form-group input:focus {
        border-color: #63b3ed;
        box-shadow: 0 0 0 3px rgba(99, 179, 237, 0.1);
    }
    
    .step-content p {
        color: #a0aec0;
    }
    
    .close {
        color: #a0aec0;
    }
    
    .close:hover {
        color: #e2e8f0;
    }
}
    </style>
</head>
<body>
    <div class="container">
        <div class="welcome-section">
            <div class="welcome-content">
                <h1>Selamat Datang</h1>
                <p>Sistem Manajemen Karyawan terpadu untuk mengelola data dan proses rekrutmen dengan mudah dan efisien. Bergabunglah dengan tim profesional kami.</p>
            </div>
        </div>

        <div class="form-section">
            <div class="alert alert-info" role="alert" style="
            position: relative;
            padding: 12px 20px;
            margin-bottom: 16px;
            border: 1px solid #b8daff;
            border-radius: 6px;
            background-color: #d1ecf1;
            color: #0c5460;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            box-sizing: border-box;
        ">
            Anda perlu mendaftar terlebih dahulu sebelum mengajukan lamaran.
        </div>
            
            <div class="form-toggle" style="margin-bottom: 60px;">
                <button class="toggle-btn active" id="loginToggle">Masuk</button>
                <button class="toggle-btn" id="registerToggle">Registrasi</button>
                <div class="toggle-slider" id="toggleSlider"></div>
            </div>
            
            @if(session('success'))
                <div class="custom-alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="form-container">
                <form class="form" id="loginForm" action="{{ route('auth.login') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="nikOrEmail">NIK atau Email</label>
                        <input type="text" id="nikOrEmail" name="nikOrEmail" placeholder="Masukkan NIK atau Email Anda" required>
                        @error('nikOrEmail')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                
                    <div class="form-group has-toggle">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <label for="password" style="margin: 0;">Password</label>
                            <a href="#" id="forgotPasswordBtn" style="font-size: 0.9rem; text-decoration: none; color: #117440;">
                                Lupa Kata Sandi?
                            </a>
                        </div>
                    
                        <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required>
                    
                        @error('password')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                
                    <button type="submit" class="submit-btn" id="loginBtn">
                        Masuk
                    </button>
                </form>
            
                <!-- Error or Info Message -->
                @if(session('error'))
                    <script>
                        alert('{{ session('error') }}');
                    </script>
                @endif
            
                @if(session('info'))
                    <script>
                        alert('{{ session('info') }}');
                    </script>
                @endif
            
                @if(session('success'))
                    <script>
                        alert('{{ session('success') }}');
                    </script>
                @endif  

                <form class="form hidden" id="registerForm" action="{{ route('auth.register') }}" method="POST">
                    @csrf     
                    
                    <!--<div class="form-group">-->
                    <!--    <label for="full_name">Nama Lengkap</label>-->
                    <!--    <input type="full_name" id="email" name="full_name" placeholder="Masukkan Nama Lengkap Anda" required>-->
                    <!--    @error('email')-->
                    <!--        <div class="error-message">{{ $message }}</div>-->
                    <!--    @enderror-->
                    <!--</div>-->
                    
                    <div class="form-group">
                        <label for="fullName">Nama Lengkap</label>
                        <input type="text" id="fullName" name="fullName" placeholder="Masukkan nama lengkap Anda" required>
                        @error('fullName')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Masukkan email Anda" required>
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                
                    <div class="form-group has-toggle">
                        <label for="newPassword">Password</label>
                        <input type="password" id="newPassword" name="newPassword" placeholder="Buat password baru" required>
                        @error('newPassword')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                
                    <button type="submit" class="submit-btn" id="registerBtn">
                        Daftar Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Lupa Password -->
    <div id="forgotPasswordModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" style="padding: 10px;">&times;</span>
            
            <!-- Step 1: Email Input -->
            <div id="emailStep" class="step" style="padding: 10px;">
                <h3>Lupa Kata Sandi?</h3>
                <p>Masukkan email Anda untuk menerima kode OTP</p>
                <form id="forgotPasswordForm">
                    <div class="form-group">
                        <!--<label for="forgotEmail">Email</label>-->
                        <input type="email" id="forgotEmail" name="email" placeholder="Masukkan email Anda" required>
                        <div id="emailError" class="error-message" style="display: none;"></div>
                    </div>
                    <button type="submit" class="submit-btn" id="sendOtpBtn">
                        Kirim Kode OTP
                    </button>
                </form>
            </div>
    
            <!-- Step 2: OTP Verification -->
            <div id="otpStep" class="step" style="display: none; padding: 10px;">
                <h3>Verifikasi Kode OTP</h3>
                <p>Masukkan kode OTP yang telah dikirim ke email Anda</p>
                <form id="verifyOtpForm">
                    <input type="hidden" id="otpEmail" name="email">
                    <div class="form-group">
                        <label for="otpCode">Kode OTP</label>
                        <input type="text" id="otpCode" name="otp" placeholder="Masukkan 6 digit kode OTP" maxlength="6" required>
                        <div id="otpError" class="error-message" style="display: none;"></div>
                    </div>
                    <button type="submit" class="submit-btn" id="verifyOtpBtn">
                        Verifikasi OTP
                    </button>
                    <button type="button" class="link-btn" id="resendOtpBtn">
                        Kirim Ulang Kode OTP
                    </button>
                </form>
            </div>
    
            <!-- Step 3: Reset Password -->
            <div id="resetStep" class="step" style="display: none; padding: 10px;">
                <h3>Reset Kata Sandi</h3>
                <p>Masukkan kata sandi baru Anda</p>
                <form id="resetPasswordForm">
                    <input type="hidden" id="resetEmail" name="email">
                    <input type="hidden" id="resetToken" name="token">
                    <div class="form-group">
                        <label for="resetNewPassword">Kata Sandi Baru</label>
                        <input type="password" id="resetNewPassword" name="password" placeholder="Masukkan kata sandi baru" minlength="6" required>
                    </div>
                    <div class="form-group">
                        <label for="resetConfirmPassword">Konfirmasi Kata Sandi</label>
                        <input type="password" id="resetConfirmPassword" name="password_confirmation" placeholder="Konfirmasi kata sandi baru" minlength="6" required>
                        <div id="passwordError" class="error-message" style="display: none;"></div>
                    </div>
                    <button type="submit" class="submit-btn" id="resetPasswordBtn">
                        Reset Kata Sandi
                    </button>
                </form>
            </div>
    
            <!-- Success Message -->
            <div id="successStep" class="step" style="display: none; padding: 10px;">
                <h3>Berhasil!</h3>
                <p>Kata sandi Anda berhasil direset. Silakan login dengan kata sandi baru.</p>
                <button type="button" class="submit-btn" id="closeModalBtn">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading-spinner"></div>
        <p>Memproses permintaan...</p>
    </div>
    
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('forgotPasswordModal');
    const forgotPasswordBtn = document.getElementById('forgotPasswordBtn');
    const closeModal = document.querySelector('.close');
    const closeModalBtn = document.getElementById('closeModalBtn');

    // Get CSRF token
    function getCSRFToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            return metaTag.getAttribute('content');
        }
        
        // Fallback: try to get from form
        const csrfInput = document.querySelector('input[name="_token"]');
        if (csrfInput) {
            return csrfInput.value;
        }
        
        return null;
    }

    // Modal controls
    forgotPasswordBtn.addEventListener('click', function(e) {
        e.preventDefault();
        modal.style.display = 'block';
        resetModal();
    });

    closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    closeModalBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    window.addEventListener('click', function(e) {
        if (e.target == modal) {
            modal.style.display = 'none';
        }
    });

    // Step 1: Send OTP
    document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('forgotEmail').value;
        const sendOtpBtn = document.getElementById('sendOtpBtn');
        
        // Validate email format
        if (!email || !validateEmail(email)) {
            showError('emailError', 'Format email tidak valid');
            return;
        }
        
        sendOtpBtn.disabled = true;
        sendOtpBtn.textContent = 'Mengirim...';
        clearErrors();

        const csrfToken = getCSRFToken();
        if (!csrfToken) {
            showError('emailError', 'CSRF token tidak ditemukan. Silakan refresh halaman.');
            sendOtpBtn.disabled = false;
            sendOtpBtn.textContent = 'Kirim Kode OTP';
            return;
        }

        fetch('{{ route("auth.forgot.send-otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                document.getElementById('otpEmail').value = email;
                showStep('otpStep');
                showMessage('Kode OTP telah dikirim ke email Anda', 'success');
            } else {
                showError('emailError', data.message || 'Gagal mengirim OTP');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('emailError', 'Terjadi kesalahan saat mengirim OTP. Silakan coba lagi.');
        })
        .finally(() => {
            sendOtpBtn.disabled = false;
            sendOtpBtn.textContent = 'Kirim Kode OTP';
        });
    });

    // Step 2: Verify OTP
    document.getElementById('verifyOtpForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('otpEmail').value;
        const otp = document.getElementById('otpCode').value;
        const verifyOtpBtn = document.getElementById('verifyOtpBtn');
        
        // Validate OTP
        if (!otp || otp.length !== 6) {
            showError('otpError', 'Kode OTP harus 6 digit');
            return;
        }
        
        verifyOtpBtn.disabled = true;
        verifyOtpBtn.textContent = 'Memverifikasi...';
        clearErrors();

        const csrfToken = getCSRFToken();
        if (!csrfToken) {
            showError('otpError', 'CSRF token tidak ditemukan. Silakan refresh halaman.');
            verifyOtpBtn.disabled = false;
            verifyOtpBtn.textContent = 'Verifikasi OTP';
            return;
        }

        fetch('{{ route("auth.forgot.verify-otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email: email, otp: otp })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                document.getElementById('resetEmail').value = email;
                document.getElementById('resetToken').value = data.token;
                showStep('resetStep');
                showMessage('OTP berhasil diverifikasi', 'success');
            } else {
                showError('otpError', data.message || 'Kode OTP tidak valid');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('otpError', 'Terjadi kesalahan saat verifikasi OTP. Silakan coba lagi.');
        })
        .finally(() => {
            verifyOtpBtn.disabled = false;
            verifyOtpBtn.textContent = 'Verifikasi OTP';
        });
    });

    // Resend OTP
    document.getElementById('resendOtpBtn').addEventListener('click', function() {
        const email = document.getElementById('otpEmail').value;
        const resendBtn = this;
        
        resendBtn.disabled = true;
        resendBtn.textContent = 'Mengirim ulang...';

        const csrfToken = getCSRFToken();
        if (!csrfToken) {
            showError('otpError', 'CSRF token tidak ditemukan. Silakan refresh halaman.');
            resendBtn.disabled = false;
            resendBtn.textContent = 'Kirim Ulang Kode OTP';
            return;
        }
        
        fetch('{{ route("auth.forgot.send-otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showMessage('Kode OTP baru telah dikirim', 'success');
                document.getElementById('otpCode').value = '';
                clearErrors();
            } else {
                showError('otpError', data.message || 'Gagal mengirim ulang OTP');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('otpError', 'Terjadi kesalahan saat mengirim ulang OTP');
        })
        .finally(() => {
            resendBtn.disabled = false;
            resendBtn.textContent = 'Kirim Ulang Kode OTP';
        });
    });

    // Step 3: Reset Password
    document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('resetEmail').value;
        const token = document.getElementById('resetToken').value;
        const password = document.getElementById('resetNewPassword').value;
        const passwordConfirmation = document.getElementById('resetConfirmPassword').value;
        const resetPasswordBtn = document.getElementById('resetPasswordBtn');

        // Validate password
        if (password.length < 6) {
            showError('passwordError', 'Password minimal 6 karakter');
            return;
        }

        // Validate password confirmation
        if (password !== passwordConfirmation) {
            showError('passwordError', 'Konfirmasi kata sandi tidak cocok');
            return;
        }
        
        resetPasswordBtn.disabled = true;
        resetPasswordBtn.textContent = 'Mereset...';
        clearErrors();

        const csrfToken = getCSRFToken();
        if (!csrfToken) {
            showError('passwordError', 'CSRF token tidak ditemukan. Silakan refresh halaman.');
            resetPasswordBtn.disabled = false;
            resetPasswordBtn.textContent = 'Reset Kata Sandi';
            return;
        }

        fetch('{{ route("auth.forgot.reset-password") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                email: email, 
                token: token, 
                password: password,
                password_confirmation: passwordConfirmation
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showStep('successStep');
            } else {
                showError('passwordError', data.message || 'Gagal mereset password');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('passwordError', 'Terjadi kesalahan saat reset password. Silakan coba lagi.');
        })
        .finally(() => {
            resetPasswordBtn.disabled = false;
            resetPasswordBtn.textContent = 'Reset Kata Sandi';
        });
    });

    // Helper functions
    function showStep(stepId) {
        const steps = document.querySelectorAll('.step');
        steps.forEach(step => step.style.display = 'none');
        document.getElementById(stepId).style.display = 'block';
        clearErrors();
    }

    function showError(elementId, message) {
        const errorElement = document.getElementById(elementId);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }

    function clearErrors() {
        const errors = document.querySelectorAll('.error-message');
        errors.forEach(error => {
            error.style.display = 'none';
            error.textContent = '';
        });
    }

    function showMessage(message, type) {
        // Simple console log for now - you can implement toast notification here
        console.log(type + ': ' + message);
        
        // Optional: Create a simple alert
        if (type === 'success') {
            // You can replace this with a nicer notification
            setTimeout(() => {
                console.log('Success: ' + message);
            }, 100);
        }
    }

    function resetModal() {
        showStep('emailStep');
        document.getElementById('forgotPasswordForm').reset();
        document.getElementById('verifyOtpForm').reset();
        document.getElementById('resetPasswordForm').reset();
        clearErrors();
    }

    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Auto-format OTP input
    document.getElementById('otpCode').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 6) {
            value = value.substring(0, 6);
        }
        e.target.value = value;
    });

    // Add countdown timer for OTP resend (optional)
    let resendCountdown = 0;
    function startResendCountdown() {
        const resendBtn = document.getElementById('resendOtpBtn');
        resendCountdown = 60; // 60 seconds
        resendBtn.disabled = true;
        
        const interval = setInterval(() => {
            resendBtn.textContent = `Kirim Ulang (${resendCountdown}s)`;
            resendCountdown--;
            
            if (resendCountdown < 0) {
                clearInterval(interval);
                resendBtn.disabled = false;
                resendBtn.textContent = 'Kirim Ulang Kode OTP';
            }
        }, 1000);
    }

    // Start countdown when OTP is sent successfully
    document.getElementById('forgotPasswordForm').addEventListener('submit', function() {
        // This will be called after successful OTP send
        setTimeout(() => {
            if (document.getElementById('otpStep').style.display !== 'none') {
                startResendCountdown();
            }
        }, 1000);
    });
});
</script>

    <script>
        // DOM Elements
        const loginToggle = document.getElementById('loginToggle');
        const registerToggle = document.getElementById('registerToggle');
        const toggleSlider = document.getElementById('toggleSlider');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const togglePassword = document.getElementById('togglePassword');
        const toggleNewPassword = document.getElementById('toggleNewPassword');

        // Dummy data for demonstration
        const employees = [
            { nik: '123456789', password: 'password123' },
            { nik: '987654321', password: 'admin123' }
        ];

        // Toggle between login and register
        loginToggle.addEventListener('click', () => {
            loginToggle.classList.add('active');
            registerToggle.classList.remove('active');
            toggleSlider.classList.remove('register');
            loginForm.classList.remove('hidden');
            registerForm.classList.add('hidden');
        });

        registerToggle.addEventListener('click', () => {
            registerToggle.classList.add('active');
            loginToggle.classList.remove('active');
            toggleSlider.classList.add('register');
            registerForm.classList.remove('hidden');
            loginForm.classList.add('hidden');
        });

        // Password toggle functionality
        togglePassword.addEventListener('click', () => {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            togglePassword.textContent = type === 'password' ? '👁️' : '🙈';
        });

        toggleNewPassword.addEventListener('click', () => {
            const passwordInput = document.getElementById('newPassword');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            toggleNewPassword.textContent = type === 'password' ? '👁️' : '🙈';
        });

        // Form validation
        function validateForm(form) {
            const inputs = form.querySelectorAll('input[required]');
            let isValid = true;

            inputs.forEach(input => {
                const formGroup = input.closest('.form-group');
                formGroup.classList.remove('error');

                if (!input.value.trim()) {
                    formGroup.classList.add('error');
                    isValid = false;
                } else if (input.type === 'email' && !isValidEmail(input.value)) {
                    formGroup.classList.add('error');
                    isValid = false;
                } else if (input.name === 'newPassword' && input.value.length < 6) {
                    formGroup.classList.add('error');
                    isValid = false;
                }
            });

            return isValid;
        }

        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Login form submission
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            if (!validateForm(loginForm)) {
                return;
            }

            const nik = document.getElementById('nik').value;
            const password = document.getElementById('password').value;
            const loginBtn = document.getElementById('loginBtn');
            const successMessage = document.getElementById('loginSuccess');

            // Show loading state
            loginBtn.classList.add('loading');
            loginBtn.disabled = true;

            // Simulate API call
            setTimeout(() => {
                const employee = employees.find(emp => emp.nik === nik && emp.password === password);
                
                if (employee) {
                    successMessage.classList.add('show');
                    setTimeout(() => {
                        // Redirect to dashboard or main page
                        alert('Login berhasil! Mengarahkan ke dashboard...');
                        // window.location.href = '/dashboard';
                    }, 1000);
                } else {
                    alert('NIK atau password salah. Silakan coba lagi.');
                    loginBtn.classList.remove('loading');
                    loginBtn.disabled = false;
                }
            }, 1500);
        });

        // Register form submission
        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            if (!validateForm(registerForm)) {
                return;
            }

            const registerBtn = document.getElementById('registerBtn');
            const successMessage = document.getElementById('registerSuccess');

            // Show loading state
            registerBtn.classList.add('loading');
            registerBtn.disabled = true;

            // Simulate API call
            setTimeout(() => {
                successMessage.classList.add('show');
                setTimeout(() => {
                    // Redirect to form-karyawan-baru
                    alert('Registrasi berhasil! Mengarahkan ke form karyawan baru...');
                    window.location.href = '/form-karyawan-baru';
                }, 1000);
            }, 1500);
        });

        // Real-time validation
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('blur', () => {
                const formGroup = input.closest('.form-group');
                formGroup.classList.remove('error');

                if (input.hasAttribute('required') && !input.value.trim()) {
                    formGroup.classList.add('error');
                } else if (input.type === 'email' && input.value && !isValidEmail(input.value)) {
                    formGroup.classList.add('error');
                } else if (input.name === 'newPassword' && input.value && input.value.length < 6) {
                    formGroup.classList.add('error');
                }
            });
        });

        // Add smooth animations on load
        window.addEventListener('load', () => {
            document.querySelector('.container').style.opacity = '0';
            document.querySelector('.container').style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                document.querySelector('.container').style.transition = 'all 0.6s ease';
                document.querySelector('.container').style.opacity = '1';
                document.querySelector('.container').style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>