<template>
  <AppLayout title="Riwayat Aktivitas">
    <div class="space-y-6">
      <!-- Header dengan tombol Buat Penerimaan -->
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Daftar Penerimaan</h2>
        <button @click="showModal = true"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Buat Penerimaan
        </button>
      </div>

      <!-- Filter Section -->
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
          <!-- Show Entries -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Show Entries</label>
            <select v-model="limit" class="w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
              <option value="10">Show 10</option>
              <option value="25">Show 25</option>
              <option value="50">Show 50</option>
              <option value="100">Show 100</option>
              <option value="all">Show All</option>
            </select>
          </div>
          
          <!-- Unified Search -->
          <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Pencarian</label>
            <input 
              v-model="search" 
              type="text" 
              placeholder="Cari No PO, No Incoming, No Surat Jalan, Serial Number, atau Nama Driver..."
              class="w-full px-3 py-2 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" 
            />
          </div>
          
          <!-- Date Range Filters -->
          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal</label>
            <div class="grid grid-cols-2 gap-2">
              <input 
                v-model="dateStart" 
                type="date" 
                placeholder="Dari"
                class="w-full px-2 py-2 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
              >
              <input 
                v-model="dateEnd" 
                type="date" 
                placeholder="Sampai"
                class="w-full px-2 py-2 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
              >
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Terima</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Incoming</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No PO</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Diterima</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Surat Jalan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="shipment in shipments.data" :key="shipment.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatDate(shipment.tanggalTerima) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <span class="font-mono text-xs">{{ shipment.incomingNumber }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ shipment.noPo }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">
                  <div v-if="shipment.items && shipment.items.length > 0" class="max-w-xs">
                    <div class="font-mono text-xs text-gray-600 mb-1">[{{ shipment.items[0].kodeItem || '-' }}]</div>
                    <div class="truncate font-medium" :title="shipment.items[0].namaMaterial">
                      {{ shipment.items[0].namaMaterial || '-' }}
                    </div>
                    <span v-if="shipment.items.length > 1" class="text-xs text-gray-500 mt-1 inline-block">
                      (+{{ shipment.items.length - 1 }} lainnya)
                    </span>
                  </div>
                  <span v-else class="text-gray-400">-</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <div v-if="shipment.items && shipment.items.length > 0">
                    <span class="font-semibold">{{ formatNumber(calculateTotalQtyRaw(shipment.items[0])) }}</span>
                    <span class="text-xs text-gray-600 ml-1">{{ shipment.items[0].uom || '-' }}</span>
                  </div>
                  <span v-else class="text-gray-400">-</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ shipment.noSuratJalan }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ shipment.supplier }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <span class="font-mono text-xs">{{ shipment.items && shipment.items.length > 0 ? (shipment.items[0].batchLot || '-') : '-' }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusClass(shipment.status)"
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ shipment.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex space-x-2">
                    <button @click="viewDetail(shipment)"
                      class="bg-indigo-100 text-indigo-700 hover:bg-indigo-200 px-2 py-1 rounded text-xs">Detail</button>
                    <button @click="printChecklist(shipment)"
                      class="bg-green-100 text-green-700 hover:bg-green-200 px-2 py-1 rounded text-xs">Cetak
                      Checklist</button>
                    <button @click="printFinanceSlip(shipment)"
                      class="bg-purple-100 text-purple-700 hover:bg-purple-200 px-2 py-1 rounded text-xs">Cetak
                      Finance</button>
                    <button @click="handlePrintQRLabel(shipment)"
                      class="bg-blue-600 text-white hover:bg-blue-700 px-2 py-1 rounded text-xs">
                      Cetak Label QR
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- Pagnation was here -->
         <!-- Pagination -->
         <!-- Modified Pagination: Only showing totals, no links as per request "jangan ada pagination", but allowing limit selection above -->
        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Menampilkan
                        <span class="font-medium">{{ shipments.from }}</span>
                        sampai
                        <span class="font-medium">{{ shipments.to }}</span>
                        dari
                        <span class="font-medium">{{ shipments.total }}</span>
                        hasil
                    </p>
                </div>
            </div>
        </div>
      </div>


      <div v-if="showDetailModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]"
        style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg max-w-6xl w-full mx-4 max-h-[90vh] overflow-y-auto">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Detail Penerimaan - {{ selectedShipment?.noSuratJalan }}
              </h3>
              <button @click="closeDetailModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-6">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                <div>
                  <span class="font-medium text-gray-700">No Incoming:</span>
                  <span class="ml-2 text-gray-900">{{ selectedShipment?.incomingNumber }}</span>
                </div>
                <div>
                  <span class="font-medium text-gray-700">No PO:</span>
                  <span class="ml-2 text-gray-900">{{ selectedShipment?.noPo }}</span>
                </div>
                <div>
                  <span class="font-medium text-gray-700">Supplier:</span>
                  <span class="ml-2 text-gray-900">{{ selectedShipment?.supplier }}</span>
                </div>
                <div>
                  <span class="font-medium text-gray-700">No Kendaraan:</span>
                  <span class="ml-2 text-gray-900">{{ selectedShipment?.noKendaraan }}</span>
                </div>
                <div>
                  <span class="font-medium text-gray-700">Nama Driver:</span>
                  <span class="ml-2 text-gray-900">{{ selectedShipment?.namaDriver }}</span>
                </div>
                <div>
                  <span class="font-medium text-gray-700">Tanggal Terima:</span>
                  <span class="ml-2 text-gray-900">{{ formatDate(selectedShipment?.tanggalTerima) }}</span>
                </div>
                <div>
                  <span class="font-medium text-gray-700">Kategori:</span>
                  <span class="ml-2 text-gray-900">{{ selectedShipment?.kategori }}</span>
                </div>
                <div>
                  <span class="font-medium text-gray-700">Status:</span>
                  <span :class="getStatusClass(selectedShipment?.status)"
                    class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ selectedShipment?.status }}
                  </span>
                </div>
              </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
              <h4 class="text-lg font-medium text-gray-900 mb-4">Detail Material</h4>

              <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200" style="min-width: 1400px;">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Kode
                        Item</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Nama
                        Material</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                        Batch/Mfg Lot</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Exp
                        Date</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Qty
                        Wadah</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Qty
                        Unit</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                        Kondisi</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">CoA
                      </th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                        Label Mfg</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                        Label & CoA Sesuai</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                        Pabrik Pembuat</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                        Status QC</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="(item, index) in selectedShipment?.items" :key="index">
                      <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ item.kodeItem }}</td>
                      <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ item.namaMaterial }}</td>
                      <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ item.batchLot }}</td>
                      <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                        <div class="font-semibold mb-1">
                          {{ formatDateOnly(item.expDate) }}
                        </div>

                        <span :class="getCountdown(item.expDate).class"
                          class="px-2 py-0.5 text-xs font-medium rounded-full">
                          {{ getCountdown(item.expDate).text }}
                        </span>
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ parseInt(item.qtyWadah) }}</td>
                      <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ formatNumber(item.qtyUnit) }}</td>
                      <td class="px-3 py-2 whitespace-nowrap">
                        <div class="flex gap-1">
                          <span v-if="item.kondisiBaik"
                            class="bg-green-500 text-white px-2 py-1 text-xs rounded">Baik</span>
                          <span v-if="item.kondisiTidakBaik"
                            class="bg-red-500 text-white px-2 py-1 text-xs rounded">Tidak Baik</span>
                        </div>
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap">
                        <div class="flex gap-1">
                          <span v-if="item.coaAda" class="bg-green-500 text-white px-2 py-1 text-xs rounded">Ada</span>
                          <span v-if="item.coaTidakAda"
                            class="bg-red-500 text-white px-2 py-1 text-xs rounded">Tidak</span>
                        </div>
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap">
                        <div class="flex gap-1">
                          <span v-if="item.labelMfgAda"
                            class="bg-green-500 text-white px-2 py-1 text-xs rounded">Ada</span>
                          <span v-if="item.labelMfgTidakAda"
                            class="bg-red-500 text-white px-2 py-1 text-xs rounded">Tidak</span>
                        </div>
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap">
                        <div class="flex gap-1">
                          <span v-if="item.labelCoaSesuai"
                            class="bg-green-500 text-white px-2 py-1 text-xs rounded">Sesuai</span>
                          <span v-if="item.labelCoaTidakSesuai"
                            class="bg-red-500 text-white px-2 py-1 text-xs rounded">Tidak Sesuai</span>
                        </div>
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ item.pabrikPembuat }}</td>
                      <td class="px-3 py-2 whitespace-nowrap">
                        <span
                          :class="item.statusQC === 'To QC' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800'"
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                          {{ item.statusQC }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
              <button @click="closeDetailModal"
                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                Tutup
              </button>
              <button @click="printChecklist(selectedShipment)"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Cetak Checklist
              </button>
              <button @click="printFinanceSlip(selectedShipment)"
                class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                Cetak Finance
              </button>
              <button @click="showQRModal(selectedShipment)"
                class="bg-blue-600 hover:bg-blue-700 px-4 py-2 text-white rounded-md">
                Cetak Label QR
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="showQRCodeModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]"
        style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg max-w-4xl w-full mx-4 max-h-[80vh] overflow-y-auto">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Label QR Code - {{ selectedShipment?.noSuratJalan }}
              </h3>
              <button @click="closeQRModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-6">
              <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                  <span class="font-medium text-gray-700">No PO:</span>
                  <span class="ml-2 text-gray-900">{{ selectedShipment?.noPo }}</span>
                </div>
                <div>
                  <span class="font-medium text-gray-700">Supplier:</span>
                  <span class="ml-2 text-gray-900">{{ selectedShipment?.supplier }}</span>
                </div>
                <div>
                  <span class="font-medium text-gray-700">No Kendaraan:</span>
                  <span class="ml-2 text-gray-900">{{ selectedShipment?.noKendaraan }}</span>
                </div>
                <div>
                  <span class="font-medium text-gray-700">Tanggal:</span>
                  <span class="ml-2 text-gray-900">{{ formatDate(selectedShipment?.tanggalTerima) }}</span>
                </div>
              </div>
            </div>

            <div class="space-y-4">
              <h4 class="text-md font-medium text-gray-900">Daftar QR Code per Item:</h4>
              <div v-if="selectedShipment?.qrCodeLabels && selectedShipment.qrCodeLabels.length > 0" class="space-y-4">
                <div v-for="(labelData, index) in selectedShipment.qrCodeLabels" :key="index"
                  class="border border-gray-200 rounded-lg p-4">
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <div class="font-medium text-gray-900 mb-2">[{{ labelData.kodeItem }}] - {{ labelData.namaMaterial
                        }}</div>
                      <div class="text-sm text-gray-600 space-y-1">
                        <div>Batch: {{ labelData.batchLot }}</div>
                        <div>Wadah Ke: **{{ labelData.wadahKe }}** / {{ labelData.qtyWadah }}</div>
                        <div>Qty: {{ labelData.qtyUnit }}</div>
                        <div>Exp: {{ formatDateOnly(labelData.expDate) }}</div>
                      </div>
                      <div class="bg-gray-100 p-2 rounded font-mono text-xs text-gray-800 mt-3">
                        <strong>QR Content:</strong><br>{{ labelData.qrContent.split('|')[2] }}
                      </div>
                      <div class="flex space-x-2 mt-3">
                        <button @click="printSingleQR(labelData)"
                          class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                          Cetak QR Wadah {{ labelData.wadahKe }}
                        </button>
                      </div>
                    </div>

                    <div class="flex flex-col items-center justify-center">
                      <div class="bg-white p-4 rounded-lg border-2 border-gray-300 mb-2">
                        <canvas :data-qr-canvas="index" class="block"></canvas>
                      </div>
                      <div class="text-xs text-gray-500 text-center">
                        Preview QR Code
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-8 text-gray-500">
                Belum ada item untuk shipment ini
              </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
              <button @click="closeQRModal" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                Tutup
              </button>

              <button @click="printAllQR" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 text-white rounded-md">
                Cetak Semua QR
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Status Selector untuk Cetak Label QR -->
      <div v-if="showStatusSelectorModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]"
        style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg max-w-md w-full mx-4">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Pilih Status Label QR</h3>
              <button @click="closeStatusSelector" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <div class="mb-6">
              <p class="text-sm text-gray-600 mb-4">
                Material ini memiliki multiple status. Pilih status yang ingin dicetak:
              </p>
              <div class="space-y-3">
                <button 
                  v-for="status in availableStatuses" 
                  :key="status"
                  @click="printLabelByStatus(status)"
                  :class="getStatusButtonClass(status)"
                  class="w-full px-4 py-3 rounded-lg font-semibold text-left flex items-center justify-between hover:opacity-80 transition-opacity">
                  <span>Cetak Label {{ status }}</span>
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                  </svg>
                </button>
              </div>
            </div>

            <div class="flex justify-end">
              <button @click="closeStatusSelector" 
                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                Batal
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading Overlay saat Generate QR Label -->
      <div v-if="isGeneratingLabel"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]"
        style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg p-8 max-w-sm mx-4 text-center">
          <div class="mb-4">
            <svg class="animate-spin h-12 w-12 mx-auto text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Generating Labels...</h3>
          <p class="text-sm text-gray-600">Mohon tunggu, sedang membuat label QR</p>
        </div>
      </div>

      <!-- Modal Form Buat Penerimaan -->
      <div v-if="showModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]"
        style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg max-w-6xl w-full mx-4 max-h-[90vh] overflow-y-auto">
          <div class="p-6">
            <!-- Header Modal -->
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Buat Penerimaan Baru</h3>
              <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <div class="border p-4 mb-6 rounded-lg bg-indigo-50 border-indigo-200">
              <h4 class="font-bold text-indigo-700 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd"
                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L10 11.586l2.293-2.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
                  <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v7a1 1 0 11-2 0V3a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Opsi 1: Upload Data Penerimaan dari PDF ERP
              </h4>
              <div class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-grow">
                  <label class="block text-sm font-medium text-gray-700 mb-1">File PDF dari ERP</label>
                  <input @change="handleFileUpload" type="file" accept=".pdf"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white text-gray-900 text-sm" />
                </div>
                <button @click="processErpPdf" :disabled="!newShipment.erpPdfFile || isProcessingErp"
                  class="w-full sm:w-auto px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:bg-indigo-300 flex items-center justify-center transition-colors">
                  <svg v-if="isProcessingErp" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                  </svg>
                  {{ isProcessingErp ? 'Memproses...' : 'Proses File ERP' }}
                </button>
              </div>
              <p v-if="newShipment.isErpDataLoaded" class="text-sm text-green-600 mt-2 font-medium">
                Data dari PDF ERP berhasil dimuat! Mohon cek dan lengkapi rincian di bawah.
              </p>
            </div>

            <!-- Wizard Stepper Section (Multi-Item-Code) -->
            <div v-if="wizardMode && newShipment.isErpDataLoaded" class="mb-6 bg-blue-50 rounded-lg p-4 border-2 border-blue-300">
              <h4 class="text-md font-bold text-blue-900 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                  <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
                Terdeteksi {{ itemCodeGroups.length }} Kode Item Berbeda
              </h4>
              <p class="text-sm text-blue-700 mb-4">
                Sistem akan membuat <strong>{{ itemCodeGroups.length }} penerimaan terpisah</strong> 
                (1 per kode item) untuk memudahkan cetak checklist. Gunakan tombol navigasi untuk melihat dan edit detail setiap kode item.
              </p>
              
              <!-- Step Navigation -->
              <div class="flex items-center justify-between mb-4">
                <button 
                  @click="previousStep" 
                  :disabled="currentStepIndex === 0"
                  class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                  </svg>
                  Sebelumnya
                </button>
                
                <div class="flex gap-2">
                  <div 
                    v-for="(group, index) in itemCodeGroups" 
                    :key="index"
                    :class="[
                      'w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all cursor-pointer',
                      currentStepIndex === index 
                        ? 'bg-blue-600 text-white ring-2 ring-blue-300' 
                        : 'bg-gray-300 text-gray-700 hover:bg-gray-400'
                    ]"
                    @click="goToStep(index)"
                    :title="`Step ${index + 1}: ${group.code}`">
                    {{ index + 1 }}
                  </div>
                </div>
                
                <button 
                  @click="nextStep" 
                  :disabled="currentStepIndex === itemCodeGroups.length - 1"
                  class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                  Selanjutnya
                  <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                  </svg>
                </button>
              </div>
              
              <!-- Current Step Info -->
              <div class="bg-white rounded-lg p-3 border border-blue-300 shadow-sm">
                <p class="text-sm font-semibold text-gray-800">
                  Step {{ currentStepIndex + 1 }} dari {{ itemCodeGroups.length }}: 
                  <span class="text-blue-700 font-mono text-base">{{ currentItemCodeGroup?.code }}</span>
                </p>
                <p class="text-xs text-gray-600 mt-1">
                  {{ currentItemCodeGroup?.items.length }} baris material dengan kode item ini
                </p>
              </div>
            </div>

            <!-- Form Header -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No Incoming (ERP) <span v-if="newShipment.incomingNumber">(Otomatis)</span></label>
                <input v-model="newShipment.incomingNumber" type="text" placeholder="Auto dari sistem/ERP" :readonly="!!newShipment.incomingNumber"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-100 text-gray-900" />
              </div>
              
              <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">No PO *</label>
                  <input v-model="newShipment.noPo" type="text" placeholder="Masukkan No PO" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No Surat Jalan *</label>
                <input v-model="newShipment.noSuratJalan" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Supplier *
                </label>
                <div class="relative">
                  <input v-model="supplierSearchQuery" @input="filterSuppliers" type="text"
                    placeholder="Ketik untuk mencari Supplier..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900" />
                  <div v-if="filteredSuppliers.length > 0 && supplierSearchQuery"
                    class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                    <div v-for="supplier in filteredSuppliers" :key="supplier.id" @click="selectSupplier(supplier)"
                      class="px-3 py-2 cursor-pointer hover:bg-blue-50 text-gray-900">
                      {{ supplier.nama_supplier }}
                    </div>
                  </div>
                </div>
                <p v-if="newShipment.supplierName" class="text-xs text-gray-500 mt-1">
                  **Dipilih:** {{ newShipment.supplierName }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No Kendaraan *</label>
                <input v-model="newShipment.noKendaraan" type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Driver *</label>
                <input v-model="newShipment.namaDriver" type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal & Waktu Terima</label>
                <input v-model="newShipment.tanggalTerima" type="datetime-local"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori <span v-if="newShipment.kategori">(Otomatis)</span></label>
                <input v-model="newShipment.kategori" type="text" readonly placeholder="Auto dari Material"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-900 focus:outline-none">
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sub Kategori <span v-if="newShipment.subCategory">(Otomatis)</span></label>
                <input v-model="newShipment.subCategory" type="text" readonly placeholder="Auto dari Material"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-900 focus:outline-none">
              </div>
            </div>

            <!-- Detail Items -->
            <div class="border-t border-gray-200 pt-6">
              <!-- Badge for Wizard Mode -->
              <div v-if="wizardMode" class="mb-3 inline-block bg-blue-100 px-4 py-2 rounded-full border border-blue-300">
                <span class="text-sm font-semibold text-blue-800">
                  🔧 Editing Kode Item: <span class="font-mono">{{ currentItemCodeGroup?.code }}</span>
                </span>
              </div>
              
              <div class="flex justify-between items-center mb-4">
                <h4 class="text-lg font-medium text-gray-900">Detail Material</h4>
                <button @click="addNewItem"
                  class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                  + Tambah Baris
                </button>
              </div>

              <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200" style="min-width: 1400px;">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                        Halal/Non-Halal
                      </th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                        Bin Karantina Tujuan
                      </th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Kode
                        Item</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Nama
                        Material</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                        Batch/Mfg Lot</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Exp
                        Date</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Qty
                        Wadah</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Qty
                        Unit</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                        Kondisi</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">CoA
                      </th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                        Label Mfg</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                        Label & CoA Sesuai</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                        Pabrik Pembuat</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                        Status QC</th>
                      <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Aksi
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="(item, index) in newShipment.items" :key="index">
                      <td class="px-3 py-2 whitespace-nowrap">
                        <div class="flex gap-1">
                          <button @click="toggleCheckbox(item, 'isHalal')"
                            :class="[item.isHalal ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700', 'px-2 py-1 text-xs rounded']">Halal</button>
                          <button @click="toggleCheckbox(item, 'isNonHalal')"
                            :class="[item.isNonHalal ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700', 'px-2 py-1 text-xs rounded']">Non-Halal</button>
                        </div>
                      </td>

                      <td class="px-3 py-2 whitespace-nowrap">
                        <select v-model="item.binTarget"
                          class="w-32 text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-900">
                          <option value="">Pilih Bin QRT</option>
                          <option v-for="bin in qrtBins" :key="bin.code" :value="bin.code">
                            {{ bin.code }}
                          </option>
                        </select>
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap">
                        <div class="relative">
                          <input v-model="item.skuSearch" @input="filterMaterials(index)"
                            @focus="item.showSuggestions = true"
                            @blur="setTimeout(() => { item.showSuggestions = false }, 200)" type="text"
                            placeholder="Cari Kode/Nama SKU"
                            class="w-32 text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-900"
                            :class="{'border-red-500 ring-1 ring-red-500 bg-red-50': !item.kodeItem && item.skuSearch}" />
                        </div>
                        <div v-if="item.showSuggestions && item.filteredMaterials.length > 0"
                          class="absolute z-20 w-80 mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto"
                          style="z-index: 99999;">
                          <div v-for="material in item.filteredMaterials" :key="material.id"
                            @mousedown.prevent="selectMaterial(index, material)"
                            class="px-3 py-2 cursor-pointer hover:bg-blue-50 text-xs text-gray-900">
                            **[{{ material.code }}]** - {{ material.name }}
                          </div>
                        </div>
                        <p v-if="item.kodeItem" class="text-xs text-gray-500 mt-1">
                          **Dipilih:** {{ item.kodeItemDisplay }}
                        </p>
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap">
                        <input v-model="item.namaMaterial" type="text" readonly
                          class="w-40 text-sm border border-gray-300 rounded px-2 py-1 bg-gray-50 text-gray-900">
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap">
                        <input v-model="item.batchLot" type="text"
                          class="w-28 text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-900">
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap">
                        <input v-model="item.expDate" type="date"
                          class="w-36 text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-900">
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap">
                        <input v-model="item.qtyWadah" type="number"
                          class="w-24 text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-900">
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap">
                        <input v-model="item.qtyUnit" type="number"
                          class="w-24 text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-900">
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap">
                        <div class="flex gap-1">
                          <button @click="toggleCheckbox(item, 'kondisiBaik')"
                            :class="[item.kondisiBaik ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700', 'px-2 py-1 text-xs rounded']">Baik</button>
                          <button @click="toggleCheckbox(item, 'kondisiTidakBaik')"
                            :class="[item.kondisiTidakBaik ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700', 'px-2 py-1 text-xs rounded']">Tidak
                            Baik</button>
                        </div>
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap">
                        <div class="flex gap-1">
                          <button @click="toggleCheckbox(item, 'coaAda')"
                            :class="[item.coaAda ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700', 'px-2 py-1 text-xs rounded']">Ada</button>
                          <button @click="toggleCheckbox(item, 'coaTidakAda')"
                            :class="[item.coaTidakAda ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700', 'px-2 py-1 text-xs rounded']">Tidak</button>
                        </div>
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap">
                        <div class="flex gap-1">
                          <button @click="toggleCheckbox(item, 'labelMfgAda')"
                            :class="[item.labelMfgAda ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700', 'px-2 py-1 text-xs rounded']">Ada</button>
                          <button @click="toggleCheckbox(item, 'labelMfgTidakAda')"
                            :class="[item.labelMfgTidakAda ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700', 'px-2 py-1 text-xs rounded']">Tidak</button>
                        </div>
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap">
                        <div class="flex gap-1">
                          <button @click="toggleCheckbox(item, 'labelCoaSesuai')"
                            :class="[item.labelCoaSesuai ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700', 'px-2 py-1 text-xs rounded']">Ada</button>
                          <button @click="toggleCheckbox(item, 'labelCoaTidakSesuai')"
                            :class="[item.labelCoaTidakSesuai ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700', 'px-2 py-1 text-xs rounded']">Tidak</button>
                        </div>
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap">
                        <input v-model="item.pabrikPembuat" type="text"
                          class="w-32 text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-900"
                          placeholder="Nama pabrik">
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap">
                        <span class="text-sm text-gray-600">{{ item.statusQC }}</span>
                      </td>
                      <td class="px-3 py-2 whitespace-nowrap">
                        <button @click="removeItem(index)"
                          class="bg-red-100 text-red-700 hover:bg-red-200 px-2 py-1 rounded text-xs">Hapus</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div v-if="totalStockByMaterial.length > 0"
              class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
              <h4 class="text-md font-bold text-yellow-800 mb-3">
                <span class="inline-block align-middle mr-2">⚠️</span> Ringkasan Total Stok Masuk (Kalkulasi)
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="(stock, index) in totalStockByMaterial" :key="index"
                  class="bg-white p-3 rounded-md border border-yellow-300">
                  <p class="text-sm text-gray-600 truncate">Material:</p>
                  <p class="font-bold text-gray-900 truncate">{{ stock.materialName }}</p>
                  <p class="text-md font-extrabold text-blue-600 mt-1">
                    {{ stock.totalQty }} {{ stock.satuan }}
                  </p>
                </div>
              </div>
            </div>


            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">

              <!-- Footer Modal -->
              <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <button @click="closeModal" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                  Batal
                </button>
                <button @click="saveShipment" :disabled="!isFormValid || isSaving"
                  class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-400 flex items-center justify-center">
                  <svg v-if="isSaving" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                  </svg>
                  {{ isSaving ? 'Menyimpan...' : 'Simpan' }}
                                  </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal QR Code -->
        <div v-if="showQRCodeModal"
          class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]"
          style="background-color: rgba(43, 51, 63, 0.67);">
          <div class="bg-white rounded-lg max-w-4xl w-full mx-4 max-h-[80vh] overflow-y-auto">
            <div class="p-6">
              <!-- Header Modal QR -->
              <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Label QR Code - {{ selectedShipment?.noSuratJalan }}
                </h3>
                <button @click="closeQRModal" class="text-gray-400 hover:text-gray-600">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <!-- Info Shipment -->
              <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <span class="font-medium text-gray-700">No PO:</span>
                    <span class="ml-2 text-gray-900">{{ selectedShipment?.noPo }}</span>
                  </div>
                  <div>
                    <span class="font-medium text-gray-700">Supplier:</span>
                    <span class="ml-2 text-gray-900">{{ selectedShipment?.supplier }}</span>
                  </div>
                  <div>
                    <span class="font-medium text-gray-700">No Kendaraan:</span>
                    <span class="ml-2 text-gray-900">{{ selectedShipment?.noKendaraan }}</span>
                  </div>
                  <div>
                    <span class="font-medium text-gray-700">Tanggal:</span>
                    <span class="ml-2 text-gray-900">{{ formatDate(selectedShipment?.tanggalTerima) }}</span>
                  </div>
                </div>
              </div>

              <!-- Daftar QR Items -->
              <div class="space-y-4">
                <h4 class="text-md font-medium text-gray-900">Daftar QR Code per Item:</h4>
                <div v-if="selectedShipment?.items && selectedShipment.items.length > 0" class="space-y-4">
                  <div v-for="(item, index) in selectedShipment.items" :key="index"
                    class="border border-gray-200 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <!-- Info Item -->
                      <div>
                        <div class="font-medium text-gray-900 mb-2">{{ item.kodeItem }} - {{ item.namaMaterial }}</div>
                        <div class="text-sm text-gray-600 space-y-1">
                          <div>Serial Lot: {{ item.batchLot }}</div>
                          <div>Qty: {{ Math.floor(item.qtyUnit) }}</div>
                          <div>Exp: {{ formatDateOnly(item.expDate) }}</div>
                        </div>
                        <div class="bg-gray-100 p-2 rounded font-mono text-xs text-gray-800 mt-3">
                          <strong>QR Content:</strong><br>{{ item.qrCode ? item.qrCode.split('|')[2] : '-' }}
                        </div>
                        <div class="flex space-x-2 mt-3">
                          <button @click="downloadQR(item)"
                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                            Download QR
                          </button>
                          <button @click="printSingleQR(item)"
                            class="bg-gray-400  text-white px-3 py-1 rounded text-sm">
                            Cetak QR
                          </button>
                        </div>
                      </div>

                      <!-- Preview QR -->
                      <div class="flex flex-col items-center justify-center">
                        <div class="bg-white p-4 rounded-lg border-2 border-gray-300 mb-2">
                          <canvas :data-qr-canvas="index" class="block"></canvas>
                        </div>
                        <div class="text-xs text-gray-500 text-center">
                          Preview QR Code
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div v-else class="text-center py-8 text-gray-500">
                  Belum ada item untuk shipment ini
                </div>
              </div>

              <!-- Footer Modal QR -->
              <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                <button @click="closeQRModal" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                  Tutup
                </button>

                <button @click="printAllQR" class="bg-gray-400  px-4 py-2 text-white rounded-md">
                  Cetak Semua QR
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal Missing Materials Alert -->
      
      <div v-if="showMissingMaterialsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]"
          style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6 border-l-4 border-yellow-500">
          <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <svg class="w-6 h-6 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            Material Tidak Ditemukan
          </h3>
          <p class="text-gray-600 mb-4">
            Sistem mendeteksi <strong>{{ missingMaterialsList.length }}</strong> kode material dari PDF yang belum terdaftar di Master Data. Mohon tambahkan agar dapat diproses.
          </p>
          
          <div class="space-y-3 max-h-60 overflow-y-auto mb-6 pr-2">
            <div v-for="code in missingMaterialsList" :key="code" class="flex items-center justify-between bg-gray-50 p-3 rounded border border-gray-200">
              <span class="font-mono font-bold text-gray-800">{{ code }}</span>
              <button @click="openAddMaterialModal(code)" class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200 font-medium">
                + Tambah Data
              </button>
            </div>
          </div>
          
          <div class="flex justify-end">
            <button @click="showMissingMaterialsModal = false" class="text-gray-500 hover:text-gray-700 text-sm underline mr-4">
              Abaikan (Data akan kosong)
            </button>
            <button @click="showMissingMaterialsModal = false" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 font-medium">
              Tutup
            </button>
          </div>
        </div>
      </div>

      <!-- Modal Quick Add Material -->
      <div v-if="showAddMaterialModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-[10001] flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Tambah Material Baru</h3>
                    <button @click="closeAddMaterialModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Item</label>
                        <input v-model="newMaterialForm.code" type="text" readonly class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 text-gray-900 font-mono" />
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Material *</label>
                        <input v-model="newMaterialForm.name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" placeholder="Nama lengkap material" />
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">UoM *</label>
                         <select v-model="newMaterialForm.uom" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih UoM</option>
                            <option value="PCS">PCS</option>
                            <option value="KG">KG</option>
                            <option value="LTR">LITER</option>
                            <option value="LITER">LITER</option>
                            <option value="BOX">BOX</option>
                         </select>
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                        <select v-model="newMaterialForm.category" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Kategori</option>
                            <option value="Raw Material">Raw Material</option>
                            <option value="Packaging">Packaging</option>
                            <option value="Finished Goods">Finished Goods</option>
                         </select>
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sub Kategori</label>
                        <input v-model="newMaterialForm.subCategory" type="text" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" placeholder="Opsional" />
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status Halal</label>
                        <select v-model="newMaterialForm.halalStatus" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Status</option>
                            <option value="Halal">Halal</option>
                            <option value="Non Halal">Non Halal</option>
                         </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button @click="closeAddMaterialModal" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</button>
                    <button @click="saveNewMaterial" :disabled="isSavingMaterial" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:bg-blue-400 flex items-center">
                        <svg v-if="isSavingMaterial" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        {{ isSavingMaterial ? 'Menyimpan...' : 'Simpan Material' }}
                    </button>
                </div>
            </div>
        </div>
      </div>

      <!-- Modal Confirmation for Multiple Shipments -->
      <div v-if="showConfirmMultipleModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 backdrop-blur-sm z-[10000] flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-2xl max-w-md w-full p-6 border-t-4 border-blue-600">
          <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Konfirmasi Pembuatan Penerimaan
          </h3>
          <div class="mb-6">
            <p class="text-gray-700 mb-3">
              Sistem akan membuat <strong class="text-blue-700 text-lg">{{ itemCodeGroups.length }} penerimaan terpisah</strong> 
              dengan data sebagai berikut:
            </p>
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
              <div class="text-sm space-y-2">
                <div class="flex justify-between">
                  <span class="text-gray-600">Incoming Number:</span>
                  <span class="font-mono font-bold text-blue-700">{{ newShipment.incomingNumber }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">No PO:</span>
                  <span class="font-semibold">{{ newShipment.noPo }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">No Surat Jalan:</span>
                  <span class="font-semibold">{{ newShipment.noSuratJalan }}</span>
                </div>
              </div>
            </div>
            <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded p-3">
              <p class="text-xs text-yellow-800">
                <strong>⚠️ Catatan:</strong> Setiap penerimaan akan dipisah berdasarkan kode item untuk memudahkan cetak checklist.
              </p>
            </div>
          </div>
          <div class="flex justify-end space-x-3">
            <button 
              @click="showConfirmMultipleModal = false" 
              class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 font-medium">
              Batal
            </button>
            <button 
              @click="confirmAndSaveMultiple" 
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium flex items-center">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              Ya, Lanjutkan
            </button>
          </div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>


<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed, onMounted } from 'vue'
import { router, usePage, Link } from '@inertiajs/vue3'
import QRCode from 'qrcode'
import axios from 'axios'


// Props dari backend
const props = defineProps({
  shipments: Object, // Changed from Array to Object for Pagination
  purchaseOrders: Array,
  suppliers: Array,
  materials: Array,
  filters: Object // Receive filters from backend
})

// Data reaktif untuk Autocomplete
const supplierSearchQuery = ref('')
const filteredSuppliers = ref([])

// Data reaktif untuk Filter Dashboard
const search = ref(props.filters?.search || '')
const dateStart = ref(props.filters?.date_start || '')
const dateEnd = ref(props.filters?.date_end || '')
  // const supplierFilter = ref(props.filters?.supplier_id || '') // Removed
const limit = ref(props.filters?.limit || '10') // Default 10

// Watchers untuk Filter Otomatis
import { watch } from 'vue'
// import { debounce } from 'lodash' // Removed as lodash is not installed

// Custom debounce function jika lodash tidak tersedia
function customDebounce(func, wait) {
  let timeout;
  return function(...args) {
    const context = this;
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(context, args), wait);
  };
}

const updateParams = customDebounce(() => {
  router.get('/transaction/goods-receipt', { // Route disesuaikan dengan web.php
    search: search.value,
    date_start: dateStart.value,
    date_end: dateEnd.value,
    limit: limit.value
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  })
}, 500)

watch([search, dateStart, dateEnd, limit], () => {
  updateParams()
})

// Data reaktif
const isSaving = ref(false)
const showModal = ref(false)
const showQRCodeModal = ref(false)
const showDetailModal = ref(false)
const showStatusSelectorModal = ref(false)
const availableStatuses = ref([])
const selectedShipmentForPrint = ref(null)
const isGeneratingLabel = ref(false)
const selectedShipment = ref(null)

const newShipment = ref({
  noPo: '',
  noSuratJalan: '',
  supplier: '', // ID Supplier
  noKendaraan: '',
  namaDriver: '',
  tanggalTerima: new Date().toISOString().slice(0, 16),
  kategori: '',
  subCategory: '', // Field Baru
  supplierName: '',
  incomingNumber: '',
  erpPdfFile: null,
  isErpDataLoaded: false,
  items: []
})
const isProcessingErp = ref(false)

// State untuk Missing Materials & Quick Add
const showMissingMaterialsModal = ref(false)
const missingMaterialsList = ref([]) // Array of strings (codes)
const showAddMaterialModal = ref(false)
const isSavingMaterial = ref(false)

const newMaterialForm = ref({
  code: '',
  name: '',
  uom: '',
  category: '',
  subCategory: '',
  halalStatus: '',
  qcRequired: false,
  status: 'Active' // Default
})

// State untuk Wizard Mode (Multi-Item-Code)
const wizardMode = ref(false)
const currentStepIndex = ref(0)
const itemCodeGroups = ref([])  // Array of { code: string, items: [] }
const showConfirmMultipleModal = ref(false)

// Computed for current step
const currentItemCodeGroup = computed(() => {
  if (wizardMode.value && itemCodeGroups.value.length > 0) {
    return itemCodeGroups.value[currentStepIndex.value]
  }
  return null
})

// Computed property untuk validasi semua wizard groups
const areAllWizardStepsValid = computed(() => {
  if (!wizardMode.value) return true; // Not in wizard mode, skip this validation
  
  // 1. Cek Header dulu
  const isHeaderValid = newShipment.value.noPo &&
    newShipment.value.noSuratJalan &&
    newShipment.value.supplier &&
    newShipment.value.noKendaraan &&
    newShipment.value.namaDriver &&
    newShipment.value.kategori && 
    newShipment.value.subCategory;

  if (!isHeaderValid) return false;

  // 2. Simpan current step items ke group sebelum validasi
  if (itemCodeGroups.value.length > 0 && currentStepIndex.value >= 0) {
    itemCodeGroups.value[currentStepIndex.value].items = JSON.parse(JSON.stringify(newShipment.value.items));
  }

  // 3. Validasi SEMUA groups
  for (const group of itemCodeGroups.value) {
    if (!group.items || group.items.length === 0) {
      return false; // Group tidak boleh kosong
    }
    
    // Setiap item di group harus valid
    const allItemsValid = group.items.every(item => {
      return item.kodeItem && 
             item.batchLot && 
             item.expDate && 
             item.qtyWadah && 
             item.qtyUnit &&
             item.binTarget; // Bin target juga wajib
    });
    
    if (!allItemsValid) {
      return false; // Ada item yang tidak valid di group ini
    }
  }
  
  return true; // Semua group valid
});

// Computed
const isFormValid = computed(() => {
  // Jika dalam wizard mode, gunakan validasi wizard
  if (wizardMode.value) {
    return areAllWizardStepsValid.value;
  }
  
  // Mode normal (non-wizard): validasi seperti biasa
  // 1. Cek Header
  const isHeaderValid = newShipment.value.noPo &&
    newShipment.value.noSuratJalan &&
    newShipment.value.supplier &&
    newShipment.value.noKendaraan &&
    newShipment.value.namaDriver &&
    newShipment.value.kategori && 
    newShipment.value.subCategory && // Wajib ada (auto/manual)
    newShipment.value.items.length > 0;

  if (!isHeaderValid) return false;

  // 2. Cek Items (termasuk Exp Date)
  const areItemsValid = newShipment.value.items.every(item => {
    return item.kodeItem && 
           item.batchLot && 
           item.expDate && // WAJIB DIISI
           item.qtyWadah && 
           item.qtyUnit; 
  });

  return areItemsValid;
})

const totalStockByMaterial = computed(() => {
  // Objek untuk menyimpan total stok per kode material
  const stockMap = {};

  newShipment.value.items.forEach(item => {
    const materialId = item.kodeItem;
    const qtyWadah = parseInt(item.qtyWadah) || 0;
    const qtyUnit = parseInt(item.qtyUnit) || 0;

    // HANYA hitung jika item sudah dipilih (punya kodeItem)
    if (materialId) {
      // Cari material di props untuk mendapatkan satuan
      const materialDetail = props.materials.find(m => m.id === materialId)

      // Kalkulasi: (Qty Wadah * Qty Unit)
      const rowStock = qtyWadah * qtyUnit;

      if (stockMap[materialId]) {
        // Tambahkan ke total yang sudah ada
        stockMap[materialId].totalQty += rowStock;
      } else {
        // Inisialisasi total baru
        stockMap[materialId] = {
          totalQty: rowStock,
          materialName: item.namaMaterial || item.kodeItemDisplay,
          satuan: materialDetail ? materialDetail.unit : 'Pcs' // Mengambil satuan dari material props
        };
      }
    }
  });

  // Mengubah objek menjadi array untuk tampilan yang lebih mudah di V-for
  return Object.values(stockMap);
});

// Methods
const viewDetail = (shipment) => {
  selectedShipment.value = shipment
  showDetailModal.value = true
}

const closeDetailModal = () => {
  showDetailModal.value = false
  selectedShipment.value = null
}

const closeModal = () => {
  showModal.value = false
  resetForm()
}

const closeQRModal = () => {
  showQRCodeModal.value = false
  selectedShipment.value = null
}

const handleFileUpload = (event) => {
  newShipment.value.erpPdfFile = event.target.files ? event.target.files[0] : null
  newShipment.value.isErpDataLoaded = false;
}

// FUNGSI BARU UNTUK MEMPROSES PDF ERP
const processErpPdf = async () => {
  if (!newShipment.value.erpPdfFile) {
    alert('Mohon pilih file PDF ERP terlebih dahulu.')
    return
  }

  isProcessingErp.value = true
  newShipment.value.isErpDataLoaded = false;

  const formData = new FormData()
  formData.append('erp_pdf', newShipment.value.erpPdfFile)

  try {
    const response = await axios.post('/transaction/goods-receipt/parse-erp-pdf', formData, { 
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    })

    const parsedData = response.data

    // CHECK IF MULTIPLE ITEM CODES DETECTED
    if (parsedData.has_multiple_item_codes) {
      // WIZARD MODE
      wizardMode.value = true
      currentStepIndex.value = 0
      
      // Build item code groups
      itemCodeGroups.value = parsedData.items_grouped_by_code.map(group => {
        const items = group.items.map(itemData => {
          const materialDetail = props.materials.find(m => m.code == itemData.kode_material)
          
          return {
            kodeItem: materialDetail ? materialDetail.id : '',
            kodeItemDisplay: itemData.kode_material,
            namaMaterial: materialDetail ? materialDetail.name : itemData.description,
            batchLot: itemData.serial_number || '',
            expDate: '',
            qtyWadah: '1',
            qtyUnit: itemData.quantity,
            pabrikPembuat: parsedData.supplier_name || '',
            skuSearch: itemData.kode_material,
            filteredMaterials: [],
            showSuggestions: false,
            kondisiBaik: true,
            kondisiTidakBaik: false,
            coaAda: true,
            coaTidakAda: false,
            labelMfgAda: true,
            labelMfgTidakAda: false,
            labelCoaSesuai: true,
            labelCoaTidakSesuai: false,
            statusQC: 'Karantina',
            binTarget: 'QRT-HALAL',
            isHalal: true,
            isNonHalal: false,
          }
        })
        
        return {
          code: group.code,
          items: items
        }
      })
      
      // Set header data (same for all groups)
      newShipment.value.incomingNumber = parsedData.incoming_number || ''
      newShipment.value.noSuratJalan = (parsedData.no_surat_jalan && parsedData.no_surat_jalan !== 'N/A') 
          ? parsedData.no_surat_jalan 
          : 'N/A'
      newShipment.value.noPo = parsedData.no_po || ''
      newShipment.value.noKendaraan = parsedData.truck_number || ''
      newShipment.value.namaDriver = parsedData.driver_name || ''
      
      if (parsedData.tanggal_terima) {
        newShipment.value.tanggalTerima = parsedData.tanggal_terima
      }
      
      // Set supplier
      const foundSupplier = props.suppliers.find(s =>
        s.nama_supplier.toLowerCase().includes(parsedData.supplier_name.toLowerCase()) ||
        s.kode_supplier.toLowerCase() === parsedData.supplier_code.toLowerCase()
      )
      
      if (foundSupplier) {
        newShipment.value.supplier = foundSupplier.id
        newShipment.value.supplierName = foundSupplier.nama_supplier
        supplierSearchQuery.value = foundSupplier.nama_supplier
      }
      
      // Load first group items
      newShipment.value.items = itemCodeGroups.value[0].items
      
      // Auto-fill kategori from first valid material
      const firstItem = newShipment.value.items[0]
      if (firstItem && firstItem.kodeItem) {
        const mat = props.materials.find(m => m.id === firstItem.kodeItem)
        if (mat) {
          newShipment.value.kategori = mat.kategori || ''
          newShipment.value.subCategory = mat.subCategory || ''
        }
      }
      
      newShipment.value.isErpDataLoaded = true
      alert(`Terdeteksi ${itemCodeGroups.value.length} kode item berbeda. Gunakan wizard untuk melihat dan edit detail setiap kode item.`)
      
    } else {
      // NORMAL MODE (single item code atau no items)
      wizardMode.value = false
      itemCodeGroups.value = []
      
      // 1. Reset Items
      newShipment.value.items = []

      // 2. Isi Header
      newShipment.value.incomingNumber = parsedData.incoming_number || ''
      newShipment.value.noSuratJalan = (parsedData.no_surat_jalan && parsedData.no_surat_jalan !== 'N/A') 
          ? parsedData.no_surat_jalan 
          : 'N/A'
      newShipment.value.noPo = parsedData.no_po || ''

      newShipment.value.noKendaraan = parsedData.truck_number || ''
      newShipment.value.namaDriver = parsedData.driver_name || ''
      
      if (parsedData.tanggal_terima) {
           newShipment.value.tanggalTerima = parsedData.tanggal_terima
      }

      // 3. Set Supplier
      const foundSupplier = props.suppliers.find(s =>
        s.nama_supplier.toLowerCase().includes(parsedData.supplier_name.toLowerCase()) ||
        s.kode_supplier.toLowerCase() === parsedData.supplier_code.toLowerCase()
      )

      if (foundSupplier) {
        newShipment.value.supplier = foundSupplier.id
        newShipment.value.supplierName = foundSupplier.nama_supplier
        supplierSearchQuery.value = foundSupplier.nama_supplier
      } else {
        newShipment.value.supplier = ''
        newShipment.value.supplierName = parsedData.supplier_name ? `*${parsedData.supplier_name}` : ''
        supplierSearchQuery.value = parsedData.supplier_name || ''
      }

      // 4. Proses Items (Looping)
      // Backend sekarang mengembalikan array items yang lengkap (meski ada duplikat kode)
      parsedData.items.forEach(itemData => {
        
        // Cari Material di Master Data berdasarkan Kode Item (misal: 30015)
        const materialDetail = props.materials.find(m => m.code == itemData.kode_material)

        let statusQC = 'Karantina'
        if (materialDetail && materialDetail.qcRequired === false) {
          statusQC = 'Karantina'
        }

        // AUTO-FILL Kategori & SubKategori dari Item Pertama yang valid di Master
        if (newShipment.value.items.length === 0 && materialDetail) { // Jika ini item pertama (atau belum ada yg set)
             newShipment.value.kategori = materialDetail.kategori || '';
             newShipment.value.subCategory = materialDetail.subCategory || '';
        }

        newShipment.value.items.push({
          kodeItem: materialDetail ? materialDetail.id : '', 
          kodeItemDisplay: itemData.kode_material, 
          namaMaterial: materialDetail ? materialDetail.name : itemData.description, 
          batchLot: itemData.serial_number || '', 
          expDate: '', 
          qtyWadah: '1',
          qtyUnit: itemData.quantity, 
          pabrikPembuat: newShipment.value.supplierName || '', 
          skuSearch: itemData.kode_material,
          filteredMaterials: [],
          showSuggestions: false,
          kondisiBaik: true,
          kondisiTidakBaik: false,
          coaAda: true,
          coaTidakAda: false,
          labelMfgAda: true,
          labelMfgTidakAda: false,
          labelCoaSesuai: true,
          labelCoaTidakSesuai: false,
          statusQC: statusQC,
          binTarget: 'QRT-HALAL',
          isHalal: true,
          isNonHalal: false,
        })
      })

      // Fallback: Jika looping selesai tapi kategori masih kosong (misal item pertama skip),
      // Coba cari dari items yg masuk
      if (!newShipment.value.kategori && newShipment.value.items.length > 0) {
          const firstValidItem = newShipment.value.items.find(i => i.kodeItem);
          if (firstValidItem) {
               const mat = props.materials.find(m => m.id === firstValidItem.kodeItem);
               if (mat) {
                   newShipment.value.kategori = mat.kategori || '';
                   newShipment.value.subCategory = mat.subCategory || '';
               }
          }
      }

      newShipment.value.isErpDataLoaded = true;
    }
    
    // VALIDASI MATERIAL HILANG (untuk kedua mode: normal dan wizard)
    if (wizardMode.value) {
      // Wizard mode: cek missing materials dari semua groups
      const allMissingMaterials = []
      
      itemCodeGroups.value.forEach(group => {
        const missingInGroup = group.items
          .filter(item => !item.kodeItem && item.skuSearch)
          .map(item => item.skuSearch)
        allMissingMaterials.push(...missingInGroup)
      })
      
      // Hapus duplikat
      const uniqueMissing = [...new Set(allMissingMaterials)]
      
      if (uniqueMissing.length > 0) {
        missingMaterialsList.value = uniqueMissing
        showMissingMaterialsModal.value = true
      } else {
        alert(`Berhasil memuat ${itemCodeGroups.value.length} grup kode item dari PDF.`)
      }
    } else {
      // Normal mode: cek missing materials dari newShipment.items
      const missingMaterials = newShipment.value.items
          .filter(item => !item.kodeItem && item.skuSearch)
          .map(item => item.skuSearch)
          
      // Hapus duplikat
      const uniqueMissing = [...new Set(missingMaterials)]

      if (uniqueMissing.length > 0) {
          missingMaterialsList.value = uniqueMissing
          showMissingMaterialsModal.value = true
      } else {
          alert(`Berhasil memuat ${parsedData.items.length} item dari PDF.`)
      }
    }
    // --------------------------------


  } catch (error) {
    console.error('Error processing ERP PDF:', error)
    if (error.response && error.response.data && error.response.data.error) {
        alert(error.response.data.error);
    } else {
        alert('Gagal memproses file. Pastikan format PDF sesuai.');
    }
  } finally {
    isProcessingErp.value = false
  }
}

// Wizard Navigation Methods
const nextStep = () => {
  if (currentStepIndex.value < itemCodeGroups.value.length - 1) {
    // Save current step's items back to group
    itemCodeGroups.value[currentStepIndex.value].items = JSON.parse(JSON.stringify(newShipment.value.items))
    
    // Move to next step
    currentStepIndex.value++
    
    // Load next group items
    newShipment.value.items = JSON.parse(JSON.stringify(itemCodeGroups.value[currentStepIndex.value].items))
  }
}

const previousStep = () => {
  if (currentStepIndex.value > 0) {
    // Save current step's items
    itemCodeGroups.value[currentStepIndex.value].items = JSON.parse(JSON.stringify(newShipment.value.items))
    
    // Move back
    currentStepIndex.value--
    
    // Load previous group items
    newShipment.value.items = JSON.parse(JSON.stringify(itemCodeGroups.value[currentStepIndex.value].items))
  }
}

const goToStep = (stepIndex) => {
  if (stepIndex >= 0 && stepIndex < itemCodeGroups.value.length && stepIndex !== currentStepIndex.value) {
    // Save current step
    itemCodeGroups.value[currentStepIndex.value].items = JSON.parse(JSON.stringify(newShipment.value.items))
    
    // Jump to selected step
    currentStepIndex.value = stepIndex
    
    // Load selected group
    newShipment.value.items = JSON.parse(JSON.stringify(itemCodeGroups.value[stepIndex].items))
  }
}


// Save Shipment Logic (handles both wizard and normal mode)
const saveShipment = () => {
  if (wizardMode.value) {
    // Wizard mode: show confirmation dialog first
    // Save current step before showing dialog
    itemCodeGroups.value[currentStepIndex.value].items = JSON.parse(JSON.stringify(newShipment.value.items))
    showConfirmMultipleModal.value = true
  } else {
    // Normal mode: submit single shipment
    submitSingleShipment()
  }
}

const confirmAndSaveMultiple = async () => {
  showConfirmMultipleModal.value = false
  
  // Build shipments array from all groups
  const shipmentsToCreate = itemCodeGroups.value.map(group => ({
    noPo: newShipment.value.noPo,
    noSuratJalan: newShipment.value.noSuratJalan,
    supplier: newShipment.value.supplier,
    noKendaraan: newShipment.value.noKendaraan,
    namaDriver: newShipment.value.namaDriver,
    tanggalTerima: newShipment.value.tanggalTerima,
    kategori: newShipment.value.kategori,
    subCategory: newShipment.value.subCategory,
    incomingNumber: newShipment.value.incomingNumber,
    items: group.items
  }))
  
  isSaving.value = true
  try {
    const response = await axios.post('/transaction/goods-receipt/store-multiple', {
      shipments: shipmentsToCreate
    })
    
    if (response.data.success) {
      alert(response.data.message || `Berhasil membuat ${response.data.created_count} penerimaan!`)
      closeModal()
      router.reload()
    }
  } catch (error) {
    console.error(error)
    alert('Gagal menyimpan: ' + (error.response?.data?.error || error.message))
  } finally {
    isSaving.value = false
  }
}

const submitSingleShipment = () => {
  isSaving.value = true
  
  router.post('/transaction/goods-receipt', {
    ...newShipment.value
  }, {
    onSuccess: () => {
      isSaving.value = false
      closeModal()
      router.reload()
    },
    onError: (errors) => {
      isSaving.value = false
      console.error(errors)
      alert('Gagal menyimpan: ' + JSON.stringify(errors))
    }
  })
}


// Methods untuk Quick Add Material
const openAddMaterialModal = (code) => {
  newMaterialForm.value = {
    code: code,
    name: '',
    uom: '',
    category: '',
    subCategory: '',
    halalStatus: '',
    qcRequired: false,
    status: 'Active'
  }
  showAddMaterialModal.value = true
}

const closeAddMaterialModal = () => {
    showAddMaterialModal.value = false
}

const saveNewMaterial = async () => {
    // Validasi Sederhana
    if (!newMaterialForm.value.name || !newMaterialForm.value.uom || !newMaterialForm.value.category) {
        alert('Mohon lengkapi Nama, UoM, dan Kategori.');
        return;
    }

    isSavingMaterial.value = true;
    try {
        const response = await axios.post('/master-data/sku', newMaterialForm.value);
        
        if (response.data.success) {
            alert('Material berhasil ditambahkan!');
            
            // 1. Refresh Data Material (Props)
            // Kita gunakan router.reload untuk refresh props 'materials'
            router.reload({ only: ['materials'], onSuccess: () => {
                // Setelah reload sukses:
                
                // 2. Hapus dari list missing
                const addedCode = newMaterialForm.value.code;
                missingMaterialsList.value = missingMaterialsList.value.filter(c => c !== addedCode);
                
                // 3. Update items di tabel shipment yang tadinya kosong
                // Cari item yang kodenya cocok, lalu update kodeItem (ID) dan Nama dari response
                const returnedMaterial = response.data.data; // Asumsi response bawa data material baru
                
                newShipment.value.items.forEach(item => {
                    if (item.skuSearch === addedCode) {
                        item.kodeItem = returnedMaterial.id;
                        item.namaMaterial = returnedMaterial.nama_material;
                        item.kodeItemDisplay = returnedMaterial.kode_item;
                        // Update default values lain jika perlu
                    }
                });

                // 4. Tutup modal add
                closeAddMaterialModal();
                
                // 5. Jika list missing habis, tutup modal missing
                if (missingMaterialsList.value.length === 0) {
                    showMissingMaterialsModal.value = false;
                    alert('Semua material yang hilang telah ditambahkan.');
                }
            }});
        }
    } catch (error) {
        console.error('Error saving material:', error);
        alert('Gagal menyimpan material: ' + (error.response?.data?.message || error.message));
    } finally {
        isSavingMaterial.value = false;
    }
}

const resetForm = () => {
  newShipment.value = {
    noPo: '',
    noSuratJalan: '',
    supplier: '',
    noKendaraan: '',
    namaDriver: '',
    tanggalTerima: new Date().toISOString().slice(0, 16),
    kategori: '',
    subCategory: '',
    supplierName: '',
    incomingNumber: '', // <<< RESET INCOMING NUMBER
    erpPdfFile: null, // <<< RESET FILE
    isErpDataLoaded: false, // <<< RESET FLAG
    items: []
  }
  supplierSearchQuery.value = '';
}

const addNewItem = () => {
  const items = newShipment.value.items;

  // Cek jika sudah ada baris data sebelumnya
  if (items.length > 0) {
    // Ambil data dari baris terakhir
    const lastItem = items[items.length - 1];

    // Lakukan Deep Copy menggunakan JSON parse/stringify
    // Ini penting agar referensi object terputus (edit baris baru tidak mengubah baris lama)
    const clonedItem = JSON.parse(JSON.stringify(lastItem));

    // Reset Qty agar user bisa langsung input jumlah baru
    // (Hapus 2 baris di bawah ini jika ingin Qty juga ikut tercopy persis)
    clonedItem.qtyWadah = '';
    clonedItem.qtyUnit = '';

    // Masukkan hasil cloning ke array
    newShipment.value.items.push(clonedItem);

  } else {
    // Jika belum ada baris sama sekali (kosong), buat baris default baru
    newShipment.value.items.push({
      kodeItem: '',
      kodeItemDisplay: '',
      namaMaterial: '',
      batchLot: '',
      expDate: '',
      qtyWadah: '',
      qtyUnit: '',
      pabrikPembuat: newShipment.value.supplierName || '', // Default ikut supplier juga
      skuSearch: '',
      filteredMaterials: [],
      showSuggestions: false,
      kondisiBaik: true, // Default Baik
      kondisiTidakBaik: false,
      coaAda: true, // Default Ada
      coaTidakAda: false,
      labelMfgAda: true, // Default Ada
      labelMfgTidakAda: false,
      labelCoaSesuai: true, // Default Sesuai
      labelCoaTidakSesuai: false,
      statusQC: 'Karantina',
      binTarget: 'QRT-HALAL',
      isHalal: true,
      isNonHalal: false,
    })
  }
}

const qrtBins = [
  { code: 'QRT-HALAL', name: 'Karantina Halal' },
  { code: 'QRT-NON HALAL', name: 'Karantina Non Halal' },
  { code: 'QRT-HALAL-AC', name: 'Karantina hALAL AC' },
];

const removeItem = (index) => {
  newShipment.value.items.splice(index, 1)
}

const toggleCheckbox = (item, field) => {
  // Reset all related checkboxes first
  if (field === 'isHalal' || field === 'isNonHalal') {
    item.isHalal = false
    item.isNonHalal = false
  }
  else if (field === 'kondisiBaik' || field === 'kondisiTidakBaik') {
    item.kondisiBaik = false
    item.kondisiTidakBaik = false
  } else if (field === 'coaAda' || field === 'coaTidakAda') {
    item.coaAda = false
    item.coaTidakAda = false
  } else if (field === 'labelMfgAda' || field === 'labelMfgTidakAda') {
    item.labelMfgAda = false
    item.labelMfgTidakAda = false
  } else if (field === 'labelCoaSesuai' || field === 'labelCoaTidakSesuai') {
    item.labelCoaSesuai = false
    item.labelCoaTidakSesuai = false
  }

  // Set the clicked one to true
  item[field] = true
}

const filterSuppliers = () => {
  if (supplierSearchQuery.value.length < 2) {
    filteredSuppliers.value = []
    return
  }

  const query = supplierSearchQuery.value.toLowerCase()

  filteredSuppliers.value = props.suppliers
    .filter(supplier => {
      // 1. Pastikan objek supplier BUKAN null/undefined.
      if (!supplier) return false;

      // 2. Akses properti dengan aman, gunakan fallback string kosong jika properti tidak ada
      const name = supplier.nama_supplier || '';
      const code = supplier.code || '';

      return name.toLowerCase().includes(query) ||
        code.toLowerCase().includes(query);
    })
    .slice(0, 10)
}

// Fungsi untuk memilih Supplier
const selectSupplier = (supplier) => {
  newShipment.value.supplier = supplier.id // Simpan ID
  newShipment.value.supplierName = supplier.nama_supplier // Simpan Nama untuk tampilan
  supplierSearchQuery.value = supplier.nama_supplier
  filteredSuppliers.value = []
}

// Fungsi untuk memfilter Material/SKU
const filterMaterials = (index) => {
  const item = newShipment.value.items[index]
  const query = item.skuSearch.toLowerCase()

  if (query.length < 2) {
    item.filteredMaterials = []
    return
  }

  item.filteredMaterials = props.materials.filter(material =>
    material.code.toLowerCase().includes(query) ||
    material.name.toLowerCase().includes(query)
  ).slice(0, 10) // Batasi maksimal 10 saran
}

// Fungsi untuk memilih Material/SKU
const selectMaterial = (index, material) => {
  const item = newShipment.value.items[index]

  // 1. Update form data
  item.kodeItem = material.id
  item.kodeItemDisplay = material.code
  item.namaMaterial = material.name
  item.pabrikPembuat = material.mfg
  item.statusQC = material.qcRequired ? 'To QC' : 'Direct Putaway'

  // 2. Clear state pencarian
  item.skuSearch = material.code
  item.filteredMaterials = []
  item.showSuggestions = false

  // 3. Auto-populate Halal Status
  if (material.halalStatus === 'Halal') {
    item.isHalal = true
    item.isNonHalal = false
  } else if (material.halalStatus === 'Non Halal') {
    item.isHalal = false
    item.isNonHalal = true
  } else {
    // Default fallback if unknown (optional, keep existing default or reset)
    // item.isHalal = true
    // item.isNonHalal = false
  }

  // AUTO-UPDATE HEADER Kategori & SubKategori jika belum diisi atau user mau ganti?
  // User request: "otomatis terpilih sesuai kategori kode materialnya"
  // Asumsi: Header mengikuti Item yang TERAKHIR DISI (atau yang dipilih user)
  if (material.kategori) newShipment.value.kategori = material.kategori;
  if (material.subCategory) newShipment.value.subCategory = material.subCategory;
}

const updateNamaMaterial = (index) => {
  const materialId = parseInt(newShipment.value.items[index].kodeItem)
  const selectedMaterial = props.materials.find(m => m.id === materialId)

  if (selectedMaterial) {
    newShipment.value.items[index].namaMaterial = selectedMaterial.name
    newShipment.value.items[index].pabrikPembuat = selectedMaterial.mfg
    newShipment.value.items[index].statusQC = selectedMaterial.qcRequired ? 'To QC' : 'Direct Putaway'
    
    // Auto-populate Halal Status
    if (selectedMaterial.halalStatus === 'Halal') {
      newShipment.value.items[index].isHalal = true
      newShipment.value.items[index].isNonHalal = false
    } else if (selectedMaterial.halalStatus === 'Non Halal') {
      newShipment.value.items[index].isHalal = false
    newShipment.value.items[index].isNonHalal = true
    }
  }
}

// Old saveShipment method removed - replaced with wizard-aware version above

const generateQR = (shipmentId, itemId, lot, qty, expDate) => {
  return `${shipmentId}|${itemId}|${lot}|${qty}|${expDate}`
}

const forceInteger = (item, field) => {
  let val = item[field].toString().replace(/[^0-9]/g, '')

  item[field] = parseInt(val) || (val === '0' ? 0 : '')
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleString('id-ID', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatDateOnly = (dateString) => {
  if (!dateString) return '-';
  // Mengambil tanggal (menghilangkan T dan waktu)
  const date = new Date(dateString.split('T')[0]);
  // Format ke DD/MM/YYYY
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  });
}

// Format angka dengan separator titik untuk ribuan (1000 -> 1.000)
const formatNumber = (value) => {
  if (!value && value !== 0) return '-';
  const num = parseInt(value);
  if (isNaN(num)) return '-';
  return num.toLocaleString('id-ID'); // Format Indonesia menggunakan titik sebagai separator ribuan
}

// Calculate total received qty (qty wadah * qty unit)
const calculateTotalQty = (item) => {
  if (!item) return '-';
  const qtyWadah = parseFloat(item.qtyWadah) || 0;
  const qtyUnit = parseFloat(item.qtyUnit) || 0;
  const total = qtyWadah * qtyUnit;
  return formatNumber(total);
}

// Calculate total received qty raw (returns number, not formatted string)
const calculateTotalQtyRaw = (item) => {
  if (!item) return 0;
  const qtyWadah = parseFloat(item.qtyWadah) || 0;
  const qtyUnit = parseFloat(item.qtyUnit) || 0;
  return qtyWadah * qtyUnit;
}

const formatInteger = (value) => {
  if (value === null || value === undefined || value === '') {
    return '';
  }
  return Math.round(parseFloat(value));
}

// Helper function untuk generate QR Code Data URL
const generateQRDataURL = async (qrContent) => {
  try {
    const url = await QRCode.toDataURL(qrContent, {
      width: 150,
      margin: 1,
      errorCorrectionLevel: 'M',
    });
    return url;
  } catch (err) {
    console.error("Gagal generate QR Data URL:", err);
    return "";
  }
};

const LOGO_URL = "https://gondowangi.com/assets/logo/Logo-gondowangi-berwarna.png";

// Generate HTML untuk satu label QR
const generateLabelHTML = (status, item, qrDataURL, labelIndex, totalLabels) => {
  const statusText = status === 'REJECTED' ? 'R E J E C T' : 
                     status === 'RELEASED' ? 'R E L E A S E D' : 
                     'K A R A N T I N A';
  const borderColor = '#171917';
  const textColor = '#000000'; // Hitam untuk semua status (warna dihilangkan)

  const noLot = item.noLot || item.batchLot || 'N/A';
  const expDatePrint = formatDateOnly(item.expDate || 'N/A');
  const tanggalTerimaPrint = formatDateOnly(item.tanggalTerima || 'N/A');
  const itemCodeAndName = `[${item.kodeItem || 'N/A'}] ${item.namaMaterial || 'N/A'}`;
  const createdBy = usePage().props.auth?.user?.name || 'Logistik';

  const qtyUnitPerWadah = item.qtyUnitPerWadah || item.qtyUnit || 0;
  const jmlBarangDisplay = `${formatInteger(qtyUnitPerWadah)} ${item.uom || 'PCS'}`;

  const qtyWadahTotal = item.qtyWadah || 1;
  const wadahDetail = qtyWadahTotal > 0 ? `${qtyUnitPerWadah} x ${qtyWadahTotal} box` : '';
  
  const qrContentHtml = qrDataURL ?
    `<img src="${qrDataURL}" class="qr-code" alt="QR Code">` :
    `<div class="qr-placeholder">NO QR</div>`;

  return `
    <div class="label-wrapper">
      <div class="label-container" style="border-color: ${borderColor};">
        <div class="header" style="border-bottom-color: ${borderColor};">
          <img src="${LOGO_URL}" alt="Logo Gondowangi" class="logo-img">
          <div class="status-box" style="color: ${textColor}; border-top-color: ${borderColor};">${statusText}</div>
        </div>
        
        <div class="content">
          <div class="info-section">
            <div class="info-row">
              <div class="info-label">Nama Barang</div>
              <div class="info-value" style="font-size: 11px;">: ${itemCodeAndName}</div>
            </div>
            <div class="info-row">
              <div class="info-label">Kode Barang</div>
              <div class="info-value">: ${item.kodeItem || 'N/A'}</div>
            </div>
            <div class="info-row">
              <div class="info-label">No Lot</div>
              <div class="info-value">: ${noLot}</div>
            </div>
            <div class="info-row">
              <div class="info-label">Supplier</div>
              <div class="info-value">: ${item.supplier || 'N/A'}</div>
            </div>
            <div class="info-row">
              <div class="info-label">Jmlh Barang</div>
              <div class="info-value">: ${jmlBarangDisplay}</div>
            </div>
            ${wadahDetail ? `<div class="info-row">
              <div class="info-label">Detail Wadah</div>
              <div class="info-value multi-line">: ${wadahDetail}</div>
            </div>` : ''}
            <div class="info-row">
              <div class="info-label">Wadah Ke</div>
              <div class="info-value">: ${labelIndex} / ${qtyWadahTotal}</div>
            </div>
            <div class="info-row">
              <div class="info-label">Tgl Datang</div>
              <div class="info-value">: ${tanggalTerimaPrint}</div>
            </div>
            <div class="info-row">
              <div class="info-label">Exp. Date</div>
              <div class="info-value">: ${expDatePrint}</div>
            </div>
            <div class="info-row">
              <div class="info-label">Dibuat Oleh</div>
              <div class="info-value">: ${createdBy}</div>
            </div>
          </div>
          
          <div class="qr-section">
            ${qrContentHtml}
          </div>
        </div>
        
        <div class="footer" style="border-top-color: ${borderColor};">
          <span class="footer-left">Logistik</span>
          <span class="footer-right">QL1001-01 Rev. 02</span>
        </div>
      </div>
    </div>
  `;
};

// Create complete print document
const createPrintDocument = async (item, status) => {
  // PENTING: Gunakan qtyWadah untuk menentukan jumlah label yang dicetak
  const totalWadah = parseInt(item.qtyWadah) || 1;
  const labelsPerPage = 6;
  let pagesHTML = '';
  
  if (totalWadah <= 0) {
    throw new Error("Jumlah wadah tidak valid untuk dicetak. Qty Wadah harus lebih dari 0.");
  }

  // Optimasi: Generate QR codes dengan batch untuk menghindari browser freeze
  console.log(`Generating ${totalWadah} label(s) based on Qty Wadah...`);
  
  for (let i = 0; i < totalWadah; i += labelsPerPage) {
    let labelsInPageHTML = '';
    const pageLabelsCount = Math.min(labelsPerPage, totalWadah - i);

    for (let j = 0; j < pageLabelsCount; j++) {
      const currentWadahIndex = i + j + 1;
      
      const qrContent = item.qrCode || `${item.batchLot}|${item.kodeItem}|${status}|${item.qtyUnitPerWadah || item.qtyUnit}|${item.expDate}|${currentWadahIndex}/${totalWadah}`;
      const qrDataURL = await generateQRDataURL(qrContent);

      const labelHTML = generateLabelHTML(status, item, qrDataURL, currentWadahIndex, totalWadah);
      labelsInPageHTML += labelHTML;
    }

    pagesHTML += `
      <div class="print-page" style="${i > 0 ? 'page-break-before: always;' : ''}">
        ${labelsInPageHTML}
      </div>
    `;
    
    // Beri jeda kecil setiap halaman untuk mencegah browser freeze
    if (i + labelsPerPage < totalWadah) {
      await new Promise(resolve => setTimeout(resolve, 10));
    }
  }

  console.log('Label generation complete!');
  const borderColor = status === 'REJECTED' ? 'red' : status === 'RELEASED' ? 'green' : '#FFA500';

  return `
    <html>
    <head>
      <title>Cetak Label QR - ${item.kodeItem} (${totalWadah} Wadah)</title>
      <style>
        @page { size: A4; margin: 0.5cm; }
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; background: white; font-size: 10px; }
        .print-page { width: 20cm; height: 28.7cm; margin: 0.5cm auto; box-sizing: border-box; display: flex; flex-wrap: wrap; align-content: flex-start; justify-content: space-between; }
        .label-wrapper { width: 9.9cm; height: calc(33.33% - 0.4cm); padding: 0; box-sizing: border-box; margin-bottom: 0.4cm; margin-right: 0.1cm; margin-left: 0.1cm; }
        .label-container { width: 100%; height: 100%; border: 2px solid ${borderColor}; padding: 5px; box-sizing: border-box; background-color: #fff; display: flex; flex-direction: column; font-size: 10px; }
        .header { display: flex; flex-direction: column; align-items: center; border-bottom: 2px solid ${borderColor}; padding-bottom: 5px; margin-bottom: 5px; }
        .logo-img { width: 130px; height: auto; margin-bottom: 5px; }
        .status-box { margin-top: 5px; border-top: 0.8px solid #000; padding: 2px 5px; font-weight: bold; font-size: 14px; width: 100%; text-align: center; }
        .content { display: flex; flex-grow: 1; padding-top: 5px; gap: 10px; }
        .info-section { flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .info-row { display: flex; margin-bottom: 2px; align-items: baseline; }
        .info-label { width: 80px; min-width: 80px; font-weight: normal; flex-shrink: 0; }
        .info-value { flex: 1; font-weight: bold; overflow: hidden; text-overflow: ellipsis; }
        .info-value.multi-line { white-space: normal; }
        .qr-section { width: 80px; height: 80px; display: flex; flex-direction: column; justify-content: flex-start; align-items: flex-end; flex-shrink: 0; }
        .qr-code { width: 100%; height: 100%; border: 1px solid #ccc; }
        .qr-placeholder { width: 100%; height: 100%; border: 1px dashed #999; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999; font-weight: bold; }
        .footer { display: flex; justify-content: space-between; font-size: 10px; border-top: 2px solid ${borderColor}; padding-top: 25px; margin-top: 5px; }
        .footer-left { font-style: italic; }
        .footer-right { font-weight: bold; }
        @media print {
          .print-page { page-break-after: always; width: initial; height: initial; padding: 1px; margin: 0 auto; }
          .print-page:last-child { page-break-after: avoid; }
          body { -webkit-print-color-adjust: exact; print-color-adjust: exact; display: block; margin: 0; }
          .label-container { border-top: 0.8px solid #000; padding: 2px; }
          .header { border-bottom: 0.8px solid #000; padding: 2px; padding-bottom: 0px; margin-bottom: 0px; }
          .footer { border-top: 0.8px solid #000; padding-top: 30px; margin-top: 3px; }
        }
      </style>
    </head>
    <body>
      ${pagesHTML}
    </body>
    </html>
  `;
}

// Print functions untuk masing-masing status
const printKarantinaQRLabel = async (item) => {
  try {
    isGeneratingLabel.value = true; // Show loading
    const loadingMsg = `Generating ${item.qtyWadah || 1} label(s)...`;
    console.log(loadingMsg);
    
    const htmlContent = await createPrintDocument(item, 'KARANTINA');
    
    isGeneratingLabel.value = false; // Hide loading
    
    const printWindow = window.open('', '_blank');
    if (printWindow) {
      printWindow.document.write(htmlContent);
      printWindow.document.close();
      printWindow.focus();
    } else {
      alert('Gagal membuka jendela cetak. Pastikan pop-up tidak diblokir.');
    }
  } catch (error) {
    isGeneratingLabel.value = false; // Hide loading on error
    console.error("Error saat mencetak label KARANTINA:", error);
    alert(error.message || "Terjadi kesalahan saat mencetak label.");
  }
};

const printReleaseQRLabel = async (item) => {
  try {
    isGeneratingLabel.value = true; // Show loading
    const loadingMsg = `Generating ${item.qtyWadah || 1} label(s)...`;
    console.log(loadingMsg);
    
    const htmlContent = await createPrintDocument(item, 'RELEASED');
    
    isGeneratingLabel.value = false; // Hide loading
    
    const printWindow = window.open('', '_blank');
    if (printWindow) {
      printWindow.document.write(htmlContent);
      printWindow.document.close();
      printWindow.focus();
    } else {
      alert('Gagal membuka jendela cetak. Pastikan pop-up tidak diblokir.');
    }
  } catch (error) {
    isGeneratingLabel.value = false; // Hide loading on error
    console.error("Error saat mencetak label RELEASED:", error);
    alert(error.message || "Terjadi kesalahan saat mencetak label.");
  }
};

const printRejectQRLabel = async (item) => {
  try {
    isGeneratingLabel.value = true; // Show loading
    const loadingMsg = `Generating ${item.qtyWadah || 1} label(s)...`;
    console.log(loadingMsg);
    
    const htmlContent = await createPrintDocument(item, 'REJECTED');
    
    isGeneratingLabel.value = false; // Hide loading
    
    const printWindow = window.open('', '_blank');
    if (printWindow) {
      printWindow.document.write(htmlContent);
      printWindow.document.close();
      printWindow.focus();
    } else {
      alert('Gagal membuka jendela cetak. Pastikan pop-up tidak diblokir.');
    }
  } catch (error) {
    isGeneratingLabel.value = false; // Hide loading on error
    console.error("Error saat mencetak label REJECTED:", error);
    alert(error.message || "Terjadi kesalahan saat mencetak label.");
  }
};

// Handler functions untuk status selector
const handlePrintQRLabel = async (shipment) => {
  try {
    const response = await axios.get(`/transaction/goods-receipt/${shipment.id}/statuses`);
    
    const { available_statuses, shipment_data } = response.data;
    
    // DEBUG: Cek data yang diterima dari backend
    console.log('=== DEBUG PRINT QR ===');
    console.log('Data dari backend:', shipment_data);
    console.log('qtyWadah (jumlah label):', shipment_data.qtyWadah);
    console.log('qtyUnit (qty per wadah):', shipment_data.qtyUnit);
    console.log('======================');
    
    selectedShipmentForPrint.value = {
      ...shipment_data,
      id: shipment.id
    };
    
    if (available_statuses.length === 0) {
      alert('Tidak ada status tersedia untuk material ini');
      return;
    }
    
    // Logic baru: Jika ada RELEASED atau REJECTED, munculkan popup
    // Popup akan menampilkan pilihan antara KARANTINA dengan RELEASED/REJECTED
    const hasReleasedOrRejected = available_statuses.some(status => 
      status === 'RELEASED' || status === 'REJECTED'
    );
    
    if (hasReleasedOrRejected) {
      // Ada RELEASED/REJECTED → tampilkan popup dengan semua opsi termasuk KARANTINA
      // Pastikan KARANTINA selalu ada sebagai opsi
      const allOptions = ['KARANTINA', ...available_statuses.filter(s => s !== 'KARANTINA')];
      availableStatuses.value = allOptions;
      showStatusSelectorModal.value = true;
    } else {
      // Hanya KARANTINA → langsung cetak
      printKarantinaQRLabel(selectedShipmentForPrint.value);
    }
  } catch (error) {
    console.error('Error fetching statuses:', error);
    alert('Gagal mengambil data status: ' + (error.response?.data?.error || error.message));
  }
};

const closeStatusSelector = () => {
  showStatusSelectorModal.value = false;
  availableStatuses.value = [];
  selectedShipmentForPrint.value = null;
};

const printLabelByStatus = (status) => {
  if (!selectedShipmentForPrint.value) {
    alert('Data shipment tidak ditemukan');
    return;
  }
  
  if (status === 'KARANTINA') {
    printKarantinaQRLabel(selectedShipmentForPrint.value);
  } else if (status === 'RELEASED') {
    printReleaseQRLabel(selectedShipmentForPrint.value);
  } else if (status === 'REJECTED') {
    printRejectQRLabel(selectedShipmentForPrint.value);
  }
  
  closeStatusSelector();
};

const getStatusButtonClass = (status) => {
  const classMap = {
    'KARANTINA': 'bg-yellow-100 text-yellow-800 border-2 border-yellow-300',
    'RELEASED': 'bg-green-100 text-green-800 border-2 border-green-300',
    'REJECTED': 'bg-red-100 text-red-800 border-2 border-red-300',
  };
  return classMap[status] || 'bg-gray-100 text-gray-800 border-2 border-gray-300';
};

const getCountdown = (dateString) => {
  if (!dateString) return { text: 'N/A', class: 'text-gray-500' };

  // Hapus bagian waktu/timezone jika ada
  const expDate = new Date(dateString.split('T')[0]);
  const today = new Date();
  today.setHours(0, 0, 0, 0); // Atur waktu hari ini ke tengah malam untuk perbandingan akurat

  // Hitung selisih dalam hari
  const diffTime = expDate.getTime() - today.getTime();
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  if (diffDays < 0) {
    return { text: 'Expired', class: 'bg-red-500 text-white' };
  } else if (diffDays === 0) {
    return { text: 'Hari Ini!', class: 'bg-red-400 text-white' };
  } else if (diffDays <= 30) {
    return { text: `${diffDays} hari lagi`, class: 'bg-orange-400 text-white' };
  } else if (diffDays <= 90) {
    return { text: `${diffDays} hari lagi`, class: 'bg-yellow-400 text-gray-900' };
  } else {
    return { text: `${diffDays} hari lagi`, class: 'text-green-600' };
  }
}

const formatTime = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getStatusClass = (status) => {
  const classes = {
    'Draft': 'bg-gray-100 text-gray-800',
    'Karantina': 'bg-yellow-100 text-yellow-800',
    'Proses': 'bg-orange-100 text-orange-800',
    'Selesai': 'bg-green-100 text-green-800'
  }
  // Ganti 'Completed' lama dengan 'Selesai'
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const printChecklist = (shipment) => {
  // Create print window with checklist form
  const printWindow = window.open('', '_blank')

  // logika generate nomor form checklist
  const date = new Date(shipment.tanggalTerima);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const incomingNumber = shipment.incomingNumber || shipment.id;
  const formChecklistNumber = `GR-${year}${month}-${incomingNumber}`;

  // --- LOGIKA UTAMA: Tentukan Teks Coretan/Normal ---
  const getCheckedOrStriked = (isChecked, text) => {
    // Jika true (dipilih), tampilkan tanda centang (✓)
    if (isChecked) {
      return `<span style="font-weight: bold;">${text} ✓</span>`;
    }
    // Jika false (tidak dipilih), tampilkan teks dengan coretan (strike-through)
    return `<span style="text-decoration: line-through; color: #888;">${text}</span>`;
  };

  let itemsHTML = ''
  let currentWadahStart = 1;
  let totalWadah = 0;
  let grandTotalQty = 0;

  shipment.items.forEach((item, index) => {
    // Pastikan qtyWadah adalah integer yang valid
    const qtyWadah = parseInt(item.qtyWadah || '0');
    const qtyUnit = parseInt(item.qtyUnit || '0');

    // 2. LAKUKAN PENJUMLAHAN KUMULATIF
    totalWadah += qtyWadah;
    grandTotalQty += (qtyWadah * qtyUnit);

    // Nomor wadah awal adalah nilai kumulatif sebelumnya
    const wadahStart = currentWadahStart;

    // Nomor wadah akhir adalah wadah awal + jumlah wadah - 1
    const wadahEnd = wadahStart + qtyWadah - 1;

    // Lanjutkan: Perbarui nilai kumulatif untuk baris berikutnya
    currentWadahStart = wadahEnd + 1;

    // Get UoM
    let uom = 'PCS';
    if (item.kodeItem) {
        // Since kodeItem stored in DB might be ID, we try to find by ID first
        // But props.materials might be accessible here
        const mat = props.materials.find(m => m.id == item.kodeItem || m.code == item.kodeItem);
        if (mat) uom = mat.unit;
    }

    // --- Perhitungan Wadah untuk Tampilan ---
    let wadahDisplay;
    if (qtyWadah === 0) {
      wadahDisplay = 'N/A';
    } else if (wadahStart === wadahEnd) {
      wadahDisplay = wadahStart;
    } else {
      wadahDisplay = `${wadahStart}-${wadahEnd}`;
    }

    // --- HTML Table Row Generation (Tidak berubah) ---
    itemsHTML += `
            <tr>
                <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: middle; height: 40px;">
                    ${wadahDisplay}
                </td>
                <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: middle;">
                    ${item.kondisiBaik ? '✓' : ''}
                </td>
                <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: middle;">
                    ${item.kondisiTidakBaik ? '✓' : ''}
                </td>
                <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: middle;"></td>
                <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: middle;"></td>
                <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: middle;">
                    ${item.labelMfgAda ? '✓' : ''}
                </td>
                <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: middle;">
                    ${item.labelMfgTidakAda ? '✓' : ''}
                </td>
                <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: middle;">${uom}</td>
                <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: middle;">N/A</td>
                <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: middle;">N/A</td>
                <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: middle;">${item.qtyUnit ? parseInt(item.qtyUnit) : ''}</td>
            </tr>
        `
  })

  // Add empty rows to fill the table (total 8-10 rows)
  for (let i = shipment.items.length; i < 10; i++) {
    itemsHTML += `
          <tr>
            <td style="border: 1px solid #000; padding: 8px; height: 40px;">&nbsp;</td>
            <td style="border: 1px solid #000; padding: 8px;">&nbsp;</td>
            <td style="border: 1px solid #000; padding: 8px;">&nbsp;</td>
            <td style="border: 1px solid #000; padding: 8px;">&nbsp;</td>
            <td style="border: 1px solid #000; padding: 8px;">&nbsp;</td>
            <td style="border: 1px solid #000; padding: 8px;">&nbsp;</td>
            <td style="border: 1px solid #000; padding: 8px;">&nbsp;</td>
            <td style="border: 1px solid #000; padding: 8px;">&nbsp;</td>
            <td style="border: 1px solid #000; padding: 8px;">&nbsp;</td>
            <td style="border: 1px solid #000; padding: 8px;">&nbsp;</td>
            <td style="border: 1px solid #000; padding: 8px;">&nbsp;</td>
          </tr>
        `
  }

  // --- PRE-CALCULATE CHECKBOX STATES (Hanya ambil data item pertama) ---
  const firstItem = shipment.items[0] || {};

  const labelMfgAdaHtml = getCheckedOrStriked(firstItem.labelMfgAda, 'Ada');
  const labelMfgTidakAdaHtml = getCheckedOrStriked(firstItem.labelMfgTidakAda, 'Tidak');

  const labelCoaSesuaiHtml = getCheckedOrStriked(firstItem.labelCoaSesuai, 'Ada');
  const labelCoaTidakSesuaiHtml = getCheckedOrStriked(firstItem.labelCoaTidakSesuai, 'Tidak');

  const coaAdaHtml = getCheckedOrStriked(firstItem.coaAda, 'Ada');
  const coaTidakAdaHtml = getCheckedOrStriked(firstItem.coaTidakAda, 'Tidak');

  const isHalalHtml = getCheckedOrStriked(firstItem.isHalal, 'Halal');
  const isNonHalalHtml = getCheckedOrStriked(firstItem.isNonHalal, 'Non-Halal');

  printWindow.document.write(`
        <html>
        <head>
            <title>Form Checklist Penerimaan Material - ${shipment.noSuratJalan}</title>
            <style>
            @page {
                size: A4;
                margin: 1cm;
            }
            
            body { 
                font-family: Arial, sans-serif; 
                margin: 0;
                padding: 0;
                font-size: 10px;
                line-height: 1.2;
            }
            
            .main-container {
                border: 2px solid #000;
                padding: 0;
                width: 100%;
                box-sizing: border-box;
            }
            
            .header { 
                text-align: center; 
                padding: 10px;
                border-bottom: 1px solid #000;
                position: relative;
                height: 80px;
                box-sizing: border-box;
            }
            
            .logo-section {
                position: absolute;
                left: 10px;
                top: 10px;
                width: 100px;
                height: 60px;
            }
            
            .logo-box {
                border: 1px solid #000;
                padding: 5px;
                height: 100%;
                box-sizing: border-box;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
            
            .logo-icon {
                width: 30px;
                height: 20px;
                background: linear-gradient(45deg, #4CAF50, #2E7D32);
                margin-bottom: 3px;
                position: relative;
            }
            
            .logo-icon::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 15px;
                height: 15px;
                background: #FFF;
                clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
            }
            
            .company-name {
                font-weight: bold;
                font-size: 8px;
                color: #2E7D32;
            }
            
            .form-number-section {
                position: absolute;
                right: 10px;
                top: 15px;
                width: 120px;
                text-align: center;
            }
            
            .form-number-box {
                background: #000;
                color: white;
                padding: 3px;
                font-size: 9px;
                font-weight: bold;
            }
            
            .header-title {
                margin-top: 10px;
                font-weight: bold;
                font-size: 11px;
            }
            
            .form-fields {
                border-collapse: collapse;
                width: 100%;
            }
            
            .form-fields tr {
                border-bottom: 1px solid #000;
            }
            
            .form-fields tr:last-child {
                border-bottom: none;
            }
            
            .form-fields td {
                border-right: 1px solid #000;
                padding: 6px 8px;
                vertical-align: middle;
                height: 25px;
            }
            
            .form-fields td:last-child {
                border-right: none;
            }
            
            .field-label {
                background-color: #f5f5f5;
                font-weight: bold;
                width: 120px;
            }
            
            .field-value {
                width: auto;
            }
            
            .checkbox-inline {
                display: inline-block;
                margin-right: 15px;
            }

            /* CSS agar tanda centang (✓) terlihat tebal dan coretan terlihat tipis */
            .checkbox-inline span {
                font-size: 10px; /* Base font size */
            }
            .checkbox-inline span[style*="line-through"] {
                color: #888;
                font-weight: normal;
            }
            
            .data-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 0;
            }
            
            .data-table th,
            .data-table td {
                border: 1px solid #000;
                padding: 5px;
                text-align: center;
                vertical-align: middle;
            }
            
            .data-table th {
                background-color: #f0f0f0;
                font-weight: bold;
                font-size: 9px;
                height: 30px;
            }
            
            .main-header {
                height: 50px;
            }
            
            .sub-header {
                height: 35px;
                font-size: 8px;
                line-height: 1.1;
            }
            
            .kemasan-header {
                width: 200px;
            }
            
            .label-header {
                width: 100px;
            }
            
            .unit-header {
                width: 50px;
            }
            
            .jumlah-headers {
                width: 60px;
            }
            
            .wadah-col {
                width: 60px;
            }
            
            .kemasan-cols {
                width: 100px;
            }
            
            .label-cols {
                width: 50px;
            }
            
            .signature-section {
                border-top: 1px solid #000;
                display: flex;
                height: 120px;
            }
            
            .signature-left,
            .signature-right {
                flex: 1;
                padding: 15px;
                text-align: center;
                position: relative;
            }
            
            .signature-left {
                border-right: 1px solid #000;
            }
            
            .signature-title {
                font-weight: bold;
                margin-bottom: 50px;
            }
            
            .signature-fields {
                position: absolute;
                bottom: 15px;
                left: 15px;
                text-align: left;
                font-size: 9px;
            }
            
            .footer-note {
                padding: 10px;
                font-size: 8px;
                font-style: italic;
                border-top: 1px solid #000;
            }
            
            @media print {
                body {
                    margin: 0;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
                .no-print { display: none; }
            }
            </style>
        </head>
        <body>
            <div class="main-container">
                <div class="header">
                    <div class="logo-section">
                        <div class="logo-box">
                            <div class="logo-icon"></div>
                            <div class="company-name">GONDOWANGI</div>
                        </div>
                    </div>
                    
                    <div class="form-number-section">
                        ${formChecklistNumber}
                    </div>
                    
                    <div class="header-title">E-FORM CHECKLIST PENERIMAAN MATERIAL DARI VENDOR</div>
                </div>
                
                <table class="form-fields">
                    <tr>
                        <td class="field-label">Kode Item</td>
                        <td class="field-value">${firstItem.kodeItem || ''}</td>
                    </tr>
                    <tr>
                        <td class="field-label">Nama Material</td>
                        <td class="field-value">${firstItem.namaMaterial || ''}</td>
                    </tr>
                    <tr>
                        <td class="field-label">Label dari Mfg*</td>
                        <td class="field-value">
                            <span class="checkbox-inline">${labelMfgAdaHtml}</span>
                            <span class="checkbox-inline">${labelMfgTidakAdaHtml}</span>
                            <span style="margin-left: 100px;">Label & CoA sesuai :</span>
                            <span class="checkbox-inline">${labelCoaSesuaiHtml}</span>
                            <span class="checkbox-inline">${labelCoaTidakSesuaiHtml}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="field-label">Pabrik Pembuat (mfg)*</td>
                        <td class="field-value">${firstItem.pabrikPembuat || ''}</td>
                    </tr>
                    <tr>
                        <td class="field-label">Produksi di negara*</td>
                        <td class="field-value">Indonesia</td>
                    </tr>
                    <tr>
                        <td class="field-label">Supplier</td>
                        <td class="field-value">${shipment.supplier}</td>
                    </tr>
                    <tr>
                        <td class="field-label">No. PO</td>
                        <td class="field-value">${shipment.noPo}</td>
                    </tr>
                    <tr>
                        <td class="field-label">No. Surat Jalan</td>
                        <td class="field-value">${shipment.noSuratJalan}</td>
                    </tr>
                    <tr>
                        <td class="field-label">Mfg. Batch</td>
                        <td class="field-value">${firstItem.batchLot || ''}</td>
                    </tr>
                    <tr>
                        <td class="field-label">ED</td>
                        <td class="field-value">${firstItem.expDate ? formatDateOnly(firstItem.expDate) : ''}</td>
                    </tr>
                    <tr>
                        <td class="field-label">CoA</td>
                        <td class="field-value">
                            <span class="checkbox-inline">${coaAdaHtml}</span>
                            <span class="checkbox-inline">${coaTidakAdaHtml}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="field-label">Kategori Halal</td> <td class="field-value">
                            <span class="checkbox-inline">${isHalalHtml}</span>
                            <span class="checkbox-inline">${isNonHalalHtml}</span>
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="field-label">Jumlah Wadah</td>
                        <td class="field-value">${totalWadah}</td>
                    </tr>
                    <tr>
                        <td class="field-label">Total Quantity</td>
                        <td class="field-value">${grandTotalQty}</td>
                    </tr>
                    <tr>
                        <td class="field-label">Tgl. Diterima</td>
                        <td class="field-value">
                            ${formatDateOnly(shipment.tanggalTerima)} &nbsp;&nbsp;&nbsp; -
                            ${formatTime(shipment.tanggalTerima)} WIB &nbsp;&nbsp;&nbsp;
                            
                        </td>
                    </tr>
                    <tr>
                        <td class="field-label">Kategori</td>
                        <td class="field-value">${shipment.kategori || ''}</td>
                    </tr>
                    <tr>
                        <td class="field-label">Nama Driver</td>
                        <td class="field-value">${shipment.namaDriver}</td>
                    </tr>
                    <tr>
                        <td class="field-label">No Kendaraan</td>
                        <td class="field-value">${shipment.noKendaraan}</td>
                    </tr>
                </table>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th rowspan="2" class="main-header wadah-col">Wadah Ke-</th>
                            <th colspan="4" class="main-header kemasan-header">Kemasan</th>
                            <th colspan="2" class="main-header label-header">Label</th>
                            <th colspan="4" class="main-header">Jumlah</th>
                        </tr>
                        <tr>
                            <th class="sub-header kemasan-cols">BAIK</th>
                            <th class="sub-header kemasan-cols">TIDAK BAIK</th>
                            <th class="sub-header kemasan-cols">Sobek</th>
                            <th class="sub-header kemasan-cols">Penyok yang mempengaruhi material</th>
                            <th class="sub-header label-cols">Ada</th>
                            <th class="sub-header label-cols">Tidak</th>
                            <th class="sub-header unit-header">Unit</th>
                            <th class="sub-header jumlah-headers">Bruto</th>
                            <th class="sub-header jumlah-headers">Tara</th>
                            <th class="sub-header jumlah-headers">Netto</th>
                        </tr>
                        <tr>
                            <th style="height: 25px; font-size: 8px;"></th>
                            <th style="height: 25px; font-size: 7px; line-height: 1;">
                                Bersih, utuh, tidak<br>sobek/penyok, dll
                            </th>
                            <th style="height: 25px; font-size: 7px; line-height: 1;">
                                Kotor (potensi kotor<br>sampai kedalam)
                            </th>
                            <th style="height: 25px; font-size: 8px;"></th>
                            <th style="height: 25px; font-size: 8px;"></th>
                            <th style="height: 25px; font-size: 8px;"></th>
                            <th style="height: 25px; font-size: 8px;"></th>
                            <th style="height: 25px; font-size: 8px;">Jumlah</th>
                            <th style="height: 25px; font-size: 8px;"></th>
                            <th style="height: 25px; font-size: 8px;"></th>
                            <th style="height: 25px; font-size: 8px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsHTML}
                    </tbody>
                </table>
                
                <div class="signature-section">
                    <div class="signature-left">
                        <div class="signature-title">Dilaporkan oleh</div>
                        <div class="signature-fields">
                            <div>Nama &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</div>
                            <div>Tanggal &nbsp;&nbsp;:</div>
                        </div>
                    </div>
                    <div class="signature-right">
                        <div class="signature-title">Diperiksa Oleh</div>
                        <div class="signature-fields">
                            <div>Nama &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</div>
                            <div>Tanggal &nbsp;&nbsp;:</div>
                        </div>
                    </div>
                </div>
                
                <div class="footer-note">
                    *khusus untuk bahan baku
                </div>
            </div>
            
            <div class="no-print" style="margin-top: 20px; text-align: center;">
                <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Print</button>
                <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">Close</button>
            </div>
        </body>
        </html>
    `)

  printWindow.document.close()
  printWindow.focus()
}

const printFinanceSlip = (shipment) => {
  // Create print window with finance slip
  const printWindow = window.open('', '_blank')

  let itemsHTML = ''
  shipment.items.forEach((item) => {
    itemsHTML += `
      <tr>
        <td style="border: 1px solid #000; padding: 8px;">${item.kodeItem}</td>
        <td style="border: 1px solid #000; padding: 8px;">${item.namaMaterial}</td>
        <td style="border: 1px solid #000; padding: 8px; text-align: right;">${item.qtyUnit}</td>
        <td style="border: 1px solid #000; padding: 8px; text-align: center;">Pcs</td>
      </tr>
    `
  })

  printWindow.document.write(`
    <html>
      <head>
        <title>Good Receipt Slip - ${shipment.noSuratJalan}</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; }
          .letterhead { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 15px; }
          .company-info { margin-bottom: 10px; }
          .addresses { display: flex; justify-content: space-between; margin: 20px 0; }
          .address-box { border: 1px solid #000; padding: 15px; width: 45%; }
          .shipment-info { margin: 20px 0; }
          .shipment-table { width: 100%; border-collapse: collapse; margin: 10px 0; }
          .shipment-table th, .shipment-table td { border: 1px solid #000; padding: 5px; }
          .shipment-table th { background-color: #f0f0f0; font-weight: bold; text-align: center; }
          .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
          .items-table th, .items-table td { border: 1px solid #000; padding: 8px; }
          .items-table th { background-color: #f0f0f0; font-weight: bold; text-align: center; }
          .signature-section { display: flex; justify-content: space-between; margin-top: 40px; }
          .signature-box { text-align: center; width: 200px; }
          @media print {
            body { margin: 0; }
            .no-print { display: none; }
          }
        </style>
      </head>
      <body>
        <div class="letterhead">
          <div class="company-info">
            <strong>PT. Gondowangi Tradisional Kosmetika</strong><br>
            JL. JABABEKA BLOK U NO. 29-C<br>
            RT/RW:03/03 KARANG BARU<br>
            BEKASI<br>
            Indonesia<br>
            Phone: (021) 8910 7915 - 17 | Fax: (021) 8910 7919 | Website: www.gondowangi.com<br>
            Contact : Reza Rizky - Page: 1
          </div>
        </div>
        
        <div class="addresses">
          <div class="address-box">
            <strong>Supplier Address :</strong><br>
            ${shipment.supplier}<br>
            Jl. Satria Raya II No. 32 RT 05 RW 09 Margahayu<br>
            Utara-Babakan Ciparay<br>
            Bandung<br>
            Indonesia
          </div>
          <div class="address-box">
            <strong>Contact Address :</strong><br>
            +62.22.5417871
          </div>
        </div>
        
        <div class="shipment-info">
          <strong>Incoming Shipment : ${shipment.incomingNumber}</strong>
        </div>
        
        <table class="shipment-table">
          <tr>
            <th>No SJ</th>
            <th>Order(Origin)</th>
            <th>Date</th>
            <th>Input by</th>
            <th>No Truck</th>
            <th>Driver Name</th>
          </tr>
          <tr>
            <td>${shipment.noSuratJalan}</td>
            <td>${shipment.noPo}</td>
            <td>${formatDate(shipment.tanggalTerima)}</td>
            <td>Dita A.P</td>
            <td>${shipment.noKendaraan}</td>
            <td>${shipment.namaDriver}</td>
          </tr>
        </table>
        
        <table class="items-table">
          <thead>
            <tr>
              <th>Code</th>
              <th>Description</th>
              <th>Quantity</th>
              <th>UoM</th>
            </tr>
          </thead>
          <tbody>
            ${itemsHTML}
          </tbody>
        </table>
        
        <div class="signature-section">
          <div class="signature-box">
            <strong>Approved By,</strong><br><br><br><br>
            <div style="border-top: 1px solid #000; padding-top: 5px;">
              Reza Rizky<br>
              DD/MM/YYYY
            </div>
          </div>
          <div class="signature-box">
            <strong>Received By,</strong><br><br><br><br>
            <div style="border-top: 1px solid #000; padding-top: 5px;">
              Permadi Rohiman<br>
              DD/MM/YYYY
            </div>
          </div>
        </div>
        
        <div class="no-print" style="margin-top: 20px; text-align: center;">
          <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Print</button>
          <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">Close</button>
        </div>
      </body>
    </html>
  `)

  printWindow.document.close()
  printWindow.focus()
}

const collectAllQRData = (shipment) => {
  // Inisialisasi array untuk menampung semua data label QR per wadah
  const allLabels = [];

  // Pastikan shipment dan items ada
  if (!shipment || !shipment.items) return allLabels;

  shipment.items.forEach((item) => {
    // Ambil Qty Wadah (pastikan integer)
    const qtyWadah = parseInt(item.qtyWadah || 0);

    // Jika qtyWadah valid, ulangi sebanyak jumlah wadah
    for (let i = 1; i <= qtyWadah; i++) {
      // Konten QR Code unik untuk setiap wadah ke-i
      // Format: ShipmentID|ItemID|BatchLot|WadahKe|ExpDate (contoh saja)
      const uniqueQRContent = `${shipment.incomingNumber}|${item.kodeItem}|${item.batchLot}|${i}|${item.expDate}`;

      allLabels.push({
        // Data untuk tampilan label
        qrContent: uniqueQRContent,
        kodeItem: item.kodeItemDisplay || item.kodeItem,
        namaMaterial: item.namaMaterial,
        batchLot: item.batchLot,
        qtyUnit: item.qtyUnit,
        qtyWadah: qtyWadah,
        wadahKe: i, // Nomor wadah ke-i
        expDate: item.expDate,
        supplier: shipment.supplier,
        tanggalTerima: shipment.tanggalTerima,
      });
    }
  });

  // Simpan data label yang sudah dikelompokkan ke dalam selectedShipment
  shipment.qrCodeLabels = allLabels;
  return allLabels;
};

const showQRModal = (shipment) => {
  // Calculating total wadah
  const totalWadah = shipment.items.reduce((sum, item) => sum + parseFloat(item.qtyWadah || 0), 0);
  
  if (totalWadah > 20) {
      if (!confirm(`Peringatan: Jumlah label QR melebihi 20 (${totalWadah} label). Membuka preview mungkin akan menyebabkan website melambat. Lanjutkan?`)) {
          return;
      }
  }

  selectedShipment.value = shipment;

  // Kumpulkan data QR untuk setiap wadah dan simpan di selectedShipment
  collectAllQRData(selectedShipment.value);

  showQRCodeModal.value = true;

  // Generate QR codes setelah modal ditampilkan
  setTimeout(() => {
    generateQRCodes();
  }, 100);
};

const generateQRCodes = () => {
  // Sekarang kita iterasi melalui qrCodeLabels, bukan items!
  if (!selectedShipment.value?.qrCodeLabels) return;

  if (typeof QRCode === 'undefined') {
    console.error('QRCode library not loaded');
    return;
  }

  selectedShipment.value.qrCodeLabels.forEach((labelData, index) => {
    // Selector kanvas harus diubah untuk mengakomodasi banyak label
    const canvas = document.querySelector(`canvas[data-qr-canvas="${index}"]`);
    if (canvas) {
      const ctx = canvas.getContext('2d');
      ctx.clearRect(0, 0, canvas.width, canvas.height);

      try {
        // Gunakan qrContent dari data label
        QRCode.toCanvas(canvas, labelData.qrContent, {
          width: 180,
          height: 180,
          margin: 1,
          errorCorrectionLevel: 'M',
          color: {
            dark: '#000000',
            light: '#FFFFFF'
          }
        }, function (error) {
          if (error) console.error('QR Code generation error:', error);
        });
      } catch (error) {
        console.error('Failed to generate QR code:', error);
      }
    }
  });
};

const downloadQR = (item) => {
  const index = selectedShipment.value.items.findIndex(i => i === item)
  const canvas = document.querySelector(`canvas[data-qr-canvas="${index}"]`)

  if (canvas) {
    // Create download link
    const link = document.createElement('a')
    link.download = `QR_${item.kodeItem}_${item.batchLot}.png`
    link.href = canvas.toDataURL('image/png')
    link.click()
  }
}

const printSingleQR = (labelData) => {
  // Cari index labelData yang diklik di array qrCodeLabels untuk mendapatkan canvas
  const index = selectedShipment.value.qrCodeLabels.findIndex(i => i.qrContent === labelData.qrContent)
  const canvas = document.querySelector(`canvas[data-qr-canvas="${index}"]`)

  // Get current user from Inertia page props
  const currentUser = usePage().props.auth?.user?.name || 'Admin'

  if (canvas) {
    const printWindow = window.open('', '_blank')
    const qrDataURL = canvas.toDataURL('image/png')

    // Format Tanggal
    const formattedTglDatang = formatDateOnly(selectedShipment.value.tanggalTerima);
    const formattedExpDate = formatDateOnly(labelData.expDate);


    const qtyBoxDescription = `${Math.floor(labelData.qtyUnit)} Pcs`;
    const qtyBoxDetail = labelData.qtyWadah > 0 ? `${Math.floor(labelData.qtyUnit / labelData.qtyWadah)} x ${Math.floor(labelData.qtyWadah)} box` : '';


    printWindow.document.write(`
            <html>
            <head>
                <title>Label Karantina - ${labelData.kodeItem} - Wadah ${labelData.wadahKe}</title>
                <style>
                    body {
                        font-family: 'Arial', sans-serif;
                        margin: 0;
                        padding: 0;
                        display: flex;
                        justify-content: center;
                        align-items: flex-start;
                        min-height: 100vh;
                        background-color: #f0f0f0;
                    }
                    .label-container {
                        width: 10cm; /* Lebar label yang lebih besar agar mirip contoh */
                        height: 7cm; /* Tinggi label */
                        border: 2px solid #000;
                        padding: 5px;
                        box-sizing: border-box;
                        background-color: #fff;
                        display: flex;
                        flex-direction: column;
                        font-size: 10px; 
                        margin-top: 20px; 
                    }
                    .header {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        border-bottom: 2px solid #000;
                        padding-bottom: 5px;
                        margin-bottom: 5px;
                    }
                    .logo-img {
                        width: 90px; /* Ukuran logo lebih besar */
                        height: auto;
                        margin-bottom: 5px;
                    }
                    .company-name {
                        font-weight: bold;
                        font-size: 16px; /* Font lebih besar */
                        color: #2d5f3f; 
                        margin-top: 3px;
                    }
                    .status-box {
                        margin-top: 5px;
                        border: 1px solid #000;
                        padding: 2px 5px;
                        font-weight: bold;
                        font-size: 14px; /* Font lebih besar */
                        background-color: #000;
                        color: #fff;
                        width: 100%;
                        text-align: center;
                    }
                    .content {
                        display: flex;
                        flex-grow: 1;
                        padding-top: 5px;
                        gap: 10px; /* Jarak antara info dan QR */
                    }
                    .info-section {
                        flex: 1;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                    }
                    .info-row {
                        display: flex;
                        margin-bottom: 2px;
                        align-items: baseline; /* Agar teks sejajar di baris pertama */
                    }
                    .info-label {
                        width: 80px; /* Sesuaikan lebar label teks */
                        min-width: 80px;
                        font-weight: normal;
                        flex-shrink: 0;
                    }
                    .info-value {
                        flex: 1;
                        font-weight: bold;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                    .info-value.multi-line {
                        white-space: normal; /* Izinkan wrap untuk detail box */
                    }

                    .qr-section {
                        width: 80px; /* Ukuran QR Code */
                        height: 80px; 
                        display: flex;
                        flex-direction: column;
                        justify-content: flex-start;
                        align-items: flex-end; 
                        flex-shrink: 0; /* Pastikan QR tidak menyusut */
                    }
                    .qr-code {
                        width: 100%;
                        height: 100%;
                        border: 1px solid #ccc;
                    }
                    .footer {
                        display: flex;
                        justify-content: space-between;
                        font-size: 10px; /* Font footer */
                        border-top: 2px solid #000;
                        padding-top: 5px;
                        margin-top: 5px;
                    }
                    .footer-left {
                        font-style: italic;
                    }
                    .footer-right {
                        font-weight: bold;
                    }

                    /* Untuk mode print */
                    @media print {
                        body {
                            background-color: #fff;
                            display: block; 
                            margin: 0;
                            padding: 0;
                            -webkit-print-color-adjust: exact;
                            print-color-adjust: exact;
                        }
                        .label-container {
                            margin: 0;
                            border: 1px solid #000; 
                        }
                        .no-print {
                            display: none;
                        }
                        @page {
                            size: 10cm 7cm; /* Custom size jika ini 1 label per halaman */
                            margin: 0; /* Hapus margin halaman jika ini 1 label per halaman */
                        }
                    }
                </style>
            </head>
            <body>
                <div class="label-container">
                    <div class="header">
                        <img src="https://gondowangi.com/assets/logo/Logo-gondowangi-berwarna.png" alt="Logo Gondowangi" class="logo-img">
                        
                        <div class="status-box">KARANTINA</div>
                    </div>
                    
                    <div class="content">
                        <div class="info-section">
                            <div class="info-row">
                                <div class="info-label">Nama Barang</div>
                                <div class="info-value">: [${labelData.kodeItem}] ${labelData.namaMaterial}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Kode Barang</div>
                                <div class="info-value">: ${labelData.kodeItem}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Serial Lot</div>
                                <div class="info-value">: ${labelData.batchLot}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Supplier</div>
                                <div class="info-value">: ${labelData.supplier}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Jmlh Barang</div>
                                <div class="info-value">: ${qtyBoxDescription}</div>
                            </div>
                            ${qtyBoxDetail ? `<div class="info-row">
                                <div class="info-label"></div>
                                <div class="info-value multi-line">: ${qtyBoxDetail}</div>
                            </div>` : ''}
                            <div class="info-row">
                                <div class="info-label">Tgl Datang</div>
                                <div class="info-value">: ${formattedTglDatang}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Exp. Date</div>
                                <div class="info-value">: ${formattedExpDate}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Dibuat Oleh</div>
                                <div class="info-value">: ${currentUser}</div>
                            </div>
                        </div>
                        
                        <div class="qr-section">
                            <img src="${qrDataURL}" class="qr-code" alt="QR Code">
                        </div>
                    </div>
                    
                    <div class="footer">
                        <span class="footer-left">Logistik</span>
                        <span class="footer-right">QL1001-01 Rev. 02</span>
                    </div>
                </div>
                
                <div class="no-print" style="margin-top: 20px; text-align: center;">
                    <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">Print</button>
                    <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px; font-size: 14px;">Close</button>
                </div>
            </body>
            </html>
        `);

    printWindow.document.close();
    printWindow.focus();

    setTimeout(() => {
      printWindow.print();
    }, 500);
  }
}

const printAllQR = () => {
  const allLabels = selectedShipment.value?.qrCodeLabels;
  if (!allLabels || allLabels.length === 0) {
    alert('Tidak ada label QR untuk dicetak.');
    return;
  }

  const printWindow = window.open('', '_blank');
  const currentUser = usePage().props.auth?.user?.name || 'Admin';

  // Ditetapkan menjadi 6 label per halaman (2 kolom x 3 baris)
  const LABELS_PER_PAGE = 6;

  const LOGO_URL = "https://gondowangi.com/assets/logo/Logo-gondowangi-berwarna.png";
  let pagesHTML = '';

  for (let i = 0; i < allLabels.length; i += LABELS_PER_PAGE) {
    const pageLabels = allLabels.slice(i, i + LABELS_PER_PAGE);
    let labelsInPageHTML = '';

    pageLabels.forEach((labelData, labelIndex) => {
      const globalIndex = i + labelIndex;
      
      // Ambil canvas berdasarkan index global
      const canvas = document.querySelector(`canvas[data-qr-canvas="${globalIndex}"]`);
      let qrDataURL = '';

      // --- PERBAIKAN DI SINI ---
      // Sebelumnya ada pengecekan: const isFirstWadah = labelData.wadahKe === 1;
      // Pengecekan itu dihapus agar SEMUA wadah mendapatkan gambar QR-nya masing-masing.
      
      if (canvas) {
        // Ambil data gambar dari canvas yang sudah digenerate di modal
        qrDataURL = canvas.toDataURL('image/png');
      }

      // Jika qrDataURL ada, tampilkan gambar. Jika tidak (error generate), tampilkan placeholder.
      const qrContentHtml = qrDataURL ?
        `<img src="${qrDataURL}" class="qr-code" alt="QR Code">` :
        `<div class="qr-placeholder">ERROR</div>`;

      // -------------------------

      const formattedTglDatang = formatDateOnly(selectedShipment.value.tanggalTerima);
      const formattedExpDate = formatDateOnly(labelData.expDate);

      const qtyBoxDescription = `${Math.floor(labelData.qtyUnit)} Pcs`;
      const qtyBoxDetail = labelData.qtyWadah > 0 ? `${Math.floor(labelData.qtyUnit / labelData.qtyWadah)} x ${Math.floor(labelData.qtyWadah)} box` : '';

      labelsInPageHTML += `
          <div class="label-wrapper">
              <div class="label-container">
                  <div class="header">
                      <img src="${LOGO_URL}" alt="Logo Gondowangi" class="logo-img">
                      <div class="status-box">KARANTINA</div>
                  </div>
                  
                  <div class="content">
                      <div class="info-section">
                          <div class="info-row">
                              <div class="info-label">Nama Barang</div>
                              <div class="info-value">: [${labelData.kodeItem}] ${labelData.namaMaterial}</div>
                          </div>
                          <div class="info-row">
                              <div class="info-label">Kode Barang</div>
                              <div class="info-value">: ${labelData.kodeItem}</div>
                          </div>
                          <div class="info-row">
                              <div class="info-label">Serial Lot</div>
                              <div class="info-value">: ${labelData.batchLot}</div>
                          </div>
                          <div class="info-row">
                              <div class="info-label">Supplier</div>
                              <div class="info-value">: ${labelData.supplier}</div>
                          </div>
                          <div class="info-row">
                              <div class="info-label">Jmlh Barang</div>
                              <div class="info-value">: ${qtyBoxDescription}</div>
                          </div>
                          ${qtyBoxDetail ? `<div class="info-row">
                              <div class="info-label">Wadah Detail</div>
                              <div class="info-value multi-line">: ${qtyBoxDetail}</div>
                          </div>` : ''}
                          <div class="info-row">
                              <div class="info-label">Wadah Ke</div>
                              <div class="info-value">: ${Math.floor(labelData.wadahKe)} / ${Math.floor(labelData.qtyWadah)}</div>
                          </div>
                          <div class="info-row">
                              <div class="info-label">Tgl Datang</div>
                              <div class="info-value">: ${formattedTglDatang}</div>
                          </div>
                          <div class="info-row">
                              <div class="info-label">Exp. Date</div>
                              <div class="info-value">: ${formattedExpDate}</div>
                          </div>
                          <div class="info-row">
                              <div class="info-label">Dibuat Oleh</div>
                              <div class="info-value">: ${currentUser}</div>
                          </div>
                      </div>
                      
                      <div class="qr-section">
                          ${qrContentHtml}
                      </div>
                  </div>
                  
                  <div class="footer">
                      <span class="footer-left">Logistik</span>
                      <span class="footer-right">Rev. 02</span>
                  </div>
              </div>
          </div>
      `;
    });

    pagesHTML += `
            <div class="print-page" style="${i > 0 ? 'page-break-before: always;' : ''}">
                ${labelsInPageHTML}
            </div>
        `;
  }

  printWindow.document.write(`
        <html>
        <head>
            <title>Cetak ${allLabels.length} Label QR - ${selectedShipment.value.noSuratJalan}</title>
            <style>
                /* Pengaturan Halaman A4 untuk cetak 6 label (2x3) */
                @page {
                    size: A4;
                    margin: 0.5cm; /* Margin halaman disetel */
                }
                
                body {
                    font-family: 'Arial', sans-serif;
                    margin: 0;
                    padding: 0;
                    background: white;
                }
                
                .print-page {
                    width: 20cm; /* Lebar A4 efektif */
                    height: 28.7cm; /* Tinggi A4 efektif */
                    margin: 0.5cm auto; 
                    box-sizing: border-box;
                    display: flex;
                    flex-wrap: wrap;
                    align-content: flex-start;
                    justify-content: space-between; 
                }

                /* Container untuk setiap label. 
                   2 kolom (49%) dan 3 baris (33.33% - margin) */
                .label-wrapper {
                    width: 9.9cm; /* Hampir 10cm untuk 2 kolom */
                    height: calc(33.33% - 0.4cm); /* 3 baris di tinggi A4 dikurangi margin */
                    padding: 0; 
                    box-sizing: border-box;
                    margin-bottom: 0.4cm; /* Jarak antar baris */
                    margin-right: 0.1cm;
                    margin-left: 0.1cm;
                }
                
                /* ===========================================
                   GAYA LABEL
                   =========================================== */

                .label-container {
                    width: 100%; 
                    height: 100%; 
                    border: 2px solid #000;
                    padding: 5px; 
                    box-sizing: border-box;
                    background-color: #fff;
                    display: flex;
                    flex-direction: column;
                    font-size: 10px; 
                }
                .header {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    border-bottom: 2px solid #000;
                    padding-bottom: 5px; 
                    margin-bottom: 5px; 
                }
                .logo-img {
                    width: 130px; 
                    height: auto;
                    margin-bottom: 5px;
                }
                .status-box {
                    margin-top: 5px;
                    border-top: 0.8px solid #000; 
                    border-bottom: none;
                    border-left: none;
                    border-right: none;
                    padding: 2px 5px;
                    font-weight: bold;
                    font-size: 14px; 
                    padding: 2px;
                    
                    color: #000; 
                    width: 100%;
                    text-align: center;
                }
                .content {
                    display: flex;
                    flex-grow: 1;
                    padding-top: 5px;
                    gap: 10px; 
                }
                .info-section {
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                }
                .info-row {
                    display: flex;
                    margin-bottom: 2px;
                    align-items: baseline;
                }
                .info-label {
                    width: 80px; 
                    min-width: 80px;
                    font-weight: normal;
                    flex-shrink: 0;
                }
                .info-value {
                    flex: 1;
                    font-weight: bold;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
                .info-value.multi-line {
                    white-space: normal; 
                }

                .qr-section {
                    width: 80px; 
                    height: 80px; 
                    display: flex;
                    flex-direction: column;
                    justify-content: flex-start;
                    align-items: flex-end; 
                    flex-shrink: 0;
                }
                .qr-code {
                    width: 100%;
                    height: 100%;
                    border: 1px solid #ccc;
                }
                .qr-placeholder { 
                    width: 100%;
                    height: 100%;
                    border: 1px dashed #999;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 10px;
                    color: #999;
                    font-weight: bold;
                }
                
                .footer {
                    display: flex;
                    justify-content: space-between;
                    font-size: 10px; 
                    border-top: 2px solid #000;
                    padding-top: 25px;
                    margin-top: 5px;
                }
                .footer-left {
                    font-style: italic;
                }
                .footer-right {
                    font-weight: bold;
                }
                
                /* Untuk mode print */
                @media print {
                    .print-page { 
                        page-break-after: always;
                        width: initial;
                        height: initial;
                        padding: 1px;
                    }
                    .print-page:last-child { page-break-after: avoid; }
                    body { 
                        -webkit-print-color-adjust: exact; 
                        print-color-adjust: exact;
                        display: block;
                        margin: 0;
                    }
                    /* Mengganti border tebal di print */
                    .label-container {
                        border-top: 0.8px solid #000; 
                        padding: 2px;
                    }
                        
                    .header {
                        border-bottom: 0.8px solid #000; 
                        padding: 2px;
                        padding-bottom: 0px; 
                        margin-bottom: 0px; 
                    }
                    .footer {
                        border-top: 0.8px solid #000; 
                        padding-top: 30px;
                        margin-top: 3px;
                    }
                }
            </style>
        </head>
        <body>
            ${pagesHTML}
        </body>
        </html>
    `);

  printWindow.document.close();
  printWindow.focus();

  setTimeout(() => {
    printWindow.print();
  }, 500);
}

// Lifecycle
onMounted(() => {
  resetForm()
})
</script>