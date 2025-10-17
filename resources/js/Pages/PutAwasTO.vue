<template>
  <AppLayout title="PutAway & TO">
    <div class="min-h-screen bg-gray-50 p-6">
      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Putaway & Transfer Order</h1>
        <p class="text-gray-600">Kelola Transfer Order untuk putaway, transfer, dan picking barang</p>
      </div>

      <!-- Auto Generate Putaway Button -->
      <div class="mb-6">
        <button @click="generateAutoPutaway"
          class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Generate Auto Putaway dari QC Released
        </button>
      </div>

      <!-- Filter & Search -->
      <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search TO Number</label>
            <input v-model="searchQuery" type="text" placeholder="TO-2024-001..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
            <select v-model="filterType"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="">Semua Tipe</option>
              <option value="Putaway - QC Release">Putaway - QC Release</option>
              <option value="Transfer - Internal">Transfer - Internal</option>
              <option value="Transfer - Bin to Bin">Transfer - Bin to Bin</option>
              <option value="Picking - Production">Picking - Production</option>
              <option value="Picking - Sales Order">Picking - Sales Order</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select v-model="filterStatus"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="">Semua Status</option>
              <option value="Pending">Pending</option>
              <option value="In Progress">In Progress</option>
              <option value="Completed">Completed</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Warehouse</label>
            <select v-model="filterWarehouse"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="">Semua Gudang</option>
              <option value="WH-001">WH-001 - Gudang Utama</option>
              <option value="WH-002">WH-002 - Gudang Karantina</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Transfer Orders Table -->
      <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">Daftar Transfer Order</h2>
          <p class="text-sm text-gray-600 mt-1">Total: {{ filteredTransferOrders.length }} TO</p>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TO Number
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creation Date
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <!-- Empty State -->
              <tr v-if="!transferOrders || transferOrders.length === 0">
                <td colspan="7" class="px-6 py-12 text-center">
                  <div class="flex flex-col items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Belum Ada Transfer Order</h3>
                    <p class="text-gray-500">Generate putaway dari material QC Released untuk membuat TO</p>
                  </div>
                </td>
              </tr>

              <tr v-for="to in transferOrders" :key="to.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="font-medium text-gray-900">{{ to.toNumber }}</div>
                  <div v-if="to.reservationNo" class="text-sm text-gray-500">{{ to.reservationNo }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatDate(to.creationDate) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ to.warehouse }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getTypeClass(to.type)"
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ to.type }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusClass(to.status)"
                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ to.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ to.items.length }} item(s)
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end gap-2">
                    <button @click="viewDetail(to)"
                      class="flex items-center gap-2 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 hover:bg-blue-200 dark:hover:bg-blue-800 px-3 py-2 rounded-lg transition-colors"
                      title="Detail">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                      </svg>
                      <span class="text-xs font-medium">Detail</span>
                    </button>
                    <button v-if="to.status !== 'Completed'" @click="executeTO(to)"
                      class="flex items-center gap-2 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-800 px-3 py-2 rounded-lg transition-colors"
                      title="Kerjakan">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293H15M6 6h3m5 0h3">
                        </path>
                      </svg>
                      <span class="text-xs font-medium">Kerjakan</span>
                    </button>
                    <button @click="printTO(to)"
                      class="flex items-center gap-2 bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 hover:bg-purple-200 dark:hover:bg-purple-800 px-3 py-2 rounded-lg transition-colors"
                      title="Cetak TO">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2v4h10z">
                        </path>
                      </svg>
                      <span class="text-xs font-medium">Cetak TO</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Auto Putaway Generation Modal -->
      <div v-if="showAutoPutawayModal"
        class="fixed inset-0 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-[9999]"
        style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-screen overflow-y-auto">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold">Generate Auto Putaway dari QC Released</h3>
            <button @click="showAutoPutawayModal = false" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <div class="space-y-6">
            <!-- QC Released Materials -->
            <div>
              <h4 class="text-md font-medium text-gray-900 mb-3">Material yang sudah QC Released</h4>
              <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-lg">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Select</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Code</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Material Name</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current Bin</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">UoM</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Destination</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(material, index) in qcReleasedMaterials" :key="index" class="hover:bg-gray-50">
                      <td class="px-4 py-3">
                        <input type="checkbox" v-model="material.selected"
                          class="rounded border-gray-300 focus:ring-blue-500">
                      </td>
                      <td class="px-4 py-3 text-sm text-gray-900">{{ material.itemCode }}</td>
                      <td class="px-4 py-3 text-sm text-gray-900">{{ material.materialName }}</td>
                      <td class="px-4 py-3 text-sm text-gray-900">{{ material.currentBin }}</td>
                      <td class="px-4 py-3 text-sm text-gray-900">{{ material.qty }}</td>
                      <td class="px-4 py-3 text-sm text-gray-900">{{ material.uom }}</td>
                      <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                          <select v-model="material.destinationBin"
                            class="px-3 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :disabled="!material.selected">
                            <option value="">Pilih Bin</option>
                            <option v-for="bin in availableBins" :key="bin.code" :value="bin.code">
                              {{ bin.code }} - {{ bin.zone }} ({{ bin.currentItems }}/{{ bin.capacity }})
                            </option>
                          </select>
                          <button @click="showBinDetails(material)" class="text-blue-600 hover:text-blue-800 text-xs"
                            title="Lihat detail bin" :disabled="!material.destinationBin">
                            Info
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="flex gap-3 justify-end">
              <button @click="showAutoPutawayModal = false"
                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                Batal
              </button>
              <button @click="confirmAutoPutaway" :disabled="!selectedMaterials.length"
                :class="selectedMaterials.length ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400 cursor-not-allowed'"
                class="px-4 py-2 text-white rounded-md font-medium">
                Generate Putaway TO ({{ selectedMaterials.length }} items)
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Bin Details Modal -->
      <div v-if="showBinModal"
        class="fixed inset-0 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-[9999]"
        style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Detail Bin: {{ selectedBinInfo?.code }}</h3>
            <button @click="showBinModal = false" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <div v-if="selectedBinInfo" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Bin Code</label>
                <p class="text-sm text-gray-900 mt-1">{{ selectedBinInfo.code }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Warehouse</label>
                <p class="text-sm text-gray-900 mt-1">{{ selectedBinInfo.warehouse }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Zone</label>
                <p class="text-sm text-gray-900 mt-1">{{ selectedBinInfo.zone }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Capacity</label>
                <p class="text-sm text-gray-900 mt-1">{{ selectedBinInfo.capacity }}</p>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Current Materials</label>
              <div class="max-h-40 overflow-y-auto">
                <table class="w-full text-sm">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-3 py-2 text-left">Material</th>
                      <th class="px-3 py-2 text-left">Qty</th>
                      <th class="px-3 py-2 text-left">UoM</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="material in selectedBinInfo.materials" :key="material.itemCode" class="border-t">
                      <td class="px-3 py-2">{{ material.materialName }}</td>
                      <td class="px-3 py-2">{{ material.qty }}</td>
                      <td class="px-3 py-2">{{ material.uom }}</td>
                    </tr>
                    <tr v-if="!selectedBinInfo.materials.length" class="border-t">
                      <td colspan="3" class="px-3 py-2 text-gray-500 text-center">Bin kosong</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="flex justify-end mt-6">
            <button @click="showBinModal = false" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
              Tutup
            </button>
          </div>
        </div>
      </div>

      <!-- Detail/Execute TO Modal -->
      <div v-if="showDetailModal"
        class="fixed inset-0 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-[9999]"
        style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg p-6 w-full max-w-7xl max-h-screen overflow-y-auto">
          <div class="flex justify-between items-center mb-6">
            <div>
              <h3 class="text-xl font-semibold">{{ selectedTO.isExecuting ? 'Kerjakan' : 'Detail' }} Transfer Order</h3>
              <p class="text-gray-600 mt-1">{{ selectedTO.toNumber }}</p>
            </div>
            <button @click="closeDetailModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- TO Info -->
          <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">TO Number</label>
                <p class="text-sm text-gray-900 mt-1">{{ selectedTO.toNumber }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Transaction Type</label>
                <p class="text-sm text-gray-900 mt-1">{{ selectedTO.type }}</p>
              </div>
              <div v-if="selectedTO.reservationNo">
                <label class="block text-sm font-medium text-gray-700">No Reservasi</label>
                <p class="text-sm text-gray-900 mt-1">{{ selectedTO.reservationNo }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <span :class="getStatusClass(selectedTO.status)"
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full mt-1">
                  {{ selectedTO.status }}
                </span>
              </div>
            </div>
          </div>

          <!-- Items Table -->
          <div class="mb-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Daftar Item</h4>
            <div class="overflow-x-auto">
              <table class="w-full border border-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Item</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Material</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source Bin</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dest Bin</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">UoM</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th v-if="selectedTO.isExecuting"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(item, index) in selectedTO.items" :key="index" class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ index + 1 }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.itemCode }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.materialName }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.sourceBin }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.destBin }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                      <div v-if="selectedTO.isExecuting && item.status !== 'completed'">
                        <input v-model="item.actualQty" type="number" :placeholder="item.qty.toString()"
                          class="w-20 px-2 py-1 border border-gray-300 rounded text-center" min="0">
                      </div>
                      <div v-else>{{ item.actualQty || item.qty }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.uom }}</td>
                    <td class="px-4 py-3">
                      <span :class="getItemStatusClass(item.status)"
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                        {{ item.status }}
                      </span>
                    </td>
                    <td v-if="selectedTO.isExecuting" class="px-4 py-3">
                      <div v-if="item.status !== 'completed'" class="flex gap-2">
                        <button @click="scanBox(item)"
                          :class="item.boxScanned ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'"
                          class="px-2 py-1 text-xs font-medium rounded">
                          {{ item.boxScanned ? '✓ Box' : 'Scan Box' }}
                        </button>
                        <button @click="scanSourceBin(item)"
                          :class="item.sourceBinScanned ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'"
                          class="px-2 py-1 text-xs font-medium rounded">
                          {{ item.sourceBinScanned ? '✓ Source' : 'Scan Source' }}
                        </button>
                        <button @click="scanDestBin(item)"
                          :class="item.destBinScanned ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'"
                          class="px-2 py-1 text-xs font-medium rounded">
                          {{ item.destBinScanned ? '✓ Dest' : 'Scan Dest' }}
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-3 justify-end">
            <button @click="closeDetailModal"
              class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
              {{ selectedTO.isExecuting ? 'Batal' : 'Tutup' }}
            </button>
            <button v-if="selectedTO.isExecuting" @click="completeTO" :disabled="!canCompleteTO"
              :class="canCompleteTO ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed'"
              class="px-4 py-2 text-white rounded-md font-medium">
              Selesai TO
            </button>
          </div>
        </div>
      </div>

      <!-- QR Scanner Modal -->
      <div v-if="showQRModal"
        class="fixed inset-0 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-[9999]"
        style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">{{ qrScanType }} Scanner</h3>
            <button @click="closeQRModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <div class="text-center">
            <div
              class="w-48 h-48 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg mx-auto flex items-center justify-center mb-4">
              <div class="text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 16h4m-4 0h2m-6-4h2m-8-2h.01M8 12h2m-2 0V8m0 0h2m-2 0H4m0 0h4v4m0-4h2">
                  </path>
                </svg>
                <p class="text-gray-500">QR Scanner Area</p>
              </div>
            </div>

            <div class="space-y-4">
              <input v-model="qrInput" type="text" :placeholder="`Input ${qrScanType} Code Manual`"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">

              <div class="flex gap-3">
                <button @click="closeQRModal"
                  class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                  Batal
                </button>
                <button @click="confirmQRScan"
                  class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                  Confirm Scan
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
import { router } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'

// Interfaces
interface TOItem {
  itemCode: string
  materialName: string
  sourceBin: string
  destBin: string
  qty: number
  actualQty?: number
  uom: string
  status: 'pending' | 'in_progress' | 'completed'
  boxScanned: boolean
  sourceBinScanned: boolean
  destBinScanned: boolean
}

interface TransferOrder {
  id: string
  toNumber: string
  creationDate: Date
  warehouse: string
  type: string
  status: 'Pending' | 'In Progress' | 'Completed'
  reservationNo?: string
  items: TOItem[]
  isExecuting?: boolean
}

interface QCReleasedMaterial {
  itemCode: string
  materialName: string
  currentBin: string
  qty: number
  uom: string
  selected: boolean
  destinationBin: string
}

interface BinInfo {
  code: string
  warehouse: string
  zone: string
  capacity: string
  currentItems: number
  materials: Array<{
    itemCode: string
    materialName: string
    qty: number
    uom: string
  }>
}

const page = usePage()
const transferOrders = ref<TransferOrder[]>((page.props.transferOrders as TransferOrder[]) || [])


// Reactive data
const qcReleasedMaterials = ref<QCReleasedMaterial[]>([])
const availableBins = ref<BinInfo[]>([])
const searchQuery = ref('')
const filterType = ref('')
const filterStatus = ref('')
const filterWarehouse = ref('')

// Modals
const showAutoPutawayModal = ref(false)
const showBinModal = ref(false)
const showDetailModal = ref(false)
const showQRModal = ref(false)

// Selected data
const selectedTO = ref<TransferOrder | null>(null)
const selectedBinInfo = ref<BinInfo | null>(null)
const currentItem = ref<TOItem | null>(null)
const qrScanType = ref('')
const qrInput = ref('')

// Computed
const filteredTransferOrders = computed(() => {
  return transferOrders.value.filter(to => {
    const matchesSearch = !searchQuery.value || to.toNumber.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesType = !filterType.value || to.type === filterType.value
    const matchesStatus = !filterStatus.value || to.status === filterStatus.value
    const matchesWarehouse = !filterWarehouse.value || to.warehouse.includes(filterWarehouse.value)
    
    return matchesSearch && matchesType && matchesStatus && matchesWarehouse
  })
})

const selectedMaterials = computed(() => {
  return qcReleasedMaterials.value.filter(m => m.selected && m.destinationBin)
})

const canCompleteTO = computed(() => {
  if (!selectedTO.value) return false
  return selectedTO.value.items.every(item =>
    item.boxScanned && item.sourceBinScanned && item.destBinScanned &&
    (item.actualQty !== undefined || item.qty > 0)
  )
})

// Methods
const generateTONumber = () => {
  const date = new Date()
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const sequence = String(transferOrders.value.length + 1).padStart(3, '0')
  return `TO-${year}-${month}-${sequence}`
}

const generateAutoPutaway = async () => {
  try {
    // Load QC released materials
    const materialResponse = await fetch('/transaction/putaway-transfer/qc-released')
    if (!materialResponse.ok) throw new Error('Failed to fetch materials')
    const materials = await materialResponse.json()
    qcReleasedMaterials.value = materials

    // Load available bins
    const binResponse = await fetch('/transaction/putaway-transfer/available-bins')
    if (!binResponse.ok) throw new Error('Failed to fetch bins')
    const bins = await binResponse.json()
    availableBins.value = bins

    showAutoPutawayModal.value = true
  } catch (error) {
    console.error('Error loading data:', error)
    alert('Gagal memuat data')
  }
}

const getAvailableBins = (itemCode: string) => {
  // Filter bins based on material type if needed
  return availableBins.value
}


const showBinDetails = async (material: QCReleasedMaterial) => {
  if (!material.destinationBin) {
    alert('Pilih destination bin terlebih dahulu!')
    return
  }

  try {
    const response = await fetch(`/transaction/putaway-transfer/bin-details?binCode=${material.destinationBin}`)
    if (!response.ok) throw new Error('Failed to fetch bin details')
    const binData = await response.json()

    selectedBinInfo.value = binData
    showBinModal.value = true
  } catch (error) {
    console.error('Error loading bin details:', error)
    alert('Gagal memuat detail bin')
  }
}

const confirmAutoPutaway = async () => {
  if (!selectedMaterials.value.length) {
    alert('Pilih minimal 1 material untuk di-putaway!')
    return
  }

  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

    const response = await fetch('/transaction/putaway-transfer/generate', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken || '',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        materials: selectedMaterials.value.map(m => ({
          stockId: m.stockId,
          destinationBin: m.destinationBin,
          qty: m.qty
        }))
      })
    })

    if (!response.ok) {
      const errorData = await response.json()
      throw new Error(errorData.message || 'Failed to generate putaway')
    }

    const result = await response.json()

    if (result.success) {
      showAutoPutawayModal.value = false
      alert(result.message)
      // Reload halaman untuk refresh data
      router.reload()
    } else {
      alert(result.message)
    }
  } catch (error) {
    console.error('Error generating putaway:', error)
    alert('Gagal generate putaway TO: ' + error.message)
  }
}

const viewDetail = (to: TransferOrder) => {
  selectedTO.value = { ...to, isExecuting: false }
  showDetailModal.value = true
}

const executeTO = (to: TransferOrder) => {
  selectedTO.value = {
    ...to,
    isExecuting: true,
    status: 'In Progress',
    items: to.items.map(item => ({ ...item, status: 'in_progress' as const }))
  }

  // Update original TO status
  const originalIndex = transferOrders.value.findIndex(t => t.id === to.id)
  if (originalIndex !== -1) {
    transferOrders.value[originalIndex].status = 'In Progress'
  }

  showDetailModal.value = true
}

const closeDetailModal = () => {
  selectedTO.value = null
  showDetailModal.value = false
}

const scanBox = (item: TOItem) => {
  currentItem.value = item
  qrScanType.value = 'Box QR'
  qrInput.value = ''
  showQRModal.value = true
}

const scanSourceBin = (item: TOItem) => {
  currentItem.value = item
  qrScanType.value = 'Source Bin'
  qrInput.value = ''
  showQRModal.value = true
}

const scanDestBin = (item: TOItem) => {
  currentItem.value = item
  qrScanType.value = 'Destination Bin'
  qrInput.value = ''
  showQRModal.value = true
}

const closeQRModal = () => {
  showQRModal.value = false
  currentItem.value = null
  qrScanType.value = ''
  qrInput.value = ''
}

const confirmQRScan = () => {
  if (!currentItem.value || !qrInput.value.trim()) {
    alert('Please input QR code!')
    return
  }

  const item = currentItem.value

  switch (qrScanType.value) {
    case 'Box QR':
      // Validasi box QR
      if (qrInput.value.includes(item.itemCode)) {
        item.boxScanned = true
        alert('Box QR berhasil di-scan!')
      } else {
        alert('Box QR tidak sesuai dengan item!')
        return
      }
      break

    case 'Source Bin':
      // Validasi source bin
      if (qrInput.value === item.sourceBin) {
        item.sourceBinScanned = true
        alert('Source Bin berhasil di-scan!')
      } else {
        alert(`Source Bin tidak sesuai! Expected: ${item.sourceBin}`)
        return
      }
      break

    case 'Destination Bin':
      // Validasi destination bin
      if (qrInput.value === item.destBin) {
        item.destBinScanned = true
        alert('Destination Bin berhasil di-scan!')
      } else {
        alert(`Destination Bin tidak sesuai! Expected: ${item.destBin}`)
        return
      }
      break
  }

  // Check if all scans complete for this item
  if (item.boxScanned && item.sourceBinScanned && item.destBinScanned) {
    item.status = 'completed'
    if (!item.actualQty) {
      item.actualQty = item.qty
    }
  }

  closeQRModal()
}

const completeTO = () => {
  if (!selectedTO.value || !canCompleteTO.value) return

  // Update stock transactions (simulation)
  selectedTO.value.items.forEach(item => {
    console.log(`Stock Transaction:`)
    console.log(`- Reduce ${item.actualQty || item.qty} ${item.uom} from ${item.sourceBin}`)
    console.log(`- Add ${item.actualQty || item.qty} ${item.uom} to ${item.destBin}`)

    // Update bin occupancy
    const sourceBin = binData.value.find(bin => bin.code === item.sourceBin)
    const destBin = binData.value.find(bin => bin.code === item.destBin)

    if (sourceBin) {
      sourceBin.currentItems = Math.max(0, sourceBin.currentItems - 1)
      // Remove material from source bin
      sourceBin.materials = sourceBin.materials.filter(m => m.itemCode !== item.itemCode)
    }

    if (destBin) {
      destBin.currentItems++
      // Add material to dest bin
      destBin.materials.push({
        itemCode: item.itemCode,
        materialName: item.materialName,
        qty: item.actualQty || item.qty,
        uom: item.uom
      })
    }
  })

  // Update TO status
  selectedTO.value.status = 'Completed'
  selectedTO.value.items.forEach(item => {
    item.status = 'completed'
  })

  // Update original TO in array
  const originalIndex = transferOrders.value.findIndex(t => t.id === selectedTO.value!.id)
  if (originalIndex !== -1) {
    transferOrders.value[originalIndex] = { ...selectedTO.value }
  }

  alert('Transfer Order berhasil diselesaikan!')
  closeDetailModal()
}

const printTO = (to: TransferOrder) => {
  // Generate PDF content simulation
  const printContent = `
    TRANSFER ORDER
    
    TO Number: ${to.toNumber}
    Creation Date: ${formatDate(to.creationDate)}
    Warehouse: ${to.warehouse}
    Transaction Type: ${to.type}
    ${to.reservationNo ? `No Reservasi: ${to.reservationNo}` : ''}
    
    DAFTAR ITEM:
    ${to.items.map((item, index) => `
    ${index + 1}. ${item.itemCode} - ${item.materialName}
       Source Bin: ${item.sourceBin}
       Dest Bin: ${item.destBin}
       Qty: ${item.actualQty || item.qty} ${item.uom}
    `).join('')}
    
    TANDA TANGAN:
    Diserahkan: ________________  Tgl: _______
    Diterima:   ________________  Tgl: _______
    Mengetahui: ________________  Tgl: _______
  `

  // Create printable window
  const printWindow = window.open('', '_blank')
  if (printWindow) {
    printWindow.document.write(`
      <html>
        <head>
          <title>Transfer Order - ${to.toNumber}</title>
          <style>
            body { font-family: Arial, sans-serif; padding: 20px; }
            pre { white-space: pre-wrap; }
          </style>
        </head>
        <body>
          <pre>${printContent}</pre>
        </body>
      </html>
    `)
    printWindow.document.close()
    printWindow.print()
  }
}

// Utility functions
const formatDate = (date: Date | string) => {
  // Convert to Date object if it's a string
  const dateObj = typeof date === 'string' ? new Date(date) : date
  
  // Check if date is valid
  if (isNaN(dateObj.getTime())) {
    return 'Invalid Date'
  }
  
  return new Intl.DateTimeFormat('id-ID', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  }).format(dateObj)
}

const getTypeClass = (type: string) => {
  const classes = {
    'Putaway - QC Release': 'bg-blue-100 text-blue-800',
    'Transfer - Internal': 'bg-green-100 text-green-800',
    'Transfer - Bin to Bin': 'bg-teal-100 text-teal-800',
    'Picking - Production': 'bg-purple-100 text-purple-800',
    'Picking - Sales Order': 'bg-pink-100 text-pink-800'
  }
  return classes[type as keyof typeof classes] || 'bg-gray-100 text-gray-800'
}

const getStatusClass = (status: string) => {
  const classes = {
    'Pending': 'bg-yellow-100 text-yellow-800',
    'In Progress': 'bg-blue-100 text-blue-800',
    'Completed': 'bg-green-100 text-green-800'
  }
  return classes[status as keyof typeof classes] || 'bg-gray-100 text-gray-800'
}

const getItemStatusClass = (status: string) => {
  const classes = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'in_progress': 'bg-blue-100 text-blue-800',
    'completed': 'bg-green-100 text-green-800'
  }
  return classes[status as keyof typeof classes] || 'bg-gray-100 text-gray-800'
}

// Initialize data
onMounted(() => {
  console.log('Mounted - Transfer Orders:', transferOrders.value)
  console.log('Filtered:', filteredTransferOrders.value)
})
</script>
