<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kandidat - {{ $karyawan->nama_lengkap }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .hover-lift {
            transition: all 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .status-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .progress-bar {
            background: linear-gradient(90deg, #10b981, #059669);
            height: 4px;
            border-radius: 2px;
        }
        
        /* Enhanced Success Alert Styles */
        .success-alert {
            background: linear-gradient(135deg, #065f46, #047857);
            border: none;
            position: relative;
            overflow: hidden;
        }
        .success-alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 50%, rgba(255,255,255,0.1) 100%);
            animation: shimmer 3s infinite;
        }
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        .success-icon {
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        /* Enhanced Timeline Styles */
        .timeline-item {
            position: relative;
            padding-left: 3rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 1.25rem;
            top: 2.5rem;
            bottom: -1rem;
            width: 2px;
            background: linear-gradient(to bottom, #e5e7eb, transparent);
        }
        .timeline-item:last-child::before {
            display: none;
        }
        .timeline-dot {
            position: absolute;
            left: 0;
            top: 0;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }
        .timeline-dot.active {
            animation: pulse-ring 2s infinite;
        }
        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.8), 0 0 0 8px rgba(16, 185, 129, 0.3); }
            50% { box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.8), 0 0 0 12px rgba(16, 185, 129, 0.1); }
            100% { box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.8), 0 0 0 8px rgba(16, 185, 129, 0.3); }
        }
        .timeline-content {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 0.75rem;
            padding: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .timeline-content:hover {
            transform: translateX(4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .approved-alert {
            position: relative;
            overflow: hidden;
        }
        
        .approved-alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 50%, rgba(255,255,255,0.1) 100%);
            animation: shimmer 3s infinite;
        }
        
        .confetti-piece {
            animation-duration: 3s;
            animation-iteration-count: infinite;
            animation-timing-function: ease-in-out;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .glossy-border {
            border: 5px solid transparent;
        }

        .geometric-bg {
          background-color: #f9fafb;
          background-image: 
            radial-gradient(circle at 30% 30%, rgba(99, 102, 241, 0.15), transparent 40%),
            radial-gradient(circle at 70% 70%, rgba(16, 185, 129, 0.12), transparent 50%),
            linear-gradient(135deg, rgba(0, 0, 0, 0.02) 25%, transparent 25%),
            linear-gradient(225deg, rgba(0, 0, 0, 0.02) 25%, transparent 25%);
          background-size: cover, cover, 40px 40px, 40px 40px;
          background-repeat: no-repeat, no-repeat, repeat, repeat;
          background-position: center center;
        }


    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen geometric-bg">
    <!-- Modern Header -->
    <header class="glass-card sticky top-0 z-50 border-b border-gray-200 shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-tie text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Dashboard Kandidat</h1>
                        <p class="text-sm text-gray-500">Kelola profil dan status lamaran Anda</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="hidden sm:block text-right">
                        <p class="text-sm font-medium text-gray-900">{{ $karyawan->nama_lengkap }}</p>
                        <p class="text-xs text-gray-500">{{ $karyawan->email }}</p>
                    </div>
                    <form action="{{ route('auth.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 hover:scale-105">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Riwayat Lamaran Section -->
        @if($lamaranList->count() > 1)
            <div class="glass-card rounded-2xl shadow-xl mb-8 overflow-hidden hover-lift">
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-6">
                    <div class="flex items-center justify-between text-white">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                                <i class="fas fa-history text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold">Riwayat Lamaran Anda</h2>
                        </div>
                        <span class="bg-white bg-opacity-20 px-4 py-2 rounded-full text-sm font-bold">
                            {{ $lamaranList->count() }} Lamaran
                        </span>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($lamaranList as $index => $lamaran)
                        <div class="bg-white rounded-xl border-2 {{ $lamaran->id == $karyawan->id ? 'border-purple-400 bg-purple-50' : 'border-gray-200' }} p-4 hover:shadow-lg transition-all duration-200 hover:scale-105">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="font-bold text-lg text-gray-900 mb-1">
                                        {{ $lamaran->posisilamaran->position_title ?? 'Posisi tidak ditemukan' }}
                                    </h4>
                                    <p class="text-sm text-gray-600">{{ $lamaran->created_at->format('d M Y H:i') }}</p>
                                </div>
                                @if($lamaran->id == $karyawan->id)
                                <span class="bg-purple-500 text-white px-2 py-1 rounded-full text-xs font-bold">
                                    Sedang Dilihat
                                </span>
                                @endif
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="mb-3">
                                @if($lamaran->status == 'Pending')
                                    <span class="inline-flex items-center bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                                        <i class="fas fa-clock mr-1"></i> Menunggu
                                    </span>
                                @elseif($lamaran->status == 'Diterima')
                                    <span class="inline-flex items-center bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                        <i class="fas fa-check-circle mr-1"></i> Diterima
                                    </span>
                                @elseif($lamaran->status == 'rejected')
                                    <span class="inline-flex items-center bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                                        <i class="fas fa-times-circle mr-1"></i> Ditolak
                                    </span>
                                @elseif($lamaran->status == 'cocok')
                                    <span class="inline-flex items-center bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full text-sm font-medium">
                                        <i class="fas fa-star mr-1"></i> Cocok
                                    </span>
                                @else
                                    <span class="inline-flex items-center bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                        <i class="fas fa-bookmark mr-1"></i> Berprospek
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Quick Info -->
                            <div class="text-sm text-gray-600 mb-3">
                                <p><i class="fas fa-envelope mr-1"></i> {{ $lamaran->email }}</p>
                                @if($lamaran->gaji_diharapkan)
                                <p><i class="fas fa-money-bill-wave mr-1"></i> Rp {{ number_format($lamaran->gaji_diharapkan, 0, ',', '.') }}</p>
                                @endif
                            </div>
                            
                            <!-- Action Button -->
                            @if($lamaran->id != $karyawan->id)
                            <a href="{{ route('kandidat.karyawan.view-application', $lamaran->id) }}" 
                               class="block w-full text-center bg-gradient-to-r from-purple-500 to-indigo-500 hover:from-purple-600 hover:to-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200">
                                <i class="fas fa-eye mr-1"></i> Lihat Detail
                            </a>
                            @else
                            <div class="block w-full text-center bg-gray-100 text-gray-500 px-4 py-2 rounded-lg text-sm font-medium">
                                <i class="fas fa-eye mr-1"></i> Sedang Dilihat
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Summary Stats -->
                    <div class="mt-6 pt-6 border-t border-gray-200 ">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-2xl font-bold text-gray-900">{{ $lamaranList->count() }}</div>
                                <div class="text-sm text-gray-600">Total Lamaran</div>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-3">
                                <div class="text-2xl font-bold text-yellow-600">{{ $lamaranList->where('status', 'pending')->count() }}</div>
                                <div class="text-sm text-gray-600">Menunggu</div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-3">
                                <div class="text-2xl font-bold text-green-600">{{ $lamaranList->whereIn('status', ['approved', 'cocok'])->count() }}</div>
                                <div class="text-sm text-gray-600">Diterima/Cocok</div>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-3">
                                <div class="text-2xl font-bold text-blue-600">{{ $lamaranList->where('status', 'save')->count() }}</div>
                                <div class="text-sm text-gray-600">Berprospek</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Modern Alert Messages -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-r-xl fade-in">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-400 mr-3"></i>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-xl fade-in">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Status Notification Alerts -->
        @if($karyawan->status == 'save')
            <div class="bg-blue-50 border-l-4 border-blue-400 p-6 mb-6 rounded-r-xl fade-in">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-400 mr-3 mt-1 text-lg"></i>
                    <div>
                        <h4 class="text-blue-900 font-bold text-lg mb-2">Informasi Status Lamaran</h4>
                        <p class="text-blue-800 leading-relaxed">
                            Kami menghargai minat Anda untuk bergabung dengan perusahaan kami. Meskipun Anda belum terpilih untuk posisi <strong>{{ $karyawan->posisi_dilamar }}</strong> saat ini, 
                            profil Anda menunjukkan potensi yang baik dan telah kami tandai sebagai kandidat yang berprospek. 
                            Tim HRD kami akan menghubungi Anda apabila terdapat kesempatan untuk posisi lain yang sesuai dengan kualifikasi Anda di masa mendatang.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if($karyawan->status == 'Diterima')
            <div class="success-alert text-white p-8 mb-6 rounded-2xl fade-in shadow-2xl">
                <div class="flex items-start relative z-10">
                    <i class="fas fa-trophy success-icon mr-4 mt-1 text-3xl text-yellow-300"></i>
                    <div>
                        <h4 class="text-white font-bold text-2xl mb-3 flex items-center">
                            🎉 Selamat! Anda Adalah Kandidat Terpilih
                            <span class="ml-3 bg-yellow-400 text-green-800 px-3 py-1 rounded-full text-sm font-bold animate-pulse">
                                COCOK
                            </span>
                        </h4>
                        <p class="text-green-100 leading-relaxed text-lg">
                            Berdasarkan evaluasi yang telah dilakukan, Anda dinyatakan sebagai kandidat yang sesuai untuk posisi <strong class="text-yellow-300">{{ $karyawan->posisi_dilamar }}</strong>. 
                            Tim HRD kami akan segera menghubungi Anda untuk melanjutkan proses rekrutmen ke tahap berikutnya. 
                            Mohon untuk tetap memantau komunikasi melalui email atau telepon yang telah Anda daftarkan.
                        </p>
                        <div class="mt-4 flex items-center space-x-4">
                            <div class="flex items-center text-green-100">
                                <i class="fas fa-phone-alt mr-2"></i>
                                <span class="text-sm">Segera dihubungi</span>
                            </div>
                            <div class="flex items-center text-green-100">
                                <i class="fas fa-calendar-check mr-2"></i>
                                <span class="text-sm">Proses lanjutan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Alert untuk Status Approved - Tambahkan setelah alert cocok -->
        @if($karyawan->status == 'approved')
            <div class="approved-alert relative bg-gradient-to-r from-green-600 via-emerald-600 to-green-700 text-white p-8 mb-6 rounded-2xl fade-in shadow-2xl overflow-hidden">
                <!-- Background Animation -->
                <div class="absolute inset-0 bg-gradient-to-r from-green-400 via-emerald-400 to-green-500 opacity-20 animate-pulse"></div>
                
                <!-- Floating Confetti -->
                <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
                    <div class="confetti-piece absolute top-4 left-10 w-3 h-3 bg-yellow-400 rotate-45 animate-bounce" style="animation-delay: 0.5s;"></div>
                    <div class="confetti-piece absolute top-8 right-16 w-2 h-2 bg-pink-400 rounded-full animate-bounce" style="animation-delay: 1s;"></div>
                    <div class="confetti-piece absolute top-12 left-1/4 w-3 h-3 bg-blue-400 rotate-12 animate-bounce" style="animation-delay: 1.5s;"></div>
                    <div class="confetti-piece absolute top-6 right-1/3 w-2 h-2 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 2s;"></div>
                </div>
                
                <div class="flex items-start relative z-10">
                    <div class="mr-6">
                        <div class="w-16 h-16 bg-yellow-400 rounded-full flex items-center justify-center animate-bounce">
                            <i class="fas fa-trophy text-green-800 text-2xl"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-white font-bold text-3xl mb-4 flex items-center animate-pulse">
                            🎊 SELAMAT! ANDA DITERIMA! 🎊
                            <span class="ml-4 bg-yellow-400 text-green-800 px-4 py-2 rounded-full text-lg font-black animate-bounce">
                                APPROVED
                            </span>
                        </h4>
                        
                        <div class="bg-white bg-opacity-20 rounded-xl p-4 mb-4 backdrop-blur-sm">
                            <p class="text-green-100 leading-relaxed text-lg font-medium">
                                🌟 <strong class="text-yellow-300">Kami dengan bangga mengumumkan bahwa Anda telah resmi diterima</strong> 
                                untuk bergabung dengan tim kami pada posisi <strong class="text-yellow-300">{{ $karyawan->posisi_dilamar }}</strong>! 
                                Selamat datang di keluarga besar perusahaan kami.
                            </p>
                        </div>
                        
                        <div class="mt-6 p-4 bg-green-800 bg-opacity-50 rounded-xl">
                            <h5 class="text-yellow-300 font-bold text-lg mb-2 flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                Langkah Selanjutnya:
                            </h5>
                            <ul class="text-green-100 space-y-1 text-sm">
                                <li class="flex items-center"><i class="fas fa-check-circle text-yellow-300 mr-2"></i> Tim HRD akan menghubungi Anda dalam 1-2 hari kerja</li>
                                <li class="flex items-center"><i class="fas fa-check-circle text-yellow-300 mr-2"></i> Persiapkan dokumen yang diperlukan</li>
                                <li class="flex items-center"><i class="fas fa-check-circle text-yellow-300 mr-2"></i> Ikuti proses onboarding yang akan dijadwalkan</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Celebration Badge -->
                <div class="absolute top-4 right-4 bg-yellow-400 text-green-800 px-3 py-1 rounded-full text-xs font-black animate-pulse">
                    STATUS: HIRED! 🎉
                </div>
            </div>
        @endif

        <!-- Hero Status Card -->
        <div class="glass-card rounded-2xl shadow-xl mb-8 overflow-hidden hover-lift ">
            <div class="gradient-bg p-6">
                <div class="flex items-center justify-between text-white">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                            @if($karyawan->status == 'pending')
                                <i class="fas fa-clock text-2xl status-pulse"></i>
                            @elseif($karyawan->status == 'approved')
                                <i class="fas fa-check-circle text-2xl"></i>
                            @elseif($karyawan->status == 'rejected')
                                <i class="fas fa-times-circle text-2xl"></i>
                            @elseif($karyawan->status == 'cocok')
                                <i class="fas fa-star text-2xl"></i>
                            @else
                                <i class="fas fa-bookmark text-2xl"></i>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold mb-1">Status Lamaran</h2>
                            <div class="flex items-center space-x-2">
                                @if($karyawan->status == 'Pending')
                                    <span class="bg-yellow-400 text-yellow-900 px-4 py-2 rounded-full text-sm font-bold">
                                        Menunggu Review
                                    </span>
                                @elseif($karyawan->status == 'approved')
                                    <span class="bg-green-400 text-green-900 px-4 py-2 rounded-full text-sm font-bold">
                                        Diterima
                                    </span>
                                @elseif($karyawan->status == 'rejected')
                                    <span class="bg-red-400 text-red-900 px-4 py-2 rounded-full text-sm font-bold">
                                        Ditolak
                                    </span>
                                @elseif($karyawan->status == 'cocok')
                                    <span class="bg-emerald-400 text-emerald-900 px-4 py-2 rounded-full text-sm font-bold">
                                        Kandidat Cocok
                                    </span>
                                @else
                                    <span class="bg-blue-400 text-blue-900 px-4 py-2 rounded-full text-sm font-bold">
                                        Kandidat Berprospek
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm opacity-90">Diajukan pada</p>
                        <p class="text-lg font-bold">{{ $karyawan->created_at->format('d M Y') }}</p>
                        <p class="text-sm opacity-75">{{ $karyawan->created_at->format('H:i') }}</p>
                    </div>
                </div>
            </div>
            <div class="progress-bar"></div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
            <!-- Profile Content -->
            <div class="xl:col-span-3 space-y-8">
                <!-- Profile Summary -->
                <div class="glass-card rounded-2xl shadow-lg p-8 hover-lift" style="border: 1px solid rgba(0, 128, 0, 0.3);">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-user text-blue-600 text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Profil Kandidat</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Posisi yang Dilamar</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $karyawan->posisilamaran->position_title ?? 'Posisi tidak ditemukan' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                                <p class="text-gray-900">{{ $karyawan->email }}</p>
                            </div>
                            
                        </div>
                        <div class="space-y-4">
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label class="block text-sm font-bold text-gray-700 mb-1"> Tanggal Lahir</label>
                                <p class="text-gray-900">{{ \Carbon\Carbon::parse($karyawan->tanggal_lahir)->format('d M Y') }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <label class="block text-sm font-bold text-gray-700 mb-1">No. Telepon</label>
                                <p class="text-gray-900">{{ $karyawan->no_telepon }}</p>
                            </div>
                            <!--<div class="p-4 bg-gray-50 rounded-xl">-->
                            <!--    <label class="block text-sm font-bold text-gray-700 mb-1">Tempat, Tanggal Lahir</label>-->
                            <!--    <p class="text-gray-900">{{ $karyawan->tempat_lahir }}, {{ \Carbon\Carbon::parse($karyawan->tanggal_lahir)->format('d M Y') }}</p>-->
                            <!--</div>-->
                            <!--<div class="p-4 bg-gray-50 rounded-xl">-->
                            <!--    <label class="block text-sm font-bold text-gray-700 mb-1">Status Pernikahan</label>-->
                            <!--    <p class="text-gray-900">{{ $karyawan->status_pernikahan }}</p>-->
                            <!--</div>-->
                            <!--<div class="p-4 bg-gray-50 rounded-xl">-->
                            <!--    <label class="block text-sm font-bold text-gray-700 mb-1">Gaji yang Diharapkan</label>-->
                            <!--    <p class="text-gray-900 font-semibold">{{ $karyawan->gaji_diharapkan ? 'Rp ' . number_format($karyawan->gaji_diharapkan, 0, ',', '.') : '-' }}</p>-->
                            <!--</div>-->
                        </div>
                    </div>
                </div>

                <!-- Education Section -->
                @if($karyawan->pendidikan_formal)
                <div class="glass-card rounded-2xl shadow-lg p-8 hover-lift" style="border: 1px solid rgba(0, 128, 0, 0.3);">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-graduation-cap text-indigo-600 text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Pendidikan Formal</h3>
                    </div>
                    <div class="space-y-4">
                        @foreach($karyawan->pendidikan_formal as $pendidikan)
                        <div class="p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border-l-4 border-indigo-400">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-lg text-gray-900">{{ $pendidikan['jenjang'] ?? '' }}</h4>
                                @if(isset($pendidikan['nilai']) && $pendidikan['nilai'])
                                <span class="bg-indigo-200 text-indigo-800 px-3 py-1 rounded-full text-sm font-bold">
                                    Nilai Akhir : {{ $pendidikan['nilai'] }}
                                </span>
                                @endif
                            </div>
                            <p class="text-gray-700 font-medium mb-1">{{ $pendidikan['nama_sekolah'] ?? '' }}</p>
                            @if(isset($pendidikan['jurusan']) && $pendidikan['jurusan'])
                            <p class="text-gray-600 text-sm mb-2">
                                <i class="fas fa-book mr-1"></i>{{ $pendidikan['jurusan'] }}
                            </p>
                            @endif
                            <p class="text-sm text-gray-500">
                                <i class="fas fa-calendar mr-1"></i>Periode {{ $pendidikan['tahun_masuk'] ?? '' }} - {{ $pendidikan['tahun_keluar'] ?? '' }} 
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Work Experience Section -->
                @if($karyawan->pengalaman_kerja)
                <div class="glass-card rounded-2xl shadow-lg p-8 hover-lift" style="border: 1px solid rgba(0, 128, 0, 0.3);">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-briefcase text-green-600 text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Pengalaman Kerja</h3>
                    </div>
                    <div class="space-y-4">
                        @foreach($karyawan->pengalaman_kerja as $pengalaman)
                        <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border-l-4 border-green-400">
                            <h4 class="font-bold text-lg text-gray-900">{{ $pengalaman['jabatan'] ?? '' }}</h4>
                            <p class="text-gray-700 font-medium">{{ $pengalaman['nama_perusahaan'] ?? '' }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ !empty($pengalaman['masa_kerja_dari']) ? \Carbon\Carbon::parse($pengalaman['masa_kerja_dari'])->translatedFormat('j F Y') : '' }} -
                                {{ !empty($pengalaman['masih_bekerja']) && $pengalaman['masih_bekerja'] == '1' ? 'Saat ini' : (!empty($pengalaman['masa_kerja_sampai']) ? \Carbon\Carbon::parse($pengalaman['masa_kerja_sampai'])->translatedFormat('j F Y') : '') }}
                            </p>
                            <p class="text-gray-700 mt-2 text-sm">{{ $pengalaman['uraian_pekerjaan'] }}</p>

                            @if(isset($pengalaman['deskripsi']))
                            <p class="text-gray-700 mt-2 text-sm">{{ $pengalaman['uraian_pekerjaan'] }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="xl:col-span-1 space-y-6">
                <!-- Enhanced Timeline -->
                <div class="glass-card rounded-2xl shadow-lg p-6 hover-lift" style="border: 1px solid rgba(0, 128, 0, 0.3);">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-clock text-purple-600 mr-2"></i>
                        Timeline Status
                    </h3>
                    <div class="space-y-6">
                        <div class="timeline-item">
                            <div class="timeline-dot bg-green-500 active">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                            <div class="timeline-content">
                                <p class="text-sm font-bold text-gray-900">Lamaran Dikirim</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $karyawan->created_at->format('d M Y H:i') }}</p>
                                <p class="text-xs text-gray-400 mt-1">Berhasil terkirim ke sistem</p>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-dot {{ in_array($karyawan->status, ['approved', 'rejected', 'cocok', 'save']) ? 'bg-green-500 active' : 'bg-gray-300' }}">
                                <i class="fas fa-eye text-white text-sm"></i>
                            </div>
                            <div class="timeline-content">
                                <p class="text-sm font-bold text-gray-900">Review HR</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ in_array($karyawan->status, ['approved', 'rejected', 'cocok', 'save']) ? $karyawan->updated_at->format('d M Y H:i') : 'Menunggu' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">Evaluasi dokumen dan kualifikasi</p>
                            </div>
                        </div>
                        
                        @if($karyawan->status == 'approved')
                        <div class="timeline-item">
                            <div class="timeline-dot bg-green-500 active">
                                <i class="fas fa-thumbs-up text-white text-sm"></i>
                            </div>
                            <div class="timeline-content">
                                <p class="text-sm font-bold text-gray-900">Diterima</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $karyawan->updated_at->format('d M Y H:i') }}</p>
                                <p class="text-xs text-green-600 mt-1 font-medium">Selamat! Anda diterima</p>
                            </div>
                        </div>
                        @elseif($karyawan->status == 'rejected')
                        <div class="timeline-item">
                            <div class="timeline-dot bg-red-500">
                                <i class="fas fa-times text-white text-sm"></i>
                            </div>
                            <div class="timeline-content">
                                <p class="text-sm font-bold text-gray-900">Ditolak</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $karyawan->updated_at->format('d M Y H:i') }}</p>
                                <p class="text-xs text-red-600 mt-1">Terima kasih atas partisipasi Anda</p>
                            </div>
                        </div>
                        @elseif($karyawan->status == 'cocok')
                        <div class="timeline-item">
                            <div class="timeline-dot bg-gradient-to-r from-emerald-500 to-green-500 active">
                                <i class="fas fa-star text-white text-sm"></i>
                            </div>
                            <div class="timeline-content bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200">
                                <p class="text-sm font-bold text-emerald-900 flex items-center">
                                    <i class="fas fa-trophy mr-2 text-yellow-500"></i>
                                    Kandidat Cocok
                                </p>
                                <p class="text-xs text-emerald-600 mt-1">{{ $karyawan->updated_at->format('d M Y H:i') }}</p>
                                <p class="text-xs text-emerald-700 mt-1 font-medium">Anda adalah kandidat terpilih!</p>
                                <div class="mt-2 flex items-center space-x-2">
                                    <span class="bg-emerald-200 text-emerald-800 px-2 py-1 rounded-full text-xs font-bold">
                                        TERPILIH
                                    </span>
                                    <span class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full text-xs font-bold">
                                        PRIORITAS
                                    </span>
                                </div>
                            </div>
                        </div>
                        @elseif($karyawan->status == 'save')
                        <div class="timeline-item">
                            <div class="timeline-dot bg-blue-500 active">
                                <i class="fas fa-bookmark text-white text-sm"></i>
                            </div>
                            <div class="timeline-content bg-blue-50 border border-blue-200">
                                <p class="text-sm font-bold text-blue-900">Kandidat Berprospek</p>
                                <p class="text-xs text-blue-600 mt-1">{{ $karyawan->updated_at->format('d M Y H:i') }}</p>
                                <p class="text-xs text-blue-700 mt-1 font-medium">Profil Anda disimpan untuk kesempatan mendatang</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="glass-card rounded-2xl shadow-lg p-6 hover-lift" style="border: 1px solid rgba(0, 128, 0, 0.3);">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-bolt text-blue-600 mr-2"></i>
                        Aksi Cepat
                    </h3>
                    <div class="space-y-3">
                        <!-- Tombol Lamar Posisi Lain -->
                        <a href="{{ route('kandidat.formkaryawan.index', ['karyawan_id' => $karyawan->id]) }}" class="block w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white text-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 hover:scale-105">
                            <i class="fas fa-plus mr-2"></i> Lamar Posisi Lain
                        </a>
                        <button onclick="window.print()" class="block w-full bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white text-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 hover:scale-105">
                            <i class="fas fa-print mr-2"></i> Cetak Data
                        </button>
                        
                        <a href="" class="block w-full bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white text-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 hover:scale-105">
                            <i class="fas fa-download mr-2"></i> Download PDF
                        </a>
                    </div>
                </div>

                <!-- Emergency Contact -->
                @if($karyawan->kontak_darurat)
                <div class="glass-card rounded-2xl shadow-lg p-6 hover-lift" style="border: 1px solid rgba(0, 128, 0, 0.3);">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-phone text-red-600 mr-2"></i>
                        Kontak Darurat
                    </h3>
                    @foreach($karyawan->kontak_darurat as $kontak)
                    <div class="mb-4 p-3 bg-red-50 rounded-xl">
                        <p class="font-bold text-gray-900">{{ $kontak['nama'] ?? '' }}</p>
                        <p class="text-sm text-gray-600">{{ $kontak['hubungan'] ?? '' }}</p>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-phone mr-1"></i>{{ $kontak['telepon'] ?? '' }}
                        </p>
                    </div>
                    @endforeach
                </div>
                @endif

                
            </div>
        </div>
    </div>

    <script>
        // Smooth scroll animation
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.hover-lift');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('fade-in');
            });
        });

        // Auto-refresh status setiap 30 detik
        setInterval(function() {
            // Implementasi refresh status jika diperlukan
        }, 30000);
    </script>
</body>
</html>