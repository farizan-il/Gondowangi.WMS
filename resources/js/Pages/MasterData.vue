<template>
    <AppLayout pageTitle="Master Data" pageDescription="Kelola data master sistem WMS">
        <div class="min-h-screen transition-colors duration-300">
    <div class="min-h-screen bg-gray-50 p-6">
      <!-- Header -->
      <div class="mb-6">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Master Data</h1>
            <p class="text-gray-600 mt-1">Kelola data master untuk seluruh sistem WMS</p>
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
            
            <button 
              @click="triggerImport"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
              </svg>
              Import Excel
            </button>
            <input 
              ref="fileInput" 
              type="file" 
              accept=".xlsx,.xls,.csv" 
              @change="handleFileImport" 
              class="hidden"
            >
            
            <button 
              @click="exportData"
              class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Export Excel
            </button>
          </div>

          <!-- Search & Filter -->
          <div class="flex flex-wrap gap-4 mb-6">
            <div class="flex-1 min-w-64">
              <input 
                v-model="searchQuery"
                @keyup.enter="applyFilter"
                type="text" 
                :placeholder="'Cari ' + getCurrentTabLabel() + '...'"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
            </div>
            <select v-model="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500">
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
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Item</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Material</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UoM</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QC Required</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier Default</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="item in filteredSkuData" :key="item.id" class="hover:bg-gray-50">
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
                        <div class="text-sm text-gray-900">{{ item.supplierDefault }}</div>
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
                    <tr v-for="supplier in filteredSupplierData" :key="supplier.id" class="hover:bg-gray-50">
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
                    <tr v-for="bin in filteredBinData" :key="bin.id" class="hover:bg-gray-50">
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
                    <tr v-for="user in filteredUserData" :key="user.id" class="hover:bg-gray-50">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ user.jabatan }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ user.name }}</div>
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
                <label class="block text-sm font-medium text-gray-700 mb-1">ABC Class</label>
                <select 
                  v-model="formData.abcClass" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Pilih ABC Class</option>
                  <option value="A">A</option>
                  <option value="B">B</option>
                  <option value="C">C</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Default</label>
                <select 
                  v-model="formData.supplierDefault" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Pilih Supplier</option>
                  <option value="PT. Supplier A">PT. Supplier A</option>
                  <option value="PT. Supplier B">PT. Supplier B</option>
                  <option value="PT. Supplier C">PT. Supplier C</option>
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
                  <option value="Admin">Admin</option>
                  <option value="QC">QC</option>
                  <option value="Receiving">Receiving</option>
                  <option value="Warehouse">Warehouse</option>
                  <option value="Production">Production</option>
                  <option value="Supervisor">Supervisor</option>
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
            'mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-blue-500 focus:text-blue-500': true,
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

const filteredSkuData = computed(() => {
    return activeSkuData.value.data.filter(item => {
        const matchesSearch = !searchQuery.value || 
            item.code.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        const matchesStatus = !statusFilter.value || item.status === statusFilter.value
        return matchesSearch && matchesStatus
    })
})

const filteredSupplierData = computed(() => {
    return activeSupplierData.value.data.filter(item => {
        const matchesSearch = !searchQuery.value || 
            item.code.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        const matchesStatus = !statusFilter.value || item.status === statusFilter.value
        return matchesSearch && matchesStatus
    })
})

const filteredBinData = computed(() => {
    return activeBinData.value.data.filter(item => {
        const matchesSearch = !searchQuery.value || 
            item.code.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.zone.toLowerCase().includes(searchQuery.value.toLowerCase())
        const matchesStatus = !statusFilter.value || item.status === statusFilter.value
        return matchesSearch && matchesStatus
    })
})

const filteredUserData = computed(() => {
    return activeUserData.value.data.filter(item => {
        const matchesSearch = !searchQuery.value || 
            item.jabatan.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.fullName.toLowerCase().includes(searchQuery.value.toLowerCase())
        const matchesStatus = !statusFilter.value || item.status === statusFilter.value
        return matchesSearch && matchesStatus
    })
})

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

const resetForm = () => {
    const defaultValues: Record<string, any> = {
        sku: {
            code: '',
            name: '',
            uom: '',
            category: '',
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
    // Validasi form data
    if (!formData.value.code) {
      showMessage('error', 'Kode wajib diisi')
      return
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
          font-size: 14px;
        }
        .bin-code {
          font-size: 24px;
          font-weight: bold;
          margin: 20px 0;
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
        <h2>Warehouse Bin QR Code</h2>
        <div class="bin-code">${qrCodeData.value.bin_code}</div>
        <img src="${qrCodeData.value.image}" alt="QR Code" class="qr-image">
        <div class="info"><strong>Bin Name:</strong> ${qrCodeData.value.bin_name}</div>
        <div class="info"><strong>Zone:</strong> ${qrCodeData.value.zone_name}</div>
        <div class="info"><strong>Type:</strong> ${qrCodeData.value.bin_type}</div>
        <div class="info"><strong>Capacity:</strong> ${qrCodeData.value.capacity}</div>
        <div class="info"><small>Generated: ${new Date().toLocaleString()}</small></div>
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

const handleFileImport = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0]
  if (file) {
    // Simulate file processing
    showMessage('success', `File ${file.name} berhasil diimport (simulasi)`)
    
    // Reset file input
    if (fileInput.value) {
      fileInput.value.value = ''
    }
  }
}

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
});

const loadData = () => {
  resetForm()
}

onMounted(() => {
    resetForm(); 
});

</script>