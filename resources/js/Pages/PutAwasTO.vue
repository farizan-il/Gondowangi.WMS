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
            <label class="block text-sm font-medium text-gray-700 mb-2">Search Batch/Lot</label>
            <input v-model="searchBatch" type="text" placeholder="BCH-001..."
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
        </div>
      </div>

      <!-- Transfer Orders Table -->
      <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">Daftar Transfer Order</h2>
          <p class="text-sm text-gray-600 mt-1">Total: {{ filteredTransferOrders.length }} TO</p>
        </div>

        <div class="overflow-x-auto desktop-to-table-wrapper">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TO Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch/Lot</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creation Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-if="!filteredTransferOrders.length">
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

              <tr v-for="to in filteredTransferOrders" :key="to.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="font-medium text-gray-900">{{ to.toNumber }}</div>
                  <div v-if="to.reservationNo" class="text-sm text-gray-500">{{ to.reservationNo }}</div>
                  <div v-if="to.hasRejected" class="mt-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 uppercase tracking-wide">
                      Reject Items
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ to.items.length > 0 ? to.items[0].batchLot : 'N/A' }}
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
                      class="flex items-center gap-2 bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-2 rounded-lg transition-colors">
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
                      class="flex items-center gap-2 bg-green-100 text-green-700 hover:bg-green-200 px-3 py-2 rounded-lg transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293H15M6 6h3m5 0h3">
                        </path>
                      </svg>
                      <span class="text-xs font-medium">Kerjakan</span>
                    </button>
                    <button @click="printTO(to)"
                      class="flex items-center gap-2 bg-purple-100 text-purple-700 hover:bg-purple-200 px-3 py-2 rounded-lg transition-colors">
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

        <!-- Mobile Card View (visible only on mobile via CSS) -->
        <div class="mobile-to-cards-container px-4 pb-4">
          <!-- Empty State for Mobile -->
          <div v-if="!filteredTransferOrders.length" class="mobile-to-card">
            <div class="flex flex-col items-center justify-center py-8">
              <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
              </svg>
              <h3 class="text-base font-medium text-gray-900 mb-1">Belum Ada Transfer Order</h3>
              <p class="text-gray-500 text-sm text-center">Generate putaway dari material QC Released untuk membuat TO</p>
            </div>
          </div>

          <!-- Mobile Cards -->
          <div v-for="to in filteredTransferOrders" :key="'mobile-' + to.id" class="mobile-to-card">
            <!-- Card Header -->
            <div class="mobile-to-card-header">
              <div>
                <div class="font-semibold text-gray-900">{{ to.toNumber }}</div>
                <div v-if="to.reservationNo" class="text-xs text-gray-500 mt-0.5">{{ to.reservationNo }}</div>
                <div v-if="to.hasRejected" class="mt-1">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 uppercase tracking-wide">
                    Reject Items
                  </span>
                </div>
              </div>
              <span :class="getStatusClass(to.status)"
                class="px-2 py-1 text-xs font-semibold rounded-full">
                {{ to.status }}
              </span>
            </div>

            <!-- Card Body -->
            <div class="mobile-to-card-body">
              <div class="mobile-to-card-row">
                <span class="mobile-to-card-label">Batch/Lot</span>
                <span class="mobile-to-card-value">{{ to.items.length > 0 ? to.items[0].batchLot : 'N/A' }}</span>
              </div>
              <div class="mobile-to-card-row">
                <span class="mobile-to-card-label">Tanggal</span>
                <span class="mobile-to-card-value">{{ formatDate(to.creationDate) }}</span>
              </div>
              <div class="mobile-to-card-row">
                <span class="mobile-to-card-label">Warehouse</span>
                <span class="mobile-to-card-value">{{ to.warehouse }}</span>
              </div>
              <div class="mobile-to-card-row">
                <span class="mobile-to-card-label">Items</span>
                <span class="mobile-to-card-value">{{ to.items.length }} item(s)</span>
              </div>
              <div class="mobile-to-card-row col-span-2">
                <span class="mobile-to-card-label">Tipe</span>
                <span :class="getTypeClass(to.type)" class="px-2 py-0.5 text-xs font-semibold rounded-full mt-1 inline-block">
                  {{ to.type }}
                </span>
              </div>
            </div>

            <!-- Card Actions -->
            <div class="mobile-to-card-actions">
              <button @click="viewDetail(to)"
                class="flex items-center gap-1.5 bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                <span class="font-medium">Detail</span>
              </button>
              <button v-if="to.status !== 'Completed'" @click="executeTO(to)"
                class="flex items-center gap-1.5 bg-green-100 text-green-700 hover:bg-green-200 px-3 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293H15M6 6h3m5 0h3"></path>
                </svg>
                <span class="font-medium">Kerjakan</span>
              </button>
              <button @click="printTO(to)"
                class="flex items-center gap-1.5 bg-purple-100 text-purple-700 hover:bg-purple-200 px-3 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2v4h10z"></path>
                </svg>
                <span class="font-medium">Cetak</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Auto Putaway Generation Modal -->
      <div v-if="showAutoPutawayModal"
        class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]"
        style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg p-6 w-full max-w-6xl max-h-screen overflow-y-auto">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold">Generate Auto Putaway dari QC Released</h3>
            <button @click="showAutoPutawayModal = false" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <div class="space-y-6">
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
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(material, index) in qcReleasedMaterials" :key="index" class="hover:bg-gray-50">
                      <td class="px-4 py-3">
                        <input type="checkbox" v-model="material.selected"
                          class="rounded border-gray-300 focus:ring-blue-500">
                      </td>
                      <td class="px-4 py-3 text-sm text-gray-900">{{ material.itemCode }}</td>
                      <td class="px-4 py-3 text-sm text-gray-900">{{ material.materialName }} 
                        <div v-if="material.status === 'REJECTED'" class="mt-1">
                          <span class="px-2 py-0.5 bg-red-100 text-red-700 text-[10px] font-bold rounded uppercase">Reject</span>
                        </div>
                        <div v-if="material.isFromReturn" class="mt-1">
                          <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold rounded uppercase">Return Produksi</span>
                        </div>
                      </td>
                      <td class="px-4 py-3 text-sm text-gray-900">{{ material.currentBin }}</td>
                      <td class="px-4 py-3 text-sm text-gray-900">{{ formatQty(material.qty, material.category) }}</td>
                      <td class="px-4 py-3 text-sm text-gray-900">
                        {{ material.uom }}
                      </td>
                      <td class="px-4 py-3">
                        <!-- IF SPLIT: Show info message -->
                        <div v-if="material.isSplit" class="text-sm text-gray-500 italic">
                          Bin Tujuan Sudah displit
                        </div>
                        
                        <!-- IF REJECTED: Show Select Dropdown for RJT Bins -->
                        <div v-else-if="material.status === 'REJECTED'" class="w-64">
                          <select 
                            v-model="material.destinationBin"
                            :disabled="!material.selected"
                            class="w-full px-3 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="{'bg-gray-100': !material.selected}"
                          >
                            <option value="">Pilih Bin Reject (RJT)</option>
                            <option v-for="bin in rejectBins" :key="bin.code" :value="bin.code">
                              {{ bin.code }}
                            </option>
                          </select>
                        </div>

                        <!-- IF RELEASED: Show Current Searchable Input -->
                        <div v-else class="relative w-full sm:w-64">
                          <input 
                            type="text" 
                            v-model="material.binSearchQuery"
                            @input="handleBinSearchInput(material)"
                            @focus="material.showBinSuggestions = true"
                            @blur="hideBinSuggestions(material)"
                            :disabled="!material.selected"
                            placeholder="Ketik kode bin..."
                            class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="{'bg-gray-100': !material.selected}"
                          />
                          
                          <button @click="showBinDetails(material)" class="absolute right-2 top-2 text-blue-600 hover:text-blue-800"
                            title="Lihat detail bin" :disabled="!material.destinationBin">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                          </button>
                          
                          
                        </div>
                        <!-- Suggestions Dropdown - positioned relative to parent -->
                        <div v-if="material.showBinSuggestions && getFilteredBins(material.binSearchQuery).length > 0" 
                            class="absolute left-0 right-0 z-[99999] text-center mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-y-auto">
                          <div 
                            v-for="bin in getFilteredBins(material.binSearchQuery)" 
                            :key="bin.code"
                            @mousedown.prevent="selectBin(material, bin)"
                            class="px-3 py-2 text-sm cursor-pointer hover:bg-blue-50 text-gray-900 border-b border-gray-100 last:border-0"
                          >
                            {{ bin.code }} [{{ bin.zone }} - {{ bin.currentItems }}/{{ bin.capacity }}]
                          </div>
                        </div>
                      </td>
                      <td class="px-4 py-3">
                        <button
                          v-if="material.selected && material.status === 'RELEASED' && !material.isSplit"
                          @click="openSplitModal(material)"
                          class="px-3 py-1 text-xs font-medium bg-purple-100 text-purple-700 hover:bg-purple-200 rounded transition-colors flex items-center gap-1"
                        >
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                          </svg>
                          Split
                        </button>
                        <div v-else-if="material.isSplit" class="flex items-center gap-2">
                          <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded">
                            Split ({{ material.splitAllocations?.length || 0 }})
                          </span>
                          <button
                            @click="openSplitModal(material)"
                            class="text-blue-600 hover:text-blue-800 text-xs"
                          >
                            Edit
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
        class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]"
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

      <!-- Split Material Allocation Modal -->
      <div v-if="showSplitModal && currentSplitMaterial"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[9999]">
        <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
          <!-- Header -->
          <div class="flex justify-between items-center mb-6">
            <div>
              <h3 class="text-xl font-semibold">Split Material Allocation</h3>
              <p class="text-sm text-gray-600 mt-1">
                {{ currentSplitMaterial.itemCode }} - {{ currentSplitMaterial.materialName }}
              </p>
              <p class="text-sm font-medium text-blue-600 mt-1">
                Total Available: {{ formatQty(currentSplitMaterial.qty, currentSplitMaterial.category) }} {{ currentSplitMaterial.uom }}
              </p>
            </div>
            <button @click="closeSplitModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Step 1: Specify Number of Locations -->
          <div v-if="splitAllocations.length === 0" class="space-y-4">
            <label class="block text-sm font-medium text-gray-700">
              Berapa bin location yang akan digunakan?
            </label>
            <div class="flex items-center gap-4">
              <input 
                v-model.number="splitLocationCount" 
                type="number" 
                min="2" 
                max="10"
                class="w-32 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
              />
              <button 
                @click="generateAllocationForm"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium"
              >
                Generate Form
              </button>
            </div>
          </div>

          <!-- Step 2: Allocation Form -->
          <div v-else class="space-y-4">
            <div class="overflow-x-auto">
              <table class="w-full border border-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">No</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Bin Location</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Qty ({{ currentSplitMaterial.uom }})</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(allocation, index) in splitAllocations" :key="index" class="border-t">
                    <td class="px-4 py-3 text-sm">{{ index + 1 }}</td>
                    <td class="px-4 py-3">
                      <div class="relative">
                        <input 
                          v-model="allocation.binCode"
                          @input="() => { allocation.showSuggestions = true }"
                          @focus="() => { allocation.showSuggestions = true }"
                          @blur="() => setTimeout(() => { allocation.showSuggestions = false }, 200)"
                          type="text"
                          placeholder="Ketik kode bin..."
                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500"
                        />
                        
                        <!-- Info Button -->
                        <button 
                          v-if="allocation.binCode && getBinByCode(allocation.binCode)"
                          @click.stop="showSplitBinInfo(allocation.binCode)"
                          type="button"
                          class="absolute right-2 top-2.5 text-blue-600 hover:text-blue-800"
                          title="Lihat isi bin"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                        </button>
                        
                        
                      </div>
                      <!-- Autocomplete Suggestions -->
                        <div 
                          v-if="allocation.showSuggestions && getFilteredBins(allocation.binCode).length > 0"
                          class="absolute z-[99999] w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-y-auto"
                        >
                          <div 
                            v-for="bin in getFilteredBins(allocation.binCode)" 
                            :key="bin.code"
                            @mousedown.prevent="selectSplitBin(allocation, bin)"
                            class="px-3 py-2 text-sm cursor-pointer hover:bg-blue-50 border-b border-gray-100 last:border-0"
                          >
                            <div class="flex justify-between items-center">
                              <span class="font-medium text-gray-900">{{ bin.code }}</span>
                              <span class="text-xs text-gray-500">{{ bin.zone }}</span>
                            </div>
                            <div class="text-xs text-gray-600 mt-0.5">
                              {{ bin.currentItems }}/{{ bin.capacity }} items
                            </div>
                          </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                      <input 
                        v-model.number="allocation.qty"
                        type="number"
                        min="0.01"
                        step="0.01"
                        :max="currentSplitMaterial.qty"
                        class="w-32 px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500"
                      />
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex items-center gap-2">
                        <!-- Tombol Alihkan Sisa - muncul di row terakhir jika ada remaining -->
                        <button 
                          v-if="index === splitAllocations.length - 1 && remainingQty > 0"
                          @click="allocateRemaining(index)"
                          class="px-2 py-1 bg-blue-100 text-blue-700 hover:bg-blue-200 text-xs font-medium rounded flex items-center gap-1"
                          title="Otomatis isi dengan sisa qty"
                        >
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                          </svg>
                          Alihkan Sisa
                        </button>
                        <button 
                          v-if="splitAllocations.length > 2"
                          @click="removeAllocation(index)"
                          class="text-red-600 hover:text-red-800 text-xs font-medium"
                        >
                          Remove
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Add Location Button -->
            <button 
              @click="addAllocation"
              class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              Add Location
            </button>

            <!-- Summary -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
              <div class="flex justify-between text-sm">
                <span class="font-medium">Total Allocated:</span>
                <span :class="remainingQty < 0 ? 'text-red-600 font-bold' : isRemainingZero ? 'text-green-600 font-bold' : 'text-gray-900'">
                  {{ formatQty(totalAllocatedQty, currentSplitMaterial.category) }} / {{ formatQty(currentSplitMaterial.qty, currentSplitMaterial.category) }} {{ currentSplitMaterial.uom }}
                </span>
              </div>
              <div class="flex justify-between text-sm mt-2">
                <span class="font-medium">Remaining:</span>
                <span :class="isRemainingZero ? 'text-green-600 font-bold' : remainingQty < 0 ? 'text-red-600 font-bold' : 'text-yellow-600 font-medium'">
                  {{ formatQty(remainingQty, currentSplitMaterial.category) }} {{ currentSplitMaterial.uom }}
                  <span v-if="isRemainingZero" class="ml-1">✓</span>
                </span>
              </div>
              <p v-if="remainingQty < -0.0001" class="text-xs text-red-600 mt-2 font-medium">
                ⚠️ Total allocated exceeds available quantity! Remaining tidak boleh minus.
              </p>
              <p v-else-if="!isRemainingZero && totalAllocatedQty > 0" class="text-xs text-yellow-600 mt-2 font-medium">
                ⚠️ Remaining harus 0 untuk bisa Apply Split. Masih ada {{ formatQty(remainingQty, currentSplitMaterial.category) }} {{ currentSplitMaterial.uom }} yang belum dialokasikan.
              </p>
              <p v-else-if="isRemainingZero" class="text-xs text-green-600 mt-2 font-medium">
                ✓ Semua qty sudah dialokasikan. Siap untuk Apply Split!
              </p>
            </div>
          </div>

          <!-- Footer Actions -->
          <div class="flex gap-3 justify-end mt-6 pt-4 border-t">
            <button 
              v-if="splitAllocations.length > 0"
              @click="resetSplitForm"
              class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50"
            >
              Reset Form
            </button>
            <button @click="closeSplitModal" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
              Cancel
            </button>
            <button 
              v-if="splitAllocations.length > 0"
              @click="applySplit"
              :disabled="!isValidSplit"
              :class="isValidSplit ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400 cursor-not-allowed'"
              class="px-4 py-2 text-white rounded-md font-medium"
            >
              Apply Split
            </button>
          </div>
        </div>
      </div>

      <!-- Detail/Execute TO Modal -->
      <div v-if="showDetailModal"
        class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]"
        style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg p-6 w-full max-w-7xl max-h-screen overflow-y-auto">
          <div class="flex justify-between items-center mb-6">
            <div>
              <h3 class="text-xl font-semibold">{{ selectedTO?.isExecuting ? 'Kerjakan' : 'Detail' }} Transfer Order</h3>
              <p class="text-gray-600 mt-1">{{ selectedTO?.toNumber }}</p>
            </div>
            <button @click="closeDetailModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">TO Number</label>
                <p class="text-sm text-gray-900 mt-1">{{ selectedTO?.toNumber }}</p>
              </div>
              <div v-if="selectedTO && selectedTO.items.length > 0 && selectedTO.items.every(item => item.batchLot === selectedTO.items[0].batchLot)">
                <label class="block text-sm font-medium text-gray-700">Batch/Lot Utama</label>
                <p class="text-sm text-gray-900 mt-1">{{ selectedTO.items[0].batchLot }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Transaction Type</label>
                <p class="text-sm text-gray-900 mt-1">{{ selectedTO?.type }}</p>
              </div>
              <div v-if="selectedTO?.reservationNo">
                <label class="block text-sm font-medium text-gray-700">No Reservasi</label>
                <p class="text-sm text-gray-900 mt-1">{{ selectedTO.reservationNo }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <span :class="getStatusClass(selectedTO?.status || '')"
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full mt-1">
                  {{ selectedTO?.status }}
                </span>
              </div>
            </div>
          </div>

          <div class="mb-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Daftar Item</h4>
            <div class="overflow-x-auto">
              <table class="w-full border border-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Item</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Material</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch/Lot</th> 
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source Bin</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dest Bin</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">UoM</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th v-if="selectedTO?.isExecuting"
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(item, index) in selectedTO?.items" :key="index" class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ index + 1 }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.itemCode }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.materialName }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.batchLot }}</td> 
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.sourceBin }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.destBin }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.qty }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.uom }}</td>
                    <td class="px-4 py-3">
                      <span :class="getItemStatusClass(item.status)"
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                        {{ item.status }}
                      </span>
                    </td>
                    <td v-if="selectedTO?.isExecuting" class="px-4 py-3">
                      <div v-if="item.status !== 'completed'" class="flex gap-2">
                        <button @click="startScanWizard(item)"
                          :class="item.boxScanned && item.sourceBinScanned && item.destBinScanned ? 'bg-green-100 text-green-800' : 'bg-blue-600 text-white hover:bg-blue-700'"
                          class="px-3 py-1 text-xs font-medium rounded">
                          {{ item.boxScanned && item.sourceBinScanned && item.destBinScanned ? '✓ Selesai' : 'Mulai Scan' }}
                        </button>
                        
                        <div class="flex items-center gap-1">
                          <span :class="item.boxScanned ? 'bg-green-500' : 'bg-gray-300'"
                            class="w-2 h-2 rounded-full" title="Box"></span>
                          <span :class="item.sourceBinScanned ? 'bg-green-500' : 'bg-gray-300'"
                            class="w-2 h-2 rounded-full" title="Source"></span>
                          <span :class="item.destBinScanned ? 'bg-green-500' : 'bg-gray-300'"
                            class="w-2 h-2 rounded-full" title="Dest"></span>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="flex gap-3 justify-end">
            <button @click="closeDetailModal"
              class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
              {{ selectedTO?.isExecuting ? 'Batal' : 'Tutup' }}
            </button>
            <button v-if="selectedTO?.isExecuting" @click="completeTO" :disabled="!canCompleteTO"
              :class="canCompleteTO ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed'"
              class="px-4 py-2 text-white rounded-md font-medium">
              Selesaikan TO
            </button>
          </div>
        </div>
      </div>

      <!-- QR Scanner Wizard Modal dengan Camera -->
      <div v-if="showQRModal"
        class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]"
        style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
          
          <!-- Header Modal -->
          <div class="flex justify-between items-center mb-4">
            <div>
              <h3 class="text-lg font-semibold">Scan Wizard - {{ currentWizardStep }}/3</h3>
              <p class="text-sm text-gray-600">{{ wizardStepTitle }}</p>
            </div>
            <button @click="closeQRModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- Progress Bar -->
          <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-medium text-gray-700">Progress</span>
              <span class="text-xs font-medium text-blue-600">{{ Math.round((currentWizardStep / 3) * 100) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                :style="{ width: `${(currentWizardStep / 3) * 100}%` }"></div>
            </div>
          </div>

          <!-- Step Indicators -->
          <div class="flex items-center justify-between mb-6">
            <div class="flex flex-col items-center flex-1">
              <div :class="currentWizardStep >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600'"
                class="w-10 h-10 rounded-full flex items-center justify-center font-bold mb-2">
                <span v-if="currentWizardStep > 1">✓</span>
                <span v-else>1</span>
              </div>
              <span class="text-xs text-center font-medium">Scan Box</span>
            </div>
            <div class="h-1 flex-1 mx-2" :class="currentWizardStep >= 2 ? 'bg-blue-600' : 'bg-gray-300'"></div>
            <div class="flex flex-col items-center flex-1">
              <div :class="currentWizardStep >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600'"
                class="w-10 h-10 rounded-full flex items-center justify-center font-bold mb-2">
                <span v-if="currentWizardStep > 2">✓</span>
                <span v-else>2</span>
              </div>
              <span class="text-xs text-center font-medium">Source Bin</span>
            </div>
            <div class="h-1 flex-1 mx-2" :class="currentWizardStep >= 3 ? 'bg-blue-600' : 'bg-gray-300'"></div>
            <div class="flex flex-col items-center flex-1">
              <div :class="currentWizardStep >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600'"
                class="w-10 h-10 rounded-full flex items-center justify-center font-bold mb-2">
                3
              </div>
              <span class="text-xs text-center font-medium">Dest Bin</span>
            </div>
          </div>

          <!-- [PERBAIKAN] Camera Scanner Area -->
          <div class="text-center mb-4">
            
            <!-- Tampilkan area ini HANYA jika mode kamera aktif -->
            <div v-if="useCameraMode">
              <!-- Elemen div#qr-reader HARUS ada di DOM dan terlihat SEBELUM start() dipanggil -->
              <!-- Kita berikan min-height agar layout tidak 'loncat' saat video dimuat -->
              <div id="qr-reader"
                   class="w-full rounded-lg overflow-hidden border-2 border-blue-500 mb-2"
                   style="min-height: 250px;">
              </div>
              
              <!-- Tampilkan status kamera berdasarkan isCameraActive -->
              <div v-if="isCameraActive" class="mt-2 text-xs text-gray-500">
                <svg class="w-4 h-4 inline-block animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Kamera Aktif - Arahkan ke QR Code
              </div>
              <div v-else class="mt-2 text-xs text-gray-500">
                <!-- Ini adalah placeholder "loading" saat kamera sedang dimulai -->
                <div class="w-full flex items-center justify-center" style="min-height: 250px;">
                  <div class="text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="text-gray-500 font-medium">Mengaktifkan Kamera...</p>
                    <p class="text-xs text-gray-400 mt-1">Harap tunggu sebentar</p>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Blok placeholder lama yang menggunakan "v-if="!isCameraActive || !useCameraMode"" dihapus -->
            <!-- Logika untuk mode manual (v-if="!useCameraMode") sudah ditangani di bawah -->

          </div>

          <!-- Toggle Camera / Manual Input -->
          <div class="mb-4">
            <button @click="toggleScanMode"
              class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-sm font-medium flex items-center justify-center gap-2">
              <svg v-if="!useCameraMode" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                </path>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
              </svg>
              {{ useCameraMode ? 'Switch to Manual Input' : 'Switch to Camera Scanner' }}
            </button>
          </div>

          <!-- Manual Input (Alternative) -->
          <div v-if="!useCameraMode" class="space-y-4">
            <input v-model="qrInput"
              ref="qrInputRef"
              type="text"
              :placeholder="`Input ${wizardStepTitle} Manual`"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              @keyup.enter="processWizardStep"
              autofocus>

            <div v-if="expectedValue" class="text-xs text-gray-500 text-left bg-gray-50 p-2 rounded">
              <span class="font-medium">Expected Value:</span> {{ expectedValue }}
            </div>
          </div>

          <!-- Last Scanned Result -->
          <div v-if="lastScannedValue" class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center gap-2">
              <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <div>
                <p class="text-xs font-medium text-green-800">Terakhir Di-scan:</p>
                <p class="text-sm text-green-900 font-mono">{{ lastScannedValue }}</p>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3 mt-6">
            <button @click="closeQRModal"
              class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
              Batal
            </button>
            <button v-if="!useCameraMode" @click="processWizardStep"
              :disabled="!qrInput.trim()"
              :class="qrInput.trim() ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400 cursor-not-allowed'"
              class="flex-1 px-4 py-2 text-white rounded-md font-medium">
              {{ currentWizardStep === 3 ? 'Selesai' : 'Lanjut' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal for Bin Validation -->
    <div v-if="showConfirmModal"
      class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[99999]"
      style="background-color: rgba(43, 51, 63, 0.67);">
      <div class="bg-white rounded-lg max-w-md w-full mx-4 shadow-2xl">
        <div class="p-6">
          <!-- Icon & Title -->
          <div class="flex items-start gap-4 mb-4">
            <div :class="confirmModalType === 'merge' ? 'bg-blue-100' : 'bg-orange-100'" 
                 class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center">
              <!-- Merge Icon -->
              <svg v-if="confirmModalType === 'merge'" class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
              <!-- Warning Icon -->
              <svg v-else class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <div class="flex-1">
              <h3 :class="confirmModalType === 'merge' ? 'text-blue-900' : 'text-orange-900'" 
                  class="text-lg font-semibold mb-1">
                {{ confirmModalTitle }}
              </h3>
            </div>
          </div>

          <!-- Message -->
          <div class="mb-6 text-sm text-gray-700 leading-relaxed space-y-2" v-html="confirmModalMessage"></div>

          <!-- Actions -->
          <div class="flex gap-3">
            <button @click="cancelConfirm"
              class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
              Batal
            </button>
            <button @click="proceedConfirm"
              :class="confirmModalType === 'merge' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-orange-600 hover:bg-orange-700'"
              class="flex-1 px-4 py-2.5 text-white rounded-lg font-medium transition-colors shadow-sm">
              Lanjutkan
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed, onMounted, nextTick, onUnmounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import { Html5Qrcode } from 'html5-qrcode'

const LOCAL_STORAGE_KEY = 'in_progress_transfer_order';

// Interfaces
interface TOItem {
  id?: number
  itemCode: string
  materialName: string
  batchLot: string
  sourceBin: string
  destBin: string
  qty: number
  uom: string
  status: 'pending' | 'in_progress' | 'completed'
  boxScanned: boolean
  sourceBinScanned: boolean
  destBinScanned: boolean
}

interface TransferOrder {
  id: string
  toNumber: string
  creationDate: Date | string
  warehouse: string
  type: string
  status: 'Pending' | 'In Progress' | 'Completed'
  reservationNo?: string
  items: TOItem[]
  isExecuting?: boolean
  hasRejected?: boolean
}

interface SplitAllocation {
  binCode: string
  binId?: number
  qty: number
  wadah?: number
  showSuggestions?: boolean
}

interface QCReleasedMaterial {
  itemCode: string
  materialName: string
  currentBin: string
  qty: number
  uom: string
  selected: boolean
  destinationBin: string
  stockId: number
  batchLot?: string
  expDate?: string
  status: string // RELEASED | REJECTED
  binSearchQuery?: string // Untuk autocomplete
  showBinSuggestions?: boolean // State dropdown
  isSplit?: boolean // Flag if material is split
  splitAllocations?: SplitAllocation[] // Array of allocations
  isFromReturn?: boolean // Flag for return production materials
  category?: string // Material category for qty formatting
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
    batchLot: string // Added for validation
    qty: number
    uom: string
  }>
}

const saveTOState = (to: TransferOrder) => {
  try {
    localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(to));
    console.log(`State saved for TO: ${to.toNumber}`);
  } catch (e) {
    console.error("Error saving state to localStorage", e);
  }
};

const loadTOState = (): TransferOrder | null => {
  try {
    const json = localStorage.getItem(LOCAL_STORAGE_KEY);
    if (json) {
      return JSON.parse(json) as TransferOrder;
    }
  } catch (e) {
    console.error("Error loading state from localStorage", e);
  }
  return null;
};

const clearTOState = () => {
  try {
    localStorage.removeItem(LOCAL_STORAGE_KEY);
    console.log("State cleared from localStorage.");
  } catch (e) {
    console.error("Error clearing state from localStorage", e);
  }
};

const page = usePage()
const transferOrders = ref<TransferOrder[]>((page.props.transferOrders as TransferOrder[]) || [])

// Update local state when props change (e.g. after Inertia reload)
watch(() => page.props.transferOrders, (newOrders) => {
  transferOrders.value = (newOrders as TransferOrder[]) || []
}, { deep: true })

// Reactive data
const qcReleasedMaterials = ref<QCReleasedMaterial[]>([])
const availableBins = ref<BinInfo[]>([])
const rejectBins = ref<BinInfo[]>([])
const searchQuery = ref('')
const searchBatch = ref('')
const filterType = ref('')
const filterStatus = ref('')
const filterWarehouse = ref('')

// Modals
const showAutoPutawayModal = ref(false)
const showBinModal = ref(false)
const showDetailModal = ref(false)
const showQRModal = ref(false)
const showSplitModal = ref(false)
const showConfirmModal = ref(false) // NEW: Confirmation modal for bin validation

// Selected data
const selectedTO = ref<TransferOrder | null>(null)
const selectedBinInfo = ref<BinInfo | null>(null)
const currentItem = ref<TOItem | null>(null)
const qrInput = ref('')
const qrInputRef = ref<HTMLInputElement | null>(null)

// Wizard states
const currentWizardStep = ref(1) // 1: Box, 2: Source Bin, 3: Dest Bin

// Camera Scanner states
const html5QrCode = ref<Html5Qrcode | null>(null)
const isCameraActive = ref(false)
const useCameraMode = ref(true)
const lastScannedValue = ref('')

// Split modal states
const currentSplitMaterial = ref<QCReleasedMaterial | null>(null)
const splitLocationCount = ref(2)
const splitAllocations = ref<SplitAllocation[]>([])

// Confirmation modal states
const confirmModalType = ref<'merge' | 'warning'>('merge')
const confirmModalTitle = ref('')
const confirmModalMessage = ref('')
let confirmResolve: ((value: boolean) => void) | null = null

// Computed
const filteredTransferOrders = computed(() => {
  return transferOrders.value.filter(to => {
    const matchesSearch = !searchQuery.value || to.toNumber.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesType = !filterType.value || to.type === filterType.value
    const matchesStatus = !filterStatus.value || to.status === filterStatus.value
    const matchesWarehouse = !filterWarehouse.value || to.warehouse.includes(filterWarehouse.value)
    
    // [PERUBAHAN 8: Logika Filtering Batch/Lot]
    const matchesBatch = !searchBatch.value || to.items.some(item => 
      item.batchLot && item.batchLot.toLowerCase().includes(searchBatch.value.toLowerCase())
    )
    
    return matchesSearch && matchesBatch && matchesType && matchesStatus && matchesWarehouse
  })
})

const selectedMaterials = computed(() => {
  return qcReleasedMaterials.value.filter(m => {
    if (!m.selected) return false
    // If split, check if has allocations. If not split, check if has destinationBin
    return m.isSplit ? (m.splitAllocations && m.splitAllocations.length > 0) : m.destinationBin
  })
})

const canCompleteTO = computed(() => {
  if (!selectedTO.value) return false
  return selectedTO.value.items.every(item =>
    item.boxScanned && item.sourceBinScanned && item.destBinScanned
  )
})

// Split validation computed properties
const totalAllocatedQty = computed(() => {
  return splitAllocations.value.reduce((sum, a) => sum + (a.qty || 0), 0)
})

// Computed remaining qty for "Alihkan Sisa" button
const remainingQty = computed(() => {
  if (!currentSplitMaterial.value) return 0
  return currentSplitMaterial.value.qty - totalAllocatedQty.value
})

// Check if remaining is effectively zero (with epsilon tolerance for floating-point)
const isRemainingZero = computed(() => {
  return Math.abs(remainingQty.value) < 0.0001
})

const isValidSplit = computed(() => {
  if (!currentSplitMaterial.value || splitAllocations.value.length < 2) return false
  
  // Check total qty must be exactly equal to available (remaining must be 0)
  // Using epsilon tolerance to handle floating-point precision issues
  const epsilon = 0.0001
  const difference = Math.abs(totalAllocatedQty.value - currentSplitMaterial.value.qty)
  if (difference > epsilon) return false
  
  // Check all allocations have bin and qty > 0
  if (splitAllocations.value.some(a => !a.binCode || !a.qty || a.qty <= 0)) return false
  
  // Check no duplicate bins
  const bins = splitAllocations.value.map(a => a.binCode)
  if (new Set(bins).size !== bins.length) return false
  
  return true
})

const wizardStepTitle = computed(() => {
  switch (currentWizardStep.value) {
    case 1: return 'Scan Box QR Code'
    case 2: return 'Scan Source Bin'
    case 3: return 'Scan Destination Bin'
    default: return ''
  }
})

const expectedValue = computed(() => {
  if (!currentItem.value) return ''
  
  switch (currentWizardStep.value) {
    case 1: return `Box with ${currentItem.value.itemCode}`
    case 2: return currentItem.value.sourceBin
    case 3: return currentItem.value.destBin
    default: return ''
  }
})

// Camera Scanner Methods
const startCameraScanner = async () => {
  isCameraActive.value = false
  try {
    const element = document.getElementById("qr-reader")
    if (!element) {
      console.error("qr-reader element not found!")
      return
    }
    
    await stopCameraScanner(); 
    html5QrCode.value = new Html5Qrcode("qr-reader")

    const config = { 
      fps: 10, 
      qrbox: { width: 250, height: 250 },
      aspectRatio: 1.0
    }

    await html5QrCode.value.start(
      { facingMode: "environment" },
      config,
      onScanSuccess,
      onScanError
    )

    isCameraActive.value = true
  } catch (err: any) {
    console.error("Error starting camera:", err)
    alert(`Gagal memulai kamera: ${err.message}. Pastikan tidak ada aplikasi lain yang menggunakan. Coba lagi atau ganti ke mode manual.`);
  }
}

const stopCameraScanner = async () => {
  if (html5QrCode.value) {
    try {
      if (typeof html5QrCode.value.getState === 'function' && html5QrCode.value.getState() === 2) {
        await html5QrCode.value.stop();
      }
    } catch (err) {
      console.error("Error attempting to stop camera:", err);
    } finally {
      try {
        await html5QrCode.value.clear(); 
      } catch (clearErr) {
        console.warn("Error clearing html5QrCode UI:", clearErr);
      }
      html5QrCode.value = null; 
      isCameraActive.value = false;
    }
  }
  isCameraActive.value = false;
}

const onScanSuccess = (decodedText: string, decodedResult: any) => {
  console.log("QR Code detected:", decodedText)
  lastScannedValue.value = decodedText
  qrInput.value = decodedText
  
  setTimeout(() => {
    processWizardStep()
  }, 500) 
}

const onScanError = (errorMessage: string) => {
  // Ignore errors during scanning (too verbose)
  // console.warn("QR Scan error:", errorMessage)
}

const toggleScanMode = async () => {
  if (useCameraMode.value) {
    await stopCameraScanner()
    useCameraMode.value = false
    nextTick(() => {
      qrInputRef.value?.focus()
    })
  } else {
    useCameraMode.value = true
    qrInput.value = ''
    nextTick(async () => {
      await startCameraScanner()
    })
  }
}

// Methods
const generateAutoPutaway = async () => {
  try {
    // Ganti dengan implementasi Inertia/fetch Anda
    const materialResponse = await fetch('/transaction/putaway-transfer/qc-released')
    if (!materialResponse.ok) throw new Error('Failed to fetch materials')
    const materials = await materialResponse.json()
    qcReleasedMaterials.value = materials.map((m: any) => ({ 
      ...m, 
      selected: false, 
      destinationBin: '',
      binSearchQuery: '',
      showBinSuggestions: false
    }))

    const binResponse = await fetch('/transaction/putaway-transfer/available-bins')
    if (!binResponse.ok) throw new Error('Failed to fetch bins')
    const bins = await binResponse.json()
    availableBins.value = bins

    const rejectResponse = await fetch('/transaction/putaway-transfer/reject-bins')
    if (!rejectResponse.ok) throw new Error('Failed to fetch reject bins')
    const rBins = await rejectResponse.json()
    rejectBins.value = rBins

    showAutoPutawayModal.value = true
  } catch (error) {
    console.error('Error loading data:', error)
    // Ganti alert dengan notifikasi yang lebih baik
    alert('Gagal memuat data')
  }
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

// Validate bin selection for return materials
const validateBinForReturnMaterial = async (material: QCReleasedMaterial, binCode: string): Promise<boolean> => {
  if (!material.isFromReturn) return true; // Only validate for return materials
  
  try {
    const response = await fetch(`/transaction/putaway-transfer/bin-details?binCode=${binCode}`);
    if (!response.ok) throw new Error('Failed to fetch bin details');
    const binData = await response.json();
    
    if (!binData.materials || binData.materials.length === 0) {
      // Empty bin, no validation needed
      return true;
    }
    
    // Check if same material + same batch exists
    const sameMaterialBatch = binData.materials.find((m: any) => 
      m.itemCode === material.itemCode && m.batchLot === material.batchLot
    );
    
    if (sameMaterialBatch) {
      // Scenario 1: Show merge info modal  
      const formattedQty = formatQty(sameMaterialBatch.qty, material.category);
      return await showConfirmation(
        'merge',
        'Material Akan Digabungkan',
        `<div class="space-y-2">
          <p class="font-medium">Bin <span class="text-blue-600">${binCode}</span> sudah berisi:</p>
          <div class="bg-blue-50 border border-blue-200 rounded p-3 text-sm">
            <div><span class="font-medium">Material:</span> ${sameMaterialBatch.itemCode} - ${sameMaterialBatch.materialName}</div>
            <div><span class="font-medium">Batch:</span> ${sameMaterialBatch.batchLot}</div>
            <div><span class="font-medium">Qty:</span> ${formattedQty} ${sameMaterialBatch.uom}</div>
          </div>
          <p class="text-gray-600 mt-2">Material return Anda akan digabungkan dengan stok yang sudah ada.</p>
        </div>`
      );
    }
    
    // Check if different material exists
    const differentMaterial = binData.materials.find((m: any) => m.itemCode !== material.itemCode);
    
    if (differentMaterial) {
      // Scenario 2: Show warning modal
      return await showConfirmation(
        'warning',
        '⚠️ Peringatan: Bin Berisi Material Berbeda',
        `<div class="space-y-2">
          <div class="bg-orange-50 border border-orange-200 rounded p-3 text-sm">
            <p class="font-medium mb-2">Bin <span class="text-orange-600">${binCode}</span> saat ini berisi:</p>
            <div><span class="font-medium">Material:</span> ${differentMaterial.itemCode} - ${differentMaterial.materialName}</div>
          </div>
          <div class="bg-gray-50 border border-gray-200 rounded p-3 text-sm">
            <p class="font-medium mb-2">Anda akan memindahkan:</p>
            <div><span class="font-medium">Material:</span> ${material.itemCode} - ${material.materialName}</div>
          </div>
          <p class="text-orange-700 font-medium mt-3">Apakah Anda yakin ingin melanjutkan?</p>
        </div>`
      );
    }
    
    return true;
  } catch (error) {
    console.error('Error validating bin:', error);
    return true; // On error, allow anyway
  }
};

// Show confirmation modal and return promise
const showConfirmation = (type: 'merge' | 'warning', title: string, message: string): Promise<boolean> => {
  return new Promise((resolve) => {
    confirmModalType.value = type;
    confirmModalTitle.value = title;
    confirmModalMessage.value = message;
    confirmResolve = resolve;
    showConfirmModal.value = true;
  });
};

const proceedConfirm = () => {
  showConfirmModal.value = false;
  if (confirmResolve) confirmResolve(true);
  confirmResolve = null;
};

const cancelConfirm = () => {
  showConfirmModal.value = false;
  if (confirmResolve) confirmResolve(false);
  confirmResolve = null;
};

// Select bin for material (with validation for return materials)
const selectBin = async (material: QCReleasedMaterial, bin: any) => {
  // Validate for return materials
  const isValid = await validateBinForReturnMaterial(material, bin.code);
  
  if (!isValid) {
    // User canceled
    return;
  }
  
  // Proceed with setting destination
  material.destinationBin = bin.code;
  material.binSearchQuery = bin.code;
  material.showBinSuggestions = false;
};

// Select bin for split allocation (with validation for return materials)
const selectSplitBin = async (allocation: any, bin: any) => {
  // Validate for return materials
  if (currentSplitMaterial.value?.isFromReturn) {
    const isValid = await validateBinForReturnMaterial(currentSplitMaterial.value, bin.code);
    if (!isValid) return;
  }
  
  // Proceed with setting bin
  allocation.binCode = bin.code;
  allocation.showSuggestions = false;
};

// Split Modal Functions
const openSplitModal = (material: QCReleasedMaterial) => {
  currentSplitMaterial.value = material
  
  // If already split, load existing allocations
  if (material.isSplit && material.splitAllocations) {
    splitAllocations.value = [...material.splitAllocations]
  } else {
    splitAllocations.value = []
    splitLocationCount.value = 2
  }
  
  showSplitModal.value = true
}

const closeSplitModal = () => {
  showSplitModal.value = false
  currentSplitMaterial.value = null
  splitAllocations.value = []
  splitLocationCount.value = 2
}

const generateAllocationForm = () => {
  if (splitLocationCount.value < 2 || splitLocationCount.value > 10) {
    alert('Jumlah location harus antara 2-10')
    return
  }
  
  splitAllocations.value = Array.from({ length: splitLocationCount.value }, () => ({
    binCode: '',
    qty: 0,
    showSuggestions: false
  }))
}

const addAllocation = () => {
  splitAllocations.value.push({ binCode: '', qty: 0, showSuggestions: false })
}

const removeAllocation = (index: number) => {
  if (splitAllocations.value.length > 2) {
    splitAllocations.value.splice(index, 1)
  }
}

// Function to auto-fill remaining qty to the specified allocation
const allocateRemaining = (index: number) => {
  if (!currentSplitMaterial.value || remainingQty.value <= 0) return
  
  // Add remaining qty to the current allocation's qty
  splitAllocations.value[index].qty = (splitAllocations.value[index].qty || 0) + remainingQty.value
}

const resetSplitForm = () => {
  splitAllocations.value = []
  splitLocationCount.value = 2
}

const applySplit = () => {
  if (!currentSplitMaterial.value || !isValidSplit.value) {
    alert('Please fill all required fields correctly')
    return
  }
  
  // Apply split to material
  currentSplitMaterial.value.isSplit = true
  currentSplitMaterial.value.splitAllocations = [...splitAllocations.value]
  currentSplitMaterial.value.destinationBin = '' // Clear single destination
  
  closeSplitModal()
  alert(`Material berhasil di-split ke ${splitAllocations.value.length} locations`)
}

// Helper Functions for Split Bin Selection
const getBinByCode = (binCode: string) => {
  return availableBins.value.find(b => b.code.toLowerCase() === binCode.toLowerCase())
}
  
// Function removed - now using the version with validation above (line ~1295)

const showSplitBinInfo = async (binCode: string) => {
  try {
    const response = await fetch(`/transaction/putaway-transfer/bin-details?binCode=${binCode}`)
    if (!response.ok) throw new Error('Failed to fetch bin details')
    const binInfo = await response.json()
    selectedBinInfo.value = binInfo
    showBinModal.value = true
  } catch (error: any) {
    console.error('Error fetching bin details:', error)
    alert('Gagal mengambil detail bin: ' + error.message)
  }
}

const confirmAutoPutaway = async () => {
  if (!selectedMaterials.value.length) {
    alert('Pilih minimal 1 material untuk di-putaway!')
    return
  }
  
  // Ganti confirm() dengan modal konfirmasi kustom
  if (!confirm('Anda yakin ingin generate TO untuk item terpilih?')) return

  try {
    // Implementasi Inertia.post
    router.post('/transaction/putaway-transfer/generate', {
      materials: selectedMaterials.value.map(m => ({
        stockId: m.stockId,
        destinationBin: m.destinationBin,
        qty: m.qty,
        isSplit: m.isSplit || false,
        splitAllocations: m.isSplit ? m.splitAllocations : undefined
      }))
    }, {
      onSuccess: (page) => {
        showAutoPutawayModal.value = false
        if (page.props.flash && page.props.flash.success) {
          alert(page.props.flash.success)
        } else {
          alert('Putaway TO berhasil digenerate!')
        }
      },
      onError: (errors) => {
        console.error('Error generating putaway:', errors)
        const errorMsg = errors.message || Object.values(errors)[0] || 'Gagal generate putaway TO'
        alert('Gagal: ' + errorMsg)
      }
    })
    
  } catch (error: any) {
    console.error('Error submitting putaway:', error)
    alert('Gagal generate putaway TO: ' + error.message)
  }
}

const viewDetail = (to: TransferOrder) => {
  selectedTO.value = { ...to, isExecuting: false }
  showDetailModal.value = true
}

const executeTO = (to: TransferOrder) => {
  // 1. Coba muat state dari local storage
  const savedState = loadTOState();

  if (savedState && savedState.id === to.id) {
    // 2. Jika ada state tersimpan untuk TO ini, gunakan state tersebut
    selectedTO.value = savedState;
    console.log(`Resuming TO ${to.toNumber} from saved session.`);
  } else {
    // 3. Jika tidak ada, inisialisasi normal
    selectedTO.value = {
      ...to,
      isExecuting: true,
      status: 'In Progress', 
      items: to.items.map(item => ({ 
        ...item, 
        status: item.status === 'completed' ? 'completed' : 'pending' as const, 
        actualQty: item.actualQty !== undefined ? item.actualQty : item.qty 
      }))
    };
  }

  showDetailModal.value = true;
  
  // REMOVED: Partial save not needed - will save only on completion
}

const closeDetailModal = () => {
  // REMOVED: Partial save removed - user can resume by clicking "Execute" again
  if (selectedTO.value?.isExecuting && selectedTO.value.status !== 'Completed') {
    if (usePage().props.flash?.success) {
        alert(usePage().props.flash.success);
    }
  } else {
    // Clear state jika TO sudah selesai atau dibatalkan
    clearTOState(); 
  }
  selectedTO.value = null
  showDetailModal.value = false
}

const startScanWizard = async (item: TOItem) => {
  currentItem.value = item;
  
  if (item.boxScanned && item.sourceBinScanned && item.destBinScanned) {
    alert('Item ini sudah selesai di-scan.');
    return;
  } else if (item.boxScanned && item.sourceBinScanned) {
    currentWizardStep.value = 3; 
  } else if (item.boxScanned) {
    currentWizardStep.value = 2; 
  } else {
    currentWizardStep.value = 1; 
  }

  qrInput.value = '';
  lastScannedValue.value = '';
  showQRModal.value = true;
  
  await nextTick();
  await new Promise(resolve => setTimeout(resolve, 500)); 
  
  if (useCameraMode.value) {
    await startCameraScanner();
  } else {
    nextTick(() => {
      qrInputRef.value?.focus();
    });
  }
}

const closeQRModal = async () => {
  await stopCameraScanner()
  showQRModal.value = false
  currentItem.value = null
  currentWizardStep.value = 1
  qrInput.value = ''
  lastScannedValue.value = ''
  
  // [PERSISTENCE] Simpan state setelah menutup QR modal
  // REMOVED: No partial save needed after box scan
}

const processWizardStep = () => {
  if (!currentItem.value || !qrInput.value.trim()) {
    alert('Silakan input QR code!')
    return
  }

  const item = currentItem.value
  const scannedValue = qrInput.value.trim()

  let parsedBinCode = ''
  let isValid = false
  let expected = ''

  try {
      const parsedJson = JSON.parse(scannedValue)
      if (parsedJson && parsedJson.bin_code) {
        parsedBinCode = String(parsedJson.bin_code).toUpperCase()
      } else {
          parsedBinCode = scannedValue.toUpperCase()
      }
    } 
    catch (e) {
      parsedBinCode = scannedValue.toUpperCase()
    }

  switch (currentWizardStep.value) {
    case 1: 
      expected = item.itemCode
      isValid = scannedValue.includes(item.itemCode)
      if (isValid) {
        item.boxScanned = true
        currentWizardStep.value = 2
      } else {
        alert(`Box QR tidak sesuai! Expected: ${expected} (atau mengandung ${expected}), Scanned: ${scannedValue}`)
      }
      break

    case 2: 
      expected = item.sourceBin
      isValid = (parsedBinCode === expected.toUpperCase())
      if (isValid) {
        item.sourceBinScanned = true
        currentWizardStep.value = 3
      } else {
        alert(`Source Bin tidak sesuai! Expected: ${expected}, Scanned Code: ${parsedBinCode || scannedValue}`)
      }
      break

    case 3: 
      expected = item.destBin
      isValid = (parsedBinCode === expected.toUpperCase())
      
      if (isValid) {
          item.destBinScanned = true
          item.status = 'completed'
          
          alert('✓ Scan selesai! Semua tahap berhasil.')
          closeQRModal()
        } else {
          alert(`Destination Bin tidak sesuai! Expected: ${expected}, Scanned Code: ${parsedBinCode || scannedValue}`)
        }
        break
  }
  
  // Reset input
  qrInput.value = ''
  lastScannedValue.value = ''

  // REMOVED: No partial save needed after each scan step

  if (isValid && currentWizardStep.value <= 3 && !useCameraMode.value) {
    nextTick(() => {
      qrInputRef.value?.focus()
    })
  } else if (!isValid && !useCameraMode.value) {
      nextTick(() => {
      qrInputRef.value?.select()
    })
  }
}


const completeTO = async () => {
  if (!selectedTO.value || !canCompleteTO.value) {
    alert('Pastikan semua item sudah di-scan dan qty actual sudah diisi!')
    return
  }
  
  if (!confirm(`Apakah Anda yakin ingin menyelesaikan TO ${selectedTO.value.toNumber}?`)) {
    return
  }

  // Prepare data to send
  const dataToSend = {
    items: selectedTO.value.items.map(item => ({
      id: item.id,
      status: item.status,
      boxScanned: item.boxScanned,
      sourceBinScanned: item.sourceBinScanned,
      destBinScanned: item.destBinScanned
    }))
  };

  // Log the data being sent for debugging
  console.log('Sending TO completion data:', dataToSend);
  console.log('TO ID:', selectedTO.value.id);

  try {
    router.post(`/transaction/putaway-transfer/complete/${selectedTO.value.id}`, dataToSend, {
      onSuccess: (page) => {
        console.log('Success response:', page);
        alert('Transfer Order berhasil diselesaikan!')
        
        // Update status di list utama secara langsung
        if (selectedTO.value) {
            const index = transferOrders.value.findIndex(t => t.id === selectedTO.value?.id)
            if (index !== -1) {
                transferOrders.value[index].status = 'Completed'
            }
        }

        // Update status selectedTO secara lokal
        if (selectedTO.value) {
            selectedTO.value.status = 'Completed' 
            selectedTO.value.isExecuting = false 
        }
        clearTOState() 
        closeDetailModal() 
      },
      onError: (errors) => {
        console.error('❌ Error completing TO:', errors);
        
        // Format error messages untuk ditampilkan
        let errorMessage = '❌ GAGAL MENYELESAIKAN TRANSFER ORDER\n\n';
        
        if (typeof errors === 'object' && errors !== null) {
          // Check if it's a validation error
          if (Object.keys(errors).length > 0) {
            errorMessage += '📋 DETAIL ERROR:\n';
            Object.entries(errors).forEach(([field, messages]) => {
              if (Array.isArray(messages)) {
                errorMessage += `\n• ${field}: ${messages.join(', ')}`;
              } else {
                errorMessage += `\n• ${field}: ${messages}`;
              }
            });
          } else {
            errorMessage += 'Terjadi kesalahan yang tidak diketahui.\n';
            errorMessage += 'Error: ' + JSON.stringify(errors);
          }
        } else if (typeof errors === 'string') {
          errorMessage += errors;
        } else {
          errorMessage += 'Terjadi kesalahan yang tidak diketahui.';
        }
        
        errorMessage += '\n\n💡 Silakan cek console browser untuk detail lebih lanjut (F12)';
        
        alert(errorMessage);
      },
      onFinish: () => {
        console.log('Request finished');
      }
    })
    
  } catch (error: any) {
    console.error('❌ Exception in completeTO:', error);
    
    let errorMessage = '❌ TERJADI KESALAHAN\n\n';
    errorMessage += 'Error: ' + (error.message || 'Unknown error');
    errorMessage += '\n\n💡 Detail error ada di console browser (tekan F12)';
    
    alert(errorMessage);
  }
}


const printTO = (to: TransferOrder) => {
  const printContent = `
    <h1 style="text-align: center; font-family: Arial, sans-serif; margin-bottom: 20px;">TRANSFER ORDER</h1>
    <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
      <tr>
        <td style="padding: 5px; width: 150px;"><strong>TO Number</strong></td>
        <td style="padding: 5px;">: ${to.toNumber}</td>
        <td style="padding: 5px; width: 150px;"><strong>Transaction Type</strong></td>
        <td style="padding: 5px;">: ${to.type}</td>
      </tr>
      <tr>
        <td style="padding: 5px;"><strong>Creation Date</strong></td>
        <td style="padding: 5px;">: ${formatDate(to.creationDate)}</td>
        <td style="padding: 5px;"><strong>Warehouse</strong></td>
        <td style="padding: 5px;">: ${to.warehouse}</td>
      </tr>
      ${to.reservationNo ? `
      <tr>
        <td style="padding: 5px;"><strong>No Reservasi</strong></td>
        <td style="padding: 5px;" colspan="3">: ${to.reservationNo}</td>
      </tr>` : ''}
    </table>
    
    <h3 style="font-family: Arial, sans-serif; margin-top: 25px; margin-bottom: 10px;">DAFTAR ITEM:</h3>
    <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
      <thead style="background-color: #f4f4f4;">
        <tr>
          <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">No</th>
          <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Kode Item</th>
          <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Nama Material</th>
          <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Batch/Lot</th> <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Source Bin</th>
          <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Dest Bin</th>
          <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Qty</th>
          <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">UoM</th>
        </tr>
      </thead>
      <tbody>
        ${to.items.map((item, index) => `
          <tr>
            <td style="border: 1px solid #ddd; padding: 8px;">${index + 1}</td>
            <td style="border: 1px solid #ddd; padding: 8px;">${item.itemCode}</td>
            <td style="border: 1px solid #ddd; padding: 8px;">${item.materialName}</td>
            <td style="border: 1px solid #ddd; padding: 8px;">${item.batchLot}</td> <td style="border: 1px solid #ddd; padding: 8px;">${item.sourceBin}</td>
            <td style="border: 1px solid #ddd; padding: 8px;">${item.destBin}</td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${(item.actualQty !== undefined && item.actualQty !== null) ? item.actualQty : item.qty}</td>
            <td style="border: 1px solid #ddd; padding: 8px;">${item.uom}</td>
          </tr>
        `).join('')}
      </tbody>
    </table>

    <table style="width: 100%; font-family: Arial, sans-serif; font-size: 12px; margin-top: 50px; text-align: center;">
      <tr>
        <td style="padding: 10px;">
          Diserahkan:
          <br><br><br><br>
          (${usePage().props.auth?.user?.name || 'Logistik'})
          <br>
          Tgl: ${formatDateIndonesian(to.creationDate)}
        </td>
        <td style="padding: 10px;">
          Diterima:
          <br><br><br><br>
          (Rohiman / Hermansyah)
          <br>
          Tgl: ${formatDateIndonesian(to.creationDate)}
        </td>
        <td style="padding: 10px;">
          Mengetahui:
          <br><br><br><br>
          (Reza Rizky Permadi)
          <br>
          Tgl: ${formatDateIndonesian(to.creationDate)}
        </td>
      </tr>
    </table>
  `

  const printWindow = window.open('', '_blank')
  if (printWindow) {
    const scriptTag = 'script'
    printWindow.document.write(`
      <html>
        <head>
          <title>Transfer Order - ${to.toNumber}</title>
          <style>
            @media print {
              body { -webkit-print-color-adjust: exact; }
            }
          </style>
        </head>
        <body>
          ${printContent}
          <${scriptTag}>
            window.onload = function() {
              window.print();
              window.onafterprint = function() {
                 window.close();
              }
            }
          </${scriptTag}>
        </body>
      </html>
    `)
    printWindow.document.close()
  }
}

// Utility functions
const formatDate = (date: Date | string) => {
  const dateObj = typeof date === 'string' ? new Date(date) : date
  
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

const formatDateIndonesian = (date: Date | string) => {
  const dateObj = typeof date === 'string' ? new Date(date) : date
  
  if (isNaN(dateObj.getTime())) {
    return 'Invalid Date'
  }
  
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  }).format(dateObj)
}

// Format quantity based on material category
const formatQty = (qty: number | undefined, category?: string) => {
  if (qty === null || qty === undefined) return '0'
  const num = parseFloat(qty.toString())
  if (isNaN(num)) return '0'
  
  // Packaging materials: round to whole number if no decimal part
  if (category === 'Packaging' || category === 'Packaging Material') {
    // If number is already whole (e.g., 10.0000), return as integer with thousand separator
    if (num === Math.floor(num)) {
      return Math.round(num).toLocaleString('id-ID')
    }
    // If has decimals (e.g., 10.102), show up to 4 decimal places with thousand separator
    const formatted = num.toFixed(4).replace(/\.?0+$/, '')
    const parts = formatted.split('.')
    parts[0] = parseInt(parts[0]).toLocaleString('id-ID')
    return parts.join(',')
  }
  
  // Raw materials: max 4 decimal places
  if (category === 'Raw Material') {
    // If it's a whole number, show with thousand separator
    if (num === Math.floor(num)) {
      return num.toLocaleString('id-ID')
    }
    
    // For decimals, keep up to 4 decimal places, remove trailing zeros, add thousand separator
    const formatted = num.toFixed(4).replace(/\.?0+$/, '')
    const parts = formatted.split('.')
    parts[0] = parseInt(parts[0]).toLocaleString('id-ID')
    return parts.join(',')
  }
  
  // Default: show up to 4 decimals for any other category with thousand separator
  if (num === Math.floor(num)) {
    return num.toLocaleString('id-ID')
  }
  const formatted = num.toFixed(4).replace(/\.?0+$/, '')
  const parts = formatted.split('.')
  parts[0] = parseInt(parts[0]).toLocaleString('id-ID')
  return parts.join(',')
}


const getTypeClass = (type: string) => {
  const classes: Record<string, string> = {
    'Putaway - QC Release': 'bg-blue-100 text-blue-800',
    'Transfer - Internal': 'bg-green-100 text-green-800',
    'Transfer - Bin to Bin': 'bg-teal-100 text-teal-800',
    'Picking - Production': 'bg-purple-100 text-purple-800',
    'Picking - Sales Order': 'bg-pink-100 text-pink-800'
  }
  return classes[type] || 'bg-gray-100 text-gray-800'
}

const getStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    'Pending': 'bg-yellow-100 text-yellow-800',
    'In Progress': 'bg-blue-100 text-blue-800',
    'Completed': 'bg-green-100 text-green-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getItemStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'in_progress': 'bg-blue-100 text-blue-800',
    'completed': 'bg-green-100 text-green-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

// Autocomplete Helpers
const getFilteredBins = (query: string | undefined) => {
  if (!query) return availableBins.value.slice(0, 50); // Show max 50 default
  const lowerQuery = query.toLowerCase();
  
  return availableBins.value.filter(bin => 
    bin.code.toLowerCase().includes(lowerQuery) || 
    bin.zone.toLowerCase().includes(lowerQuery)
  ).slice(0, 50);
}

const handleBinSearchInput = (material: QCReleasedMaterial) => {
  material.destinationBin = ''; // Reset selection on typing
}
  
// Function removed - now using the async version with validation above (line ~1279)

const hideBinSuggestions = (material: QCReleasedMaterial) => {
  // Delay closing to allow click event to register
  setTimeout(() => {
    material.showBinSuggestions = false;
    // If not valid selection, maybe revert or clear? 
    // Here we just keep whatever text calls or destinationBin state
    if (material.destinationBin) {
       // Optional: Enforce format if user typed something but selected nothing, 
       // or let them type part of it. 
       // For now, if they selected a bin, ensure text matches.
       const bin = availableBins.value.find(b => b.code === material.destinationBin);
       if (bin) {
          material.binSearchQuery = `${bin.code} [${bin.zone} - ${bin.currentItems}/${bin.capacity}]`;
       }
    }
  }, 200);
}

onMounted(() => {
  console.log('Mounted - Transfer Orders:', transferOrders.value)
})

onMounted(() => {
  // Coba muat state TO yang sedang berjalan saat komponen dimuat
  const pendingTO = loadTOState();
  if (pendingTO) {
    // Cari TO yang sesuai di daftar props yang dimuat dari backend
    const currentTOInList = transferOrders.value.find(to => to.id === pendingTO.id);
    
    if (currentTOInList) {
        // Gabungkan data yang tersimpan dengan data dari server
        const mergedTO = { 
            ...currentTOInList, 
            ...pendingTO,
            // Pastikan flag eksekusi dan status ter-override dari local storage
            isExecuting: true, 
            status: 'In Progress' 
        };

        selectedTO.value = mergedTO;
        showDetailModal.value = true;
        
        alert(`Melanjutkan Transfer Order: ${pendingTO.toNumber}. Progress yang tersimpan telah dimuat.`);
    } else {
        // Jika TO yang tersimpan tidak ditemukan lagi (mungkin sudah diselesaikan di perangkat lain)
        clearTOState();
    }
  }
})
onUnmounted(async () => {
  await stopCameraScanner()
})
</script>

<style scoped>
/* Custom styles for QR Scanner */
/* [PERBAIKAN] Pastikan #qr-reader dan video di dalamnya ditampilkan dengan benar */
#qr-reader {
  border: 2px solid #3b82f6;
  background-color: #000; /* Latar belakang hitam saat video dimuat */
}

/* Ini penting agar video dari html5-qrcode responsif */
:deep(#qr-reader video) {
  width: 100% !important;
  height: auto !important;
  border-radius: 0.5rem;
}

/* Hide unnecessary elements from html5-qrcode */
:deep(#qr-reader__dashboard_section) {
  display: none !important;
}

:deep(#qr-reader__dashboard_section_csr) {
  display: none !important;
}

/* [PERBAIKAN] Gunakan :deep() untuk menargetkan elemen di dalam #qr-reader */
:deep(#qr-reader__status_span) {
  display: none !important; /* Sembunyikan pesan status default */
}

/* ===== MOBILE RESPONSIVE STYLES ===== */
/* Target mobile devices only (< 640px) - preserves desktop/tablet appearance */

@media (max-width: 639px) {
  /* Main container padding adjustment */
  .min-h-screen.bg-gray-50.p-6 {
    padding: 1rem;
  }

  /* Header text adjustment */
  .text-2xl.font-bold {
    font-size: 1.25rem;
  }

  /* Generate Auto Putaway button - full width on mobile */
  .bg-blue-600.hover\:bg-blue-700.text-white.px-6.py-2 {
    width: 100%;
    justify-content: center;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
  }

  /* Filter grid - single column on mobile */
  .grid.grid-cols-1.md\:grid-cols-4 {
    gap: 0.75rem;
  }

  /* Hide table on mobile, show mobile cards only */
  .overflow-x-auto table {
    display: none;
  }

  /* Modal adjustments for mobile */
  .fixed.inset-0 > div.bg-white.rounded-lg {
    margin: 0.5rem;
    max-height: calc(100vh - 1rem);
    width: calc(100% - 1rem);
  }

  /* Full screen modals on very small screens */
  .fixed.inset-0 > div.bg-white.rounded-lg.p-6.w-full.max-w-6xl,
  .fixed.inset-0 > div.bg-white.rounded-lg.p-6.w-full.max-w-7xl,
  .fixed.inset-0 > div.bg-white.rounded-lg.p-6.w-full.max-w-4xl {
    margin: 0;
    border-radius: 0;
    max-height: 100vh;
    width: 100%;
    padding: 1rem;
  }

  /* Modal header adjustment */
  .fixed.inset-0 > div .text-xl.font-semibold,
  .fixed.inset-0 > div .text-lg.font-semibold {
    font-size: 1rem;
  }

  /* Modal grid adjustments */
  .grid.grid-cols-2.md\:grid-cols-4 {
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
  }

  /* Tables inside modals - horizontal scroll with min-width */
  .overflow-x-auto table {
    display: table;
    min-width: 600px;
    font-size: 0.75rem;
  }

  .overflow-x-auto table th,
  .overflow-x-auto table td {
    padding: 0.5rem;
    white-space: nowrap;
  }

  /* Action buttons in table - stack vertically */
  .flex.items-center.justify-end.gap-2 {
    flex-direction: column;
    gap: 0.5rem;
  }

  .flex.items-center.justify-end.gap-2 button {
    width: 100%;
    justify-content: center;
  }

  /* Modal footer buttons - full width */
  .flex.gap-3.justify-end {
    flex-direction: column-reverse;
  }

  .flex.gap-3.justify-end button {
    width: 100%;
  }

  /* Split modal table adjustments - only for qty input */
  .w-32 {
    width: 100%;
  }

  /* Note: .w-64 removed - using responsive classes in HTML instead */

  /* QR Scanner modal - full screen on mobile */
  .fixed.inset-0 > div.bg-white.rounded-lg.p-6.w-full.max-w-md {
    margin: 0;
    border-radius: 0;
    max-height: 100vh;
    width: 100%;
    height: 100vh;
    overflow-y: auto;
  }

  /* Step indicators in scanner - smaller on mobile */
  .w-10.h-10 {
    width: 2rem;
    height: 2rem;
    font-size: 0.75rem;
  }

  /* Confirmation modal stays centered but with margin */
  .fixed.inset-0 > div.bg-white.rounded-lg.max-w-md {
    margin: 1rem;
  }
}

/* ===== MOBILE CARD VIEW FOR TRANSFER ORDERS TABLE ===== */
/* This creates a card-based layout for the TO list on mobile */

/* Hide mobile cards container on desktop/tablet */
.mobile-to-cards-container {
  display: none;
}

/* Hide table wrapper on mobile, show cards */
@media (max-width: 639px) {
  /* Hide ONLY the main TO list table wrapper on mobile - NOT modal tables */
  .desktop-to-table-wrapper {
    display: none;
  }
  
  /* Show mobile cards container */
  .mobile-to-cards-container {
    display: block;
  }
  
  /* Ensure modal tables remain visible with horizontal scroll */
  .fixed .overflow-x-auto {
    display: block !important;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  
  .fixed .overflow-x-auto table {
    display: table !important;
    min-width: 600px;
    font-size: 0.75rem;
  }
  
  .fixed .overflow-x-auto table th,
  .fixed .overflow-x-auto table td {
    padding: 0.5rem;
    white-space: nowrap;
  }
}

/* Mobile card view styles */
.mobile-to-card {
  display: none;
}

@media (max-width: 639px) {
  .mobile-to-card {
    display: block;
    background: white;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
  }

  .mobile-to-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #e5e7eb;
  }

  .mobile-to-card-body {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.5rem;
    font-size: 0.875rem;
    margin-bottom: 0.75rem;
  }

  .mobile-to-card-row {
    display: flex;
    flex-direction: column;
  }

  .mobile-to-card-label {
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 500;
    text-transform: uppercase;
  }

  .mobile-to-card-value {
    color: #111827;
    font-weight: 500;
  }

  .mobile-to-card-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    padding-top: 0.75rem;
    border-top: 1px solid #e5e7eb;
  }

  .mobile-to-card-actions button {
    flex: 1;
    min-width: calc(50% - 0.25rem);
    justify-content: center;
    padding: 0.625rem 0.75rem;
    font-size: 0.75rem;
  }
}

/* ===== TOUCH-FRIENDLY IMPROVEMENTS ===== */
@media (max-width: 639px) {
  /* Larger touch targets for inputs */
  input[type="text"],
  input[type="number"],
  select {
    min-height: 44px;
    font-size: 16px; /* Prevents iOS zoom on focus */
  }

  /* Larger touch targets for buttons */
  button {
    min-height: 44px;
  }

  /* Checkbox larger touch area */
  input[type="checkbox"] {
    width: 1.25rem;
    height: 1.25rem;
  }
}
</style>

