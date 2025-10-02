<template>
    <AppLayout title="Riwayat Aktivitas">
        <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Reservation Requests</h2>
    </div>

    <!-- Main Content -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
      <!-- Header dengan tombol Buat Request -->
      <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="text-sm text-gray-600 dark:text-gray-400">
          Total requests: {{ requests.length }}
        </div>
        <button
          @click="openCreateModal"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Buat Request
        </button>
      </div>

      <!-- Tabel Requests -->
      <div class="p-6">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No Reservasi</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kategori</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal Permintaan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Detail Info</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="request in requests" :key="request.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ request.noReservasi }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getCategoryClass(request.type)">
                    {{ getCategoryName(request.type) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ formatDateTime(request.tanggalPermintaan) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ getDisplayText(request) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusClass(request.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ request.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex space-x-2">
                    <button @click="viewDetail(request)" class="bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 hover:bg-blue-200 dark:hover:bg-blue-800 px-2 py-1 rounded text-xs">
                      Detail
                    </button>
                    <button @click="printForm(request)" class="bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-800 px-2 py-1 rounded text-xs">
                      Cetak Form
                    </button>
                    <button v-if="request.status === 'Submitted'" @click="approveRequest(request)" class="bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-800 px-2 py-1 rounded text-xs">
                      Approve
                    </button>
                    <button v-if="request.status === 'Submitted'" @click="rejectRequest(request)" class="bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-800 px-2 py-1 rounded text-xs">
                      Reject
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Create Request -->
    <div v-if="showModal" class="fixed inset-0 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: rgba(43, 51, 63, 0.67);">
      <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-7xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <!-- Header Modal -->
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
              Buat Request Reservasi Baru
            </h3>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Category Selection -->
          <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Pilih Kategori Request:</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
              <button
                v-for="category in categories"
                :key="category.id"
                @click="selectedCategory = category.id"
                :class="[
                  'p-4 rounded-lg border-2 text-sm font-medium transition-all',
                  selectedCategory === category.id
                    ? 'border-blue-500 bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300'
                    : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900'
                ]"
              >
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No Reservasi</label>
                <input v-model="formData.noReservasi" type="text" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-600 text-gray-900 dark:text-white">
              </div>

              <!-- Tanggal Permintaan (always) -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  {{ selectedCategory === 'packaging' || selectedCategory === 'add' ? 'Tanggal & Jam Permintaan' : 'Tanggal Permintaan' }}
                </label>
                <input v-model="formData.tanggalPermintaan" type="datetime-local" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
              </div>

              <!-- FOH: Alasan Reservasi -->
              <div v-if="selectedCategory === 'foh-rs'">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alasan Reservasi</label>
                <textarea v-model="formData.alasanReservasi" rows="2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Jelaskan alasan reservasi..."></textarea>
              </div>
            </div>

            <!-- Row 2 - Category specific fields -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <!-- FOH & RS: Departemen -->
              <div v-if="selectedCategory === 'foh-rs'">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Departemen</label>
                <select v-model="formData.departemen" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                  <option value="">Pilih Departemen</option>
                  <option value="FOH">FOH (Front of House)</option>
                  <option value="RS">RS (Restaurant Service)</option>
                  <option value="Kitchen">Kitchen</option>
                  <option value="Bar">Bar</option>
                </select>
              </div>

              <!-- Packaging & ADD: Nama Produk -->
              <div v-if="selectedCategory === 'packaging' || selectedCategory === 'add'">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Produk</label>
                <input v-model="formData.namaProduk" type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
              </div>

              <!-- Raw Material: Kode Produk -->
              <div v-if="selectedCategory === 'raw-material'">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode Produk</label>
                <input v-model="formData.kodeProduk" type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
              </div>

              <!-- Packaging & ADD: No Bets Filling -->
              <div v-if="selectedCategory === 'packaging' || selectedCategory === 'add'">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No Bets Filling / Mixing</label>
                <input v-model="formData.noBetsFilling" type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
              </div>

              <!-- Raw Material: No Bets -->
              <div v-if="selectedCategory === 'raw-material'">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No Bets Ekstrak / Mixing</label>
                <input v-model="formData.noBets" type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
              </div>

              <!-- Raw Material: Besar Bets -->
              <div v-if="selectedCategory === 'raw-material'">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Besar Bets (Kg)</label>
                <input v-model="formData.besarBets" type="number" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
              </div>
            </div>

            <!-- Items Table -->
            <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
              <div class="flex justify-between items-center mb-4">
                <h4 class="text-md font-medium text-gray-900 dark:text-white">
                  {{ getItemTableTitle }}
                </h4>
                <button @click="addNewItem" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                  + Tambah Baris
                </button>
              </div>

              <div class="overflow-x-auto border border-gray-200 dark:border-gray-600 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                  <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap w-16">No</th>
                      
                      <!-- FOH & RS Headers -->
                      <template v-if="selectedCategory === 'foh-rs'">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Kode Item</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Keterangan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Qty</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">UoM</th>
                      </template>

                      <!-- Packaging Headers -->
                      <template v-if="selectedCategory === 'packaging'">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Nama Material</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Kode PM</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Jumlah Permintaan</th>
                      </template>

                      <!-- Raw Material Headers -->
                      <template v-if="selectedCategory === 'raw-material'">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Kode Bahan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Nama Bahan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Jumlah Kebutuhan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Jumlah Kirim</th>
                      </template>

                      <!-- ADD Headers -->
                      <template v-if="selectedCategory === 'add'">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Nama Material</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Kode PM</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Alasan Penambahan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Jumlah Permintaan</th>
                      </template>

                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap w-24">Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 dark:divide-gray-600 bg-white dark:bg-gray-800">
                    <tr v-for="(item, index) in formData.items" :key="index">
                      <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ index + 1 }}</td>
                      
                      <!-- FOH & RS Row -->
                      <template v-if="selectedCategory === 'foh-rs'">
                        <td class="px-4 py-3 whitespace-nowrap">
                          <input v-model="item.kodeItem" type="text" class="w-full min-w-[120px] text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                          <input v-model="item.keterangan" type="text" class="w-full min-w-[150px] text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                          <input v-model="item.qty" type="number" class="w-full min-w-[80px] text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                          <select v-model="item.uom" class="w-full min-w-[80px] text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
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
                          <input v-model="item.namaMaterial" type="text" class="w-full min-w-[150px] text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                          <input v-model="item.kodePM" type="text" class="w-full min-w-[120px] text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                          <input v-model="item.jumlahPermintaan" type="number" class="w-full min-w-[120px] text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </td>
                      </template>

                      <!-- Raw Material Row -->
                      <template v-if="selectedCategory === 'raw-material'">
                        <td class="px-4 py-3 whitespace-nowrap">
                          <input v-model="item.kodeBahan" type="text" class="w-full min-w-[120px] text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                          <input v-model="item.namaBahan" type="text" class="w-full min-w-[150px] text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                          <input v-model="item.jumlahKebutuhan" type="number" class="w-full min-w-[120px] text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                          <input v-model="item.jumlahKirim" type="number" class="w-full min-w-[120px] text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </td>
                      </template>

                      <!-- ADD Row -->
                      <template v-if="selectedCategory === 'add'">
                        <td class="px-4 py-3 whitespace-nowrap">
                          <input v-model="item.namaMaterial" type="text" class="w-full min-w-[150px] text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                          <input v-model="item.kodePM" type="text" class="w-full min-w-[120px] text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                          <select v-model="item.alasanPenambahan" class="w-full min-w-[150px] text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">Pilih Alasan</option>
                            <option value="Reject Produksi">Reject Produksi</option>
                            <option value="Bulk Lebih">Bulk Lebih</option>
                            <option value="Reject Supplier">Reject Supplier</option>
                            <option value="Kurang Supplier">Kurang Supplier</option>
                          </select>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                          <input v-model="item.jumlahPermintaan" type="number" class="w-full min-w-[120px] text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </td>
                      </template>

                      <td class="px-4 py-3 whitespace-nowrap">
                        <button @click="removeItem(index)" class="bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-800 px-2 py-1 rounded text-xs">Hapus</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Footer Modal -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-600">
              <button @click="closeModal" class="px-6 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500">
                Batal
              </button>
              <button @click="submitRequest" :disabled="!isFormValid" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-400">
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

// Data reaktif
const showModal = ref(false)
const selectedCategory = ref('')
const requests = ref([])

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

// Initialize dummy data
const initDummyData = () => {
  requests.value = [
    {
      id: 1,
      type: 'foh-rs',
      noReservasi: 'RSV/20250918/0001',
      tanggalPermintaan: '2025-09-18T10:30:00',
      departemen: 'FOH',
      alasanReservasi: 'Kebutuhan untuk event spesial',
      status: 'Submitted',
      items: [
        { kodeItem: 'FOH-001', keterangan: 'Peralatan makan', qty: 50, uom: 'PCS' }
      ]
    },
    {
      id: 2,
      type: 'packaging',
      noReservasi: 'RSV/20250918/0002',
      tanggalPermintaan: '2025-09-18T14:15:00',
      namaProduk: 'Produk A',
      noBetsFilling: 'BF-001',
      status: 'Approved',
      items: [
        { namaMaterial: 'Botol Plastik', kodePM: 'PM-001', jumlahPermintaan: 1000 }
      ]
    },
    {
      id: 3,
      type: 'raw-material',
      noReservasi: 'RSV/20250918/0003',
      tanggalPermintaan: '2025-09-18T09:00:00',
      kodeProduk: 'PROD-001',
      noBets: 'MX-001',
      besarBets: 500,
      status: 'Picking',
      items: [
        { kodeBahan: 'RM-001', namaBahan: 'Tepung Terigu', jumlahKebutuhan: 100, jumlahKirim: 100 }
      ]
    },
    {
      id: 4,
      type: 'add',
      noReservasi: 'RSV/20250918/0004',
      tanggalPermintaan: '2025-09-18T16:20:00',
      namaProduk: 'Produk B',
      noBetsFilling: 'BF-002',
      status: 'Draft',
      items: [
        { namaMaterial: 'Label Produk', kodePM: 'PM-002', alasanPenambahan: 'Reject Produksi', jumlahPermintaan: 200 }
      ]
    }
  ]
}

// Computed properties
const getCategoryName = (type) => {
  const category = categories.find(c => c.id === type)
  return category ? category.name : type
}

const getCategoryClass = (type) => {
  const classes = {
    'foh-rs': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    'packaging': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    'raw-material': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
    'add': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300'
  }
  return classes[type] || 'bg-gray-100 text-gray-800'
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

// Methods
const getStatusClass = (status) => {
  const classes = {
    'Draft': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'Submitted': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    'Approved': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    'Rejected': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    'Picking': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    'Done': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
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

const submitRequest = () => {
  const newRequest = {
    id: requests.value.length + 1,
    type: selectedCategory.value,
    noReservasi: formData.value.noReservasi,
    tanggalPermintaan: formData.value.tanggalPermintaan,
    status: 'Submitted',
    items: [...formData.value.items]
  }
  
  // Add type-specific fields
  switch (selectedCategory.value) {
    case 'foh-rs':
      newRequest.departemen = formData.value.departemen
      newRequest.alasanReservasi = formData.value.alasanReservasi
      break
    case 'packaging':
      newRequest.namaProduk = formData.value.namaProduk
      newRequest.noBetsFilling = formData.value.noBetsFilling
      break
    case 'raw-material':
      newRequest.kodeProduk = formData.value.kodeProduk
      newRequest.noBets = formData.value.noBets
      newRequest.besarBets = formData.value.besarBets
      break
    case 'add':
      newRequest.namaProduk = formData.value.namaProduk
      newRequest.noBetsFilling = formData.value.noBetsFilling
      break
  }
  
  requests.value.unshift(newRequest)
  
  alert(`Request berhasil dibuat dan disubmit!\nNo Reservasi: ${newRequest.noReservasi}`)
  closeModal()
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
  initDummyData()
})
</script>