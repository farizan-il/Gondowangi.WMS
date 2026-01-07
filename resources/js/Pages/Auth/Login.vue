<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br px-4 py-8" style="background-color: #035930;">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-6xl w-full">
            <!-- Auth Credentials Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8 h-fit">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Akun Testing
                </h3>
                <p class="text-sm text-gray-600 mb-4">Pilih salah satu akun di bawah untuk langsung login:</p>
                
                <div class="space-y-3">
                    <!-- IT Account -->
                    <div class="border border-blue-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="font-semibold text-blue-700">IT</p>
                                <p class="text-xs text-gray-600">it@gondowangi.com</p>
                            </div>
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded font-semibold">Full Access</span>
                        </div>
                        <button 
                            @click="quickLogin('it@gondowangi.com', 'gondowangi-123')"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 rounded transition"
                        >
                            Login
                        </button>
                    </div>

                    <!-- Logistik SPV Account -->
                    <div class="border border-purple-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="font-semibold text-purple-700">Logistik SPV</p>
                                <p class="text-xs text-gray-600">spv.log@gondowangi.com</p>
                            </div>
                            <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded font-semibold">Read+Write</span>
                        </div>
                        <button 
                            @click="quickLogin('spv.log@gondowangi.com', 'gondowangi-123')"
                            class="w-full bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold py-2 rounded transition"
                        >
                            Login
                        </button>
                    </div>

                    <!-- Logistik Admin Account -->
                    <div class="border border-green-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="font-semibold text-green-700">Logistik Admin</p>
                                <p class="text-xs text-gray-600">adm.log@gondowangi.com</p>
                            </div>
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded font-semibold">Admin</span>
                        </div>
                        <button 
                            @click="quickLogin('adm.log@gondowangi.com', 'gondowangi-123')"
                            class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 rounded transition"
                        >
                            Login
                        </button>
                    </div>

                    <!-- Logistik Operator Account -->
                    <div class="border border-orange-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="font-semibold text-orange-700">Logistik Operator</p>
                                <p class="text-xs text-gray-600">op.log@gondowangi.com</p>
                            </div>
                            <span class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded font-semibold">Operator</span>
                        </div>
                        <button 
                            @click="quickLogin('op.log@gondowangi.com', 'gondowangi-123')"
                            class="w-full bg-orange-600 hover:bg-orange-700 text-white text-sm font-semibold py-2 rounded transition"
                        >
                            Login
                        </button>
                    </div>

                    <!-- QAC Account -->
                    <div class="border border-red-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="font-semibold text-red-700">QAC</p>
                                <p class="text-xs text-gray-600">qac@gondowangi.com</p>
                            </div>
                            <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded font-semibold">QC Only</span>
                        </div>
                        <button 
                            @click="quickLogin('qac@gondowangi.com', 'gondowangi-123')"
                            class="w-full bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2 rounded transition"
                        >
                            Login
                        </button>
                    </div>

                    <!-- Produksi Account -->
                    <div class="border border-indigo-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="font-semibold text-indigo-700">Produksi</p>
                                <p class="text-xs text-gray-600">prod@gondowangi.com</p>
                            </div>
                            <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded font-semibold">Limited</span>
                        </div>
                        <button 
                            @click="quickLogin('prod@gondowangi.com', 'gondowangi-123')"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2 rounded transition"
                        >
                            Login
                        </button>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-xs text-blue-700">
                        <span class="font-semibold">💡 Tips:</span> Klik tombol "Login" untuk langsung login dengan akun tersebut
                    </p>
                </div>
            </div>

            <!-- Login Form Card -->
            <div class="max-w-md w-full">

            <!-- Login Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Login</h2>
                
                <form @submit.prevent="submit" class="space-y-5">
                    <!-- NIK/Email Input -->
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">
                            NIK atau Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input 
                                v-model="form.identifier"
                                type="text" 
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                :class="{ 'border-red-500': form.errors.identifier }"
                                placeholder="Masukkan NIK atau Email"
                                required
                            >
                        </div>
                        <div v-if="form.errors.identifier" class="text-red-500 text-sm mt-1 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ form.errors.identifier }}
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input 
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                placeholder="Masukkan password"
                                required
                            >
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                            >
                                <svg v-if="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Database Selection -->
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">
                            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                            </svg>
                            Pilih Database
                        </label>
                        <div class="space-y-2">
                            <label 
                                class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all hover:shadow-md"
                                :class="form.database_connection === 'mysql' ? 'border-green-500 bg-green-50' : 'border-gray-300 bg-white hover:border-green-300'"
                            >
                                <input 
                                    v-model="form.database_connection"
                                    type="radio" 
                                    value="mysql"
                                    class="w-4 h-4 text-green-600 focus:ring-green-500"
                                >
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center">
                                        <span class="font-semibold text-gray-800">Production Database</span>
                                        <span class="ml-2 px-2 py-0.5 bg-green-100 text-green-800 text-xs font-semibold rounded">MAIN</span>
                                    </div>
                                    <span class="text-xs text-gray-600">Database utama untuk operasional</span>
                                </div>
                            </label>
                            
                            <label 
                                class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all hover:shadow-md"
                                :class="form.database_connection === 'mysql_testing' ? 'border-orange-500 bg-orange-50' : 'border-gray-300 bg-white hover:border-orange-300'"
                            >
                                <input 
                                    v-model="form.database_connection"
                                    type="radio" 
                                    value="mysql_testing"
                                    class="w-4 h-4 text-orange-600 focus:ring-orange-500"
                                >
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center">
                                        <span class="font-semibold text-gray-800">Testing Database</span>
                                        <span class="ml-2 px-2 py-0.5 bg-orange-100 text-orange-800 text-xs font-semibold rounded">TEST</span>
                                    </div>
                                    <span class="text-xs text-gray-600">Database untuk testing dan percobaan</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer">
                            <input 
                                v-model="form.remember"
                                type="checkbox" 
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            >
                            <span class="ml-2 text-sm text-gray-700">Ingat Saya</span>
                        </label>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                            Lupa Password?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                    >
                        <span v-if="form.processing" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                        <span v-else class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Login
                        </span>
                    </button>
                </form>

                <!-- Register Link -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Belum punya akun? 
                        <Link href="/register" class="text-blue-600 hover:text-blue-800 font-semibold hover:underline">
                            Daftar Sekarang
                        </Link>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 text-blue-100 text-sm">
                <p>© 2025 Gondowangi WMS. All rights reserved.</p>
            </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, nextTick } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';

const showPassword = ref(false);

const form = useForm({
    identifier: '',
    password: '',
    remember: false,
    database_connection: 'mysql', // Default to production
});

const submit = () => {
    form.post('/login', {
        onFinish: () => {
            // Reset password jika login gagal
            if (Object.keys(form.errors).length > 0) {
                form.password = '';
            }
        },
    });
};

const quickLogin = (email, password) => {
    // Set form data
    form.identifier = email;
    form.password = password;
    form.remember = true;
    form.database_connection = 'mysql';
    
    // Wait for form to be reactive, then submit
    nextTick(() => {
        submit();
    });
};
</script>