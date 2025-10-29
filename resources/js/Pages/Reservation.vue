<template>
  <AppLayout title="Riwayat Aktivitas">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Reservation Requests</h2>
      </div>

      <!-- Main Content -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header dengan Search dan Filter -->
        <div class="p-6 border-b border-gray-200">
          <div class="flex flex-col lg:flex-row gap-4 mb-4">
            <!-- Search Box -->
            <div class="flex-1">
              <div class="relative">
                <input v-model="searchQuery" type="text"
                  placeholder="Cari berdasarkan No Reservasi, Kategori, atau Detail Info..."
                  class="w-full px-4 py-2.5 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-700">
                <svg class="w-5 h-5 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor"
                  viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
            </div>

            <!-- Filter Button -->
            <button @click="showFilterPanel = !showFilterPanel"
              class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg flex items-center gap-2 transition-colors border border-gray-300">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
              </svg>
              Filter
              <span v-if="activeFiltersCount > 0" class="bg-blue-500 text-white text-xs px-2 py-0.5 rounded-full">{{
                activeFiltersCount }}</span>
            </button>

            <!-- Create Button -->
            <button @click="openCreateModal"
              class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-2 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Buat Request
            </button>
          </div>

          <!-- Filter Panel -->
          <div v-if="showFilterPanel" class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
              <!-- Filter by Category -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                <select v-model="filters.category"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 bg-white">
                  <option value="">Semua Kategori</option>
                  <option value="foh-rs">FOH & RS</option>
                  <option value="packaging">Request Packaging Material</option>
                  <option value="raw-material">Request Raw Material</option>
                  <option value="add">ADD (Additional Request)</option>
                </select>
              </div>

              <!-- Filter by Status -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select v-model="filters.status"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 bg-white">
                  <option value="">Semua Status</option>
                  <option value="Draft">Draft</option>
                  <option value="Submitted">Submitted</option>
                  <option value="Approved">Approved</option>
                  <option value="Rejected">Rejected</option>
                  <option value="Picking">Picking</option>
                  <option value="Done">Done</option>
                </select>
              </div>

              <!-- Filter by Date Range -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rentang Tanggal</label>
                <div class="flex gap-2">
                  <input v-model="filters.dateFrom" type="date"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 bg-white text-sm">
                  <input v-model="filters.dateTo" type="date"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 bg-white text-sm">
                </div>
              </div>
            </div>

            <!-- Filter Actions -->
            <div class="flex justify-between items-center">
              <div class="text-sm text-gray-600">
                Total hasil: {{ filteredRequests.length }} dari {{ requests.length }} requests
              </div>
              <div class="flex gap-2">
                <button @click="resetFilters" class="px-4 py-2 text-gray-600 hover:text-gray-800 text-sm">
                  Reset Filter
                </button>
                <button @click="showFilterPanel = false"
                  class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
                  Terapkan
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabel Requests -->
        <div class="p-6">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No
                    Reservasi</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal
                    Permintaan</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Detail
                    Info</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="filteredRequests.length === 0">
                  <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                    Tidak ada data yang ditemukan
                  </td>
                </tr>
                <tr v-for="request in filteredRequests" :key="request.id" class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">{{ request.noReservasi }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getCategoryClass(request.type)">
                      {{ getCategoryName(request.type) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{
                    formatDateTime(request.tanggalPermintaan) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ getDisplayText(request) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusClass(request.status)"
                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ request.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <button @click="viewDetail(request)"
                        class="bg-blue-50 text-blue-700 hover:bg-blue-100 px-3 py-1.5 rounded text-xs font-medium transition-colors">
                        Detail
                      </button>
                      <button @click="printForm(request)"
                        class="bg-green-50 text-green-700 hover:bg-green-100 px-3 py-1.5 rounded text-xs font-medium transition-colors">
                        Cetak
                      </button>
                      <!-- <button v-if="request.status === 'Submitted'" @click="approveRequest(request)"
                        class="bg-emerald-50 text-emerald-700 hover:bg-emerald-100 px-3 py-1.5 rounded text-xs font-medium transition-colors">
                        Approve
                      </button>
                      <button v-if="request.status === 'Submitted'" @click="rejectRequest(request)"
                        class="bg-red-50 text-red-700 hover:bg-red-100 px-3 py-1.5 rounded text-xs font-medium transition-colors">
                        Reject
                      </button> -->
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Modal Create Request -->
      <div v-if="showModal"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg w-full max-w-7xl mx-4 max-h-[90vh] overflow-y-auto shadow-xl">
          <div class="p-6">
            <!-- Header Modal -->
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-800">
                Buat Request Reservasi Baru
              </h3>
              <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Category Selection -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
              <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Kategori Request:</label>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <button v-for="category in categories" :key="category.id" @click="selectedCategory = category.id"
                  :class="[
                    'p-4 rounded-lg border-2 text-sm font-medium transition-all',
                    selectedCategory === category.id
                      ? 'border-blue-500 bg-blue-50 text-blue-700'
                      : 'border-gray-300 bg-white text-gray-700 hover:border-blue-300 hover:bg-blue-50'
                  ]">
                  {{ category.name }}
                </button>
              </div>
            </div>

            <!-- Form Fields (show only when category is selected) -->
            <div v-if="selectedCategory" class="space-y-6">
              <!-- Form Header Fields -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- No Reservasi (always) -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">No Reservasi</label>
                  <input v-model="formData.noReservasi" type="text" readonly
                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700">
                </div>

                <!-- Tanggal Permintaan (always) -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ selectedCategory === 'packaging' || selectedCategory === 'add' ? 'Tanggal & Jam Permintaan' :
                    'Tanggal Permintaan' }}
                  </label>
                  <input v-model="formData.tanggalPermintaan" type="datetime-local"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700">
                </div>

                <!-- FOH: Alasan Reservasi -->
                <div v-if="selectedCategory === 'foh-rs'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Reservasi</label>
                  <textarea v-model="formData.alasanReservasi" rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700"
                    placeholder="Jelaskan alasan reservasi..."></textarea>
                </div>
              </div>

              <!-- Row 2 - Category specific fields -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- FOH & RS: Departemen -->
                <div v-if="selectedCategory === 'foh-rs'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                  <select v-model="formData.departemen"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700">
                    <option value="">Pilih Departemen</option>
                    <option value="FOH">FOH (Front of House)</option>
                    <option value="RS">RS (Restaurant Service)</option>
                    <option value="Kitchen">Kitchen</option>
                    <option value="Bar">Bar</option>
                  </select>
                </div>

                <!-- Packaging & ADD: Nama Produk -->
                <div v-if="selectedCategory === 'packaging' || selectedCategory === 'add'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                  <input v-model="formData.namaProduk" type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700">
                </div>

                <!-- Raw Material: Kode Produk -->
                <div v-if="selectedCategory === 'raw-material'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Kode Produk</label>
                  <input v-model="formData.kodeProduk" type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700">
                </div>

                <!-- Packaging & ADD: No Bets Filling -->
                <div v-if="selectedCategory === 'packaging' || selectedCategory === 'add'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">No Bets Filling / Mixing</label>
                  <input v-model="formData.noBetsFilling" type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700">
                </div>

                <!-- Raw Material: No Bets -->
                <div v-if="selectedCategory === 'raw-material'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">No Bets Ekstrak / Mixing</label>
                  <input v-model="formData.noBets" type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700">
                </div>

                <!-- Raw Material: Besar Bets -->
                <div v-if="selectedCategory === 'raw-material'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Besar Bets (Kg)</label>
                  <input v-model="formData.besarBets" type="number"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700">
                </div>
              </div>

              <!-- Items Table -->
              <div class="border-t border-gray-200 pt-6">
                <div class="flex justify-between items-center mb-4">
                  <h4 class="text-md font-medium text-gray-800">
                    {{ getItemTableTitle }}
                  </h4>
                  <button @click="addNewItem"
                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm font-medium transition-colors">
                    + Tambah Baris
                  </button>
                </div>

                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th
                          class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap w-16">
                          No</th>

                        <!-- FOH & RS Headers -->
                        <template v-if="selectedCategory === 'foh-rs'">
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Kode Item</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Keterangan</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Qty</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            UoM</th>
                        </template>

                        <!-- Packaging Headers -->
                        <template v-if="selectedCategory === 'packaging'">
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Nama Material</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Kode PM</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Jumlah Permintaan</th>
                        </template>

                        <!-- Raw Material Headers -->
                        <template v-if="selectedCategory === 'raw-material'">
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Kode Bahan</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Nama Bahan</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Jumlah Kebutuhan</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Jumlah Kirim</th>
                        </template>

                        <!-- ADD Headers -->
                        <template v-if="selectedCategory === 'add'">
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Nama Material</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Kode PM</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Alasan Penambahan</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Jumlah Permintaan</th>
                        </template>

                        <th
                          class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap w-24">
                          Aksi</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                      <tr v-for="(item, index) in formData.items" :key="index">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ index + 1 }}</td>

                        <!-- FOH & RS Row -->
                        <template v-if="selectedCategory === 'foh-rs'">
                          <td class="px-4 py-3 whitespace-nowrap">
                            <input v-model="item.kodeItem" type="text"
                              class="w-full min-w-[120px] text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                          </td>
                          <td class="px-4 py-3 whitespace-nowrap">
                            <input v-model="item.keterangan" type="text"
                              class="w-full min-w-[150px] text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                          </td>
                          <td class="px-4 py-3 whitespace-nowrap">
                            <input v-model="item.qty" type="number"
                              class="w-full min-w-[80px] text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                          </td>
                          <td class="px-4 py-3 whitespace-nowrap">
                            <select v-model="item.uom"
                              class="w-full min-w-[80px] text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                              <option value="">UoM</option>
                              <option value="PCS">PCS</option>
                              <option value="KG">KG</option>
                              <option value="L">L</option>
                            </select>
                          </td>
                        </template>

                        <!-- Packaging Row -->
                        <template v-if="selectedCategory === 'packaging'">
                          <td class="px-4 py-3 whitespace-nowrap">
                            <input v-model="item.namaMaterial" type="text"
                              class="w-full min-w-[150px] text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                          </td>
                          <td class="px-4 py-3 whitespace-nowrap">
                            <input v-model="item.kodePM" type="text"
                              class="w-full min-w-[120px] text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                          </td>
                          <td class="px-4 py-3 whitespace-nowrap">
                            <input v-model="item.jumlahPermintaan" type="number"
                              class="w-full min-w-[120px] text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                          </td>
                        </template>

                        <td class="px-4 py-3 whitespace-nowrap">
                          <button @click="removeItem(index)"
                            class="bg-red-50 text-red-700 hover:bg-red-100 px-2 py-1 rounded text-xs font-medium transition-colors">Hapus</button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Footer Modal -->
              <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <button @click="closeModal"
                  class="px-6 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                  Batal
                </button>
                <button @click="submitRequest" :disabled="!isFormValid"
                  class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                  Simpan & Submit
                </button>
              </div>
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
import axios from 'axios'

// Data reaktif
const showModal = ref(false)
const selectedCategory = ref('')
const requests = ref([])
const showFilterPanel = ref(false)
const searchQuery = ref('')

// Filter state
const filters = ref({
  category: '',
  status: '',
  dateFrom: '',
  dateTo: ''
})

// Category configuration
const categories = [
  { id: 'foh-rs', name: 'FOH & RS' },
  { id: 'packaging', name: 'Request Packaging Material' },
  { id: 'raw-material', name: 'Request Raw Material' },
  { id: 'add', name: 'ADD (Additional Request)' }
]

// Form data
const formData = ref({
  noReservasi: '',
  tanggalPermintaan: '',
  // FOH & RS
  departemen: '',
  alasanReservasi: '',
  // Packaging & ADD
  namaProduk: '',
  noBetsFilling: '',
  // Raw Material
  kodeProduk: '',
  noBets: '',
  besarBets: '',
  // Items array
  items: []
})

// Fetch reservation requests from the backend
const fetchReservations = async () => {
  try {
    const response = await axios.get('/api/reservations')
    requests.value = response.data
  } catch (error) {
    console.error('Error fetching reservations:', error)
  }
}

// Computed properties
const getCategoryName = (type) => {
  const category = categories.find(c => c.id === type)
  return category ? category.name : type
}

const getCategoryClass = (type) => {
  const classes = {
    'foh-rs': 'bg-blue-100 text-blue-700',
    'packaging': 'bg-green-100 text-green-700',
    'raw-material': 'bg-purple-100 text-purple-700',
    'add': 'bg-orange-100 text-orange-700'
  }
  return classes[type] || 'bg-gray-100 text-gray-700'
}

const getItemTableTitle = computed(() => {
  switch (selectedCategory.value) {
    case 'foh-rs': return 'Daftar Item'
    case 'packaging': return 'Daftar Material'
    case 'raw-material': return 'Daftar Bahan Baku'
    case 'add': return 'Daftar Item Tambahan'
    default: return 'Daftar Item'
  }
})

const isFormValid = computed(() => {
  if (!selectedCategory.value || !formData.value.tanggalPermintaan || formData.value.items.length === 0) {
    return false
  }

  switch (selectedCategory.value) {
    case 'foh-rs':
      return formData.value.departemen && formData.value.alasanReservasi
    case 'packaging':
      return formData.value.namaProduk && formData.value.noBetsFilling
    case 'raw-material':
      return formData.value.kodeProduk && formData.value.noBets && formData.value.besarBets
    case 'add':
      return formData.value.namaProduk && formData.value.noBetsFilling
    default:
      return false
  }
})

// Filter computed properties
const filteredRequests = computed(() => {
  let result = requests.value

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(req => {
      return req.noReservasi.toLowerCase().includes(query) ||
        getCategoryName(req.type).toLowerCase().includes(query) ||
        getDisplayText(req).toLowerCase().includes(query) ||
        req.status.toLowerCase().includes(query)
    })
  }

  // Category filter
  if (filters.value.category) {
    result = result.filter(req => req.type === filters.value.category)
  }

  // Status filter
  if (filters.value.status) {
    result = result.filter(req => req.status === filters.value.status)
  }

  // Date range filter
  if (filters.value.dateFrom) {
    const fromDate = new Date(filters.value.dateFrom)
    result = result.filter(req => new Date(req.tanggalPermintaan) >= fromDate)
  }

  if (filters.value.dateTo) {
    const toDate = new Date(filters.value.dateTo)
    toDate.setHours(23, 59, 59, 999)
    result = result.filter(req => new Date(req.tanggalPermintaan) <= toDate)
  }

  return result
})

const activeFiltersCount = computed(() => {
  let count = 0
  if (filters.value.category) count++
  if (filters.value.status) count++
  if (filters.value.dateFrom) count++
  if (filters.value.dateTo) count++
  return count
})

// Methods
const getStatusClass = (status) => {
  const classes = {
    'Draft': 'bg-gray-100 text-gray-700',
    'Submitted': 'bg-blue-100 text-blue-700',
    'Approved': 'bg-green-100 text-green-700',
    'Rejected': 'bg-red-100 text-red-700',
    'Picking': 'bg-yellow-100 text-yellow-700',
    'Done': 'bg-purple-100 text-purple-700'
  }
  return classes[status] || 'bg-gray-100 text-gray-700'
}

const getDisplayText = (request) => {
  switch (request.type) {
    case 'foh-rs': return request.departemen
    case 'packaging': return request.namaProduk
    case 'raw-material': return request.kodeProduk
    case 'add': return request.namaProduk
    default: return '-'
  }
}

const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleString('id-ID', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const resetFilters = () => {
  filters.value = {
    category: '',
    status: '',
    dateFrom: '',
    dateTo: ''
  }
}

const openCreateModal = () => {
  resetForm()
  generateReservationNumber()
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  resetForm()
}

const resetForm = () => {
  selectedCategory.value = ''
  formData.value = {
    noReservasi: '',
    tanggalPermintaan: new Date().toISOString().slice(0, 16),
    departemen: '',
    alasanReservasi: '',
    namaProduk: '',
    noBetsFilling: '',
    kodeProduk: '',
    noBets: '',
    besarBets: '',
    items: []
  }
}

const generateReservationNumber = () => {
  const today = new Date().toISOString().slice(0, 10).replace(/-/g, '')
  const count = requests.value.length + 1
  formData.value.noReservasi = `RSV/${today}/${String(count).padStart(4, '0')}`
}

const addNewItem = () => {
  const newItem = {}

  switch (selectedCategory.value) {
    case 'foh-rs':
      newItem.kodeItem = ''
      newItem.keterangan = ''
      newItem.qty = ''
      newItem.uom = ''
      break
    case 'packaging':
      newItem.namaMaterial = ''
      newItem.kodePM = ''
      newItem.jumlahPermintaan = ''
      break
    case 'raw-material':
      newItem.kodeBahan = ''
      newItem.namaBahan = ''
      newItem.jumlahKebutuhan = ''
      newItem.jumlahKirim = ''
      break
    case 'add':
      newItem.namaMaterial = ''
      newItem.kodePM = ''
      newItem.alasanPenambahan = ''
      newItem.jumlahPermintaan = ''
      break
  }

  formData.value.items.push(newItem)
}

const removeItem = (index) => {
  formData.value.items.splice(index, 1)
}

const submitRequest = async () => {
  try {
    const response = await axios.post('/transaction/reservation', formData.value)
    if (response.status === 200) {
      alert('Reservation created successfully.')
      closeModal()
      fetchReservations()
    }
  } catch (error) {
    console.error('Error creating reservation:', error)
    alert('Failed to create reservation.')
  }
}

const viewDetail = (request) => {
  alert(`Melihat detail request: ${request.noReservasi}`)
}

const printForm = (request) => {
  // Create print window with form template
  const printWindow = window.open('', '_blank')

  let formTitle = ''
  let formContent = ''

  switch (request.type) {
    case 'foh-rs':
      formTitle = 'FORM FOH & RS REQUEST'
      formContent = `
        <div class="info-section">
          <div class="info-row">
            <span><strong>No Reservasi:</strong> ${request.noReservasi}</span>
            <span><strong>Tanggal:</strong> ${formatDateTime(request.tanggalPermintaan)}</span>
          </div>
          <div class="info-row">
            <span><strong>Departemen:</strong> ${request.departemen}</span>
            <span><strong>Status:</strong> ${request.status}</span>
          </div>
          <div class="info-row">
            <span><strong>Alasan Reservasi:</strong> ${request.alasanReservasi || '-'}</span>
          </div>
        </div>
        
        <table class="items-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Kode Item</th>
              <th>Keterangan</th>
              <th>Qty</th>
              <th>UoM</th>
            </tr>
          </thead>
          <tbody>
          ${request.items.map((item, idx) => `
            <tr>
              <td>${idx + 1}</td>
              <td>${item.kodeItem}</td>
              <td>${item.keterangan}</td>
              <td>${item.qty}</td>
              <td>${item.uom}</td>
            </tr>
          `).join('')}
          </tbody>
        </table>
      `
      break

    case 'packaging':
      formTitle = 'FORM REQUEST PACKAGING MATERIAL'
      formContent = `
        <div class="info-section">
          <div class="info-row">
            <span><strong>No Reservasi:</strong> ${request.noReservasi}</span>
            <span><strong>Tanggal & Jam:</strong> ${formatDateTime(request.tanggalPermintaan)}</span>
          </div>
          <div class="info-row">
            <span><strong>Nama Produk:</strong> ${request.namaProduk}</span>
            <span><strong>No Bets Filling:</strong> ${request.noBetsFilling}</span>
          </div>
        </div>
        
        <table class="items-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Material</th>
              <th>Kode PM</th>
              <th>Jumlah Permintaan</th>
            </tr>
          </thead>
          <tbody>
          ${request.items.map((item, idx) => `
            <tr>
              <td>${idx + 1}</td>
              <td>${item.namaMaterial}</td>
              <td>${item.kodePM}</td>
              <td>${item.jumlahPermintaan}</td>
            </tr>
          `).join('')}
          </tbody>
        </table>
      `
      break

    case 'raw-material':
      formTitle = 'FORM REQUEST RAW MATERIAL'
      formContent = `
        <div class="info-section">
          <div class="info-row">
            <span><strong>No Reservasi:</strong> ${request.noReservasi}</span>
            <span><strong>Tanggal:</strong> ${formatDateTime(request.tanggalPermintaan)}</span>
          </div>
          <div class="info-row">
            <span><strong>Kode Produk:</strong> ${request.kodeProduk}</span>
            <span><strong>No Bets:</strong> ${request.noBets}</span>
          </div>
          <div class="info-row">
            <span><strong>Besar Bets:</strong> ${request.besarBets} Kg</span>
          </div>
        </div>
        
        <table class="items-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Kode Bahan</th>
              <th>Nama Bahan</th>
              <th>Jumlah Kebutuhan</th>
              <th>Jumlah Kirim</th>
            </tr>
          </thead>
          <tbody>
          ${request.items.map((item, idx) => `
            <tr>
              <td>${idx + 1}</td>
              <td>${item.kodeBahan}</td>
              <td>${item.namaBahan}</td>
              <td>${item.jumlahKebutuhan}</td>
              <td>${item.jumlahKirim}</td>
            </tr>
          `).join('')}
          </tbody>
        </table>
      `
      break

    case 'add':
      formTitle = 'FORM ADD - ADDITIONAL REQUEST'
      formContent = `
        <div class="info-section">
          <div class="info-row">
            <span><strong>No Reservasi:</strong> ${request.noReservasi}</span>
            <span><strong>Tanggal & Jam:</strong> ${formatDateTime(request.tanggalPermintaan)}</span>
          </div>
          <div class="info-row">
            <span><strong>Nama Produk:</strong> ${request.namaProduk}</span>
            <span><strong>No Bets Filling:</strong> ${request.noBetsFilling}</span>
          </div>
        </div>
        
        <table class="items-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Material</th>
              <th>Kode PM</th>
              <th>Alasan Penambahan</th>
              <th>Jumlah Permintaan</th>
            </tr>
          </thead>
          <tbody>
          ${request.items.map((item, idx) => `
            <tr>
              <td>${idx + 1}</td>
              <td>${item.namaMaterial}</td>
              <td>${item.kodePM}</td>
              <td>${item.alasanPenambahan}</td>
              <td>${item.jumlahPermintaan}</td>
            </tr>
          `).join('')}
          </tbody>
        </table>
      `
      break
  }

  printWindow.document.write(`
    <html>
      <head>
        <title>${formTitle} - ${request.noReservasi}</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; }
          .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
          .info-section { margin: 20px 0; }
          .info-row { display: flex; justify-content: space-between; margin: 8px 0; }
          .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
          .items-table th, .items-table td { border: 1px solid #000; padding: 8px; text-align: left; }
          .items-table th { background-color: #f0f0f0; font-weight: bold; }
          .signature { margin-top: 40px; display: flex; justify-content: space-between; }
          .signature div { text-align: center; }
          .signature-line { border-top: 1px solid #000; width: 150px; margin-top: 40px; }
          @media print {
            body { margin: 0; font-size: 11px; }
          }
        </style>
      </head>
      <body>
        <div class="header">
          <h2>${formTitle}</h2>
          <p>No: ${request.noReservasi}</p>
        </div>
        
        ${formContent}
        
        <div class="signature">
          <div>
            <p>Dibuat Oleh:</p>
            <div class="signature-line"></div>
            <p>Requester</p>
          </div>
          <div>
            <p>Disetujui Oleh:</p>
            <div class="signature-line"></div>
            <p>Supervisor</p>
          </div>
          <div>
            <p>Tanggal:</p>
            <div class="signature-line"></div>
            <p>${new Date().toLocaleDateString('id-ID')}</p>
          </div>
        </div>
      </body>
    </html>
  `)

  printWindow.document.close()
  printWindow.focus()

  setTimeout(() => {
    printWindow.print()
    printWindow.close()
  }, 500)
}

const approveRequest = (request) => {
  if (confirm(`Apakah Anda yakin ingin APPROVE request ${request.noReservasi}?`)) {
    const requestIndex = requests.value.findIndex(r => r.id === request.id)
    if (requestIndex !== -1) {
      requests.value[requestIndex].status = 'Approved'
    }

    alert(`Request ${request.noReservasi} berhasil di-APPROVE!\n\nSistem akan:\n- Lock stok sesuai FIFO/FEFO\n- Buat Picking Task\n- Masuk ke Halaman Picking`)
  }
}

const rejectRequest = (request) => {
  if (confirm(`Apakah Anda yakin ingin REJECT request ${request.noReservasi}?`)) {
    const requestIndex = requests.value.findIndex(r => r.id === request.id)
    if (requestIndex !== -1) {
      requests.value[requestIndex].status = 'Rejected'
    }

    alert(`Request ${request.noReservasi} berhasil di-REJECT!`)
  }
}

// Lifecycle
onMounted(() => {
  fetchReservations()
})
</script>