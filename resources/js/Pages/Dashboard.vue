<template>
    <AppLayout pageTitle="Dashboard" pageDescription="Ringkasan sistem warehouse management">
    <div :class="isDarkMode ? 'dark' : ''" class="min-h-screen transition-colors duration-300">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
      <!-- Header -->
      <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Central Data WMS</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola semua material di gudang</p>
          </div>
          <div class="flex items-center space-x-4">
            <!-- Dark Mode Toggle -->
            <button 
              @click="toggleDarkMode"
              class="p-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
              <svg v-if="!isDarkMode" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
              </svg>
              <svg v-else class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-lin    ; ;ecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </button>
            
            <!-- QR Scanner -->
            <button 
              @click="openQRScanner"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h2M4 4h5a3 3 0 000 6H4V4zm3 2h.01M8 16h.01" />
              </svg>
              <span>Scan QR</span>
            </button>
          </div>
        </div>

        <!-- Alerts/Notifications -->
        <div v-if="alerts.length > 0" class="mb-4 space-y-2">
          <div v-for="alert in alerts" :key="alert.id" :class="getAlertClass(alert.type)" class="p-3 rounded-lg flex items-center justify-between">
            <div class="flex items-center space-x-2">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
              <span>{{ alert.message }}</span>
            </div>
            <button @click="dismissAlert(alert.id)" class="text-current opacity-70 hover:opacity-100">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Toolbar -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
          <div class="flex flex-wrap gap-4 items-center justify-between">
            <!-- Left Side - Search & Filters -->
            <div class="flex flex-wrap gap-4 items-center flex-1">
              <!-- Search Box -->
              <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input 
                  v-model="searchQuery" 
                  type="text" 
                  placeholder="Cari kode, nama material, atau lot..."
                  class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg w-80 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>

              <!-- Filters -->
              <select v-model="filterStatus" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="">Semua Status</option>
                <option value="Waiting QC">Waiting QC</option>
                <option value="Karantina">Karantina</option>
                <option value="Released">Released</option>
                <option value="Reject">Reject</option>
                <option value="In Production">In Production</option>
                <option value="Returned">Returned</option>
              </select>

              <select v-model="filterType" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="">Semua Tipe</option>
                <option value="RM">Raw Material</option>
                <option value="PM">Packaging Material</option>
              </select>

              <select v-model="filterLocation" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="">Semua Lokasi</option>
                <option v-for="location in uniqueLocations" :key="location" :value="location">{{ location }}</option>
              </select>
            </div>

            <!-- Right Side - Actions -->
            <div class="flex gap-2">
              <button 
                @click="openImportModal"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center space-x-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <span>Import</span>
              </button>
              
              <button 
                @click="exportData"
                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center space-x-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Export</span>
              </button>
              
              <!-- <button 
                @click="openAddModal"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>Tambah</span>
              </button> -->
            </div>
          </div>
        </div>
      </div>

      <!-- Main Table -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th v-for="column in tableColumns" :key="column.key" 
                    @click="sortBy(column.key)"
                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                  <div class="flex items-center space-x-1">
                    <span>{{ column.label }}</span>
                    <svg v-if="sortColumn === column.key" class="w-4 h-4" :class="sortDirection === 'asc' ? '' : 'transform rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                  </div>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="item in filteredItems" :key="item.id" 
                  @click="openDetailPanel(item)"
                  :class="getRowClass(item)"
                  class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                <td class="px-4 py-3 whitespace-nowrap">
                  <span :class="item.type === 'RM' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'" 
                        class="px-2 py-1 text-xs font-semibold rounded-full">
                    {{ item.type }}
                  </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ item.kode }}</td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ item.nama }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ item.lot }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ item.lokasi }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ item.qty }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ item.uom }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  <span :class="getExpiredClass(item.expiredDate)">{{ formatDate(item.expiredDate) }}</span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                  <span :class="getStatusClass(item.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                    {{ item.status }}
                  </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm space-x-2">
                  <button 
                    @click.stop="printQR(item)"
                    class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors"
                    title="Print QR"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h2M4 4h5a3 3 0 000 6H4V4zm3 2h.01M8 16h.01" />
                    </svg>
                  </button>
                  <button 
                    @click.stop="transferItem(item)"
                    class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 transition-colors"
                    title="Transfer"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                  </button>
                  <!-- <button 
                    @click.stop="reserveItem(item)"
                    class="text-purple-600 dark:text-purple-400 hover:text-purple-900 dark:hover:text-purple-300 transition-colors"
                    title="Reserve"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                  </button> -->
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Empty State -->
        <div v-if="filteredItems.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-4.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 009.586 13H7" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada data</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Data material tidak ditemukan atau filter terlalu spesifik</p>
        </div>
      </div>

      <!-- Detail Panel -->
      <div v-if="selectedItem" 
           class="fixed inset-y-0 right-0 w-96 bg-white dark:bg-gray-800 shadow-xl z-999 transform transition-transform duration-300 border-l border-gray-200 dark:border-gray-700"
           :class="showDetailPanel ? 'translate-x-0' : 'translate-x-full'"
           style="max-height: 100vh; overflow: hidden;">
        <div class="h-full flex flex-col">
          <!-- Panel Header -->
          <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">Detail Material</h3>
              <button @click="closeDetailPanel" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Panel Content -->
          <div class="flex-1 overflow-y-auto">
            <div class="p-6 space-y-6">
              <!-- Basic Info -->
              <div>
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Informasi Material</h4>
                <div class="space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Tipe:</span>
                    <span :class="selectedItem.type === 'RM' ? 'text-blue-600 dark:text-blue-400' : 'text-green-600 dark:text-green-400'" class="font-medium">{{ selectedItem.type }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Kode:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ selectedItem.kode }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Nama:</span>
                    <span class="font-medium text-gray-900 dark:text-white text-right">{{ selectedItem.nama }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Lot/Serial:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ selectedItem.lot }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Lokasi:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ selectedItem.lokasi }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Qty:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ selectedItem.qty }} {{ selectedItem.uom }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Expired:</span>
                    <span :class="getExpiredClass(selectedItem.expiredDate)" class="font-medium">{{ formatDate(selectedItem.expiredDate) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Status:</span>
                    <span :class="getStatusClass(selectedItem.status)" class="px-2 py-1 text-xs font-semibold rounded-full">{{ selectedItem.status }}</span>
                  </div>
                </div>
              </div>

              <!-- QR Code Section -->
              <div>
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">QR Code & Dokumen</h4>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                  <div class="w-32 h-32 bg-white dark:bg-gray-800 mx-auto mb-2 flex items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                    <!-- QR Code Pattern -->
                    <svg class="w-24 h-24 text-gray-400 dark:text-gray-500" viewBox="0 0 100 100" fill="currentColor">
                      <!-- Outer squares -->
                      <rect x="0" y="0" width="20" height="20"/>
                      <rect x="80" y="0" width="20" height="20"/>
                      <rect x="0" y="80" width="20" height="20"/>
                      <!-- Inner squares -->
                      <rect x="6" y="6" width="8" height="8" fill="white"/>
                      <rect x="86" y="6" width="8" height="8" fill="white"/>
                      <rect x="6" y="86" width="8" height="8" fill="white"/>
                      <!-- Random pattern -->
                      <rect x="40" y="0" width="4" height="4"/>
                      <rect x="60" y="0" width="4" height="4"/>
                      <rect x="20" y="20" width="4" height="4"/>
                      <rect x="40" y="20" width="4" height="4"/>
                      <rect x="60" y="20" width="4" height="4"/>
                      <rect x="0" y="40" width="4" height="4"/>
                      <rect x="20" y="40" width="4" height="4"/>
                      <rect x="60" y="40" width="4" height="4"/>
                      <rect x="80" y="40" width="4" height="4"/>
                      <rect x="20" y="60" width="4" height="4"/>
                      <rect x="40" y="60" width="4" height="4"/>
                      <rect x="80" y="60" width="4" height="4"/>
                      <rect x="40" y="80" width="4" height="4"/>
                      <rect x="60" y="80" width="4" height="4"/>
                      <rect x="80" y="80" width="4" height="4"/>
                    </svg>
                  </div>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ selectedItem.kode }}-{{ selectedItem.lot }}</p>
                  <button 
                    @click="printQR(selectedItem)"
                    class="text-blue-600 dark:text-blue-400 text-sm hover:underline font-medium"
                  >
                    Print QR Code
                  </button>
                </div>
                <div class="mt-3 space-y-1 text-sm">
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">PO Number:</span>
                    <span class="text-blue-600 dark:text-blue-400 cursor-pointer hover:underline">PO-2024-001</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">GR Number:</span>
                    <span class="text-blue-600 dark:text-blue-400 cursor-pointer hover:underline">GR-2024-001</span>
                  </div>
                </div>
              </div>

              <!-- Movement History -->
              <div>
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Riwayat Pergerakan</h4>
                <div class="space-y-3">
                  <div v-for="history in selectedItem.history" :key="history.id" class="flex items-start space-x-3">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                    <div class="flex-1 text-sm">
                      <div class="flex justify-between items-start">
                        <span class="text-gray-900 dark:text-white font-medium">{{ history.action }}</span>
                        <span class="text-gray-500 dark:text-gray-400 text-xs">{{ formatDateTime(history.date) }}</span>
                      </div>
                      <p class="text-gray-600 dark:text-gray-400 text-xs mt-1">{{ history.detail }}</p>
                      <p class="text-gray-500 dark:text-gray-500 text-xs">{{ history.user }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Backdrop for detail panel -->
      <div v-if="showDetailPanel" @click="closeDetailPanel" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-99"></div>
    </div>
  </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

import { ref, computed, onMounted } from 'vue'

// Types
interface MaterialItem {
  id: string
  type: 'RM' | 'PM'
  kode: string
  nama: string
  lot: string
  lokasi: string
  qty: number
  uom: string
  expiredDate: string
  status: 'Waiting QC' | 'Karantina' | 'Released' | 'Reject' | 'In Production' | 'Returned'
  history: HistoryItem[]
}

interface HistoryItem {
  id: string
  date: string
  action: string
  detail: string
  user: string
}

interface Alert {
  id: string
  type: 'error' | 'warning' | 'info'
  message: string
}

// Reactive data
const isDarkMode = ref(false)
const searchQuery = ref('')
const filterStatus = ref('')
const filterType = ref('')
const filterLocation = ref('')
const sortColumn = ref('kode')
const sortDirection = ref<'asc' | 'desc'>('asc')
const selectedItem = ref<MaterialItem | null>(null)
const showDetailPanel = ref(false)

// Alerts
const alerts = ref<Alert[]>([
  { id: '1', type: 'warning', message: '5 item akan expired dalam 30 hari' },
  { id: '2', type: 'error', message: '2 item sudah expired!' }
])

// Table columns
const tableColumns = [
  { key: 'type', label: 'RM/PM' },
  { key: 'kode', label: 'Kode' },
  { key: 'nama', label: 'Nama Material' },
  { key: 'lot', label: 'Serial/Lot' },
  { key: 'lokasi', label: 'Lokasi' },
  { key: 'qty', label: 'Qty' },
  { key: 'uom', label: 'UoM' },
  { key: 'expiredDate', label: 'Expired Date' },
  { key: 'status', label: 'Status' }
]

// Dummy data
const materialItems = ref<MaterialItem[]>([
  {
    id: '1',
    type: 'RM',
    kode: 'RM001',
    nama: 'Alcohol 96%',
    lot: 'LOT240901001',
    lokasi: 'BIN-A01',
    qty: 250,
    uom: 'L',
    expiredDate: '2025-12-31',
    status: 'Released',
    history: [
      { id: '1', date: '2024-09-12T08:00:00', action: 'Incoming GR001', detail: 'Diterima dari Supplier A', user: 'Admin' },
      { id: '2', date: '2024-09-13T10:30:00', action: 'QC Approved', detail: 'Lulus quality control', user: 'QC Team' },
      { id: '3', date: '2024-09-14T14:15:00', action: 'Put Away', detail: 'Disimpan di BIN-A01', user: 'Operator' }
    ]
  },
  {
    id: '2',
    type: 'PM',
    kode: 'PM001',
    nama: 'Plastic Bottle 500ml',
    lot: 'LOT240902001',
    lokasi: 'BIN-B02',
    qty: 5000,
    uom: 'pcs',
    expiredDate: '2026-08-15',
    status: 'Released',
    history: [
      { id: '1', date: '2024-09-02T09:00:00', action: 'Incoming GR002', detail: 'Diterima dari Supplier B', user: 'Admin' },
      { id: '2', date: '2024-09-02T11:00:00', action: 'QC Approved', detail: 'Lulus quality control', user: 'QC Team' },
      { id: '3', date: '2024-09-03T08:30:00', action: 'Put Away', detail: 'Disimpan di BIN-B02', user: 'Operator' }
    ]
  },
  {
    id: '3',
    type: 'RM',
    kode: 'RM002',
    nama: 'Sodium Lauryl Sulfate',
    lot: 'LOT240903001',
    lokasi: 'BIN-A03',
    qty: 100,
    uom: 'kg',
    expiredDate: '2024-10-15',
    status: 'Waiting QC',
    history: [
      { id: '1', date: '2024-09-03T14:00:00', action: 'Incoming GR003', detail: 'Diterima dari Supplier C', user: 'Admin' },
      { id: '2', date: '2024-09-03T15:30:00', action: 'Put Away', detail: 'Disimpan di BIN-A03 untuk QC', user: 'Operator' }
    ]
  },
  {
    id: '4',
    type: 'PM',
    kode: 'PM002',
    nama: 'Label Shampoo 500ml',
    lot: 'LOT240904001',
    lokasi: 'BIN-C01',
    qty: 10000,
    uom: 'pcs',
    expiredDate: '2027-01-30',
    status: 'In Production',
    history: [
      { id: '1', date: '2024-09-04T08:00:00', action: 'Incoming GR004', detail: 'Diterima dari Supplier D', user: 'Admin' },
      { id: '2', date: '2024-09-04T10:00:00', action: 'QC Approved', detail: 'Lulus quality control', user: 'QC Team' },
      { id: '3', date: '2024-09-05T07:00:00', action: 'Reserved', detail: 'Reserved untuk MO-2024-001', user: 'Planner' },
      { id: '4', date: '2024-09-05T08:30:00', action: 'Picked', detail: 'Diambil untuk produksi', user: 'Picker' }
    ]
  },
  {
    id: '5',
    type: 'RM',
    kode: 'RM003',
    nama: 'Glycerin',
    lot: 'LOT240905001',
    lokasi: 'BIN-A05',
    qty: 75,
    uom: 'L',
    expiredDate: '2024-09-20',
    status: 'Reject',
    history: [
      { id: '1', date: '2024-09-05T09:00:00', action: 'Incoming GR005', detail: 'Diterima dari Supplier E', user: 'Admin' },
      { id: '2', date: '2024-09-06T11:00:00', action: 'QC Rejected', detail: 'Tidak lulus quality control - kontaminasi', user: 'QC Team' }
    ]
  },
  {
    id: '6',
    type: 'RM',
    kode: 'RM004',
    nama: 'Citric Acid',
    lot: 'LOT240906001',
    lokasi: 'BIN-A07',
    qty: 50,
    uom: 'kg',
    expiredDate: '2025-06-30',
    status: 'Karantina',
    history: [
      { id: '1', date: '2024-09-06T13:00:00', action: 'Incoming GR006', detail: 'Diterima dari Supplier F', user: 'Admin' },
      { id: '2', date: '2024-09-06T14:30:00', action: 'Put Away', detail: 'Disimpan di karantina untuk investigasi', user: 'Operator' }
    ]
  },
  {
    id: '7',
    type: 'PM',
    kode: 'PM003',
    nama: 'Pump Dispenser',
    lot: 'LOT240907001',
    lokasi: 'BIN-D01',
    qty: 2500,
    uom: 'pcs',
    expiredDate: '2028-12-31',
    status: 'Returned',
    history: [
      { id: '1', date: '2024-09-07T08:00:00', action: 'Incoming GR007', detail: 'Diterima dari Supplier G', user: 'Admin' },
      { id: '2', date: '2024-09-07T10:00:00', action: 'QC Approved', detail: 'Lulus quality control', user: 'QC Team' },
      { id: '3', date: '2024-09-08T09:00:00', action: 'Issued to Production', detail: 'Dikeluarkan untuk produksi MO-2024-002', user: 'Picker' },
      { id: '4', date: '2024-09-08T16:00:00', action: 'Returned', detail: 'Dikembalikan - kelebihan produksi', user: 'Production' }
    ]
  }
])

// Computed properties
const uniqueLocations = computed(() => {
  return [...new Set(materialItems.value.map(item => item.lokasi))].sort()
})

const filteredItems = computed(() => {
  let filtered = materialItems.value

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(item => 
      item.kode.toLowerCase().includes(query) ||
      item.nama.toLowerCase().includes(query) ||
      item.lot.toLowerCase().includes(query)
    )
  }

  // Status filter
  if (filterStatus.value) {
    filtered = filtered.filter(item => item.status === filterStatus.value)
  }

  // Type filter
  if (filterType.value) {
    filtered = filtered.filter(item => item.type === filterType.value)
  }

  // Location filter
  if (filterLocation.value) {
    filtered = filtered.filter(item => item.lokasi === filterLocation.value)
  }

  // Sort
  filtered.sort((a, b) => {
    let aValue = a[sortColumn.value as keyof MaterialItem]
    let bValue = b[sortColumn.value as keyof MaterialItem]

    if (typeof aValue === 'string') aValue = aValue.toLowerCase()
    if (typeof bValue === 'string') bValue = bValue.toLowerCase()

    if (aValue < bValue) return sortDirection.value === 'asc' ? -1 : 1
    if (aValue > bValue) return sortDirection.value === 'asc' ? 1 : -1
    return 0
  })

  return filtered
})

// Methods
const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value
  localStorage.setItem('darkMode', isDarkMode.value.toString())
}

const sortBy = (column: string) => {
  if (sortColumn.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortColumn.value = column
    sortDirection.value = 'asc'
  }
}

const getRowClass = (item: MaterialItem) => {
  const now = new Date()
  const expiredDate = new Date(item.expiredDate)
  const daysToExpired = Math.ceil((expiredDate.getTime() - now.getTime()) / (1000 * 60 * 60 * 24))

  if (daysToExpired < 0) return 'bg-red-50 dark:bg-red-900/20'
  if (daysToExpired <= 30) return 'bg-yellow-50 dark:bg-yellow-900/20'
  return ''
}

const getExpiredClass = (expiredDate: string) => {
  const now = new Date()
  const expired = new Date(expiredDate)
  const daysToExpired = Math.ceil((expired.getTime() - now.getTime()) / (1000 * 60 * 60 * 24))

  if (daysToExpired < 0) return 'text-red-600 dark:text-red-400 font-semibold'
  if (daysToExpired <= 30) return 'text-yellow-600 dark:text-yellow-400 font-semibold'
  return 'text-gray-900 dark:text-white'
}

const getStatusClass = (status: string) => {
  const statusClasses: Record<string, string> = {
    'Waiting QC': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    'Karantina': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
    'Released': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    'Reject': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    'In Production': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    'Returned': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'
  }
  return statusClasses[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
}

const getAlertClass = (type: string) => {
  const alertClasses: Record<string, string> = {
    'error': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    'warning': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    'info': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
  }
  return alertClasses[type] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const formatDateTime = (date: string) => {
  return new Date(date).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const openDetailPanel = (item: MaterialItem) => {
  selectedItem.value = item
  showDetailPanel.value = true
}

const closeDetailPanel = () => {
  showDetailPanel.value = false
  setTimeout(() => {
    selectedItem.value = null
  }, 300)
}

const dismissAlert = (alertId: string) => {
  const index = alerts.value.findIndex(alert => alert.id === alertId)
  if (index > -1) {
    alerts.value.splice(index, 1)
  }
}

// Action methods
const openQRScanner = () => {
  alert('QR Scanner akan dibuka - integrasi dengan camera device')
}

const openImportModal = () => {
  alert('Import Modal akan dibuka - upload Excel/CSV')
}

const exportData = () => {
  const dataStr = JSON.stringify(filteredItems.value, null, 2)
  const dataBlob = new Blob([dataStr], { type: 'application/json' })
  const url = URL.createObjectURL(dataBlob)
  const link = document.createElement('a')
  link.href = url
  link.download = `wms_central_data_${new Date().toISOString().split('T')[0]}.json`
  link.click()
  URL.revokeObjectURL(url)
}

const openAddModal = () => {
  alert('Form tambah material baru akan dibuka')
}

const printQR = (item: MaterialItem) => {
  alert(`Print QR Code untuk ${item.kode} - ${item.nama}`)
}

const transferItem = (item: MaterialItem) => {
  alert(`Transfer item ${item.kode} ke lokasi baru`)
}

const reserveItem = (item: MaterialItem) => {
  alert(`Reserve item ${item.kode} untuk produksi`)
}

// Lifecycle
</script>