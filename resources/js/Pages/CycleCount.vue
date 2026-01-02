<template>
  <AppLayout title="Cycle Count">
    <div class="min-h-screen bg-gray-50 p-2 sm:p-4 md:p-6 transition-colors duration-300">
       <!-- <div>
           <h2 class="text-xl font-bold text-gray-800">Cycle Count Inventory</h2>
           <p class="text-sm text-gray-500">
             Scan QR Lokasi & Serial Number untuk validasi.
           </p>
        </div> -->
        <div class="bg-white p-4 rounded-lg shadow mb-6 border border-gray-100">
          <div class="flex flex-col md:flex-row justify-between gap-4">
            
            <div class="flex flex-wrap gap-2 w-full md:w-3/4">

                <select v-model="filterForm.status" @change="applyFilter" class="w-full sm:w-40 text-sm border rounded bg-gray-50 py-1.5">
                    <option value="">Semua Status</option>
                    <option value="DRAFT">Draft / Progress</option>
                    <option value="REVIEW_NEEDED">Butuh Approval</option>
                    <option value="APPROVED">Selesai (Approved)</option>
                </select>

                <select v-model="filterForm.frequency" @change="applyFilter" class="w-full sm:w-48 text-sm border border-orange-300 rounded bg-orange-50 py-1.5 text-orange-800 font-semibold">
                    <option value="">Semua Frekuensi</option>
                    <option value="never">Belum Pernah Opname</option>
                    <option value="rare">Jarang (< 2x Opname)</option>
                    <option value="often">Sering (> 5x Opname)</option>
                </select>

                <select v-model="filterForm.category" @change="applyFilter" class="w-full sm:w-48 text-sm border rounded bg-gray-50 py-1.5">
                    <option value="">Semua Kategori</option>
                    <option value="RAW MATERIAL">Raw Material</option>
                    <option value="PACKAGING">Packaging Material</option>
                </select>

                <button @click="resetFilter" class="px-3 py-1.5 text-sm text-gray-500 hover:text-red-500 underline">Reset</button>
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                <button 
                    v-if="selectedItems.length > 0"
                    @click="openBulkModal"
                    class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg shadow transition-colors flex items-center justify-center gap-2 animate-pulse whitespace-nowrap"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Ulangi ({{ selectedItems.length }})</span>
                </button>
                <div class="relative flex-grow sm:flex-grow-0 flex gap-2">
                    <button 
                        @click="openScanner(null, 'search')"
                        class="bg-blue-200 hover:bg-blue-300 text-blue-700 p-2 rounded-lg shadow transition-colors flex items-center justify-center"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>

                    <div class="relative w-full sm:w-64">
                        <input 
                            type="text" 
                            v-model="searchQuery" 
                            @keydown.enter="handleSearch"
                            placeholder="Scan QR Material / Cari..." 
                            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <button 
                            v-if="searchQuery" 
                            @click="clearSearch"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button 
                @click="submitOpname"
                :disabled="form.processing"
                class="flex items-center justify-center gap-2 px-4 py-2 rounded shadow transition-colors whitespace-nowrap"
                :class="form.processing ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 text-white'"
                >
                <span v-if="form.processing">Menyimpan...</span>
                <span v-else>Submit ke Supervisor</span>
                </button>
            </div>
          </div>
      </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-xl shadow p-4 border-l-4 border-blue-500">
              <div class="flex justify-between items-start">
                  <div>
                      <p class="text-xs text-gray-500 uppercase font-bold">Total SKU (Stock)</p>
                      <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ formatNumber(statistics.total_sku) }}</h3>
                  </div>
                  <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                  </div>
              </div>
          </div>

          <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-500">
              <div class="flex justify-between items-start">
                  <div>
                      <p class="text-xs text-gray-500 uppercase font-bold">Sudah Opname (Bulan Ini)</p>
                      <h3 class="text-2xl font-bold text-gray-800 mt-1">
                          {{ formatNumber(statistics.counted_items) }} 
                          <span class="text-xs font-normal text-gray-400">/ {{ formatNumber(statistics.total_sku) }}</span>
                      </h3>
                      <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                          <div class="bg-green-500 h-1.5 rounded-full" :style="`width: ${statistics.progress_percentage}%`"></div>
                      </div>
                  </div>
                  <div class="p-2 bg-green-100 rounded-lg text-green-600">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  </div>
              </div>
          </div>

          <div class="bg-white rounded-xl shadow p-4 border-l-4" :class="statistics.avg_accuracy >= 98 ? 'border-indigo-500' : 'border-red-500'">
              <div class="flex justify-between items-start">
                  <div>
                      <p class="text-xs text-gray-500 uppercase font-bold">Rata-rata Akurasi</p>
                      <h3 class="text-2xl font-bold mt-1" :class="statistics.avg_accuracy >= 98 ? 'text-indigo-600' : 'text-red-600'">
                          {{ statistics.avg_accuracy }}%
                      </h3>
                      <p class="text-[10px] text-gray-400 mt-1">Target: >98%</p>
                  </div>
                  <div class="p-2 rounded-lg" :class="statistics.avg_accuracy >= 98 ? 'bg-indigo-100 text-indigo-600' : 'bg-red-100 text-red-600'">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                  </div>
              </div>
          </div>

          <div class="bg-white rounded-xl shadow p-4 border-l-4 border-orange-500 cursor-pointer hover:bg-orange-50 transition-colors" @click="setFilterUncounted">
              <div class="flex justify-between items-start">
                  <div>
                      <p class="text-xs text-gray-500 uppercase font-bold">Belum Pernah Opname</p>
                      <h3 class="text-2xl font-bold text-orange-600 mt-1">{{ formatNumber(statistics.never_counted) }}</h3>
                      <p class="text-[10px] text-orange-400 mt-1 font-bold underline">Klik untuk filter</p>
                  </div>
                  <div class="p-2 bg-orange-100 rounded-lg text-orange-600">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  </div>
              </div>
          </div>
      </div>

      <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="w-full border-collapse border border-blue-300 text-sm">
           <thead class="bg-blue-200 text-gray-800 font-semibold text-center">
            <tr>
              <th rowspan="2" class="border border-blue-400 p-2 align-middle w-10">
                  <input type="checkbox" @change="toggleSelectAll($event)" class="rounded text-blue-600 focus:ring-blue-500 h-4 w-4" :disabled="form.items.every(i => !isRepeatable(i))">
              </th>
              
              <th rowspan="2" class="border border-blue-400 p-2 align-middle">Serial Number</th>
              <th rowspan="2" class="border border-blue-400 p-2 align-middle">Product Name</th>
              <th rowspan="2" class="border border-blue-400 p-2 align-middle">Onhand</th>
              <th rowspan="2" class="border border-blue-400 p-2 align-middle">Loc</th>
              
              <th colspan="3" class="border border-blue-400 p-2 bg-blue-300">Input Warehouseman</th>
              <th colspan="2" class="border border-blue-400 p-2 bg-blue-300">Hasil</th>
              <th colspan="2" class="border border-blue-400 p-2 bg-yellow-200">Area Supervisor</th>
            </tr>
            <tr>
              <th class="border border-blue-400 p-2 bg-blue-100 w-32">Scan Serial</th>
              <th class="border border-blue-400 p-2 bg-blue-100 w-24">Scan Bin</th>
              <th class="border border-blue-400 p-2 bg-blue-100 w-20">Qty</th>
              <th class="border border-blue-400 p-2 bg-blue-100 w-20">Acc</th>
              <th class="border border-blue-400 p-2 bg-blue-100 w-20">In Acc</th>
              <th class="border border-blue-400 p-2 bg-yellow-100 w-40">Note SPV</th>
              <th class="border border-blue-400 p-2 bg-yellow-100 w-20">Action</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="(item, index) in form.items" :key="index">
            <tr class="hover:bg-gray-50 text-center text-gray-700" :class="isSelected(item) ? 'bg-orange-50' : ''">
              
              <td class="border border-gray-300 p-2 text-center">
                   <input type="checkbox" 
                          :value="item" 
                          v-model="selectedItems" 
                          :disabled="!isRepeatable(item)"
                          class="rounded text-blue-600 focus:ring-blue-500 h-4 w-4 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
              </td>

              <td class="border border-gray-300 p-2 bg-gray-50 text-xs font-mono font-bold text-blue-900">
                  {{ extractSerial(item.serial_number) }}
              </td>
              <td class="border border-gray-300 p-2 text-left bg-gray-50 text-xs truncate max-w-[150px]" :title="item.product_name">
                  {{ item.product_name }}
                  <div v-if="item.category" class="mt-1">
                      <span class="text-[10px] px-1.5 py-0.5 rounded font-bold border"
                        :class="getCategoryBadgeClass(item.category)">
                        {{ item.category }}
                      </span>
                  </div>
              </td>
              
              <td class="border border-gray-300 p-2 bg-gray-50 text-right whitespace-nowrap">
                  <span class="font-bold text-gray-800">
                      {{ formatNumber(item.onhand) }} {{ item.uom }}
                  </span>
                  <span v-if="item.inventory_status" 
                    class="ml-2 text-[10px] px-1.5 py-0.5 rounded font-bold border"
                    :class="getStatusBadgeClass(item.inventory_status)">
                    {{ item.inventory_status }}
                  </span>
              </td>

              <td class="border border-gray-300 p-2 bg-gray-50 font-bold">{{ item.location }}</td>

              <td class="border border-gray-300 p-1">
                <button type="button" @click="openScanner(item, 'scan_serial')"
                    class="w-full py-1 px-2 text-xs rounded border flex items-center justify-center gap-1 transition-colors"
                    :class="checkSerialMatch(item) ? 'bg-green-100 border-green-500 text-green-700 font-bold' : 'bg-white border-gray-300 hover:bg-gray-100'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4h2v-4zM6 6h12v12H6V6z" /></svg>
                    <span class="truncate max-w-[80px]">{{ item.scan_serial ? item.scan_serial : 'Scan SN' }}</span>
                </button>
              </td>

              <td class="border border-gray-300 p-1">
                 <button type="button" @click="openScanner(item, 'scan_bin')"
                    class="w-full py-1 px-2 text-xs rounded border flex items-center justify-center gap-1 transition-colors"
                    :class="checkBinMatch(item) ? 'bg-green-100 border-green-500 text-green-700 font-bold' : 'bg-white border-gray-300 hover:bg-gray-100'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    <span class="truncate max-w-[80px]">{{ item.scan_bin ? item.scan_bin : 'Bin' }}</span>
                </button>
              </td>

              <td class="border border-gray-300 p-1">
                 <input type="number" v-model.number="item.physical_qty" :disabled="!isUnlocked(item)"
                 class="w-full text-center border rounded text-xs py-1 font-bold"
                 :class="isUnlocked(item) ? 'bg-white border-blue-500 text-blue-800' : 'bg-gray-100 border-gray-200 text-gray-400 cursor-not-allowed'" placeholder="0">
              </td>

              <td class="border border-gray-300 p-2 text-xs font-bold" :class="getAccuracyColor(item)">{{ calculateAccuracy(item) }}%</td>
              <td class="border border-gray-300 p-2 text-xs font-bold" :class="getVarianceColor(item)">{{ calculateVariance(item) }}%</td>

              <td class="border border-gray-300 p-1 bg-yellow-50">
                  <input type="text" v-model="item.spv_note" 
                    class="w-full text-xs border border-yellow-300 rounded px-1 py-1 bg-white focus:ring-yellow-500 focus:border-yellow-500" 
                    placeholder="Catatan SPV...">
              </td>
              <td class="border border-gray-300 p-1 bg-yellow-50 text-center align-middle">
                  
                  <div v-if="item.status === 'DRAFT' || !item.status" class="flex flex-col items-center justify-center">
                      <span class="text-gray-400 text-[10px] italic">Waiting Submit</span>
                      <span v-if="!isUnlocked(item)" class="text-[9px] text-red-400 mt-1">(Belum Scan)</span>
                  </div>

                  <button v-else-if="item.status === 'REVIEW_NEEDED'" 
                    @click="approveItem(item)"
                    type="button" 
                    class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-bold py-1 px-3 rounded shadow flex items-center justify-center gap-1 mx-auto transition-transform active:scale-95 animate-pulse">
                      <span>Approve</span>
                  </button>

                  <span v-else-if="item.status === 'APPROVED'" class="text-green-600 font-bold text-[10px] border border-green-600 bg-green-50 px-2 py-0.5 rounded inline-block">
                      DONE
                  </span>
                  
                  <span v-else class="text-gray-400 text-[10px]">-</span>

              </td>
            </tr>
            
            <tr v-if="item.status === 'APPROVED' || (item.history && item.history_count > 0)" class="bg-gray-50">
              <td colspan="11" class="border border-gray-300 p-2"> <div class="flex items-center justify-between">
                  <button 
                    v-if="item.history && item.history_count > 0"
                    @click="toggleHistory(index)"
                    class="flex items-center gap-2 text-xs text-blue-600 hover:text-blue-800 font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform" :class="item.showHistory ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    <span>{{ item.showHistory ? 'Sembunyikan' : 'Lihat' }} Riwayat ({{ item.history_count }}x)</span>
                  </button>
                  
                  <span v-else class="text-xs text-gray-500 italic">
                    Material ini sudah selesai di-cycle count
                  </span>
                  
                  <button 
                    @click="repeatCycleCount(item)"
                    class="flex items-center gap-1 px-3 py-1 text-xs bg-orange-500 hover:bg-orange-600 text-white rounded shadow transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Ulangi Cycle Count</span>
                  </button>
                </div>
                
                <div v-if="item.showHistory && item.history && item.history_count > 0" class="mt-3 overflow-x-auto">
                   <table class="w-full text-xs border border-gray-300">
                    <thead class="bg-gray-200">
                      <tr>
                        <th class="border border-gray-300 p-2">Tanggal</th>
                        <th class="border border-gray-300 p-2">System Qty</th>
                        <th class="border border-gray-300 p-2">Physical Qty</th>
                        <th class="border border-gray-300 p-2">Accuracy</th>
                        <th class="border border-gray-300 p-2">Status</th>
                        <th class="border border-gray-300 p-2">Note SPV</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(h, hIndex) in item.history" :key="hIndex" class="hover:bg-gray-100">
                        <td class="border border-gray-300 p-2 text-center">{{ h.count_date }}</td>
                        <td class="border border-gray-300 p-2 text-right">{{ formatNumber(h.system_qty) }}</td>
                        <td class="border border-gray-300 p-2 text-right">{{ formatNumber(h.physical_qty) }}</td>
                        <td class="border border-gray-300 p-2 text-center font-bold" :class="h.accuracy === 100 ? 'text-green-600' : 'text-red-600'">{{ h.accuracy }}%</td>
                        <td class="border border-gray-300 p-2 text-center">
                          <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="h.status === 'APPROVED' ? 'bg-green-100 text-green-700 border border-green-500' : 'bg-gray-100 text-gray-600'">
                            {{ h.status }}
                          </span>
                        </td>
                        <td class="border border-gray-300 p-2">{{ h.spv_note || '-' }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </td>
            </tr>
            </template>
          </tbody>
        </table>
      </div>

      <div v-if="$page.props.flash.success" class="mb-4 p-4 bg-green-100 text-green-700 rounded border border-green-200">
        {{ $page.props.flash.success }}
      </div>

      <div v-if="showBulkModal" class="fixed inset-0 z-[70] flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 flex flex-col max-h-[90vh]">
            <div class="p-4 border-b bg-gray-50 flex justify-between items-center rounded-t-lg">
                <h3 class="text-lg font-bold text-gray-800">Konfirmasi Cycle Count Ulang</h3>
                <button @click="showBulkModal = false" class="text-gray-500 hover:text-red-500 text-xl font-bold">&times;</button>
            </div>
            
            <div class="p-6 overflow-y-auto">
                <p class="text-sm text-gray-600 mb-4">
                    Anda akan mengulang cycle count untuk <b>{{ selectedItems.length }} material</b> berikut. 
                    Data saat ini akan di-reset menjadi DRAFT dan Warehouseman harus melakukan scan ulang.
                </p>

                <div class="border rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 text-left">Kode Material</th>
                                <th class="p-2 text-left">Nama Produk</th>
                                <th class="p-2 text-left">Lokasi</th>
                                <th class="p-2 text-right">System Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="(item, idx) in selectedItems" :key="idx" class="hover:bg-gray-50">
                                <td class="p-2">{{ item.code }}</td>
                                <td class="p-2">{{ item.product_name }}</td>
                                <td class="p-2">{{ item.location }}</td>
                                <td class="p-2 text-right font-bold">{{ formatNumber(item.onhand) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-4 border-t bg-gray-50 flex justify-end gap-3 rounded-b-lg">
                <button @click="showBulkModal = false" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded font-semibold transition-colors">
                    Batal
                </button>
                <button @click="submitBulkRepeat" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded font-semibold shadow transition-colors flex items-center gap-2">
                    <span>Konfirmasi & Buat Ulang</span>
                </button>
            </div>
        </div>
      </div>

      <div v-if="showScanner" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm transition-opacity">
           <div class="bg-white rounded-lg shadow-2xl w-full max-w-sm mx-4 overflow-hidden relative animate-fade-in-down">
              <div class="bg-gray-100 px-4 py-3 border-b flex justify-between items-center">
                  <h3 class="font-bold text-gray-800 text-sm">Scan: <span class="text-blue-600">{{ scanningField === 'scan_serial' ? 'Serial Number' : (scanningField === 'scan_bin' ? 'Lokasi Bin' : 'Cari Material') }}</span></h3>
                  <button @click="closeScanner" class="text-gray-400 hover:text-red-500 font-bold text-2xl leading-none">&times;</button>
              </div>
              <div class="p-0 bg-white relative">
                  <div id="reader" class="w-full" style="min-height: 300px;"></div>
                  <div v-if="isCameraLoading" class="absolute inset-0 flex items-center justify-center text-white text-xs">Memuat Kamera...</div>
              </div>
              <div class="bg-white px-4 py-3 border-t text-center">
                  <button @click="closeScanner" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded shadow">Batal Scan</button>
              </div>
          </div>
      </div>

      <div v-if="showErrorModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm">
          <div class="bg-white rounded-lg shadow-xl max-w-sm w-full mx-4 p-6 text-center animate-bounce-short">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
               <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
               </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Scan Ditolak!</h3>
            <p class="text-sm text-gray-500 mb-6">{{ errorMessage }}</p>
            <button @click="showErrorModal = false" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:text-sm">
               Tutup & Scan Ulang
            </button>
         </div>
      </div>

        <div ref="scrollTrigger" class="py-6 text-center">
          <div v-if="isLoadingMore" class="inline-flex items-center gap-2 text-gray-500">
              <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span class="text-sm font-semibold">Memuat data berikutnya...</span>
          </div>
          <div v-else-if="!nextPageUrl" class="text-xs text-gray-400 italic">
              -- Semua data telah ditampilkan --
          </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm, router, usePage } from '@inertiajs/vue3' // Added usePage
import { ref, reactive, onMounted, nextTick, onBeforeUnmount, watch, computed } from 'vue' // Added computed
import { Html5QrcodeScanner, Html5QrcodeSupportedFormats } from "html5-qrcode";
import axios from 'axios';

const props = defineProps({ 
    initialStocks: Array,
    nextPageUrl: String, // BARU: URL halaman selanjutnya dari controller
    filters: Object,
    statistics: Object 
});

const form = useForm({ items: [] });
const selectedItems = ref([]);
const page = usePage();
// Check if user has permission to approve (SPV/IT)
const canApprove = computed(() => {
    return page.props.permissions?.includes('cycle_count.approve');
});

// --- INFINITE SCROLL STATE ---
const scrollTrigger = ref(null); // Ref untuk elemen div di bawah table
const nextPageUrl = ref(props.nextPageUrl);
const isLoadingMore = ref(false);
let observer = null;

// --- EXISTING LOGIC ---
const filterForm = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    frequency: props.filters?.frequency || '',
    category: props.filters?.category || ''
});


// Apply Filter Function
const applyFilter = () => {
    router.get('/transaction/cycle-count', filterForm, {
        preserveState: true, 
        replace: true,
        onSuccess: () => {
            // Saat filter berubah, data di-reset oleh props.initialStocks
            // scroll akan otomatis dihandle loadInitialData
        }
    });
}

const resetFilter = () => {
    filterForm.search = '';
    filterForm.status = '';
    filterForm.frequency = '';
    filterForm.category = '';
    applyFilter();
}

// Shortcut untuk klik card "Belum Pernah Opname"
const setFilterUncounted = () => {
    filterForm.frequency = 'never';
    applyFilter();
}

const toggleSelectAll = (event) => {
    if (event.target.checked) {
        // Hanya select yang isRepeatable
        selectedItems.value = form.items.filter(item => isRepeatable(item));
    } else {
        selectedItems.value = [];
    }
}

const isSelected = (item) => {
    // Cek apakah item ada di selectedItems berdasarkan referensi objek atau ID
    return selectedItems.value.some(selected => 
        selected.material_id === item.material_id && selected.bin_id === item.bin_id
    );
}

const isRepeatable = (item) => {
    // Item dianggap "sudah pernah cycle count" jika statusnya APPROVED
    // ATAU memiliki history count > 0
    return item.status === 'APPROVED' || (item.history_count && item.history_count > 0);
}

const openBulkModal = () => {
    if(selectedItems.value.length === 0) return;
    showBulkModal.value = true;
}

const submitBulkRepeat = () => {
    // Siapkan payload
    const payload = selectedItems.value.map(item => ({
        material_id: item.material_id,
        bin_id: item.bin_id
    }));

    router.post('/transaction/cycle-count/bulk-repeat', {
        targets: payload
    }, {
        onSuccess: () => {
            showBulkModal.value = false;
            selectedItems.value = []; // Reset selection
            alert(`Berhasil membuat ${payload.length} cycle count baru!`);
            router.reload(); // Reload data
        },
        onError: (err) => {
            console.error(err);
            alert('Gagal memproses permintaan bulk.');
        }
    });
}
// END BARU

// Search State
const searchQuery = ref(props.filters?.search || '');

const handleSearch = () => {
    router.get('/transaction/cycle-count', { search: searchQuery.value }, {
        preserveState: true,
        replace: true,
    });
}

const clearSearch = () => {
    searchQuery.value = '';
    handleSearch();
}

// Scanner State
const showScanner = ref(false);
const isCameraLoading = ref(false);
const scanningItem = ref(null); 
const scanningField = ref('');  
let scannerInstance = null;

// Modal State
const showBulkModal = ref(false); // Added missing ref
const showErrorModal = ref(false);
const errorMessage = ref('');

onMounted(() => { 
    loadInitialData();
});

const loadInitialData = () => {
    if (props.initialStocks) {
        // Load items dari props
        form.items = JSON.parse(JSON.stringify(props.initialStocks));
        
        // Reset status history & selection
        form.items.forEach((item) => {
            item.showHistory = false;
        });
        selectedItems.value = [];

        // <--- PERBAIKAN 1: Update URL halaman selanjutnya dari Props --->
        nextPageUrl.value = props.nextPageUrl; 

        // <--- PERBAIKAN 2: Jalankan Observer untuk mendeteksi scroll --->
        setupInfiniteScroll(); 
    }
}

// Watcher juga perlu update
watch(() => props.initialStocks, () => {
    loadInitialData();
});

// 2. PASTIKAN FUNCTION INI SEPERTI INI
const setupInfiniteScroll = () => {
    // Putuskan koneksi observer lama jika ada (biar gak double)
    if (observer) observer.disconnect();

    // Jika tidak ada halaman selanjutnya, jangan setup apa-apa
    if (!nextPageUrl.value) return;

    // Tunggu sampai DOM (elemen div scrollTrigger) benar-benar render
    nextTick(() => {
        const trigger = scrollTrigger.value;
        
        // Cek apakah elemen trigger ada di HTML
        if (!trigger) return;

        observer = new IntersectionObserver((entries) => {
            const entry = entries[0];
            
            // LOGIC PENTING:
            // 1. isIntersecting: Elemen terlihat di layar
            // 2. !isLoadingMore.value: Sedang tidak loading
            // 3. nextPageUrl.value: Masih ada halaman sisa
            if (entry.isIntersecting && !isLoadingMore.value && nextPageUrl.value) {
                console.log("Trigger Load More..."); // Debugging di Console
                loadMoreItems();
            }
        }, {
            root: null, // viewport
            rootMargin: '100px', // Load 100px sebelum elemen benar-benar terlihat (biar smooth)
            threshold: 0.1
        });

        observer.observe(trigger);
    });
}

// 3. UPDATE LOAD MORE ITEMS
const loadMoreItems = async () => {
    if (isLoadingMore.value || !nextPageUrl.value) return;

    isLoadingMore.value = true;

    try {
        console.log("Fetching: " + nextPageUrl.value); // Debugging

        // Request ke backend
        const response = await axios.get(nextPageUrl.value, {
            params: {
                ...filterForm, // Kirim filter yang sedang aktif
            },
            headers: {
                'Accept': 'application/json' // Pastikan backend tau ini request JSON
            }
        });

        // Ambil data dari response controller
        const newItems = response.data.data;
        const newNextPageUrl = response.data.next_page_url;

        if (newItems && newItems.length > 0) {
            // Tambahkan properti UI
            newItems.forEach(item => item.showHistory = false);

            // Gabungkan data lama + data baru
            form.items = [...form.items, ...newItems];
            
            // Update URL halaman berikutnya
            nextPageUrl.value = newNextPageUrl;

            // Re-setup observer jika masih ada halaman lagi
            if (newNextPageUrl) {
                setupInfiniteScroll();
            } else {
                // Jika sudah habis, matikan observer
                if (observer) observer.disconnect();
            }
        } else {
            nextPageUrl.value = null; // Data habis
        }

    } catch (error) {
        console.error("Gagal memuat data tambahan:", error);
        alert("Gagal memuat data. Periksa koneksi internet.");
    } finally {
        isLoadingMore.value = false;
    }
}

onBeforeUnmount(() => { 
    if (scannerInstance) scannerInstance.clear().catch(err => console.error(err)); 
    if (observer) observer.disconnect();
});

// --- HELPER EXTRACTOR ---
const extractSerial = (rawString) => {
    if (!rawString) return '';
    const str = rawString.toString().trim().toUpperCase();
    if (str.includes('|')) {
        const parts = str.split('|');
        if (parts.length >= 3) return parts[2];
    }
    return str;
}

// --- SCANNER LOGIC ---
const openScanner = (item, field) => {
    scanningItem.value = item;
    scanningField.value = field;
    showScanner.value = true;
    isCameraLoading.value = true;
    setTimeout(() => { nextTick(() => { initScanner(); }); }, 300);
}

const initScanner = () => {
    if (!document.getElementById('reader')) return;
    const config = { fps: 10, qrbox: { width: 200, height: 200 }, aspectRatio: 1.0, formatsToSupport: [ Html5QrcodeSupportedFormats.QR_CODE, Html5QrcodeSupportedFormats.CODE_128, Html5QrcodeSupportedFormats.CODE_39 ] };
    if (scannerInstance) scannerInstance.clear().catch(e => console.warn(e));
    try {
        scannerInstance = new Html5QrcodeScanner("reader", config, false);
        scannerInstance.render(onScanSuccess, () => {});
        isCameraLoading.value = false;
    } catch (e) { alert("Error Camera"); closeScanner(); }
}

const onScanSuccess = (decodedText) => {
    let resultText = decodedText.trim();
    let isJson = false;

    try {
        const parsedData = JSON.parse(resultText);
        if (scanningField.value === 'scan_bin' && parsedData.bin_code) {
            resultText = parsedData.bin_code;
            isJson = true;
        }
    } catch (e) {}

    if ((scanningField.value === 'scan_serial' || scanningField.value === 'search') && !isJson) {
        resultText = extractSerial(resultText);
    }
    
    resultText = resultText.toUpperCase();

    if (scanningField.value === 'search') {
        searchQuery.value = resultText; 
        handleSearch();
        closeScanner();
        return;
    }

    if (scanningItem.value && scanningField.value) {
        let expectedValue = '';
        let labelField = '';

        if (scanningField.value === 'scan_bin') {
            expectedValue = cleanStr(scanningItem.value.location);
            labelField = 'Lokasi Bin';
        } else if (scanningField.value === 'scan_serial') {
            expectedValue = extractSerial(scanningItem.value.serial_number);
            labelField = 'Serial Number';
        }

        if (resultText !== expectedValue) {
            closeScanner();
            errorMessage.value = `Kode ${labelField} (${resultText}) TIDAK SESUAI target (${expectedValue}).`;
            showErrorModal.value = true;
            return; 
        }

        scanningItem.value[scanningField.value] = resultText; 
    }
    
    closeScanner();
}

const closeScanner = () => {
    if (scannerInstance) scannerInstance.clear().catch(() => {}).finally(() => { scannerInstance = null; showScanner.value = false; });
    else showScanner.value = false;
}

// --- MATH & VALIDATION ---
const cleanStr = (str) => (str || '').toString().trim().toUpperCase();
const checkSerialMatch = (item) => {
    const scan = cleanStr(item.scan_serial);
    const target = extractSerial(item.serial_number);
    return scan === target && scan.length > 0;
}
const checkBinMatch = (item) => cleanStr(item.scan_bin) === cleanStr(item.location) && !!item.scan_bin;
const isUnlocked = (item) => checkSerialMatch(item) && checkBinMatch(item);

const formatNumber = (num) => new Intl.NumberFormat('en-US').format(num);
const calculateAccuracy = (item) => {
    if (!isUnlocked(item)) return '0.00';
    const physical = item.physical_qty ?? 0;
    const system = item.onhand;
    return system ? ((physical / system) * 100).toFixed(2) : '0.00';
}
const calculateVariance = (item) => {
    if (!isUnlocked(item)) return '0.00';
    const physical = item.physical_qty;
    if (physical === null || physical === undefined || physical === '') return '0.00';
    const system = item.onhand;
    return system ? (((physical - system) / system) * 100).toFixed(2) : '0.00';
}
const getAccuracyColor = (item) => {
    if (!isUnlocked(item)) return 'text-gray-300';
    const acc = parseFloat(calculateAccuracy(item));
    return acc === 100 ? 'text-green-600' : (acc > 100 ? 'text-blue-600' : 'text-red-600');
}
const getVarianceColor = (item) => parseFloat(calculateVariance(item)) === 0 ? 'text-gray-400' : 'text-red-600';

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'KARANTINA': return 'bg-orange-100 text-orange-700 border-orange-300';
        case 'RELEASED': return 'bg-green-100 text-green-700 border-green-300';
        case 'HOLD': return 'bg-gray-100 text-gray-700 border-gray-300';
        case 'REJECTED': return 'bg-red-100 text-red-700 border-red-300';
        default: return 'bg-gray-50 text-gray-500 border-gray-200';
    }
}

const getCategoryBadgeClass = (category) => {
    const upper = (category || '').toUpperCase();
    if (upper.includes('RAW')) return 'bg-purple-100 text-purple-700 border-purple-300';
    if (upper.includes('PACKAGING')) return 'bg-pink-100 text-pink-700 border-pink-300';
    return 'bg-gray-100 text-gray-700 border-gray-300';
}

// --- ACTIONS ---

const submitOpname = () => {
    if (confirm('Simpan hasil opname?')) {
        form.post('/transaction/cycle-count/store', {
            onSuccess: () => {
                alert('Berhasil disubmit ke Supervisor!');
            },
            onError: (errors) => {
                console.error(errors);
                alert('Gagal menyimpan data.');
            }
        });
    }
}

const approveItem = (item) => {
    console.log('approveItem called with:', item);
    
    if (item.status !== 'REVIEW_NEEDED') {
        alert("Item belum disubmit oleh Warehouseman!");
        return;
    }

    if(!item.spv_note && !confirm('Approve tanpa catatan?')) return;
    
    console.log('Sending approve request:', {
        id: item.id, 
        material_id: item.material_id, 
        spv_note: item.spv_note
    });
    
    router.post('/transaction/cycle-count/approve', {
        id: item.id, 
        material_id: item.material_id, 
        spv_note: item.spv_note
    }, {
        onSuccess: (page) => {
            console.log('Approve success response:', page);
            item.status = 'APPROVED'; 
            alert('Item berhasil diapprove dan inventory telah disesuaikan!');
            
            // Reload data untuk memastikan inventory update terlihat
            router.reload();
        },
        onError: (errors) => {
            console.error('Approve error:', errors);
            alert('Gagal approve: ' + (typeof errors === 'string' ? errors : JSON.stringify(errors)));
        },
        onFinish: () => {
            console.log('Approve request finished');
        }
    });
}

const toggleHistory = (index) => {
    form.items[index].showHistory = !form.items[index].showHistory;
}

const repeatCycleCount = (item) => {
    if (!confirm(`Apakah Anda yakin ingin mengulang cycle count untuk ${item.product_name}?`)) return;
    
    router.post('/transaction/cycle-count/repeat', {
        material_id: item.material_id,
        bin_id: item.bin_id
    }, {
        onSuccess: () => {
            alert('Cycle count baru berhasil dibuat!');
            router.reload();
        },
        onError: (errors) => {
            console.error(errors);
            alert('Gagal membuat cycle count baru.');
        }
    });
}
</script>