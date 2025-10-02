<template>
    <AppLayout title="Riwayat Aktivitas">
        <div class="space-y-6">
    <!-- Header dengan tombol Buat Penerimaan -->
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Penerimaan</h2>
      <button
        @click="showModal = true"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Buat Penerimaan
      </button>
    </div>

    <!-- Tabel Daftar Penerimaan -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No PO</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No Surat Jalan</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Supplier</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal Terima</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No Kendaraan</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="shipment in shipments" :key="shipment.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ shipment.noPo }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ shipment.noSuratJalan }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ shipment.supplier }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ formatDate(shipment.tanggalTerima) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ shipment.noKendaraan }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(shipment.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ shipment.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                  <button @click="viewDetail(shipment)" class="bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-800 px-2 py-1 rounded text-xs">Detail</button>
                  <button @click="printChecklist(shipment)" class="bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-800 px-2 py-1 rounded text-xs">Cetak Checklist</button>
                  <button @click="printFinanceSlip(shipment)" class="bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 hover:bg-purple-200 dark:hover:bg-purple-800 px-2 py-1 rounded text-xs">Cetak Finance</button>
                  <button @click="showQRModal(shipment)" class="bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 hover:bg-blue-200 dark:hover:bg-blue-800 px-2 py-1 rounded text-xs">Cetak Label QR</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Detail Penerimaan -->
    <div v-if="showDetailModal" class="fixed inset-0 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: #2b333fab;">
      <div class="bg-white dark:bg-gray-800 rounded-lg max-w-6xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <!-- Header Modal -->
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Detail Penerimaan - {{ selectedShipment?.noSuratJalan }}</h3>
            <button @click="closeDetailModal" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Info Shipment -->
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
              <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">No Incoming:</span>
                <span class="ml-2 text-gray-900 dark:text-white">{{ selectedShipment?.incomingNumber }}</span>
              </div>
              <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">No PO:</span>
                <span class="ml-2 text-gray-900 dark:text-white">{{ selectedShipment?.noPo }}</span>
              </div>
              <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Supplier:</span>
                <span class="ml-2 text-gray-900 dark:text-white">{{ selectedShipment?.supplier }}</span>
              </div>
              <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">No Kendaraan:</span>
                <span class="ml-2 text-gray-900 dark:text-white">{{ selectedShipment?.noKendaraan }}</span>
              </div>
              <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Nama Driver:</span>
                <span class="ml-2 text-gray-900 dark:text-white">{{ selectedShipment?.namaDriver }}</span>
              </div>
              <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Tanggal Terima:</span>
                <span class="ml-2 text-gray-900 dark:text-white">{{ formatDate(selectedShipment?.tanggalTerima) }}</span>
              </div>
              <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Kategori:</span>
                <span class="ml-2 text-gray-900 dark:text-white">{{ selectedShipment?.kategori }}</span>
              </div>
              <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Status:</span>
                <span :class="getStatusClass(selectedShipment?.status)" class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ selectedShipment?.status }}
                </span>
              </div>
            </div>
          </div>

          <!-- Detail Items -->
          <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Detail Material</h4>
            
            <div class="overflow-x-auto border border-gray-200 dark:border-gray-600 rounded-lg">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600" style="min-width: 1400px;">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Kode Item</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Nama Material</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Batch/Mfg Lot</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Exp Date</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Qty Wadah</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Qty Unit</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Kondisi</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">CoA</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Label Mfg</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Label & CoA Sesuai</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Pabrik Pembuat</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Status QC</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-600 bg-white dark:bg-gray-800">
                  <tr v-for="(item, index) in selectedShipment?.items" :key="index">
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ item.kodeItem }}</td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ item.namaMaterial }}</td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ item.batchLot }}</td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ item.expDate }}</td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ item.qtyWadah }}</td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ item.qtyUnit }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <div class="flex gap-1">
                        <span v-if="item.kondisiBaik" class="bg-green-500 text-white px-2 py-1 text-xs rounded">Baik</span>
                        <span v-if="item.kondisiTidakBaik" class="bg-red-500 text-white px-2 py-1 text-xs rounded">Tidak Baik</span>
                      </div>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <div class="flex gap-1">
                        <span v-if="item.coaAda" class="bg-green-500 text-white px-2 py-1 text-xs rounded">Ada</span>
                        <span v-if="item.coaTidakAda" class="bg-red-500 text-white px-2 py-1 text-xs rounded">Tidak</span>
                      </div>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <div class="flex gap-1">
                        <span v-if="item.labelMfgAda" class="bg-green-500 text-white px-2 py-1 text-xs rounded">Ada</span>
                        <span v-if="item.labelMfgTidakAda" class="bg-red-500 text-white px-2 py-1 text-xs rounded">Tidak</span>
                      </div>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <div class="flex gap-1">
                        <span v-if="item.labelCoaSesuai" class="bg-green-500 text-white px-2 py-1 text-xs rounded">Sesuai</span>
                        <span v-if="item.labelCoaTidakSesuai" class="bg-red-500 text-white px-2 py-1 text-xs rounded">Tidak Sesuai</span>
                      </div>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ item.pabrikPembuat }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <span :class="item.statusQC === 'To QC' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                        {{ item.statusQC }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Footer Modal -->
          <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
            <button @click="closeDetailModal" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500">
              Tutup
            </button>
            <button @click="printChecklist(selectedShipment)" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
              Cetak Checklist
            </button>
            <button @click="printFinanceSlip(selectedShipment)" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
              Cetak Finance
            </button>
            <button @click="showQRModal(selectedShipment)" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
              Cetak Label QR
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Form Buat Penerimaan -->
    <div v-if="showModal" class="fixed inset-0 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: #2b333fab;">
      <div class="bg-white dark:bg-gray-800 rounded-lg max-w-6xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <!-- Header Modal -->
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Buat Penerimaan Baru</h3>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Form Header -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No PO *</label>
              <select v-model="newShipment.noPo" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="">Pilih No PO</option>
                <option v-for="po in dummyPOs" :key="po" :value="po">{{ po }}</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No Surat Jalan *</label>
              <input v-model="newShipment.noSuratJalan" type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Supplier *</label>
              <select v-model="newShipment.supplier" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="">Pilih Supplier</option>
                <option v-for="supplier in dummySuppliers" :key="supplier" :value="supplier">{{ supplier }}</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No Kendaraan *</label>
              <input v-model="newShipment.noKendaraan" type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Driver *</label>
              <input v-model="newShipment.namaDriver" type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal & Waktu Terima</label>
              <input v-model="newShipment.tanggalTerima" type="datetime-local" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
              <select v-model="newShipment.kategori" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="">Pilih Kategori</option>
                <option value="Raw Material">Raw Material</option>
                <option value="Packaging Material">Packaging Material</option>
                <option value="Spare Part">Spare Part</option>
                <option value="Office Supply">Office Supply</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upload Dokumen</label>
              <input type="file" multiple accept="image/*,.pdf" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
            </div>
          </div>

          <!-- Detail Items -->
          <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
            <div class="flex justify-between items-center mb-4">
              <h4 class="text-lg font-medium text-gray-900 dark:text-white">Detail Material</h4>
              <button @click="addNewItem" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                + Tambah Baris
              </button>
            </div>

            <div class="overflow-x-auto border border-gray-200 dark:border-gray-600 rounded-lg">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600" style="min-width: 1400px;">
                <thead class="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Kode Item</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Nama Material</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Batch/Mfg Lot</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Exp Date</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Qty Wadah</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Qty Unit</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Kondisi</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">CoA</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Label Mfg</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Label & CoA Sesuai</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Pabrik Pembuat</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Status QC</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase whitespace-nowrap">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-600 bg-white dark:bg-gray-800">
                  <tr v-for="(item, index) in newShipment.items" :key="index">
                    <td class="px-3 py-2 whitespace-nowrap">
                      <select v-model="item.kodeItem" @change="updateNamaMaterial(index)" class="w-32 text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Pilih SKU</option>
                        <option v-for="sku in dummySKUs" :key="sku.code" :value="sku.code">{{ sku.code }}</option>
                      </select>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <input v-model="item.namaMaterial" type="text" readonly class="w-40 text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-gray-50 dark:bg-gray-600 text-gray-900 dark:text-white">
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <input v-model="item.batchLot" type="text" class="w-28 text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <input v-model="item.expDate" type="date" class="w-36 text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <input v-model="item.qtyWadah" type="number" class="w-24 text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <input v-model="item.qtyUnit" type="number" class="w-24 text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <div class="flex gap-1">
                        <button @click="toggleCheckbox(item, 'kondisiBaik')" :class="[item.kondisiBaik ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700', 'px-2 py-1 text-xs rounded']">Baik</button>
                        <button @click="toggleCheckbox(item, 'kondisiTidakBaik')" :class="[item.kondisiTidakBaik ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700', 'px-2 py-1 text-xs rounded']">Tidak Baik</button>
                      </div>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <div class="flex gap-1">
                        <button @click="toggleCheckbox(item, 'coaAda')" :class="[item.coaAda ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700', 'px-2 py-1 text-xs rounded']">Ada</button>
                        <button @click="toggleCheckbox(item, 'coaTidakAda')" :class="[item.coaTidakAda ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700', 'px-2 py-1 text-xs rounded']">Tidak</button>
                      </div>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <div class="flex gap-1">
                        <button @click="toggleCheckbox(item, 'labelMfgAda')" :class="[item.labelMfgAda ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700', 'px-2 py-1 text-xs rounded']">Ada</button>
                        <button @click="toggleCheckbox(item, 'labelMfgTidakAda')" :class="[item.labelMfgTidakAda ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700', 'px-2 py-1 text-xs rounded']">Tidak</button>
                      </div>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <div class="flex gap-1">
                        <button @click="toggleCheckbox(item, 'labelCoaSesuai')" :class="[item.labelCoaSesuai ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700', 'px-2 py-1 text-xs rounded']">Ada</button>
                        <button @click="toggleCheckbox(item, 'labelCoaTidakSesuai')" :class="[item.labelCoaTidakSesuai ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700', 'px-2 py-1 text-xs rounded']">Tidak</button>
                      </div>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <input v-model="item.pabrikPembuat" type="text" class="w-32 text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Nama pabrik">
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <span class="text-sm text-gray-600 dark:text-gray-400">{{ item.statusQC }}</span>
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <button @click="removeItem(index)" class="bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-800 px-2 py-1 rounded text-xs">Hapus</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Footer Modal -->
          <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
            <button @click="closeModal" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500">
              Batal
            </button>
            <button @click="saveShipment" :disabled="!isFormValid" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-400">
              Simpan
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal QR Code -->
    <div v-if="showQRCodeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: #2b333fab;">
      <div class="bg-white dark:bg-gray-800 rounded-lg max-w-4xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <div class="p-6">
          <!-- Header Modal QR -->
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Label QR Code - {{ selectedShipment?.noSuratJalan }}</h3>
            <button @click="closeQRModal" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Info Shipment -->
          <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">No PO:</span>
                <span class="ml-2 text-gray-900 dark:text-white">{{ selectedShipment?.noPo }}</span>
              </div>
              <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Supplier:</span>
                <span class="ml-2 text-gray-900 dark:text-white">{{ selectedShipment?.supplier }}</span>
              </div>
              <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">No Kendaraan:</span>
                <span class="ml-2 text-gray-900 dark:text-white">{{ selectedShipment?.noKendaraan }}</span>
              </div>
              <div>
                <span class="font-medium text-gray-700 dark:text-gray-300">Tanggal:</span>
                <span class="ml-2 text-gray-900 dark:text-white">{{ formatDate(selectedShipment?.tanggalTerima) }}</span>
              </div>
            </div>
          </div>

          <!-- Daftar QR Items -->
          <div class="space-y-4">
            <h4 class="text-md font-medium text-gray-900 dark:text-white">Daftar QR Code per Item:</h4>
            <div v-if="selectedShipment?.items && selectedShipment.items.length > 0" class="space-y-4">
              <div v-for="(item, index) in selectedShipment.items" :key="index" class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Info Item -->
                  <div>
                    <div class="font-medium text-gray-900 dark:text-white mb-2">{{ item.kodeItem }} - {{ item.namaMaterial }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                      <div>Batch: {{ item.batchLot }}</div>
                      <div>Qty: {{ item.qtyUnit }}</div>
                      <div>Exp: {{ item.expDate }}</div>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-600 p-2 rounded font-mono text-xs text-gray-800 dark:text-gray-200 mt-3">
                      <strong>QR Content:</strong><br>{{ item.qrCode }}
                    </div>
                    <div class="flex space-x-2 mt-3">
                      <button @click="downloadQR(item)" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                        Download QR
                      </button>
                      <button @click="printSingleQR(item)" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                        Cetak QR
                      </button>
                    </div>
                  </div>
                  
                  <!-- Preview QR -->
                  <div class="flex flex-col items-center justify-center">
                    <div class="bg-white p-4 rounded-lg border-2 border-gray-300 mb-2">
                      <canvas :data-qr-canvas="index" class="block"></canvas>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                      Preview QR Code
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
              Belum ada item untuk shipment ini
            </div>
          </div>

          <!-- Footer Modal QR -->
          <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
            <button @click="closeQRModal" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500">
              Tutup
            </button>
            <button @click="printAllQR" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
              Cetak Semua QR
            </button>
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

// Data reaktif
const showModal = ref(false)
const showQRCodeModal = ref(false)
const showDetailModal = ref(false)
const selectedShipment = ref(null)
const shipments = ref([])

// Data dummy untuk dropdown
const dummyPOs = ref(['PO-001/2025', 'PO-002/2025', 'PO-003/2025'])
const dummySuppliers = ref([
  'PT Supplier A',
  'PT Supplier B', 
  'CV Supplier C',
  'Sinar Universal Labelindo,PT'
])
const dummySKUs = ref([
  { code: 'RM-001', name: 'Tepung Terigu', mfg: 'PT Indofood' },
  { code: 'RM-002', name: 'Gula Pasir', mfg: 'PT Gulaku' },
  { code: 'PM-001', name: 'Kardus Box 20x30', mfg: 'PT Kemasan Indo' },
  { code: 'PM-002', name: 'Plastik Wrap', mfg: 'PT Plastik Jaya' },
  { code: '23412', name: 'Sticker Botol Natur Natural Extract Hair Tonic Aloe Vera Extract 90 ML - Redesign', mfg: 'Sinar Universal Labelindo' }
])

// Form data
const newShipment = ref({
  noPo: '',
  noSuratJalan: '',
  supplier: '',
  noKendaraan: '',
  namaDriver: '',
  tanggalTerima: '',
  kategori: '',
  items: []
})

// Data dummy untuk tabel
const initDummyData = () => {
  shipments.value = [
    {
      id: 1,
      incomingNumber: 'IN/26760',
      noPo: 'PO62860',
      noSuratJalan: 'LBL202501532',
      supplier: 'Sinar Universal Labelindo,PT',
      tanggalTerima: '2025-08-27T14:50:06',
      noKendaraan: 'B 2222 FFF',
      namaDriver: 'Anton S',
      status: 'Karantina',
      kategori: 'Packaging Material',
      items: [
        {
          kodeItem: '23412',
          namaMaterial: 'Sticker Botol Natur Natural Extract Hair Tonic Aloe Vera Extract 90 ML - Redesign',
          batchLot: 'BATCH001',
          expDate: '2025-12-31',
          qtyWadah: '10',
          qtyUnit: '20000',
          pabrikPembuat: 'Sinar Universal Labelindo',
          kondisiBaik: true,
          kondisiTidakBaik: false,
          coaAda: true,
          coaTidakAda: false,
          labelMfgAda: true,
          labelMfgTidakAda: false,
          labelCoaSesuai: true,
          labelCoaTidakSesuai: false,
          statusQC: 'To QC',
          qrCode: 'IN/26760|23412|BATCH001|20000|2025-12-31'
        }
      ]
    },
    {
      id: 2,
      incomingNumber: 'IN/20250918/0002',
      noPo: 'PO-002/2025',
      noSuratJalan: 'SJ-002/2025',
      supplier: 'PT Supplier B',
      tanggalTerima: '2025-09-17T14:15:00',
      noKendaraan: 'B 5678 EF',
      namaDriver: 'Budi Santoso',
      status: 'Karantina',
      kategori: 'Raw Material',
      items: [
        {
          kodeItem: 'RM-002',
          namaMaterial: 'Gula Pasir',
          batchLot: 'BATCH003',
          expDate: '2025-11-15',
          qtyWadah: '5',
          qtyUnit: '25',
          pabrikPembuat: 'PT Gulaku',
          kondisiBaik: true,
          kondisiTidakBaik: false,
          coaAda: false,
          coaTidakAda: true,
          labelMfgAda: true,
          labelMfgTidakAda: false,
          labelCoaSesuai: false,
          labelCoaTidakSesuai: true,
          statusQC: 'To QC',
          qrCode: 'IN/20250917/0002|RM-002|BATCH003|25|2025-11-15'
        }
      ]
    }
  ]
}

// Computed
const isFormValid = computed(() => {
  return newShipment.value.noPo && 
         newShipment.value.noSuratJalan && 
         newShipment.value.supplier && 
         newShipment.value.noKendaraan && 
         newShipment.value.namaDriver &&
         newShipment.value.items.length > 0
})

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
    namaMaterial: '',
    batchLot: '',
    expDate: '',
    qtyWadah: '',
    qtyUnit: '',
    pabrikPembuat: '',
    kondisiBaik: false,
    kondisiTidakBaik: false,
    coaAda: false,
    coaTidakAda: false,
    labelMfgAda: false,
    labelMfgTidakAda: false,
    labelCoaSesuai: false,
    labelCoaTidakSesuai: false,
    statusQC: 'To QC'
  })
}

const removeItem = (index) => {
  newShipment.value.items.splice(index, 1)
}

const toggleCheckbox = (item, field) => {
  // Reset all related checkboxes first
  if (field === 'kondisiBaik' || field === 'kondisiTidakBaik') {
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

const updateNamaMaterial = (index) => {
  const selectedSKU = dummySKUs.value.find(sku => sku.code === newShipment.value.items[index].kodeItem)
  if (selectedSKU) {
    newShipment.value.items[index].namaMaterial = selectedSKU.name
    newShipment.value.items[index].pabrikPembuat = selectedSKU.mfg
    // Auto set status QC based on SKU type
    if (selectedSKU.code.startsWith('RM-')) {
      newShipment.value.items[index].statusQC = 'To QC'
    } else {
      newShipment.value.items[index].statusQC = 'Direct Putaway'
    }
  }
}

const saveShipment = () => {
  // Generate ID dan incoming number
  const newId = shipments.value.length + 1
  const today = new Date().toISOString().slice(0, 10).replace(/-/g, '')
  const incomingNumber = `IN/${today}/${String(newId).padStart(4, '0')}`
  
  // Buat record shipment baru
  const shipment = {
    id: newId,
    incomingNumber,
    noPo: newShipment.value.noPo,
    noSuratJalan: newShipment.value.noSuratJalan,
    supplier: newShipment.value.supplier,
    tanggalTerima: newShipment.value.tanggalTerima,
    noKendaraan: newShipment.value.noKendaraan,
    namaDriver: newShipment.value.namaDriver,
    kategori: newShipment.value.kategori,
    status: 'Karantina', // Status otomatis karantina
    items: newShipment.value.items.map(item => ({
      ...item,
      qrCode: generateQR(incomingNumber, item.kodeItem, item.batchLot, item.qtyUnit, item.expDate)
    }))
  }
  
  shipments.value.unshift(shipment)
  
  alert(`Penerimaan berhasil disimpan dengan nomor: ${incomingNumber}`)
  closeModal()
}

const generateQR = (shipmentId, itemId, lot, qty, expDate) => {
  return `${shipmentId}|${itemId}|${lot}|${qty}|${expDate}`
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
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  })
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
    'Draft': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'Karantina': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    'Arrived': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    'QC': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
    'Completed': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
  }
  return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
}


const printChecklist = (shipment) => {
  // Create print window with checklist form
  const printWindow = window.open('', '_blank')
  
  let itemsHTML = ''
  shipment.items.forEach((item, index) => {
    const wadahStart = index + 1
    const wadahEnd = wadahStart + parseInt(item.qtyWadah || '1') - 1
    
    itemsHTML += `
      <tr>
        <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: middle; height: 40px;">
          ${wadahStart}${wadahEnd !== wadahStart ? `-${wadahEnd}` : ''}
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
        <td style="border: 1px solid #000; padding: 8px; text-align: center; vertical-align: middle;">${item.qtyUnit}</td>
      </tr>
    `
  })
  
  // Add empty rows to fill the table (total 8-10 rows)
  for(let i = shipment.items.length; i < 10; i++) {
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
              <div class="form-number-box">( No Form Checklist )</div>
            </div>
            
            <div class="header-title">E-FORM CHECKLIST PENERIMAAN MATERIAL DARI VENDOR</div>
          </div>
          
          <table class="form-fields">
            <tr>
              <td class="field-label">Kode Item</td>
              <td class="field-value">${shipment.items[0]?.kodeItem || ''}</td>
            </tr>
            <tr>
              <td class="field-label">Nama Material</td>
              <td class="field-value">${shipment.items[0]?.namaMaterial || ''}</td>
            </tr>
            <tr>
              <td class="field-label">Label dari Mfg*</td>
              <td class="field-value">
                <span class="checkbox-inline">Ada ${shipment.items[0]?.labelMfgAda ? '✓' : ''}</span>
                <span class="checkbox-inline">Tidak ${shipment.items[0]?.labelMfgTidakAda ? '✓' : ''}</span>
                <span style="margin-left: 100px;">Label & CoA sesuai :</span>
                <span class="checkbox-inline">Ada ${shipment.items[0]?.labelCoaSesuai ? '✓' : ''}</span>
                <span class="checkbox-inline">Tidak ${shipment.items[0]?.labelCoaTidakSesuai ? '✓' : ''}</span>
              </td>
            </tr>
            <tr>
              <td class="field-label">Pabrik Pembuat (mfg)*</td>
              <td class="field-value">${shipment.items[0]?.pabrikPembuat || ''}</td>
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
              <td class="field-value">${shipment.items[0]?.batchLot || ''}</td>
            </tr>
            <tr>
              <td class="field-label">ED</td>
              <td class="field-value">${shipment.items[0]?.expDate || ''}</td>
            </tr>
            <tr>
              <td class="field-label">CoA</td>
              <td class="field-value">
                <span class="checkbox-inline">Ada ${shipment.items[0]?.coaAda ? '✓' : ''}</span>
                <span class="checkbox-inline">Tidak ${shipment.items[0]?.coaTidakAda ? '✓' : ''}</span>
              </td>
            </tr>
            <tr>
              <td class="field-label">Jumlah Wadah</td>
              <td class="field-value">${shipment.items[0]?.qtyWadah || ''}</td>
            </tr>
            <tr>
              <td class="field-label">Tgl. Diterima</td>
              <td class="field-value">
                ${formatDateOnly(shipment.tanggalTerima)} &nbsp;&nbsp;&nbsp; 
                Start : ${formatTime(shipment.tanggalTerima)} WIB &nbsp;&nbsp;&nbsp; 
                End : _____ WIB
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

const showQRModal = (shipment) => {
  selectedShipment.value = shipment
  showQRCodeModal.value = true
  // Generate QR codes after modal is shown
  setTimeout(() => {
    generateQRCodes()
  }, 100)
}

const generateQRCodes = () => {
  if (!selectedShipment.value?.items) return
  
  selectedShipment.value.items.forEach((item, index) => {
    const canvas = document.querySelector(`canvas[data-qr-canvas="${index}"]`)
    if (canvas) {
      const ctx = canvas.getContext('2d')
      canvas.width = 150
      canvas.height = 150
      
      // Simple QR code placeholder (you can integrate real QR library here)
      const size = 150
      const moduleSize = size / 21 // 21x21 modules for simple QR
      
      // Create simple pattern based on QR content
      const content = item.qrCode
      let hash = 0
      for (let i = 0; i < content.length; i++) {
        hash = ((hash << 5) - hash + content.charCodeAt(i)) & 0xffffffff
      }
      
      // Clear canvas with white background
      ctx.fillStyle = '#ffffff'
      ctx.fillRect(0, 0, size, size)
      ctx.fillStyle = '#000000'
      
      // Draw simple QR pattern
      for (let row = 0; row < 21; row++) {
        for (let col = 0; col < 21; col++) {
          // Create pattern based on hash and position
          const shouldFill = ((hash >> ((row + col) % 32)) & 1) === 1 || 
                           (row < 7 && col < 7 && (row === 0 || row === 6 || col === 0 || col === 6)) || // Top-left finder
                           (row < 7 && col >= 14 && (row === 0 || row === 6 || col === 14 || col === 20)) || // Top-right finder  
                           (row >= 14 && col < 7 && (row === 14 || row === 20 || col === 0 || col === 6)) // Bottom-left finder
          
          if (shouldFill) {
            ctx.fillRect(col * moduleSize, row * moduleSize, moduleSize - 0.5, moduleSize - 0.5)
          }
        }
      }
      
      // Add center squares for finder patterns
      ctx.fillRect(2 * moduleSize, 2 * moduleSize, 3 * moduleSize, 3 * moduleSize) // Top-left
      ctx.fillRect(16 * moduleSize, 2 * moduleSize, 3 * moduleSize, 3 * moduleSize) // Top-right  
      ctx.fillRect(2 * moduleSize, 16 * moduleSize, 3 * moduleSize, 3 * moduleSize) // Bottom-left
    }
  })
}

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

const printSingleQR = (item) => {
  const index = selectedShipment.value.items.findIndex(i => i === item)
  const canvas = document.querySelector(`canvas[data-qr-canvas="${index}"]`)
  
  if (canvas) {
    // Create print window with formatted label
    const printWindow = window.open('', '_blank')
    const qrDataURL = canvas.toDataURL('image/png')
    
    printWindow.document.write(`
      <html>
        <head>
          <title>QR Label - ${item.kodeItem}</title>
          <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .label { border: 2px solid #000; padding: 15px; width: 300px; text-align: center; }
            .qr-code { margin: 10px 0; }
            .item-info { font-size: 12px; margin: 5px 0; }
            .item-code { font-weight: bold; font-size: 14px; }
            @media print {
              body { margin: 0; }
              .label { border: 2px solid #000; page-break-after: always; }
            }
          </style>
        </head>
        <body>
          <div class="label">
            <div class="item-code">${item.kodeItem}</div>
            <div class="item-info">${item.namaMaterial}</div>
            <div class="qr-code">
              <img src="${qrDataURL}" width="120" height="120">
            </div>
            <div class="item-info">Batch: ${item.batchLot}</div>
            <div class="item-info">Qty: ${item.qtyUnit}</div>
            <div class="item-info">Exp: ${item.expDate}</div>
            <div class="item-info">PO: ${selectedShipment.value.noPo}</div>
          </div>
        </body>
      </html>
    `)
    
    printWindow.document.close()
    printWindow.focus()
    
    // Auto print after loading
    setTimeout(() => {
      printWindow.print()
      printWindow.close()
    }, 500)
  }
}

const printAllQR = () => {
  if (selectedShipment.value?.items?.length > 0) {
    // Create print window with all labels
    const printWindow = window.open('', '_blank')
    let labelsHTML = ''
    
    selectedShipment.value.items.forEach((item, index) => {
      const canvas = document.querySelector(`canvas[data-qr-canvas="${index}"]`)
      if (canvas) {
        const qrDataURL = canvas.toDataURL('image/png')
        labelsHTML += `
          <div class="label">
            <div class="item-code">${item.kodeItem}</div>
            <div class="item-info">${item.namaMaterial}</div>
            <div class="qr-code">
              <img src="${qrDataURL}" width="120" height="120">
            </div>
            <div class="item-info">Batch: ${item.batchLot}</div>
            <div class="item-info">Qty: ${item.qtyUnit}</div>
            <div class="item-info">Exp: ${item.expDate}</div>
            <div class="item-info">PO: ${selectedShipment.value.noPo}</div>
          </div>
        `
      }
    })
    
    printWindow.document.write(`
      <html>
        <head>
          <title>All QR Labels - ${selectedShipment.value.noSuratJalan}</title>
          <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .label { 
              border: 2px solid #000; 
              padding: 15px; 
              width: 300px; 
              text-align: center; 
              margin-bottom: 20px;
              display: inline-block;
              vertical-align: top;
            }
            .qr-code { margin: 10px 0; }
            .item-info { font-size: 12px; margin: 5px 0; }
            .item-code { font-weight: bold; font-size: 14px; }
            @media print {
              body { margin: 0; }
              .label { 
                border: 2px solid #000; 
                page-break-inside: avoid;
                margin: 10px;
              }
            }
          </style>
        </head>
        <body>
          ${labelsHTML}
        </body>
      </html>
    `)
    
    printWindow.document.close()
    printWindow.focus()
    
    setTimeout(() => {
      printWindow.print()
      printWindow.close()
    }, 500)
  } else {
    alert('Tidak ada item untuk dicetak')
  }
}

// Lifecycle
onMounted(() => {
  initDummyData()
  resetForm()
})
</script>