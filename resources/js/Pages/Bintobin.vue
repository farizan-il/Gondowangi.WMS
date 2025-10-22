<template>
    <AppLayout title="Bin to Bin Transfer">
        <div class="py-8 bg-gradient-to-br from-slate-50 via-blue-50 to-slate-100 min-h-screen">
            <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Header Section with Sidebar Toggle -->
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1
                            class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">
                            Sistem Bin to Bin Transfer
                        </h1>
                        <p class="text-gray-600 text-lg">Real-time warehouse material movement tracking system</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Toggle Sidebar Button -->
                        <button @click="toggleSidebar"
                            class="relative px-6 py-3 bg-white rounded-xl shadow-lg border border-gray-200 hover:bg-gray-50 transition-all flex items-center space-x-2 group">
                            <svg class="w-6 h-6 text-gray-700 group-hover:text-blue-600 transition-colors" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            <span class="font-semibold text-gray-700 group-hover:text-blue-600">Dashboard</span>
                            <!-- Notification Badge -->
                            <span v-if="transferHistory.length > 0"
                                class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse">
                                {{ transferHistory.length }}
                            </span>
                        </button>

                        <div class="bg-white px-6 py-3 rounded-xl shadow-lg border border-gray-200">
                            <div class="text-xs text-gray-500 mb-1">Tanggal</div>
                            <div class="font-semibold text-gray-900">{{ currentDate }}</div>
                        </div>
                        <div
                            class="bg-gradient-to-r from-green-500 to-emerald-500 px-6 py-3 rounded-xl shadow-lg text-white">
                            <div class="text-xs opacity-90 mb-1">Status Sistem</div>
                            <div class="font-bold flex items-center">
                                <span class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></span>
                                ONLINE
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content (Full Width) -->
                <div class="space-y-6">

                    <!-- Progress Steps -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                        <!-- Progress steps content sama seperti sebelumnya -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center flex-1">
                                <div class="flex items-center"
                                    :class="currentStep === 'scan_material' || currentStep === 'scan_destination' || currentStep === 'complete' ? 'text-blue-600' : 'text-gray-400'">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg border-2"
                                        :class="currentStep === 'scan_material' || currentStep === 'scan_destination' || currentStep === 'complete' ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-200 text-gray-400 border-gray-300'">
                                        <span v-if="currentStep === 'scan_material'">1</span>
                                        <svg v-else class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-bold">Scan Material</p>
                                        <p class="text-xs">Scan kode material</p>
                                    </div>
                                </div>

                                <div class="flex-1 h-1 mx-4"
                                    :class="currentStep === 'scan_destination' || currentStep === 'complete' ? 'bg-blue-600' : 'bg-gray-300'">
                                </div>

                                <div class="flex items-center"
                                    :class="currentStep === 'scan_destination' || currentStep === 'complete' ? 'text-blue-600' : 'text-gray-400'">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg border-2"
                                        :class="currentStep === 'scan_destination' || currentStep === 'complete' ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-200 text-gray-400 border-gray-300'">
                                        <span v-if="currentStep !== 'complete'">2</span>
                                        <svg v-else class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-bold">Scan Bin Tujuan</p>
                                        <p class="text-xs">Scan lokasi tujuan</p>
                                    </div>
                                </div>

                                <div class="flex-1 h-1 mx-4"
                                    :class="currentStep === 'complete' ? 'bg-green-600' : 'bg-gray-300'"></div>

                                <div class="flex items-center"
                                    :class="currentStep === 'complete' ? 'text-green-600' : 'text-gray-400'">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg border-2"
                                        :class="currentStep === 'complete' ? 'bg-green-600 text-white border-green-600' : 'bg-gray-200 text-gray-400 border-gray-300'">
                                        ✓
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-bold">Konfirmasi</p>
                                        <p class="text-xs">Selesaikan transfer</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Scanner Card -->
                    <div
                        class="bg-gradient-to-br from-white to-blue-50 rounded-2xl shadow-2xl p-8 border-2 border-blue-200">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 mb-1">
                                    {{ currentStep === 'scan_material' ? '📦 Scan Material' : currentStep ===
                                        'scan_destination' ? '📍 Scan Bin Tujuan' : '✅ Siap Konfirmasi' }}
                                </h2>
                                <p class="text-gray-600">
                                    {{ currentStep === 'scan_material' ? 'Pilih metode scanning untuk material' :
                                        currentStep === 'scan_destination' ? 'Pilih metode scanning untuk bin tujuan' :
                                            'Klik tombol konfirmasi untuk menyelesaikan' }}
                                </p>
                            </div>
                        </div>

                        <!-- Camera View -->
                        <div v-if="showCamera" class="mb-6">
                            <div
                                class="bg-black rounded-2xl overflow-hidden relative border-4 border-blue-500 shadow-2xl">
                                <video ref="videoElement" autoplay playsinline class="w-full h-96 object-cover"></video>

                                <!-- Scanning Frame Overlay -->
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div class="relative">
                                        <!-- Scanning Frame -->
                                        <div class="w-80 h-48 border-4 border-red-500 rounded-xl relative">
                                            <!-- Corner Decorations -->
                                            <div
                                                class="absolute -top-1 -left-1 w-8 h-8 border-t-8 border-l-8 border-red-500 rounded-tl-xl">
                                            </div>
                                            <div
                                                class="absolute -top-1 -right-1 w-8 h-8 border-t-8 border-r-8 border-red-500 rounded-tr-xl">
                                            </div>
                                            <div
                                                class="absolute -bottom-1 -left-1 w-8 h-8 border-b-8 border-l-8 border-red-500 rounded-bl-xl">
                                            </div>
                                            <div
                                                class="absolute -bottom-1 -right-1 w-8 h-8 border-b-8 border-r-8 border-red-500 rounded-br-xl">
                                            </div>

                                            <!-- Scanning Line -->
                                            <div v-if="isCameraScanning" class="absolute inset-0">
                                                <div class="camera-scan-line"></div>
                                            </div>
                                        </div>

                                        <!-- Instruction Text -->
                                        <div class="mt-4 text-center">
                                            <p
                                                class="text-white text-lg font-bold bg-black bg-opacity-70 px-6 py-3 rounded-full inline-block">
                                                {{ isCameraScanning ? '🔍 Scanning...' : '📱 Arahkan Barcode ke Frame'
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <canvas ref="canvasElement" class="hidden"></canvas>

                                <!-- Camera Controls -->
                                <div class="absolute top-4 right-4 flex space-x-2">
                                    <button @click="stopCamera"
                                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-all shadow-lg flex items-center">
                                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Tutup
                                    </button>
                                </div>
                            </div>
                            <p class="text-center text-gray-600 mt-4 text-sm flex items-center justify-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                Kamera Aktif - Deteksi barcode otomatis
                            </p>
                        </div>

                        <!-- Manual Input -->
                        <div v-if="!showCamera" class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                💡 Input Manual - Ketik kode barcode
                            </label>
                            <div class="relative">
                                <input v-model="barcodeInput" @keyup.enter="handleManualScan" type="text"
                                    :placeholder="currentStep === 'scan_material' ? 'Contoh: MAT001, MAT002, MAT003...' : 'Contoh: A1, B2, C3...'"
                                    class="w-full px-6 py-5 bg-white border-3 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-500 focus:border-blue-500 text-gray-900 text-xl font-mono placeholder-gray-400 transition-all shadow-inner"
                                    :disabled="currentStep === 'complete' || isScanning" ref="barcodeInputRef" />
                                <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                                    <div v-if="isScanning"
                                        class="w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin">
                                    </div>
                                    <svg v-else class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Camera Button -->
                            <button @click="toggleCamera" :disabled="currentStep === 'complete'"
                                class="py-5 px-6 rounded-xl font-bold text-lg transition-all duration-200 shadow-lg flex items-center justify-center"
                                :class="currentStep === 'complete'
                                    ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                    : showCamera
                                        ? 'bg-red-600 hover:bg-red-700 text-white transform hover:scale-105'
                                        : 'bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white transform hover:scale-105'">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ showCamera ? 'Stop Kamera' : 'Buka Kamera' }}
                            </button>

                            <!-- Manual Scan Button -->
                            <button @click="handleManualScan"
                                :disabled="!barcodeInput || currentStep === 'complete' || isScanning || showCamera"
                                class="py-5 px-6 rounded-xl font-bold text-lg transition-all duration-200 shadow-lg flex items-center justify-center"
                                :class="!barcodeInput || currentStep === 'complete' || isScanning || showCamera
                                    ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                    : 'bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white transform hover:scale-105'">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <span v-if="!isScanning">Scan Manual</span>
                                <span v-else>Processing...</span>
                            </button>
                        </div>

                        <!-- Reset Button -->
                        <button v-if="scannedMaterial && currentStep !== 'complete'" @click="resetTransfer"
                            class="w-full mt-4 py-4 px-6 bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white font-bold rounded-xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Reset Transfer
                        </button>

                        <!-- Alert Messages -->
                        <transition name="slide-fade">
                            <div v-if="alertMessage"
                                class="mt-6 p-5 rounded-xl font-semibold flex items-center shadow-lg border-2"
                                :class="alertClass">
                                <span class="text-3xl mr-3">{{ alertType === 'success' ? '✅' : '❌' }}</span>
                                <span class="flex-1">{{ alertMessage }}</span>
                                <button @click="alertMessage = ''" class="ml-2 hover:opacity-70">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </transition>
                    </div>

                    <!-- Material Details Card -->
                    <transition name="scale-fade">
                        <div v-if="scannedMaterial"
                            class="bg-white rounded-2xl shadow-2xl overflow-hidden border-2 border-gray-200">
                            <div class="bg-gradient-to-r from-indigo-600 to-blue-600 p-6">
                                <h3 class="text-2xl font-bold text-white flex items-center">
                                    <span
                                        class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-3 text-3xl">📦</span>
                                    Informasi Material
                                </h3>
                            </div>

                            <div class="p-8 space-y-6">
                                <!-- Material Header -->
                                <div class="grid grid-cols-2 gap-6">
                                    <div
                                        class="bg-gradient-to-br from-blue-50 to-indigo-100 p-6 rounded-2xl border-2 border-blue-300 shadow-lg">
                                        <p class="text-sm text-blue-700 mb-2 font-bold uppercase tracking-wide">Kode
                                            Material</p>
                                        <p class="text-4xl font-black text-blue-600">{{ scannedMaterial.code }}</p>
                                    </div>
                                    <div
                                        class="bg-gradient-to-br from-green-50 to-emerald-100 p-6 rounded-2xl border-2 border-green-300 shadow-lg">
                                        <p class="text-sm text-green-700 mb-2 font-bold uppercase tracking-wide">Jumlah
                                        </p>
                                        <p class="text-4xl font-black text-green-600">{{ scannedMaterial.quantity }}
                                            <span class="text-2xl">{{ scannedMaterial.unit }}</span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Material Info Grid -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div
                                        class="bg-gradient-to-br from-gray-50 to-gray-100 p-5 rounded-xl border-2 border-gray-200 shadow">
                                        <div class="flex items-center mb-2">
                                            <span class="w-3 h-3 bg-purple-500 rounded-full mr-2"></span>
                                            <p class="text-xs text-gray-600 font-bold uppercase tracking-wide">Nama
                                                Material</p>
                                        </div>
                                        <p class="font-bold text-gray-900 text-lg">{{ scannedMaterial.name }}</p>
                                    </div>

                                    <div
                                        class="bg-gradient-to-br from-gray-50 to-gray-100 p-5 rounded-xl border-2 border-gray-200 shadow">
                                        <div class="flex items-center mb-2">
                                            <span class="w-3 h-3 bg-pink-500 rounded-full mr-2"></span>
                                            <p class="text-xs text-gray-600 font-bold uppercase tracking-wide">Kategori
                                            </p>
                                        </div>
                                        <span
                                            class="inline-block px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg text-sm font-bold shadow-md">
                                            {{ scannedMaterial.category }}
                                        </span>
                                    </div>

                                    <div
                                        class="bg-gradient-to-br from-gray-50 to-gray-100 p-5 rounded-xl border-2 border-gray-200 shadow">
                                        <div class="flex items-center mb-2">
                                            <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                                            <p class="text-xs text-gray-600 font-bold uppercase tracking-wide">Batch
                                                Number</p>
                                        </div>
                                        <p class="font-bold text-gray-900 text-lg">{{ scannedMaterial.batchNo }}</p>
                                    </div>

                                    <div
                                        class="bg-gradient-to-br from-gray-50 to-gray-100 p-5 rounded-xl border-2 border-gray-200 shadow">
                                        <div class="flex items-center mb-2">
                                            <span class="w-3 h-3 bg-orange-500 rounded-full mr-2"></span>
                                            <p class="text-xs text-gray-600 font-bold uppercase tracking-wide">Tanggal
                                                Kadaluarsa</p>
                                        </div>
                                        <p class="font-bold text-gray-900 text-lg">{{ scannedMaterial.expiryDate }}</p>
                                    </div>
                                </div>

                                <!-- Transfer Route -->
                                <div
                                    class="bg-gradient-to-r from-amber-50 via-yellow-50 to-orange-50 p-8 rounded-2xl border-3 border-amber-300 shadow-xl">
                                    <p
                                        class="text-sm font-bold text-amber-900 mb-6 uppercase tracking-wider flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                        Rute Perpindahan
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="text-xs text-amber-700 mb-3 font-semibold uppercase">Dari Bin</p>
                                            <div
                                                class="bg-white px-8 py-6 rounded-2xl shadow-lg border-3 border-amber-400">
                                                <p class="text-4xl font-black text-amber-600">{{
                                                    scannedMaterial.currentBin }}</p>
                                            </div>
                                        </div>
                                        <div class="px-6">
                                            <svg class="w-16 h-16 text-amber-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-xs text-amber-700 mb-3 font-semibold uppercase">Ke Bin</p>
                                            <div v-if="destinationBin"
                                                class="bg-white px-8 py-6 rounded-2xl shadow-lg border-3 border-green-400">
                                                <p class="text-4xl font-black text-green-600">{{ destinationBin }}</p>
                                            </div>
                                            <div v-else
                                                class="bg-gray-100 px-8 py-6 rounded-2xl shadow-lg border-3 border-gray-300">
                                                <p class="text-4xl font-black text-gray-400">???</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Confirm Button -->
                                <div v-if="destinationBin && currentStep === 'complete'" class="pt-2">
                                    <button @click="completeTransfer"
                                        class="w-full py-6 px-8 bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 hover:from-green-700 hover:via-emerald-700 hover:to-teal-700 text-white font-black text-2xl rounded-2xl transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-2xl flex items-center justify-center">
                                        <svg class="w-10 h-10 mr-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        KONFIRMASI TRANSFER
                                    </button>
                                </div>
                            </div>
                        </div>
                    </transition>

                    <div
                        class="bg-gradient-to-br from-white to-blue-50 rounded-2xl shadow-2xl p-8 border-2 border-blue-200">
                        <div class="text-center py-12">
                            <p class="text-gray-500">Scanner Card Content - Gunakan kode yang sudah ada</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Blur Overlay -->
        <transition name="fade">
            <div v-if="sidebarOpen" @click="closeSidebar"
                class="fixed inset-0 bg-opacity-50 backdrop-blur-sm z-40 transition-all duration-300" style="background-color: #00000054;"></div>
        </transition>

        <!-- Off-Canvas Sidebar -->
        <transition name="slide"> 
            <div v-if="sidebarOpen"
                class="fixed top-0 right-0 h-full w-full md:w-[600px] lg:w-[800px] bg-white shadow-2xl z-50 overflow-y-auto">
                <!-- Sidebar Header -->
                <div
                    class="sticky top-0  p-6 flex items-center justify-between z-10 shadow-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-green bg-opacity-20 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold">Dashboard</h2>
                            <p class="text-black-100 text-sm">Real-time Statistics</p>
                        </div>
                    </div>
                    <button @click="closeSidebar"
                        class="w-10 h-10 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg flex items-center justify-center transition-all">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Sidebar Content -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Transfer History -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                <span
                                    class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-2">📜</span>
                                Riwayat Transfer
                            </h3>
                            <span class="px-3 py-1 bg-gray-100 rounded-lg text-sm font-semibold text-gray-600">
                                {{ transferHistory.length }}
                            </span>
                        </div>

                        <div v-if="transferHistory.length === 0" class="text-center py-12">
                            <div
                                class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <p class="text-gray-400 font-semibold">Belum ada transfer</p>
                            <p class="text-gray-400 text-sm mt-1">Mulai scan untuk membuat transfer</p>
                        </div>

                        <div v-else class="space-y-3 max-h-[800px] overflow-y-auto custom-scrollbar">
                            <transition-group name="list">
                                <div v-for="(transfer, index) in transferHistory.slice().reverse()" :key="index"
                                    class="p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border-2 border-gray-200 hover:border-blue-300 hover:shadow-lg transition-all duration-200">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1">
                                            <p class="font-bold text-gray-900 mb-1">{{ transfer.materialName }}</p>
                                            <p class="text-xs text-gray-500 font-mono">{{ transfer.materialCode }}</p>
                                        </div>
                                        <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded-lg">{{
                                            transfer.timestamp }}</span>
                                    </div>

                                    <div class="flex items-center justify-between bg-white p-3 rounded-lg mb-2">
                                        <span
                                            class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-lg text-sm font-bold">
                                            {{ transfer.fromBin }}
                                        </span>
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                        <span
                                            class="px-3 py-1 bg-green-100 text-green-800 rounded-lg text-sm font-bold">
                                            {{ transfer.toBin }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">Qty:</span>
                                        <span class="font-bold text-gray-900">{{ transfer.quantity }} {{ transfer.unit
                                            }}</span>
                                    </div>
                                </div>
                            </transition-group>
                        </div>
                    </div>

                    <!-- Bin Status -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <span
                                class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-2">🏪</span>
                            Status Kapasitas Bin
                        </h3>
                        <div class="space-y-3">
                            <div v-for="bin in bins" :key="bin.code"
                                class="p-4 bg-gray-50 rounded-xl border-2 hover:shadow-md transition-all"
                                :class="bin.capacity > 80 ? 'border-red-300 bg-red-50' : 'border-gray-200'">
                                <div class="flex justify-between items-center mb-2">
                                    <div>
                                        <span class="font-bold text-lg"
                                            :class="bin.capacity > 80 ? 'text-red-600' : 'text-gray-900'">
                                            {{ bin.code }}
                                        </span>
                                        <p class="text-xs text-gray-500 mt-1">{{ bin.location }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-black"
                                            :class="bin.capacity > 80 ? 'text-red-600' : 'text-gray-700'">
                                            {{ bin.capacity }}%
                                        </span>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500"
                                        :class="bin.capacity > 80 ? 'bg-red-500' : bin.capacity > 60 ? 'bg-yellow-500' : 'bg-green-500'"
                                        :style="{ width: bin.capacity + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, nextTick, computed, watch, watchEffect } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm, router, usePage } from '@inertiajs/vue3'
import jsQR from "jsqr"; // <-- 1. IMPORT jsQR

// 1. Terima data dari Inertia (Laravel Controller)
const props = defineProps({
    title: String,
    initialBins: Array,
    initialTransferHistory: Array,
    scannedMaterial: Object,
    destinationBin: Object,
    errors: Object,
});

const page = usePage();

// Sidebar State
const sidebarOpen = ref(false)
const toggleSidebar = () => sidebarOpen.value = !sidebarOpen.value
const closeSidebar = () => sidebarOpen.value = false

// State Management
const barcodeInput = ref('')
const barcodeInputRef = ref(null)
const currentStep = ref('scan_material')
const alertMessage = ref('')
const alertType = ref('success')
const isScanning = ref(false) // Untuk loading manual scan
const showCamera = ref(false)
const videoElement = ref(null)
const canvasElement = ref(null) // Pastikan Anda punya <canvas ref="canvasElement" class="hidden"></canvas> di template
const cameraStream = ref(null)
const animationFrameId = ref(null) // <-- Ganti scanInterval
const isCameraScanning = ref(false) // <-- Untuk animasi "Scanning..." di overlay

const transferHistory = ref(props.initialTransferHistory)
const bins = ref(props.initialBins)
const localScannedMaterial = ref(props.scannedMaterial)
const localDestinationBin = ref(props.destinationBin)

const form = useForm({
    from_bin_id: null,
    to_bin_id: null,
    stock_id: null,
    quantity: null,
})

// Watcher untuk update state lokal JIKA props berubah
watch(() => props.scannedMaterial, (newValue) => {
    localScannedMaterial.value = newValue
})
watch(() => props.destinationBin, (newValue) => {
    localDestinationBin.value = newValue
})
watch(() => props.initialTransferHistory, (newValue) => {
    transferHistory.value = newValue
})
watch(() => props.initialBins, (newValue) => {
    bins.value = newValue
})

// Watcher untuk menampilkan flash messages dari Laravel
watch(() => page.props.flash, (flash) => {
    if (flash?.success) {
        showAlert(flash.success, 'success');
    } else if (flash?.error) {
        showAlert(flash.error, 'error');
    }
}, { deep: true });

// WatchEffect untuk meng-update 'currentStep' dan 'form' secara reaktif
watchEffect(() => {
    if (localScannedMaterial.value && localDestinationBin.value) {
        currentStep.value = 'complete'
        form.stock_id = localScannedMaterial.value.stock_id
        form.from_bin_id = localScannedMaterial.value.current_bin_id
        form.quantity = localScannedMaterial.value.quantity
        form.to_bin_id = localDestinationBin.value.id
    } else if (localScannedMaterial.value) {
        currentStep.value = 'scan_destination'
        if (!alertMessage.value && !page.props.flash?.error) {
             showAlert(`✅ Material ${localScannedMaterial.value.name} (Batch: ${localScannedMaterial.value.batchNo}) berhasil di-scan! Silakan scan bin tujuan.`, 'success')
        }
    } else {
        currentStep.value = 'scan_material'
    }
})

// Current Date
const currentDate = computed(() => {
    const now = new Date()
    return now.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
})

// --- METHODS ---

// 2. PERBAIKI 'handleScan' (Hapus 'route()')
const handleScan = () => {
    if (isScanning.value || !barcodeInput.value.trim()) {
        if (!barcodeInput.value.trim()) {
            showAlert('Silakan scan atau masukkan kode barcode', 'error');
        }
        return;
    }

    const code = barcodeInput.value.trim().toUpperCase();
    isScanning.value = true;
    alertMessage.value = '';

    let data = {};
    // Gunakan URL string, BUKAN route()
    let url = '/transaction/bin-to-bin';

    if (currentStep.value === 'scan_material') {
        data = { material_batch: code };
    } else if (currentStep.value === 'scan_destination') {
        data = {
            material_batch: localScannedMaterial.value.batchNo,
            bin_code: code
        };
    }

    router.get(url, data, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            isScanning.value = false;
            barcodeInput.value = '';
            focusInput();
        },
        onError: (errors) => {
            showAlert('❌ Gagal memproses: ' + (Object.values(errors)[0] || 'Error server'), 'error');
        }
    });
}

const handleManualScan = () => {
    handleScan()
}

// 3. PERBAIKI 'completeTransfer' (Hapus 'route()')
const completeTransfer = () => {
    if (form.processing) return;

    // Gunakan URL string, BUKAN route()
    form.post('/transaction/bin-to-bin/transfer', {
        preserveScroll: true,
        onSuccess: () => {
            // Reset otomatis karena props akan kosong
        },
        onError: (errors) => {
            const errorMsg = Object.values(errors).join(' \n')
            showAlert(`❌ Gagal Konfirmasi: ${errorMsg}`, 'error')
        }
    })
}

// 4. PERBAIKI 'resetTransfer' (Hapus 'route()')
const resetTransfer = () => {
    // Gunakan URL string, BUKAN route()
    router.get('/bin-to-bin', {}, { // <-- PERBAIKAN ERROR
        preserveState: false,
    });
}

const showAlert = (message, type) => {
    alertMessage.value = message
    alertType.value = type
    setTimeout(() => {
        if (alertMessage.value === message) {
            alertMessage.value = ''
        }
    }, 6000)
}

const focusInput = () => {
    nextTick(() => {
        if (barcodeInputRef.value && !showCamera.value) {
            barcodeInputRef.value.focus()
        }
    })
}


// --- 5. PERBAIKI LOGIKA KAMERA ---

const toggleCamera = async () => {
    if (showCamera.value) {
        stopCamera()
    } else {
        await startCamera()
    }
}

const startCamera = async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        })

        cameraStream.value = stream
        showCamera.value = true

        await nextTick()

        if (videoElement.value) {
            videoElement.value.srcObject = stream
            videoElement.value.play()
            
            // Mulai loop deteksi
            tick(); // Panggil fungsi tick
            
            showAlert('📷 Kamera aktif! Arahkan barcode ke kamera.', 'success')
        }
    } catch (error) {
        console.error('Camera error:', error)
        if (error.name === "NotAllowedError") {
             showAlert('❌ Izin kamera ditolak. Harap izinkan akses kamera di browser Anda.', 'error')
        } else {
             showAlert('❌ Tidak dapat mengakses kamera. Pastikan Anda menggunakan HTTPS.', 'error')
        }
    }
}

const stopCamera = () => {
    if (cameraStream.value) {
        cameraStream.value.getTracks().forEach(track => track.stop())
        cameraStream.value = null
    }

    // Hentikan loop deteksi
    if (animationFrameId.value) {
        cancelAnimationFrame(animationFrameId.value)
        animationFrameId.value = null
    }

    showCamera.value = false
    isCameraScanning.value = false

    if (videoElement.value) {
        videoElement.value.srcObject = null
    }
    focusInput(); // Fokus kembali ke input manual setelah kamera ditutup
}

// Fungsi 'tick' untuk loop deteksi QR
const tick = () => {
    if (!showCamera.value || !videoElement.value || !canvasElement.value) {
        return;
    }

    isCameraScanning.value = false;

    // Pastikan video sudah siap
    if (videoElement.value.readyState === videoElement.value.HAVE_ENOUGH_DATA) {
        const canvas = canvasElement.value;
        const ctx = canvas.getContext('2d', { willReadFrequently: true }); // Optimasi

        canvas.height = videoElement.value.videoHeight;
        canvas.width = videoElement.value.videoWidth;
        
        // Gambar frame video ke canvas
        ctx.drawImage(videoElement.value, 0, 0, canvas.width, canvas.height);
        
        // Ambil data gambar dari canvas
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        
        // Coba deteksi QR code menggunakan jsQR
        const code = jsQR(imageData.data, imageData.width, imageData.height, {
            inversionAttempts: "dontInvert",
        });

        isCameraScanning.value = true; // Tampilkan animasi scanning line

        if (code) {
            // JIKA BERHASIL TERDETEKSI
            isCameraScanning.value = false;
            stopCamera(); // Matikan kamera
            barcodeInput.value = code.data; // Masukkan hasil scan ke input
            handleScan(); // Panggil handleScan untuk diproses
            return; // Hentikan loop
        }
    }

    // Lanjutkan ke frame berikutnya
    animationFrameId.value = requestAnimationFrame(tick);
}

// HAPUS 'startBarcodeDetection' dan 'simulateCameraScan' (diganti 'tick')

// Computed
const alertClass = computed(() => {
    return alertType.value === 'success'
        ? 'bg-green-500 text-white border-2 border-green-600'
        : 'bg-red-500 text-white border-2 border-red-600'
})

// Lifecycle
onMounted(() => {
    focusInput()
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebarOpen.value) {
            closeSidebar()
        }
    })

    if (page.props.flash?.success) {
        showAlert(page.props.flash.success, 'success');
    } else if (page.props.flash?.error) {
        showAlert(page.props.flash.error, 'error');
    }
})

// Cleanup camera on unmount
const cleanup = () => {
    stopCamera()
}

if (typeof window !== 'undefined') {
    window.addEventListener('beforeunload', cleanup)
}
</script>

<style scoped>
/* Scanning Laser Animation */
.scan-line {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, transparent, #3b82f6, transparent);
    animation: scan 2s linear infinite;
    box-shadow: 0 0 20px #3b82f6;
}

@keyframes scan {
    0% {
        top: 0;
    }

    100% {
        top: 100%;
    }
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #94a3b8;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #64748b;
}

/* Transitions */
.slide-fade-enter-active {
    transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
    transition: all 0.2s ease-in;
}

.slide-fade-enter-from {
    transform: translateY(-20px);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateY(-10px);
    opacity: 0;
}

.scale-fade-enter-active {
    transition: all 0.4s ease-out;
}

.scale-fade-leave-active {
    transition: all 0.3s ease-in;
}

.scale-fade-enter-from {
    transform: scale(0.95);
    opacity: 0;
}

.scale-fade-leave-to {
    transform: scale(0.95);
    opacity: 0;
}

.list-enter-active,
.list-leave-active {
    transition: all 0.4s ease;
}

.list-enter-from {
    opacity: 0;
    transform: translateX(-30px);
}

.list-leave-to {
    opacity: 0;
    transform: translateX(30px);
}

.list-move {
    transition: transform 0.4s ease;
}

/* Fade Transition for Overlay */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Slide Transition for Sidebar */
.slide-enter-active {
  transition: transform 0.3s ease-out;
}

.slide-leave-active {
  transition: transform 0.25s ease-in;
}

.slide-enter-from {
  transform: translateX(100%);
}

.slide-leave-to {
  transform: translateX(100%);
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #94a3b8;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #64748b;
}

/* List Transitions */
.list-enter-active,
.list-leave-active {
  transition: all 0.4s ease;
}

.list-enter-from {
  opacity: 0;
  transform: translateX(-30px);
}

.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

/* Prevent body scroll when sidebar is open */
body:has(.slide-enter-active),
body:has(.slide-leave-active) {
  overflow: hidden;
}
</style>