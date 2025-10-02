<template>
    <AppLayout pageTitle="Master Data" pageDescription="Kelola data master sistem WMS">
        <div :class="isDarkMode ? 'dark' : ''" class="min-h-screen transition-colors duration-300">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
      <!-- Header -->
      <div class="mb-6">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Master Data</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola data master untuk seluruh sistem WMS</p>
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
          </div>
        </div>
      </div>

      <!-- Tab Navigation -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
          <nav class="-mb-px flex space-x-8 px-6">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="activeTab === tab.id
                ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
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
                type="text" 
                :placeholder="`Cari ${getCurrentTabLabel()}...`"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
            </div>
            <select v-model="statusFilter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
              <option value="">Semua Status</option>
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>

          <!-- SKU Tab -->
          <div v-show="activeTab === 'sku'">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode Item</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Material</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">UoM</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">QC Required</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expiry</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Supplier Default</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="item in filteredSkuData" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-mono text-gray-900 dark:text-white">{{ item.code }}</div>
                      </td>
                      <td class="px-6 py-4">
                        <div class="text-sm text-gray-900 dark:text-white">{{ item.name }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">{{ item.uom }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                          {{ item.category }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="item.qcRequired ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500'">
                          {{ item.qcRequired ? '✓' : '✗' }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="item.expiry ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500'">
                          {{ item.expiry ? '✓' : '✗' }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">{{ item.supplierDefault }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="getStatusClass(item.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                          {{ item.status }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <button @click="editItem(item)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Edit</button>
                        <button @click="deleteItem(item.id)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Supplier Tab -->
          <div v-show="activeTab === 'supplier'">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Supplier Code</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Supplier Name</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Address</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact Person</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Phone</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="supplier in filteredSupplierData" :key="supplier.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-mono text-gray-900 dark:text-white">{{ supplier.code }}</div>
                      </td>
                      <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ supplier.name }}</div>
                      </td>
                      <td class="px-6 py-4">
                        <div class="text-sm text-gray-900 dark:text-white">{{ supplier.address }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">{{ supplier.contactPerson }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">{{ supplier.phone }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="getStatusClass(supplier.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                          {{ supplier.status }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <button @click="editItem(supplier)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Edit</button>
                        <button @click="deleteItem(supplier.id)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Bin Location Tab -->
          <div v-show="activeTab === 'bin'">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bin Code</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Zone</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Capacity</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="bin in filteredBinData" :key="bin.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-mono text-gray-900 dark:text-white">{{ bin.code }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                          {{ bin.zone }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">{{ bin.capacity }}</div>
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
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <button @click="editItem(bin)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Edit</button>
                        <button @click="deleteItem(bin.id)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- User & Role Tab -->
          <div v-show="activeTab === 'user'">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                  <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">jabatan</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Full Name</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Department</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <tr v-for="user in filteredUserData" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ user.jabatan }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">{{ user.fullName }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="getRoleClass(user.role)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                          {{ user.role }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">{{ user.department }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span :class="getStatusClass(user.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                          {{ user.status }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <button @click="editItem(user)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Edit</button>
                        <button @click="deleteItem(user.id)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Add/Edit -->
      <div v-if="showAddModal || showEditModal" class="fixed inset-0 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-2xl max-h-screen overflow-y-auto border border-gray-200 dark:border-gray-700">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
              {{ showEditModal ? 'Edit' : 'Tambah' }} {{ getCurrentTabLabel() }}
            </h3>
            <button 
              @click="closeModal"
              class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400"
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode Item *</label>
                <input 
                  v-model="formData.code"
                  type="text" 
                  placeholder="ITM001"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">UoM *</label>
                <select 
                  v-model="formData.uom" 
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
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
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Material *</label>
              <input 
                v-model="formData.name"
                type="text" 
                placeholder="Nama material lengkap"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori *</label>
                <select 
                  v-model="formData.category" 
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Pilih Kategori</option>
                  <option value="Raw Material">Raw Material</option>
                  <option value="Packaging">Packaging</option>
                  <option value="Finished Goods">Finished Goods</option>
                  <option value="Spare Parts">Spare Parts</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ABC Class</label>
                <select 
                  v-model="formData.abcClass" 
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Supplier Default</label>
                <select 
                  v-model="formData.supplierDefault" 
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Pilih Supplier</option>
                  <option value="PT. Supplier A">PT. Supplier A</option>
                  <option value="PT. Supplier B">PT. Supplier B</option>
                  <option value="PT. Supplier C">PT. Supplier C</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select 
                  v-model="formData.status" 
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
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
                  class="text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-400 rounded"
                >
                <span class="ml-2 text-gray-900 dark:text-white">QC Required</span>
              </label>
              <label class="flex items-center">
                <input 
                  v-model="formData.expiry" 
                  type="checkbox"
                  class="text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-400 rounded"
                >
                <span class="ml-2 text-gray-900 dark:text-white">Expiry Date Required</span>
              </label>
            </div>
          </div>

          <!-- Supplier Form -->
          <div v-if="activeTab === 'supplier'" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Supplier Code *</label>
                <input 
                  v-model="formData.code"
                  type="text" 
                  placeholder="SUP001"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select 
                  v-model="formData.status" 
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                >
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Supplier Name *</label>
              <input 
                v-model="formData.name"
                type="text" 
                placeholder="PT. Nama Supplier"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
              <textarea 
                v-model="formData.address"
                placeholder="Alamat lengkap supplier"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              ></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact Person</label>
                <input 
                  v-model="formData.contactPerson"
                  type="text" 
                  placeholder="Nama PIC"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                <input 
                  v-model="formData.phone"
                  type="text" 
                  placeholder="0812-3456-7890"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>
            </div>
          </div>

          <!-- Bin Location Form -->
          <div v-if="activeTab === 'bin'" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bin Code *</label>
                <input 
                  v-model="formData.code"
                  type="text" 
                  placeholder="A1-001"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Zone *</label>
                <select 
                  v-model="formData.zone" 
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Pilih Zone</option>
                  <option value="A">Zone A</option>
                  <option value="B">Zone B</option>
                  <option value="C">Zone C</option>
                  <option value="Cold">Cold Storage</option>
                  <option value="Reject">Reject</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Capacity</label>
                <input 
                  v-model="formData.capacity"
                  type="number" 
                  placeholder="1000"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type *</label>
                <select 
                  v-model="formData.type" 
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
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
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
              <select 
                v-model="formData.status" 
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Jabatan *
                </label>
                <select
                  v-model="formData.jabatan"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="" disabled>Pilih jabatan</option>
                  <option value="Manager">Manager</option>
                  <option value="Supervisor">Supervisor</option>
                  <option value="Staff">Staff</option>
                  <option value="Intern">Intern</option>
                </select>
              </div>              
              <div v-if="!showEditModal">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password *</label>
                <input 
                  v-model="formData.password"
                  type="password" 
                  placeholder="********"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name *</label>
              <input 
                v-model="formData.fullName"
                type="text" 
                placeholder="John Doe"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role *</label>
                <select 
                  v-model="formData.role" 
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department *</label>
                <select 
                  v-model="formData.department" 
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
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
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
              <select 
                v-model="formData.status" 
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
              >
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button 
              @click="closeModal"
              class="px-4 py-2 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
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
        
        <!-- dst untuk tab lainnya -->
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed, onMounted } from 'vue'

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
const activeTab = ref('sku')
const searchQuery = ref('')
const statusFilter = ref('')

// Modal states
const showAddModal = ref(false)
const showEditModal = ref(false)
const editingItem = ref<any>(null)

// Data arrays
const skuData = ref<SkuItem[]>([])
const supplierData = ref<Supplier[]>([])
const binData = ref<BinLocation[]>([])
const userData = ref<User[]>([])

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

// Computed properties
const filteredSkuData = computed(() => {
  return skuData.value.filter(item => {
    const matchesSearch = !searchQuery.value || 
      item.code.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesStatus = !statusFilter.value || item.status === statusFilter.value
    return matchesSearch && matchesStatus
  })
})

const filteredSupplierData = computed(() => {
  return supplierData.value.filter(item => {
    const matchesSearch = !searchQuery.value || 
      item.code.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesStatus = !statusFilter.value || item.status === statusFilter.value
    return matchesSearch && matchesStatus
  })
})

const filteredBinData = computed(() => {
  return binData.value.filter(item => {
    const matchesSearch = !searchQuery.value || 
      item.code.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.zone.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesStatus = !statusFilter.value || item.status === statusFilter.value
    return matchesSearch && matchesStatus
  })
})

const filteredUserData = computed(() => {
  return userData.value.filter(item => {
    const matchesSearch = !searchQuery.value || 
      item.jabatan.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.fullName.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesStatus = !statusFilter.value || item.status === statusFilter.value
    return matchesSearch && matchesStatus
  })
})

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
    'Normal': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    'Quarantine': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    'Reject': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    'Staging': 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
    'Production': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
  }
  return colors[type] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
}

const getRoleClass = (role: string) => {
  const colors: Record<string, string> = {
    'Admin': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
    'QC': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
    'Receiving': 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
    'Warehouse': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
    'Production': 'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400',
    'Supervisor': 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400'
  }
  return colors[role] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
}

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
      zone: '',
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
  formData.value = { ...item }
  showEditModal.value = true
}

const saveData = () => {
  const newItem = {
    id: editingItem.value?.id || Date.now().toString(),
    ...formData.value
  }

  const dataArrays: Record<string, any> = {
    sku: skuData,
    supplier: supplierData,
    bin: binData,
    user: userData
  }

  if (editingItem.value) {
    // Update existing item
    const index = dataArrays[activeTab.value].value.findIndex((item: any) => item.id === editingItem.value.id)
    if (index !== -1) {
      dataArrays[activeTab.value].value[index] = newItem
      showMessage('success', `${getCurrentTabLabel()} berhasil diupdate`)
    }
  } else {
    // Add new item
    dataArrays[activeTab.value].value.unshift(newItem)
    showMessage('success', `${getCurrentTabLabel()} berhasil ditambahkan`)
  }

  closeModal()
}

const deleteItem = (id: string) => {
  if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
    const dataArrays: Record<string, any> = {
      sku: skuData,
      supplier: supplierData,
      bin: binData,
      user: userData
    }

    const index = dataArrays[activeTab.value].value.findIndex((item: any) => item.id === id)
    if (index !== -1) {
      dataArrays[activeTab.value].value.splice(index, 1)
      showMessage('success', `${getCurrentTabLabel()} berhasil dihapus`)
    }
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

// Initialize data
onMounted(() => {
  // Load dark mode preference
  const savedDarkMode = localStorage.getItem('darkMode')
  if (savedDarkMode) {
    isDarkMode.value = JSON.parse(savedDarkMode)
  }

  // Initialize dummy data
  skuData.value = [
    {
      id: '1',
      code: 'ITM001',
      name: 'Raw Material A - Chemical Component',
      uom: 'KG',
      category: 'Raw Material',
      qcRequired: true,
      expiry: true,
      supplierDefault: 'PT. Supplier A',
      abcClass: 'A',
      status: 'Active'
    },
    {
      id: '2',
      code: 'ITM002',
      name: 'Packaging Material - Cardboard Box',
      uom: 'PCS',
      category: 'Packaging',
      qcRequired: false,
      expiry: false,
      supplierDefault: 'PT. Supplier B',
      abcClass: 'B',
      status: 'Active'
    },
    {
      id: '3',
      code: 'ITM003',
      name: 'Finished Product - Assembly Unit',
      uom: 'PCS',
      category: 'Finished Goods',
      qcRequired: true,
      expiry: false,
      supplierDefault: '',
      abcClass: 'A',
      status: 'Active'
    },
    {
      id: '4',
      code: 'ITM004',
      name: 'Spare Parts - Motor Component',
      uom: 'PCS',
      category: 'Spare Parts',
      qcRequired: false,
      expiry: false,
      supplierDefault: 'PT. Supplier C',
      abcClass: 'C',
      status: 'Inactive'
    }
  ]

  supplierData.value = [
    {
      id: '1',
      code: 'SUP001',
      name: 'PT. Supplier A',
      address: 'Jl. Industri No. 123, Jakarta Timur',
      contactPerson: 'Budi Santoso',
      phone: '021-1234-5678',
      status: 'Active'
    },
    {
      id: '2',
      code: 'SUP002',
      name: 'PT. Supplier B',
      address: 'Jl. Raya Bekasi No. 456, Bekasi',
      contactPerson: 'Siti Rahayu',
      phone: '021-8765-4321',
      status: 'Active'
    },
    {
      id: '3',
      code: 'SUP003',
      name: 'PT. Supplier C',
      address: 'Jl. Gatot Subroto No. 789, Tangerang',
      contactPerson: 'Ahmad Rahman',
      phone: '021-5555-6666',
      status: 'Active'
    },
    {
      id: '4',
      code: 'SUP004',
      name: 'PT. Supplier D',
      address: 'Jl. Sudirman No. 321, Jakarta Pusat',
      contactPerson: 'Diana Sari',
      phone: '021-7777-8888',
      status: 'Inactive'
    }
  ]

  binData.value = [
    {
      id: '1',
      code: 'A1-001',
      zone: 'A',
      capacity: 1000,
      type: 'Normal',
      status: 'Active'
    },
    {
      id: '2',
      code: 'A1-002',
      zone: 'A',
      capacity: 1000,
      type: 'Normal',
      status: 'Active'
    },
    {
      id: '3',
      code: 'QC-001',
      zone: 'QC',
      capacity: 500,
      type: 'Quarantine',
      status: 'Active'
    },
    {
      id: '4',
      code: 'REJ-001',
      zone: 'Reject',
      capacity: 200,
      type: 'Reject',
      status: 'Active'
    },
    {
      id: '5',
      code: 'COLD-001',
      zone: 'Cold',
      capacity: 800,
      type: 'Normal',
      status: 'Active'
    },
    {
      id: '6',
      code: 'PROD-001',
      zone: 'Production',
      capacity: 600,
      type: 'Production',
      status: 'Active'
    },
    {
      id: '7',
      code: 'B1-001',
      zone: 'B',
      capacity: 1200,
      type: 'Normal',
      status: 'Inactive'
    }
  ]

  userData.value = [
    {
      id: '1',
      jabatan: 'admin',
      fullName: 'Administrator System',
      role: 'Admin',
      department: 'IT',
      status: 'Active'
    },
    {
      id: '2',
      jabatan: 'johndoe',
      fullName: 'John Doe',
      role: 'Warehouse',
      department: 'Gudang',
      status: 'Active'
    },
    {
      id: '3',
      jabatan: 'marys',
      fullName: 'Mary Smith',
      role: 'QC',
      department: 'QC',
      status: 'Active'
    },
    {
      id: '4',
      jabatan: 'bobw',
      fullName: 'Bob Wilson',
      role: 'Receiving',
      department: 'Gudang',
      status: 'Active'
    },
    {
      id: '5',
      jabatan: 'aliceb',
      fullName: 'Alice Brown',
      role: 'Production',
      department: 'Produksi',
      status: 'Active'
    },
    {
      id: '6',
      jabatan: 'tomj',
      fullName: 'Tom Johnson',
      role: 'Supervisor',
      department: 'PPIC',
      status: 'Active'
    },
    {
      id: '7',
      jabatan: 'sarahd',
      fullName: 'Sarah Davis',
      role: 'Supervisor',
      department: 'HRD',
      status: 'Inactive'
    }
  ]

  // Reset form for current tab
  resetForm()
  })
</script>