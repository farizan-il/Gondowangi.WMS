<template>
    <AppLayout title="Riwayat Aktivitas">
        <div :class="isDarkMode ? 'dark' : ''" class="min-h-screen transition-colors duration-300">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
      <!-- Header -->
      <div class="mb-6">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Return (Supplier / Production)</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola return barang ke supplier dan dari produksi</p>
          </div>
          <div class="flex items-center gap-4">
            <!-- Dark Mode Toggle -->
            <button 
              @click="toggleDarkMode"
              class="p-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              <svg v-if="isDarkMode" class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
              </svg>
              <svg v-else class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
              </svg>
            </button>
            
            <button 
              @click="showAddModal = true"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Buat Return
            </button>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd" />
                  <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a2 2 0 002 2v1a2 2 0 002 2v1a2 2 0 002 2v1a2 2 0 002 2v1a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Returns</p>
              <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ totalReturns }}</p>
            </div>
          </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Supplier Returns</p>
              <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ supplierReturns }}</p>
            </div>
          </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Production Returns</p>
              <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ productionReturns }}</p>
            </div>
          </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Returns</p>
              <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ pendingReturns }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filter dan Search -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-6 border border-gray-200 dark:border-gray-700">
        <div class="flex flex-wrap gap-4">
          <div class="flex-1 min-w-64">
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Cari Return Number, Supplier, Item..."
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
          </div>
          <select v-model="typeFilter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Jenis</option>
            <option value="Supplier">Return Supplier</option>
            <option value="Production">Return Production</option>
          </select>
          <select v-model="statusFilter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Status</option>
            <option value="Draft">Draft</option>
            <option value="Submitted">Submitted</option>
            <option value="Completed">Completed</option>
          </select>
          <select v-model="reasonFilter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Reason</option>
            <option value="QC Reject">QC Reject</option>
            <option value="Expired">Expired</option>
            <option value="Damage">Damage</option>
            <option value="Excess Production">Excess Production</option>
            <option value="Wrong Delivery">Wrong Delivery</option>
          </select>
        </div>
      </div>

      <!-- Tabel Returns -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Return Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Supplier / Dept</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode Item</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Material</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lot/Batch</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Qty</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reason</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="returnItem in filteredReturns" :key="returnItem.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900 dark:text-white">{{ returnItem.returnNumber }}</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">{{ returnItem.type }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-white">{{ formatDate(returnItem.date) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-white">{{ returnItem.supplier }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-mono text-gray-900 dark:text-white">{{ returnItem.itemCode }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900 dark:text-white">{{ returnItem.itemName }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-mono text-gray-900 dark:text-white">{{ returnItem.lotBatch }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-white">{{ returnItem.qty }} {{ returnItem.uom }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getReasonClass(returnItem.reason)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ returnItem.reason }}
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
                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/30 px-2 py-1 rounded transition-colors"
                  >
                    Detail
                  </button>
                  <button 
                    @click="printSlip(returnItem)"
                    class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 bg-purple-50 hover:bg-purple-100 dark:bg-purple-900/20 dark:hover:bg-purple-900/30 px-2 py-1 rounded transition-colors"
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
      <div v-if="showAddModal" class="fixed inset-0 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-4xl max-h-screen overflow-y-auto border border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Buat Return Baru</h3>
            <button 
              @click="closeAddModal"
              class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400"
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
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Jenis Return</label>
              <div class="flex gap-6">
                <label class="flex items-center">
                  <input 
                    v-model="newReturn.type" 
                    type="radio" 
                    value="Supplier"
                    class="text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-400"
                  >
                  <span class="ml-2 text-gray-900 dark:text-white">Return ke Supplier</span>
                </label>
                <label class="flex items-center">
                  <input 
                    v-model="newReturn.type" 
                    type="radio" 
                    value="Production"
                    class="text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-400"
                  >
                  <span class="ml-2 text-gray-900 dark:text-white">Return dari Produksi</span>
                </label>
              </div>
            </div>

            <!-- Header Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Return</label>
                <input 
                  v-model="newReturn.date"
                  type="date" 
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  {{ newReturn.type === 'Supplier' ? 'Supplier' : 'Dept Asal' }}
                </label>
                <select 
                  v-model="newReturn.supplier" 
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Pilih {{ newReturn.type === 'Supplier' ? 'Supplier' : 'Departemen' }}</option>
                  <template v-if="newReturn.type === 'Supplier'">
                    <option value="PT. Supplier A">PT. Supplier A</option>
                    <option value="PT. Supplier B">PT. Supplier B</option>
                    <option value="PT. Supplier C">PT. Supplier C</option>
                  </template>
                  <template v-else>
                    <option value="Production Line 1">Production Line 1</option>
                    <option value="Production Line 2">Production Line 2</option>
                    <option value="Assembly Department">Assembly Department</option>
                  </template>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No Shipment / Reservasi</label>
                <select 
                  v-model="newReturn.shipmentNo" 
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Pilih (Opsional)</option>
                  <option value="SHP240919001">SHP240919001</option>
                  <option value="RES240919002">RES240919002</option>
                  <option value="PO240919003">PO240919003</option>
                </select>
              </div>
            </div>

            <!-- Items Table -->
            <div>
              <div class="flex justify-between items-center mb-4">
                <h4 class="text-lg font-medium text-gray-900 dark:text-white">Daftar Item Return</h4>
                <button 
                  @click="addItem"
                  class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors"
                >
                  + Tambah Item
                </button>
              </div>
              
              <div class="overflow-x-auto border border-gray-300 dark:border-gray-600 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kode Item</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nama Material</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Lot/Batch</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Qty Return</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">UoM</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Reason</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    <tr v-for="(item, index) in newReturn.items" :key="index">
                      <td class="px-4 py-3">
                        <input 
                          v-model="item.itemCode"
                          type="text" 
                          placeholder="ITM001"
                          class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500"
                        >
                      </td>
                      <td class="px-4 py-3">
                        <input 
                          v-model="item.itemName"
                          type="text" 
                          placeholder="Nama material"
                          class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500"
                        >
                      </td>
                      <td class="px-4 py-3">
                        <input 
                          v-model="item.lotBatch"
                          type="text" 
                          placeholder="LOT001"
                          class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500"
                        >
                      </td>
                      <td class="px-4 py-3">
                        <input 
                          v-model="item.qty"
                          type="number" 
                          placeholder="0"
                          class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500"
                        >
                      </td>
                      <td class="px-4 py-3">
                        <select 
                          v-model="item.uom" 
                          class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500"
                        >
                          <option value="PCS">PCS</option>
                          <option value="BOX">BOX</option>
                          <option value="KG">KG</option>
                          <option value="LITER">LITER</option>
                        </select>
                      </td>
                      <td class="px-4 py-3">
                        <select 
                          v-model="item.reason" 
                          class="w-full px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500"
                        >
                          <option value="">Pilih Reason</option>
                          <option value="QC Reject">QC Reject</option>
                          <option value="Expired">Expired</option>
                          <option value="Damage">Damage</option>
                          <option value="Excess Production">Excess Production</option>
                          <option value="Wrong Delivery">Wrong Delivery</option>
                        </select>
                      </td>
                      <td class="px-4 py-3">
                        <button 
                          @click="removeItem(index)"
                          class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm"
                        >
                          🗑️
                        </button>
                      </td>
                    </tr>
                    <tr v-if="newReturn.items.length === 0">
                      <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        Belum ada item. Klik "Tambah Item" untuk menambahkan.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
              <button 
                @click="closeAddModal"
                class="px-4 py-2 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              >
                Batal
              </button>
              <button 
                @click="saveReturn"
                :disabled="!canSaveReturn"
                :class="canSaveReturn 
                  ? 'bg-blue-600 hover:bg-blue-700 text-white' 
                  : 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 cursor-not-allowed'"
                class="px-4 py-2 rounded-lg transition-colors"
              >
                Simpan Return
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Detail Return -->
      <div v-if="showDetailModal && selectedReturn" class="fixed inset-0 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-4xl max-h-screen overflow-y-auto border border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Detail Return</h3>
            <button 
              @click="closeDetailModal"
              class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Return Header Info -->
          <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 mb-6 border border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Return Number</label>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ selectedReturn.returnNumber }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Jenis Return</label>
                <p class="text-lg text-gray-900 dark:text-white">{{ selectedReturn.type }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Tanggal</label>
                <p class="text-lg text-gray-900 dark:text-white">{{ formatDate(selectedReturn.date) }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status</label>
                <span :class="getStatusClass(selectedReturn.status)" class="px-2 py-1 text-sm font-semibold rounded-full">
                  {{ selectedReturn.status }}
                </span>
              </div>
            </div>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">
                  {{ selectedReturn.type === 'Supplier' ? 'Supplier' : 'Dept Asal' }}
                </label>
                <p class="text-lg text-gray-900 dark:text-white">{{ selectedReturn.supplier }}</p>
              </div>
              <div v-if="selectedReturn.shipmentNo">
                <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">No Shipment/Reservasi</label>
                <p class="text-lg font-mono text-gray-900 dark:text-white">{{ selectedReturn.shipmentNo }}</p>
              </div>
            </div>
          </div>

          <!-- Items Detail -->
          <div class="overflow-x-auto border border-gray-300 dark:border-gray-600 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">No</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kode Item</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nama Material</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Lot/Batch</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Qty</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">UoM</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Reason</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                <tr>
                  <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">1</td>
                  <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-white">{{ selectedReturn.itemCode }}</td>
                  <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ selectedReturn.itemName }}</td>
                  <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-white">{{ selectedReturn.lotBatch }}</td>
                  <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ selectedReturn.qty }}</td>
                  <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ selectedReturn.uom }}</td>
                  <td class="px-4 py-3">
                    <span :class="getReasonClass(selectedReturn.reason)" class="px-2 py-1 text-xs font-medium rounded-full">
                      {{ selectedReturn.reason }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button 
              @click="closeDetailModal"
              class="px-4 py-2 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              Tutup
            </button>
            <button 
              @click="printSlip(selectedReturn)"
              class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
            >
              Cetak Slip Return
            </button>
          </div>
        </div>
      </div>

      <!-- Success/Error Messages -->
      <div v-if="message" :class="message.type === 'success' ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 text-green-800 dark:text-green-400' : 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-800 dark:text-red-400'" class="fixed top-4 right-4 border rounded-lg p-4 shadow-lg" style="z-index: 1000;">
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
    </div>
  </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'

import { ref, computed, onMounted } from 'vue'

// Types
interface ReturnItem {
  id: string
  returnNumber: string
  date: string
  type: 'Supplier' | 'Production'
  supplier: string
  shipmentNo?: string
  itemCode: string
  itemName: string
  lotBatch: string
  qty: number
  uom: string
  reason: string
  status: 'Draft' | 'Submitted' | 'Completed'
}

interface NewReturnItem {
  itemCode: string
  itemName: string
  lotBatch: string
  qty: number
  uom: string
  reason: string
}

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

// Form data
const newReturn = ref({
  type: 'Supplier',
  date: new Date().toISOString().split('T')[0],
  supplier: '',
  shipmentNo: '',
  items: [] as NewReturnItem[]
})

const message = ref<{ type: 'success' | 'error', text: string } | null>(null)

// Computed properties
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
  const hasBasicInfo = newReturn.value.type && newReturn.value.date && newReturn.value.supplier
  const hasValidItems = newReturn.value.items.length > 0 && 
    newReturn.value.items.every(item => 
      item.itemCode && item.itemName && item.lotBatch && item.qty > 0 && item.uom && item.reason
    )
  return hasBasicInfo && hasValidItems
})

// Methods
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
    case 'Submitted':
      return `${baseClasses} bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400`
    case 'Completed':
      return `${baseClasses} bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400`
    default:
      return `${baseClasses} bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300`
  }
}

const getReasonClass = (reason: string) => {
  const baseClasses = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full'
  switch (reason) {
    case 'QC Reject':
      return `${baseClasses} bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400`
    case 'Expired':
      return `${baseClasses} bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400`
    case 'Damage':
      return `${baseClasses} bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400`
    case 'Excess Production':
      return `${baseClasses} bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400`
    case 'Wrong Delivery':
      return `${baseClasses} bg-pink-100 text-pink-800 dark:bg-pink-900/20 dark:text-pink-400`
    default:
      return `${baseClasses} bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300`
  }
}

const generateReturnNumber = () => {
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
    reason: ''
  })
}

const removeItem = (index: number) => {
  newReturn.value.items.splice(index, 1)
}

const closeAddModal = () => {
  showAddModal.value = false
  resetForm()
}

const resetForm = () => {
  newReturn.value = {
    type: 'Supplier',
    date: new Date().toISOString().split('T')[0],
    supplier: '',
    shipmentNo: '',
    items: []
  }
}

const saveReturn = () => {
  if (!canSaveReturn.value) return

  // Create return records for each item
  newReturn.value.items.forEach((item, index) => {
    const returnRecord: ReturnItem = {
      id: `${Date.now()}_${index}`,
      returnNumber: generateReturnNumber(),
      date: newReturn.value.date,
      type: newReturn.value.type as 'Supplier' | 'Production',
      supplier: newReturn.value.supplier,
      shipmentNo: newReturn.value.shipmentNo,
      itemCode: item.itemCode,
      itemName: item.itemName,
      lotBatch: item.lotBatch,
      qty: item.qty,
      uom: item.uom,
      reason: item.reason,
      status: 'Submitted'
    }
    
    returns.value.unshift(returnRecord)
  })

  // Simulate stock movement
  const stockMovement = {
    type: newReturn.value.type,
    items: newReturn.value.items.map(item => ({
      itemCode: item.itemCode,
      qty: item.qty,
      from: newReturn.value.type === 'Supplier' ? 'Reject Bin' : 'Production Bin',
      to: newReturn.value.type === 'Supplier' ? 'Outbound (Supplier)' : 'Quarantine/Available Bin'
    }))
  }
  
  console.log('Stock Movement:', stockMovement)
  
  showMessage('success', `Return berhasil dibuat dengan ${newReturn.value.items.length} item`)
  closeAddModal()
}

const viewDetail = (returnItem: ReturnItem) => {
  selectedReturn.value = returnItem
  showDetailModal.value = true
}

const closeDetailModal = () => {
  showDetailModal.value = false
  selectedReturn.value = null
}

const printSlip = (returnItem: ReturnItem) => {
  const currentDate = new Date().toLocaleDateString('id-ID')
  const returnDate = formatDate(returnItem.date)
  
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
      <td>${returnItem.type === 'Supplier' ? 'Supplier Address' : 'Dept Asal'}</td>
      <td colspan="3">${returnItem.supplier}</td>
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
      <tr>
        <td>1</td><td>${returnItem.itemCode}</td><td>${returnItem.itemName}</td>
        <td>${returnItem.lotBatch}</td><td>${returnItem.qty}</td>
        <td>${returnItem.uom}</td><td>${returnItem.reason}</td>
      </tr>
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
  // Load dark mode preference
  const savedDarkMode = localStorage.getItem('darkMode')
  if (savedDarkMode) {
    isDarkMode.value = JSON.parse(savedDarkMode)
  }

  // Initialize dummy data
  returns.value = [
    {
      id: '1',
      returnNumber: 'RET24091901',
      date: '2024-09-19T00:00:00Z',
      type: 'Supplier',
      supplier: 'PT. Supplier A',
      shipmentNo: 'SHP240919001',
      itemCode: 'ITM001',
      itemName: 'Raw Material A - Quality Issue',
      lotBatch: 'LOT240915001',
      qty: 25,
      uom: 'PCS',
      reason: 'QC Reject',
      status: 'Submitted'
    },
    {
      id: '2',
      returnNumber: 'RET24091902',
      date: '2024-09-19T00:00:00Z',
      type: 'Production',
      supplier: 'Production Line 1',
      itemCode: 'ITM002',
      itemName: 'Semi Finished Goods B',
      lotBatch: 'LOT240918002',
      qty: 15,
      uom: 'BOX',
      reason: 'Excess Production',
      status: 'Completed'
    },
    {
      id: '3',
      returnNumber: 'RET24091903',
      date: '2024-09-18T00:00:00Z',
      type: 'Supplier',
      supplier: 'PT. Supplier B',
      shipmentNo: 'RES240918003',
      itemCode: 'ITM003',
      itemName: 'Chemical Component C',
      lotBatch: 'LOT240910003',
      qty: 50,
      uom: 'LITER',
      reason: 'Expired',
      status: 'Completed'
    },
    {
      id: '4',
      returnNumber: 'RET24091904',
      date: '2024-09-18T00:00:00Z',
      type: 'Supplier',
      supplier: 'PT. Supplier C',
      itemCode: 'ITM004',
      itemName: 'Packaging Material D',
      lotBatch: 'LOT240917004',
      qty: 100,
      uom: 'PCS',
      reason: 'Damage',
      status: 'Submitted'
    },
    {
      id: '5',
      returnNumber: 'RET24091905',
      date: '2024-09-17T00:00:00Z',
      type: 'Production',
      supplier: 'Assembly Department',
      itemCode: 'ITM005',
      itemName: 'Electronic Component E',
      lotBatch: 'LOT240916005',
      qty: 30,
      uom: 'PCS',
      reason: 'Wrong Delivery',
      status: 'Draft'
    }
  ]

  // Initialize with one item for demo
  addItem()
})
</script>