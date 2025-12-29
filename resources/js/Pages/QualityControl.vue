<template>
  <AppLayout title="Riwayat Aktivitas">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Daftar QC (Quality Control)</h2>
        <div class="text-sm text-gray-600">
          Total item menunggu QC: {{ itemsToQC.total }}
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

      <!-- Filters -->
      <div class="bg-white p-4 rounded-lg shadow mb-6">
        <div class="flex flex-col md:flex-row gap-4">
          <!-- Status Filter -->
           <div class="w-48">
            <select v-model="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
              <option value="">Semua Status</option>
              <option value="To QC">Menunggu QC</option>
              <option value="Completed">Sudah QC (Pass/Reject)</option>
              <option value="PASS">Lolos QC (PASS)</option>
              <option value="REJECT">Tidak Lolos (REJECT)</option>
            </select>
          </div>

          <!-- Search -->
          <div class="flex-grow">
            <input v-model="search" type="text" placeholder="Cari No Shipment, PO, Surat Jalan, Material..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
          </div>

          <!-- Date Range -->
          <div class="flex gap-2">
            <input v-model="dateStart" type="date"
              class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
              title="Tanggal Mulai">
            <span class="self-center text-gray-500">-</span>
            <input v-model="dateEnd" type="date"
              class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
              title="Tanggal Akhir">
          </div>

          <!-- Limit -->
          <div class="w-24">
             <select v-model="limit" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="all">Semua</option>
             </select>
          </div>
        </div>
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
                <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Datang</th> -->
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Diambil</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status QC</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="item in itemsToQC.data" :key="item.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <div>{{ item.shipmentNumber }}</div>
                  <div class="text-xs text-gray-500">{{ item.noPo }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.noSuratJalan }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.supplier }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">
                  <div class="font-medium">{{ item.kodeItem }}</div>
                  <div class="text-xs text-gray-500">{{ item.namaMaterial }}</div>
                  <!-- \u2b50 RE-QC BADGE -->
                  <span v-if="item.is_reqc" class="mt-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-orange-100 text-orange-800 border border-orange-300">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                    </svg>
                    RE-QC ({{ item.reqc_count }}x)
                  </span>
                </td>
                <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatInteger(item.qtyDatangTotal) }} {{ item.uom }} 
                </td> -->

                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <span class="font-bold text-blue-700">
                    {{ formatQty(getDisplayQtyReceived(item)) }} {{ item.uom }}
                  </span>
                </td>
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

                    <!-- Detail Button for Completed QC -->
                    <button v-if="item.statusQC === 'PASS' || item.statusQC === 'REJECT'" @click="showQCDetailModal(item)"
                      class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-3 py-1 rounded text-xs flex items-center gap-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      Detail
                    </button>

                    <button v-if="item.statusQC === 'PASS'" @click="printReleaseQRLabel(item)"
                      class="bg-purple-100 text-purple-700 hover:bg-purple-200 px-2 py-1 rounded text-xs">
                      Cetak Label QR (RELEASED)
                    </button>

                    <button v-if="item.statusQC === 'REJECT'" @click="printRejectQRLabel(item)"
                      class="bg-red-100 text-red-700 hover:bg-red-200 px-2 py-1 rounded text-xs">
                      Cetak Label QR (REJECT)
                    </button>

                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
         <div class="px-6 py-4 border-t border-gray-200">
            <Pagination :links="itemsToQC.links" />
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
                  <div class="mt-1 text-sm text-gray-900 font-medium">{{ formatInteger(selectedItem?.qtyReceived) }}
                    {{ selectedItem?.uom }}</div>
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Batch Lot</label>
                <input v-model="qcForm.batchLot" type="text" readonly
                  class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900">
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Supplier</label>
                <input v-model="qcForm.supplier" type="text" readonly
                  class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900">
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <input v-model="qcForm.kategori" type="text" readonly
                  class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-900">
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
              <h4 class="text-md font-medium text-gray-900 mb-4">Pengambilan Sampel QC</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                <div class="col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Qty Sampel Diambil ({{ qcForm.uom }}) *
                  </label>
                  <input v-model="qcForm.qtySample" type="number" min="0" :max="qcForm.totalIncoming"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
                  <p class="text-xs text-red-500 mt-1" v-if="qcForm.qtySample > qcForm.totalIncoming">
                    Qty Sampel tidak boleh melebihi Total Incoming ({{ qcForm.totalIncoming }} {{ qcForm.uom }})
                  </p>
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

                  <!-- ⭐ EXPIRED DATE EDIT (ONLY FOR RE-QC PASS) -->
                  <div v-if="selectedItem?.is_reqc && qcForm.hasilQC === 'PASS'" class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                    <label class="block text-sm font-medium text-orange-900 mb-1">
                      <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                        Expired Date Baru (RE-QC PASS)
                      </span>
                    </label>
                    <input v-model="qcForm.newExpDate" type="date" required
                      class="w-full px-3 py-2 border border-orange-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white text-gray-900">
                    <p class="text-xs text-orange-700 mt-1">Masukkan tanggal expired baru untuk material yang PASS Re-QC</p>
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
              <button @click="backToDetail" :disabled="isSubmittingQC" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                Kembali ke Detail
              </button>
              <button @click="submitQC" :disabled="!isQCFormValid || isSubmittingQC"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center gap-2">
                <svg v-if="isSubmittingQC" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ isSubmittingQC ? 'Menyimpan...' : 'Simpan Hasil QC' }}</span>
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

      <!-- Modal: QC Detail (for completed QC) -->
      <div v-if="showQCDetail"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]"
        style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
          <div class="p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Detail QC - {{ qcDetailData?.kodeItem }}
              </h3>
              <button @click="closeQCDetailModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Content -->
            <div class="space-y-6">
              <!-- Material Information -->
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                  </svg>
                  Informasi Material
                </h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <span class="text-gray-600">Kode Item:</span>
                    <span class="ml-2 font-medium text-gray-900">{{ qcDetailData?.kodeItem }}</span>
                  </div>
                  <div>
                    <span class="text-gray-600">Nama Material:</span>
                    <span class="ml-2 font-medium text-gray-900">{{ qcDetailData?.namaMaterial }}</span>
                  </div>
                  <div>
                    <span class="text-gray-600">Batch Lot:</span>
                    <span class="ml-2 font-medium text-gray-900">{{ qcDetailData?.batchLot }}</span>
                  </div>
                  <div>
                    <span class="text-gray-600">Supplier:</span>
                    <span class="ml-2 font-medium text-gray-900">{{ qcDetailData?.supplier }}</span>
                  </div>
                  <div>
                    <span class="text-gray-600">No PO:</span>
                    <span class="ml-2 font-medium text-gray-900">{{ qcDetailData?.noPo }}</span>
                  </div>
                  <div>
                    <span class="text-gray-600">No Surat Jalan:</span>
                    <span class="ml-2 font-medium text-gray-900">{{ qcDetailData?.noSuratJalan }}</span>
                  </div>
                </div>
              </div>

              <!-- QC Results -->
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Hasil QC
                </h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <span class="text-gray-600">Status QC:</span>
                    <span :class="qcDetailData?.statusQC === 'PASS' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                      class="ml-2 px-2 py-1 text-xs font-semibold rounded-full">
                      {{ qcDetailData?.statusQC }}
                    </span>
                  </div>
                  <div>
                    <span class="text-gray-600">Qty Sampel Diambil:</span>
                    <span class="ml-2 font-medium text-blue-600">{{ qcDetailData?.qcSampleQty }} {{ qcDetailData?.uom }}</span>
                  </div>
                </div>
              </div>

              <!-- QC Details -->
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                  <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                  Detail Pemeriksaan
                </h4>
                <div class="space-y-3 text-sm">
                  <div v-if="qcDetailData?.catatanQC">
                    <span class="text-gray-600 font-medium">Catatan QC:</span>
                    <div class="mt-1 p-3 bg-white rounded border border-gray-200 text-gray-900">
                      {{ qcDetailData?.catatanQC }}
                    </div>
                  </div>
                  <div v-else>
                    <span class="text-gray-500 italic">Tidak ada catatan QC</span>
                  </div>
                </div>
              </div>

              <!-- User & Timestamp Information -->
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="text-sm font-semibold text-blue-900 mb-3 flex items-center gap-2">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  Informasi QC
                </h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <span class="text-blue-700">Diperiksa Oleh:</span>
                    <span class="ml-2 font-semibold text-blue-900">{{ qcDetailData?.qc_by_name || 'N/A' }}</span>
                  </div>
                  <div>
                    <span class="text-blue-700">Tanggal QC:</span>
                    <span class="ml-2 font-medium text-blue-900">{{ qcDetailData?.qc_date || 'N/A' }}</span>
                  </div>
                  <div>
                    <span class="text-blue-700">No Form Checklist:</span>
                    <span class="ml-2 font-medium text-blue-900">{{ qcDetailData?.no_form_checklist || 'N/A' }}</span>
                  </div>
                  <div>
                    <span class="text-blue-700">Kategori:</span>
                    <span class="ml-2 font-medium text-blue-900">{{ qcDetailData?.kategori || 'N/A' }}</span>
                  </div>
                </div>
              </div>

              <!-- Re-QC Badge if applicable -->
              <div v-if="qcDetailData?.is_reqc" class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <div class="flex items-center gap-2 text-orange-800">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                  </svg>
                  <span class="font-semibold">Material Re-QC ({{ qcDetailData?.reqc_count }}x)</span>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end mt-6 pt-6 border-t border-gray-200">
              <button @click="closeQCDetailModal"
                class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Tutup
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
import Pagination from '@/Components/Pagination.vue'
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import axios from 'axios'
import { Html5Qrcode } from 'html5-qrcode'
import QRCode from 'qrcode';

// ... (semua variabel ref, let qrScanner, dan props lainnya)
const page = usePage()
const canCreateQC = computed(() => page.props.permissions.includes('qc.create'))

let qrScanner = null

const props = defineProps({
  itemsToQC: Object, // Changed to Object for Pagination
  filters: Object
})

// Custom debounce function
function customDebounce(func, wait) {
  let timeout;
  return function(...args) {
    const context = this;
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(context, args), wait);
  };
}

// Filter Refs
const search = ref(props.filters?.search || '')
const dateStart = ref(props.filters?.date_start || '')
const dateEnd = ref(props.filters?.date_end || '')
const limit = ref(props.filters?.limit || '10')
const status = ref(props.filters?.status || '') // Default: All

// Update Params Logic
const updateParams = customDebounce(() => {
  router.get('/transaction/quality-control', {
    search: search.value,
    date_start: dateStart.value,
    date_end: dateEnd.value,
    limit: limit.value,
    status: status.value
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  })
}, 500)

watch([search, dateStart, dateEnd, limit, status], () => {
  updateParams()
})

// Data reaktif
const showDetailModal = ref(false)
const showQCDetail = ref(false)
const qcDetailData = ref(null) as any
const showMovementHistory = ref(false)
const showQCModal = ref(false)
const showQRScanner = ref(false)
const selectedItem = ref(null) as any
const manualQRInput = ref('')

// Camera related
const videoElement = ref(null)
const cameraStream = ref(null)
const cameraReady = ref(false)
const cameraError = ref('')
const scanResult = ref('')
const scanInterval = ref(null)
const isSubmittingQC = ref(false)

// QR Scanner variables
const html5QrCode = ref(null)
const scannerStatus = ref('')
const scanError = ref('')

// Form QC data
const qcForm = ref({
  incoming_item_id: null,
  noFormChecklist: '',
  date: '',
  noPo: '',
  noSuratJalan: '',
  kodeItem: '',
  namaMaterial: '',
  batchLot: '',
  supplier: '',
  kategori: '',
  qtySample: 0,
  totalIncoming: '',
  uom: '',
  noKendaraan: '',
  namaDriver: '',
  defectCount: 0,
  catatanQC: '',
  hasilQC: '',
  photos: [],
  newExpDate: '' // For Re-QC PASS - new expired date
})

// Computed
const isQCFormValid = computed(() => {
  // Validasi QTY Sample harus diisi dan tidak melebihi total incoming
  const qtySample = parseFloat(qcForm.value.qtySample) || 0;
  const totalIncoming = parseFloat(qcForm.value.totalIncoming) || 0;

  return qcForm.value.hasilQC &&
    qcForm.value.kategori &&
    (qtySample >= 0 && qtySample <= totalIncoming); // Qty Sample harus valid
})

const formatInteger = (value: number | string | null | undefined): number | string => {
  if (value === null || value === undefined || value === '') {
    return '';
  }
  // Mengubah ke float dan tampilkan dengan 2 desimal
  const numValue = parseFloat(value as string);
  // Jika angkanya bulat (tidak ada desimal), tampilkan tanpa desimal
  // Jika ada desimal, tampilkan dengan maksimal 2 digit desimal
  return numValue % 1 === 0 ? numValue.toFixed(0) : numValue.toFixed(2);
}

// Format quantity with max 4 decimal places
const formatQty = (qty: number | undefined): string => {
  if (qty === null || qty === undefined) return '0'
  const num = parseFloat(qty.toString())
  if (isNaN(num)) return '0'
  
  // If it's a whole number, show as is
  if (num === Math.floor(num)) return num.toString()
  
  // For decimals, keep up to 4 decimal places, remove trailing zeros
  return num.toFixed(4).replace(/\.?0+$/, '')
}

const getDisplayQtyReceived = (item: any): number => {
    // Jika status masih "To QC", Qty Diambil (Sample) seharusnya 0
    if (item.statusQC === 'To QC') {
        return 0;
    }
    // Jika sudah PASS/REJECT, tampilkan Qty Sample yang sudah diambil (data dari backend)
    return item.qcSampleQty ?? 0;
}

const scannerStatusClass = computed(() => {
  if (scannerStatus.value.includes('Aktif')) return 'bg-green-500 text-white'
  if (scannerStatus.value.includes('Error')) return 'bg-red-500 text-white'
  return 'bg-yellow-500 text-white'
})

const generateQRDataURL = async (qrContent: string): Promise<string> => {
  try {
    const url = await QRCode.toDataURL(qrContent, {
      width: 150, // Sesuaikan ukuran cetak
      margin: 1,
      errorCorrectionLevel: 'M',
    });
    return url;
  } catch (err) {
    console.error("Gagal generate QR Data URL:", err);
    return ""; // Mengembalikan string kosong jika gagal
  }
};

const formatDateOnly = (dateString: string | Date | null) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    // Format menjadi D/M/YYYY (cth: 1/2/2025)
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'numeric',
        year: 'numeric'
    }).replace(/\./g, '/');
}

const LOGO_URL = "https://karir-production.nos.jkt-1.neo.id/logos/05/6980305/logo_gondowangi.png";

const isProcessing = ref(false);

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

const showQCDetailModal = async (item) => {
  try {
    // Fetch full QC details including user info from backend
    const response = await axios.get(`/transaction/quality-control/${item.id}/detail`)
    qcDetailData.value = response.data
    showQCDetail.value = true
  } catch (error) {
    console.error('Error fetching QC details:', error)
    alert('Gagal mengambil detail QC')
  }
}

const closeQCDetailModal = () => {
  showQCDetail.value = false
  qcDetailData.value = null
  showMovementHistory.value = false
}

const getMovementTypeLabel = (type: string): string => {
  const labels: Record<string, string> = {
    'QC_SAMPLING': 'Pengambilan Sampel QC',
    'STATUS_CHANGE': 'Perubahan Status',
    'RESERVATION': 'Reservasi',
    'PICKING': 'Picking',
    'PUTAWAY': 'Putaway',
    'BIN_TRANSFER': 'Transfer Bin',
    'RETURN': 'Retur',
    'ADJUSTMENT': 'Penyesuaian'
  }
  return labels[type] || type
}

const getMovementTypeClass = (type: string): string => {
  const classes: Record<string, string> = {
    'QC_SAMPLING': 'bg-purple-100 text-purple-800',
    'STATUS_CHANGE': 'bg-blue-100 text-blue-800',
    'RESERVATION': 'bg-yellow-100 text-yellow-800',
    'PICKING': 'bg-orange-100 text-orange-800',
    'PUTAWAY': 'bg-green-100 text-green-800',
    'BIN_TRANSFER': 'bg-indigo-100 text-indigo-800',
    'RETURN': 'bg-red-100 text-red-800',
    'ADJUSTMENT': 'bg-gray-100 text-gray-800'
  }
  return classes[type] || 'bg-gray-100 text-gray-800'
}

const formatNumber = (value: number | string | null | undefined): string => {
  if (value === null || value === undefined) return '0'
  const num = typeof value === 'string' ? parseFloat(value) : value
  if (isNaN(num)) return '0'
  // Format with thousand separator and up to 2 decimal places
  return num.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 })
}

const closeDetailModal = () => {
  showDetailModal.value = false
  selectedItem.value = null
}

const openQCModal = () => {
  if (!selectedItem.value) return

  const today = new Date().toISOString().slice(0, 10).replace(/-/g, '')
  const checklistNumber = `QC/${today}/XXXX`

  qcForm.value = {
    incoming_item_id: selectedItem.value.id,
    noFormChecklist: checklistNumber,
    date: new Date().toISOString().slice(0, 16),
    noPo: selectedItem.value.noPo,
    noSuratJalan: selectedItem.value.noSuratJalan,
    kodeItem: selectedItem.value.kodeItem,
    namaMaterial: selectedItem.value.namaMaterial,
    batchLot: selectedItem.value.batchLot || '',
    supplier: selectedItem.value.supplier,
    kategori: selectedItem.value.kategori,
    qtySample: 0,
    totalIncoming: Math.floor(selectedItem.value.qtyReceived),
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
    batchLot: '',
    supplier: '',
    kategori: '',
    qtySample: 0,
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
    alert('Mohon lengkapi semua field yang diperlukan dengan benar')
    return
  }

  // Set loading state to prevent duplicate submissions
  isSubmittingQC.value = true

  const formData = new FormData()
  formData.append('incoming_item_id', qcForm.value.incoming_item_id)
  formData.append('reference', qcForm.value.reference || '')
  formData.append('kategori', qcForm.value.kategori)
  formData.append('qty_sample', qcForm.value.qtySample || 0)
  formData.append('defect_count', qcForm.value.defectCount || 0)
  formData.append('catatan_qc', qcForm.value.catatanQC || '')
  formData.append('hasil_qc', qcForm.value.hasilQC)
  
  // \u2b50 Send new expired date for Re-QC PASS
  if (selectedItem.value?.is_reqc && qcForm.value.hasilQC === 'PASS' && qcForm.value.newExpDate) {
    formData.append('new_exp_date', qcForm.value.newExpDate)
  }

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
      isSubmittingQC.value = false
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
      isSubmittingQC.value = false

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
      isSubmittingQC.value = false
    }
  })
}

// ... (openQRScanner, startHtml5QrcodeScanner, loadQRScanner, closeQRScanner, checkCameraPermission, startCamera, startQRScanningAlternative, startQRScanning, stopCamera, processQRCode tidak berubah)
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

// let isProcessing = ref(false)

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

// START: Fungsi yang Diperbarui dan Ditambahkan

const printReturnSlip = (item) => {
  // Memastikan item.catatanQC digunakan, jika tidak ada, tampilkan pesan default.
  const catatanReject = item.catatanQC || 'Tidak ada catatan reject spesifik yang tersedia.';

  const printWindow = window.open('', '_blank')

  printWindow.document.write(`
        <html>
        <head>
            <title>Return Slip - ${item.kodeItem}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; background-color: #ffe6e6; }
                h2 { color: #ff0000; } /* Merah untuk penekanan */
                .content { margin: 20px 0; }
                .info-row { display: flex; justify-content: space-between; margin: 10px 0; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th, td { border: 1px solid #000; padding: 8px; text-align: left; }
                .reject-note { background-color: #ffe6e6; border: 1px solid #ff0000; padding: 10px; margin: 20px 0; }
                .signature { margin-top: 40px; display: flex; justify-content: space-between; }
                .signature div { text-align: center; }
                .signature-line { border-top: 1px solid #000; width: 150px; margin-top: 40px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>RETURN SLIP (SLIP PENGEMBALIAN)</h2>
                <p>No: RTN/${new Date().toISOString().slice(0, 10).replace(/-/g, '')}/${String(item.id).padStart(4, '0')}</p>
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
                <table>
                    <tr style="background-color: #f0f0f0;">
                        <th>Kode Item</th>
                        <th>Nama Material</th>
                        <th>Quantity Reject</th>
                        <th>UoM</th>
                        <th>Catatan Reject</th>
                    </tr>
                    <tr>
                        <td>${item.kodeItem}</td>
                        <td>${item.namaMaterial}</td>
                        <td style="text-align: center;">${item.qtyReceived}</td>
                        <td style="text-align: center;">${item.uom}</td>
                        <td style="color: red; font-weight: bold;">${catatanReject}</td> 
                    </tr>
                </table>
            </div>
            <div class="reject-note">
                **PERHATIAN:** Material ini ditolak (REJECT) berdasarkan hasil Quality Control dan harus dikembalikan.
            </div>
            <div class="signature">
                <div><p>QC By:</p><div class="signature-line"></div><p>Quality Control</p></div>
                <div><p>Known By:</p><div class="signature-line"></div><p>Supplier / Driver</p></div>
            </div>
        </body>
        </html>
    `)

  printWindow.document.close()
  printWindow.focus()
  setTimeout(() => { printWindow.print(); printWindow.close() }, 500)
}

const generateLabelHTML = (isRejected: boolean, item: any, qrDataURL: string, labelIndex: number, totalLabels: number) => {
    // Penentuan Status dan Warna
    const statusText = isRejected ? 'R E J E C T' : 'R E L E A S E D';
    const borderColor = isRejected ? '#171917' : '#171917';
    const textColor = isRejected ? 'red' : 'green';

    const noLot = item.noLot || item.batchLot || 'N/A';
    const expDatePrint = formatDateOnly(item.expDate || 'N/A');
    const tanggalTerimaPrint = formatDateOnly(item.tanggalTerima || 'N/A');
    const itemCodeAndName = `[${item.kodeItem || 'N/A'}] ${item.namaMaterial || 'N/A'}`;
    const createdBy = usePage().props.auth?.user?.name || 'Logistik';

    // QTY Per Wadah
    const qtyUnitPerWadah = item.qtyUnitPerWadah || 0; 
    const jmlBarangDisplay = `${formatInteger(qtyUnitPerWadah)} ${item.uom || 'PCS'}`;

    // Detail Wadah
    const qtyWadahTotal = item.qtyWadah || 1;
    const wadahDetail = qtyWadahTotal > 0 ? `${qtyUnitPerWadah} x ${qtyWadahTotal} box` : '';
    
    // Konten QR Code (untuk ditampilkan di dalam label)
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

const createPrintDocument = async (item: any, isRejected: boolean) => {
    const totalWadah = item.qtyWadah || 1;
    const labelsPerPage = 6;
    let pagesHTML = '';
    const status = isRejected ? 'REJECT' : 'RELEASED';
    
    if (totalWadah <= 0) {
        throw new Error("Jumlah wadah tidak valid untuk dicetak.");
    }

    for (let i = 0; i < totalWadah; i += labelsPerPage) {
        let labelsInPageHTML = '';
        const pageLabelsCount = Math.min(labelsPerPage, totalWadah - i);

        for (let j = 0; j < pageLabelsCount; j++) {
            const currentWadahIndex = i + j + 1; // 1, 2, 3, ...
            
            // 1. Generate QR Data URL (menggunakan QTY per wadah untuk kode QR)
            const qrContent = item.qrCode || `${item.batchLot}|${item.kodeItem}|${status}|${item.qtyUnitPerWadah}|${item.expDate}|${currentWadahIndex}/${totalWadah}`;
            const qrDataURL = await generateQRDataURL(qrContent);

            // 2. Generate HTML untuk satu label
            const labelHTML = generateLabelHTML(isRejected, item, qrDataURL, currentWadahIndex, totalWadah);
            labelsInPageHTML += labelHTML;
        }

        // Kumpulkan semua label ke dalam satu halaman dengan page break
        pagesHTML += `
            <div class="print-page" style="${i > 0 ? 'page-break-before: always;' : ''}">
                ${labelsInPageHTML}
            </div>
        `;
    }

    // 3. Gabungkan ke dalam dokumen cetak final dengan CSS
    const borderColor = isRejected ? 'red' : 'green';

    return `
        <html>
        <head>
            <title>Cetak Label QR - ${item.kodeItem} (${totalWadah} Wadah)</title>
            <style>
                @page {
                    size: A4;
                    margin: 0.5cm;
                }
                
                body {
                    font-family: 'Arial', sans-serif;
                    margin: 0;
                    padding: 0;
                    background: white;
                    font-size: 10px;
                }
                
                .print-page {
                    width: 20cm; 
                    height: 28.7cm; 
                    margin: 0.5cm auto; 
                    box-sizing: border-box;
                    display: flex;
                    flex-wrap: wrap;
                    align-content: flex-start;
                    justify-content: space-between; 
                }

                /* ===========================================
                    GAYA VISUAL (diselaraskan dengan Penerimaan Barang)
                    =========================================== */

                .label-wrapper {
                    width: 9.9cm; /* 2 kolom di A4 */
                    height: calc(33.33% - 0.4cm); /* 3 baris di A4 */
                    padding: 0; 
                    box-sizing: border-box;
                    margin-bottom: 0.4cm; 
                    margin-right: 0.1cm;
                    margin-left: 0.1cm;
                }
                
                .label-container {
                    width: 100%; 
                    height: 100%; 
                    border: 2px solid ${borderColor}; /* Warna border sesuai status */
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
                    border-bottom: 2px solid ${borderColor};
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
                    border-top: 2px solid ${borderColor};
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
                        margin: 0 auto;
                    }
                    .print-page:last-child { page-break-after: avoid; }
                    body { 
                        -webkit-print-color-adjust: exact; 
                        print-color-adjust: exact;
                        display: block;
                        margin: 0;
                    }
                    /* Menyesuaikan border/padding di mode print */
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
    `;
}

const printReleaseQRLabel = async (item) => {
    try {
        const htmlContent = await createPrintDocument(item, false); // false = RELEASED
        
        const printWindow = window.open('', '_blank');
        if (printWindow) {
            printWindow.document.write(htmlContent);
            printWindow.document.close();
            printWindow.focus();
        } else {
            alert('Gagal membuka jendela cetak. Pastikan pop-up tidak diblokir.');
        }

    } catch (error) {
        console.error("Error saat mencetak label RELEASED:", error);
        alert(error.message || "Terjadi kesalahan saat mencetak label.");
    }
};

const printRejectQRLabel = async (item) => {
    try {
        const htmlContent = await createPrintDocument(item, true); // true = REJECTED
        
        const printWindow = window.open('', '_blank');
        if (printWindow) {
            printWindow.document.write(htmlContent);
            printWindow.document.close();
            printWindow.focus();
        } else {
            alert('Gagal membuka jendela cetak. Pastikan pop-up tidak diblokir.');
        }

    } catch (error) {
        console.error("Error saat mencetak label REJECT:", error);
        alert(error.message || "Terjadi kesalahan saat mencetak label.");
    }
};

// Mengganti nama fungsi lama (printQRLabel)
const printQRLabel = printReleaseQRLabel;

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