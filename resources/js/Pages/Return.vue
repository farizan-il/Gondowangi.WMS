<template>
  <AppLayout title="Riwayat Aktivitas">
    <div class="min-h-screen transition-colors duration-300">
      <div class="min-h-screen p-2 sm:p-4 md:p-6">
        <!-- Header -->
        <div class="mb-4 sm:mb-6">
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
              <h1 class="text-2xl sm:text-1xl font-bold text-gray-900">Return (Supplier / Production)</h1>
              <p class="text-sm sm:text-base text-gray-600 mt-1">Kelola return barang ke supplier dan dari produksi</p>
            </div>
            <div class="flex items-center gap-2 sm:gap-4 w-full sm:w-auto">
              <button @click="showAddModal = true"
                class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded-lg flex items-center gap-2 transition-colors w-full sm:w-auto justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span class="hidden sm:inline">Buat Return</span>
                <span class="sm:hidden">Return</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Filter dan Search -->
        <div class="bg-white rounded-lg shadow-sm p-3 sm:p-4 mb-4 sm:mb-6 border border-gray-200">
          <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
            <div class="w-full sm:flex-1">
              <input v-model="searchQuery" type="text" placeholder="Cari Return Number, Supplier, Item..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 sm:gap-4">
              <select v-model="typeFilter"
                class="px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="">Semua Jenis</option>
                <option value="Supplier">Return Supplier</option>
                <option value="Production">Return Production</option>
              </select>
              <select v-model="statusFilter"
                class="px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="">Semua Status</option>
                <option value="Draft">Draft</option>
                <option value="Submitted">Submitted</option>
                <option value="Completed">Completed</option>
              </select>
              <select v-model="reasonFilter"
                class="px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 text-sm col-span-2 sm:col-span-1">
                <option value="">Semua Reason</option>
                <option value="QC Reject">QC Reject</option>
                <option value="Kadaluarsa">Kadaluarsa</option>
                <option value="Rusak">Rusak</option>
                <option value="Kelebihan Produksi">Kelebihan Produksi</option>
                <option value="Salah Kirim">Salah Kirim</option>
                <option value="Lainnya">Lainnya</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Tabel Returns -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Return Number</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier / Dept</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Item</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Material</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lot/Batch</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="returnItem in filteredReturns" :key="returnItem.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ returnItem.returnNumber }}</div>
                    <div class="text-sm text-gray-500">{{ returnItem.type }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ formatDate(returnItem.date) }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ returnItem.supplier }}</div>
                  </td>

                  <td class="px-6 py-4 whitespace-nowrap">
                    <div v-if="returnItem.items.length > 1" class="text-sm font-bold text-blue-600 italic">
                      Multiple Items ({{ returnItem.items.length }})
                    </div>
                    <div v-else class="text-sm font-mono text-gray-900">
                      {{ returnItem.items[0]?.item_code || '-' }}
                    </div>
                  </td>

                  <td class="px-6 py-4">
                    <div v-if="returnItem.items.length > 1" class="text-sm text-gray-400 italic">
                      - Lihat Detail -
                    </div>
                    <div v-else class="text-sm text-gray-900">
                      {{ returnItem.items[0]?.item_name || '-' }}
                    </div>
                  </td>

                  <td class="px-6 py-4 whitespace-nowrap">
                    <div v-if="returnItem.items.length > 1" class="text-sm text-gray-400">
                      -
                    </div>
                    <div v-else class="text-sm font-mono text-gray-900">
                      {{ returnItem.items[0]?.batch_lot || '-' }}
                    </div>
                  </td>

                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                        {{ formatQty(returnItem.total_qty) }} 
                        <span v-if="returnItem.items.length === 1">{{ returnItem.items[0]?.uom }}</span>
                    </div>
                  </td>


                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getReasonClass(returnItem.items[0]?.reason)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                        {{ returnItem.items[0]?.reason || '-' }}
                        <span v-if="returnItem.items.length > 1" class="ml-1 text-[10px] opacity-75">
                            (+{{ returnItem.items.length - 1 }})
                        </span>
                    </span>
                  </td>

                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusClass(returnItem.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ returnItem.status }}
                    </span>
                  </td>

                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                    <button 
                      @click="viewDetail(returnItem)"
                      class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-2 py-1 rounded transition-colors"
                    >
                      Detail
                    </button>
                    <button 
                          v-if="returnItem.status === 'Pending Approval'"
                          @click="openApproveModal(returnItem)"
                          class="text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-2 py-1 rounded transition-colors"
                    >
                          Approve
                    </button>
                    <button 
                      @click="printSlip(returnItem)"
                      class="text-purple-600 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 px-2 py-1 rounded transition-colors"
                    >
                      Cetak Slip
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Modal Add Return -->
        <div v-if="showAddModal" class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: rgba(43, 51, 63, 0.67);">
          <div class="bg-white rounded-lg p-6 w-full max-w-7xl max-h-screen overflow-y-auto border border-gray-200">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-xl font-semibold text-gray-900">Buat Return Baru</h3>
              <button 
                @click="closeAddModal"
                class="text-gray-400 hover:text-gray-600"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Form Return -->
            <div class="space-y-6">
              <!-- Jenis Return -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Jenis Return</label>
                <div class="flex gap-6">
                  <label class="flex items-center">
                    <input 
                      v-model="newReturn.type" 
                      type="radio" 
                      value="Production"
                      class="text-blue-600 focus:ring-blue-500"
                    >
                    <span class="ml-2 text-gray-900">Return dari Produksi</span>
                  </label>
                  <label class="flex items-center">
                    <input 
                      v-model="newReturn.type" 
                      type="radio" 
                      value="Rejected Material"
                      class="text-blue-600 focus:ring-blue-500"
                    >
                    <span class="ml-2 text-gray-900">Return Material Reject</span>
                  </label>
                </div>
              </div>

              <!-- PDF Upload Section for Production Return -->
              <div v-if="newReturn.type === 'Production'" class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
                <label class="block text-sm font-medium text-blue-900 mb-2">Upload PDF ERP (Return dari Produksi)</label>
                <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                    <input 
                        type="file" 
                        accept="application/pdf"
                        @change="handleFileUpload"
                        class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-blue-100 file:text-blue-700
                            hover:file:bg-blue-200"
                    />
                    <button 
                        @click="processErpPdf" 
                        :disabled="!erpPdfFile || isProcessingErp"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 whitespace-nowrap min-w-[140px]"
                    >
                        <svg v-if="isProcessingErp" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span v-if="isProcessingErp">Memproses...</span>
                        <span v-else>Proses PDF</span>
                    </button>
                </div>
                <p class="text-xs text-blue-600 mt-2">Upload file PDF "Internal Shipment" (Return) untuk mengisi form otomatis.</p>
              </div>

              <!-- Header Info -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Return</label>
                  <input 
                    v-model="newReturn.date"
                    type="date" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ newReturn.type === 'Production' ? 'Departemen Asal' : 'Supplier' }}
                  </label>
                  <!-- For Rejected Material: Readonly (auto-filled from shipment) -->
                  <input 
                    v-if="newReturn.type === 'Rejected Material'"
                    v-model="newReturn.supplier"
                    readonly
                    type="text"
                    placeholder="Pilih No Shipment terlebih dahulu"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed"
                  >
                  <!-- For Production: Readonly dept -->
                  <input 
                    v-else-if="newReturn.type === 'Production'"
                    :value="userDept"
                    readonly
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed"
                  >
                  <!-- For Supplier: Select dropdown -->
                  <select 
                    v-else
                    v-model="newReturn.supplier" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">Pilih Supplier</option>
                    <option v-for="sup in suppliers" :key="sup.nama_supplier" :value="sup.nama_supplier">
                      {{ sup.nama_supplier }}
                    </option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    No Shipment {{ newReturn.type == 'Production' ? '/ No Return' : '' }}
                  </label>
                  
                  <!-- For Production: Input field (readonly after PDF upload) -->
                  <input 
                    v-if="newReturn.type === 'Production'"
                    v-model="newReturn.shipmentNo"
                    type="text"
                    placeholder="Upload PDF untuk mengisi otomatis"
                    readonly
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 cursor-not-allowed focus:ring-2 focus:ring-blue-500"
                  >
                  
                  <!-- For Supplier & Rejected: Keep Select dropdown -->
                  <select 
                    v-else
                    v-model="newReturn.shipmentNo" 
                    @change="handleShipmentNoChange"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">Pilih</option>
                    <template v-if="newReturn.type === 'Supplier'">
                        <option v-for="ship in rejectedShipments" :key="ship.incoming_number" :value="ship.incoming_number">
                            {{ ship.incoming_number }} {{ ship.no_surat_jalan ? `(${ship.no_surat_jalan})` : '' }}
                        </option>
                    </template>
                    <template v-else-if="newReturn.type === 'Rejected Material'">
                        <option v-for="ship in rejectedShipments" :key="ship.incoming_number" :value="ship.incoming_number">
                            {{ ship.incoming_number }}
                        </option>
                    </template>
                  </select>
                </div>
              </div>

              <!-- Items Table -->
              <div>
                <div class="flex justify-between items-center mb-4">
                  <h4 class="text-lg font-medium text-gray-900">Daftar Item Return</h4>
                  <button 
                    @click="addItem"
                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors"
                  >
                    + Tambah Item
                  </button>
                </div>
                
                <div class="overflow-x-auto border border-gray-300 rounded-lg">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Item</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Material</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lot/Batch</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty Return</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">UoM</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                      <tr v-for="(item, index) in newReturn.items" :key="index">
                        
                        <td class="px-4 py-3 text-sm font-mono text-gray-900">{{ item.itemCode }}</td>
                        
                        <td class="px-4 py-3 text-sm text-gray-900">{{ item.itemName }}</td>
                        
                        <td class="px-4 py-3 text-sm font-mono text-gray-900">{{ item.lotBatch }}</td>
                        
                        <td class="px-4 py-3 text-sm text-gray-900">{{ item.qty }}</td>
                        
                        <td class="px-4 py-3 text-sm text-gray-900">{{ item.uom }}</td>
                        
                        <td class="px-4 py-3">
                          <span :class="getReasonClass(item.reason)" class="px-2 py-1 text-xs font-medium rounded-full">
                            {{ item.reason }}
                          </span>
                        </td>
                        
                        <td class="px-4 py-3">
                          <button 
                            @click="removeItem(index)"
                            class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded transition-colors"
                            title="Hapus item"
                          >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                          </button>
                        </td>
                        
                      </tr>
                      
                      <tr v-if="!newReturn.items || newReturn.items.length === 0">
                          <td colspan="7" class="px-4 py-3 text-center text-gray-500">Tidak ada item. Klik "Tambah Item" untuk menambah.</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <button 
                  @click="closeAddModal"
                  class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  Batal
                </button>
                <button 
                  @click="saveReturn"
                  :disabled="!canSaveReturn"
                  :class="canSaveReturn 
                    ? 'bg-blue-600 hover:bg-blue-700 text-white' 
                    : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                  class="px-4 py-2 rounded-lg transition-colors"
                >
                  Simpan Return
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Detail Return -->
        <div v-if="showDetailModal && selectedReturn" class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: rgba(43, 51, 63, 0.67);">
          <div class="bg-white rounded-lg p-6 w-full max-w-6xl max-h-screen overflow-y-auto border border-gray-200">
            <div class="flex justify-between items-center mb-6">
              <div class="flex items-center gap-4">
                <h3 class="text-xl font-semibold text-gray-900">Detail Return</h3>
                <span v-if="isEditMode" class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-medium">
                  Mode Edit Aktif
                </span>
              </div>
              <button 
                @click="closeDetailModal"
                class="text-gray-400 hover:text-gray-600"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Return Header Info -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-200">
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-600">Return Number</label>
                  <p class="text-lg font-semibold text-gray-900">{{ selectedReturn.returnNumber }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-600">Jenis Return</label>
                  <p class="text-lg text-gray-900">{{ selectedReturn.type }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-600">Tanggal</label>
                  <p class="text-lg text-gray-900">{{ formatDate(selectedReturn.date) }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-600">Status</label>
                  <span :class="getStatusClass(selectedReturn.status)" class="px-2 py-1 text-sm font-semibold rounded-full">
                    {{ selectedReturn.status }}
                  </span>
                </div>
              </div>
              <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-600">
                    {{ selectedReturn.type === 'Supplier' ? 'Supplier' : 'Dept Asal' }}
                  </label>
                  <p class="text-lg text-gray-900">{{ selectedReturn.supplier }}</p>
                </div>
                <div v-if="selectedReturn.shipmentNo">
                  <label class="block text-sm font-medium text-gray-600">No Shipment/Reservasi</label>
                  <p class="text-lg font-mono text-gray-900">{{ selectedReturn.shipmentNo }}</p>
                </div>
              </div>
            </div>

            <!-- Items Detail -->
            <div class="overflow-x-auto border border-gray-300 rounded-lg">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Item</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Material</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lot/Batch</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">UoM</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <tr v-for="(item, index) in (isEditMode ? editableItems : selectedReturn.items)" :key="index">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ index + 1 }}</td>
                    <td class="px-4 py-3 text-sm font-mono text-gray-900">{{ item.item_code }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.item_name }}</td>
                    <td class="px-4 py-3 text-sm font-mono text-gray-900">{{ item.batch_lot }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                      <input 
                        v-if="isEditMode"
                        v-model.number="editableItems[index].qty"
                        type="number"
                        step="0.01"
                        min="0.01"
                        class="w-24 px-2 py-1 border rounded focus:ring-2 focus:ring-blue-500"
                        :class="editableItems[index].qty !== selectedReturn.items[index].qty ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
                      >
                      <span v-else>{{ formatQty(item.qty) }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.uom }}</td>
                    <td class="px-4 py-3">
                      <span :class="getReasonClass(item.reason)" class="px-2 py-1 text-xs font-medium rounded-full">
                        {{ item.reason }}
                      </span>
                    </td>
                  </tr>
                  <tr v-if="!selectedReturn.items || selectedReturn.items.length === 0">
                    <td colspan="7" class="px-4 py-3 text-center text-gray-500">Tidak ada item detail.</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200">
              <div>
                <button 
                  v-if="!isEditMode && canEdit(selectedReturn)"
                  @click="enableEditMode"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                  Edit Qty
                </button>
              </div>
              
              <div class="flex gap-3">
                <template v-if="isEditMode">
                  <button 
                    @click="cancelEdit"
                    class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                  >
                    Batal
                  </button>
                  <button 
                    @click="saveChanges"
                    :disabled="isSaving"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 flex items-center gap-2"
                  >
                    <svg v-if="isSaving" class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}
                  </button>
                </template>
                <template v-else>
                  <button 
                    @click="closeDetailModal"
                    class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                  >
                    Tutup
                  </button>
                  <button 
                    @click="printSlip(selectedReturn)"
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
                  >
                    Cetak Slip Return
                  </button>
                </template>
              </div>
            </div>
          </div>
        </div>

        <!-- Success/Error Messages -->
        <div v-if="message" :class="message.type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'" class="fixed top-4 right-4 border rounded-lg p-4 shadow-lg" style="z-index: 1000;">
          <div class="flex items-center gap-2">
            <svg v-if="message.type === 'success'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            {{ message.text }}
          </div>
        </div>

        <!-- Modal Approval -->
        <div v-if="showApproveModal" class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: rgba(0,0,0,0.5);">
             <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-xl">
                <h3 class="text-xl font-bold mb-4 text-gray-800">Approve Return</h3>
                <p class="mb-4 text-gray-600">Konfirmasi persetujuan untuk Return <strong>{{ approveForm.return_number }}</strong>?</p>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Bin (Karantina)</label>
                    <select v-model="approveForm.target_bin_id" class="w-full border rounded px-3 py-2">
                        <option value="QRT-HALAL">QRT-HALAL</option>
                        <option value="QRT-HALAL-AC">QRT-HALAL-AC</option>
                        <option value="QRT-NON HALAL">QRT-NON HALAL</option>
                        <option value="REJECT">REJECT</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button @click="closeApproveModal" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Batal</button>
                    <button 
                        @click="submitApprove" 
                        :disabled="isApproving"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50 flex items-center gap-2"
                    >
                        <svg v-if="isApproving" class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        {{ isApproving ? 'Memproses...' : 'Approve & Masukkan Stock' }}
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
import axios from 'axios'
import { ref, computed, onMounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'

interface ReturnDetailItem {
  id?: number
  item_code: string
  item_name: string
  batch_lot: string
  qty: number
  uom: string
  reason: string
}

// Types
interface ReturnItem {
  id: string
  returnNumber: string
  date: string
  type: 'Supplier' | 'Production' | 'Rejected Material'
  supplier: string
  shipmentNo?: string
  itemCode: string
  itemName: string
  lotBatch: string
  qty: number
  uom: string
  reason: string
  status: 'Draft' | 'Submitted' | 'Completed' | 'Pending Approval' | 'Approved'
  items: ReturnDetailItem[] 
  total_qty: number
}

interface NewReturnItem {
  itemCode: string
  itemName: string
  lotBatch: string
  qty: number
  uom: string
  reason: string
  originalQty?: number
  id?: number | null // stock id helper
  category?: string
}

const props = defineProps({
  suppliers: Array,
  shipments: Array,
  rejectedShipments: Array,
  userDept: String,
  initialReturns: Array
})

// Reactive data
const isDarkMode = ref(false)
const returns = ref<ReturnItem[]>([])
const searchQuery = ref('')
const typeFilter = ref('')
const statusFilter = ref('')
const reasonFilter = ref('')

// Modals
const showAddModal = ref(false)
const showDetailModal = ref(false)

const selectedReturn = ref<ReturnItem | null>(null)
const deptReservations = ref<string[]>([]) 

// PDF Upload State
const erpPdfFile = ref<File | null>(null)
const isProcessingErp = ref(false)

// Edit Mode State
const isEditMode = ref(false)
const editableItems = ref<ReturnDetailItem[]>([])
const isSaving = ref(false)


// Form data
const newReturn = ref({
  type: 'Rejected Material', 
  date: new Date().toISOString().split('T')[0],
  supplier: '',
  shipmentNo: '',
  items: [] as NewReturnItem[]
})

watch(() => newReturn.value.type, (newType) => {
    if (newType === 'Production') {
        newReturn.value.supplier = props.userDept || '';
        fetchDeptReservations();
    } else {
        newReturn.value.supplier = '';
        newReturn.value.shipmentNo = '';
        newReturn.value.items = [];
    }
});

const handleShipmentNoChange = () => {
    if (newReturn.value.type === 'Production') {
        fetchReservationDetails();
    } else if (newReturn.value.type === 'Rejected Material') {
        fetchRejectedShipmentDetails();
    } else if (newReturn.value.type === 'Supplier') {
        fetchSupplierShipmentDetails();
    }
};

const message = ref<{ type: 'success' | 'error', text: string } | null>(null)

// ... computed properties ...
const filteredReturns = computed(() => {
  return returns.value.filter(item => {
    const matchesSearch = !searchQuery.value ||
      item.returnNumber.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.supplier.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.itemCode.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.itemName.toLowerCase().includes(searchQuery.value.toLowerCase())

    const matchesType = !typeFilter.value || item.type === typeFilter.value
    const matchesStatus = !statusFilter.value || item.status === statusFilter.value
    const matchesReason = !reasonFilter.value || item.reason === reasonFilter.value

    return matchesSearch && matchesType && matchesStatus && matchesReason
  })
})

const totalReturns = computed(() => returns.value.length)
const supplierReturns = computed(() => returns.value.filter(r => r.type === 'Supplier').length)
const productionReturns = computed(() => returns.value.filter(r => r.type === 'Production').length)
const pendingReturns = computed(() => returns.value.filter(r => r.status !== 'Completed').length)

const canSaveReturn = computed(() => {
  const hasBasicInfo = newReturn.value.type && newReturn.value.date
  
  if (newReturn.value.type === 'Supplier' && !newReturn.value.supplier) return false

  const hasValidItems = newReturn.value.items.length > 0 &&
    newReturn.value.items.every(item =>
      item.itemCode && item.itemName && item.lotBatch && item.qty > 0 && item.uom && item.reason
    )
  return hasBasicInfo && hasValidItems
})

// Methods
const fetchDeptReservations = async () => {
    try {
        const response = await axios.get('/transaction/return/dept-reservations');
        deptReservations.value = response.data;
    } catch (e) {
        console.error('Error fetching department reservations', e);
    }
}

const fetchReservationDetails = async () => {
    if (!newReturn.value.shipmentNo) return;
    
    try {
        const response = await axios.get('/transaction/return/reservation-details', {
            params: { no: newReturn.value.shipmentNo }
        });
        const items = response.data;
        
        newReturn.value.items = items.map((item: any) => ({
          itemCode: item.item_code,
          itemName: item.item_name,
          lotBatch: item.batch_lot,
          qty: 0,
          uom: item.uom,
          reason: 'Kelebihan Produksi',
          category: item.category,
      }));
        
    } catch (e) {
        console.error('Error fetching reservation details', e);
        alert('Gagal mengambil detail reservasi.');
    }
}

const fetchRejectedShipmentDetails = async () => {
    if (!newReturn.value.shipmentNo) return;
    
    try {
        const response = await axios.get('/transaction/return/rejected-shipment-details', {
            params: { no: newReturn.value.shipmentNo }
        });
        const items = response.data;
        
        if (items.length > 0) {
            newReturn.value.supplier = items[0].supplier_name;
        }

        newReturn.value.items = items.map((item: any) => ({
            id: item.id,
            itemCode: item.item_code,
            itemName: item.item_name,
            lotBatch: item.batch_lot,
            qty: item.on_hand_qty, 
            uom: item.uom,
            reason: 'QC Reject', 
            originalQty: item.on_hand_qty,
            category: item.category,
        }));
        
    } catch (e) {
        console.error('Error fetching rejected shipment details', e);
        alert('Gagal mengambil detail material reject.');
    }
}

const fetchSupplierShipmentDetails = async () => {
    if (!newReturn.value.shipmentNo) return;
    
    try {
        const response = await axios.get('/transaction/return/supplier-shipment-details', {
            params: { no: newReturn.value.shipmentNo }
        });
        const items = response.data;
        
        if (items.length > 0) {
            newReturn.value.supplier = items[0].supplier_name;
        }

        newReturn.value.items = items.map((item: any) => ({
            id: item.id,
            itemCode: item.item_code,
            itemName: item.item_name,
            lotBatch: item.batch_lot,
            qty: item.on_hand_qty, 
            uom: item.uom,
            reason: 'QC Reject', 
            originalQty: item.on_hand_qty,
            category: item.category, 
        }));
        
    } catch (e) {
        console.error('Error fetching supplier shipment details', e);
        alert('Gagal mengambil detail shipment.');
    }
}

const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value
  localStorage.setItem('darkMode', JSON.stringify(isDarkMode.value))
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const getStatusClass = (status: string) => {
  const baseClasses = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
  switch (status) {
    case 'Draft':
      return `${baseClasses} bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300`
    case 'Pending Approval':
      return `${baseClasses} bg-yellow-100 text-yellow-800 light:bg-yellow-900/20 dark:text-yellow-400`
    case 'Approved':
      return `${baseClasses} bg-green-100 text-green-800 bg-green-900/20 text-green-600`
    default:
      return `${baseClasses} bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300`
  }
}

const getReasonClass = (reason: string) => {
  const baseClasses = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
  switch (reason) {
    case 'QC Reject':
      return `${baseClasses} bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400`
    case 'Kadaluarsa':
      return `${baseClasses} bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400`
    case 'Rusak':
      return `${baseClasses} bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400`
    case 'Kelebihan Produksi':
      return `${baseClasses} text-purple-800 dark:bg-purple-900/20 dark:text-purple-400`
    case 'Salah Kirim':
      return `${baseClasses} bg-pink-100 text-pink-800 dark:bg-pink-900/20 dark:text-pink-400`
    default:
      return `${baseClasses} bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300`
  }
}

// Format Qty: Show whole numbers without decimals (2.00 → 2, 12.00 → 12), keep decimals when needed (1.19 → 1.19)
const formatQty = (qty: number): string => {
  if (qty === null || qty === undefined) return '0'
  // Check if number is whole (no decimal part)
  if (qty % 1 === 0) {
    return qty.toString() // "2", "12"
  }
  return qty.toString() // "1.19", "0.5"
}


const generateReturnNumber = () => {
  // Logic ini sekarang tidak digunakan karena mengambil dari shipmentNo
  // Namun dibiarkan untuk fallback jika diperlukan
  const now = new Date()
  const year = now.getFullYear().toString().slice(-2)
  const month = (now.getMonth() + 1).toString().padStart(2, '0')
  const day = now.getDate().toString().padStart(2, '0')
  const random = Math.floor(Math.random() * 9999).toString().padStart(4, '0')
  return `RET${year}${month}${day}${random}`
}

const addItem = () => {
  newReturn.value.items.push({
    itemCode: '',
    itemName: '',
    lotBatch: '',
    qty: 0,
    uom: 'PCS',
    reason: '',
    id: null 
  })
}

const handleFileUpload = (event: Event) => {
  const target = event.target as HTMLInputElement;
  erpPdfFile.value = target.files ? target.files[0] : null;
}

const processErpPdf = async () => {
    if (!erpPdfFile.value) return;
    isProcessingErp.value = true;
    
    try {
        const formData = new FormData();
        formData.append('erp_pdf', erpPdfFile.value);
        
        const response = await axios.post('/transaction/return/parse-pdf', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
        
        const data = response.data;
        
        // --- LOGIKA BARU: Cek Duplicate ---
        if (data.status === 'duplicate') {
            // Reset file input agar user sadar
            erpPdfFile.value = null; 
            const fileInput = document.querySelector('input[type="file"]') as HTMLInputElement;
            if (fileInput) fileInput.value = '';

            // Tampilkan Alert Duplicate
            alert(`⛔ GAGAL MEMPROSES!\n\nNo. Return: ${data.return_number} sudah ada di database (Duplicate Entry).\nSistem menolak file ini.`);
            
            isProcessingErp.value = false;
            return; // Hentikan proses
        }

        // Proses data normal
        if (data.formatted_date) {
            newReturn.value.date = data.formatted_date;
        }
        
        if (data.return_number) {
            newReturn.value.shipmentNo = data.return_number;
        }
        
        newReturn.value.items = [];
        
        // --- LOGIKA BARU: Cek Material Tidak Dikenal ---
        if (data.unknown_items && data.unknown_items.length > 0) {
            let unknownList = data.unknown_items.map((i: any) => `- ${i.item_code} (${i.description})`).join('\n');
            
            // Popup Konfirmasi
            alert(`⚠️ PERINGATAN MASTER DATA\n\nDitemukan ${data.unknown_items.length} material yang belum terdaftar di Master Data:\n\n${unknownList}\n\nSistem akan MENGHAPUS item ini dari daftar import.\nSilakan hubungi Administrator untuk mendaftarkan kode material tersebut.`);
        }

        // Masukkan hanya item yang VALID (yang dikembalikan dari backend sudah difilter)
        if (data.items.length === 0) {
            alert('Tidak ada item valid yang dapat diproses dari PDF ini.');
        } else {
            data.items.forEach((item: any) => {
                if (item.qty > 0) {
                    newReturn.value.items.push({
                        itemCode: item.item_code,
                        itemName: item.description,
                        lotBatch: item.serial_number || 'N/A', 
                        qty: item.qty,
                        uom: item.uom || 'PCS',
                        reason: 'Kelebihan Produksi',
                        id: null
                    });
                }
            });

            // Trigger fetch material lagi untuk memastikan data reaktif (opsional karena data backend sudah valid)
            newReturn.value.items.forEach((_, index) => {
               // fetchMaterial(index); // Bisa dikomentari jika backend sudah kirim nama material yang benar
            });

            alert(`✅ Berhasil memuat ${newReturn.value.items.length} item valid.\nNo Return: ${data.return_number || 'N/A'}`);
        }
        
    } catch (error: any) {
        console.error('Error processing PDF:', error);
        alert('Gagal memproses file PDF: ' + (error.response?.data?.error || error.message));
    } finally {
        isProcessingErp.value = false;
    }
}

// Material Lookup
const fetchMaterial = async (index: number) => {
  const itemCode = newReturn.value.items[index].itemCode
  if (!itemCode) return

  try {
    const response = await fetch(`/transaction/return/material/${itemCode}`)
    if (response.ok) {
        const data = await response.json()
        newReturn.value.items[index].itemName = data.nama_material
        newReturn.value.items[index].uom = data.satuan || 'PCS'
    }
  } catch (e) {
      console.error('Error fetching material', e)
  }
}

const removeItem = (index: number) => {
  if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
    newReturn.value.items.splice(index, 1)
  }
}

const closeAddModal = () => {
  showAddModal.value = false
  resetForm()
}

const resetForm = () => {
  newReturn.value = {
    type: 'Rejected Material',
    date: new Date().toISOString().split('T')[0],
    supplier: '',
    shipmentNo: '',
    items: []
  }
}

const saveReturn = () => {
  if (!canSaveReturn.value) return

  router.post('/transaction/return', newReturn.value, {
    preserveScroll: true,
    onSuccess: (page: any) => {
      if (page.props.flash?.error) {
          showMessage('error', page.props.flash.error)
          console.error("Server Error:", page.props.flash.error)
      } else {
          closeAddModal()
          showMessage('success', 'Return berhasil disimpan.')
      }
    },
    onError: (errors: any) => {
      console.error('Save Return Error:', errors)
      let msg = 'Gagal menyimpan return.'
      
      if (errors.shipmentNo) msg = errors.shipmentNo
      if (errors.date) msg = errors.date
      
      const errorKeys = Object.keys(errors);
      if (errorKeys.length > 0) {
        const firstErrorKey = errorKeys[0];
        if (firstErrorKey.startsWith('items.')) {
            msg = errors[firstErrorKey];
        } else if (!errors.shipmentNo && !errors.date) {
            msg = errors[firstErrorKey];
        }
      }
      
      showMessage('error', msg)
    }
  })
}

// ... (Sisa logic approval, viewDetail, printSlip tetap sama) ...
    // Logic Approval
    const showApproveModal = ref(false)
    const approveForm = ref({
        return_id: null,
        return_number: '',
        target_bin_id: 'QRT-HALAL' // Default
    })
    const isApproving = ref(false)

    const openApproveModal = (item) => {
        approveForm.value = {
            return_id: item.id,
            return_number: item.returnNumber,
            target_bin_id: 'QRT-HALAL'
        }
        showApproveModal.value = true
    }

    const closeApproveModal = () => {
        showApproveModal.value = false
        approveForm.value = { return_id: null, return_number: '', target_bin_id: 'QRT-HALAL' }
    }

    const submitApprove = async () => {
        if (!approveForm.value.target_bin_id) {
            alert('Pilih Target Bin!')
            return
        }

        try {
            isApproving.value = true
            const response = await fetch(route('transaction.return.approve'), { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(approveForm.value)
            })
            
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                const result = await response.json()

                if (result.success) {
                    const index = returns.value.findIndex(r => r.id === approveForm.value.return_id)
                    if (index !== -1) {
                        returns.value[index].status = 'Approved'
                    }
                    closeApproveModal()
                    window.location.reload() 
                } else {
                    alert('Gagal Approve: ' + result.message)
                }
            } else {
                const text = await response.text();
                console.error("Non-JSON Response Server Error:", text);
                alert(`Terjadi kesalahan server (${response.status}). Respon bukan JSON. Cek Console.`);
            }
        } catch (error) {
            console.error('Approve Error:', error)
            alert('Terjadi kesalahan saat approve: ' + (error instanceof Error ? error.message : String(error)))
        } finally {
            isApproving.value = false
        }
    }

const viewDetail = (returnItem: ReturnItem) => {
  selectedReturn.value = returnItem
  showDetailModal.value = true
}

const closeDetailModal = () => {
  showDetailModal.value = false
  selectedReturn.value = null
  isEditMode.value = false
  editableItems.value = []
}

// Edit Mode Methods
const canEdit = (returnItem: ReturnItem) => {
  return ['Pending Approval', 'Draft', 'Submitted'].includes(returnItem.status)
}

const enableEditMode = () => {
  if (selectedReturn.value && selectedReturn.value.items) {
    // Deep copy items array for editing
    editableItems.value = JSON.parse(JSON.stringify(selectedReturn.value.items))
    isEditMode.value = true
  }
}

const cancelEdit = () => {
  isEditMode.value = false
  editableItems.value = []
}

const saveChanges = async () => {
  if (!selectedReturn.value) return

  // Validate quantities
  const invalidQty = editableItems.value.some(item => !item.qty || item.qty <= 0)
  if (invalidQty) {
    alert('Semua quantity harus lebih besar dari 0')
    return
  }

  isSaving.value = true

  try {
    const response = await axios.put(`/transaction/return/${selectedReturn.value.id}`, {
      items: editableItems.value.map(item => ({
        id: item.id,
        item_code: item.item_code, // Fallback for matching
        batch_lot: item.batch_lot,  // Fallback for matching
        qty: item.qty
      }))
    })

    if (response.data.success) {
      // Update the local data
      if (selectedReturn.value && selectedReturn.value.items) {
        selectedReturn.value.items = JSON.parse(JSON.stringify(editableItems.value))
      }

      // Update the returns list
      const returnIndex = returns.value.findIndex(r => r.id === selectedReturn.value?.id)
      if (returnIndex !== -1) {
        returns.value[returnIndex].items = JSON.parse(JSON.stringify(editableItems.value))
        returns.value[returnIndex].total_qty = editableItems.value.reduce((sum, item) => sum + item.qty, 0)
      }

      isEditMode.value = false
      showMessage('success', response.data.message || 'Perubahan berhasil disimpan')
    } else {
      alert(response.data.message || 'Gagal menyimpan perubahan')
    }
  } catch (error: any) {
    console.error('Save Changes Error:', error)
    alert(error.response?.data?.message || 'Terjadi kesalahan saat menyimpan')
  } finally {
    isSaving.value = false
  }
}


const printSlip = (returnItem: ReturnItem) => {
  const currentDate = new Date().toLocaleDateString('id-ID')
  const returnDate = formatDate(returnItem.date)

  // Generate table rows for all items
  const itemsRows = returnItem.items.map((item, index) => `
    <tr>
      <td>${index + 1}</td>
      <td>${item.item_code || '-'}</td>
      <td>${item.item_name || '-'}</td>
      <td>${item.batch_lot || '-'}</td>
      <td>${formatQty(item.qty)}</td>
      <td>${item.uom || '-'}</td>
      <td>${item.reason || '-'}</td>
    </tr>
  `).join('')

  const printWindow = window.open('', '_blank')
  if (printWindow) {
    const htmlContent = `<!DOCTYPE html>
<html>
<head>
  <title>Return Slip - ${returnItem.returnNumber}</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; color: #000; }
    .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 20px; }
    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .info-table td { padding: 8px; border: 1px solid #000; }
    .info-table td:first-child { background-color: #f5f5f5; font-weight: bold; width: 200px; }
    .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
    .items-table th, .items-table td { padding: 8px; border: 1px solid #000; text-align: left; }
    .items-table th { background-color: #f5f5f5; font-weight: bold; }
    .signatures { display: flex; justify-content: space-between; margin-top: 80px; }
    .signature-box { text-align: center; width: 250px; }
    .signature-line { border-bottom: 1px solid #000; margin-bottom: 10px; height: 60px; }
    @media print { body { margin: 0; } }
  </style>
</head>
<body>
  <div class="header">
    <h1>SLIP RETURN ${returnItem.type.toUpperCase()}</h1>
    <h2>${returnItem.returnNumber}</h2>
  </div>
  
  <table class="info-table">
    <tr>
      <td>Return Number</td><td>${returnItem.returnNumber}</td>
      <td>Return Date</td><td>${returnDate}</td>
    </tr>
    <tr>
      <td>Return Type</td><td>${returnItem.type}</td>
      <td>Status</td><td>${returnItem.status}</td>
    </tr>
    <tr>
      <td>${returnItem.type === 'Supplier' ? 'Supplier' : 'Dept Asal'}</td>
      <td colspan="3">${returnItem.supplier || '-'}</td>
    </tr>
    ${returnItem.shipmentNo ? `<tr><td>No Shipment/Reservasi</td><td colspan="3">${returnItem.shipmentNo}</td></tr>` : ''}
  </table>

  <table class="items-table">
    <thead>
      <tr>
        <th>No</th><th>Kode Item</th><th>Nama Material</th><th>Lot/Batch</th>
        <th>Qty</th><th>UoM</th><th>Reason</th>
      </tr>
    </thead>
    <tbody>
      ${itemsRows}
    </tbody>
  </table>

  <div class="signatures">
    <div class="signature-box">
      <div class="signature-line"></div>
      <p><strong>Approved By</strong></p>
      <p>Nama: _________________</p>
      <p>Tanggal: ${currentDate}</p>
    </div>
    <div class="signature-box">
      <div class="signature-line"></div>
      <p><strong>Received By</strong></p>
      <p>Nama: _________________</p>
      <p>Tanggal: ${currentDate}</p>
    </div>
  </div>
</body>
</html>`

    printWindow.document.write(htmlContent)
    printWindow.document.close()

    setTimeout(() => {
      printWindow.print()
    }, 500)
  }
  
  showMessage('success', `Slip return ${returnItem.returnNumber} siap dicetak`)
}


const showMessage = (type: 'success' | 'error', text: string) => {
  message.value = { type, text }
  setTimeout(() => {
    message.value = null
  }, 3000)
}

// Initialize
onMounted(() => {
  const savedDarkMode = localStorage.getItem('darkMode')
  if (savedDarkMode) {
    isDarkMode.value = JSON.parse(savedDarkMode)
  }

  fetchDeptReservations();

  if (props.initialReturns) {
    returns.value = props.initialReturns as ReturnItem[]
  }

  addItem()
})

watch(() => props.initialReturns, (newVal) => {
  if (newVal) {
    returns.value = newVal as ReturnItem[]
  }
}, { immediate: true })

</script>