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

    <!-- Modal Detail Picking Task -->
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
                <div class="text-sm text-blue-700">Total Items</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ getPickedItemsCount }}</div>
                <div class="text-sm text-green-700">Picked</div>
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

          <!-- Items Table -->
          <div class="border border-gray-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200" style="min-width: 1400px;">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">No</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Kode Item</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Nama Material</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Lot/Serial</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Source Bin</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Dest Bin</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Qty Diminta</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Qty Picked</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">UoM</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Status</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <tr v-for="(item, index) in selectedTask?.items || []" :key="index" :class="item.status === 'Picked' ? 'bg-green-50' : item.status === 'Short-Pick' ? 'bg-orange-50' : ''">
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ index + 1 }}</td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.kodeItem }}</td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ item.namaMaterial }}</td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ item.lotSerial }}</td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ item.sourceBin }}</td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ item.destBin }}</td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ item.qtyDiminta }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <input v-model="item.qtyPicked" type="number" :max="item.qtyDiminta" class="w-20 text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-900" @input="updateItemStatus(item)">
                    </td>
                    <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ item.uom }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">
                      <span :class="getItemStatusClass(item.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                        {{ item.status }}
                      </span>
                    </td>
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
              Progress: {{ getCompletedItemsCount }}/{{ selectedTask?.items?.length || 0 }} items completed
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

    <!-- Modal QR Scanner dengan Camera -->
    <div v-if="showQRScannerModal" class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: rgba(43, 51, 63, 0.67);">
      <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">QR Scanner - Item #{{ currentItemIndex + 1 }}</h3>
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
              <div class="text-gray-600">Lot: {{ currentScanItem.lotSerial }} | Qty: {{ currentScanItem.qtyDiminta }} {{ currentScanItem.uom }}</div>
              <div class="text-gray-600">{{ currentScanItem.sourceBin }} → {{ currentScanItem.destBin }}</div>
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
              <label class="block text-sm font-medium text-gray-700">Qty yang Dipick:</label>
              <input v-model="manualQty" type="number" :max="currentScanItem?.qtyDiminta" placeholder="Masukkan quantity..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
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

    <!-- Modal Summary Detail -->
    <div v-if="showSummaryModal" class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: rgba(43, 51, 63, 0.67);">
      <div class="bg-white rounded-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
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
              <div class="text-sm text-blue-700">Total Items</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
              <div class="text-2xl font-bold text-green-600">{{ getSummaryPickedItems }}</div>
              <div class="text-sm text-green-700">Items Picked</div>
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

          <!-- Detailed breakdown -->
          <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
              <h4 class="text-sm font-medium text-gray-900">Detail Items</h4>
            </div>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Item</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Material</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty Diminta</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty Picked</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">UoM</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Achievement</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr v-for="(item, index) in selectedSummaryTask?.items || []" :key="index" :class="getItemRowClass(item.status)">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ item.kodeItem }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.namaMaterial }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.qtyDiminta }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.qtyPicked || 0 }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">{{ item.uom }}</td>
                    <td class="px-4 py-3">
                      <span :class="getItemStatusClass(item.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                        {{ item.status }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                      {{ getItemAchievement(item) }}
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
  box: {
    'FOH-001': 'SHP001|FOH-001|LOT001|50|2025-12-31|ACTIVE',
    'FOH-002': 'SHP002|FOH-002|LOT002|100|2025-12-31|ACTIVE',
    'FOH-003': 'SHP003|FOH-003|LOT003|75|2025-12-31|ACTIVE',
    'PM-001': 'SHP004|PM-001|BATCH001|1000|2025-12-31|ACTIVE',
    'PM-002': 'SHP005|PM-002|BATCH002|1000|2025-12-31|ACTIVE',
    'RM-001': 'SHP006|RM-001|BATCH002|25|2025-12-31|ACTIVE',
    'RM-002': 'SHP007|RM-002|BATCH003|10|2025-12-31|ACTIVE'
  },
  bins: ['A-01-01', 'A-01-02', 'A-01-03', 'B-02-01', 'B-02-02', 'C-03-01', 'C-03-02', 
         'STAGING-001', 'PROD-LINE-A', 'KITCHEN-A']
}

// Initialize dummy data
const initDummyData = () => {
  pickingTasks.value = [
    {
      id: 1,
      toNumber: 'TO/20250918/0001',
      noReservasi: 'RSV/20250918/0001',
      tanggalDibuat: '2025-09-18T08:30:00',
      requester: 'John Doe',
      departemen: 'FOH',
      status: 'Pending',
      items: [
        {
          kodeItem: 'FOH-001',
          namaMaterial: 'Peralatan Makan',
          lotSerial: 'LOT001',
          sourceBin: 'A-01-01',
          destBin: 'STAGING-001',
          qtyDiminta: 50,
          qtyPicked: 0,
          uom: 'PCS',
          status: 'Pending'
        },
        {
          kodeItem: 'FOH-002',
          namaMaterial: 'Gelas Plastik',
          lotSerial: 'LOT002',
          sourceBin: 'A-01-02',
          destBin: 'STAGING-001',
          qtyDiminta: 100,
          qtyPicked: 0,
          uom: 'PCS',
          status: 'Pending'
        },
        {
          kodeItem: 'FOH-003',
          namaMaterial: 'Sendok Garpu Set',
          lotSerial: 'LOT003',
          sourceBin: 'A-01-03',
          destBin: 'STAGING-001',
          qtyDiminta: 75,
          qtyPicked: 0,
          uom: 'SET',
          status: 'Pending'
        }
      ]
    },
    {
      id: 2,
      toNumber: 'TO/20250918/0002',
      noReservasi: 'RSV/20250918/0002',
      tanggalDibuat: '2025-09-18T10:15:00',
      requester: 'Jane Smith',
      departemen: 'Production',
      status: 'In Progress',
      items: [
        {
          kodeItem: 'PM-001',
          namaMaterial: 'Botol Plastik',
          lotSerial: 'BATCH001',
          sourceBin: 'B-02-01',
          destBin: 'PROD-LINE-A',
          qtyDiminta: 1000,
          qtyPicked: 750,
          uom: 'PCS',
          status: 'Short-Pick'
        },
        {
          kodeItem: 'PM-002',
          namaMaterial: 'Tutup Botol',
          lotSerial: 'BATCH002',
          sourceBin: 'B-02-02',
          destBin: 'PROD-LINE-A',
          qtyDiminta: 1000,
          qtyPicked: 1000,
          uom: 'PCS',
          status: 'Picked'
        }
      ]
    },
    {
      id: 3,
      toNumber: 'TO/20250918/0003',
      noReservasi: 'RSV/20250917/0003',
      tanggalDibuat: '2025-09-17T14:20:00',
      requester: 'Bob Wilson',
      departemen: 'Kitchen',
      status: 'Completed',
      items: [
        {
          kodeItem: 'RM-001',
          namaMaterial: 'Tepung Terigu',
          lotSerial: 'BATCH002',
          sourceBin: 'C-03-01',
          destBin: 'KITCHEN-A',
          qtyDiminta: 25,
          qtyPicked: 25,
          uom: 'KG',
          status: 'Picked'
        },
        {
          kodeItem: 'RM-002',
          namaMaterial: 'Gula Pasir',
          lotSerial: 'BATCH003',
          sourceBin: 'C-03-02',
          destBin: 'KITCHEN-A',
          qtyDiminta: 10,
          qtyPicked: 10,
          uom: 'KG',
          status: 'Picked'
        }
      ]
    }
  ]
}

// Computed properties
const filteredTasks = computed(() => {
  if (filterStatus.value === 'ALL') {
    return pickingTasks.value
  }
  return pickingTasks.value.filter(task => task.status === filterStatus.value)
})

const getTotalItemsCount = computed(() => {
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
  if (!selectedTask.value?.items) return 0
  return selectedTask.value.items.filter(item => item.status === 'Pending').length
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
  return selectedTask.value.items.every(item => item.qtyPicked > 0)
})

const getScanStepText = computed(() => {
  const steps = [
    '',
    'Scan QR Box untuk validasi item',
    'Scan QR Source Bin untuk validasi lokasi',
    'Input quantity yang dipick',
    'Scan QR Dest Bin (opsional)'
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
  return selectedSummaryTask.value.items.filter(item => item.status === 'Pending').length
})

// Camera Methods
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
    alert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.')
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
  // Simulate QR code scanning
  // In production, you would use a library like jsQR or @zxing/library
  scanInterval = setInterval(() => {
    if (!isScanning.value || !videoElement.value) return
    
    // This is where you would implement actual QR code detection
    // For now, it's just a placeholder for the scanning loop
  }, 100)
}

const simulateScan = () => {
  if (!currentScanItem.value) return
  
  let simulatedQR = ''
  
  switch (scanStep.value) {
    case 1: // Simulate Box QR
      simulatedQR = dummyQRData.box[currentScanItem.value.kodeItem]
      break
    case 2: // Simulate Source Bin
      simulatedQR = currentScanItem.value.sourceBin
      break
    case 4: // Simulate Dest Bin
      simulatedQR = currentScanItem.value.destBin
      break
    default:
      alert('Step ini tidak memerlukan scan QR')
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
const getStatusClass = (status) => {
  const classes = {
    'Pending': 'bg-gray-100 text-gray-800',
    'In Progress': 'bg-yellow-100 text-yellow-800',
    'Completed': 'bg-green-100 text-green-800',
    'Short-Pick': 'bg-orange-100 text-orange-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getItemStatusClass = (status) => {
  const classes = {
    'Pending': 'bg-gray-100 text-gray-800',
    'Picked': 'bg-green-100 text-green-800',
    'Short-Pick': 'bg-orange-100 text-orange-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getItemRowClass = (status) => {
  const classes = {
    'Picked': 'bg-green-50',
    'Short-Pick': 'bg-orange-50',
    'Pending': 'bg-white'
  }
  return classes[status] || 'bg-white'
}

const getItemAchievement = (item) => {
  if (item.status === 'Pending') return '-'
  if (item.qtyDiminta === 0) return '0%'
  const percentage = Math.round((item.qtyPicked / item.qtyDiminta) * 100)
  return `${percentage}% (${item.qtyPicked}/${item.qtyDiminta})`
}

const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleString('id-ID', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const viewReservation = (noReservasi) => {
  alert(`Melihat detail reservasi: ${noReservasi}`)
}

const viewDetail = (task) => {
  selectedSummaryTask.value = task
  showSummaryModal.value = true
}

const startPicking = (task) => {
  selectedTask.value = task
  if (task.status === 'Pending') {
    const taskIndex = pickingTasks.value.findIndex(t => t.id === task.id)
    if (taskIndex !== -1) {
      pickingTasks.value[taskIndex].status = 'In Progress'
    }
  }
  showPickingModal.value = true
}

const closePickingModal = () => {
  showPickingModal.value = false
  selectedTask.value = null
  resetScanner()
}

const closeSummaryModal = () => {
  showSummaryModal.value = false
  selectedSummaryTask.value = null
}

const startItemQRScan = (item, index) => {
  if (item.status === 'Picked') return
  
  currentScanItem.value = item
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
    alert('Silakan scan atau input QR Code!')
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
  const qrParts = qrContent.split('|')
  if (qrParts.length < 2) {
    alert('Format QR Box tidak valid!')
    return
  }
  
  const itemCode = qrParts[1]
  const lotNo = qrParts[2]
  
  if (itemCode !== currentScanItem.value.kodeItem || lotNo !== currentScanItem.value.lotSerial) {
    alert(`Item tidak sesuai!\nHarus: ${currentScanItem.value.kodeItem} - ${currentScanItem.value.lotSerial}\nDi-scan: ${itemCode} - ${lotNo}`)
    return
  }
  
  scanStep.value = 2
  qrInput.value = ''
  alert(`✓ Item valid: ${currentScanItem.value.kodeItem} - ${currentScanItem.value.namaMaterial}`)
}

const validateSourceBin = (binCode) => {
  if (!currentScanItem.value) return
  
  if (binCode !== currentScanItem.value.sourceBin) {
    alert(`Lokasi bin salah!\nHarus: ${currentScanItem.value.sourceBin}\nDi-scan: ${binCode}`)
    return
  }
  
  scanStep.value = 3
  qrInput.value = ''
  manualQty.value = currentScanItem.value.qtyDiminta.toString()
  alert(`✓ Source Bin valid: ${binCode}`)
}

const confirmQuantity = () => {
  if (!manualQty.value || parseInt(manualQty.value) <= 0) {
    alert('Silakan input quantity yang valid!')
    return
  }
  
  const qtyPicked = parseInt(manualQty.value)
  const qtyDiminta = currentScanItem.value.qtyDiminta
  
  if (qtyPicked > qtyDiminta) {
    alert(`Quantity melebihi yang diminta!\nDiminta: ${qtyDiminta}\nInput: ${qtyPicked}`)
    return
  }
  
  currentScanItem.value.qtyPicked = qtyPicked
  
  if (qtyPicked === qtyDiminta) {
    currentScanItem.value.status = 'Picked'
    alert(`✓ Item berhasil dipick complete: ${qtyPicked}/${qtyDiminta}`)
  } else {
    currentScanItem.value.status = 'Short-Pick'
    alert(`⚠ Short-pick detected: ${qtyPicked}/${qtyDiminta}`)
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
    alert(`Destination bin salah!\nHarus: ${currentScanItem.value.destBin}\nDi-scan: ${binCode}`)
    return
  }
  
  alert(`✓ Destination Bin valid: ${binCode}`)
  finishCurrentItem()
}

const finishCurrentItem = () => {
  alert(`Item ${currentScanItem.value.kodeItem} berhasil diselesaikan!`)
  closeQRScanner()
  
  const remainingItems = selectedTask.value.items.filter(item => item.status === 'Pending')
  if (remainingItems.length === 0) {
    alert('Semua item sudah dipick! Silakan klik "Selesai Picking"')
  }
}

const prevScanStep = () => {
  if (scanStep.value > 1) {
    scanStep.value--
    qrInput.value = ''
  }
}

const updateItemStatus = (item) => {
  if (!item.qtyPicked || item.qtyPicked <= 0) {
    item.status = 'Pending'
    return
  }
  
  if (item.qtyPicked >= item.qtyDiminta) {
    item.status = 'Picked'
    item.qtyPicked = item.qtyDiminta
  } else {
    item.status = 'Short-Pick'
  }
}

const finishPicking = () => {
  if (!selectedTask.value) return
  
  const allItems = selectedTask.value.items
  const pickedItems = allItems.filter(item => item.status === 'Picked')
  const shortPickItems = allItems.filter(item => item.status === 'Short-Pick')
  
  let newStatus = 'Completed'
  let message = `Picking selesai!\n\nTotal items: ${allItems.length}\nPicked: ${pickedItems.length}`
  
  if (shortPickItems.length > 0) {
    newStatus = 'Short-Pick'
    message += `\nShort-pick: ${shortPickItems.length}`
    message += '\n\nSistem akan:\n- Update stok\n- Trigger reallocation untuk shortage\n- Notifikasi supervisor'
  } else {
    message += '\n\nSistem akan:\n- Update stok dari Source Bin\n- Pindahkan ke Destination Bin\n- Simpan transaksi picking'
  }
  
  const taskIndex = pickingTasks.value.findIndex(t => t.id === selectedTask.value.id)
  if (taskIndex !== -1) {
    pickingTasks.value[taskIndex].status = newStatus
  }
  
  alert(message)
  closePickingModal()
}

const printPickingList = (task) => {
  const printWindow = window.open('', '_blank')
  
  const totalItems = task.items.length
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
          <strong>Summary:</strong> Total ${totalItems} items | Picked: ${pickedItems} | Short-pick: ${shortPickItems} | Pending: ${totalItems - pickedItems - shortPickItems}
        </div>
        
        <table class="items-table">
          <tr>
            <th rowspan="2" style="vertical-align: middle; text-align: center; width: 5%;">No</th>
            <th rowspan="2" style="vertical-align: middle; text-align: center; width: 12%;">Code Item</th>
            <th rowspan="2" style="vertical-align: middle; text-align: center; width: 25%;">Material Description</th>
            <th rowspan="2" style="vertical-align: middle; text-align: center; width: 10%;">SN</th>
            <th colspan="2" style="text-align: center; width: 20%;">Bin Location</th>
            <th rowspan="2" style="vertical-align: middle; text-align: center; width: 8%;">Quantity</th>
            <th rowspan="2" style="vertical-align: middle; text-align: center; width: 5%;">UoM</th>
            <th rowspan="2" style="vertical-align: middle; text-align: center; width: 10%;">Resv. Qty</th>
          </tr>
          <tr>
            <th style="text-align: center; width: 10%;">Source Bin Location</th>
            <th style="text-align: center; width: 10%;">Dest. Bin Location</th>
          </tr>
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
  initDummyData()
})

onBeforeUnmount(() => {
  stopCamera()
})
</script>