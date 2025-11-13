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

      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No PO</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Surat
                  Jalan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal
                  Terima</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Kendaraan
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="shipment in shipments" :key="shipment.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ shipment.noPo }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ shipment.noSuratJalan }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ shipment.supplier }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatDate(shipment.tanggalTerima) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ shipment.noKendaraan }}</td>
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
                    <button @click="showQRModal(shipment)"
                      class="bg-blue-600 text-white hover:bg-blue-700 px-2 py-1 rounded text-xs">
                      Cetak Label QR
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
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
                      <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ parseInt(item.qtyUnit) }}</td>
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
                      <div class="font-medium text-gray-900 mb-2">[{{ labelData.kodeItem }}] - {{ labelData.namaMaterial }}</div>
                      <div class="text-sm text-gray-600 space-y-1">
                        <div>Batch: {{ labelData.batchLot }}</div>
                        <div>Wadah Ke: **{{ labelData.wadahKe }}** / {{ labelData.qtyWadah }}</div>
                        <div>Qty: {{ labelData.qtyUnit }}</div>
                        <div>Exp: {{ labelData.expDate }}</div>
                      </div>
                      <div class="bg-gray-100 p-2 rounded font-mono text-xs text-gray-800 mt-3">
                        <strong>QR Content:</strong><br>{{ labelData.qrContent }}
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

              <button @click="printAllQR"
                class="bg-blue-600 hover:bg-blue-700 px-4 py-2 text-white rounded-md">
                Cetak Semua QR
              </button>
            </div>
          </div>
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

            <!-- Form Header -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No PO *</label>
                <input v-model="newShipment.noPo" type="text" placeholder="Masukkan No PO"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900" />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No Surat Jalan *</label>
                <input v-model="newShipment.noSuratJalan" type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select v-model="newShipment.kategori"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
                  <option value="">Pilih Kategori</option>
                  <option value="Raw Material">Raw Material</option>
                  <option value="Packaging Material">Packaging Material</option>
                  <!-- <option value="Spare Part">Spare Part</option>
                  <option value="Office Supply">Office Supply</option> -->
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Dokumen</label>
                <input type="file" multiple accept="image/*,.pdf"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
              </div>
            </div>

            <!-- Detail Items -->
            <div class="border-t border-gray-200 pt-6">
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
                            class="w-32 text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-900" />
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
                <button @click="saveShipment" :disabled="!isFormValid"
                  class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-400">
                  Simpan
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
                          <div>Batch: {{ item.batchLot }}</div>
                          <div>Qty: {{ item.qtyUnit }}</div>
                          <div>Exp: {{ item.expDate }}</div>
                        </div>
                        <div class="bg-gray-100 p-2 rounded font-mono text-xs text-gray-800 mt-3">
                          <strong>QR Content:</strong><br>{{ item.qrCode }}
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

                <button @click="printAllQR" 
                  class="bg-gray-400  px-4 py-2 text-white rounded-md">
                  Cetak Semua QR
                </button>
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
import { ref, computed, onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import QRCode from 'qrcode'


// Props dari backend
const props = defineProps({
  shipments: Array,
  purchaseOrders: Array,
  suppliers: Array,
  materials: Array
})

// Data reaktif untuk Autocomplete
const supplierSearchQuery = ref('')
const filteredSuppliers = ref([])

// Data reaktif
const showModal = ref(false)
const showQRCodeModal = ref(false)
const showDetailModal = ref(false)
const selectedShipment = ref(null)

const newShipment = ref({
  noPo: '',
  noSuratJalan: '',
  supplier: '',
  noKendaraan: '',
  namaDriver: '',
  tanggalTerima: '',
  kategori: '',
  supplierName: '',
  items: []
})

// Computed
const isFormValid = computed(() => {
  return newShipment.value.noPo &&
    newShipment.value.noSuratJalan &&
    newShipment.value.supplier &&
    newShipment.value.noKendaraan &&
    newShipment.value.namaDriver &&
    newShipment.value.items.length > 0
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
      // Kalkulasi: (Qty Wadah * Qty Unit)
      const rowStock = qtyWadah * qtyUnit;

      if (stockMap[materialId]) {
        // Tambahkan ke total yang sudah ada
        stockMap[materialId].totalQty += rowStock;
      } else {
        // Inisialisasi total baru
        stockMap[materialId] = {
          totalQty: rowStock,
          materialName: item.namaMaterial || item.kodeItemDisplay, // Ambil nama/kode untuk tampilan
          satuan: 'Pcs' // Asumsi satuan adalah Pcs, sesuaikan jika Anda punya properti satuan.
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

const resetForm = () => {
  newShipment.value = {
    noPo: '',
    noSuratJalan: '',
    supplier: '',
    noKendaraan: '',
    namaDriver: '',
    tanggalTerima: new Date().toISOString().slice(0, 16),
    kategori: '',
    items: []
  }
}

const addNewItem = () => {
  newShipment.value.items.push({
    kodeItem: '',
    kodeItemDisplay: '',
    namaMaterial: '',
    batchLot: '',
    expDate: '',
    qtyWadah: '',
    qtyUnit: '',
    pabrikPembuat: '',
    skuSearch: '',
    filteredMaterials: [],
    showSuggestions: false,
    kondisiBaik: false,
    kondisiTidakBaik: false,
    coaAda: false,
    coaTidakAda: false,
    labelMfgAda: false,
    labelMfgTidakAda: false,
    labelCoaSesuai: false,
    labelCoaTidakSesuai: false,
    statusQC: 'Karantina',
    binTarget: '',
    isHalal: false,
    isNonHalal: false,
  })
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
}

const updateNamaMaterial = (index) => {
  const materialId = parseInt(newShipment.value.items[index].kodeItem)
  const selectedMaterial = props.materials.find(m => m.id === materialId)

  if (selectedMaterial) {
    newShipment.value.items[index].namaMaterial = selectedMaterial.name
    newShipment.value.items[index].pabrikPembuat = selectedMaterial.mfg
    newShipment.value.items[index].statusQC = selectedMaterial.qcRequired ? 'To QC' : 'Direct Putaway'
  }
}

const saveShipment = () => {
  if (!isFormValid.value) {
    alert('Mohon lengkapi semua field yang diperlukan')
    return
  }

  // Debug: Log data sebelum dikirim
  console.log('Data yang akan dikirim:', JSON.stringify(newShipment.value, null, 2))

  router.post('/transaction/goods-receipt', newShipment.value, {
    preserveScroll: true,
    onBefore: () => {
      console.log('Request dimulai...')
    },
    onSuccess: (page) => {
      console.log('Response sukses:', page)
      console.log('Flash messages:', page.props.flash)
      closeModal()

      // Reload data setelah sukses
      router.reload({ only: ['shipments'] })
      alert('Penerimaan berhasil disimpan')
    },
    onError: (errors) => {
      console.error('Validation errors:', errors)
      let errorMsg = 'Gagal menyimpan:\n'
      Object.entries(errors).forEach(([key, value]) => {
        errorMsg += `${key}: ${value}\n`
      })
      alert(errorMsg)
    },
    onFinish: () => {
      console.log('Request selesai')
    }
  })
}

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
    const formChecklistNumber = `GWC-GRCKL-${year}${month}-${incomingNumber}`;

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

    shipment.items.forEach((item, index) => {
        // Pastikan qtyWadah adalah integer yang valid
        const qtyWadah = parseInt(item.qtyWadah || '0');
        
        // 2. LAKUKAN PENJUMLAHAN KUMULATIF
        totalWadah += qtyWadah;

        // Nomor wadah awal adalah nilai kumulatif sebelumnya
        const wadahStart = currentWadahStart;
        
        // Nomor wadah akhir adalah wadah awal + jumlah wadah - 1
        const wadahEnd = wadahStart + qtyWadah - 1;
        
        // Lanjutkan: Perbarui nilai kumulatif untuk baris berikutnya
        currentWadahStart = wadahEnd + 1;

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
                <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: middle;">PCS</td>
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

    // Contoh format Qty Wadah (Ini perlu penyesuaian jika data Anda berbeda)
    // Jika Anda memiliki data struktur box seperti "1000 x 11 + 500 x 1", 
    // Anda perlu properti khusus di item Anda, atau buat logika di sini.
    // Untuk demo, saya akan gabungkan qtyUnit dan qtyWadah
    const qtyBoxDescription = `${labelData.qtyUnit} Pcs`; // Asumsi qtyUnit adalah total
    // Jika Anda ingin "1000 x 11 + 500 x 1 box", Anda perlu data yang mendukung ini
    // Contoh placeholder: `1000 x ${labelData.qtyWadah} box`;
    // Atau jika data Anda seperti ini:
    // const qtyDetail = labelData.boxDetail ? labelData.boxDetail + ' box' : `${labelData.qtyWadah} box`;
    const qtyBoxDetail = labelData.qtyWadah > 0 ? `${labelData.qtyUnit / labelData.qtyWadah} x ${labelData.qtyWadah} box` : '';


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
                        white-space: nowrap; /* Teks tidak wrap */
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
                        <img src="https://karir-production.nos.jkt-1.neo.id/logos/05/6980305/logo_gondowangi.png" alt="Logo Gondowangi" class="logo-img">
                        <div class="company-name">GONDOWANGI</div>
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
                                <div class="info-label">No Lot</div>
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
                        <span class="footer-right">Rev. 02</span>
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

  const LABELS_PER_PAGE = 6;
  const LOGO_URL = "https://karir-production.nos.jkt-1.neo.id/logos/05/6980305/logo_gondowangi.png";
  let pagesHTML = '';

  for (let i = 0; i < allLabels.length; i += LABELS_PER_PAGE) {
    const pageLabels = allLabels.slice(i, i + LABELS_PER_PAGE);
    let labelsInPageHTML = '';

    pageLabels.forEach((labelData, labelIndex) => {
      const globalIndex = i + labelIndex;
      const canvas = document.querySelector(`canvas[data-qr-canvas="${globalIndex}"]`);
      let qrDataURL = '';

      const isFirstWadah = labelData.wadahKe === 1;

      if (isFirstWadah && canvas) {
        qrDataURL = canvas.toDataURL('image/png');
      }

      const qrContentHtml = isFirstWadah ?
        `<img src="${qrDataURL}" class="qr-code-img-multi" alt="QR Code">` :
        `<div class="qr-placeholder">NO QR</div>`;

      const formattedTglDatang = formatDateOnly(selectedShipment.value.tanggalTerima);
      const formattedExpDate = formatDateOnly(labelData.expDate);

      // Asumsi format Qty
      const qtyBoxDescription = `${labelData.qtyUnit} Pcs`;
      const qtyBoxDetail = labelData.qtyWadah > 0 ? `${labelData.qtyUnit / labelData.qtyWadah} x ${labelData.qtyWadah} box` : '';


      labelsInPageHTML += `
                <div class="label-wrapper">
                    <div class="qr-label">
                        
                        <div class="header-qr">
                            <img src="${LOGO_URL}" alt="Logo" class="logo-small">
                            <span class="company-qr">GONDOWANGI</span>
                            <span class="status-qr">KARANTINA</span>
                        </div>

                        <div class="content-qr-multi">
                            <table class="info-table">
                                <tr>
                                    <td class="label-cell">Nama Barang</td>
                                    <td class="value-cell">: [${labelData.kodeItem}] ${labelData.namaMaterial}</td>
                                </tr>
                                <tr>
                                    <td class="label-cell">Kode Barang</td>
                                    <td class="value-cell">: ${labelData.kodeItem}</td>
                                </tr>
                                <tr>
                                    <td class="label-cell">No Lot</td>
                                    <td class="value-cell">: ${labelData.batchLot}</td>
                                </tr>
                                <tr>
                                    <td class="label-cell">Supplier</td>
                                    <td class="value-cell">: ${labelData.supplier}</td>
                                </tr>
                                <tr>
                                    <td class="label-cell">Tgl Datang</td>
                                    <td class="value-cell">: ${formattedTglDatang}</td>
                                </tr>
                                <tr>
                                    <td class="label-cell">Exp. Date</td>
                                    <td class="value-cell">: ${formattedExpDate}</td>
                                </tr>
                                <tr>
                                    <td class="label-cell">Jmlh Barang</td>
                                    <td class="value-cell">: ${qtyBoxDescription}</td>
                                </tr>
                                ${qtyBoxDetail ? `
                                <tr>
                                    <td class="label-cell">Jmlh Detail</td>
                                    <td class="value-cell">: ${qtyBoxDetail}</td>
                                </tr>
                                ` : ''}
                                <tr>
                                    <td class="label-cell">Wadah Ke</td>
                                    <td class="value-cell">: ${labelData.wadahKe} / ${labelData.qtyWadah}</td>
                                </tr>
                            </table>
                            <div class="qr-code-wrapper-multi">
                                ${qrContentHtml}
                            </div>
                        </div>
                        
                        <div class="footer-qr-multi">
                            <span class="left-qr-multi">Logistik</span>
                            <span class="right-qr-multi">Rev. 02</span>
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
                @page {
                    size: A4;
                    margin: 0.5cm; 
                }
                
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    background: white;
                }
                
                .print-page {
                    width: 21cm; 
                    height: 29.7cm; 
                    padding: 0.5cm;
                    box-sizing: border-box;
                    display: flex;
                    flex-wrap: wrap;
                    align-content: flex-start;
                    justify-content: space-between; 
                }

                /* Layout 2 Kolom x 3 Baris */
                .label-wrapper {
                    width: 49%; 
                    height: calc(33.33% - 10px); 
                    padding: 3px; 
                    box-sizing: border-box;
                    margin-bottom: 5px; 
                }

                .qr-label {
                    border: 1px solid #000;
                    height: 100%;
                    width: 100%;
                    display: flex;
                    flex-direction: column;
                    padding: 2px;
                    font-size: 6px; /* DIKECILKAN: Font Size Dasar */
                    box-sizing: border-box;
                }

                .header-qr {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    border-bottom: 1px solid #000;
                    padding-bottom: 1px; /* Dikurangi */
                    margin-bottom: 1px; /* Dikurangi */
                }
                .logo-small {
                    height: 14px; /* Logo sedikit lebih kecil */
                    width: auto;
                    margin-right: 1px;
                }
                .company-qr { font-weight: bold; font-size: 7px; color: #2d5f3f; flex-grow: 1; }
                .status-qr { background-color: #000; color: white; padding: 1px 2px; font-weight: bold; font-size: 6px; white-space: nowrap; }

                .content-qr-multi {
                    display: flex;
                    flex-grow: 1;
                    padding-top: 1px;
                }

                /* Styling untuk Tabel Informasi */
                .info-table {
                    border-collapse: collapse;
                    width: 100%;
                    table-layout: fixed;
                }
                .info-table td {
                    padding: 0px 1px; 
                    vertical-align: top;
                    line-height: 1; /* DIKECILKAN: Line Height Paling Rapat */
                }
                .label-cell {
                    width: 60px; 
                    font-weight: normal;
                    padding-right: 1px;
                }
                .value-cell {
                    font-weight: bold;
                    white-space: nowrap; /* UTAMA: Mencegah Line Break */
                    overflow: hidden;
                    text-overflow: ellipsis; /* Menampilkan elipsis jika terpotong */
                }
                /* Akhir Styling Tabel Informasi */


                .qr-code-wrapper-multi {
                    text-align: center;
                    width: 50px; /* Ukuran QR sedikit lebih kecil */
                    height: 50px;
                    display: flex;
                    justify-content: center;
                    align-items: flex-start;
                    margin-left: 1px; /* Dikurangi */
                    flex-shrink: 0;
                    padding-top: 1px;
                }
                .qr-code-img-multi {
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
                    font-size: 6px;
                    color: #999;
                    font-weight: bold;
                }

                .footer-qr-multi {
                    border-top: 1px solid #000;
                    padding-top: 2px;
                    display: flex;
                    justify-content: space-between;
                    font-size: 6px; /* Footer font size disamakan dengan dasar */
                    font-style: italic;
                    margin-top: 1px;
                }
                
                @media print {
                    .print-page { page-break-after: always; }
                    .print-page:last-child { page-break-after: avoid; }
                    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
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