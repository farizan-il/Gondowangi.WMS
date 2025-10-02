<template>
    <div :class="['min-h-screen flex', darkMode ? 'bg-gray-900' : 'bg-gray-100']">
        <!-- Sidebar -->
        <aside 
            :class="[
                darkMode ? 'bg-gray-900 text-gray-100' : 'bg-gray-800 text-white',
                'transition-all duration-300 flex flex-col',
                sidebarOpen ? 'w-64' : 'w-20'
            ]"
        >
            <!-- Logo / Header -->
            <div class="p-4 border-b border-gray-700 flex items-center justify-between">
                <div v-show="sidebarOpen" class="flex items-center space-x-2">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span class="text-xl font-bold">WMS</span>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- Dark mode toggle -->
                    <button @click="darkMode = !darkMode" class="p-2 rounded hover:bg-gray-700 transition">
                        <svg v-if="!darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-8.66l-.71.71M4.05 4.05l-.71.71M21 12h-1M4 12H3m16.24 4.24l-.71-.71M4.05 19.95l-.71-.71"/>
                        </svg>
                        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3a7 7 0 009.79 9.79z"/>
                        </svg>
                    </button>
                    <button 
                        @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded hover:bg-gray-700 transition"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 overflow-y-auto py-4">
                <div class="space-y-1 px-2">
                    <!-- Central Data -->
                    <Link 
                        href="/dashboard"
                        :class="navLinkClass('/dashboard')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium">Central Data</span>
                    </Link>
                    <!-- Riwayat Aktivitas -->
                    <Link 
                        href="/activity-log"
                        :class="navLinkClass('/activity-log')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium">Riwayat Aktivitas</span>
                    </Link>
                    <!-- Master Data -->
                    <Link 
                        href="/master-data"
                        :class="navLinkClass('/master-data')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium">Master Data</span>
                    </Link>
                    <!-- Role Permission -->
                    <Link 
                        href="/role-permission"
                        :class="navLinkClass('/role-permission')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium">Role Permission</span>
                    </Link>

                    <!-- DIVIDER / SEPARATOR -->
                    <div class="my-4 px-2">
                        <div class="border-t border-gray-700"></div>
                        <div v-show="sidebarOpen" class="mt-3 mb-2 px-2">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                Transaksi
                            </span>
                        </div>
                    </div>

                    <!-- Penerimaan Barang -->
                    <Link 
                        href="/transaction/goods-receipt"
                        :class="navLinkClass('/transaction/goods-receipt')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium">Penerimaan Barang</span>
                    </Link>
                    <!-- Quality Control -->
                    <Link 
                        href="/transaction/quality-control"
                        :class="navLinkClass('/transaction/quality-control')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4"/>
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium">Quality Control</span>
                    </Link>
                    <!-- PutAway & Transfer Order -->
                    <Link 
                        href="/transaction/putaway-transfer"
                        :class="navLinkClass('/transaction/putaway-transfer')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium">PutAway & Transfer Order</span>
                    </Link>
                    <!-- Reservation -->
                    <Link 
                        href="/transaction/reservation"
                        :class="navLinkClass('/transaction/reservation')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 2v4M8 2v4"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium">Reservation</span>
                    </Link>
                    <!-- Picking List -->
                    <Link 
                        href="/transaction/picking-list"
                        :class="navLinkClass('/transaction/picking-list')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="4" y="4" width="16" height="16" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h6M9 13h6"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium">Picking List</span>
                    </Link>
                    <!-- Return -->
                    <Link 
                        href="/transaction/return"
                        :class="navLinkClass('/transaction/return')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium">Return</span>
                    </Link>
                </div>
            </nav>

            <!-- User Profile Section -->
            <div class="border-t border-gray-700 p-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-semibold text-sm">
                            {{ getUserInitials }}
                        </span>
                    </div>
                    <div v-show="sidebarOpen" class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">
                            {{ $page.props.auth.user.nama_lengkap }}
                        </p>
                        <p class="text-xs text-gray-400 truncate">
                            {{ $page.props.auth.user.jabatan || 'Staff' }}
                        </p>
                    </div>
                </div>
                <button
                    v-show="sidebarOpen"
                    @click="logout"
                    class="mt-3 w-full flex items-center justify-center space-x-2 px-3 py-2 bg-red-600 hover:bg-red-700 rounded-lg transition text-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Navbar -->
            <header :class="darkMode ? 'bg-gray-800 shadow-sm' : 'bg-white shadow-sm'">
                <div class="px-6 py-4 flex items-center justify-between">
                    <div>
                        <h1 :class="darkMode ? 'text-2xl font-bold text-gray-100' : 'text-2xl font-bold text-gray-800'">
                            {{ pageTitle }}
                        </h1>
                        <p :class="darkMode ? 'text-sm text-gray-400 mt-1' : 'text-sm text-gray-500 mt-1'">
                            {{ pageDescription }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Notification -->
                        <button class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        <!-- User Info -->
                        <div class="flex items-center space-x-2 px-3 py-2 bg-gray-50 rounded-lg">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-800">{{ $page.props.auth.user.nama_lengkap }}</p>
                                <p class="text-xs text-gray-500">{{ $page.props.auth.user.nik }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    pageTitle: {
        type: String,
        default: 'Dashboard'
    },
    pageDescription: {
        type: String,
        default: 'Selamat datang di Warehouse Management System'
    }
});

const page = usePage();
const sidebarOpen = ref(true);
const openSubmenu = ref(null);
const darkMode = ref(false);

const toggleSubmenu = (menu) => {
    openSubmenu.value = openSubmenu.value === menu ? null : menu;
};

const isActive = (path) => {
    return page.url === path;
};

const isActiveSubmenu = (prefix) => {
    return page.url.startsWith(prefix);
};

const getUserInitials = computed(() => {
    const name = page.props.auth.user.nama_lengkap;
    const words = name.split(' ');
    if (words.length >= 2) {
        return words[0][0] + words[1][0];
    }
    return name.substring(0, 2).toUpperCase();
});

const logout = () => {
    router.post('/logout');
};

// Helper for nav link styling
// const navLinkClass = (path) => [
//     'flex items-center space-x-3 px-3 py-3 rounded-lg transition',
//     isActive(path)
//         ? (darkMode.value ? 'bg-blue-600 text-white' : 'bg-blue-600 text-white')
//         : (darkMode.value ? 'text-gray-400 hover:bg-gray-800 hover:text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white')
// ];
const navLinkClass = (path) => {
    // Check if current URL matches the path
    const isActive = page.url === path || page.url.startsWith(path + '/')
    
    const baseClasses = 'flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200'
    
    if (isActive) {
        return `${baseClasses} bg-blue-600 text-white shadow-lg`
    }
    
    return `${baseClasses} text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800`
}

const navButtonClass = (menu, prefix) => [
    'w-full flex items-center justify-between px-3 py-3 rounded-lg transition',
    openSubmenu.value === menu || isActiveSubmenu(prefix)
        ? (darkMode.value ? 'bg-gray-800 text-white' : 'bg-gray-700 text-white')
        : (darkMode.value ? 'text-gray-400 hover:bg-gray-800 hover:text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white')
];

const subNavLinkClass = (path) => [
    'block px-3 py-2 rounded-lg text-sm transition',
    isActive(path)
        ? (darkMode.value ? 'bg-blue-600 text-white' : 'bg-blue-600 text-white')
        : (darkMode.value ? 'text-gray-400 hover:bg-gray-800 hover:text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white')
];
</script>
