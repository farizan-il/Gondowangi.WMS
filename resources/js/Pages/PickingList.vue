<template>
    <AppLayout title="Riwayat Aktivitas">
        <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <h2 class="text-2xl font-bold text-gray-900">Picking List (Transfer Order)</h2>
      <div class="text-sm text-gray-600">
        Total picking tasks: {{ pickingTasks.length }}
      </div>
    </div>

    <!-- Filter Status -->
    <div class="bg-white rounded-lg p-4 shadow">
      <div class="flex items-center space-x-4">
        <span class="text-sm font-medium text-gray-700">Filter Status:</span>
        <div class="flex space-x-2">
          <button @click="filterStatus = 'ALL'" :class="filterStatus === 'ALL' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'" class="px-3 py-1 rounded text-sm hover:bg-blue-500 hover:text-white">
            Semua
          </button>
          <button @click="filterStatus = 'Pending'" :class="filterStatus === 'Pending' ? 'bg-gray-600 text-white' : 'bg-gray-200 text-gray-700'" class="px-3 py-1 rounded text-sm hover:bg-gray-500 hover:text-white">
            Pending
          </button>
          <button @click="filterStatus = 'In Progress'" :class="filterStatus === 'In Progress' ? 'bg-yellow-600 text-white' : 'bg-gray-200 text-gray-700'" class="px-3 py-1 rounded text-sm hover:bg-yellow-500 hover:text-white">
            In Progress
          </button>
          <button @click="filterStatus = 'Completed'" :class="filterStatus === 'Completed' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'" class="px-3 py-1 rounded text-sm hover:bg-green-500 hover:text-white">
            Completed
          </button>
          <button @click="filterStatus = 'Short-Pick'" :class="filterStatus === 'Short-Pick' ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-700'" class="px-3 py-1 rounded text-sm hover:bg-orange-500 hover:text-white">
            Short-Pick
          </button>
        </div>
      </div>
    </div>

    <!-- Tabel Picking Tasks -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TO Number</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Reservasi</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Dibuat</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requester / Departemen</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          

          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="filteredTasks.length === 0">
              <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                Tidak ada Picking Task yang ditemukan untuk status {{ filterStatus === 'ALL' ? 'apapun' : filterStatus }}.
              </td>
            </tr>
            <tr v-for="task in filteredTasks" :key="task.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ task.toNumber }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 hover:underline cursor-pointer" @click="viewReservation(task.noReservasi)">
                {{ task.noReservasi }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatDateTime(task.tanggalDibuat) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ task.requester }} / {{ task.departemen }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(task.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ task.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                  <button @click="viewDetail(task)" class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-2 py-1 rounded text-xs">
                    Detail
                  </button>
                  <button v-if="task.status === 'Pending' || task.status === 'In Progress'" @click="startPicking(task)" class="bg-green-100 text-green-700 hover:bg-green-200 px-2 py-1 rounded text-xs">
                    Kerjakan
                  </button>
                  <button @click="printPickingList(task)" class="bg-purple-100 text-purple-700 hover:bg-purple-200 px-2 py-1 rounded text-xs">
                    Cetak Picking List
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Detail Picking Task (Work Modal) -->
    <div v-if="showPickingModal" class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: rgba(43, 51, 63, 0.67);">
      <div class="bg-white rounded-lg max-w-7xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <!-- Header Modal -->
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Detail Picking Task - {{ selectedTask?.toNumber }}</h3>
            <button @click="closePickingModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Header Info -->
          <div v-if="selectedTask" class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <div>
                <span class="text-sm font-medium text-gray-700">TO Number:</span>
                <div class="text-gray-900 font-medium">{{ selectedTask.toNumber }}</div>
              </div>
              <div>
                <span class="text-sm font-medium text-gray-700">No Reservasi:</span>
                <div class="text-blue-600 cursor-pointer hover:underline" @click="viewReservation(selectedTask.noReservasi)">{{ selectedTask.noReservasi }}</div>
              </div>
              <div>
                <span class="text-sm font-medium text-gray-700">Tanggal Dibuat:</span>
                <div class="text-gray-900">{{ formatDateTime(selectedTask.tanggalDibuat) }}</div>
              </div>
              <div>
                <span class="text-sm font-medium text-gray-700">Requester:</span>
                <div class="text-gray-900">{{ selectedTask.requester }} / {{ selectedTask.departemen }}</div>
              </div>
            </div>
          </div>

          <!-- Summary Progress -->
          <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ getTotalItemsCount }}</div>
                <div class="text-sm text-blue-700">Total Alokasi Batch</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ getPickedItemsCount }}</div>
                <div class="text-sm text-green-700">Picked Complete</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-orange-600">{{ getShortPickItemsCount }}</div>
                <div class="text-sm text-orange-700">Short-Pick</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-gray-600">{{ getPendingItemsCount }}</div>
                <div class="text-sm text-gray-700">Pending</div>
              </div>
            </div>
            <div class="mt-4">
              <div class="flex justify-between text-sm mb-1">
                <span class="text-blue-700">Progress Picking</span>
                <span class="text-blue-700">{{ Math.round(getPickingProgress) }}%</span>
              </div>
              <div class="w-full bg-blue-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" :style="{ width: getPickingProgress + '%' }"></div>
              </div>
            </div>
          </div>

          <!-- Items Table (Sekarang menampilkan detail Batch Alokasi) -->
          <div class="border border-gray-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
              <!-- PERUBAHAN UTAMA DI SINI -->
              <table class="min-w-full divide-y divide-gray-200" style="min-width: 1400px;">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">No</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap min-w-[120px]">Kode Item / Nama Material</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap min-w-[120px]">Lot/Batch</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap min-w-[150px]">Source Bin</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Exp Date</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Qty Dialokasikan</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Qty Picked</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">UoM</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Status</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <tr v-for="(item, index) in selectedTask?.items || []" :key="index" :class="item.status === 'Picked' ? 'bg-green-50' : item.status === 'Short-Pick' ? 'bg-orange-50' : ''">
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ index + 1 }}</td>
                    
                    <!-- Kode Item / Nama Material -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ item.kodeItem }} <br>
                      <span class="text-xs text-gray-600 font-normal">{{ item.namaMaterial }}</span>
                    </td>
                    
                    <!-- Lot/Batch -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ item.lotSerial }}</td>
                    
                    <!-- Source Bin / WH -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                      {{ item.sourceBin }}
                    </td>
                    
                    <!-- Exp Date -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                      {{ item.expDate ? new Date(item.expDate).toLocaleDateString('id-ID') : '-' }}
                    </td>
                    
                    <!-- Qty Dialokasikan -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 font-medium">{{ item.qtyDiminta }}</td>
                    
                    <!-- Qty Picked (Input) -->
                    <td class="px-3 py-2 whitespace-nowrap">
                      <!-- Max sekarang adalah qtyDialokasikan -->
                      <input v-model.number="item.qtyPicked" type="number" :max="item.qtyDiminta" class="w-20 text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-900" @input="updateItemStatus(item)">
                    </td>
                    
                    <!-- UoM -->
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ item.uom }}</td>
                    
                    <!-- Status -->
                    <td class="px-3 py-2 whitespace-nowrap">
                      <span :class="getItemStatusClass(item.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                        {{ item.status }}
                      </span>
                    </td>
                    
                    <!-- Aksi -->
                    <td class="px-3 py-2 whitespace-nowrap">
                      <button 
                        @click="startItemQRScan(item, index)" 
                        :disabled="item.status === 'Picked'"
                        :class="item.status === 'Picked' ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 text-white'"
                        class="px-3 py-1 rounded text-xs font-medium transition-colors duration-200"
                      >
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h2M4 4h16a2 2 0 012 2v12a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                        </svg>
                        {{ item.status === 'Picked' ? 'Done' : 'Scan QR' }}
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Footer Modal -->
          <div class="flex justify-between items-center mt-6 pt-6 border-t border-gray-200">
            <div class="text-sm text-gray-600">
              Progress: {{ getCompletedItemsCount }}/{{ selectedTask?.items?.length || 0 }} batch allocations completed
            </div>
            <div class="flex space-x-3">
              <button @click="closePickingModal" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                Batal
              </button>
              <button @click="finishPicking" :disabled="!canFinishPicking" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:bg-gray-400">
                Selesai Picking
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal QR Scanner dengan Camera (Perlu update logic scan) -->
    <div v-if="showQRScannerModal" class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: rgba(43, 51, 63, 0.67);">
      <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">QR Scanner - Batch Item #{{ currentItemIndex + 1 }}</h3>
            <button @click="closeQRScanner" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Item Info -->
          <div v-if="currentScanItem" class="bg-gray-50 p-3 rounded-lg mb-4">
            <div class="text-sm">
              <div class="font-medium text-gray-900">{{ currentScanItem.kodeItem }} - {{ currentScanItem.namaMaterial }}</div>
              <!-- Qty Diminta -> Qty Dialokasikan -->
              <div class="text-gray-600">Batch: {{ currentScanItem.lotSerial }} | Qty Alokasi: {{ currentScanItem.qtyDiminta }} {{ currentScanItem.uom }}</div>
              <div class="text-gray-600">Source: {{ currentScanItem.sourceBin }} ({{ currentScanItem.sourceWarehouse }})</div>
            </div>
          </div>
          
          <div class="space-y-4">
            <!-- Current Scan Step -->
            <div class="bg-blue-50 p-3 rounded-lg">
              <div class="text-sm font-medium text-blue-900">Step {{ scanStep }}/4</div>
              <div class="text-xs text-blue-700">{{ getScanStepText }}</div>
            </div>

            <!-- Camera Preview -->
            <div class="relative bg-black rounded-lg overflow-hidden" style="aspect-ratio: 4/3;">
              <video ref="videoElement" autoplay playsinline class="w-full h-full object-cover"></video>
              <canvas ref="canvasElement" class="hidden"></canvas>
              
              <!-- Scanning Overlay -->
              <div v-if="isScanning" class="absolute inset-0 flex items-center justify-center">
                <div class="border-4 border-green-500 rounded-lg" style="width: 250px; height: 250px;">
                  <div class="absolute inset-0 border-2 border-white opacity-50 animate-pulse"></div>
                </div>
              </div>
              
              <!-- Camera Status -->
              <div class="absolute top-2 right-2 bg-black bg-opacity-75 text-white px-3 py-1 rounded text-xs">
                {{ cameraStatus }}
              </div>
            </div>

            <!-- Control Buttons -->
            <div class="flex space-x-2">
              <button 
                @click="toggleCamera" 
                :class="isCameraActive ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'"
                class="flex-1 text-white py-2 px-4 rounded-md flex items-center justify-center"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path v-if="!isCameraActive" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                  <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                {{ isCameraActive ? 'Matikan Kamera' : 'Aktifkan Kamera' }}
              </button>
              
              <button 
                @click="simulateScan" 
                class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-md flex items-center justify-center"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Simulasi Scan
              </button>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Atau input manual QR Code:</label>
              <input v-model="qrInput" @keyup.enter="processQR" type="text" placeholder="Scan atau ketik QR Code..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
            </div>
            
            <!-- Manual Qty Input (when scanning box) -->
            <div v-if="scanStep === 3" class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Qty yang Dipick (Max: {{ currentScanItem?.qtyDiminta || 0 }} {{ currentScanItem?.uom || '' }}):</label>
              <input v-model.number="manualQty" type="number" :max="currentScanItem?.qtyDiminta" placeholder="Masukkan quantity..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
            </div>
            
            <div class="flex space-x-3">
              <button @click="processQR" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md">
                {{ scanStep === 3 ? 'Confirm Qty' : 'Process QR' }}
              </button>
              <button v-if="scanStep > 1" @click="prevScanStep" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md">
                Back
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Summary Detail (Tidak diubah) -->
    <div v-if="showSummaryModal" class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: rgba(43, 51, 63, 0.67);">
      <div class="bg-white rounded-lg max-w-7xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Summary Detail - {{ selectedSummaryTask?.toNumber }}</h3>
            <button @click="closeSummaryModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Summary Stats -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
              <div class="text-2xl font-bold text-blue-600">{{ getSummaryTotalItems }}</div>
              <div class="text-sm text-blue-700">Total Alokasi Batch</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
              <div class="text-2xl font-bold text-green-600">{{ getSummaryPickedItems }}</div>
              <div class="text-sm text-green-700">Batch Picked Complete</div>
            </div>
            <div class="bg-orange-50 p-4 rounded-lg">
              <div class="text-2xl font-bold text-orange-600">{{ getSummaryShortPickItems }}</div>
              <div class="text-sm text-orange-700">Short-Pick</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <div class="text-2xl font-bold text-gray-600">{{ getSummaryPendingItems }}</div>
              <div class="text-sm text-gray-700">Pending</div>
            </div>
          </div>

          <!-- Detailed breakdown (Sekarang menampilkan detail Batch Alokasi) -->
          <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
              <h4 class="text-sm font-medium text-gray-900">Detail Alokasi Batch</h4>
            </div>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Item</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Material</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch/Lot</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty Alokasi</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty Picked</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">UoM</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source Bin</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="(item, index) in selectedSummaryTask?.items || []" :key="index" :class="getItemRowClass(item.status)">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ item.kodeItem }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.namaMaterial }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.lotSerial }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.qtyDiminta }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.qtyPicked || 0 }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.uom }}</td>
                    <td class="px-4 py-3">
                      <span :class="getItemStatusClass(item.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                        {{ item.status }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                      {{ item.sourceBin }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="flex justify-end mt-6">
            <button @click="closeSummaryModal" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'

// Data reaktif
const filterStatus = ref('ALL')
const pickingTasks = ref([])
const showPickingModal = ref(false)
const showQRScannerModal = ref(false)
const showSummaryModal = ref(false)
const selectedTask = ref(null)
const selectedSummaryTask = ref(null)
const qrInput = ref('')
const scanStep = ref(1)
const currentScanItem = ref(null)
const currentItemIndex = ref(0)
const manualQty = ref('')

// Camera related refs
const videoElement = ref(null)
const canvasElement = ref(null)
const isCameraActive = ref(false)
const isScanning = ref(false)
const cameraStatus = ref('Kamera Off')
let mediaStream = null
let scanInterval = null

// Dummy QR data untuk simulasi
const dummyQRData = {
    // QR format: ITEM_CODE|LOT/BATCH|QTY|EXP_DATE|STATUS
    box: {
        'SHP001': 'SHP001|LOT001|50|2025-12-31|ACTIVE',
        'SHP002': 'SHP002|LOT002|100|2025-12-31|ACTIVE',
        '20008': '20008|BATCH-A|100|2024-10-01|ACTIVE',
        '20009': '20009|BATCH-B|1100|2024-12-31|ACTIVE',
    },
    bins: ['A-01-01', 'A-01-02', 'STAGING-001']
}

// Fetch picking list from the backend
const fetchPickingList = async () => {
    try {
        // FIX UTAMA: Menggunakan fungsi route() untuk mengatasi 404
        const response = await axios.get(route('transaction.api.picking-list'))
        pickingTasks.value = response.data
    } catch (error) {
        console.error('Error fetching picking list:', error)
    }
}

// Computed properties
const getTotalItemsCount = computed(() => {
    // Menghitung jumlah record alokasi batch
    if (!selectedTask.value?.items) return 0
    return selectedTask.value.items.length
})

const getPickedItemsCount = computed(() => {
    if (!selectedTask.value?.items) return 0
    return selectedTask.value.items.filter(item => item.status === 'Picked').length
})

const getShortPickItemsCount = computed(() => {
    if (!selectedTask.value?.items) return 0
    return selectedTask.value.items.filter(item => item.status === 'Short-Pick').length
})

const getPendingItemsCount = computed(() => {
    // Reserved atau status lain yang belum selesai dipick
    if (!selectedTask.value?.items) return 0
    return selectedTask.value.items.filter(item => item.status === 'Reserved' || item.status === 'In Progress').length
})

const getCompletedItemsCount = computed(() => {
    if (!selectedTask.value?.items) return 0
    return selectedTask.value.items.filter(item => item.status === 'Picked' || item.status === 'Short-Pick').length
})

const getPickingProgress = computed(() => {
    if (!selectedTask.value?.items) return 0
    const total = selectedTask.value.items.length
    const completed = getCompletedItemsCount.value
    return total > 0 ? (completed / total) * 100 : 0
})

const canFinishPicking = computed(() => {
    if (!selectedTask.value?.items) return false
    // Memastikan semua item alokasi batch sudah dipick (status Picked atau Short-Pick)
    return selectedTask.value.items.every(item => item.status === 'Picked' || item.status === 'Short-Pick')
})

const getScanStepText = computed(() => {
    const steps = [
        '',
        'Scan QR Box/Material untuk validasi kode dan batch',
        'Scan QR Source Bin untuk validasi lokasi pengambilan',
        'Input quantity yang dipick',
        'Scan QR Dest Bin (Staging Area) untuk validasi transfer'
    ]
    return steps[scanStep.value]
})

// Summary Modal Computed Properties
const getSummaryTotalItems = computed(() => {
    if (!selectedSummaryTask.value?.items) return 0
    return selectedSummaryTask.value.items.length
})

const getSummaryPickedItems = computed(() => {
    if (!selectedSummaryTask.value?.items) return 0
    return selectedSummaryTask.value.items.filter(item => item.status === 'Picked').length
})

const getSummaryShortPickItems = computed(() => {
    if (!selectedSummaryTask.value?.items) return 0
    return selectedSummaryTask.value.items.filter(item => item.status === 'Short-Pick').length
})

const getSummaryPendingItems = computed(() => {
    if (!selectedSummaryTask.value?.items) return 0
    return selectedSummaryTask.value.items.filter(item => item.status === 'Reserved' || item.status === 'In Progress').length
})

// Camera Methods (Logika tidak diubah, tetap simulasi)
const startCamera = async () => {
    try {
        cameraStatus.value = 'Mengaktifkan kamera...'
        
        const constraints = {
            video: {
                facingMode: 'environment', // Use back camera on mobile
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        }
        
        mediaStream = await navigator.mediaDevices.getUserMedia(constraints)
        
        if (videoElement.value) {
            videoElement.value.srcObject = mediaStream
            isCameraActive.value = true
            isScanning.value = true
            cameraStatus.value = 'Kamera Aktif - Siap Scan'
            
            // Start scanning loop
            startScanning()
        }
    } catch (error) {
        console.error('Error accessing camera:', error)
        cameraStatus.value = 'Error: Tidak dapat mengakses kamera'
        console.error('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.')
    }
}

const stopCamera = () => {
    if (mediaStream) {
        mediaStream.getTracks().forEach(track => track.stop())
        mediaStream = null
    }
    
    if (videoElement.value) {
        videoElement.value.srcObject = null
    }
    
    if (scanInterval) {
        clearInterval(scanInterval)
        scanInterval = null
    }
    
    isCameraActive.value = false
    isScanning.value = false
    cameraStatus.value = 'Kamera Off'
}

const toggleCamera = () => {
    if (isCameraActive.value) {
        stopCamera()
    } else {
        startCamera()
    }
}

const startScanning = () => {
    scanInterval = setInterval(() => {
        if (!isScanning.value || !videoElement.value) return
    }, 100)
}

const simulateScan = () => {
    if (!currentScanItem.value) return
    
    let simulatedQR = ''
    
    switch (scanStep.value) {
        case 1: // Simulate Box QR (Item Code | Batch)
            // Coba ambil dari data dummy yang cocok dengan kode item saat ini
            simulatedQR = dummyQRData.box[currentScanItem.value.kodeItem]
                ? `${currentScanItem.value.kodeItem}|${currentScanItem.value.lotSerial}|100|2025-12-31|ACTIVE`
                : `${currentScanItem.value.kodeItem}|${currentScanItem.value.lotSerial}|50|2025-12-31|ACTIVE`
            break
        case 2: // Simulate Source Bin
            simulatedQR = currentScanItem.value.sourceBin
            break
        case 4: // Simulate Dest Bin
            simulatedQR = currentScanItem.value.destBin
            break
        default:
            console.warn('Step ini tidak memerlukan scan QR')
            return
    }
    
    qrInput.value = simulatedQR
    
    // Visual feedback
    const originalStatus = cameraStatus.value
    cameraStatus.value = '✓ QR Terdeteksi!'
    
    setTimeout(() => {
        cameraStatus.value = originalStatus
        processQR()
    }, 500)
}

// Methods
const filteredTasks = computed(() => {
    if (filterStatus.value === 'ALL') {
        return pickingTasks.value
    }
    return pickingTasks.value.filter(task => task.status === filterStatus.value || (filterStatus.value === 'Pending' && task.status === 'Reserved'))
})

const getStatusClass = (status) => {
    const classes = {
        'Reserved': 'bg-gray-100 text-gray-800',
        'Pending': 'bg-gray-100 text-gray-800',
        'In Progress': 'bg-yellow-100 text-yellow-800',
        'Completed': 'bg-green-100 text-green-800',
        'Short-Pick': 'bg-orange-100 text-orange-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const getItemStatusClass = (status) => {
    const classes = {
        'Reserved': 'bg-gray-100 text-gray-700',
        'Pending': 'bg-gray-100 text-gray-700',
        'Picked': 'bg-green-100 text-green-700',
        'Short-Pick': 'bg-orange-100 text-orange-700'
    }
    return classes[status] || 'bg-gray-100 text-gray-700'
}

const getItemRowClass = (status) => {
    const classes = {
        'Picked': 'bg-green-50',
        'Short-Pick': 'bg-orange-50',
        'Reserved': 'bg-white',
        'Pending': 'bg-white',
    }
    return classes[status] || 'bg-white'
}

const getItemAchievement = (item) => {
    if (item.status === 'Reserved' || item.status === 'Pending') return '-'
    if (item.qtyDiminta === 0) return '0%'
    const percentage = Math.round((item.qtyPicked / item.qtyDiminta) * 100)
    return `${percentage}% (${item.qtyPicked}/${item.qtyDiminta})`
}

const formatDateTime = (dateString) => {
    if (!dateString) return '-';
    try {
        return new Date(dateString).toLocaleString('id-ID', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        })
    } catch {
        return dateString
    }
}

const viewReservation = (noReservasi) => {
    console.log(`Melihat detail reservasi: ${noReservasi}`)
}

const viewDetail = (task) => {
    selectedSummaryTask.value = task
    showSummaryModal.value = true
}

const startPicking = (task) => {
    selectedTask.value = JSON.parse(JSON.stringify(task)) // Deep clone the task
    // Update status ke In Progress jika masih Reserved/Pending
    if (task.status === 'Reserved' || task.status === 'Pending') {
        const taskIndex = pickingTasks.value.findIndex(t => t.id === task.id)
        if (taskIndex !== -1) {
            // Update status di list utama dan task yang dipilih
            pickingTasks.value[taskIndex].status = 'In Progress'
            selectedTask.value.status = 'In Progress'
            // NOTE: Di sini perlu ada API call ke backend untuk update status header
        }
    }
    showPickingModal.value = true
}

const closePickingModal = () => {
    showPickingModal.value = false
    selectedTask.value = null
    resetScanner()
    fetchPickingList() // Refresh data setelah menutup modal
}

const closeSummaryModal = () => {
    showSummaryModal.value = false
    selectedSummaryTask.value = null
}

const startItemQRScan = (item, index) => {
    if (item.status === 'Picked') return
    
    currentScanItem.value = JSON.parse(JSON.stringify(item)) // Deep clone
    currentItemIndex.value = index
    showQRScannerModal.value = true
    resetScanner()
}

const closeQRScanner = () => {
    stopCamera()
    showQRScannerModal.value = false
    resetScanner()
}

const resetScanner = () => {
    scanStep.value = 1
    qrInput.value = ''
    manualQty.value = ''
}

const processQR = () => {
    if (!qrInput.value && scanStep.value !== 3) {
        console.error('Silakan scan atau input QR Code!')
        return
    }

    switch (scanStep.value) {
        case 1:
            validateQRBox(qrInput.value)
            break
        case 2:
            validateSourceBin(qrInput.value)
            break
        case 3:
            confirmQuantity()
            break
        case 4:
            validateDestBin(qrInput.value)
            break
    }
}

const validateQRBox = (qrContent) => {
    // Expected QR Box/Batch format: ITEM_CODE|LOT/BATCH|...
    const qrParts = qrContent.split('|')
    if (qrParts.length < 2) {
        console.error('Format QR Box tidak valid! Harap ikuti format: ITEM_CODE|BATCH_LOT|...')
        return
    }
    
    const itemCode = qrParts[0] // Asumsi item code di index 0
    const lotNo = qrParts[1]    // Asumsi lot/batch di index 1
    
    if (itemCode !== currentScanItem.value.kodeItem || lotNo !== currentScanItem.value.lotSerial) {
        console.error(`Item/Batch tidak sesuai!\nHarus: ${currentScanItem.value.kodeItem} - ${currentScanItem.value.lotSerial}\nDi-scan: ${itemCode} - ${lotNo}`)
        return
    }
    
    scanStep.value = 2
    qrInput.value = ''
    console.log(`✓ Item valid: ${currentScanItem.value.kodeItem} - ${currentScanItem.value.lotSerial}`)
}

const validateSourceBin = (binCode) => {
    if (!currentScanItem.value) return
    
    if (binCode !== currentScanItem.value.sourceBin) {
        console.error(`Lokasi bin salah!\nHarus: ${currentScanItem.value.sourceBin}\nDi-scan: ${binCode}`)
        return
    }
    
    scanStep.value = 3
    qrInput.value = ''
    // Isi manualQty dengan Qty Dialokasikan sebagai default
    manualQty.value = currentScanItem.value.qtyDiminta
    console.log(`✓ Source Bin valid: ${binCode}`)
}

const confirmQuantity = () => {
    const qtyPicked = parseFloat(manualQty.value)
    if (isNaN(qtyPicked) || qtyPicked <= 0) {
        console.error('Silakan input quantity yang valid!')
        return
    }
    
    const qtyDiminta = currentScanItem.value.qtyDiminta
    
    if (qtyPicked > qtyDiminta) {
        console.error(`Quantity melebihi yang dialokasikan!\nDialokasikan: ${qtyDiminta}\nInput: ${qtyPicked}`)
        return
    }
    
    // Update data di modal (currentScanItem adalah deep clone)
    currentScanItem.value.qtyPicked = qtyPicked
    
    if (qtyPicked === qtyDiminta) {
        currentScanItem.value.status = 'Picked'
        console.log(`✓ Batch berhasil dipick complete: ${qtyPicked}/${qtyDiminta}`)
    } else {
        currentScanItem.value.status = 'Short-Pick'
        console.warn(`⚠ Short-pick detected: ${qtyPicked}/${qtyDiminta}`)
    }
    
    // Update item di selectedTask utama
    const taskItem = selectedTask.value.items.find(item => item.id === currentScanItem.value.id)
    if (taskItem) {
        taskItem.qtyPicked = currentScanItem.value.qtyPicked
        taskItem.status = currentScanItem.value.status
    }
    
    if (currentScanItem.value.destBin && currentScanItem.value.destBin !== '-') {
        scanStep.value = 4
        qrInput.value = ''
    } else {
        finishCurrentItem()
    }
}

const validateDestBin = (binCode) => {
    if (!currentScanItem.value) return
    
    if (binCode !== currentScanItem.value.destBin) {
        console.error(`Destination bin salah!\nHarus: ${currentScanItem.value.destBin}\nDi-scan: ${binCode}`)
        return
    }
    
    console.log(`✓ Destination Bin valid: ${binCode}`)
    finishCurrentItem()
}

const finishCurrentItem = () => {
    console.log(`Batch ${currentScanItem.value.lotSerial} untuk ${currentScanItem.value.kodeItem} berhasil diselesaikan!`)
    closeQRScanner()
    
    // Trigger re-calculation of progress
    // updateItemStatus(selectedTask.value.items[currentItemIndex.value]);
    
    const remainingItems = selectedTask.value.items.filter(item => item.status === 'Reserved' || item.status === 'In Progress')
    if (remainingItems.length === 0) {
        console.log('Semua alokasi batch sudah dipick! Silakan klik "Selesai Picking"')
    }
}

const prevScanStep = () => {
    if (scanStep.value > 1) {
        scanStep.value--
        qrInput.value = ''
    }
}

// Hanya update status di tabel utama jika ada perubahan QTY manual
const updateItemStatus = (item) => {
    if (!item.qtyPicked || item.qtyPicked <= 0) {
        item.status = 'Reserved' // Kembali ke status reserved/pending
        item.qtyPicked = 0
        return
    }
    
    if (item.qtyPicked >= item.qtyDiminta) {
        item.status = 'Picked'
        item.qtyPicked = item.qtyDiminta
    } else {
        item.status = 'Short-Pick'
    }
}

const finishPicking = async () => {
    if (!selectedTask.value) return

    // Pastikan semua alokasi sudah diselesaikan (Picked/Short-Pick)
    if (!canFinishPicking.value) {
        console.error('Mohon selesaikan semua alokasi batch sebelum menyelesaikan picking task.')
        return
    }

    const pickedItems = selectedTask.value.items.map(item => ({
        // PENTING: Kirim ID dari record RESERVATION (alokasi batch)
        reservation_id: item.id, 
        picked_quantity: item.qtyPicked,
    }))

    try {
        const response = await axios.post(route('transaction.picking-list.store'), {
            // Kirim ID dari record RESERVATION REQUEST (Header)
            reservation_request_id: selectedTask.value.id, 
            items: pickedItems,
        })

        if (response.status === 200) {
            console.log('Picking successful. Status task di-update.')
            closePickingModal()
            // fetchPickingList() // Akan dipanggil saat closePickingModal
        }
    } catch (error) {
        console.error('Error finishing picking:', error)
        console.error('Gagal menyelesaikan picking. Silakan cek konsol untuk detail error.')
    }
}

const printPickingList = (task) => {
    const printWindow = window.open('', '_blank')
    
    // Perhitungan Summary didasarkan pada jumlah item ALOKASI BATCH (task.items.length)
    const totalAllocations = task.items.length
    const pickedItems = task.items.filter(item => item.status === 'Picked').length
    const shortPickItems = task.items.filter(item => item.status === 'Short-Pick').length
    
    printWindow.document.write(`
        <html>
            <head>
                <title>Picking List - ${task.toNumber}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; }
                    .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 15px; margin-bottom: 20px; }
                    .info-section { margin: 15px 0; }
                    .info-row { display: flex; justify-content: space-between; margin: 8px 0; }
                    .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                    .items-table th, .items-table td { border: 1px solid #000; padding: 6px; text-align: left; font-size: 11px; }
                    .items-table th { background-color: #f0f0f0; font-weight: bold; }
                    .picked { background-color: #e6ffe6; }
                    .short-pick { background-color: #fff2e6; }
                    .summary { margin: 20px 0; padding: 10px; background-color: #f9f9f9; border: 1px solid #ddd; }
                    .signature { margin-top: 40px; display: flex; justify-content: space-between; }
                    .signature div { text-align: center; }
                    .signature-line { border-top: 1px solid #000; width: 150px; margin-top: 40px; }
                    @media print {
                        body { margin: 0; font-size: 10px; }
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>PICKING LIST</h2>
                    <p><strong>TO Number: ${task.toNumber}</strong></p>
                    <p>Creation Date: ${formatDateTime(task.tanggalDibuat)} | Warehouse: Main WH</p>
                </div>
                
                <div class="info-section">
                    <div class="info-row">
                        <span><strong>No Reservasi:</strong> ${task.noReservasi}</span>
                        <span><strong>Transaction Type:</strong> Picking</span>
                    </div>
                    <div class="info-row">
                        <span><strong>Requester:</strong> ${task.requester}</span>
                        <span><strong>Departemen:</strong> ${task.departemen}</span>
                    </div>
                    <div class="info-row">
                        <span><strong>Status:</strong> ${task.status}</span>
                        <span><strong>Print Date:</strong> ${new Date().toLocaleString('id-ID')}</span>
                    </div>
                </div>

                <div class="summary">
                    <strong>Summary:</strong> Total ${totalAllocations} Batch Alokasi | Picked: ${pickedItems} | Short-pick: ${shortPickItems} | Pending: ${totalAllocations - pickedItems - shortPickItems}
                </div>
                
                <table class="items-table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="vertical-align: middle; text-align: center; width: 5%;">No</th>
                            <th rowspan="2" style="vertical-align: middle; text-align: center; width: 10%;">Code Item</th>
                            <th rowspan="2" style="vertical-align: middle; text-align: center; width: 25%;">Material Description</th>
                            <th rowspan="2" style="vertical-align: middle; text-align: center; width: 10%;">Batch/Lot</th>
                            <th colspan="2" style="text-align: center; width: 20%;">Bin Location</th>
                            <th rowspan="2" style="vertical-align: middle; text-align: center; width: 8%;">Qty Alokasi</th>
                            <th rowspan="2" style="vertical-align: middle; text-align: center; width: 5%;">UoM</th>
                            <th rowspan="2" style="vertical-align: middle; text-align: center; width: 10%;">Qty Picked</th>
                        </tr>
                        <tr>
                            <th style="text-align: center; width: 10%;">Source Bin Location</th>
                            <th style="text-align: center; width: 10%;">Dest. Bin Location</th>
                        </tr>
                    </thead>
                    <tbody>
                    ${task.items.map((item, index) => `
                        <tr class="${item.status === 'Picked' ? 'picked' : item.status === 'Short-Pick' ? 'short-pick' : ''}" style="height: 40px;">
                            <td style="text-align: center; vertical-align: middle;">${index + 1}</td>
                            <td style="vertical-align: middle;">${item.kodeItem}</td>
                            <td style="vertical-align: middle;">${item.namaMaterial}</td>
                            <td style="text-align: center; vertical-align: middle;">${item.lotSerial}</td>
                            <td style="text-align: center; vertical-align: middle;">${item.sourceBin}</td>
                            <td style="text-align: center; vertical-align: middle;">${item.destBin}</td>
                            <td style="text-align: center; vertical-align: middle;">${item.qtyDiminta}</td>
                            <td style="text-align: center; vertical-align: middle;">${item.uom}</td>
                            <td style="text-align: center; vertical-align: middle;">${item.qtyPicked || ''}</td>
                        </tr>
                    `).join('')}
                    </tbody>
                </table>
                
                <div class="signature">
                    <div>
                        <p>Diserahkan Oleh:</p>
                        <div class="signature-line"></div>
                        <p>Warehouse Staff</p>
                    </div>
                    <div>
                        <p>Diterima Oleh:</p>
                        <div class="signature-line"></div>
                        <p>${task.requester}</p>
                    </div>
                    <div>
                        <p>Mengetahui:</p>
                        <div class="signature-line"></div>
                        <p>Supervisor</p>
                    </div>
                </div>
            </body>
        </html>
    `)
    
    printWindow.document.close()
    printWindow.focus()
    
    setTimeout(() => {
        printWindow.print()
        printWindow.close()
    }, 500)
}

// Lifecycle
onMounted(() => {
    fetchPickingList()
})

onBeforeUnmount(() => {
    stopCamera()
})
</script>
