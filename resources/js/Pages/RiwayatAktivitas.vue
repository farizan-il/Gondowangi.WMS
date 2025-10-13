<template>
    <AppLayout title="Riwayat Aktivitas">
        <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold text-gray-900">Riwayat Aktivitas</h2>
      <div class="flex space-x-3">
        <button @click="exportToExcel" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Export Excel
        </button>
        <button @click="exportToPDF" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
          </svg>
          Export PDF
        </button>
      </div>
    </div>

    <!-- Advanced Filter Panel -->
    <div class="bg-white rounded-lg shadow">
      <div class="p-4 border-b border-gray-200">
        <button @click="showAdvancedFilter = !showAdvancedFilter" class="flex items-center justify-between w-full text-left">
          <h3 class="text-lg font-semibold text-gray-900">Advanced Filter</h3>
          <svg class="w-5 h-5 transform transition-transform" :class="showAdvancedFilter ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
      </div>

      <div v-show="showAdvancedFilter" class="p-6 space-y-4">
        <!-- Row 1: Time Filters -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Quick Time Filter</label>
            <select v-model="filters.quickTime" @change="applyQuickTimeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
              <option value="">Custom Range</option>
              <option value="today">Hari Ini</option>
              <option value="this_week">Minggu Ini</option>
              <option value="this_month">Bulan Ini</option>
              <option value="last_month">Bulan Lalu</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Awal</label>
            <input v-model="filters.startDate" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
            <input v-model="filters.endDate" type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select v-model="filters.status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
              <option value="">Semua Status</option>
              <option value="Success">Success</option>
              <option value="Pending">Pending</option>
              <option value="Rejected">Rejected</option>
              <option value="Completed">Completed</option>
            </select>
          </div>
        </div>

        <!-- Row 2: User & Activity Filters -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <select v-model="filters.role" @change="updateUsersByRole" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
              <option value="">Semua Role</option>
              <option value="Admin">Admin</option>
              <option value="QC Staff">QC Staff</option>
              <option value="Warehouse">Warehouse</option>
              <option value="Production">Production</option>
              <option value="Supervisor">Supervisor</option>
              <option value="Receiving Staff">Receiving Staff</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
            <select v-model="filters.user" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
              <option value="">Semua User</option>
              <option v-for="user in availableUsers" :key="user" :value="user">{{ user }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Aktivitas</label>
            <select v-model="filters.module" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
              <option value="">Semua Aktivitas</option>
              <option value="Incoming">Incoming / Receipt</option>
              <option value="QC">QC</option>
              <option value="Karantina">Label Karantina</option>
              <option value="Putaway">Putaway</option>
              <option value="Reservation">Reservation</option>
              <option value="Picking">Picking</option>
              <option value="Return">Return</option>
              <option value="Master Data">Master Data</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi/Bin</label>
            <select v-model="filters.bin" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
              <option value="">Semua Lokasi</option>
              <option v-for="bin in availableBins" :key="bin" :value="bin">{{ bin }}</option>
            </select>
          </div>
        </div>

        <!-- Row 3: Material & Quantity Filters -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Material/SKU</label>
            <input v-model="filters.sku" type="text" placeholder="Cari kode atau nama SKU..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Lot/Batch No</label>
            <input v-model="filters.lotBatch" type="text" placeholder="Cari lot/batch..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Qty Min</label>
            <input v-model="filters.qtyMin" type="number" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Qty Max</label>
            <input v-model="filters.qtyMax" type="number" placeholder="9999" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
          </div>
        </div>

        <!-- Filter Actions -->
        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
          <div class="text-sm text-gray-600">
            Showing {{ filteredActivities.length }} of {{ activities.length }} records
          </div>
          <div class="flex space-x-3">
            <button @click="resetFilters" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
              Reset Filter
            </button>
            <button @click="applyFilters" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
              Apply Filter
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Activity Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role/User</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktivitas</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material/SKU</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lot/Batch</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi/Bin</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referensi Dokumen</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="activity in paginatedActivities" :key="activity.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <div>{{ formatDateTime(activity.timestamp) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <div class="font-medium">{{ activity.user }}</div>
                <div class="text-xs text-gray-500">{{ activity.role }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <span class="px-2 py-1 text-xs rounded-full font-medium" :class="getModuleBadgeClass(activity.module)">
                    {{ activity.module }}
                  </span>
                  <div class="ml-2 text-sm text-gray-900">{{ activity.action }}</div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <div class="font-medium">{{ activity.sku_code }}</div>
                <div class="text-xs text-gray-500">{{ activity.sku_name }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ activity.lot_no || '-' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <div v-if="activity.qty_before !== activity.qty_after">
                  <span class="text-red-600">{{ activity.qty_before }}</span>
                  <span class="mx-1">→</span>
                  <span class="text-green-600">{{ activity.qty_after }}</span>
                </div>
                <div v-else>{{ activity.qty_after || '-' }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <div v-if="activity.bin_from !== activity.bin_to && activity.bin_from && activity.bin_to">
                  <span class="text-red-600">{{ activity.bin_from }}</span>
                  <span class="mx-1">→</span>
                  <span class="text-green-600">{{ activity.bin_to }}</span>
                </div>
                <div v-else>{{ activity.bin_to || activity.bin_from || '-' }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ activity.reference_no || '-' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button @click="showDetailModal(activity)" class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1 rounded text-xs">
                  Detail
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center text-sm text-gray-700">
            <span>Show</span>
            <select v-model="perPage" @change="updatePagination" class="mx-2 px-2 py-1 border border-gray-300 rounded text-sm bg-white">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
            <span>entries</span>
          </div>
          <div class="flex items-center space-x-2">
            <button @click="previousPage" :disabled="currentPage === 1" class="px-3 py-1 border border-gray-300 rounded text-sm bg-white text-gray-700 disabled:opacity-50">
              Previous
            </button>
            <span class="text-sm text-gray-700">
              {{ currentPage }} of {{ totalPages }}
            </span>
            <button @click="nextPage" :disabled="currentPage === totalPages" class="px-3 py-1 border border-gray-300 rounded text-sm bg-white text-gray-700 disabled:opacity-50">
              Next
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <div v-if="showDetailModalFlag" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: rgba(43, 51, 63, 0.67);">
      <div class="bg-white rounded-lg max-w-4xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Detail Activity Log</h3>
            <button @click="closeDetailModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <div v-if="selectedActivity" class="space-y-6">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-4">
                <div>
                  <label class="text-sm font-medium text-gray-700">Timestamp</label>
                  <p class="text-gray-900">{{ formatDateTime(selectedActivity.timestamp) }}</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-gray-700">User & Role</label>
                  <p class="text-gray-900">{{ selectedActivity.user }} ({{ selectedActivity.role }})</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-gray-700">Module</label>
                  <span class="px-2 py-1 text-xs rounded-full font-medium" :class="getModuleBadgeClass(selectedActivity.module)">
                    {{ selectedActivity.module }}
                  </span>
                </div>
                <div>
                  <label class="text-sm font-medium text-gray-700">Activity</label>
                  <p class="text-gray-900">{{ selectedActivity.action }}</p>
                </div>
              </div>
              
              <div class="space-y-4">
                <div>
                  <label class="text-sm font-medium text-gray-700">Device</label>
                  <p class="text-gray-900">{{ selectedActivity.device }}</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-gray-700">IP Address</label>
                  <p class="text-gray-900">{{ selectedActivity.ip_address }}</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-gray-700">Reference Document</label>
                  <p class="text-gray-900">{{ selectedActivity.reference_no || 'N/A' }}</p>
                </div>
              </div>
            </div>

            <!-- Material Info -->
            <div class="border-t border-gray-200 pt-6">
              <h4 class="text-md font-medium text-gray-900 mb-4">Material Information</h4>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label class="text-sm font-medium text-gray-700">SKU</label>
                  <p class="text-gray-900">{{ selectedActivity.sku_code }} - {{ selectedActivity.sku_name }}</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-gray-700">Lot/Batch</label>
                  <p class="text-gray-900">{{ selectedActivity.lot_no || 'N/A' }}</p>
                </div>
                <div>
                  <label class="text-sm font-medium text-gray-700">Exp Date</label>
                  <p class="text-gray-900">{{ selectedActivity.exp_date || 'N/A' }}</p>
                </div>
              </div>
            </div>

            <!-- Changes -->
            <div class="border-t border-gray-200 pt-6">
              <h4 class="text-md font-medium text-gray-900 mb-4">Changes</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="text-sm font-medium text-gray-700">Quantity</label>
                  <div class="flex items-center space-x-2">
                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded">{{ selectedActivity.qty_before }}</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded">{{ selectedActivity.qty_after }}</span>
                  </div>
                </div>
                <div>
                  <label class="text-sm font-medium text-gray-700">Location</label>
                  <div class="flex items-center space-x-2">
                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded">{{ selectedActivity.bin_from || 'N/A' }}</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded">{{ selectedActivity.bin_to || 'N/A' }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Remarks -->
            <div v-if="selectedActivity.remarks" class="border-t border-gray-200 pt-6">
              <h4 class="text-md font-medium text-gray-900 mb-2">Remarks</h4>
              <p class="text-gray-600 bg-gray-50 p-3 rounded">{{ selectedActivity.remarks }}</p>
            </div>
          </div>

          <div class="flex justify-end mt-6 pt-6 border-t border-gray-200">
            <button @click="closeDetailModal" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed, onMounted } from 'vue'

// Reactive data
const showAdvancedFilter = ref(true)
const showDetailModalFlag = ref(false)
const selectedActivity = ref(null)
const currentPage = ref(1)
const perPage = ref(25)

// Filter data
const filters = ref({
  quickTime: '',
  startDate: '',
  endDate: '',
  role: '',
  user: '',
  module: '',
  sku: '',
  lotBatch: '',
  qtyMin: '',
  qtyMax: '',
  bin: '',
  status: ''
})

// Dummy data
const activities = ref([])
const availableUsers = ref(['ADM001', 'QC001', 'QC002', 'WH001', 'WH002', 'WH003', 'PD001', 'PD002', 'SUP001'])
const availableBins = ref(['A-01-01', 'A-01-02', 'B-02-01', 'B-02-02', 'C-03-01', 'KARANTINA-001', 'STAGING-001', 'REJECT-001'])

// Initialize dummy data
const initDummyData = () => {
  const dummyActivities = [
    // Complete Journey Material: RM-005 Minyak Kelapa Sawit - LOT ABC123
    // Step 1: Incoming
    {
      id: 1,
      timestamp: '2025-09-15 08:00:00',
      user: 'WH002',
      role: 'Receiving Staff',
      module: 'Incoming',
      action: 'Input Penerimaan',
      sku_code: 'RM-005',
      sku_name: 'Minyak Kelapa Sawit',
      lot_no: 'ABC123',
      qty_before: 0,
      qty_after: 500,
      bin_from: null,
      bin_to: 'INCOMING-001',
      reference_no: 'IN/20250915/001',
      device: 'Receiving Scanner',
      ip_address: '192.168.1.102',
      remarks: 'Received 500L Minyak Kelapa Sawit from PT Sawit Makmur - PO789, Shipment SJ-789/2025',
      exp_date: '2026-09-15'
    },
    
    // Step 2: QC Process
    {
      id: 2,
      timestamp: '2025-09-15 10:30:00',
      user: 'QC001',
      role: 'QC Staff',
      module: 'QC',
      action: 'QC PASS',
      sku_code: 'RM-005',
      sku_name: 'Minyak Kelapa Sawit',
      lot_no: 'ABC123',
      qty_before: 500,
      qty_after: 485,
      bin_from: 'INCOMING-001',
      bin_to: 'KARANTINA-002',
      reference_no: 'QC/20250915/001',
      device: 'QC Scanner 01',
      ip_address: '192.168.1.101',
      remarks: 'QC PASS - Quality approved. 15L retained for lab analysis. Visual inspection OK, density test passed.',
      exp_date: '2026-09-15'
    },
    
    // Step 3: Karantina Release
    {
      id: 3,
      timestamp: '2025-09-16 09:15:00',
      user: 'SUP001',
      role: 'Supervisor',
      module: 'Karantina',
      action: 'Release Karantina',
      sku_code: 'RM-005',
      sku_name: 'Minyak Kelapa Sawit',
      lot_no: 'ABC123',
      qty_before: 485,
      qty_after: 485,
      bin_from: 'KARANTINA-002',
      bin_to: 'READY-FOR-PUTAWAY',
      reference_no: 'REL/20250916/001',
      device: 'Supervisor Terminal',
      ip_address: '192.168.1.401',
      remarks: 'Released from quarantine after lab analysis confirmation. Ready for storage allocation.',
      exp_date: '2026-09-15'
    },
    
    // Step 4: Putaway to Storage
    {
      id: 4,
      timestamp: '2025-09-16 14:20:00',
      user: 'WH001',
      role: 'Warehouse',
      module: 'Putaway',
      action: 'Putaway Completed',
      sku_code: 'RM-005',
      sku_name: 'Minyak Kelapa Sawit',
      lot_no: 'ABC123',
      qty_before: 485,
      qty_after: 485,
      bin_from: 'READY-FOR-PUTAWAY',
      bin_to: 'C-05-03',
      reference_no: 'TO/PUT/20250916/005',
      device: 'WH Scanner 01',
      ip_address: '192.168.1.201',
      remarks: 'Successfully stored in bulk liquid storage area C-05-03. FEFO position confirmed.',
      exp_date: '2026-09-15'
    },
    
    // Step 5: Production Reservation Request
    {
      id: 5,
      timestamp: '2025-10-02 07:45:00',
      user: 'PD001',
      role: 'Production',
      module: 'Reservation',
      action: 'Request RM',
      sku_code: 'RM-005',
      sku_name: 'Minyak Kelapa Sawit',
      lot_no: 'ABC123',
      qty_before: 485,
      qty_after: 485,
      bin_from: 'C-05-03',
      bin_to: 'C-05-03',
      reference_no: 'RSV/20250918/007',
      device: 'Production Terminal',
      ip_address: '192.168.1.301',
      remarks: 'Production request for 200L Minyak Kelapa Sawit for Batch MKS-2025-018 (Cooking Oil Production)',
      exp_date: '2026-09-15'
    },
    
    // Step 6: Reservation Approved
    {
      id: 6,
      timestamp: '2025-10-02 08:15:00',
      user: 'SUP001',
      role: 'Supervisor',
      module: 'Reservation',
      action: 'Reservation Approved',
      sku_code: 'RM-005',
      sku_name: 'Minyak Kelapa Sawit',
      lot_no: 'ABC123',
      qty_before: 485,
      qty_after: 485,
      bin_from: 'C-05-03',
      bin_to: 'C-05-03',
      reference_no: 'RSV/20250918/007',
      device: 'Supervisor Terminal',
      ip_address: '192.168.1.401',
      remarks: 'Reservation approved. Stock allocated for production. Picking task created automatically.',
      exp_date: '2026-09-15'
    },
    
    // Step 7: Picking Process
    {
      id: 7,
      timestamp: '2025-10-02 10:30:00',
      user: 'WH003',
      role: 'Warehouse',
      module: 'Picking',
      action: 'Picking Completed',
      sku_code: 'RM-005',
      sku_name: 'Minyak Kelapa Sawit',
      lot_no: 'ABC123',
      qty_before: 485,
      qty_after: 285,
      bin_from: 'C-05-03',
      bin_to: 'PROD-STAGING-A',
      reference_no: 'TO/PICK/20250918/003',
      device: 'WH Scanner 03',
      ip_address: '192.168.1.203',
      remarks: 'Picked 200L for production. Transferred to production staging area. Remaining stock: 285L in storage.',
      exp_date: '2026-09-15'
    },
    
    // Step 8: Transfer to Production Area
    {
      id: 8,
      timestamp: '2025-10-02 11:45:00',
      user: 'PD002',
      role: 'Production',
      module: 'Picking',
      action: 'Material Received',
      sku_code: 'RM-005',
      sku_name: 'Minyak Kelapa Sawit',
      lot_no: 'ABC123',
      qty_before: 200,
      qty_after: 200,
      bin_from: 'PROD-STAGING-A',
      bin_to: 'PROD-LINE-A',
      reference_no: 'TO/PICK/20250918/003',
      device: 'Production Scanner',
      ip_address: '192.168.1.302',
      remarks: 'Material received at production line A. Ready for cooking oil production batch MKS-2025-018.',
      exp_date: '2026-09-15'
    },
    
    // Step 9: Production Usage (Consumption)
    {
      id: 9,
      timestamp: '2025-10-02 15:20:00',
      user: 'PD002',
      role: 'Production',
      module: 'Picking',
      action: 'Production Consumption',
      sku_code: 'RM-005',
      sku_name: 'Minyak Kelapa Sawit',
      lot_no: 'ABC123',
      qty_before: 200,
      qty_after: 25,
      bin_from: 'PROD-LINE-A',
      bin_to: 'PROD-LINE-A',
      reference_no: 'PROD/MKS-2025-018',
      device: 'Production Scanner',
      ip_address: '192.168.1.302',
      remarks: 'Used 175L in production process. 25L remaining (excess from recipe). Production batch completed successfully.',
      exp_date: '2026-09-15'
    },
    
    // Step 10: Return Excess Stock to Warehouse
    {
      id: 10,
      timestamp: '2025-10-02 16:30:00',
      user: 'PD002',
      role: 'Production',
      module: 'Return',
      action: 'Return Excess Production',
      sku_code: 'RM-005',
      sku_name: 'Minyak Kelapa Sawit',
      lot_no: 'ABC123',
      qty_before: 25,
      qty_after: 0,
      bin_from: 'PROD-LINE-A',
      bin_to: 'RETURN-STAGING',
      reference_no: 'RTP/20250918/001',
      device: 'Production Scanner',
      ip_address: '192.168.1.302',
      remarks: 'Returning 25L excess material to warehouse. Material condition: Good, unopened container.',
      exp_date: '2026-09-15'
    },
    
    // Step 11: Warehouse Receives Return Stock
    {
      id: 11,
      timestamp: '2025-10-02 17:15:00',
      user: 'WH001',
      role: 'Warehouse',
      module: 'Return',
      action: 'Return Stock Received',
      sku_code: 'RM-005',
      sku_name: 'Minyak Kelapa Sawit',
      lot_no: 'ABC123',
      qty_before: 285,
      qty_after: 310,
      bin_from: 'RETURN-STAGING',
      bin_to: 'C-05-03',
      reference_no: 'RTP/20250918/001',
      device: 'WH Scanner 01',
      ip_address: '192.168.1.201',
      remarks: 'Return stock received and added back to inventory. Total stock now: 310L. Container inspected - good condition.',
      exp_date: '2026-09-15'
    },

    // Other activities for different materials (original data)
    {
      id: 12,
      timestamp: '2025-10-02 09:15:00',
      user: 'QC001',
      role: 'QC Staff',
      module: 'QC',
      action: 'QC PASS',
      sku_code: 'RM-001',
      sku_name: 'Tepung Terigu',
      lot_no: 'BATCH123',
      qty_before: 100,
      qty_after: 95,
      bin_from: 'INCOMING-001',
      bin_to: 'KARANTINA-001',
      reference_no: 'QC/20250919/001',
      device: 'QC Scanner 01',
      ip_address: '192.168.1.101',
      remarks: 'QC Pass - 5 unit rejected due to packaging damage',
      exp_date: '2025-12-31'
    },
    {
      id: 13,
      timestamp: '2025-10-02 09:45:00',
      user: 'WH001',
      role: 'Warehouse',
      module: 'Putaway',
      action: 'Putaway Completed',
      sku_code: 'PM-001',
      sku_name: 'Kardus Box 20x30',
      lot_no: 'BATCH456',
      qty_before: 200,
      qty_after: 200,
      bin_from: 'KARANTINA-001',
      bin_to: 'A-01-01',
      reference_no: 'TO/2025/0007',
      device: 'WH Scanner 01',
      ip_address: '192.168.1.201',
      remarks: 'Successfully moved to storage location',
      exp_date: '2026-06-30'
    },
    {
      id: 14,
      timestamp: '2025-10-02 10:20:00',
      user: 'PD001',
      role: 'Production',
      module: 'Reservation',
      action: 'Request RM',
      sku_code: 'RM-002',
      sku_name: 'Gula Pasir',
      lot_no: null,
      qty_before: 0,
      qty_after: 50,
      bin_from: null,
      bin_to: null,
      reference_no: 'RSV/2025/009',
      device: 'Production Terminal',
      ip_address: '192.168.1.301',
      remarks: 'Raw material request for production batch PB-2025-001',
      exp_date: null
    },
    {
      id: 15,
      timestamp: '2025-10-02 06:00:00',
      user: 'ADM001',
      role: 'Admin',
      module: 'Master Data',
      action: 'Tambah SKU',
      sku_code: 'PM-003',
      sku_name: 'Label Produk A4',
      lot_no: null,
      qty_before: 0,
      qty_after: 0,
      bin_from: null,
      bin_to: null,
      reference_no: 'SKU/ADD/001',
      device: 'Admin PC',
      ip_address: '192.168.1.501',
      remarks: 'Added new packaging material SKU for Product Line A',
      exp_date: null
    }
  ]
  
  activities.value = dummyActivities
}

// Computed properties
const filteredActivities = computed(() => {
  let filtered = activities.value

  // Apply filters
  if (filters.value.startDate && filters.value.endDate) {
    filtered = filtered.filter(activity => {
      const activityDate = activity.timestamp.split(' ')[0]
      return activityDate >= filters.value.startDate && activityDate <= filters.value.endDate
    })
  }

  if (filters.value.role) {
    filtered = filtered.filter(activity => activity.role === filters.value.role)
  }

  if (filters.value.user) {
    filtered = filtered.filter(activity => activity.user === filters.value.user)
  }

  if (filters.value.module) {
    filtered = filtered.filter(activity => activity.module === filters.value.module)
  }

  if (filters.value.sku) {
    filtered = filtered.filter(activity => 
      activity.sku_code.toLowerCase().includes(filters.value.sku.toLowerCase()) ||
      activity.sku_name.toLowerCase().includes(filters.value.sku.toLowerCase())
    )
  }

  if (filters.value.lotBatch) {
    filtered = filtered.filter(activity => 
      activity.lot_no && activity.lot_no.toLowerCase().includes(filters.value.lotBatch.toLowerCase())
    )
  }

  if (filters.value.qtyMin) {
    filtered = filtered.filter(activity => activity.qty_after >= parseInt(filters.value.qtyMin))
  }

  if (filters.value.qtyMax) {
    filtered = filtered.filter(activity => activity.qty_after <= parseInt(filters.value.qtyMax))
  }

  if (filters.value.bin) {
    filtered = filtered.filter(activity => 
      activity.bin_from === filters.value.bin || activity.bin_to === filters.value.bin
    )
  }

  return filtered.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp))
})

const paginatedActivities = computed(() => {
  const start = (currentPage.value - 1) * perPage.value
  const end = start + perPage.value
  return filteredActivities.value.slice(start, end)
})

const totalPages = computed(() => {
  return Math.ceil(filteredActivities.value.length / perPage.value)
})

// Methods
const formatDateTime = (timestamp) => {
  return new Date(timestamp).toLocaleString('id-ID', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
}

const getModuleBadgeClass = (module) => {
  const classes = {
    'Incoming': 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200',
    'QC': 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200',
    'Karantina': 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200',
    'Putaway': 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200',
    'Reservation': 'bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200',
    'Picking': 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
    'Return': 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
    'Master Data': 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200'
  }
  return classes[module] || classes['Master Data']
}

const applyQuickTimeFilter = () => {
  const today = new Date()
  const yesterday = new Date(today)
  yesterday.setDate(today.getDate() - 1)

  switch (filters.value.quickTime) {
    case 'today':
      filters.value.startDate = today.toISOString().split('T')[0]
      filters.value.endDate = today.toISOString().split('T')[0]
      break
    case 'this_week':
      const weekStart = new Date(today.setDate(today.getDate() - today.getDay()))
      filters.value.startDate = weekStart.toISOString().split('T')[0]
      filters.value.endDate = new Date().toISOString().split('T')[0]
      break
    case 'this_month':
      filters.value.startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0]
      filters.value.endDate = new Date().toISOString().split('T')[0]
      break
    case 'last_month':
      const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1)
      const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0)
      filters.value.startDate = lastMonth.toISOString().split('T')[0]
      filters.value.endDate = lastMonthEnd.toISOString().split('T')[0]
      break
    default:
      filters.value.startDate = ''
      filters.value.endDate = ''
  }
}

const updateUsersByRole = () => {
  // Filter users based on selected role
  const usersByRole = {
    'Admin': ['ADM001'],
    'QC Staff': ['QC001', 'QC002'],
    'Warehouse': ['WH001', 'WH002', 'WH003'],
    'Production': ['PD001', 'PD002'],
    'Supervisor': ['SUP001'],
    'Receiving Staff': ['WH002']
  }
  
  if (filters.value.role) {
    availableUsers.value = usersByRole[filters.value.role] || []
  } else {
    availableUsers.value = ['ADM001', 'QC001', 'QC002', 'WH001', 'WH002', 'WH003', 'PD001', 'PD002', 'SUP001']
  }
  
  // Reset user filter if current user not in new list
  if (!availableUsers.value.includes(filters.value.user)) {
    filters.value.user = ''
  }
}

const resetFilters = () => {
  filters.value = {
    quickTime: '',
    startDate: '',
    endDate: '',
    role: '',
    user: '',
    module: '',
    sku: '',
    lotBatch: '',
    qtyMin: '',
    qtyMax: '',
    bin: '',
    status: ''
  }
  currentPage.value = 1
}

const applyFilters = () => {
  currentPage.value = 1
}

const showDetailModal = (activity) => {
  selectedActivity.value = activity
  showDetailModalFlag.value = true
}

const closeDetailModal = () => {
  showDetailModalFlag.value = false
  selectedActivity.value = null
}

const updatePagination = () => {
  currentPage.value = 1
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const exportToExcel = () => {
  // Create CSV content
  const headers = ['Timestamp', 'Role/User', 'Module', 'Activity', 'SKU Code', 'SKU Name', 'Lot/Batch', 'Qty Before', 'Qty After', 'Bin From', 'Bin To', 'Reference', 'Remarks']
  const csvContent = [
    headers.join(','),
    ...filteredActivities.value.map(activity => [
      activity.timestamp,
      `${activity.user} (${activity.role})`,
      activity.module,
      activity.action,
      activity.sku_code,
      activity.sku_name,
      activity.lot_no || '',
      activity.qty_before || '',
      activity.qty_after || '',
      activity.bin_from || '',
      activity.bin_to || '',
      activity.reference_no || '',
      activity.remarks || ''
    ].map(field => `"${field}"`).join(','))
  ].join('\n')

  // Download file
  const blob = new Blob([csvContent], { type: 'text/csv' })
  const url = window.URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = `activity_log_${new Date().toISOString().split('T')[0]}.csv`
  link.click()
  window.URL.revokeObjectURL(url)
}

const exportToPDF = () => {
  // Create print window for PDF export
  const printWindow = window.open('', '_blank')
  
  const htmlContent = `
    <html>
      <head>
        <title>Activity Log Report</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 20px; font-size: 10px; }
          .header { text-align: center; margin-bottom: 20px; }
          .filters { background-color: #f5f5f5; padding: 10px; margin-bottom: 20px; }
          table { width: 100%; border-collapse: collapse; }
          th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
          th { background-color: #f2f2f2; font-weight: bold; }
          .module-badge { padding: 2px 6px; border-radius: 4px; font-size: 8px; }
          @media print {
            body { margin: 0; font-size: 9px; }
          }
        </style>
      </head>
      <body>
        <div class="header">
          <h2>Activity Log Report</h2>
          <p>Generated on: ${new Date().toLocaleString('id-ID')}</p>
        </div>
        
        <div class="filters">
          <strong>Applied Filters:</strong>
          ${filters.value.startDate ? `Date Range: ${filters.value.startDate} to ${filters.value.endDate} | ` : ''}
          ${filters.value.role ? `Role: ${filters.value.role} | ` : ''}
          ${filters.value.module ? `Module: ${filters.value.module} | ` : ''}
          ${filters.value.sku ? `SKU: ${filters.value.sku} | ` : ''}
          Total Records: ${filteredActivities.value.length}
        </div>
        
        <table>
          <thead>
            <tr>
              <th>Timestamp</th>
              <th>User</th>
              <th>Module</th>
              <th>Activity</th>
              <th>SKU</th>
              <th>Lot/Batch</th>
              <th>Qty</th>
              <th>Location</th>
              <th>Reference</th>
            </tr>
          </thead>
          <tbody>
            ${filteredActivities.value.map(activity => `
              <tr>
                <td>${formatDateTime(activity.timestamp)}</td>
                <td>${activity.user}<br><small>${activity.role}</small></td>
                <td><span class="module-badge">${activity.module}</span></td>
                <td>${activity.action}</td>
                <td>${activity.sku_code}<br><small>${activity.sku_name}</small></td>
                <td>${activity.lot_no || '-'}</td>
                <td>${activity.qty_before !== activity.qty_after ? `${activity.qty_before} → ${activity.qty_after}` : (activity.qty_after || '-')}</td>
                <td>${activity.bin_from !== activity.bin_to && activity.bin_from && activity.bin_to ? `${activity.bin_from} → ${activity.bin_to}` : (activity.bin_to || activity.bin_from || '-')}</td>
                <td>${activity.reference_no || '-'}</td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      </body>
    </html>
  `
  
  printWindow.document.write(htmlContent)
  printWindow.document.close()
  printWindow.focus()
  
  setTimeout(() => {
    printWindow.print()
    printWindow.close()
  }, 500)
}

// Lifecycle
onMounted(() => {
  initDummyData()
  // Set default date range to last 7 days
  const today = new Date()
  const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000)
  filters.value.startDate = weekAgo.toISOString().split('T')[0]
  filters.value.endDate = today.toISOString().split('T')[0]
})
</script>