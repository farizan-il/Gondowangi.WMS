<template>
    <AppLayout pageTitle="Master Data" pageDescription="Kelola data master sistem WMS">
        <div class="min-h-screen transition-colors duration-300">
    <div class="min-h-screen bg-gray-50 p-6 relative">
      <!-- Loading Overlay -->
      <div v-if="isLoading" class="fixed inset-0 bg-white/90 backdrop-blur-sm z-50 flex flex-col items-center justify-center">
        <div class="bg-white p-8 rounded-xl shadow-2xl flex flex-col items-center border border-gray-100">
          <div class="warehouse-loader mb-4">
            <div class="forklift">
              <div class="forks"></div>
            </div>
            <div class="box"></div>
          </div>
          <h3 class="text-xl font-bold text-gray-800">{{ loadingTitle || 'Processing...' }}</h3>
          <p class="text-gray-500 mt-2">{{ loadingSubtitle || 'Please wait while we process your request.' }}</p>
        </div>
      </div>

      <!-- Error Modal -->
      <div v-if="showErrorModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[80vh] flex flex-col">
          <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-red-50 rounded-t-xl">
            <div class="flex items-center gap-3">
              <div class="bg-red-100 p-2 rounded-full">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-gray-900">Import Errors</h3>
                <p class="text-sm text-red-600">Terjadi kesalahan pada beberapa baris data</p>
              </div>
            </div>
            <button @click="closeErrorModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          
          <div class="p-6 overflow-y-auto">
            <div class="space-y-4">
              <div v-for="(error, index) in importErrors" :key="index" class="bg-red-50 border border-red-100 rounded-lg p-4">
                <div class="flex items-start gap-3">
                  <span class="bg-red-200 text-red-800 text-xs font-bold px-2 py-1 rounded">Row {{ error.row }}</span>
                  <div class="flex-1">
                    <p class="text-sm font-medium text-red-900">{{ error.message }}</p>
                    <details class="mt-2">
                      <summary class="text-xs text-red-600 cursor-pointer hover:underline">View Row Data</summary>
                      <pre class="mt-2 text-xs bg-white p-2 rounded border border-red-100 overflow-x-auto">{{ error.data }}</pre>
                    </details>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="p-6 border-t border-gray-100 bg-gray-50 rounded-b-xl flex justify-end">
            <button 
              @click="closeErrorModal"
              class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-lg transition-colors"
            >
              Close & Fix Data
            </button>
          </div>
        </div>
      </div>
      <!-- Header -->
      <div class="mb-6">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Master Data</h1>
            <p class="text-gray-600 mt-1">Kelola data master untuk seluruh sistem WMS</p>
          </div>
          <div>
            <button 
              @click="triggerImport"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-sm"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
              </svg>
              Import Initial Stock
            </button>
            <input 
              ref="fileInput" 
              type="file" 
              accept=".xlsx,.xls,.csv" 
              @change="handleFileImport" 
              class="hidden"
            >
          </div>
        </div>
      </div>

      <!-- Tab Navigation -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200">
          <nav class="-mb-px flex space-x-8 px-6">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="setActiveTab(tab.id)" 
              :class="activeTab === tab.id
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
              class="py-4 px-1 border-b-2 font-medium text-sm transition-colors"
            >
              {{ tab.label }}
            </button>
        </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
          <!-- Action Buttons -->
          <div class="flex flex-wrap gap-3 mb-6">
            <button 
              @click="showAddModal = true"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Tambah {{ getCurrentTabLabel() }}
            </button>
            
            <!-- Bulk Edit Button - Only for SKU tab -->
            <button 
              v-if="activeTab === 'sku' && selectedSkuIds.length > 0"
              @click="showBulkEditModal = true"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Bulk Edit ({{ selectedSkuIds.length }} selected)
            </button>
            
            <button 
              @click="exportData"
              class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Export Excel
            </button>
            
            <button 
              v-if="activeTab === 'bin'"
              @click="printAllQRCodes"
              class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
              </svg>
              Print All QR Codes
            </button>
          </div>

          <!-- Search & Filter -->
          <div class="flex flex-wrap gap-4 mb-6">
            <div class="flex-1 min-w-64 flex gap-2">
              <input 
                v-model="searchQuery"
                @keyup.enter="performSearch"
                type="text" 
                :placeholder="'Cari ' + getCurrentTabLabel() + '...'"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
              <button 
                @click="performSearch"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </button>
            </div>
            <select v-model="statusFilter" @change="performSearch" class="px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500">
              <option value="">Semua Status</option>
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>

          <!-- SKU Tab -->
          <div v-show="activeTab === 'sku'">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-4 py-3 text-center">
                        <input 
                          type="checkbox" 
                          :checked="isAllSelected"
                          @change="toggleSelectAll"
                          class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Item</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Material</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UoM</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sub Kategori</th>

                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Halal</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QC Required</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="item in activeSkuData.data" :key="item.id" class="hover:bg-gray-50">
                      <td class="px-4 py-4 text-center">
                        <input 
                          type="checkbox" 
                          :checked="selectedSkuIds.includes(item.id)"
                          @change="toggleSelectSku(item.id)"
                          class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-mono text-gray-900">{{ item.code }}</div>
                      </td>
                      <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ item.name }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ item.uom }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                          {{ item.category }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ item.subCategory || '-' }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                         <span :class="item.halalStatus === 'Halal' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                           {{ item.halalStatus || '-' }}
                         </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="item.qcRequired ? 'text-green-600' : 'text-gray-400'">
                          {{ item.qcRequired ? '✓' : '✗' }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="item.expiry ? 'text-green-600' : 'text-gray-400'">
                          {{ item.expiry ? '✓' : '✗' }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="getStatusClass(item.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                          {{ item.status }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <button @click="editItem(item)" class="text-blue-600 hover:text-blue-900">Edit</button>
                        <button @click="deleteItem(item.id)" class="text-red-600 hover:text-red-900">Hapus</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="p-4 border-t">
                  <Pagination :links="activeSkuData.links" />
              </div>
            </div>
          </div>

          <!-- Supplier Tab -->
          <div v-show="activeTab === 'supplier'">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier Code</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier Name</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Person</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="supplier in activeSupplierData.data" :key="supplier.id" class="hover:bg-gray-50">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-mono text-gray-900">{{ supplier.code }}</div>
                      </td>
                      <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ supplier.name }}</div>
                      </td>
                      <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ supplier.address }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ supplier.contactPerson }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ supplier.phone }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="getStatusClass(supplier.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                          {{ supplier.status }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <button @click="editItem(supplier)" class="text-blue-600 hover:text-blue-900">Edit</button>
                        <button @click="deleteItem(supplier.id)" class="text-red-600 hover:text-red-900">Hapus</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="p-4 border-t">
                <Pagination :links="activeSupplierData.links" />
              </div>
            </div>
          </div>

          <!-- Bin Location Tab -->
          <div v-show="activeTab === 'bin'">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bin Code</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zone</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material Count</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QR Code</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="bin in activeBinData.data" :key="bin.id" class="hover:bg-gray-50">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-mono text-gray-900">{{ bin.code }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                          {{ bin.zone }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="getBinTypeClass(bin.type)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                          {{ bin.type }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="getStatusClass(bin.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                          {{ bin.status }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div v-if="bin.current_items_count > 0">
                            <button @click="loadBinDetails(bin.id, bin.code)" 
                                    class="text-sm font-semibold text-blue-600 hover:text-blue-800 underline">
                                {{ bin.current_items_count }} Item
                            </button>
                        </div>
                        <span v-else class="text-sm text-gray-400">Kosong</span>
                    </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <button 
                          v-if="bin.qrCode || bin.id"
                          @click="previewQRCode(bin.id)" 
                          class="text-blue-600 hover:text-blue-900 flex items-center gap-1"
                          title="Preview QR Code"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                          </svg>
                          Preview QR
                        </button>
                        <span v-else class="text-gray-400 text-xs">No QR</span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <button @click="editItem(bin)" class="text-blue-600 hover:text-blue-900">Edit</button>
                        <button @click="deleteItem(bin.id)" class="text-red-600 hover:text-red-900">Hapus</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="p-4 border-t">
                <Pagination :links="activeBinData.links" />
              </div>
            </div>
          </div>

          <!-- User & Role Tab -->
          <div v-show="activeTab === 'user'">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">jabatan</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="user in activeUserData.data" :key="user.id" class="hover:bg-gray-50">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ user.jabatan }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ user.fullName }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="getRoleClass(user.role)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                          {{ user.role }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ user.department }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="getStatusClass(user.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                          {{ user.status }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <button @click="editItem(user)" class="text-blue-600 hover:text-blue-900">Edit</button>
                        <button @click="deleteItem(user.id)" class="text-red-600 hover:text-red-900">Hapus</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="p-4 border-t">
                <Pagination :links="activeUserData.links" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- QR Code Preview Modal -->
      <div v-if="showQRModal && qrCodeData" class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg p-6 w-full max-w-lg border border-gray-200 shadow-2xl">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-900">QR Code - {{ qrCodeData.bin_code }}</h3>
            <button 
              @click="closeQRModal"
              class="text-gray-400 hover:text-gray-600"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- QR Code Image -->
          <div class="flex justify-center mb-6 bg-gray-50 p-6 rounded-lg">
            <img :src="qrCodeData.image" alt="QR Code" class="w-64 h-64 border-2 border-gray-200 rounded-lg shadow-sm">
          </div>

          <!-- Bin Information -->
          <div class="bg-blue-50 rounded-lg p-4 mb-6 space-y-2">
            <div class="flex justify-between">
              <span class="text-sm font-medium text-gray-600">Bin Code:</span>
              <span class="text-sm font-bold text-gray-900">{{ qrCodeData.bin_code }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm font-medium text-gray-600">Bin Name:</span>
              <span class="text-sm text-gray-900">{{ qrCodeData.bin_name }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm font-medium text-gray-600">Zone:</span>
              <span class="text-sm text-gray-900">{{ qrCodeData.zone_name }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm font-medium text-gray-600">Type:</span>
              <span class="text-sm text-gray-900">{{ qrCodeData.bin_type }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm font-medium text-gray-600">Capacity:</span>
              <span class="text-sm text-gray-900">{{ qrCodeData.capacity }}</span>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3">
            <button 
              @click="downloadQRCodeDirect"
              class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg flex items-center justify-center gap-2 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Download
            </button>
            <button 
              @click="printQRCode"
              class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg flex items-center justify-center gap-2 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
              </svg>
              Print
            </button>
            <button 
              @click="closeQRModal"
              class="px-4 py-3 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >
              Tutup
            </button>
          </div>
        </div>
      </div>

      <!-- Modal Add/Edit -->
      <div v-if="showAddModal || showEditModal" class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[999]" style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-screen overflow-y-auto border border-gray-200">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-900">
              {{ showEditModal ? 'Edit' : 'Tambah' }} {{ getCurrentTabLabel() }}
            </h3>
            <button 
              @click="closeModal"
              class="text-gray-400 hover:text-gray-600"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- SKU Form -->
          <div v-if="activeTab === 'sku'" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Item *</label>
                <input 
                  v-model="formData.code"
                  type="text" 
                  placeholder="ITM001"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">UoM *</label>
                <select 
                  v-model="formData.uom" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Pilih UoM</option>
                  <option value="PCS">PCS</option>
                  <option value="KG">KG</option>
                  <option value="LTR">LITER</option>
                  <option value="LITER">LITER</option>
                  <option value="BOX">BOX</option>
                </select>
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nama Material *</label>
              <input 
                v-model="formData.name"
                type="text" 
                placeholder="Nama material lengkap"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                <select 
                  v-model="formData.category" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Pilih Kategori</option>
                  <option value="Raw Material">Raw Material</option>
                  <option value="Packaging">Packaging</option>
                  <option value="Finished Goods">Finished Goods</option>
                  <option value="Spare Parts">Spare Parts</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sub Kategori</label>
                <input 
                  v-model="formData.subCategory"
                  type="text" 
                  placeholder="e.g. Botol, Karton, Chemical, etc"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Halal Status</label>
                <select 
                  v-model="formData.halalStatus" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Pilih Status</option>
                  <option value="Halal">Halal</option>
                  <option value="Non Halal">Non Halal</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select 
                  v-model="formData.status" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500"
                >
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
            </div>

            <div class="flex gap-6">
              <label class="flex items-center">
                <input 
                  v-model="formData.qcRequired" 
                  type="checkbox"
                  class="text-blue-600 focus:ring-blue-500 rounded"
                >
                <span class="ml-2 text-gray-900">QC Required</span>
              </label>
              <label class="flex items-center">
                <input 
                  v-model="formData.expiry" 
                  type="checkbox"
                  class="text-blue-600 focus:ring-blue-500 rounded"
                >
                <span class="ml-2 text-gray-900">Expiry Date Required</span>
              </label>
            </div>
          </div>

          <!-- Supplier Form -->
          <div v-if="activeTab === 'supplier'" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Code *</label>
                <input 
                  v-model="formData.code"
                  type="text" 
                  placeholder="SUP001"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select 
                  v-model="formData.status" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500"
                >
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Name *</label>
              <input 
                v-model="formData.name"
                type="text" 
                placeholder="PT. Nama Supplier"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
              <textarea 
                v-model="formData.address"
                placeholder="Alamat lengkap supplier"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              ></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label>
                <input 
                  v-model="formData.contactPerson"
                  type="text" 
                  placeholder="Nama PIC"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input 
                  v-model="formData.phone"
                  type="text" 
                  placeholder="0812-3456-7890"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>
            </div>
          </div>

          <!-- Bin Location Form -->
          <div v-if="activeTab === 'bin'" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bin Code *</label>
                <input 
                  v-model="formData.code"
                  type="text" 
                  placeholder="A1-001"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Zone *</label>
                <select 
                  v-model="formData.zone" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Pilih Zone</option>
                  <option value="1">Zone A</option>
                  <option value="2">Zone B</option>
                  <option value="3">Zone C</option>
                  <option value="4">Cold Storage</option>
                  <option value="5">Reject</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Capacity</label>
                <input 
                  v-model="formData.capacity"
                  type="number" 
                  placeholder="1000"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                <select 
                  v-model="formData.type" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Pilih Type</option>
                  <option value="Normal">Normal</option>
                  <option value="Quarantine">Quarantine</option>
                  <option value="Reject">Reject</option>
                  <option value="Staging">Staging</option>
                  <option value="Production">Production</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select 
                v-model="formData.status" 
                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500"
              >
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>

          <!-- User Form -->
          <div v-if="activeTab === 'user'" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Jabatan *
                </label>
                <select
                  v-model="formData.jabatan"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="" disabled>Pilih jabatan</option>
                  <option value="Manager">Manager</option>
                  <option value="Supervisor">Supervisor</option>
                  <option value="Staff">Staff</option>
                  <option value="Intern">Intern</option>
                </select>
              </div>              
              <div v-if="!showEditModal">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                <input 
                  v-model="formData.password"
                  type="password" 
                  placeholder="********"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
              <input 
                v-model="formData.fullName"
                type="text" 
                placeholder="John Doe"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                <select 
                  v-model="formData.role" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Pilih Role</option>
                  <option v-for="role in roleList" :key="role.id" :value="role.name">
                    {{ role.name }}
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department *</label>
                <select 
                  v-model="formData.department" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Pilih Department</option>
                  <option value="Gudang">Gudang</option>
                  <option value="QC">Quality Control</option>
                  <option value="Produksi">Produksi</option>
                  <option value="PPIC">PPIC</option>
                  <option value="IT">IT</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select 
                v-model="formData.status" 
                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500"
              >
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
            <button 
              @click="closeModal"
              class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >
              Batal
            </button>
            <button 
              @click="saveData"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
            >
              {{ showEditModal ? 'Update' : 'Simpan' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Success/Error Messages -->
      <div v-if="message" :class="message.type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'" class="fixed top-4 right-4 border rounded-lg p-4 shadow-lg" style="z-index: 99999;">
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

      <!-- Bulk Edit Modal -->
      <div v-if="showBulkEditModal" class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[999]" style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-screen overflow-y-auto border border-gray-200">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-900">
              Bulk Edit SKU ({{ selectedSkuIds.length }} items)
            </h3>
            <button 
              @click="closeBulkEditModal"
              class="text-gray-400 hover:text-gray-600"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-800">
              <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
              </svg>
              Hanya field yang diubah akan diterapkan ke semua item yang dipilih. Field yang dikosongkan tidak akan diubah.
            </p>
          </div>

          <!-- Bulk Edit Form -->
          <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select 
                  v-model="bulkEditData.category" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">-- Tidak diubah --</option>
                  <option value="Raw Material">Raw Material</option>
                  <option value="Packaging">Packaging</option>
                  <option value="Finished Goods">Finished Goods</option>
                  <option value="Spare Parts">Spare Parts</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Halal Status</label>
                <select 
                  v-model="bulkEditData.halalStatus" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">-- Tidak diubah --</option>
                  <option value="Halal">Halal</option>
                  <option value="Non Halal">Non Halal</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select 
                  v-model="bulkEditData.status" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">-- Tidak diubah --</option>
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sub Kategori</label>
                <input 
                  v-model="bulkEditData.subCategory"
                  type="text" 
                  placeholder="Tidak diubah jika kosong"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>
            </div>

            <div class="flex gap-6">
              <label class="flex items-center">
                <input 
                  v-model="bulkEditData.qcRequired" 
                  type="checkbox"
                  :indeterminate.prop="bulkEditData.qcRequired === null"
                  @click="toggleTriState('qcRequired')"
                  class="text-blue-600 focus:ring-blue-500 rounded"
                >
                <span class="ml-2 text-gray-900">QC Required {{ getTriStateLabel('qcRequired') }}</span>
              </label>
              <label class="flex items-center">
                <input 
                  v-model="bulkEditData.expiry" 
                  type="checkbox"
                  :indeterminate.prop="bulkEditData.expiry === null"
                  @click="toggleTriState('expiry')"
                  class="text-blue-600 focus:ring-blue-500 rounded"
                >
                <span class="ml-2 text-gray-900">Expiry Date Required {{ getTriStateLabel('expiry') }}</span>
              </label>
            </div>
          </div>

          <!-- Selected Items List -->
          <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <p class="text-sm font-medium text-gray-700 mb-2">Item yang akan diubah:</p>
            <div class="max-h-40 overflow-y-auto">
              <ul class="text-sm text-gray-600 space-y-1">
                <li v-for="id in selectedSkuIds" :key="id" class="flex items-center gap-2">
                  <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  {{ getSkuInfo(id) }}
                </li>
              </ul>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
            <button 
              @click="closeBulkEditModal"
              class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >
              Batal
            </button>
            <button 
              @click="saveBulkEdit"
              class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors"
            >
              Update {{ selectedSkuIds.length }} Items
            </button>
          </div>
        </div>
      </div>

      <div v-if="showBinDetailsModal" class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: rgba(43, 51, 63, 0.67);">

        <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto border border-gray-200 shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900">
                    Material di Bin: {{ selectedBinDetails?.bin_code || 'Memuat...' }}
                </h3>
                <button @click="closeBinDetailsModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div v-if="!selectedBinDetails" class="text-center py-8 text-gray-500">Memuat data material...</div>
            
            <div v-else-if="selectedBinDetails.details.length === 0" class="text-center py-8 text-gray-500">
                Bin ini tidak memiliki stok material aktif.
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kode Item</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Material</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Batch/Lot</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty Tersedia</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">UoM</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Exp Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="material in selectedBinDetails.details" :key="material.id">
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ material.material_code }}</td>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ material.material_name }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ material.batch_lot }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ material.qty_available }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ material.uom }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ material.exp_date }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ material.status }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end mt-6 pt-4 border-t border-gray-200">
                <button 
                    @click="closeBinDetailsModal"
                    class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
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
import {router, Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { h, ref, computed, onMounted, watch } from 'vue'
declare const route: (name: string, params?: Record<string, any>) => string;

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedData<T> {
    data: T[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
    path: string;
}

interface ItemBase {
    id: string;
    code: string;
    status: string;
    [key: string]: any;
}
interface SkuItem extends ItemBase {}
interface Supplier extends ItemBase {}
interface BinLocation extends ItemBase {}
interface User extends ItemBase {}

const props = defineProps<{
    skuData: PaginatedData<any>;
    supplierData: PaginatedData<any>;
    binData: PaginatedData<any>;
    userData: PaginatedData<any>;
    supplierList?: any[];
    zoneList?: any[];
    roleList?: any[];
    activeTab: string;
    search: string;
    status: string;
}>();

const page = usePage();

// Types
interface SkuItem {
  id: string
  code: string
  name: string
  uom: string
  category: string
  qcRequired: boolean
  expiry: boolean
  supplierDefault: string
  abcClass: string
  status: string
}

interface Supplier {
  id: string
  code: string
  name: string
  address: string
  contactPerson: string
  phone: string
  status: string
}

interface BinLocation {
  id: string
  code: string
  zone: string
  capacity: number
  type: string
  status: string
}

interface User {
  id: string
  jabatan: string
  fullName: string
  role: string
  department: string
  status: string
}

// Reactive data
const isDarkMode = ref(false)
const activeTab = ref(props.activeTab || 'sku')
const searchQuery = ref(props.search || '')
const statusFilter = ref(props.status || '')
const showQRModal = ref(false)
const qrCodeData = ref<any>(null)
const showBinDetailsModal = ref(false)
const selectedBinDetails = ref<{ 
    bin_code: string, 
    details: any[] 
} | null>(null)

// Modal states
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingItem = ref<any>(null)

// Bulk Edit states
const showBulkEditModal = ref(false)
const selectedSkuIds = ref<string[]>([])
const bulkEditData = ref<any>({
  category: '',
  subCategory: '',
  halalStatus: '',
  status: '',
  qcRequired: null, // null = tidak diubah, true/false = diubah
  expiry: null
})

// Form data
const formData = ref<any>({})
const fileInput = ref<HTMLInputElement | null>(null)
const message = ref<{ type: 'success' | 'error', text: string } | null>(null)

// Tab configuration
const tabs = [
  { id: 'sku', label: 'SKU (Material Master)' },
  { id: 'supplier', label: 'Supplier' },
  { id: 'bin', label: 'Bin Location' },
  { id: 'user', label: 'User & Role' }
]

const Pagination = (props: { links: PaginationLink[] }) => {
    // Pengecekan awal, sama seperti v-if="links.length > 3"
    if (!props.links || props.links.length <= 3) {
        return h('div'); // Mengembalikan div kosong jika tidak ada data
    }

    const elements = props.links.map((link, key) => {
        const classes = {
            'mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-blue-500 focus:border-blue-500': true,
            'bg-blue-500 text-white': link.active,
            'text-gray-400': link.url === null // Styling untuk tombol non-aktif
        };

        if (link.url === null) {
            // Jika URL null, buat <div> (non-clickable)
            return h('div', { 
                key, 
                class: classes, 
                innerHTML: link.label 
            });
        }
        
        // Jika URL ada, buat komponen Link Inertia
        return h(Link, {
            key,
            class: classes,
            href: link.url,
            // Perlu menggunakan innerHTML karena label bisa berupa HTML (seperti &laquo;)
            innerHTML: link.label, 
            preserveState: true,
            preserveScroll: true
        });
    });

    return h('div', 
        { class: 'flex flex-wrap -mb-1' }, 
        elements
    );
};

const loadBinDetails = async (binId: string, binCode: string) => {
    selectedBinDetails.value = null; // Clear previous data
    showBinDetailsModal.value = true;
    
    try {
        const url = route('bin.stocks.details', { binId: binId });
        const response = await fetch(url);
        
        if (!response.ok) {
            showMessage('error', `Gagal memuat detail Bin ${binCode}`);
            selectedBinDetails.value = { bin_code: binCode, details: [] };
            return;
        }

        const result = await response.json();
        selectedBinDetails.value = result;

    } catch (error) {
        console.error('Fetch Bin Details error:', error);
        showMessage('error', 'Error saat memuat detail material.');
        selectedBinDetails.value = { bin_code: binCode, details: [] };
    }
}

const closeBinDetailsModal = () => {
    showBinDetailsModal.value = false
    selectedBinDetails.value = null
}

// Methods
const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value
  localStorage.setItem('darkMode', JSON.stringify(isDarkMode.value))
}

const getCurrentTabLabel = () => {
  return tabs.find(tab => tab.id === activeTab.value)?.label || ''
}

const getStatusClass = (status: string) => {
  return status === 'Active' 
    ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
    : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400'
}

 const getBinTypeClass = (type: string) => {
    const colors: Record<string, string> = {
      'Normal': 'bg-blue-100 text-blue-800',
      'Quarantine': 'bg-yellow-100 text-yellow-800',
      'Reject': 'bg-red-100 text-red-800',
      'Staging': 'bg-purple-100 text-purple-800',
      'Production': 'bg-green-100 text-green-800'
    }
    return colors[type] || 'bg-gray-100 text-gray-800'
  }

  const getRoleClass = (role: string) => {
    const colors: Record<string, string> = {
      'Admin': 'bg-purple-100 text-purple-800',
      'QC': 'bg-blue-100 text-blue-800',
      'Receiving': 'bg-green-100 text-green-800',
      'Warehouse': 'bg-yellow-100 text-yellow-800',
      'Production': 'bg-orange-100 text-orange-800',
      'Supervisor': 'bg-red-100 text-red-800'
    }
    return colors[role] || 'bg-gray-100 text-gray-800'
  }

interface Props {
  initialSkuData?: any[]
  initialSupplierData?: any[]
  initialBinData?: any[]
  initialUserData?: any[]
  supplierList?: any[]
  zoneList?: any[]  // PENTING: Data zone dari backend
}

const activeSkuData = computed(() => props.skuData);
const activeSupplierData = computed(() => props.supplierData);
const activeBinData = computed(() => props.binData);
const activeUserData = computed(() => props.userData);
const zoneList = ref(props.zoneList || [])
const roleList = ref(props.roleList || [])

const resetForm = () => {
    const defaultValues: Record<string, any> = {
        sku: {
            code: '',
            name: '',
            uom: '',
            category: '',
            subCategory: '',
            qcRequired: false,
            expiry: false,
            supplierDefault: '',
            abcClass: '',
            status: 'Active'
        },
        supplier: {
            code: '',
            name: '',
            address: '',
            contactPerson: '',
            phone: '',
            status: 'Active'
        },
        bin: {
            code: '',
            zone: '', // Ini akan berisi ID zone
            capacity: 0,
            type: '',
            status: 'Active'
        },
        user: {
            jabatan: '',
            password: '',
            fullName: '',
            role: '',
            department: '',
            status: 'Active'
        }
    }
    
    formData.value = { ...defaultValues[activeTab.value] }
}

const closeModal = () => {
  showAddModal.value = false
  showEditModal.value = false
  editingItem.value = null
  resetForm()
}

const editItem = (item: any) => {
  editingItem.value = item
  
  if (activeTab.value === 'bin') {
    // Untuk bin, kita perlu mendapatkan zone_id dari nama zone
    const zone = zoneList.value.find((z: any) => z.name === item.zone)
    formData.value = {
      ...item,
      zone: zone?.id || '' // Set zone ID, bukan nama
    }
  } else {
    formData.value = { ...item }
  }
  
  showEditModal.value = true
}

const saveData = async () => {
  try {
    // Validasi form data - skip code validation untuk user tab
    if (activeTab.value !== 'user' && !formData.value.code) {
      showMessage('error', 'Kode wajib diisi')
      return
    }

    // Validasi khusus untuk user
    if (activeTab.value === 'user') {
      if (!formData.value.fullName) {
        showMessage('error', 'Nama lengkap wajib diisi')
        return
      }
      if (!showEditModal.value && !formData.value.password) {
        showMessage('error', 'Password wajib diisi')
        return
      }
      if (!formData.value.role) {
        showMessage('error', 'Role wajib dipilih')
        return
      }
      if (!formData.value.department) {
        showMessage('error', 'Department wajib dipilih')
        return
      }
    }

    // Validasi khusus untuk bin
    if (activeTab.value === 'bin') {
      if (!formData.value.zone) {
        showMessage('error', 'Zone wajib dipilih')
        return
      }
      if (!formData.value.type) {
        showMessage('error', 'Type wajib dipilih')
        return
      }
    }

    const endpoint = showEditModal.value 
      ? `/master-data/${activeTab.value}/${editingItem.value.id}`
      : `/master-data/${activeTab.value}`
    
    const method = showEditModal.value ? 'PUT' : 'POST'

    console.log('Submit data:', {
      endpoint,
      method,
      data: formData.value
    })

    const response = await fetch(endpoint, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Accept': 'application/json'
      },
      body: JSON.stringify(formData.value)
    })

    const result = await response.json()
    console.log('Response:', result)

    if (!response.ok) {
      showMessage('error', result.message || 'Terjadi kesalahan')
      return
    }

    showMessage('success', result.message || 'Data berhasil disimpan')
    closeModal()
    
    // Reload page setelah 1.5 detik
    setTimeout(() => window.location.reload(), 1500)

  } catch (error) {
    console.error('Save error:', error)
    showMessage('error', 'Error: ' + (error as Error).message)
  }
}

const previewQRCode = async (binId: string) => {
  try {
    const response = await fetch(`/master-data/bin/${binId}/qr-code/preview`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json'
      }
    })

    const result = await response.json()

    if (!response.ok) {
      showMessage('error', result.message || 'Gagal memuat QR Code')
      return
    }

    qrCodeData.value = result.data
    showQRModal.value = true

  } catch (error) {
    console.error('Preview error:', error)
    showMessage('error', 'Error saat memuat QR Code')
  }
}

const downloadQRCodeDirect = async () => {
  if (!qrCodeData.value) return

  try {
    // Ambil bin_code dari URL atau data
    const binCode = qrCodeData.value.bin_code
    
    // Cari bin ID dari binData
    const bin = binData.value.find(b => b.code === binCode)
    if (!bin) {
      showMessage('error', 'Bin tidak ditemukan')
      return
    }

    const response = await fetch(`/master-data/bin/${bin.id}/qr-code/download`)

    if (!response.ok) {
      showMessage('error', 'Gagal mengunduh QR Code')
      return
    }

    const blob = await response.blob()
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `QR_${binCode}.png`
    document.body.appendChild(a)
    a.click()
    window.URL.revokeObjectURL(url)
    document.body.removeChild(a)

    showMessage('success', 'QR Code berhasil diunduh')
  } catch (error) {
    console.error('Download error:', error)
    showMessage('error', 'Error saat mengunduh QR Code')
  }
}

const printQRCode = () => {
  if (!qrCodeData.value) return

  const printWindow = window.open('', '_blank')
  if (!printWindow) {
    showMessage('error', 'Gagal membuka window print. Mohon izinkan popup.')
    return
  }

  // Escape closing script tag untuk menghindari konflik dengan Vue parser
  const htmlContent = `
    <!DOCTYPE html>
    <html>
    <head>
      <title>Print QR Code - ${qrCodeData.value.bin_code}</title>
      <style>
        body {
          margin: 0;
          padding: 20px;
          font-family: Arial, sans-serif;
          display: flex;
          justify-content: center;
          align-items: center;
          min-height: 100vh;
        }
        .print-container {
          text-align: center;
          max-width: 400px;
        }
        .qr-image {
          width: 300px;
          height: 300px;
          margin: 20px auto;
        }
        .info {
          margin: 10px 0;
          font-size: 16px;
          font-weight: bold;
        }
        @media print {
          body {
            padding: 0;
          }
        }
      </style>
    </head>
    <body>
      <div class="print-container">
        <img src="${qrCodeData.value.image}" alt="QR Code" class="qr-image">
        <div class="info">Bin Name: ${qrCodeData.value.bin_name}</div>
        <div class="info">Zone: ${qrCodeData.value.zone_name}</div>
      </div>
      <script>
        window.onload = function() {
          window.print();
        }
      <\/script>
    </body>
    </html>
  `
  
  printWindow.document.write(htmlContent)
  printWindow.document.close()
}

const printAllQRCodes = async () => {
  try {
    isLoading.value = true
    loadingTitle.value = 'Preparing QR Codes...'
    loadingSubtitle.value = 'Generating printable QR codes for all bins. This may take a moment.'
    
    // Fetch ALL bin locations from backend (not just current page)
    const binsResponse = await fetch('/master-data/bins', {
      method: 'GET',
      headers: {
        'Accept': 'application/json'
      }
    })
    
    if (!binsResponse.ok) {
      showMessage('error', 'Gagal memuat bin locations')
      return
    }
    
    const bins = await binsResponse.json()
    
    if (!bins || bins.length === 0) {
      showMessage('error', 'Tidak ada bin location untuk di-print')
      return
    }
    
    // Fetch QR code data for all bins
    const qrPromises = bins.map(bin => 
      fetch(`/master-data/bin/${bin.id}/qr-code/preview`)
        .then(res => {
          if (!res.ok) {
            console.error(`HTTP error for bin ${bin.code}: ${res.status}`)
            return null
          }
          return res.json()
        })
        .then(data => {
          if (!data || !data.success) {
            console.error(`Invalid data for bin ${bin.code}`)
            return null
          }
          return data.data
        })
        .catch(err => {
          console.error(`Error fetching QR for bin ${bin.code}:`, err)
          return null
        })
    )
    
    const qrDataList = await Promise.all(qrPromises)
    const validQRData = qrDataList.filter(qr => qr !== null)
    
    if (validQRData.length === 0) {
      showMessage('error', 'Gagal memuat QR Codes')
      return
    }
    
    const printWindow = window.open('', '_blank')
    if (!printWindow) {
      showMessage('error', 'Gagal membuka window print. Mohon izinkan popup.')
      return
    }
    
    // Generate HTML content - Flow layout with tight packing
    // A4 (210mm width) - 5mm margins = 200mm usable.
    // 62mm item * 3 = 186mm. Gap 3mm * 2 = 6mm. Total 192mm. Fits comfortably.
    
    let pagesHTML = ''
    const itemsPerPage = 9
    
    for (let i = 0; i < validQRData.length; i += itemsPerPage) {
      const pageItems = validQRData.slice(i, i + itemsPerPage)
      
      pagesHTML += `
        <div class="page">
            ${pageItems.map(qr => `
              <div class="qr-item">
                <img src="${qr.image}" alt="QR Code" class="qr-image">
                <div class="info-container">
                    <div class="info">${qr.bin_name || qr.bin_code}</div>
                    <div class="info zone">${qr.zone_name}</div>
                </div>
              </div>
            `).join('')}
        </div>
      `
    }
         const htmlContent = `
      <!DOCTYPE html>
      <html>
      <head>
        <title>Print All QR Codes</title>
        <style>
          @page {
            size: A4;
            margin: 5mm;
          }
          * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
          }
          
          body {
            font-family: Arial, sans-serif;
            background-color: white;
          }
          
          .page {
            width: 200mm; /* Fit within print margins */
            padding: 0;
            page-break-after: always;
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            row-gap: 2mm;
            column-gap: 2mm;
          }
          
          .page:last-child {
            page-break-after: auto;
          }
          
          /* 60mm * 3 + 2mm * 2 = 180 + 4 = 184mm (Fits in 200mm easily) */
          .qr-item {
            width: 50mm; 
            height: 80mm;
            border: 1px dashed #999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            text-align: center;
            overflow: hidden;
            padding-top: 2mm;
            page-break-inside: avoid;
          }
          
          .qr-image {
            width: 55mm;
            height: 55mm;
            margin-bottom: 2mm;
          }
          
          .info-container {
             width: 100%;
             padding: 0 1mm;
             display: flex;
             flex-direction: column;
             align-items: center;
             justify-content: center;
          }

          .info {
            font-size: 16px; 
            font-weight: bold;
            line-height: 1.1;
            word-break: break-all;
            margin-bottom: 1px;
            color: #000;
          }
          
          .info.zone {
              font-size: 12px;
              color: #444;
          }
          
          @media print {
            body {
               -webkit-print-color-adjust: exact;
            }
          }
          
          @media print {
            body {
              margin: 0;
              padding: 0;
            }
            .page {
              margin: 0;
              padding: 5mm;
              height: auto; 
              min-height: 0; /* Removing min-height */
            }
          }
        </style>
      </head>
      <body>
        ${pagesHTML}
        <script>
          window.onload = function() {
            setTimeout(() => {
              window.print();
            }, 500);
          }
        <\/script>
      </body>
      </html>
    `
    
    printWindow.document.write(htmlContent)
    printWindow.document.close()
    
  } catch (error) {
    console.error('Print All QR Codes error:', error)
    showMessage('error', 'Error saat print QR Codes')
  }
}

const closeQRModal = () => {
  showQRModal.value = false
  qrCodeData.value = null
}

const deleteItem = async (id: string) => {
  if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) return

  try {
    const response = await fetch(`/master-data/${activeTab.value}/${id}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Accept': 'application/json'
      }
    })

    const result = await response.json()

    if (!response.ok) {
      showMessage('error', result.message || 'Terjadi kesalahan')
      return
    }

    showMessage('success', result.message)
    setTimeout(() => window.location.reload(), 1000)

  } catch (error) {
    console.error('Delete error:', error)
    showMessage('error', 'Error: ' + (error as Error).message)
  }
}

const triggerImport = () => {
  fileInput.value?.click()
}


const isLoading = ref(false)
const loadingTitle = ref('')
const loadingSubtitle = ref('')
const showErrorModal = ref(false)
const importErrors = ref([])

// Watch for flash messages with import errors
watch(() => page.props.flash, (newFlash) => {
    if (newFlash?.import_errors) {
        importErrors.value = newFlash.import_errors
        showErrorModal.value = true
    }
}, { deep: true, immediate: true })

const closeErrorModal = () => {
    showErrorModal.value = false
    importErrors.value = []
}

const handleFileImport = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (file) {
    isLoading.value = true // Start loading
    
    const formData = new FormData()
    formData.append('file', file)

    router.post('/master-data/import-stock', formData, {
      forceFormData: true,
      onSuccess: () => {
        showMessage('success', `File ${file.name} berhasil diimport!`)
        setTimeout(() => window.location.reload(), 1000)
      },
      onError: (errors) => {
        console.error('Import error:', errors)
        // showMessage('error', 'Gagal mengimport file. Cek format data.') // Handled by modal now
      },
      onFinish: () => {
        isLoading.value = false // Stop loading
        if (fileInput.value) {
          fileInput.value.value = ''
        }
      }
    })
  }
}

// ... existing code ...

const exportData = () => {
  const dataToExport = {
    sku: filteredSkuData.value,
    supplier: filteredSupplierData.value,
    bin: filteredBinData.value,
    user: filteredUserData.value
  }

  const csvContent = convertToCSV(dataToExport[activeTab.value as keyof typeof dataToExport])
  downloadCSV(csvContent, `${activeTab.value}_data.csv`)
  
  showMessage('success', `Data ${getCurrentTabLabel()} berhasil diexport`)
}

const convertToCSV = (data: any[]) => {
  if (!data.length) return ''
  
  const headers = Object.keys(data[0]).join(',')
  const rows = data.map(item => Object.values(item).join(','))
  
  return [headers, ...rows].join('\n')
}

const downloadCSV = (content: string, filename: string) => {
  const blob = new Blob([content], { type: 'text/csv;charset=utf-8;' })
  const link = document.createElement('a')
  
  if (link.download !== undefined) {
    const url = URL.createObjectURL(blob)
    link.setAttribute('href', url)
    link.setAttribute('download', filename)
    link.style.visibility = 'hidden'
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  }
}

const showMessage = (type: 'success' | 'error', text: string) => {
  message.value = { type, text }
  setTimeout(() => {
    message.value = null
  }, 3000)
}

const setActiveTab = (tabId: string) => {
    activeTab.value = tabId;
    applyFilter();
}

const applyFilter = () => {
    // Memicu request Inertia baru dengan parameter query yang sesuai
    router.get(route('master-data.index'), {
        search: searchQuery.value,
        status: statusFilter.value,
        activeTab: activeTab.value
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['skuData', 'supplierData', 'binData', 'userData'],
        onSuccess: () => {
             closeModal();
        }
    });
}

watch(activeTab, () => {
    resetForm();
    // Clear selections when switching tabs
    selectedSkuIds.value = [];
});

const loadData = () => {
  resetForm()
}

// Bulk Edit Computed Properties
const isAllSelected = computed(() => {
  if (!activeSkuData.value.data || activeSkuData.value.data.length === 0) return false
  return selectedSkuIds.value.length === activeSkuData.value.data.length
})

// Search Method
const performSearch = () => {
  router.get(route('master-data.index'), {
    search: searchQuery.value,
    status: statusFilter.value,
    activeTab: activeTab.value
  }, {
    preserveState: true,
    preserveScroll: true,
    only: ['skuData', 'supplierData', 'binData', 'userData']
  })
}

// Bulk Edit Methods
const toggleSelectSku = (id: string) => {
  const index = selectedSkuIds.value.indexOf(id)
  if (index > -1) {
    selectedSkuIds.value.splice(index, 1)
  } else {
    selectedSkuIds.value.push(id)
  }
}

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedSkuIds.value = []
  } else {
    selectedSkuIds.value = activeSkuData.value.data.map((item: any) => item.id)
  }
}

const closeBulkEditModal = () => {
  showBulkEditModal.value = false
  // Reset bulk edit form
  bulkEditData.value = {
    category: '',
    subCategory: '',
    halalStatus: '',
    status: '',
    qcRequired: null,
    expiry: null
  }
}

const toggleTriState = (field: 'qcRequired' | 'expiry') => {
  if (bulkEditData.value[field] === null) {
    bulkEditData.value[field] = true
  } else if (bulkEditData.value[field] === true) {
    bulkEditData.value[field] = false
  } else {
    bulkEditData.value[field] = null
  }
}

const getTriStateLabel = (field: 'qcRequired' | 'expiry') => {
  const value = bulkEditData.value[field]
  if (value === null) return '(tidak diubah)'
  if (value === true) return '(Ya)'
  return '(Tidak)'
}

const getSkuInfo = (id: string) => {
  const sku = activeSkuData.value.data.find((item: any) => item.id === id)
  return sku ? `${sku.code} - ${sku.name}` : id
}

const saveBulkEdit = async () => {
  try {
    // Prepare update data - only send fields that have values
    const updateData: any = {
      ids: selectedSkuIds.value
    }

    if (bulkEditData.value.category) {
      updateData.category = bulkEditData.value.category
    }
    if (bulkEditData.value.subCategory) {
      updateData.subCategory = bulkEditData.value.subCategory
    }
    if (bulkEditData.value.halalStatus) {
      updateData.halalStatus = bulkEditData.value.halalStatus
    }
    if (bulkEditData.value.status) {
      updateData.status = bulkEditData.value.status
    }
    if (bulkEditData.value.qcRequired !== null) {
      updateData.qcRequired = bulkEditData.value.qcRequired
    }
    if (bulkEditData.value.expiry !== null) {
      updateData.expiry = bulkEditData.value.expiry
    }

    // Validate that at least one field is being updated
    if (Object.keys(updateData).length === 1) { // only 'ids'
      showMessage('error', 'Pilih minimal satu field untuk diubah')
      return
    }

    const response = await fetch('/master-data/sku/bulk-update', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Accept': 'application/json'
      },
      body: JSON.stringify(updateData)
    })

    const result = await response.json()

    if (!response.ok) {
      showMessage('error', result.message || 'Terjadi kesalahan')
      return
    }

    showMessage('success', result.message || `${selectedSkuIds.value.length} items berhasil diupdate`)
    closeBulkEditModal()
    selectedSkuIds.value = []
    
    // Reload page after 1.5 seconds
    setTimeout(() => window.location.reload(), 1500)

  } catch (error) {
    console.error('Bulk update error:', error)
    showMessage('error', 'Error: ' + (error as Error).message)
  }
}

onMounted(() => {
    resetForm(); 
});

</script>

<style scoped>
/* Warehouse Loading Animation */
.warehouse-loader {
  width: 100px;
  height: 100px;
  position: relative;
}

.forklift {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 60px;
  height: 40px;
  background: #f59e0b; /* Amber-500 */
  border-radius: 4px;
  animation: drive 2s infinite linear;
}

.forklift::before {
  content: '';
  position: absolute;
  top: -20px;
  left: 10px;
  width: 20px;
  height: 20px;
  background: #f59e0b;
  border-radius: 2px;
}

.forklift::after {
  content: '';
  position: absolute;
  bottom: -5px;
  left: 5px;
  width: 10px;
  height: 10px;
  background: #333;
  border-radius: 50%;
  box-shadow: 35px 0 0 #333;
}

.forks {
  position: absolute;
  bottom: 5px;
  right: -15px;
  width: 20px;
  height: 5px;
  background: #333;
}

.forks::before {
  content: '';
  position: absolute;
  bottom: 0;
  right: 0;
  width: 5px;
  height: 30px;
  background: #333;
}

.box {
  position: absolute;
  bottom: 5px;
  right: -15px;
  width: 20px;
  height: 20px;
  background: #8B4513; /* SaddleBrown */
  border: 2px solid #5D4037;
  animation: lift 2s infinite ease-in-out;
}

@keyframes drive {
  0% { transform: translateX(-20px); }
  50% { transform: translateX(20px); }
  100% { transform: translateX(-20px); }
}

@keyframes lift {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-30px); }
}
</style>

