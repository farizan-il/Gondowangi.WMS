<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-center mb-6">Daftar Akun WMS</h2>
            
            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        NIK <span class="text-red-500">*</span>
                    </label>
                    <input 
                        v-model="form.nik"
                        type="text" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan NIK"
                        required
                    >
                    <div v-if="form.errors.nik" class="text-red-500 text-sm mt-1">
                        {{ form.errors.nik }}
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input 
                        v-model="form.nama_lengkap"
                        type="text" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan nama lengkap"
                        required
                    >
                    <div v-if="form.errors.nama_lengkap" class="text-red-500 text-sm mt-1">
                        {{ form.errors.nama_lengkap }}
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input 
                        v-model="form.email"
                        type="email" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan email"
                        required
                    >
                    <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                        {{ form.errors.email }}
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Departement
                    </label>
                    <input 
                        v-model="form.departement"
                        type="text" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: Produksi, Warehouse, QC"
                    >
                    <div v-if="form.errors.departement" class="text-red-500 text-sm mt-1">
                        {{ form.errors.departement }}
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Jabatan
                    </label>
                    <input 
                        v-model="form.jabatan"
                        type="text" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: Staff, Supervisor, Manager"
                    >
                    <div v-if="form.errors.jabatan" class="text-red-500 text-sm mt-1">
                        {{ form.errors.jabatan }}
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        v-model="form.password"
                        type="password" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Minimal 8 karakter"
                        required
                    >
                    <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
                        {{ form.errors.password }}
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <input 
                        v-model="form.password_confirmation"
                        type="password" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ulangi password"
                        required
                    >
                </div>

                <button 
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200 disabled:opacity-50"
                >
                    <span v-if="form.processing">Loading...</span>
                    <span v-else>Daftar</span>
                </button>

                <div class="text-center mt-4">
                    <Link href="/login" class="text-blue-500 hover:underline text-sm">
                        Sudah punya akun? Login
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    nik: '',
    nama_lengkap: '',
    email: '',
    departement: '',
    jabatan: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register');
};
</script>