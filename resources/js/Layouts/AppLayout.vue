<template>
    <div class="h-screen flex bg-gray-100 overflow-hidden">
        <!-- Mobile Overlay -->
        <div 
            v-if="mobileMenuOpen" 
            @click="mobileMenuOpen = false"
            class="fixed inset-0 bg-black/50 z-40 lg:hidden"
        ></div>

        <!-- Sidebar -->
        <aside 
            :class="[
                'bg-white text-gray-800 shadow-lg',
                'transition-all duration-300 flex flex-col',
                'border-r border-gray-200',
                // Desktop styles
                'hidden lg:flex',
                sidebarOpen ? 'lg:w-64' : 'lg:w-20'
            ]"
        >
            <!-- Logo / Header -->
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <div v-show="sidebarOpen" class="flex items-center space-x-2">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span class="text-xl font-bold text-gray-800">WMS</span>
                </div>
                <div class="flex items-center space-x-2">
                    <button 
                        @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded hover:bg-gray-100 transition"
                    >
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 overflow-y-auto py-4">
                <div class="space-y-1 px-2">
                    <!-- WMS Dashboard (New) -->
                    <Link 
                        v-if="hasAnyPermission(['dashboard.wms'])"
                        href="/wms-dashboard"
                        :class="navLinkClass('/wms-dashboard')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium">Dashboard</span>
                    </Link>

                    <!-- Dashboard (visible to all) -->
                    <Link 
                        v-if="hasAnyPermission(['onhand.view'])"
                        href="/dashboard"
                        :class="navLinkClass('/dashboard')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium">On Hand</span>
                    </Link>

                    <Link 
                        v-if="hasAnyPermission(['cycle_count.view'])"
                        href="/transaction/cycle-count"
                        :class="navLinkClass('/transaction/cycle-count')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium">Cycle Count</span>
                    </Link>

                    <!-- Riwayat Aktivitas -->
                    <Link 
                        v-if="hasAnyPermission(['activity_log.view_all', 'activity_log.view_self'])"
                        href="/activity-log"
                        :class="navLinkClass('/activity-log')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium">Riwayat Aktivitas</span>
                    </Link>

                    <!-- IT Admin Dashboard - Only for IT role -->
                    <Link 
                        v-if="hasAnyPermission(['it_dashboard.view'])"
                        href="/it-dashboard"
                        :class="navLinkClass('/it-dashboard')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium">IT Dashboard</span>
                    </Link>



                    <!-- DIVIDER / SEPARATOR - hanya tampil jika ada menu transaksi -->
                    <div v-if="hasAnyTransactionPermission" class="my-4 px-2">
                        <div class="border-t border-gray-200"></div>
                        <div v-show="sidebarOpen" class="mt-3 mb-2 px-2">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Transaksi
                            </span>
                        </div>
                    </div>

                    <!-- Penerimaan Barang -->
                    <Link 
                        v-if="hasAnyPermission(['incoming.view', 'incoming.create', 'incoming.edit'])"
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
                        v-if="hasAnyPermission(['qc.view', 'qc.input_qc_result', 'qc.approve'])"
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
                        v-if="hasAnyPermission(['putaway.view', 'putaway.kerjakan_to'])"
                        href="/transaction/putaway-transfer"
                        :class="navLinkClass('/transaction/putaway-transfer')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium flex-1">PutAway</span>
                        <span 
                            v-if="$page.props.pendingPutawayCount > 0" 
                            v-show="sidebarOpen"
                            class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"
                        >
                            {{ $page.props.pendingPutawayCount }}
                        </span>
                    </Link>

                    <!-- Bin to Bin -->
                    <Link 
                        v-if="hasAnyPermission(['bin-to-bin.view', 'bin-to-bin.pindah_barang'])"
                        href="/transaction/bin-to-bin"
                        :class="navLinkClass('/transaction/bin-to-bin')">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium">Bin to Bin</span>
                    </Link>

                    <!-- Reservation -->
                    <Link 
                        v-if="hasAnyPermission(['reservation.view', 'reservation.create', 'reservation.approve'])"
                        href="/transaction/reservation"
                        :class="navLinkClass('/transaction/reservation')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 2v4M8 2v4"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium flex-1">Reservation</span>
                        <span 
                            v-if="$page.props.inProgressReservationCount > 0" 
                            v-show="sidebarOpen"
                            class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"
                        >
                            {{ $page.props.inProgressReservationCount }}
                        </span>
                    </Link>

                    <!-- Picking List -->
                    <Link 
                        v-if="hasAnyPermission(['picking.view', 'picking.execute'])"
                        href="/transaction/picking-list"
                        :class="navLinkClass('/transaction/picking-list')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="4" y="4" width="16" height="16" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h6M9 13h6"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium flex-1">Picking List</span>
                        <span 
                            v-if="$page.props.pendingPickingCount > 0" 
                            v-show="sidebarOpen"
                            class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"
                        >
                            {{ $page.props.pendingPickingCount }}
                        </span>
                    </Link>

                    <!-- Return -->
                    <Link 
                        v-if="hasAnyPermission(['return.view', 'return.create', 'return.approve'])"
                        href="/transaction/return"
                        :class="navLinkClass('/transaction/return')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M4 4v5h5M20 20v-5h-5m-1 5a8 8 0 01-14.8-3h-2m.2-2h-2m14-3h-2m-1 5a8 8 0 01-14.8-3" 
                            />
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium flex-1">Return</span>
                        <span 
                            v-if="$page.props.pendingReturnCount > 0" 
                            v-show="sidebarOpen"
                            class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"
                        >
                            {{ $page.props.pendingReturnCount }}
                        </span>
                    </Link>

                    <!-- DIVIDER / SEPARATOR - hanya tampil jika ada menu transaksi -->
                    <div v-if="hasAnyTransactionPermission" class="my-4 px-2">
                        <div class="border-t border-gray-200"></div>
                        <div v-show="sidebarOpen" class="mt-3 mb-2 px-2">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Kelola
                            </span>
                        </div>
                    </div>

                    <!-- Master Data -->
                    <Link 
                        v-if="hasAnyPermission(['master_data.view'])"
                        href="/master-data"
                        :class="navLinkClass('/master-data')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium">Master Data</span>
                    </Link>

                    <!-- Role Permission (hanya untuk admin) -->
                    <Link 
                        v-if="hasAnyPermission(['it_dashboard.view'])"
                        href="/role-permission"
                        :class="navLinkClass('/it-dashboard')"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        </svg>
                        <span v-show="sidebarOpen" class="font-medium">Role Permission</span>
                    </Link>
                </div>
            </nav>

            <!-- User Profile Section -->
            <div class="border-t border-gray-200 p-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-semibold text-sm">
                            {{ getUserInitials }}
                        </span>
                    </div>
                    <div v-show="sidebarOpen" class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">
                            {{ $page.props.auth.user.name }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ $page.props.auth.user.role?.name || 'Staff' }}
                        </p>
                    </div>
                </div>
                <button
                    v-show="sidebarOpen"
                    @click="logout"
                    class="mt-3 w-full flex items-center justify-center space-x-2 px-3 py-2 bg-red-600 hover:bg-red-700 rounded-lg transition text-sm text-white"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <!-- Mobile Offcanvas Sidebar -->
        <aside 
            :class="[
                'fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-xl',
                'transform transition-transform duration-300 ease-in-out',
                'lg:hidden flex flex-col',
                mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'
            ]"
        >
            <!-- Mobile Sidebar Header -->
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span class="text-xl font-bold text-gray-800">WMS</span>
                </div>
                <button 
                    @click="mobileMenuOpen = false"
                    class="p-2 rounded-lg hover:bg-gray-100 transition"
                >
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Mobile Navigation Menu -->
            <nav class="flex-1 overflow-y-auto py-4">
                <div class="space-y-1 px-2">
                    <!-- WMS Dashboard -->
                    <Link 
                        v-if="hasAnyPermission(['dashboard.wms'])"
                        href="/wms-dashboard"
                        :class="mobileNavLinkClass('/wms-dashboard')"
                        @click="mobileMenuOpen = false"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="font-medium">Dashboard</span>
                    </Link>

                    <!-- Dashboard -->
                    <Link 
                        v-if="hasAnyPermission(['onhand.view'])"
                        href="/dashboard"
                        :class="mobileNavLinkClass('/dashboard')"
                        @click="mobileMenuOpen = false"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="font-medium">On Hand</span>
                    </Link>

                    <Link 
                        v-if="hasAnyPermission(['cycle_count.view'])"
                        href="/transaction/cycle-count"
                        :class="mobileNavLinkClass('/transaction/cycle-count')"
                        @click="mobileMenuOpen = false"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6"/>
                        </svg>
                        <span class="font-medium">Cycle Count</span>
                    </Link>

                    <!-- Riwayat Aktivitas -->
                    <Link 
                        v-if="hasAnyPermission(['incoming.view', 'qc.view', 'putaway.view', 'picking.view', 'reservation.view', 'return.view'])"
                        href="/activity-log"
                        :class="mobileNavLinkClass('/activity-log')"
                        @click="mobileMenuOpen = false"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="font-medium">Riwayat Aktivitas</span>
                    </Link>

                    <!-- IT Admin Dashboard -->
                    <Link 
                        v-if="hasAnyPermission(['it_dashboard.view'])"
                        href="/it-dashboard"
                        :class="mobileNavLinkClass('/it-dashboard')"
                        @click="mobileMenuOpen = false"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="font-medium">IT Dashboard</span>
                    </Link>

                    <!-- Transaksi Divider -->
                    <div v-if="hasAnyTransactionPermission" class="my-4 px-2">
                        <div class="border-t border-gray-200"></div>
                        <div class="mt-3 mb-2 px-2">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Transaksi
                            </span>
                        </div>
                    </div>

                    <!-- Penerimaan Barang -->
                    <Link 
                        v-if="hasAnyPermission(['incoming.view', 'incoming.create', 'incoming.edit'])"
                        href="/transaction/goods-receipt"
                        :class="mobileNavLinkClass('/transaction/goods-receipt')"
                        @click="mobileMenuOpen = false"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span class="font-medium">Penerimaan Barang</span>
                    </Link>

                    <!-- Quality Control -->
                    <Link 
                        v-if="hasAnyPermission(['qc.view', 'qc.input_qc_result', 'qc.approve'])"
                        href="/transaction/quality-control"
                        :class="mobileNavLinkClass('/transaction/quality-control')"
                        @click="mobileMenuOpen = false"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4"/>
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                        </svg>
                        <span class="font-medium">Quality Control</span>
                    </Link>

                    <!-- PutAway -->
                    <Link 
                        v-if="hasAnyPermission(['putaway.view', 'putaway.kerjakan_to'])"
                        href="/transaction/putaway-transfer"
                        :class="mobileNavLinkClass('/transaction/putaway-transfer')"
                        @click="mobileMenuOpen = false"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"/>
                        </svg>
                        <span class="font-medium flex-1">PutAway</span>
                        <span 
                            v-if="$page.props.pendingPutawayCount > 0"
                            class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"
                        >
                            {{ $page.props.pendingPutawayCount }}
                        </span>
                    </Link>

                    <!-- Bin to Bin -->
                    <Link 
                        v-if="hasAnyPermission(['bin-to-bin.view', 'bin-to-bin.pindah_barang'])"
                        href="/transaction/bin-to-bin"
                        :class="mobileNavLinkClass('/transaction/bin-to-bin')"
                        @click="mobileMenuOpen = false"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        <span class="font-medium">Bin to Bin</span>
                    </Link>

                    <!-- Reservation -->
                    <Link 
                        v-if="hasAnyPermission(['reservation.view', 'reservation.create', 'reservation.approve'])"
                        href="/transaction/reservation"
                        :class="mobileNavLinkClass('/transaction/reservation')"
                        @click="mobileMenuOpen = false"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 2v4M8 2v4"/>
                        </svg>
                        <span class="font-medium flex-1">Reservation</span>
                        <span 
                            v-if="$page.props.inProgressReservationCount > 0"
                            class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"
                        >
                            {{ $page.props.inProgressReservationCount }}
                        </span>
                    </Link>

                    <!-- Picking List -->
                    <Link 
                        v-if="hasAnyPermission(['picking.view', 'picking.execute'])"
                        href="/transaction/picking-list"
                        :class="mobileNavLinkClass('/transaction/picking-list')"
                        @click="mobileMenuOpen = false"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="4" y="4" width="16" height="16" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h6M9 13h6"/>
                        </svg>
                        <span class="font-medium flex-1">Picking List</span>
                        <span 
                            v-if="$page.props.pendingPickingCount > 0"
                            class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"
                        >
                            {{ $page.props.pendingPickingCount }}
                        </span>
                    </Link>

                    <!-- Return -->
                    <Link 
                        v-if="hasAnyPermission(['return.view', 'return.create', 'return.approve'])"
                        href="/transaction/return"
                        :class="mobileNavLinkClass('/transaction/return')"
                        @click="mobileMenuOpen = false"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M4 4v5h5M20 20v-5h-5m-1 5a8 8 0 01-14.8-3h-2m.2-2h-2m14-3h-2m-1 5a8 8 0 01-14.8-3" 
                            />
                        </svg>
                        <span class="font-medium flex-1">Return</span>
                        <span 
                            v-if="$page.props.pendingReturnCount > 0"
                            class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"
                        >
                            {{ $page.props.pendingReturnCount }}
                        </span>
                    </Link>

                    <!-- Kelola Divider -->
                    <div v-if="hasAnyTransactionPermission" class="my-4 px-2">
                        <div class="border-t border-gray-200"></div>
                        <div class="mt-3 mb-2 px-2">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Kelola
                            </span>
                        </div>
                    </div>

                    <!-- Master Data -->
                    <Link 
                        v-if="hasAnyPermission(['master_data.view'])"
                        href="/master-data"
                        :class="mobileNavLinkClass('/master-data')"
                        @click="mobileMenuOpen = false"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                        </svg>
                        <span class="font-medium">Master Data</span>
                    </Link>

                    <!-- Role Permission -->
                    <Link 
                        v-if="hasAnyPermission(['it_dashboard.view'])"
                        href="/role-permission"
                        :class="mobileNavLinkClass('/role-permission')"
                        @click="mobileMenuOpen = false"
                    >
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        </svg>
                        <span class="font-medium">Role Permission</span>
                    </Link>
                </div>
            </nav>

            <!-- Mobile User Profile Section -->
            <div class="border-t border-gray-200 p-4">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-semibold text-sm">
                            {{ getUserInitials }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">
                            {{ $page.props.auth.user.name }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ $page.props.auth.user.role?.name || 'Staff' }}
                        </p>
                    </div>
                </div>
                <button
                    @click="logout"
                    class="w-full flex items-center justify-center space-x-2 px-3 py-2.5 bg-red-600 hover:bg-red-700 rounded-lg transition text-sm text-white"
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
            <header class="bg-white shadow-sm">
                <div class="px-4 lg:px-6 py-3 lg:py-4 flex items-center justify-between">
                    <!-- Mobile Menu Button + Title -->
                    <div class="flex items-center gap-3">
                        <!-- Mobile Burger Button -->
                        <button 
                            @click="mobileMenuOpen = true"
                            class="lg:hidden p-2 -ml-2 rounded-lg hover:bg-gray-100 transition"
                        >
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div>
                            <h1 class="text-lg lg:text-2xl font-bold text-gray-800">
                                {{ pageTitle }}
                            </h1>
                            <p class="text-xs lg:text-sm text-gray-500 mt-0.5 lg:mt-1 hidden sm:block">
                                {{ pageDescription }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 lg:space-x-4">
                        <!-- Database Indicator -->
                        <div 
                            v-if="selectedDatabase"
                            :class="[
                                'px-2 lg:px-3 py-1.5 lg:py-2 rounded-lg flex items-center space-x-1 lg:space-x-2 text-xs lg:text-sm font-semibold shadow-sm',
                                selectedDatabase === 'mysql_testing' 
                                    ? 'bg-orange-100 text-orange-800 border border-orange-300' 
                                    : 'bg-green-100 text-green-800 border border-green-300'
                            ]"
                            :title="selectedDatabase === 'mysql_testing' ? 'Anda sedang menggunakan database Testing' : 'Anda sedang menggunakan database Production'"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                            </svg>
                            <span class="hidden sm:inline">
                                {{ selectedDatabase === 'mysql_testing' ? 'TESTING' : 'PRODUCTION' }}
                            </span>
                        </div>
                        
                        <!-- Notification
                        <button class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">
                            <svg class="w-5 lg:w-6 h-5 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button> -->
                        <!-- User Info - Hidden on mobile -->
                        <div class="hidden lg:flex items-center space-x-2 px-3 py-2 bg-gray-50 rounded-lg">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-800">{{ $page.props.auth.user.name }}</p>
                                <p class="text-xs text-gray-500">{{ $page.props.auth.user.nik }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-6 bg-gray-50">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed } from 'vue';
    import { Link, router, usePage } from '@inertiajs/vue3';
    import { usePermissions } from '@/Composables/usePermissions';

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
    const { hasPermission, hasAnyPermission } = usePermissions();
    
    // DEBUG: Check permissions
    console.log('DEBUG: User Role:', page.props.auth.user?.role?.name);
    console.log('DEBUG: Permissions:', page.props.permissions);

    const sidebarOpen = ref(true);
    const mobileMenuOpen = ref(false);

    // Check if user has any transaction permissions
    const hasAnyTransactionPermission = computed(() => {
        return hasAnyPermission([
            'incoming.view', 'incoming.create',
            'qc.view', 'qc.input_qc_result',
            'putaway.view', 'putaway.kerjakan_to',
            'reservation.view', 'reservation.create_request',
            'picking.view', 'picking.kerjakan_picking',
            'return.view', 'return.create_return'
        ]);
    });

    const isActive = (path) => {
        return page.url === path;
    };

    const getUserInitials = computed(() => {
        const name = page.props.auth.user.name;
        const words = name.split(' ');
        if (words.length >= 2) {
            return words[0][0] + words[1][0];
        }
        return name.substring(0, 2).toUpperCase();
    });

    const logout = () => {
        router.post('/logout');
    };

    // Get selected database from session
    const selectedDatabase = computed(() => {
        return page.props.selectedDatabase || 'mysql'; // Default to production if not set
    });

    const navLinkClass = (path) => {
        // Remove query parameters from current URL for comparison
        const currentPath = page.url.split('?')[0]
        const isActive = currentPath === path || currentPath.startsWith(path + '/')

        const baseClasses = 'flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200'

        if (isActive) {
            return `${baseClasses} bg-[#157347] text-white shadow-lg` 
        }

        return `${baseClasses} text-gray-700 hover:bg-gray-100`
    }

    const mobileNavLinkClass = (path) => {
        // Remove query parameters from current URL for comparison
        const currentPath = page.url.split('?')[0]
        const isActive = currentPath === path || currentPath.startsWith(path + '/')

        const baseClasses = 'flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200'

        if (isActive) {
            return `${baseClasses} bg-[#157347] text-white shadow-lg` 
        }

        return `${baseClasses} text-gray-700 hover:bg-gray-100`
    }
</script>