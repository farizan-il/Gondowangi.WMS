<template>
  <AppLayout pageTitle="Dashboard" pageDescription="Ringkasan sistem warehouse management">
    <div class="min-h-screen bg-gray-50 p-6">
      <!-- Header -->
      <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Central Data WMS</h1>
            <p class="text-gray-600 mt-1">Kelola semua material di gudang</p>
          </div>
        </div>

        <!-- Alerts/Notifications -->
        <div v-if="localAlerts.length > 0" class="mb-4 space-y-2">
        <div v-for="alert in localAlerts" :key="alert.id" 
          :class="[
            getAlertClass(alert.type),
            { 
              // Put Away Filter (ID 0)
              'ring-2 ring-blue-500 ring-offset-2': activeAlertFilter === 'putAwayRequired' && alert.id === '0',
              // Karantina/QC Filter (ID 3)
              'ring-2 ring-orange-500 ring-offset-2': activeAlertFilter === 'requiresQC' && alert.id === '3',
              // Expired Soon Filter (ID 1)
              'ring-2 ring-yellow-500 ring-offset-2': activeAlertFilter === 'expiringSoon' && alert.id === '1',

              // Tambahkan indikator bisa diklik untuk alert yang ingin difilter
              'cursor-pointer hover:shadow-md transition duration-150 ease-in-out': ['0', '3', '1', '2'].includes(alert.id)
            }
          ]"
          class="p-3 rounded-lg flex items-center justify-between"
          @click="
            alert.id === '0' ? toggleAlertFilter('putAwayRequired') :
            alert.id === '3' ? toggleAlertFilter('requiresQC') :
            alert.id === '1' ? toggleAlertFilter('expiringSoon') :
            alert.id === '2' ? showExpiredMaterialsModal() : null
          " >
          <div class="flex items-center space-x-2">
            <svg v-if="alert.id === '0'" class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
            <svg v-if="alert.id === '3'" class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.276a2 2 0 010 2.553l-3.52 3.52c-.144.144-.33.225-.53.225H12V7h3.707l-3.52-3.52a2 2 0 012.553 0l3.52 3.52z" />
            </svg>
            <svg v-if="alert.id === '1'" class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ alert.message }}</span>
          </div>
          
          <button @click.stop="dismissAlert(alert.id)" class="text-current opacity-70 hover:opacity-100 p-1"> 
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

        <!-- Modal: Expired Materials List -->
        <div v-if="showExpiredModal"
          class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[9999]"
          @click.self="closeExpiredModal">
          <div class="bg-white rounded-lg max-w-6xl w-full mx-4 max-h-[80vh] overflow-hidden">
            <div class="p-6">
              <!-- Header -->
              <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-red-600 flex items-center gap-2">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  Material Expired / Near Expiry ({{ expiredMaterialsCount }})
                </h3>
                <button @click="closeExpiredModal" class="text-gray-400 hover:text-gray-600">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <!-- Table -->
              <div class="overflow-y-auto max-h-[60vh]">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50 sticky top-0">
                    <tr>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Material</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lot</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expired Date</th>
                      <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="material in expiredMaterials" :key="material.id" class="hover:bg-gray-50">
                      <td class="px-4 py-3 text-sm text-gray-900">{{ material.kode }}</td>
                      <td class="px-4 py-3 text-sm text-gray-900">{{ material.nama }}</td>
                      <td class="px-4 py-3 text-sm text-gray-900">{{ material.lot }}</td>
                      <td class="px-4 py-3 text-sm">
                        <span :class="isInQrtBin(material.lokasi) ? 'text-blue-600 font-medium' : 'text-orange-600 font-medium'">{{ material.lokasi }}</span>
                      </td>
                      <td class="px-4 py-3 text-sm text-gray-900">{{ material.qty }} {{ material.uom }}</td>
                      <td class="px-4 py-3 text-sm">
                        <div class="flex flex-col gap-1">
                          <span :class="getExpiredClass(material.expiredDate)">{{ formatDate(material.expiredDate) }}</span>
                          <span :class="getExpiredBadgeClass(material.expiredDate)" class="px-2 py-0.5 text-xs font-semibold rounded-full inline-flex items-center gap-1 w-fit">
                            <svg v-if="getDaysUntilExpired(material.expiredDate) < 0" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <svg v-else class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            {{ getExpiredBadgeText(material.expiredDate) }}
                          </span>
                        </div>
                      </td>
                      <td class="px-4 py-3">
                        <span :class="getStatusClass(material.status)" class="px-2 py-1 text-xs font-semibold rounded-full">{{ material.status }}</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Footer -->
              <div class="flex justify-end mt-6 pt-4 border-t border-gray-200">
                <button @click="closeExpiredModal"
                  class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                  Tutup
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Expired Materials Widget -->
        <div v-if="expiredMaterialsCount > 0" class="mb-6">
          <form @submit.prevent="initiateReqc" method="POST">
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white card-dashboard">
              <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                <div class="flex items-start md:items-center space-x-4">
                  <!-- Animated Warning Icon -->
                  <div class="relative flex-shrink-0">
                    <div class="absolute inset-0 bg-white/20 rounded-full animate-ping"></div>
                    <div class="relative bg-white/30 p-4 rounded-full">
                      <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                      </svg>
                    </div>
                  </div>
                  <!-- Info -->
                  <div class="min-w-0">
                    <h3 class="text-xl md:text-2xl font-bold mb-1 break-words">{{ expiredMaterialsCount }} Material Expired / Near Expiry!</h3>
                    <p class="text-red-100 text-sm leading-relaxed break-words">Pilih material yang sudah/hampir expired (H-30) di bin QRT untuk proses Re-QC</p>
                    <p class="text-red-100 text-xs mt-1 opacity-80">Bin QRT: QRT-HALAL, QRT-NON HALAL, QRT-HALAL-AC</p>
                  </div>
                </div>
                <!-- Action Button -->
                <button 
                  type="submit"
                  :disabled="selectedExpiredMaterials.length === 0"
                  :class="selectedExpiredMaterials.length > 0 ? 'bg-white hover:bg-red-50' : 'bg-white/50 cursor-not-allowed'"
                  class="text-red-600 px-6 py-3 rounded-lg font-semibold shadow-lg transition-all hover:scale-105 flex items-center justify-center space-x-2 group w-full md:w-auto flex-shrink-0">
                  <span>Proses Re-QC ({{ selectedExpiredMaterials.length }})</span>
                  <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                  </svg>
                </button>
              </div>

              <!-- Expired Material List with Checkboxes -->
              <div class="mt-4 pt-4 border-t border-white/20">
                <div class="flex items-center justify-between mb-3">
                  <p class="text-sm text-red-100">Pilih material untuk Re-QC:</p>
                  <button type="button" @click="toggleSelectAllExpired" class="text-xs text-white hover:text-red-100 underline">
                    {{ selectedExpiredMaterials.length === expiredMaterials.length ? 'Batalkan Semua' : 'Pilih Semua' }}
                  </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-60 overflow-y-auto">
                  <label 
                    v-for="material in expiredMaterials" 
                    :key="material.id" 
                    :class="[
                      'flex items-start gap-3 p-3 rounded cursor-pointer transition-colors',
                      isInQrtBin(material.lokasi) ? 'bg-white/10 hover:bg-white/20' : 'bg-orange-500/50 hover:bg-orange-500/60'
                    ]">
                    <input 
                      type="checkbox" 
                      :value="getInventoryStockId(material)"
                      v-model="selectedExpiredMaterials"
                      class="mt-1 w-4 h-4 rounded">
                    <div class="flex-1 text-sm">
                      <div class="font-semibold flex items-center gap-2">
                        {{ material.nama }}
                        <span v-if="!isInQrtBin(material.lokasi)" class="px-1.5 py-0.5 bg-orange-600 text-white text-[9px] font-bold rounded">
                          Belum QRT
                        </span>
                      </div>
                      <div class="text-red-100 text-xs mt-0.5">
                        Exp: {{ formatDate(material.expiredDate) }} • {{ material.lokasi }} • {{ material.lot }}
                      </div>
                      <div v-if="!isInQrtBin(material.lokasi)" class="text-orange-200 text-[10px] mt-1">
                        <a href="/transaction/bin-to-bin" class="underline hover:text-white">Bin-to-Bin dulu →</a>
                      </div>
                    </div>
                  </label>
                </div>
                <p v-if="expiredMaterialsCount > 10" class="text-xs text-red-100 mt-2">
                  +{{ expiredMaterialsCount - 10 }} material lainnya (scroll untuk lihat semua)
                </p>
              </div>
            </div>
          </form>
        </div>

        <!-- Toolbar -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
          <div class="flex flex-wrap gap-4 items-center justify-between">
            <!-- Left Side - Search & Filters -->
            <div class="flex flex-wrap gap-4 items-center flex-1">
              <!-- Search Box -->
              <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                  stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input v-model="searchQuery" type="text" placeholder="Cari kode, nama material, atau lot..."
                  class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full md:w-80 bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              </div>

              <!-- Filters -->
              <select v-model="filterStatus" class="px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900">
                <option value="">Semua Status</option>
                <option value="KARANTINA">Karantina</option>
                <option value="RELEASED">Released</option>
                <!-- <option value="HOLD">Hold</option> -->
                <option value="REJECTED">Rejected</option>
              </select>

              <select v-model="filterType" class="px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900">
                <option value="">Semua Tipe</option>
                <option value="Raw Material">Raw Material</option>
                <option value="Packaging">Packaging Material</option>
              </select>

              <select v-model="filterLocation"
                class="px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900">
                <option value="">Semua Lokasi</option>
                <option v-for="location in uniqueLocations" :key="location" :value="location">{{ location }}</option>
              </select>

              <!-- Sort Filter -->
              <select v-model="sortOption" class="px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900">
                 <option value="default">Default Sort (Kode)</option>
                 <option value="newest">Terbaru (Newest)</option>
                 <option value="oldest">Terlama (Oldest)</option>
              </select>
            </div>

            
            <div class="flex gap-2">
              <!--<button @click="openImportModal"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <span>Import</span>
              </button>  -->

              <button @click="showPrintAllModal"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Print All QR</span>
              </button>

              <button @click="exportData"
                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Export</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Table (Desktop Only) -->
      <div class="hidden md:block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th v-for="column in tableColumns" :key="column.key" @click="sortBy(column.key)"
                  class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors">
                  <div class="flex items-center space-x-1">
                    <span>{{ column.label }}</span>
                    <svg v-if="sortColumn === column.key" class="w-4 h-4"
                      :class="sortDirection === 'asc' ? '' : 'transform rotate-180'" fill="none" stroke="currentColor"
                      viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                  </div>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="item in filteredItems" :key="item.id" @click="openDetailPanel(item)" :class="getRowClass(item)"
                class="hover:bg-gray-50 cursor-pointer transition-colors">
                <td class="px-4 py-3 whitespace-nowrap">
                  <span :class="item.type === 'Raw Material' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'"
                    class="px-2 py-1 text-xs font-semibold rounded-full">
                    {{ item.type }}
                  </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ item.subkategori || '-' }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.kode }}</td>
                <td class="px-4 py-3 text-sm text-gray-900">{{ item.nama }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ item.lot }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ item.lokasi }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ formatQty(item.qty, item.type) }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ item.uom }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                  <span :class="getExpiredClass(item.expiredDate)">{{ formatDate(item.expiredDate) }}</span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                  <span :class="getStatusClass(item.qr_type)" class="px-2 py-1 text-xs font-semibold rounded-full border">
                    {{ item.status }}
                  </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm space-x-2 flex items-center">
    
                <template v-if="item.requiresPutAway">
                    <button @click.stop="router.get('/transaction/putaway-transfer')"
                      class="px-2 py-1 text-xs font-semibold bg-indigo-500 text-white rounded hover:bg-indigo-700 transition-colors flex items-center space-x-1" 
                      title="Put Away Cepat ke lokasi permanen">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                      </svg>
                      <span>Put Away</span>
                  </button>

                    <button @click.stop="openQRDetailModal(item)" 
                        class="text-blue-600 hover:text-blue-900 transition-colors ml-2" 
                        title="Cetak/Lihat Detail QR">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h2M4 4h5a3 3 0 000 6H4V4zm3 2h.01M8 16h.01" />
                        </svg>
                    </button>
                </template>

                <template v-else>
                    <button @click.stop="openQRDetailModal(item)" 
                        class="text-blue-600 hover:text-blue-900 transition-colors" 
                        title="Cetak/Lihat Detail QR">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h2M4 4h5a3 3 0 000 6H4V4zm3 2h.01M8 16h.01" />
                        </svg>
                    </button>

                    <!-- <button @click.stop="openBinToBinModal(item)"
                        class="text-green-600 hover:text-green-900 transition-colors ml-2" 
                        title="Bin to Bin Transfer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </button> -->
                </template>
            </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="filteredItems.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-4.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 009.586 13H7" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
          <p class="mt-1 text-sm text-gray-500">Data material tidak ditemukan atau filter terlalu spesifik</p>
        </div>
      </div>

      <!-- Mobile Card View (Mobile Only) -->
      <div class="md:hidden space-y-4">
        <div v-for="item in filteredItems" :key="item.id" @click="openDetailPanel(item)" 
             class="bg-white rounded-lg shadow-sm p-4 border border-gray-200 active:scale-[0.98] transition-transform">
            
            <!-- Header -->
            <div class="flex justify-between items-start mb-3">
                <span :class="item.type === 'Raw Material' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'"
                      class="px-2 py-1 text-xs font-semibold rounded-full">
                    {{ item.type }}
                </span>
                <span :class="getStatusClass(item.qr_type)" class="px-2 py-1 text-xs font-semibold rounded-full border">
                    {{ item.status }}
                </span>
            </div>

            <!-- Content -->
            <div class="mb-3">
                <h4 class="font-bold text-gray-900 text-base mb-1">{{ item.nama }}</h4>
                <div class="text-sm text-gray-500 font-mono">{{ item.kode }} • {{ item.lot }}</div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-2 gap-y-2 gap-x-4 text-sm text-gray-600 mb-4 bg-gray-50 p-2 rounded">
                <div>
                    <span class="text-xs text-gray-500 block">Lokasi</span>
                    <span class="font-medium text-gray-900">{{ item.lokasi }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Qty</span>
                    <span class="font-medium text-gray-900">{{ formatQty(item.qty, item.type) }} {{ item.uom }}</span>
                </div>
                <div class="col-span-2">
                    <span class="text-xs text-gray-500 block">Expired</span>
                    <span :class="getExpiredClass(item.expiredDate)">{{ formatDate(item.expiredDate) }}</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-2 border-t pt-3">
                 <template v-if="item.requiresPutAway">
                    <button @click.stop="router.get('/transaction/putaway-transfer')"
                      class="flex-1 px-3 py-2 text-xs font-semibold bg-indigo-500 text-white rounded hover:bg-indigo-700 transition-colors flex items-center justify-center gap-1">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                      </svg>
                      Put Away
                  </button>
                 </template>
                 
                 <button @click.stop="openQRDetailModal(item)" 
                    class="px-3 py-2 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h2M4 4h5a3 3 0 000 6H4V4zm3 2h.01M8 16h.01" />
                    </svg>
                    QR Info
                </button>
            </div>
        </div>

        <!-- Empty State Mobile -->
        <div v-if="filteredItems.length === 0" class="text-center py-8 bg-white rounded-lg shadow-sm border border-gray-200">
             <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-4.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 009.586 13H7" />
             </svg>
             <p class="mt-2 text-sm text-gray-500">Tidak ada data ditemukan</p>
        </div>
      </div>


      <!-- Bin to Bin Transfer Modal -->
      <div v-if="showBinToBinModal"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[9999]">
        <div class="bg-white rounded-lg p-6 w-full max-w-3xl max-h-screen overflow-y-auto">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-900">Bin to Bin Transfer</h3>
            <button @click="closeBinToBinModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Material Info -->
          <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Informasi Material</h4>
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div>
                <span class="text-gray-600">Kode:</span>
                <span class="ml-2 font-medium text-gray-900">{{ transferData.material?.kode }}</span>
              </div>
              <div>
                <span class="text-gray-600">Nama:</span>
                <span class="ml-2 font-medium text-gray-900">{{ transferData.material?.nama }}</span>
              </div>
              <div>
                <span class="text-gray-600">Lot:</span>
                <span class="ml-2 font-medium text-gray-900">{{ transferData.material?.lot }}</span>
              </div>
              <div>
                <span class="text-gray-600">Lokasi Saat Ini:</span>
                <span class="ml-2 font-medium text-blue-600">{{ transferData.material?.lokasi }}</span>
              </div>
              <div>
                <span class="text-gray-600">Qty Tersedia:</span>
                <span class="ml-2 font-medium text-gray-900">{{ transferData.material?.qty }} {{
                  transferData.material?.uom }}</span>
              </div>
            </div>
          </div>

          <!-- Transfer Form -->
          <div class="space-y-4">
            <!-- Qty to Transfer -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Quantity Transfer <span class="text-red-500">*</span>
              </label>
              <div class="flex items-center space-x-2">
                <input v-model.number="transferData.qty" type="number" :max="transferData.material?.qty" min="0"
                  class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500"
                  placeholder="Masukkan qty yang akan di-transfer">
                <span class="text-gray-600">{{ transferData.material?.uom }}</span>
              </div>
              <p v-if="transferData.qty > (transferData.material?.qty || 0)" class="text-red-500 text-xs mt-1">
                Qty transfer melebihi qty tersedia!
              </p>
            </div>

            <!-- Destination Bin Selection -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Bin Tujuan <span class="text-red-500">*</span>
              </label>
              <select v-model="transferData.destinationBin"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Bin Tujuan --</option>
                <optgroup label="Standard Storage">
                  <option v-for="bin in availableBins.standard" :key="bin.code" :value="bin.code">
                    {{ bin.code }} - {{ bin.zone }} ({{ bin.currentItems }}/{{ bin.maxItems }} items)
                  </option>
                </optgroup>
                <optgroup label="Hazardous Storage">
                  <option v-for="bin in availableBins.hazardous" :key="bin.code" :value="bin.code">
                    {{ bin.code }} - {{ bin.zone }} ({{ bin.currentItems }}/{{ bin.maxItems }} items)
                  </option>
                </optgroup>
              </select>
            </div>

            <!-- Selected Bin Details -->
            <div v-if="transferData.destinationBin" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
              <h5 class="text-sm font-medium text-blue-900 mb-2">Detail Bin Tujuan</h5>
              <div class="space-y-1 text-sm text-blue-800">
                <div class="flex justify-between">
                  <span>Kode Bin:</span>
                  <span class="font-medium">{{ selectedDestBin?.code }}</span>
                </div>
                <div class="flex justify-between">
                  <span>Zona:</span>
                  <span class="font-medium">{{ selectedDestBin?.zone }}</span>
                </div>
                <div class="flex justify-between">
                  <span>Kapasitas:</span>
                  <span class="font-medium">{{ selectedDestBin?.currentItems }}/{{ selectedDestBin?.maxItems }}
                    items</span>
                </div>
                <div class="flex justify-between">
                  <span>Status:</span>
                  <span :class="getBinCapacityClass(selectedDestBin)" class="px-2 py-0.5 text-xs rounded-full">
                    {{ getBinCapacityText(selectedDestBin) }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Transfer Reason -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Alasan Transfer <span class="text-red-500">*</span>
              </label>
              <select v-model="transferData.reason"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 mb-2">
                <option value="">-- Pilih Alasan --</option>
                <option value="optimization">Optimasi Ruang Gudang</option>
                <option value="consolidation">Konsolidasi Material</option>
                <option value="reorganization">Reorganisasi Zona</option>
                <option value="safety">Alasan Keamanan</option>
                <option value="quality">Segregasi Quality</option>
                <option value="other">Lainnya</option>
              </select>

              <textarea v-if="transferData.reason === 'other' || transferData.reason" v-model="transferData.notes"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500"
                placeholder="Catatan tambahan..."></textarea>
            </div>

            <!-- Approval Section (Admin/Manager) -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
              <div class="flex items-start space-x-2">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd"
                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd" />
                </svg>
                <div class="flex-1">
                  <h5 class="text-sm font-medium text-yellow-900">Persetujuan Manager</h5>
                  <p class="text-xs text-yellow-800 mt-1">
                    Transfer Order Bin to Bin akan dibuat dan memerlukan approval dari manager/admin sebelum dapat
                    dieksekusi.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3 justify-end mt-6 pt-6 border-t border-gray-200">
            <button @click="closeBinToBinModal"
              class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
              Batal
            </button>
            <button @click="createBinToBinTO" :disabled="!isTransferValid"
              :class="isTransferValid ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed'"
              class="px-4 py-2 text-white rounded-lg transition-colors flex items-center space-x-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <span>Buat Transfer Order</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Detail Panel -->
      <div v-if="selectedItem"
        class="fixed inset-y-0 right-0 w-full md:w-96 bg-white shadow-xl z-[998] transform transition-transform duration-300 border-l border-gray-200"
        :class="showDetailPanel ? 'translate-x-0' : 'translate-x-full'" style="max-height: 100vh; overflow: hidden;">
        <div class="h-full flex flex-col">
          <!-- Panel Header -->
          <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-medium text-gray-900">Detail Material</h3>
              <button @click="closeDetailPanel" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Panel Content -->
          <div class="flex-1 overflow-y-auto">
            <div class="p-6 space-y-6">
              <!-- Basic Info -->
              <div>
                <h4 class="text-sm font-medium text-gray-900 mb-3">Informasi Material</h4>
                <div class="space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Tipe:</span>
                    <span :class="selectedItem.type === 'RM' ? 'text-blue-600' : 'text-green-600'"
                      class="font-medium">{{ selectedItem.type }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Kode:</span>
                    <span class="font-medium text-gray-900">{{ selectedItem.kode }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Nama:</span>
                    <span class="font-medium text-gray-900 text-right">{{ selectedItem.nama }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Lot/Serial:</span>
                    <span class="font-medium text-gray-900">{{ selectedItem.lot }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Lokasi:</span>
                    <span class="font-medium text-gray-900">{{ selectedItem.lokasi }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Qty:</span>
                    <span class="font-medium text-gray-900">{{ selectedItem.qty }} {{ selectedItem.uom }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Expired:</span>
                    <span :class="getExpiredClass(selectedItem.expiredDate)" class="font-medium">{{
                      formatDate(selectedItem.expiredDate) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span :class="getStatusClass(selectedItem.status)"
                      class="px-2 py-1 text-xs font-semibold rounded-full">{{ selectedItem.status }}</span>
                  </div>
                </div>
              </div>

              <!-- QR Code Section -->
              <div>
                <h4 class="text-sm font-medium text-gray-900 mb-3">QR Code & Dokumen</h4>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                  <div
                    class="w-32 h-32 bg-white mx-auto mb-2 flex items-center justify-center border-2 border-dashed border-gray-300 rounded-lg overflow-hidden">
                    <img v-if="selectedItem.qr_image_url" :src="selectedItem.qr_image_url" alt="QR Code" class="w-full h-full object-contain">
                    <div v-else class="text-xs text-gray-400">Generating...</div>
                  </div>
                  <p class="text-xs text-gray-500 mb-2">{{ selectedItem.kode }}-{{ selectedItem.lot }}</p>
                  <button @click="printQR(selectedItem)" class="text-blue-600 text-sm hover:underline font-medium">
                    Print QR Code
                  </button>
                </div>
                <div class="mt-3 space-y-1 text-sm">
                  <div class="flex justify-between">
                    <span class="text-gray-600">No PO:</span>
                    <span class="text-blue-600 font-medium">{{ selectedItem.no_po || '-' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">No Surat Jalan:</span>
                    <span class="text-blue-600 font-medium">{{ selectedItem.no_surat_jalan || '-' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">No Incoming:</span>
                    <span class="text-blue-600 font-medium">{{ selectedItem.no_incoming || '-' }}</span>
                  </div>
                </div>
              </div>

              <!-- Movement History -->
              <div>
                <h4 class="text-sm font-medium text-gray-900 mb-3">Riwayat Pergerakan</h4>
                <div v-if="selectedItem.history.length > 0" class="space-y-3">
                  <div v-for="history in selectedItem.history" :key="history.id" class="flex items-start space-x-3">
                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                    <div class="flex-1 text-sm">
                      <div class="flex justify-between items-start">
                        <span class="text-gray-900 font-medium">{{ getSimpleMovementLabel(history.action) }}</span>
                        <span class="text-gray-500 text-xs">{{ formatDateTime(history.date) }}</span>
                      </div>
                      <p class="text-gray-600 text-xs mt-1">{{ getSimpleMovementDetail(history.action, history.detail) }}</p>
                      <p class="text-gray-500 text-xs">Oleh: {{ history.user }}</p>
                    </div>
                  </div>
                </div>
                <div v-else class="text-sm text-gray-500 italic p-3 bg-gray-50 rounded-lg">
                  Belum ada riwayat pergerakan untuk item ini.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Backdrop for detail panel -->
      <div v-if="showDetailPanel" @click="closeDetailPanel" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[997]">
      </div>
      <div v-if="showQRDetailModal && qrItem"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[10000]">
        <div class="bg-white rounded-lg p-6 w-full max-w-sm">
          <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h3 class="text-xl font-semibold text-gray-900">
              QR Code: <span :class="qrItem.qr_type === 'Karantina' ? 'text-orange-600' : 'text-green-600'">
                {{ qrItem.qr_type }}
              </span>
            </h3>
            <button @click="closeQRDetailModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="text-center space-y-4">
            <div id="qr-code-to-print" class="p-4 border border-gray-200 rounded-lg inline-block">
                <div class="w-48 h-48 bg-white mx-auto flex items-center justify-center overflow-hidden">
                    <img v-if="qrItem.qr_image_url" :src="qrItem.qr_image_url" alt="QR Code" class="w-full h-full object-contain">
                    <span v-else class="text-xs text-gray-600">Generating...</span>
                </div>
                </div>
            
            <p class="text-sm font-medium text-gray-700">Kode: **{{ qrItem.kode }}-{{ qrItem.lot }}**</p>
            <p class="text-xs text-gray-500">
              Data Terenkripsi: `{{ qrItem.qr_data.substring(0, 40) }}...`
            </p>

            <div class="mt-6 flex justify-between gap-3">
              <button @click="closeQRDetailModal"
                class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Tutup
              </button>
              <button @click="triggerPrint(qrItem)"
                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2v2H5v-2h2M17 17v-5H7v5m12 0a2 2 0 01-2 2H7a2 2 0 01-2-2m2-5V4a2 2 0 012-2h6a2 2 0 012 2v8M7 7h10"/></svg>
                <span>Cetak QR</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Modern Error Modal for Re-QC -->
      <div v-if="showErrorModal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 backdrop-blur-sm animate-fadeIn">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden animate-slideUp">
          <!-- Header with Gradient -->
          <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="bg-white/20 p-2 rounded-full">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <h3 class="text-xl font-bold text-white">Validasi Gagal</h3>
            </div>
          </div>
          
          <!-- Body -->
          <div class="p-6">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
              <p class="text-sm text-red-800 whitespace-pre-line leading-relaxed">{{ errorModalMessage }}</p>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 flex items-start gap-2">
              <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
              </svg>
              <div class="text-xs text-blue-800">
                <p class="font-semibold mb-1">Solusi:</p>
                <p>Lakukan <a href="/transaction/bin-to-bin" class="underline hover:text-blue-900 font-medium">Bin-to-Bin Transfer</a> terlebih dahulu ke bin QRT (QRT-HALAL, QRT-NON HALAL, atau QRT-HALAL-AC)</p>
              </div>
            </div>
          </div>
          
          <!-- Footer -->
          <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
            <button 
              @click="closeErrorModal"
              class="px-6 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold rounded-lg shadow-lg transition-all hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
              Tutup
            </button>
          </div>
        </div>
      </div>

      <!-- Print All QR Modal -->
      <div v-if="showPrintAllQRModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]"
        style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-xl font-bold text-gray-900">Print All QR Codes</h3>
              <button @click="closePrintAllModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Statistics Display -->
            <div v-if="inventoryStats" class="space-y-4 mb-6">
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-blue-900 mb-3 flex items-center">
                  <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                  </svg>
                  Statistik Inventory On-Hand
                </h4>
                <div class="grid grid-cols-2 gap-3 text-sm">
                  <div class="bg-white rounded p-3 border border-green-200">
                    <div class="text-gray-600 text-xs font-medium">Released</div>
                    <div class="text-green-700 text-2xl font-bold">{{ inventoryStats.released }}</div>
                    <div class="text-gray-500 text-xs">SKU</div>
                  </div>
                  <div class="bg-white rounded p-3 border border-orange-200">
                    <div class="text-gray-600 text-xs font-medium">Karantina</div>
                    <div class="text-orange-700 text-2xl font-bold">{{ inventoryStats.karantina }}</div>
                    <div class="text-gray-500 text-xs">SKU</div>
                  </div>
                  <div class="bg-white rounded p-3 border border-red-200">
                    <div class="text-gray-600 text-xs font-medium">Rejected</div>
                    <div class="text-red-700 text-2xl font-bold">{{ inventoryStats.rejected }}</div>
                    <div class="text-gray-500 text-xs">SKU</div>
                  </div>
                  <div class="bg-white rounded p-3 border border-yellow-200" v-if="inventoryStats.expired > 0">
                    <div class="text-gray-600 text-xs font-medium">Expired</div>
                    <div class="text-yellow-700 text-2xl font-bold">{{ inventoryStats.expired }}</div>
                    <div class="text-gray-500 text-xs">SKU</div>
                  </div>
                </div>
              </div>

              <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-sm font-medium text-indigo-900">Total QR Codes:</span>
                  <span class="text-2xl font-bold text-indigo-700">{{ inventoryStats.total_qr_codes }}</span>
                </div>
                <div class="flex items-center justify-between text-xs text-indigo-700">
                  <span>Estimasi Waktu:</span>
                  <span class="font-semibold">~{{ Math.ceil(inventoryStats.estimated_time_seconds / 60) }} menit</span>
                </div>
              </div>

              <div v-if="inventoryStats.expired > 0" class="bg-yellow-50 border border-yellow-300 rounded-lg p-3">
                <div class="flex items-center">
                  <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                  </svg>
                  <div class="text-sm">
                    <span class="font-semibold text-yellow-900">Perhatian!</span>
                    <span class="text-yellow-800"> Material expired akan tetap dicetak.</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
              <button @click="closePrintAllModal"
                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                Batal
              </button>
              <button @click="proceedToPrintAll"
                :disabled="isPrintingAllQR"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:bg-indigo-300 flex items-center">
                <svg v-if="!isPrintingAllQR" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <svg v-else class="animate-spin h-5 w-5 mr-2 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isPrintingAllQR ? 'Processing...' : 'Proceed to Print' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading Bar for Print All QR -->
      <div v-if="showPrintAllProgress"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[10000]"
        style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg p-8 max-w-md mx-4 w-full">
          <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">Generating QR Codes</h3>
          
          <!-- Progress Bar -->
          <div class="mb-4">
            <div class="flex justify-between text-sm text-gray-600 mb-2">
              <span>Progress</span>
              <span>{{ printAllProgress }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
              <div 
                class="bg-indigo-600 h-4 rounded-full transition-all duration-300 ease-out"
                :style="{ width: printAllProgress + '%' }">
              </div>
            </div>
          </div>

          <!-- Status Text -->
          <div class="text-center text-sm text-gray-700">
            <p class="font-medium">{{ printAllStatusText }}</p>
            <p class="text-xs text-gray-500 mt-1">Mohon tunggu, jangan tutup browser...</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, onMounted, nextTick, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import QRCode from 'qrcode'

const activeAlertFilter = ref<string>('');

const props = defineProps<{
    materialItems: MaterialItem[],
    alerts: Alert[]
}>();

// Types
interface MaterialItem {
  id: string
  type: 'Raw Material' | 'Packaging'
  subkategori?: string
  kode: string
  nama: string
  lot: string
  lokasi: string
  qty: number
  uom: string
  expiredDate: string
  status: 'Waiting QC' | 'Karantina' | 'Released' | 'Reject' | 'In Production' | 'Returned'
  history: HistoryItem[]
  qr_type: 'Karantina' | 'Released' | 'Waiting QC' | 'Reject'
  qr_data?: string // Added for consistency
  qr_image_url?: string // Added to store generated image URL
  entry_date?: string // Added for sorting
  no_po?: string // Purchase Order Number
  no_surat_jalan?: string // Delivery Note Number
  no_incoming?: string // Incoming Goods Number
}

interface HistoryItem {
  id: string
  date: string
  action: string
  detail: string
  user: string
}

interface Alert {
  id: string
  type: 'error' | 'warning' | 'info'
  message: string
}

interface BinLocation {
  code: string
  zone: string
  warehouse: string
  type: 'STD' | 'HAZ' | 'QTN' | 'FG'
  currentItems: number
  maxItems: number
  capacity: string
}

interface TransferData {
  material: MaterialItem | null
  qty: number
  destinationBin: string
  reason: string
  notes: string
}

// Reactive data
const isDarkMode = ref(false)
const searchQuery = ref('')
const filterStatus = ref('')
const filterType = ref('')
const filterLocation = ref('')
const sortColumn = ref('kode')
const sortDirection = ref<'asc' | 'desc'>('asc')
const selectedItem = ref<MaterialItem | null>(null)
const showDetailPanel = ref(false)
const showBinToBinModal = ref(false)
const showQRDetailModal = ref(false)
const qrItem = ref<MaterialItem | null>(null)
const localAlerts = ref<Alert[]>([...props.alerts]);
const sortOption = ref<string>('default'); // Sort Option Ref

// Print All QR States
const showPrintAllQRModal = ref(false)
const inventoryStats = ref<any>(null)
const isPrintingAllQR = ref(false)
const showPrintAllProgress = ref(false)
const printAllProgress = ref(0)
const printAllStatusText = ref('')
const allMaterialsData = ref<any[]>([])

// Watch Sort Option
watch(sortOption, (newVal) => {
    if (newVal === 'newest') {
        sortColumn.value = 'entry_date';
        sortDirection.value = 'desc';
    } else if (newVal === 'oldest') {
        sortColumn.value = 'entry_date';
        sortDirection.value = 'asc';
    } else {
        sortColumn.value = 'kode';
        sortDirection.value = 'asc';
    }
});

// Transfer Data
const transferData = ref<TransferData>({
  material: null,
  qty: 0,
  destinationBin: '',
  reason: '',
  notes: ''
})

// Bin Locations Data
const binLocations = ref<BinLocation[]>([])

onMounted(async () => {
  try {
    const response = await axios.get('/master-data/bins');
    binLocations.value = response.data;
  } catch (error) {
    console.error('Error fetching bin locations:', error);
  }
});

// Table columns
const tableColumns = [
  { key: 'type', label: 'kategori' },
  { key: 'subkategori', label: 'Sub Kategori' },
  { key: 'kode', label: 'Kode' },
  { key: 'nama', label: 'Nama Material' },
  { key: 'lot', label: 'Serial/Lot' },
  { key: 'lokasi', label: 'Lokasi' },
  { key: 'qty', label: 'Qty' },
  { key: 'uom', label: 'UoM' },
  { key: 'expiredDate', label: 'Expired Date' },
  { key: 'status', label: 'Status' }
]

// Computed properties
const uniqueLocations = computed(() => {
    return [...new Set(props.materialItems.map(item => item.lokasi))].sort() 
})

const availableBins = computed(() => {
  const currentBin = transferData.value.material?.lokasi
  return {
    standard: binLocations.value.filter(bin => bin.type === 'STD' && bin.code !== currentBin),
    hazardous: binLocations.value.filter(bin => bin.type === 'HAZ' && bin.code !== currentBin)
  }
})

const selectedDestBin = computed(() => {
  return binLocations.value.find(bin => bin.code === transferData.value.destinationBin)
})

const isTransferValid = computed(() => {
  return (
    transferData.value.material !== null &&
    transferData.value.qty > 0 &&
    transferData.value.qty <= (transferData.value.material?.qty || 0) &&
    transferData.value.destinationBin !== '' &&
    transferData.value.reason !== ''
  )
})

const filteredItems = computed(() => {
  let filtered = props.materialItems

  // Filter Utama: Alert Put Away
  if (activeAlertFilter.value === 'putAwayRequired') {
    // Filter: Item yang memiliki flag requiresPutAway
    filtered = filtered.filter(item => item.requiresPutAway)
  } 
  // Filter Baru: Alert Karantina/QC
  else if (activeAlertFilter.value === 'requiresQC') {
    // Filter: Item yang memiliki flag requiresQC
    filtered = filtered.filter(item => item.requiresQC)
  }
  // Filter Baru: Alert Expired Soon (dalam 30 hari, tidak termasuk yang sudah expired)
  else if (activeAlertFilter.value === 'expiringSoon') {
    const now = new Date();
    const thirtyDaysFromNow = new Date();
    thirtyDaysFromNow.setDate(now.getDate() + 30);
    
    filtered = filtered.filter(item => {
      const expired = new Date(item.expiredDate);
      // Item harus BELUM expired (expired > now) DAN expired DALAM 30 hari (expired <= thirtyDaysFromNow)
      return expired > now && expired <= thirtyDaysFromNow;
    });
  }

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(item =>
      item.kode.toLowerCase().includes(query) ||
      item.nama.toLowerCase().includes(query) ||
      item.lot.toLowerCase().includes(query)
    )
  }

  // Status filter
  if (filterStatus.value) {
    filtered = filtered.filter(item => item.qr_type === filterStatus.value)
  }

  // Type filter
  if (filterType.value) {
    filtered = filtered.filter(item => item.type === filterType.value)
  }

  // Location filter
  if (filterLocation.value) {
    filtered = filtered.filter(item => item.lokasi === filterLocation.value)
  }

  // Sort
  filtered.sort((a, b) => {
    let aValue = a[sortColumn.value as keyof MaterialItem]
    let bValue = b[sortColumn.value as keyof MaterialItem]

    if (typeof aValue === 'string') aValue = aValue.toLowerCase()
    if (typeof bValue === 'string') bValue = bValue.toLowerCase()

    if (aValue < bValue) return sortDirection.value === 'asc' ? -1 : 1
    if (aValue > bValue) return sortDirection.value === 'asc' ? 1 : -1
    return 0
  })

  return filtered
})

// Expired Materials Computed (Includes Near Expiry 30 Days)
const expiredMaterials = computed(() => {
  const now = new Date()
  const thirtyDaysFromNow = new Date()
  thirtyDaysFromNow.setDate(now.getDate() + 30)
  
  return props.materialItems.filter(item => {
    const expiredDate = new Date(item.expiredDate)
    // Allow Re-QC for items that are Expired OR Expiring within 30 days
    return expiredDate <= thirtyDaysFromNow
  })
})

const expiredMaterialsCount = computed(() => {
  return expiredMaterials.value.length
})

// Re-QC State
const selectedExpiredMaterials = ref<string[]>([])
const showErrorModal = ref(false)
const errorModalMessage = ref('')
const showExpiredModal = ref(false)

// Re-QC Methods
const toggleSelectAllExpired = () => {
  if (selectedExpiredMaterials.value.length === expiredMaterials.value.length) {
    selectedExpiredMaterials.value = []
  } else {
    selectedExpiredMaterials.value = expiredMaterials.value
      .map(m => getInventoryStockId(m))
      .filter(id => id !== null) as string[]
  }
}

const isInQrtBin = (binLocation: string) => {
  const qrtBins = ['QRT-HALAL', 'QRT-NON HALAL', 'QRT-HALAL-AC']
  return qrtBins.includes(binLocation)
}

const getInventoryStockId = (material: MaterialItem) => {
  // Extract inventory stock ID from material.id format: "INV-123"
  const match = material.id.match(/INV-(\d+)/)
  return match ? match[1] : null
}

const closeErrorModal = () => {
  showErrorModal.value = false
  errorModalMessage.value = ''
}

const initiateReqc = () => {
  if (selectedExpiredMaterials.value.length === 0) {
    errorModalMessage.value = 'Pilih minimal 1 material untuk Re-QC'
    showErrorModal.value = true
    return
  }

  console.log('🚀 Initiating Re-QC for:', selectedExpiredMaterials.value)

  // Use Inertia router.post which handles CSRF automatically
  router.post('/dashboard/reqc/initiate', {
    inventory_stock_ids: selectedExpiredMaterials.value
  }, {
    preserveState: true,
    preserveScroll: true,
    onStart: () => {
      console.log('📤 Re-QC request started...')
    },
    onSuccess: (page) => {
      console.log('✅ Re-QC initiated successfully', page)
    },
    onError: (errors) => {
      console.error('❌ Re-QC error:', errors)
      // Show error in modal
      if (errors.message) {
        errorModalMessage.value = errors.message
      } else {
        const errorMessages = Object.values(errors).flat().join('\n')
        errorModalMessage.value = errorMessages || 'Terjadi kesalahan. Silakan coba lagi.'
      }
      showErrorModal.value = true
    },
    onFinish: () => {
      console.log('🏁 Re-QC request finished')
    }
  })
}

const toggleAlertFilter = (filterKey: string) => {
  if (activeAlertFilter.value === filterKey) {
    activeAlertFilter.value = '';
  } else {
    activeAlertFilter.value = filterKey;
    filterStatus.value = '';
    filterType.value = '';
    filterLocation.value = '';
    searchQuery.value = '';
  }
}

const showExpiredMaterialsModal = () => {
  showExpiredModal.value = true
}

const closeExpiredModal = () => {
  showExpiredModal.value = false
}

// Fungsi untuk memicu filter put away
const triggerPutAwayFilter = () => {
    if (activeAlertFilter.value === 'putAwayRequired') {
        activeAlertFilter.value = '';
    } else {
        activeAlertFilter.value = 'putAwayRequired';
    }
}

// Methods
const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value
}

const sortBy = (column: string) => {
  if (sortColumn.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortColumn.value = column
    sortDirection.value = 'asc'
  }
}

const getRowClass = (item: MaterialItem) => {
  const now = new Date()
  const expiredDate = new Date(item.expiredDate)
  const daysToExpired = Math.ceil((expiredDate.getTime() - now.getTime()) / (1000 * 60 * 60 * 24))

  if (daysToExpired < 0) return 'bg-red-50 dark:bg-red-900/20'
  if (daysToExpired <= 30) return 'bg-yellow-50 dark:bg-yellow-900/20'
  return ''
}

const getExpiredClass = (expiredDate: string) => {
  const now = new Date()
  const expired = new Date(expiredDate)
  const daysToExpired = Math.ceil((expired.getTime() - now.getTime()) / (1000 * 60 * 60 * 24))

  if (daysToExpired < 0) return 'text-red-600 dark:text-red-400 font-semibold'
  if (daysToExpired <= 30) return 'text-yellow-600 dark:text-yellow-400 font-semibold'
  return 'text-gray-900'
}

const getStatusClass = (status: string) => {
  // Handle both Title Case and Uppercase keys
  const statusClasses: Record<string, string> = {
    // Original Title Case
    'Waiting QC': 'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-yellow-900 dark:text-yellow-200',
    'Karantina': 'bg-orange-100 text-orange-800 border-orange-300 dark:bg-orange-900 dark:text-orange-200',
    'Released': 'bg-green-100 text-green-800 border-green-300 dark:bg-green-900 dark:text-green-200',
    'Rejected': 'bg-red-100 text-red-800 border-red-300 dark:bg-red-900 dark:text-red-200',
    'In Production': 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900 dark:text-blue-200',
    'Returned': 'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-900 dark:text-purple-200',
    
    // Uppercase (Raw Status from DB/Controller)
    'KARANTINA': 'bg-orange-100 text-orange-800 border-orange-300 dark:bg-orange-900 dark:text-orange-200',
    'RELEASED': 'bg-green-100 text-green-800 border-green-300 dark:bg-green-900 dark:text-green-200',
    'HOLD': 'bg-gray-100 text-gray-800 border-gray-300 dark:bg-gray-700 dark:text-gray-200',
    'REJECTED': 'bg-red-100 text-red-800 border-red-300 dark:bg-red-900 dark:text-red-200',
    'REJECT': 'bg-red-100 text-red-800 border-red-300 dark:bg-red-900 dark:text-red-200'
  }
  return statusClasses[status] || 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-900 dark:text-gray-200'
}

const getAlertClass = (type: string) => {
  const alertClasses: Record<string, string> = {
    'error': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    'warning': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    'info': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
  }
  return alertClasses[type] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
}

const getBinCapacityClass = (bin: BinLocation | undefined) => {
  if (!bin) return ''
  const percentage = (bin.currentItems / bin.maxItems) * 100
  if (percentage >= 90) return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
  if (percentage >= 70) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'
  return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
}

const getBinCapacityText = (bin: BinLocation | undefined) => {
  if (!bin) return ''
  const percentage = (bin.currentItems / bin.maxItems) * 100
  if (percentage >= 90) return 'Hampir Penuh'
  if (percentage >= 70) return 'Cukup Terisi'
  return 'Tersedia'
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const formatDateTime = (date: string) => {
  return new Date(date).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Helper untuk menghitung hari sampai expired
const getDaysUntilExpired = (expiredDate: string): number => {
  const now = new Date()
  const expired = new Date(expiredDate)
  return Math.ceil((expired.getTime() - now.getTime()) / (1000 * 60 * 60 * 24))
}

// Helper untuk mendapatkan text badge expired
const getExpiredBadgeText = (expiredDate: string): string => {
  const days = getDaysUntilExpired(expiredDate)
  
  if (days < 0) return 'KADALUARSA'
  if (days === 0) return 'EXPIRED HARI INI'
  if (days === 1) return '1 HARI LAGI'
  return `${days} HARI LAGI`
}

// Helper untuk mendapatkan class badge expired
const getExpiredBadgeClass = (expiredDate: string): string => {
  const days = getDaysUntilExpired(expiredDate)
  
  if (days < 0) return 'bg-red-600 text-white'
  if (days === 0) return 'bg-red-500 text-white'
  if (days <= 7) return 'bg-orange-500 text-white'
  if (days <= 30) return 'bg-yellow-500 text-gray-900'
  return 'bg-green-500 text-white'
}

// Helper untuk mengubah label pergerakan teknis menjadi bahasa sederhana
const getSimpleMovementLabel = (action: string): string => {
  const simpleLabels: Record<string, string> = {
    // Movement types from technical to simple
    'QC_SAMPLING': 'Pengambilan Sampel',
    'STATUS_CHANGE': 'Perubahan Status',
    'RESERVATION': 'Material Dipesan',
    'PICKING': 'Pengambilan Material',
    'PUTAWAY': 'Penyimpanan Material',
    'BIN_TRANSFER': 'Pemindahan Lokasi',
    'RETURN': 'Pengembalian', 
    'ADJUSTMENT': 'Penyesuaian Stok',
    'RECEIVING': 'Penerimaan Barang',
    'GR': 'Penerimaan Barang',
    'GOODS_RECEIPT': 'Material Masuk',
    'INCOMING': 'Barang Datang'
  }
  
  // Check if exact match exists
  if (simpleLabels[action]) {
    return simpleLabels[action]
  }
  
  // Fallback: Try partial matching for compound labels
  for (const [key, value] of Object.entries(simpleLabels)) {
    if (action.toUpperCase().includes(key)) {
      return value
    }
  }
  
  // If no match, return original but capitalize properly
  return action.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}

// Helper untuk menyederhanakan detail pergerakan
const getSimpleMovementDetail = (action: string, detail: string): string => {
  if (!detail) return ''
  
  // Ganti istilah teknis dengan bahasa yang lebih mudah dipahami
  let simpleDetail = detail
    // Movement specific terms
    .replace(/QC_SAMPLING/gi, 'pengambilan sampel untuk pemeriksaan kualitas')
    .replace(/STATUS_CHANGE/gi, 'perubahan status')
    .replace(/RESERVATION/gi, 'pemesanan material')
    .replace(/PICKING/gi, 'pengambilan material dari gudang')
    .replace(/PUTAWAY/gi, 'penyimpanan ke lokasi')
    .replace(/BIN_TRANSFER/gi, 'perpindahan antar lokasi')
    .replace(/RETURN/gi, 'pengembalian')
    .replace(/ADJUSTMENT/gi, 'penyesuaian jumlah')
    
    // Technical terms to simple Indonesian
    .replace(/from bin/gi, 'dari lokasi')
    .replace(/to bin/gi, 'ke lokasi')
    .replace(/bin/gi, 'lokasi')
    .replace(/qty/gi, 'jumlah')
    .replace(/quantity/gi, 'jumlah')
    .replace(/stock/gi, 'stok')
    .replace(/inventory/gi, 'persediaan')
    .replace(/warehouse/gi, 'gudang')
    .replace(/material/gi, 'barang')
    .replace(/item/gi, 'barang')
    .replace(/released/gi, 'dilepas')
    .replace(/rejected/gi, 'ditolak')
    .replace(/karantina/gi, 'dalam pemeriksaan')
    .replace(/sample/gi, 'sampel')
    
  return simpleDetail
}

const generateQRUrl = async (item: MaterialItem) => {
    // Format: KODE|STATUS|LOT|QTY|EXP_DATE
    // Status harus uppercase untuk konsistensi
    const status = item.status.toUpperCase();
    const qrContent = `${item.kode}|${status}|${item.lot}|${item.qty}|${item.expiredDate}`;
    
    // Simpan data QR string ke item jika belum ada
    item.qr_data = qrContent;

    try {
        const url = await QRCode.toDataURL(qrContent, {
            width: 200,
            margin: 1,
            errorCorrectionLevel: 'M'
        });
        item.qr_image_url = url;
    } catch (err) {
        console.error('Error generating QR:', err);
    }
}

const openDetailPanel = async (item: MaterialItem) => {
    selectedItem.value = item
    showDetailPanel.value = true
    // Generate QR saat panel dibuka
    await generateQRUrl(item);
}

const closeDetailPanel = () => {
    showDetailPanel.value = false
    setTimeout(() => {
        selectedItem.value = null
    }, 300)
}

const dismissAlert = (alertId: string) => {
    const index = localAlerts.value.findIndex(alert => alert.id === alertId)
    if (index > -1) {
        localAlerts.value.splice(index, 1)
    }
}

const openQRDetailModal = async (item: MaterialItem) => {
    qrItem.value = item
    showQRDetailModal.value = true
    // Generate QR saat modal dibuka
    await generateQRUrl(item);
}

const closeQRDetailModal = () => {
    showQRDetailModal.value = false
    qrItem.value = null
}

// Bin to Bin Transfer Methods
const openBinToBinModal = (item: MaterialItem) => {
  transferData.value = {
    material: { ...item },
    qty: item.qty,
    destinationBin: '',
    reason: '',
    notes: ''
  }
  showBinToBinModal.value = true
}

const triggerPrint = (item: MaterialItem) => {
  console.log(`Mempersiapkan cetak QR Code untuk: ${item.kode}-${item.lot} (Tipe: ${item.qr_type})`);
  
  // LOGIKA PENCETAKAN WINDOW POP-UP (Printer friendly)
  const content = document.getElementById('qr-code-to-print')?.innerHTML;
  
  if (content) {
    const printWindow = window.open('', '_blank');
    if (printWindow) {
      printWindow.document.write('<html><head><title>Cetak QR Code</title>');
      // Sertakan CSS untuk cetak, misalnya Tailwind minimal
      printWindow.document.write('<style>');
      printWindow.document.write('body { font-family: sans-serif; margin: 0; padding: 10mm; }');
      printWindow.document.write('#qr-content { display: flex; flex-direction: column; align-items: center; text-align: center; border: 2px solid #3b82f6; padding: 2px; width: fit-content; margin: 0 auto; }');
      printWindow.document.write('h4 { margin-bottom: 5px; font-size: 14pt; }');
      printWindow.document.write('p { margin: 0; font-size: 8pt; }');
      printWindow.document.write('.qr-area { margin-bottom: 10px; }');
      printWindow.document.write('@media print { body { padding: 0; } #qr-content { border: none; padding: 0; } }');
      printWindow.document.write('</style>');
      printWindow.document.write('</head><body>');
      
      let titleText = item.status === 'Karantina' ? 'QR KARANTINA' : 'QR RELEASED';
      
      printWindow.document.write('<div id="qr-content">');
      printWindow.document.write(`<h4>${titleText}</h4>`);
      printWindow.document.write('<div class="qr-area">');
      printWindow.document.write(content); // Konten QR Code
      printWindow.document.write('</div>');
      printWindow.document.write(`<p>Kode Material: ${item.kode}</p>`);
      printWindow.document.write(`<p>Lot/Serial: ${item.lot}</p>`);
      printWindow.document.write(`<p>Qty: ${item.qty} ${item.uom}</p>`);
      printWindow.document.write(`<p>Lokasi: ${item.lokasi}</p>`);
      printWindow.document.write(`</div>`);
      printWindow.document.write('<script>window.onload = function() { window.print(); window.close(); }<\/script>');
      printWindow.document.write('</body></html>');
      
      printWindow.document.close();
      // Tidak perlu printWindow.print() di sini karena sudah ada di onload script
    }
  } else {
    alert('Gagal mengambil konten QR Code untuk dicetak.');
  }
}

const closeBinToBinModal = () => {
  showBinToBinModal.value = false
  transferData.value = {
    material: null,
    qty: 0,
    destinationBin: '',
    reason: '',
    notes: ''
  }
}

const createBinToBinTO = () => {
  if (!isTransferValid.value || !transferData.value.material) return

  const toNumber = `TO-B2B-${new Date().getFullYear()}-${String(Math.floor(Math.random() * 1000)).padStart(3, '0')}`

  const transferOrder = {
    toNumber: toNumber,
    type: 'Bin to Bin Transfer',
    status: 'Pending Approval',
    material: transferData.value.material,
    sourceBin: transferData.value.material.lokasi,
    destinationBin: transferData.value.destinationBin,
    qty: transferData.value.qty,
    reason: transferData.value.reason,
    notes: transferData.value.notes,
    createdBy: 'Admin User',
    createdAt: new Date().toISOString(),
    approvalRequired: true
  }

  console.log('Transfer Order Created:', transferOrder)

  alert(`✅ Transfer Order ${toNumber} berhasil dibuat!\n\n` +
    `Material: ${transferData.value.material.kode} - ${transferData.value.material.nama}\n` +
    `Dari: ${transferData.value.material.lokasi}\n` +
    `Ke: ${transferData.value.destinationBin}\n` +
    `Qty: ${transferData.value.qty} ${transferData.value.material.uom}\n\n` +
    `Status: Menunggu approval dari Manager/Admin`)

  closeBinToBinModal()
}

// Action methods
const openQRScanner = () => {
  alert('QR Scanner akan dibuka - integrasi dengan camera device')
}

const openImportModal = () => {
  alert('Import Modal akan dibuka - upload Excel/CSV')
}

const exportData = () => {
  const dataStr = JSON.stringify(filteredItems.value, null, 2)
  const dataBlob = new Blob([dataStr], { type: 'application/json' })
  const url = URL.createObjectURL(dataBlob)
  const link = document.createElement('a')
  link.href = url
  link.download = `wms_central_data_${new Date().toISOString().split('T')[0]}.json`
  link.click()
  URL.revokeObjectURL(url)
}

const printQR = (item: MaterialItem) => {
  let qrTitle = '';
  if (item.qr_type === 'Karantina') {
    qrTitle = 'QR KARANTINA (JANGAN DIGUNAKAN)';
    alert(`Print QR KARANTINA untuk ${item.kode} - ${item.lot}\nData QR: ${item.qr_data}`);
    // Panggil fungsi print untuk QR Karantina
    // Contoh: window.open(`/print/qr/quarantine?data=${encodeURIComponent(item.qr_data)}`, '_blank');
  } else {
    qrTitle = 'QR RELEASED (SIAP PRODUKSI)';
    alert(`Print QR RELEASED untuk ${item.kode} - ${item.lot}\nData QR: ${item.qr_data}`);
    // Panggil fungsi print untuk QR Released
    // Contoh: window.open(`/print/qr/released?data=${encodeURIComponent(item.qr_data)}`, '_blank');
  }
  console.log(`Logika cetak QR Code: ${qrTitle}`);
}

// ===================================
// PRINT ALL QR CODES FUNCTIONS
// ===================================

// Show Print All QR Modal
const showPrintAllModal = async () => {
  try {
    const response = await axios.get('/api/inventory/stats');
    
    if (response.data.success) {
      inventoryStats.value = response.data.data;
      showPrintAllQRModal.value = true;
    } else {
      alert('Gagal mengambil statistik inventory');
    }
  } catch (error) {
    console.error('Error fetching inventory stats:', error);
    alert('Terjadi kesalahan saat mengambil statistik inventory');
  }
}

// Close Print All Modal
const closePrintAllModal = () => {
  showPrintAllQRModal.value = false;
  inventoryStats.value = null;
}

// Proceed to Print All QR
const proceedToPrintAll = async () => {
  if (!inventoryStats.value) return;

  isPrintingAllQR.value = true;
  showPrintAllQRModal.value = false;
  showPrintAllProgress.value = true;
  printAllProgress.value = 0;
  allMaterialsData.value = [];

  try {
    const totalQRCodes = inventoryStats.value.total_qr_codes;
    let offset = 0;
    const batchSize = 100;
    let allData: any[] = [];

    printAllStatusText.value = `Fetching data... 0 / ${totalQRCodes} QR codes`;

    // Fetch all data in batches
    while (true) {
      const response = await axios.get('/api/inventory/print-all', {
        params: { offset, limit: batchSize }
      });

      if (!response.data.success) {
        throw new Error(response.data.message || 'Failed to fetch materials');
      }

      const batchData = response.data.data;
      allData = [...allData, ...batchData];

      const fetchProgress = Math.min(50, Math.floor((allData.length / totalQRCodes) * 50));
      printAllProgress.value = fetchProgress;
      printAllStatusText.value = `Fetching data... ${allData.length} / ${totalQRCodes} QR codes`;

      if (!response.data.pagination.has_more) {
        break;
      }

      offset += batchSize;
    }

    allMaterialsData.value = allData;
    printAllStatusText.value = `Data fetched successfully. Generating QR codes...`;

    await generateAndPrintAllQR(allData);

  } catch (error: any) {
    console.error('Error in proceedToPrintAll:', error);
    alert(`Error: ${error.message || 'Terjadi kesalahan saat memproses data'}`);
  } finally {
    isPrintingAllQR.value = false;
    showPrintAllProgress.value = false;
    printAllProgress.value = 0;
    printAllStatusText.value = '';
  }
}

// Generate and Print All QR Codes
const generateAndPrintAllQR = async (materialsData: any[]) => {
  const LABELS_PER_PAGE = 16; // 4x4 grid
  const totalQRs = materialsData.length;
  
  let pagesHTML = '';

  const GENERATION_BATCH_SIZE = 50;
  
  for (let i = 0; i < totalQRs; i += GENERATION_BATCH_SIZE) {
    const batchEnd = Math.min(i + GENERATION_BATCH_SIZE, totalQRs);
    
    const generationProgress = 50 + Math.floor(((i+1) / totalQRs) * 50);
    printAllProgress.value = generationProgress;
    printAllStatusText.value = `Generating QR codes... ${i+1} / ${totalQRs}`;

    for (let j = i; j < batchEnd; j++) {
      const material = materialsData[j];
      
      try {
        const qrDataURL = await QRCode.toDataURL(material.qr_content, {
          width: 150,
          margin: 1,
          errorCorrectionLevel: 'M'
        });

        const labelHTML = createCompactLabelHTML(material, qrDataURL);
        
        // Start new page at the beginning of each LABELS_PER_PAGE batch
        if (j % LABELS_PER_PAGE === 0) {
          if (j > 0) {
            pagesHTML += `</div>`; // Close previous page
          }
          pagesHTML += `<div class="print-page">`;
        }
        
        pagesHTML += labelHTML;
        
      } catch (error) {
        console.error(`Error generating QR for ${material.kode_item}:`, error);
      }
    }

    await new Promise(resolve => setTimeout(resolve, 10));
  }

  // Close the last page if it wasn't closed
  if (totalQRs > 0) {
    pagesHTML += `</div>`;
  }

  printAllStatusText.value = 'Opening print window...';
  const printWindow = window.open('', '_blank');
  
  if(!printWindow) {
    alert('Gagal membuka window print. Mohon izinkan popup.');
    return;
  }

  printWindow.document.write(createPrintDocumentHTML(pagesHTML));
  printWindow.document.close();
  printWindow.focus();

  setTimeout(() => {
    printWindow.print();
  }, 500);
}

// Create Compact Label HTML
const createCompactLabelHTML = (material: any, qrDataURL: string) => {
  return `
    <div class="label-item">
      <div class="label-container">
        <div class="qr-wrapper">
          <img src="${qrDataURL}" class="qr-image" alt="QR Code"/>
        </div>
        <div class="material-name">
          ${material.nama_material}
        </div>
      </div>
    </div>
  `;
}

// Create Print Document HTML
const createPrintDocumentHTML = (pagesHTML: string) => {
  return `
    <!DOCTYPE html>
    <html>
    <head>
      <title>Print All QR Codes</title>
      <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; }
        
        .print-page {
          display: grid;
          grid-template-columns: repeat(4, 1fr);
          grid-template-rows: repeat(4, 1fr);
          gap: 8px;
          padding: 8px;
          page-break-after: always;
          width: 210mm;
          height: 297mm;
        }
        
        .print-page:last-child {
          page-break-after: avoid;
        }
        
        .label-item {
          display: flex;
          align-items: center;
          justify-content: center;
        }
        
        .label-container {
          width: 50mm;
          height: 68mm;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          padding: 8px;
        }
        
        .qr-wrapper {
          margin-bottom: 8px;
        }
        
        .qr-image {
          width: 100px;
          height: 100px;
          display: block;
        }
        
        .material-name {
          text-align: center;
          font-size: 8px;
          font-weight: 600;
          color: #000;
          line-height: 1.2;
          max-width: 100%;
          word-wrap: break-word;
        }
        
        @media print {
          .print-page {
            page-break-after: always;
            margin: 0;
          }
          .print-page:last-child {
            page-break-after: avoid;
          }
          body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
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

// Format quantity based on material type
const formatQty = (qty, type) => {
  if (qty === null || qty === undefined) return '0'
  const num = parseFloat(qty)
  if (isNaN(num)) return '0'
  
  // Packaging: bulatkan ke atas
  if (type === 'Packaging') {
    return Math.ceil(num).toString()
  }
  
  // Raw Material: tampilkan apa adanya dengan precision
  // If integer or whole number, show as is
  if (num === Math.floor(num)) return num.toString()
  
  // For decimals, keep up to 6 decimal places, remove trailing zeros  
  return num.toFixed(6).replace(/\.?0+$/, '')
}

</script>

<style scoped>
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slideUp {
  from {
    transform: translateY(20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.animate-fadeIn {
  animation: fadeIn 0.2s ease-out;
}

.animate-slideUp {
  animation: slideUp 0.3s ease-out;
}
</style>