<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    isCollapsed: Boolean,
});

// Akses properti halaman saat ini dari Inertia (untuk pengecekan route aktif)
const page = usePage();

// Definisi Struktur Menu
const menuGroups = [
    {
        name: 'Utama',
        items: [
            { name: 'Data Center', route: 'dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l-2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6m-6 0h-2M9 21h6' },
            { name: 'Riwayat Aktivitas', route: 'riwayat-aktivitas', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
        ]
    },
    {
        name: 'Data & Master',
        items: [
            { name: 'Master Data', route: 'master-data', icon: 'M17 20h-4a2 2 0 01-2-2V6a2 2 0 012-2h4a2 2 0 012 2v12a2 2 0 01-2 2zm-7 0H7a2 2 0 01-2-2V6a2 2 0 012-2h3m0 0l-1-1m0 0l-1 1m2-1a2 2 0 01-2-2v-4a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2h-4' },
            { name: 'Central Data', route: 'central-data', icon: 'M4 7v10l-2 2v1h20v-1l-2-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2z' },
        ]
    },
    {
        name: 'Operasional Gudang',
        items: [
            { name: 'Penerimaan Barang', route: 'penerimaan-barang', icon: 'M12 4.5v15m0 0l-6-6m6 6l6-6' },
            { name: 'Quality Control', route: 'quality-control', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
            { name: 'Picking List', route: 'picking-list', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h-6m3 3h-3' },
            { name: 'Put Awas TO', route: 'put-awas-to', icon: 'M5 13l4 4L19 7' }, // Icon placeholder
            { name: 'Reservation', route: 'reservation', icon: 'M8 7V3m8 4V3m-9 8h.01M8 11h.01M8 15h.01M16 15h.01M12 15h.01M16 11h.01M12 11h.01M3 17a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10z' }, // Calendar
            { name: 'Retur', route: 'retur', icon: 'M10 19l-7-7m0 0l7-7m-7 7h18' },
        ]
    },
    {
        name: 'Pengaturan',
        items: [
            { name: 'Role & Permission', route: 'role-permission', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20v-2m0 2h-4a2 2 0 01-2-2v-4M7 4a3 3 0 00-3 3v6a3 3 0 003 3h6a3 3 0 003-3V7a3 3 0 00-3-3H7z' }, // Icon for users/roles
        ]
    }
];

const isActive = (routeName) => {
    if (typeof route !== 'undefined') {
        return route().current(routeName);
    }
    return page.url.startsWith(`/${routeName}`);
};
</script>

<template>
    <!-- Sidebar Container -->
    <div :class="[
        'fixed top-0 left-0 h-full bg-[#1A5B3D] transition-all duration-300 z-30 overflow-y-auto overflow-x-hidden shadow-2xl',
        props.isCollapsed ? 'w-20' : 'w-72',
    ]">
        <!-- Header Logo -->
        <div class="h-20 flex items-center p-4 border-b border-[#257551]">
            <div v-if="!props.isCollapsed" class="flex items-center text-white text-2xl font-bold tracking-wider whitespace-nowrap transition-opacity duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Gondowangi WMS
            </div>
            <div v-else class="flex items-center justify-center w-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>

        <!-- Menu Navigation -->
        <nav class="mt-6 px-4 space-y-6">
            <div v-for="group in menuGroups" :key="group.name" class="space-y-2">
                <!-- Group Title -->
                <h3 v-if="!props.isCollapsed" class="text-xs font-semibold uppercase text-gray-300 tracking-wider transition-opacity duration-300 ml-3">
                    {{ group.name }}
                </h3>
                <h3 v-else class="text-center text-gray-300 text-xs font-semibold mt-4 transition-opacity duration-300">
                    ...
                </h3>

                <!-- Menu Items -->
                <div v-for="item in group.items" :key="item.name">
                    <!-- Menggunakan Inertia Link untuk navigasi -->
                    <Link 
                        :href="route(item.route)" 
                        :class="[
                            'flex items-center py-2 rounded-lg transition-colors duration-200 group relative',
                            isActive(item.route) 
                                ? 'bg-[#0e4933] text-white shadow-lg' 
                                : 'text-gray-300 hover:bg-[#257551] hover:text-white',
                            props.isCollapsed ? 'justify-center w-full px-0' : 'px-3'
                        ]"
                    >
                        <!-- Icon -->
                        <svg class="h-6 w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                        </svg>

                        <!-- Text -->
                        <span v-if="!props.isCollapsed" class="ml-3 text-base font-medium whitespace-nowrap">
                            {{ item.name }}
                        </span>

                        <!-- Tooltip untuk mode collapsed -->
                        <div v-else class="absolute left-full ml-3 py-1 px-3 bg-gray-800 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap">
                            {{ item.name }}
                        </div>
                    </Link>
                </div>
            </div>
        </nav>
    </div>
</template>
