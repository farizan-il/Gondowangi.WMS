<template>
  <AppLayout title="Riwayat Aktivitas">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Daftar QC (Quality Control)</h2>
        <div class="text-sm text-gray-600">
          Total item menunggu QC: {{ itemsToQC.length }}
        </div>
        <button @click="openQRScanner"
          class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h2M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2z" />
          </svg>
          Scan QR Code
        </button>
      </div>

      <!-- Tabel Daftar QC -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Shipment /
                  No PO</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Surat
                  Jalan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item (Kode &
                  Nama)</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Received
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status QC
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="item in itemsToQC" :key="item.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <div>{{ item.shipmentNumber }}</div>
                  <div class="text-xs text-gray-500">{{ item.noPo }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.noSuratJalan }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.supplier }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <div class="font-medium">{{ item.kodeItem }}</div>
                  <div class="text-xs text-gray-500">{{ item.namaMaterial }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.qtyReceived }} {{ item.uom }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getQCStatusClass(item.statusQC)"
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ item.statusQC }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex space-x-2">
                    <button v-if="item.statusQC === 'To QC'" @click="showItemDetail(item)"
                      class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1 rounded text-xs">
                      Periksa QC
                    </button>
                    <button v-if="item.statusQC === 'PASS'" @click="printGRSlip(item)"
                      class="bg-green-100 text-green-700 hover:bg-green-200 px-2 py-1 rounded text-xs">
                      Cetak GR Slip
                    </button>
                    <button v-if="item.statusQC === 'REJECT'" @click="printReturnSlip(item)"
                      class="bg-red-100 text-red-700 hover:bg-red-200 px-2 py-1 rounded text-xs">
                      Cetak Return Slip
                    </button>
                    <button v-if="item.statusQC !== 'To QC'" @click="printQRLabel(item)"
                      class="bg-purple-100 text-purple-700 hover:bg-purple-200 px-2 py-1 rounded text-xs">
                      Cetak Label QR
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Modal Detail Item (Step 1) -->
      <div v-if="showDetailModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]"
        style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
          <div class="p-6">
            <!-- Header Modal -->
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Detail Item QC - {{ selectedItem?.kodeItem }}</h3>
              <button @click="closeDetailModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Detail Informasi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">No Shipment</label>
                  <div class="mt-1 text-sm text-gray-900 font-medium">{{ selectedItem?.shipmentNumber }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">No PO</label>
                  <div class="mt-1 text-sm text-gray-900">{{ selectedItem?.noPo }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">No Surat Jalan</label>
                  <div class="mt-1 text-sm text-gray-900">{{ selectedItem?.noSuratJalan }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Kode Item</label>
                  <div class="mt-1 text-sm text-gray-900 font-medium">{{ selectedItem?.kodeItem }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Nama Material</label>
                  <div class="mt-1 text-sm text-gray-900">{{ selectedItem?.namaMaterial }}</div>
                </div>
              </div>

              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Supplier</label>
                  <div class="mt-1 text-sm text-gray-900">{{ selectedItem?.supplier }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Quantity Received</label>
                  <div class="mt-1 text-sm text-gray-900 font-medium">{{ selectedItem?.qtyReceived }} {{
                    selectedItem?.uom }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">No Kendaraan</label>
                  <div class="mt-1 text-sm text-gray-900">{{ selectedItem?.noKendaraan }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Nama Driver</label>
                  <div class="mt-1 text-sm text-gray-900">{{ selectedItem?.namaDriver }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Status QC</label>
                  <span :class="getQCStatusClass(selectedItem?.statusQC)"
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                    {{ selectedItem?.statusQC }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Footer Modal -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
              <button @click="closeDetailModal"
                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                Tutup
              </button>
              <button @click="openQCModal" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Lanjut QC
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Form QC (Step 2) -->
      <div v-if="showQCModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]"
        style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
          <div class="p-6">
            <!-- Header Modal -->
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Form QC - {{ selectedItem?.kodeItem }}</h3>
              <button @click="closeQCModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Form Header Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No Form Checklist</label>
                <input v-model="qcForm.noFormChecklist" type="text" readonly
                  class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900">
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input v-model="qcForm.date" type="datetime-local"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No PO</label>
                <input v-model="qcForm.noPo" type="text" readonly
                  class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900">
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No Surat Jalan</label>
                <input v-model="qcForm.noSuratJalan" type="text" readonly
                  class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900">
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Item</label>
                <input v-model="qcForm.kodeItem" type="text" readonly
                  class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900">
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Material</label>
                <input v-model="qcForm.namaMaterial" type="text" readonly
                  class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900">
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reference</label>
                <input v-model="qcForm.reference" type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Supplier</label>
                <input v-model="qcForm.supplier" type="text" readonly
                  class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900">
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select v-model="qcForm.kategori"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
                  <option value="">Pilih Kategori</option>
                  <option value="Raw Material">Raw Material</option>
                  <option value="Packaging Material">Packaging Material</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No Kendaraan</label>
                <input v-model="qcForm.noKendaraan" type="text" readonly
                  class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900">
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Driver</label>
                <input v-model="qcForm.namaDriver" type="text" readonly
                  class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900">
              </div>
            </div>

            <!-- Form Quantity Info -->
            <div class="border-t border-gray-200 pt-6 mb-6">
              <h4 class="text-md font-medium text-gray-900 mb-4">Informasi Quantity</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Box Utuh</label>
                  <input v-model="qcForm.jumlahBoxUtuh" type="number" @input="calculateTotal"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Qty Box Utuh</label>
                  <input v-model="qcForm.qtyBoxUtuh" type="number" @input="calculateTotal"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Box Tidak Utuh</label>
                  <input v-model="qcForm.jumlahBoxTidakUtuh" type="number" @input="calculateTotal"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Qty Box Tidak Utuh</label>
                  <input v-model="qcForm.qtyBoxTidakUtuh" type="number" @input="calculateTotal"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Total Incoming</label>
                  <input v-model="qcForm.totalIncoming" type="number" readonly
                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900">
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">UoM</label>
                  <input v-model="qcForm.uom" type="text" readonly
                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900">
                </div>
              </div>
            </div>

            <!-- Hasil QC -->
            <div class="border-t border-gray-200 pt-6">
              <h4 class="text-md font-medium text-gray-900 mb-4">Hasil QC</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Defect Count</label>
                    <input v-model="qcForm.defectCount" type="number"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan QC</label>
                    <textarea v-model="qcForm.catatanQC" rows="4"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900"></textarea>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Foto Bukti</label>
                    <input type="file" multiple accept="image/*"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
                  </div>
                </div>

                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Hasil QC *</label>
                    <div class="space-y-2">
                      <label class="flex items-center">
                        <input v-model="qcForm.hasilQC" type="radio" value="PASS"
                          class="mr-2 text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-900">PASS</span>
                      </label>
                      <label class="flex items-center">
                        <input v-model="qcForm.hasilQC" type="radio" value="REJECT"
                          class="mr-2 text-red-600 focus:ring-red-500">
                        <span class="text-sm text-gray-900">REJECT</span>
                      </label>
                    </div>
                  </div>

                  <div v-if="qcForm.hasilQC" class="mt-4 p-4 rounded-lg"
                    :class="qcForm.hasilQC === 'PASS' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
                    <div class="text-sm" :class="qcForm.hasilQC === 'PASS' ? 'text-green-800' : 'text-red-800'">
                      <strong>{{ qcForm.hasilQC === 'PASS' ? 'Akan digenerate:' : 'Akan digenerate:' }}</strong>
                      <ul class="mt-2 list-disc list-inside">
                        <li v-if="qcForm.hasilQC === 'PASS'">Good Receipt Slip</li>
                        <li v-if="qcForm.hasilQC === 'PASS'">Label Karantina QR (Status: RELEASED)</li>
                        <li v-if="qcForm.hasilQC === 'REJECT'">Return Slip</li>
                        <li v-if="qcForm.hasilQC === 'REJECT'">Label QR (Status: REJECT)</li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer Modal -->
            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
              <button @click="backToDetail" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                Kembali ke Detail
              </button>
              <button @click="submitQC" :disabled="!isQCFormValid"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-400">
                Simpan Hasil QC
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal QR Scanner dengan Camera -->
      <div v-if="showQRScanner"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]"
        style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg mx-4 max-h-[90vh] overflow-y-auto">
          <div class="p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-semibold text-gray-900">Scan QR Code</h3>
              <button @click="closeQRScanner" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <div class="space-y-4">
              <!-- Scanner Container -->
              <div class="relative">
                <!-- Html5-qrcode scanner akan render di sini -->
                <div id="qr-reader" class="w-full rounded-lg overflow-hidden"></div>

                <!-- Status Indicator -->
                <div v-if="scannerStatus" class="absolute top-4 right-4 z-10">
                  <span :class="scannerStatusClass" class="px-3 py-1 rounded-full text-xs font-semibold">
                    {{ scannerStatus }}
                  </span>
                </div>
              </div>

              <!-- Scan Result -->
              <div v-if="scanResult" class="bg-green-50 border border-green-200 rounded-lg p-3">
                <p class="text-sm text-green-800">
                  <strong>✅ QR Code terdeteksi:</strong><br>
                  {{ scanResult }}
                </p>
              </div>

              <!-- Error Message -->
              <div v-if="scanError" class="bg-red-50 border border-red-200 rounded-lg p-3">
                <p class="text-sm text-red-800">
                  <strong>❌ Error:</strong><br>
                  {{ scanError }}
                </p>
              </div>

              <!-- Divider -->
              <div class="relative">
                <div class="absolute inset-0 flex items-center">
                  <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                  <span class="px-2 bg-white text-gray-500">atau</span>
                </div>
              </div>

              <!-- Manual Input -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Input Manual QR Code:
                </label>
                <div class="flex gap-2">
                  <input v-model="manualQRInput" @keyup.enter="processQRCode(manualQRInput)" type="text"
                    placeholder="Paste QR Code di sini..."
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
                  <button @click="processQRCode(manualQRInput)" :disabled="!manualQRInput"
                    class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-md">
                    Process
                  </button>
                </div>
                <p class="mt-2 text-xs text-gray-500">
                  Contoh format: IN/20250918/0001|RM-001|BATCH123|100|2027-01-01
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import axios from 'axios'
import { Html5Qrcode } from 'html5-qrcode'

let qrScanner = null

const props = defineProps({
  itemsToQC: Array
})

// Data reaktif
const showDetailModal = ref(false)
const showQCModal = ref(false)
const showQRScanner = ref(false)
const selectedItem = ref(null)
const manualQRInput = ref('')

// Camera related
const videoElement = ref(null)
const cameraStream = ref(null)
const cameraReady = ref(false)
const cameraError = ref('')
const scanResult = ref('')
const scanInterval = ref(null)

// QR Scanner variables
const html5QrCode = ref(null)
const scannerStatus = ref('')
const scanError = ref('')

const scannerStatusClass = computed(() => {
  if (scannerStatus.value.includes('Aktif')) return 'bg-green-500 text-white'
  if (scannerStatus.value.includes('Error')) return 'bg-red-500 text-white'
  return 'bg-yellow-500 text-white'
})

// Form QC data
const qcForm = ref({
  incoming_item_id: null,
  noFormChecklist: '',
  date: '',
  noPo: '',
  noSuratJalan: '',
  kodeItem: '',
  namaMaterial: '',
  reference: '',
  supplier: '',
  kategori: '',
  jumlahBoxUtuh: '',
  qtyBoxUtuh: '',
  jumlahBoxTidakUtuh: 0,
  qtyBoxTidakUtuh: 0,
  totalIncoming: '',
  uom: '',
  noKendaraan: '',
  namaDriver: '',
  defectCount: 0,
  catatanQC: '',
  hasilQC: '',
  photos: []
})

// Computed
const isQCFormValid = computed(() => {
  return qcForm.value.hasilQC &&
    qcForm.value.kategori &&
    qcForm.value.jumlahBoxUtuh !== '' &&
    qcForm.value.qtyBoxUtuh !== ''
})

// Methods
const getQCStatusClass = (status) => {
  const classes = {
    'To QC': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    'PASS': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    'REJECT': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
  }
  return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
}

const showItemDetail = (item) => {
  selectedItem.value = item
  showDetailModal.value = true
}

const closeDetailModal = () => {
  showDetailModal.value = false
  selectedItem.value = null
}

const openQCModal = () => {
  if (!selectedItem.value) return

  const today = new Date().toISOString().slice(0, 10).replace(/-/g, '')
  const checklistNumber = `QC/${today}/XXXX` // Will be generated by backend

  qcForm.value = {
    incoming_item_id: selectedItem.value.id,
    noFormChecklist: checklistNumber,
    date: new Date().toISOString().slice(0, 16),
    noPo: selectedItem.value.noPo,
    noSuratJalan: selectedItem.value.noSuratJalan,
    kodeItem: selectedItem.value.kodeItem,
    namaMaterial: selectedItem.value.namaMaterial,
    reference: '',
    supplier: selectedItem.value.supplier,
    kategori: selectedItem.value.kategori,
    jumlahBoxUtuh: '',
    qtyBoxUtuh: '',
    jumlahBoxTidakUtuh: 0,
    qtyBoxTidakUtuh: 0,
    totalIncoming: selectedItem.value.qtyReceived,
    uom: selectedItem.value.uom,
    noKendaraan: selectedItem.value.noKendaraan,
    namaDriver: selectedItem.value.namaDriver,
    defectCount: 0,
    catatanQC: '',
    hasilQC: '',
    photos: []
  }

  showDetailModal.value = false
  showQCModal.value = true
}

const closeQCModal = () => {
  showQCModal.value = false
  selectedItem.value = null
  resetQCForm()
}

const backToDetail = () => {
  showQCModal.value = false
  showDetailModal.value = true
}

const resetQCForm = () => {
  qcForm.value = {
    incoming_item_id: null,
    noFormChecklist: '',
    date: '',
    noPo: '',
    noSuratJalan: '',
    kodeItem: '',
    namaMaterial: '',
    reference: '',
    supplier: '',
    kategori: '',
    jumlahBoxUtuh: '',
    qtyBoxUtuh: '',
    jumlahBoxTidakUtuh: 0,
    qtyBoxTidakUtuh: 0,
    totalIncoming: '',
    uom: '',
    noKendaraan: '',
    namaDriver: '',
    defectCount: 0,
    catatanQC: '',
    hasilQC: '',
    photos: []
  }
}

const calculateTotal = () => {
  const boxUtuh = parseFloat(qcForm.value.qtyBoxUtuh) || 0
  const boxTidakUtuh = parseFloat(qcForm.value.qtyBoxTidakUtuh) || 0
  qcForm.value.totalIncoming = boxUtuh + boxTidakUtuh
}

const handlePhotoUpload = (event) => {
  const files = Array.from(event.target.files)
  qcForm.value.photos = files
}

const submitQC = () => {
  if (!isQCFormValid.value) {
    alert('Mohon lengkapi semua field yang diperlukan')
    return
  }

  // Debug: Log data sebelum dikirim
  console.log('Submitting QC with data:', {
    incoming_item_id: qcForm.value.incoming_item_id,
    kategori: qcForm.value.kategori,
    hasil_qc: qcForm.value.hasilQC
  })

  const formData = new FormData()
  formData.append('incoming_item_id', qcForm.value.incoming_item_id)
  formData.append('reference', qcForm.value.reference || '')
  formData.append('kategori', qcForm.value.kategori)
  formData.append('jumlah_box_utuh', qcForm.value.jumlahBoxUtuh)
  formData.append('qty_box_utuh', qcForm.value.qtyBoxUtuh)
  formData.append('jumlah_box_tidak_utuh', qcForm.value.jumlahBoxTidakUtuh || 0)
  formData.append('qty_box_tidak_utuh', qcForm.value.qtyBoxTidakUtuh || 0)
  formData.append('defect_count', qcForm.value.defectCount || 0)
  formData.append('catatan_qc', qcForm.value.catatanQC || '')
  formData.append('hasil_qc', qcForm.value.hasilQC)

  // Append photos jika ada
  if (qcForm.value.photos && qcForm.value.photos.length > 0) {
    qcForm.value.photos.forEach((photo, index) => {
      formData.append(`photos[${index}]`, photo)
    })
  }

  // ✅ PERBAIKAN: Gunakan path langsung
  router.post('/transaction/quality-control', formData, {
    forceFormData: true,
    preserveState: false, // Refresh state setelah submit
    preserveScroll: true,
    onSuccess: (page) => {
      console.log('QC berhasil disimpan!', page)
      closeQCModal()

      // Tampilkan pesan sukses dari backend
      if (page.props.flash && page.props.flash.success) {
        alert(page.props.flash.success)
      } else {
        alert(`QC ${qcForm.value.hasilQC} berhasil disimpan!`)
      }
    },
    onError: (errors) => {
      console.error('Validation errors:', errors)

      // Tampilkan error lebih detail
      let errorMessage = 'Gagal menyimpan QC:\n\n'
      for (const [field, messages] of Object.entries(errors)) {
        if (Array.isArray(messages)) {
          errorMessage += `• ${messages.join(', ')}\n`
        } else {
          errorMessage += `• ${messages}\n`
        }
      }

      alert(errorMessage)
    },
    onFinish: () => {
      console.log('Request finished')
    }
  })
}

// QR Scanner functions
const openQRScanner = async () => {
  console.log('🚀 Opening QR Scanner...')

  showQRScanner.value = true
  manualQRInput.value = ''
  scanResult.value = ''
  scanError.value = ''
  scannerStatus.value = '⏳ Memulai kamera...'

  // Tunggu DOM ready
  await new Promise(resolve => setTimeout(resolve, 300))

  try {
    await startHtml5QrcodeScanner()
  } catch (err) {
    console.error('❌ Failed to start scanner:', err)
    scanError.value = err.message
    scannerStatus.value = '❌ Error'
  }
}

const startHtml5QrcodeScanner = async () => {
  try {
    console.log('📦 Initializing Html5Qrcode...')

    // Initialize scanner
    html5QrCode.value = new Html5Qrcode("qr-reader")

    console.log('✅ Html5Qrcode instance created')

    // Get available cameras
    const devices = await Html5Qrcode.getCameras()
    console.log('📹 Available cameras:', devices)

    if (!devices || devices.length === 0) {
      throw new Error('Tidak ada kamera yang ditemukan')
    }

    // Pilih kamera belakang jika ada (untuk mobile)
    let selectedCamera = devices[0].id
    const backCamera = devices.find(device =>
      device.label.toLowerCase().includes('back') ||
      device.label.toLowerCase().includes('rear') ||
      device.label.toLowerCase().includes('environment')
    )

    if (backCamera) {
      selectedCamera = backCamera.id
      console.log('📸 Using back camera:', backCamera.label)
    }

    // Config untuk scanner
    const config = {
      fps: 10, // Frame per second
      qrbox: { width: 250, height: 250 }, // Scan box size
      aspectRatio: 1.0,
      formatsToSupport: [0] // 0 = QR_CODE
    }

    // Start scanner
    await html5QrCode.value.start(
      selectedCamera,
      config,
      (decodedText, decodedResult) => {
        // SUCCESS CALLBACK - QR Code terdeteksi!
        console.log('🎯 QR Code detected!')
        console.log('📄 Decoded text:', decodedText)
        console.log('📊 Full result:', decodedResult)

        // Hindari multiple processing
        if (isProcessing.value) {
          console.log('⏸️ Already processing...')
          return
        }

        scanResult.value = decodedText
        scannerStatus.value = '✅ QR Terdeteksi!'

        // Process QR Code
        processQRCode(decodedText)
      },
      (errorMessage) => {
        // ERROR CALLBACK - Tidak perlu di-log karena akan spam console
        // Ini normal saat scanner belum menemukan QR code
      }
    )

    console.log('✅ Scanner started successfully!')
    scannerStatus.value = '🟢 Scanner Aktif'
    scanError.value = ''

  } catch (err) {
    console.error('❌ Scanner error:', err)

    let errorMsg = 'Gagal memulai scanner: '

    if (err.name === 'NotAllowedError') {
      errorMsg += 'Akses kamera ditolak. Izinkan akses kamera di browser.'
    } else if (err.name === 'NotFoundError') {
      errorMsg += 'Kamera tidak ditemukan.'
    } else if (err.name === 'NotReadableError') {
      errorMsg += 'Kamera sedang digunakan aplikasi lain.'
    } else {
      errorMsg += err.message
    }

    scanError.value = errorMsg
    scannerStatus.value = '❌ Error'
    throw err
  }
}

// Tambahkan di bagian script
const loadQRScanner = async () => {
  return new Promise((resolve, reject) => {
    // Cek apakah sudah ada
    if (window.QrScanner) {
      console.log('✅ QrScanner already loaded')
      resolve(window.QrScanner)
      return
    }

    console.log('📦 Loading QrScanner library...')

    const script = document.createElement('script')
    script.src = 'https://cdn.jsdelivr.net/npm/qr-scanner@1.4.2/qr-scanner.umd.min.js'

    script.onload = () => {
      console.log('✅ QrScanner library loaded successfully')

      // Tunggu sebentar untuk memastikan library siap
      setTimeout(() => {
        if (window.QrScanner) {
          resolve(window.QrScanner)
        } else {
          reject(new Error('QrScanner library failed to initialize'))
        }
      }, 100)
    }

    script.onerror = () => {
      console.error('❌ Failed to load QrScanner library')
      reject(new Error('Failed to load QR Scanner library from CDN'))
    }

    document.head.appendChild(script)
  })
}

const closeQRScanner = async () => {
  console.log('🛑 Closing QR Scanner...')

  // Stop scanner
  if (html5QrCode.value) {
    try {
      await html5QrCode.value.stop()
      await html5QrCode.value.clear()
      console.log('✅ Scanner stopped')
    } catch (err) {
      console.error('Error stopping scanner:', err)
    }

    html5QrCode.value = null
  }

  showQRScanner.value = false
  manualQRInput.value = ''
  scanResult.value = ''
  scanError.value = ''
  scannerStatus.value = ''
  isProcessing.value = false
}

// Tambahkan sebelum startCamera
const checkCameraPermission = async () => {
  try {
    // Cek permission jika browser support
    if (navigator.permissions && navigator.permissions.query) {
      const permission = await navigator.permissions.query({ name: 'camera' })
      return permission.state // 'granted', 'denied', atau 'prompt'
    }
    return 'prompt' // Default jika tidak support
  } catch (err) {
    console.warn('Cannot check camera permission:', err)
    return 'prompt'
  }
}

const startCamera = async () => {
  try {
    stopCamera()

    cameraError.value = ''
    cameraReady.value = false

    await new Promise(resolve => setTimeout(resolve, 500))

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      throw new Error('Browser Anda tidak mendukung akses kamera')
    }

    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)

    const constraints = isMobile ? {
      video: {
        facingMode: { ideal: 'environment' },
        width: { ideal: 1920, max: 1920 },
        height: { ideal: 1080, max: 1080 }
      }
    } : {
      video: {
        facingMode: 'environment',
        width: { ideal: 1280 },
        height: { ideal: 720 }
      }
    }

    console.log('🎥 Requesting camera access...')

    const stream = await navigator.mediaDevices.getUserMedia(constraints)
    console.log('✅ Camera stream obtained')

    cameraStream.value = stream

    await nextTick()

    if (videoElement.value) {
      videoElement.value.srcObject = stream

      if (isMobile) {
        videoElement.value.setAttribute('playsinline', 'true')
        videoElement.value.setAttribute('webkit-playsinline', 'true')
      }

      videoElement.value.onloadedmetadata = async () => {
        console.log('📹 Video metadata loaded')

        const playPromise = videoElement.value.play()

        if (playPromise !== undefined) {
          playPromise
            .then(async () => {
              cameraReady.value = true
              console.log('✅ Camera started successfully')

              // MULAI QR SCANNING
              await startQRScanning()
            })
            .catch(err => {
              console.error('Error playing video:', err)
              videoElement.value.muted = true
              videoElement.value.play()
                .then(async () => {
                  cameraReady.value = true
                  await startQRScanning()
                })
                .catch(e => {
                  cameraError.value = 'Gagal memutar video kamera'
                })
            })
        }
      }
    }
  } catch (err) {
    console.error('❌ Camera Error:', err)

    if (err.name === 'NotAllowedError') {
      cameraError.value = 'Akses kamera ditolak. Pastikan Anda mengizinkan akses kamera.'
    } else if (err.name === 'NotFoundError') {
      cameraError.value = 'Kamera tidak ditemukan.'
    } else if (err.name === 'NotReadableError') {
      cameraError.value = 'Kamera sedang digunakan aplikasi lain. Tutup aplikasi lain lalu coba lagi.'
    } else {
      cameraError.value = `Gagal mengakses kamera: ${err.message}`
    }
  }
}

const startQRScanningAlternative = () => {
  const canvas = document.createElement('canvas')
  const context = canvas.getContext('2d')

  scanInterval.value = setInterval(() => {
    if (!videoElement.value || videoElement.value.readyState !== 4) return

    canvas.width = videoElement.value.videoWidth
    canvas.height = videoElement.value.videoHeight
    context.drawImage(videoElement.value, 0, 0, canvas.width, canvas.height)

    const imageData = context.getImageData(0, 0, canvas.width, canvas.height)
    const code = jsQR(imageData.data, imageData.width, imageData.height)

    if (code && !isProcessing.value) {
      console.log('🎯 QR detected:', code.data)
      scanResult.value = code.data
      processQRCode(code.data)
    }
  }, 100) // Scan every 100ms
}

const startQRScanning = async () => {
  try {
    console.log('🔍 Starting QR scanning...')

    // Pastikan library sudah loaded
    if (typeof QrScanner === 'undefined') {
      console.error('❌ QrScanner library not loaded!')
      cameraError.value = 'QR Scanner library belum dimuat. Refresh halaman.'
      return
    }

    if (!videoElement.value) {
      console.error('❌ Video element not found')
      return
    }

    // Tunggu video benar-benar ready
    if (videoElement.value.readyState < 2) {
      console.log('⏳ Waiting for video to be ready...')
      await new Promise((resolve) => {
        videoElement.value.addEventListener('loadeddata', resolve, { once: true })
      })
    }

    console.log('📹 Video ready:', videoElement.value.videoWidth, 'x', videoElement.value.videoHeight)

    // Inisialisasi QR Scanner dengan config yang lebih tepat
    qrScanner = new QrScanner(
      videoElement.value,
      result => {
        console.log('🎯 QR Code detected:', result.data)

        // Hindari multiple scan
        if (isProcessing.value) return

        scanResult.value = result.data
        processQRCode(result.data)
      },
      {
        returnDetailedScanResult: true,
        highlightScanRegion: true,
        highlightCodeOutline: true,
        maxScansPerSecond: 2, // Kurangi dari 5 ke 2 untuk stabilitas
        preferredCamera: 'environment',
        calculateScanRegion: (video) => {
          // Buat scan region di tengah (lebih fokus)
          const smallerDimension = Math.min(video.videoWidth, video.videoHeight)
          const scanRegionSize = Math.round(0.5 * smallerDimension) // 50% dari video

          return {
            x: Math.round((video.videoWidth - scanRegionSize) / 2),
            y: Math.round((video.videoHeight - scanRegionSize) / 2),
            width: scanRegionSize,
            height: scanRegionSize,
          }
        }
      }
    )

    console.log('🎬 QR Scanner instance created')

    // Mulai scanning dengan delay sedikit
    await new Promise(resolve => setTimeout(resolve, 500))
    await qrScanner.start()

    console.log('✅ QR Scanner started and active!')

    // Verifikasi scanner aktif
    setTimeout(() => {
      if (qrScanner && qrScanner._active) {
        console.log('✅ Scanner verification: ACTIVE')
      } else {
        console.error('❌ Scanner verification: NOT ACTIVE')
        cameraError.value = 'Scanner gagal start. Coba lagi.'
      }
    }, 1000)

  } catch (err) {
    console.error('❌ QR Scanner error:', err)
    cameraError.value = `Gagal memulai QR scanner: ${err.message}`
  }
}

const stopCamera = () => {
  console.log('🛑 Stopping camera...')

  // Stop QR Scanner
  if (qrScanner) {
    qrScanner.stop()
    qrScanner.destroy()
    qrScanner = null
    console.log('QR Scanner stopped')
  }

  // Stop camera stream
  if (cameraStream.value) {
    const tracks = cameraStream.value.getTracks()
    console.log(`Stopping ${tracks.length} track(s)`)

    tracks.forEach(track => {
      track.stop()
      console.log(`Track stopped: ${track.kind}`)
    })

    cameraStream.value = null
  }

  // Clear video element
  if (videoElement.value) {
    videoElement.value.srcObject = null
    videoElement.value.load()
  }

  cameraReady.value = false
  scanResult.value = ''

  console.log('✅ Camera stopped')
}

let isProcessing = ref(false)

const processQRCode = async (qrData) => {
  if (!qrData) {
    alert('❌ QR Code kosong!')
    return
  }

  if (isProcessing.value) {
    console.log('⏸️ Already processing...')
    return
  }

  isProcessing.value = true
  qrData = qrData.trim()

  console.log('🔍 Processing QR Code:', qrData)

  // Stop scanner sementara
  if (html5QrCode.value) {
    try {
      await html5QrCode.value.pause()
      scannerStatus.value = '⏸️ Processing...'
    } catch (err) {
      console.log('Cannot pause scanner:', err)
    }
  }

  try {
    const response = await axios.post('/transaction/quality-control/scan', {
      qr_code: qrData
    })

    console.log('✅ Server response:', response.data)

    if (response.data.success) {
      scanResult.value = `✅ ${response.data.message}`

      setTimeout(() => {
        closeQRScanner()
        showItemDetail(response.data.data)
      }, 1000)
    }
  } catch (error) {
    console.error('❌ Error processing QR:', error)

    let errorMessage = '❌ Error: '

    if (error.response?.data) {
      errorMessage += error.response.data.message || 'Terjadi kesalahan'
    } else {
      errorMessage += error.message || 'Terjadi kesalahan'
    }

    scanError.value = errorMessage
    alert(errorMessage)

    // Resume scanner
    if (html5QrCode.value) {
      try {
        await html5QrCode.value.resume()
        scannerStatus.value = '🟢 Scanner Aktif'
      } catch (err) {
        console.log('Cannot resume scanner:', err)
      }
    }
  } finally {
    isProcessing.value = false
  }
}

// Action handlers
const printGRSlip = (item) => {
  const printWindow = window.open('', '_blank')

  printWindow.document.write(`
    <html>
      <head>
        <title>Good Receipt Slip - ${item.kodeItem}</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 20px; }
          .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
          .content { margin: 20px 0; }
          .info-row { display: flex; justify-content: space-between; margin: 10px 0; }
          .signature { margin-top: 40px; display: flex; justify-content: space-between; }
          .signature div { text-align: center; }
          .signature-line { border-top: 1px solid #000; width: 150px; margin-top: 40px; }
        </style>
      </head>
      <body>
        <div class="header">
          <h2>GOOD RECEIPT SLIP</h2>
          <p>No: GR/${new Date().toISOString().slice(0, 10).replace(/-/g, '')}/${String(item.id).padStart(4, '0')}</p>
        </div>
        <div class="content">
          <div class="info-row">
            <span><strong>No PO:</strong> ${item.noPo}</span>
            <span><strong>Tanggal:</strong> ${new Date().toLocaleDateString('id-ID')}</span>
          </div>
          <div class="info-row">
            <span><strong>No Surat Jalan:</strong> ${item.noSuratJalan}</span>
            <span><strong>No Kendaraan:</strong> ${item.noKendaraan}</span>
          </div>
          <div class="info-row">
            <span><strong>Supplier:</strong> ${item.supplier}</span>
            <span><strong>Driver:</strong> ${item.namaDriver}</span>
          </div>
          <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr style="background-color: #f0f0f0;">
              <th style="border: 1px solid #000; padding: 8px;">Kode Item</th>
              <th style="border: 1px solid #000; padding: 8px;">Nama Material</th>
              <th style="border: 1px solid #000; padding: 8px;">Quantity</th>
              <th style="border: 1px solid #000; padding: 8px;">UoM</th>
              <th style="border: 1px solid #000; padding: 8px;">Status</th>
            </tr>
            <tr>
              <td style="border: 1px solid #000; padding: 8px;">${item.kodeItem}</td>
              <td style="border: 1px solid #000; padding: 8px;">${item.namaMaterial}</td>
              <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.qtyReceived}</td>
              <td style="border: 1px solid #000; padding: 8px; text-align: center;">${item.uom}</td>
              <td style="border: 1px solid #000; padding: 8px; text-align: center; color: green; font-weight: bold;">PASS</td>
            </tr>
          </table>
        </div>
        <div class="signature">
          <div><p>Received By:</p><div class="signature-line"></div><p>Warehouse Staff</p></div>
          <div><p>QC By:</p><div class="signature-line"></div><p>Quality Control</p></div>
          <div><p>Approved By:</p><div class="signature-line"></div><p>Supervisor</p></div>
        </div>
      </body>
    </html>
  `)

  printWindow.document.close()
  printWindow.focus()
  setTimeout(() => { printWindow.print(); printWindow.close() }, 500)
}

const printReturnSlip = (item) => {
  const printWindow = window.open('', '_blank')

  printWindow.document.write(`
    <html>
      <head>
        <title>Return Slip - ${item.kodeItem}</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 20px; }
          .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; background-color: #ffe6e6; }
          .reject-note { background-color: #ffe6e6; border: 1px solid #ff0000; padding: 10px; margin: 20px 0; }
        </style>
      </head>
      <body>
        <div class="header">
          <h2>RETURN SLIP</h2>
          <p>No: RTN/${new Date().toISOString().slice(0, 10).replace(/-/g, '')}/${String(item.id).padStart(4, '0')}</p>
        </div>
        <div class="reject-note">
          <strong>ALASAN REJECT:</strong><br>
          Material tidak memenuhi standar QC. Barang akan dikembalikan ke supplier.
        </div>
      </body>
    </html>
  `)

  printWindow.document.close()
  printWindow.focus()
  setTimeout(() => { printWindow.print(); printWindow.close() }, 500)
}

const printQRLabel = (item) => {
  const printWindow = window.open('', '_blank')
  const qrContent = `${item.shipmentNumber}|${item.kodeItem}|${item.statusQC}|${item.qtyReceived}|${new Date().toISOString().slice(0, 10)}`

  printWindow.document.write(`
    <html>
      <head>
        <title>QR Label - ${item.kodeItem}</title>
        <style>
          .label { border: 2px solid #000; padding: 15px; width: 300px; text-align: center; ${item.statusQC === 'PASS' ? 'background-color: #e6ffe6;' : 'background-color: #ffe6e6;'} }
          .qr-placeholder { width: 120px; height: 120px; border: 2px solid #000; margin: 10px auto; display: flex; align-items: center; justify-content: center; background-color: white; }
        </style>
      </head>
      <body>
        <div class="label">
          <div class="qr-placeholder">QR CODE<br>${qrContent}</div>
          <div><strong>${item.statusQC === 'PASS' ? 'KARANTINA' : 'REJECT'}</strong></div>
        </div>
      </body>
    </html>
  `)

  printWindow.document.close()
  printWindow.focus()
  setTimeout(() => { printWindow.print(); printWindow.close() }, 500)
}

onUnmounted(() => {
  stopCamera()
})
onUnmounted(async () => {
  if (html5QrCode.value) {
    try {
      await html5QrCode.value.stop()
      await html5QrCode.value.clear()
    } catch (err) {
      console.error('Error cleaning up scanner:', err)
    }
  }
})
</script>