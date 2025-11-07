<template>
  <AppLayout title="Riwayat Aktivitas">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Reservation Requests</h2>
      </div>

      <!-- Main Content -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- Header dengan Search dan Filter -->
        <div class="p-6 border-b border-gray-200">
          <div class="flex flex-col lg:flex-row gap-4 mb-4">
            <!-- Search Box -->
            <div class="flex-1">
              <div class="relative">
                <input v-model="searchQuery" type="text"
                  placeholder="Cari berdasarkan No Reservasi, Kategori, atau Detail Info..."
                  class="w-full px-4 py-2.5 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-700">
                <svg class="w-5 h-5 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor"
                  viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
            </div>

            <!-- Filter Button -->
            <button @click="showFilterPanel = !showFilterPanel"
              class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg flex items-center gap-2 transition-colors border border-gray-300">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
              </svg>
              Filter
              <span v-if="activeFiltersCount > 0" class="bg-blue-500 text-white text-xs px-2 py-0.5 rounded-full">{{
                activeFiltersCount }}</span>
            </button>

            <!-- Create Button -->
            <button @click="openCreateModal"
              class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-2 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Buat Request
            </button>
          </div>

          <!-- Filter Panel -->
          <div v-if="showFilterPanel" class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
              <!-- Filter by Category -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                <select v-model="filters.category"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 bg-white">
                  <option value="">Semua Kategori</option>
                  <option value="foh-rs">FOH & RS</option>
                  <option value="packaging">Request Packaging Material</option>
                  <option value="raw-material">Request Raw Material</option>
                  <option value="add">ADD (Additional Request)</option>
                </select>
              </div>

              <!-- Filter by Status -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select v-model="filters.status"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 bg-white">
                  <option value="">Semua Status</option>
                  <option value="Draft">Draft</option>
                  <option value="Submitted">Submitted</option>
                  <option value="Approved">Approved</option>
                  <option value="Rejected">Rejected</option>
                  <option value="Picking">Picking</option>
                  <option value="Done">Done</option>
                </select>
              </div>

              <!-- Filter by Date Range -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rentang Tanggal</label>
                <div class="flex gap-2">
                  <input v-model="filters.dateFrom" type="date"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 bg-white text-sm">
                  <input v-model="filters.dateTo" type="date"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 bg-white text-sm">
                </div>
              </div>
            </div>

            <!-- Filter Actions -->
            <div class="flex justify-between items-center">
              <div class="text-sm text-gray-600">
                Total hasil: {{ filteredRequests.length }} dari {{ requests.length }} requests
              </div>
              <div class="flex gap-2">
                <button @click="resetFilters" class="px-4 py-2 text-gray-600 hover:text-gray-800 text-sm">
                  Reset Filter
                </button>
                <button @click="showFilterPanel = false"
                  class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
                  Terapkan
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabel Requests -->
        <div class="p-6">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No
                    Reservasi</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal
                    Permintaan</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No Bets Filling/Mixing</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Detail
                    Info</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="filteredRequests.length === 0">
                  <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                    Tidak ada data yang ditemukan
                  </td>
                </tr>

                <tr v-for="request in filteredRequests" :key="request.id" class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">{{ request.noReservasi }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getCategoryClass(request.type)">
                      {{ getCategoryName(request.type) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{
                    formatDateTime(request.tanggalPermintaan) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">763473</td>

                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ getDisplayText(request) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusClass(request.status)"
                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ request.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <button @click="viewDetail(request)"
                        class="bg-blue-50 text-blue-700 hover:bg-blue-100 px-3 py-1.5 rounded text-xs font-medium transition-colors">
                        Detail
                      </button>
                      <button @click="printForm(request)"
                        class="bg-green-50 text-green-700 hover:bg-green-100 px-3 py-1.5 rounded text-xs font-medium transition-colors">
                        Cetak
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Modal Create Request -->
      <div v-if="showModal"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]"
        style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg w-full max-w-7xl mx-4 max-h-[90vh] overflow-y-auto shadow-xl">
          <div class="p-6">
            <!-- Header Modal -->
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-semibold text-gray-800">
                Buat Request Reservasi Baru
              </h3>
              <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Category Selection -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
              <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Kategori Request:</label>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <button v-for="category in categories" :key="category.id" @click="selectedCategory = category.id"
                  :class="[
                    'p-4 rounded-lg border-2 text-sm font-medium transition-all',
                    selectedCategory === category.id
                      ? 'border-blue-500 bg-blue-50 text-blue-700'
                      : 'border-gray-300 bg-white text-gray-700 hover:border-blue-300 hover:bg-blue-50'
                  ]">
                  {{ category.name }}
                </button>
              </div>
            </div>

            <!-- Form Fields (show only when category is selected) -->
            <div v-if="selectedCategory" class="space-y-6">
              <!-- Form Header Fields -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- No Reservasi (always) -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">No Reservasi</label>
                  <input v-model="formData.noReservasi" type="text" readonly
                    class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700">
                </div>

                <!-- Tanggal Permintaan (always) -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ selectedCategory === 'packaging' || selectedCategory === 'add' ? 'Tanggal & Jam Permintaan' :
                      'Tanggal Permintaan' }}
                  </label>
                  <input v-model="formData.tanggalPermintaan" type="datetime-local"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700">
                </div>

                <!-- FOH: Alasan Reservasi -->
                <div v-if="selectedCategory === 'foh-rs'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Reservasi <span
                      class="text-red-500">*</span></label>
                  <textarea v-model="formData.alasanReservasi" rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700"
                    placeholder="Jelaskan alasan reservasi..."></textarea>
                </div>
              </div>

              <!-- Row 2 - Category specific fields -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- FOH & RS: Departemen -->
                <div v-if="selectedCategory === 'foh-rs'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Departemen <span
                      class="text-red-500">*</span></label>
                  <select v-model="formData.departemen"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700">
                    <option value="">Pilih Departemen</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Produksi">Produksi</option>
                    <option value="IT">IT</option>
                    <option value="HRD">HRD</option>
                  </select>
                </div>

                <!-- Packaging & ADD: Nama Produk -->
                <div v-if="selectedCategory === 'packaging' || selectedCategory === 'add'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk <span
                      class="text-red-500">*</span></label>
                  <input v-model="formData.namaProduk" type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700">
                </div>

                <!-- Raw Material: Kode Produk -->
                <div v-if="selectedCategory === 'raw-material'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Kode Produk <span
                      class="text-red-500">*</span></label>
                  <input v-model="formData.kodeProduk" type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700">
                </div>

                <!-- Packaging & ADD: No Bets Filling -->
                <div v-if="selectedCategory === 'packaging' || selectedCategory === 'add'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">No Bets Filling / Mixing <span
                      class="text-red-500">*</span></label>
                  <input v-model="formData.noBetsFilling" type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700">
                </div>

                <!-- Raw Material: No Bets -->
                <div v-if="selectedCategory === 'raw-material'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">No Bets Ekstrak / Mixing <span
                      class="text-red-500">*</span></label>
                  <input v-model="formData.noBets" type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700">
                </div>

                <!-- Raw Material: Besar Bets -->
                <div v-if="selectedCategory === 'raw-material'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">Besar Bets (Kg) <span
                      class="text-red-500">*</span></label>
                  <input v-model="formData.besarBets" type="number"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-700">
                </div>
              </div>

              <div v-if="selectedCategory === 'packaging'" class="mb-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Import dari Production Order (PDF) 
                  <span v-if="selectedCategory === 'raw-material'">(Hanya mengambil bahan dengan UoM Kg)</span>
                  <span v-else-if="selectedCategory === 'packaging'">(Hanya mengambil bahan selain UoM Kg)</span>
                </label>
                
                <div class="flex items-center gap-3">
                  <input type="file" @change="handleFileUpload" accept=".pdf, .xlsx, .xls"
                      class="block w-full text-sm text-gray-700
                          file:mr-4 file:py-2 file:px-4
                          file:rounded-md file:border-0
                          file:text-sm file:font-semibold
                          file:bg-blue-500 file:text-white
                          hover:file:bg-blue-600" />
                  
                  <button @click="uploadFileAndParse" :disabled="!uploadedFile"
                      class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors disabled:bg-gray-400">
                      Proses Import
                  </button>
                  
                  <button @click="debugFileParse" :disabled="!uploadedFile"
                      class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors disabled:bg-gray-400">
                      DEBUG DD
                  </button>
              </div>
                <p v-if="uploadStatus" class="mt-2 text-sm" :class="uploadStatus.type === 'error' ? 'text-red-600' : 'text-green-600'">
                    {{ uploadStatus.message }}
                </p>
            </div>

              <!-- Items Table -->
              <div class="border-t border-gray-200 pt-6">
                <div class="flex justify-between items-center mb-4">
                  <h4 class="text-md font-medium text-gray-800">
                    {{ getItemTableTitle }}
                  </h4>
                  <button @click="addNewItem"
                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-sm font-medium transition-colors">
                    + Tambah Baris
                  </button>
                </div>

                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th
                          class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap w-16">
                          No</th>

                        <!-- FOH & RS Headers -->
                        <template v-if="selectedCategory === 'foh-rs'">
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Kode Item</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Keterangan</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap min-w-[120px]">
                            Qty & Stok Tersedia</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            UoM</th>
                        </template>

                        <!-- Packaging Headers -->
                        <template v-if="selectedCategory === 'packaging'">
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Kode PM</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Nama Material</th>

                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap min-w-[150px]">
                            Jumlah Permintaan & Stok Tersedia</th>
                        </template>

                        <!-- Raw Material Headers -->
                        <template v-if="selectedCategory === 'raw-material'">
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Kode Bahan</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Nama Bahan</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap min-w-[150px]">
                            Jumlah Kebutuhan & Stok Tersedia</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Jumlah Kirim</th>
                        </template>

                        <!-- ADD Headers -->
                        <template v-if="selectedCategory === 'add'">
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Kode PM</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Nama Material</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap">
                            Alasan Penambahan</th>
                          <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap min-w-[150px]">
                            Jumlah Permintaan & Stok Tersedia</th>
                        </template>

                        <th
                          class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase whitespace-nowrap w-24">
                          Aksi</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                      <tr v-for="(item, index) in formData.items" :key="index">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ index + 1 }}</td>

                        <!-- FOH & RS Row -->
                        <template v-if="selectedCategory === 'foh-rs'">
                          <td class="px-4 py-3 relative whitespace-nowrap z-10">
                            <input v-model="item.kodeItem" type="text"
                              @input="debounceSearch(item.kodeItem, index, 'kodeItem')"
                              @focus="startSearch(index, 'kodeItem')" @blur="endSearch()"
                              class="w-full min-w-[120px] text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <ul v-if="isSearching(index, 'kodeItem') && materialSuggestions.length > 0"
                              class="absolute left-0 right-0 mt-1 max-h-48 overflow-y-auto bg-white border border-gray-300 rounded-md shadow-lg z-20">
                              <li v-for="material in materialSuggestions" :key="material.id"
                                @mousedown.prevent="selectMaterial(material, index, 'kodeItem')"
                                class="p-2 text-sm cursor-pointer hover:bg-blue-100 flex justify-between">
                                <span>{{ material.kodeItem }}</span>
                                <span class="text-gray-500 text-xs truncate ml-2">{{ material.keterangan ||
                                  material.namaMaterial }} (Stok: {{ material.stokAvailable }})</span>
                              </li>
                            </ul>
                          </td>
                          <td class="px-4 py-3 whitespace-nowrap">
                            <input v-model="item.keterangan" type="text"
                              class="w-full min-w-[150px] text-sm border border-gray-300 rounded px-2 py-1 bg-gray-50 text-gray-700"
                              readonly>
                          </td>
                          <td class="px-4 py-3 whitespace-nowrap">
                            <input v-model.number="item.qty" type="number" min="0"
                              :class="{ 'border-red-500 ring-red-500': isQtyExceeded(item, 'qty') }"
                              class="w-full min-w-[80px] text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p v-if="item.stokAvailable !== undefined"
                              :class="['text-xs mt-1', item.stokAvailable <= 0 ? 'text-red-600' : 'text-green-600']">
                              Tersedia: {{ item.stokAvailable || 0 }} {{ item.uom || '' }}
                            </p>
                            <p v-if="isQtyExceeded(item, 'qty')" class="text-xs text-red-500 font-medium">
                              Permintaan melebihi stok!
                            </p>
                          </td>
                          <td class="px-4 py-3 whitespace-nowrap">
                            <select v-model="item.uom"
                              class="w-full min-w-[80px] text-sm border border-gray-300 rounded px-2 py-1 bg-gray-50 text-gray-700"
                              readonly disabled>
                              <option value="">UoM</option>
                              <option value="PCS">PCS</option>
                              <option value="KG">KG</option>
                              <option value="L">L</option>
                            </select>
                          </td>
                        </template>

                        <!-- Packaging Row -->
                        <template v-if="selectedCategory === 'packaging'">
                          <td class="px-4 py-3 relative whitespace-nowrap z-10">
                            <input v-model="item.kodePM" type="text" @input="debounceSearch(item.kodePM, index, 'kodePM')"
                              @focus="startSearch(index, 'kodePM')" @blur="endSearch()"
                              class="w-full min-w-[120px] text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <ul v-if="isSearching(index, 'kodePM') && materialSuggestions.length > 0"
                              class="absolute left-0 right-0 mt-1 max-h-48 overflow-y-auto bg-white border border-gray-300 rounded-md shadow-lg z-20">
                              <li v-for="material in materialSuggestions" :key="material.id"
                                @mousedown.prevent="selectMaterial(material, index, 'kodePM')"
                                class="p-2 text-sm cursor-pointer hover:bg-blue-100 flex justify-between">
                                <span>{{ material.kodePM }}</span>
                                <span class="text-gray-500 text-xs truncate ml-2">Stok: {{ material.stokAvailable }}
                                  {{ material.satuan || '' }}</span>
                              </li>
                            </ul>
                          </td>
                          <td class="px-4 py-3 whitespace-nowrap">
                            <input v-model="item.namaMaterial" type="text"
                              class="w-full min-w-[150px] text-sm border border-gray-300 rounded px-2 py-1 bg-gray-50 text-gray-700"
                              readonly>
                          </td>
                          <td class="px-4 py-3 whitespace-nowrap">
                            <input v-model.number="item.jumlahPermintaan" type="number" min="0"
                              :class="{ 'border-red-500 ring-red-500': isQtyExceeded(item, 'jumlahPermintaan') }"
                              class="w-full min-w-[120px] text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p v-if="item.stokAvailable !== undefined"
                              :class="['text-xs mt-1', item.stokAvailable <= 0 ? 'text-red-600' : 'text-green-600']">
                              Tersedia: {{ item.stokAvailable || 0 }} {{ item.satuan || '' }}
                            </p>
                            <p v-if="isQtyExceeded(item, 'jumlahPermintaan')" class="text-xs text-red-500 font-medium">
                              Permintaan melebihi stok!
                            </p>
                          </td>
                        </template>

                        <!-- Raw Material Row -->
                        <template v-if="selectedCategory === 'raw-material'">
                          <td class="px-4 py-3 relative whitespace-nowrap z-10">
                            <input v-model="item.kodeBahan" type="text"
                              @input="debounceSearch(item.kodeBahan, index, 'kodeBahan')"
                              @focus="startSearch(index, 'kodeBahan')" @blur="endSearch()"
                              class="w-full min-w-[120px] text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">

                            <ul v-if="isSearching(index, 'kodeBahan') && materialSuggestions.length > 0"
                              class="absolute left-0 right-0 mt-1 max-h-48 overflow-y-auto bg-white border border-gray-300 rounded-md shadow-lg z-20">
                              <li v-for="material in materialSuggestions" :key="material.id"
                                @mousedown.prevent="selectMaterial(material, index, 'kodeBahan')"
                                class="p-2 text-sm cursor-pointer hover:bg-blue-100 flex justify-between">
                                <span>{{ material.kodeBahan }}</span>
                                <span class="text-gray-500 text-xs truncate ml-2">Stok: {{ material.stokAvailable }}
                                  {{ material.satuan || '' }}</span>
                              </li>
                            </ul>

                          </td>
                          <td class="px-4 py-3 whitespace-nowrap">
                            <input v-model="item.namaBahan" type="text"
                              class="w-full min-w-[150px] text-sm border border-gray-300 rounded px-2 py-1 bg-gray-50 text-gray-700"
                              readonly>
                          </td>
                          <td class="px-4 py-3 whitespace-nowrap">
                            <input v-model.number="item.jumlahKebutuhan" type="number" min="0"
                              :class="{ 'border-red-500 ring-red-500': isQtyExceeded(item, 'jumlahKebutuhan') }"
                              class="w-full min-w-[120px] text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p v-if="item.stokAvailable !== undefined"
                              :class="['text-xs mt-1', item.stokAvailable <= 0 ? 'text-red-600' : 'text-green-600']">
                              Tersedia: {{ item.stokAvailable || 0 }} {{ item.satuan || '' }}
                            </p>
                            <p v-if="isQtyExceeded(item, 'jumlahKebutuhan')" class="text-xs text-red-500 font-medium">
                              Permintaan melebihi stok!
                            </p>
                          </td>
                          <td class="px-4 py-3 whitespace-nowrap">
                            <input v-model.number="item.jumlahKirim" type="number"
                              class="w-full min-w-[120px] text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                          </td>
                        </template>

                        <!-- ADD Row -->
                        <template v-if="selectedCategory === 'add'">

                          <td class="px-4 py-3 relative whitespace-nowrap z-10">
                            <input v-model="item.kodePM" type="text" @input="debounceSearch(item.kodePM, index, 'kodePM')"
                              @focus="startSearch(index, 'kodePM')" @blur="endSearch()"
                              class="w-full min-w-[120px] text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">

                            <ul v-if="isSearching(index, 'kodePM') && materialSuggestions.length > 0"
                              class="absolute left-0 right-0 mt-1 max-h-48 overflow-y-auto bg-white border border-gray-300 rounded-md shadow-lg z-20">
                              <li v-for="material in materialSuggestions" :key="material.id"
                                @mousedown.prevent="selectMaterial(material, index, 'kodePM')"
                                class="p-2 text-sm cursor-pointer hover:bg-blue-100 flex justify-between">
                                <span>{{ material.kodePM }}</span>
                                <span class="text-gray-500 text-xs truncate ml-2">Stok: {{ material.stokAvailable }}
                                  {{ material.satuan || '' }}</span>
                              </li>
                            </ul>

                          </td>
                          <td class="px-4 py-3 whitespace-nowrap">
                            <input v-model="item.namaMaterial" type="text"
                              class="w-full min-w-[150px] text-sm border border-gray-300 rounded px-2 py-1 bg-gray-50 text-gray-700"
                              readonly>
                          </td>
                          <td class="px-4 py-3 whitespace-nowrap">
                            <input v-model="item.alasanPenambahan" type="text"
                              class="w-full min-w-[150px] text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                          </td>
                          <td class="px-4 py-3 whitespace-nowrap">
                            <input v-model.number="item.jumlahPermintaan" type="number" min="0"
                              :class="{ 'border-red-500 ring-red-500': isQtyExceeded(item, 'jumlahPermintaan') }"
                              class="w-full min-w-[120px] text-sm border border-gray-300 rounded px-2 py-1 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p v-if="item.stokAvailable !== undefined"
                              :class="['text-xs mt-1', item.stokAvailable <= 0 ? 'text-red-600' : 'text-green-600']">
                              Tersedia: {{ item.stokAvailable || 0 }} {{ item.satuan || '' }}
                            </p>
                            <p v-if="isQtyExceeded(item, 'jumlahPermintaan')" class="text-xs text-red-500 font-medium">
                              Permintaan melebihi stok!
                            </p>
                          </td>
                        </template>

                        <td class="px-4 py-3 whitespace-nowrap">
                          <button @click="removeItem(index)"
                            class="bg-red-50 text-red-700 hover:bg-red-100 px-2 py-1 rounded text-xs font-medium transition-colors">Hapus</button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Footer Modal -->
              <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <button @click="closeModal"
                  class="px-6 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                  Batal
                </button>
                <button @click="submitRequest" :disabled="!isFormValid || hasStockExceededItems"
                  class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                  Simpan & Submit
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal Detail Request (TIDAK BERUBAH) -->
      <div v-if="showDetailModal && selectedRequest"
        class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]"
        style="background-color: rgba(43, 51, 63, 0.67);">
        <div class="bg-white rounded-lg w-full max-w-7xl mx-4 max-h-[90vh] overflow-y-auto shadow-xl">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6 border-b pb-4">
              <h3 class="text-xl font-bold text-gray-800">
                Detail Reservasi: {{ selectedRequest.noReservasi }}
              </h3>
              <button @click="closeDetailModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 bg-gray-50 p-4 rounded-lg">
              <div class="space-y-1">
                <p class="text-sm font-medium text-gray-600">Kategori Request</p>
                <span class="px-3 py-1 text-xs font-semibold rounded-full" :class="getCategoryClass(selectedRequest.type)">
                  {{ getCategoryName(selectedRequest.type) }}
                </span>
              </div>
              <div class="space-y-1">
                <p class="text-sm font-medium text-gray-600">Tanggal Permintaan</p>
                <p class="font-medium text-gray-800">{{ formatDateTime(selectedRequest.tanggalPermintaan) }}</p>
              </div>
              <div class="space-y-1">
                <p class="text-sm font-medium text-gray-600">Status</p>
                <span :class="getStatusClass(selectedRequest.status)" class="px-3 py-1 text-xs font-semibold rounded-full">
                  {{ selectedRequest.status }}
                </span>
              </div>

              <template v-if="selectedRequest.type === 'foh-rs'">
                <div class="space-y-1">
                  <p class="text-sm font-medium text-gray-600">Departemen</p>
                  <p class="text-gray-800">{{ selectedRequest.departemen }}</p>
                </div>
                <div class="space-y-1 md:col-span-2">
                  <p class="text-sm font-medium text-gray-600">Alasan Reservasi</p>
                  <p class="text-gray-800">{{ selectedRequest.alasanReservasi || '-' }}</p>
                </div>
              </template>

              <template v-else-if="selectedRequest.type === 'packaging' || selectedRequest.type === 'add'">
                <div class="space-y-1">
                  <p class="text-sm font-medium text-gray-600">Nama Produk</p>
                  <p class="text-gray-800">{{ selectedRequest.namaProduk }}</p>
                </div>
                <div class="space-y-1">
                  <p class="text-sm font-medium text-gray-600">No Bets Filling/Mixing</p>
                  <p class="text-gray-800">{{ selectedRequest.noBetsFilling }}</p>
                </div>
              </template>

              <template v-else-if="selectedRequest.type === 'raw-material'">
                <div class="space-y-1">
                  <p class="text-sm font-medium text-gray-600">Kode Produk</p>
                  <p class="text-gray-800">{{ selectedRequest.kodeProduk }}</p>
                </div>
                <div class="space-y-1">
                  <p class="text-sm font-medium text-gray-600">No Bets Ekstrak/Mixing</p>
                  <p class="text-gray-800">{{ selectedRequest.noBets }}</p>
                </div>
                <div class="space-y-1">
                  <p class="text-sm font-medium text-gray-600">Besar Bets (Kg)</p>
                  <p class="text-gray-800">{{ selectedRequest.besarBets }} Kg</p>
                </div>
              </template>
            </div>

            <h4 class="text-lg font-semibold text-gray-800 mb-4">Daftar Item yang Diminta</h4>

            <div v-if="!selectedRequest.items || selectedRequest.items.length === 0"
              class="p-4 bg-yellow-50 text-yellow-700 rounded-lg">
              Tidak ada detail item yang tercatat.
            </div>

            <div v-else class="overflow-x-auto border border-gray-200 rounded-lg">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-16">No</th>
                    <template v-if="selectedRequest.type === 'foh-rs'">
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kode Item</th>
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Keterangan</th>
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Qty</th>
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">UoM</th>
                    </template>
                    <template v-else-if="selectedRequest.type === 'raw-material'">
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kode Bahan</th>
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Bahan</th>
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Jumlah Kebutuhan</th>
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Jumlah Kirim</th>
                    </template>
                    <template v-else-if="selectedRequest.type === 'packaging'">
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Material</th>
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kode PM</th>
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Jumlah Permintaan</th>
                    </template>
                    <template v-else-if="selectedRequest.type === 'add'">
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Material</th>
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kode PM</th>
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Alasan Tambahan</th>
                      <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Jumlah Permintaan</th>
                    </template>

                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <!-- Gunakan selectedRequest.items yang sudah dijamin camelCase oleh Controller -->
                  <tr v-for="(item, index) in selectedRequest.items" :key="index">
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ index + 1 }}</td>
                    <template v-if="selectedRequest.type === 'foh-rs'">
                      <td class="px-4 py-3 text-sm text-gray-700">{{ item.kodeItem }}</td>
                      <td class="px-4 py-3 text-sm text-gray-700">{{ item.keterangan }}</td>
                      <td class="px-4 py-3 text-sm text-gray-700">{{ item.qty }}</td>
                      <td class="px-4 py-3 text-sm text-gray-700">{{ item.uom }}</td>
                    </template>
                    <template v-else-if="selectedRequest.type === 'packaging'">
                      <td class="px-4 py-3 text-sm text-gray-700">{{ item.namaMaterial }}</td>
                      <td class="px-4 py-3 text-sm text-gray-700">{{ item.kodePM }}</td>
                      <td class="px-4 py-3 text-sm text-gray-700">{{ item.jumlahPermintaan }}</td>
                    </template>
                    <template v-else-if="selectedRequest.type === 'raw-material'">
                      <td class="px-4 py-3 text-sm text-gray-700">{{ item.kodeBahan }}</td>
                      <td class="px-4 py-3 text-sm text-gray-700">{{ item.namaBahan }}</td>
                      <td class="px-4 py-3 text-sm text-gray-700">{{ item.jumlahKebutuhan }}</td>
                      <td class="px-4 py-3 text-sm text-gray-700">{{ item.jumlahKirim }}</td>
                    </template>
                    <template v-else-if="selectedRequest.type === 'add'">
                      <td class="px-4 py-3 text-sm text-gray-700">{{ item.namaMaterial }}</td>
                      <td class="px-4 py-3 text-sm text-gray-700">{{ item.kodePM }}</td>
                      <td class="px-4 py-3 text-sm text-gray-700">{{ item.alasanPenambahan }}</td>
                      <td class="px-4 py-3 text-sm text-gray-700">{{ item.jumlahPermintaan }}</td>
                    </template>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
              <button @click="closeDetailModal"
                class="px-6 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                Tutup
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Konfirmasi (ganti alert/confirm) -->
    <div v-if="showConfirmationModal"
      class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]"
      style="background-color: rgba(43, 51, 63, 0.67);">
      <div class="bg-white rounded-lg p-6 w-full max-w-sm shadow-xl">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Peringatan Input</h3>
        <p class="text-sm text-gray-600 mb-6">{{ confirmationMessage }}</p>
        <div class="flex justify-end space-x-3">
          <button @click="showConfirmationModal = false"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm">
            Oke
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { router } from '@inertiajs/vue3'

// Data reaktif
const showModal = ref(false)
const selectedCategory = ref('')
const showFilterPanel = ref(false)
const searchQuery = ref('')
const showDetailModal = ref(false);
const selectedRequest = ref(null);
const showConfirmationModal = ref(false);
const confirmationMessage = ref('');
const uploadedFile = ref(null); 
const uploadStatus = ref(null);


// ** NEW STATE for Dynamic Search **
const materialSuggestions = ref([]);
const activeSearchIndex = ref(null); // { index: number, field: string }
let searchTimeout = null;

// Filter state
const filters = ref({
  category: '',
  status: '',
  dateFrom: '',
  dateTo: ''
})

// Category configuration
const categories = [
  { id: 'packaging', name: 'Request Packaging Material' },
  { id: 'raw-material', name: 'Request Raw Material' },
  { id: 'foh-rs', name: 'FOH & RS' },
  { id: 'add', name: 'ADD (Additional Request)' }
]

// Form data
const formData = ref({
  noReservasi: '',
  tanggalPermintaan: '',
  // FOH & RS
  departemen: '',
  alasanReservasi: '',
  // Packaging & ADD
  namaProduk: '',
  noBetsFilling: '',
  // Raw Material
  kodeProduk: '',
  noBets: '',
  besarBets: '',
  // Items array
  items: []
})

const handleFileUpload = (event) => {
    // Hanya ambil file pertama dari input
    const file = event.target.files ? event.target.files[0] : null;
    uploadedFile.value = file;
    uploadStatus.value = file ? { type: 'info', message: `File terpilih: ${file.name}` } : null;
};

const debugFileParse = async () => {
    if (!uploadedFile.value) {
        alert('Pilih file terlebih dahulu untuk debugging.');
        return;
    }

    // Buat FormData seperti pada fungsi upload normal
    const data = new FormData();
    data.append('file', uploadedFile.value);

    // Kirim file ke endpoint debugging di Controller
    // Menggunakan fetch biasa dan window.open untuk mengirim request yang memicu dd()
    
    // Perlu cara yang lebih baik untuk mengirim FormData melalui POST dan membuka dd()
    // Karena method ini kompleks, kita akan mengimplementasikan DD LANGSUNG di Controller, 
    // tetapi menggunakan route yang berbeda. Kita butuh cara agar file-nya bisa dipost.
    
    // Solusi tercepat: Kita akan menggunakan teknik 'Inertia.visit' dengan method GET
    // untuk memicu dd() saat file sudah diunggah.
    
    // Karena file besar, POST harus dilakukan. Kita akan pakai solusi di Controller saja.
    
    // --- Solusi Controller DD (Lihat Langkah 2) ---
    alert("Tester debug ambil data di pdf, pukkkk lah");
    
    // Kita akan gunakan debug point #2 dan #3 di Controller.
    // Lanjutkan ke Controller.
};

const uploadFileAndParse = async () => {
    // 1. Cek File Terpilih
    if (!uploadedFile.value) {
        uploadStatus.value = { type: 'error', message: '❌ Harap pilih file PDF terlebih dahulu.' };
        return;
    }

    // 2. Cek Kategori Yang Sesuai (Raw Material atau Packaging)
    if (selectedCategory.value !== 'raw-material' && selectedCategory.value !== 'packaging') {
        uploadStatus.value = { type: 'error', message: '❌ Mohon pilih kategori "Request Raw Material" atau "Request Packaging Material" di bagian atas form.' };
        return;
    }

    // Persiapan sebelum kirim
    formData.value.items = []; // Bersihkan tabel item lama
    uploadStatus.value = { type: 'info', message: 'Sedang memproses file, mohon tunggu...' };

    const data = new FormData();
    
    // 🔥 PERBAIKAN KRITIS: KIRIM FILE DAN REQUEST_TYPE
    data.append('file', uploadedFile.value); // Mengirim file itu sendiri (Perbaikan terakhir)
    data.append('request_type', selectedCategory.value); // Mengirim kategori yang dibutuhkan oleh validasi Controller (Perbaikan sebelumnya)

    try {
        // Kirim request ke endpoint parsing
        const response = await axios.post(route('transaction.reservation.parse-materials'), data, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        // 1. Ambil data material yang berhasil diparsing
        const materials = response.data.materials || [];
        const notFoundMaterials = response.data.notFoundMaterials || [];

        // Isi form data items
        if (materials.length > 0) {
            // Mapping items berdasarkan kategori yang dipilih
            formData.value.items = materials.map(m => {
                if (selectedCategory.value === 'raw-material') {
                     return {
                        kodeBahan: m.kodeBahan,
                        namaBahan: m.namaBahan,
                        jumlahKebutuhan: m.jumlahKebutuhan,
                        jumlahKirim: null,
                        satuan: m.satuan,
                        stokAvailable: m.stokAvailable,
                     }
                } else if (selectedCategory.value === 'packaging') {
                    return {
                        kodePM: m.kodePM,
                        namaMaterial: m.namaMaterial,
                        jumlahPermintaan: m.jumlahPermintaan,
                        satuan: m.satuan,
                        stokAvailable: m.stokAvailable,
                    }
                }
                return {}; // Default jika tidak ada
            });
            uploadStatus.value = { type: 'success', message: `✅ Berhasil mengimpor ${materials.length} material.` };
        } else {
            uploadStatus.value = { type: 'error', message: `❌ Tidak ada material yang ditemukan dalam dokumen untuk kategori ${selectedCategory.value}.` };
        }

        // Tampilkan peringatan jika ada material yang tidak ditemukan/stok kurang
        if (notFoundMaterials.length > 0) {
            // Gunakan message dari backend karena sudah berisi detail stok/not found
            const list = notFoundMaterials.map(m => `• **${m.kode}** (${m.satuan}): ${m.message}`).join('\n');
            confirmationMessage.value = `⚠️ Ditemukan masalah pada material berikut:\n\n${list}`;
            showConfirmationModal.value = true;
        }

    } catch (error) {
        console.error('Error saat parse file:', error);
        
        // Membaca pesan error dari backend
        const statusText = error.response?.statusText || 'Kesalahan Server';
        
        // Coba ambil pesan dari body respons
        let backendMessage = error.response?.data?.message || `Gagal memproses. (${statusText})`;
        
        // Ambil pesan validasi Laravel jika ada
        if (error.response?.data?.errors) {
             const errors = error.response.data.errors;
             // Gabungkan semua pesan error validasi menjadi satu string
             backendMessage = Object.values(errors).map(e => e[0]).join('; ');
        }
        
        uploadStatus.value = { 
            type: 'error', 
            message: `❌ Gagal memproses (${statusText}): ${backendMessage}`
        };
    } finally {
        // Reset file input setelah selesai
        uploadedFile.value = null;
        document.querySelector('input[type="file"]').value = '';
    }
};

const props = defineProps({
  // Tambahkan prop untuk data awal
  initialRequests: {
    type: Array,
    default: () => []
  },
  errors: Object,
  auth: Object,
  permissions: Array,
});

const requests = ref(props.initialRequests || []);

// Fetch reservation requests from the backend
const fetchReservations = async () => {
  try {
    const response = await axios.get(route('transaction.reservation.data'));
    // Data dari backend sudah di-map ke camelCase di Controller, jadi langsung assign
    requests.value = response.data;
  } catch (error) {
    console.error('Error fetching reservations:', error);
  }
}

// Computed properties
const getCategoryName = (type) => {
  const category = categories.find(c => c.id === type)
  return category ? category.name : type
}

const getCategoryClass = (type) => {
  const classes = {
    'foh-rs': 'bg-blue-100 text-blue-700',
    'packaging': 'bg-green-100 text-green-700',
    'raw-material': 'bg-purple-100 text-purple-700',
    'add': 'bg-orange-100 text-orange-700'
  }
  return classes[type] || 'bg-gray-100 text-gray-700'
}

const getItemTableTitle = computed(() => {
  switch (selectedCategory.value) {
    case 'foh-rs': return 'Daftar Item'
    case 'packaging': return 'Daftar Material'
    case 'raw-material': return 'Daftar Bahan Baku'
    case 'add': return 'Daftar Item Tambahan'
    default: return 'Daftar Item'
  }
})

// Logika cek apakah kuantitas melebihi stok
const isQtyExceeded = (item, qtyField) => {
  const requestedQty = parseFloat(item[qtyField]) || 0;
  const availableStock = parseFloat(item.stokAvailable) || 0;
  // Validasi: kuantitas > stok tersedia, DAN kuantitas tidak kosong, DAN stok tersedia sudah terisi
  return requestedQty > availableStock && requestedQty > 0 && item.stokAvailable !== undefined;
};

// Cek apakah ada item yang kuantitasnya melebihi stok
const hasStockExceededItems = computed(() => {
  // Tentukan field kuantitas yang relevan untuk kategori saat ini
  let qtyField = '';
  switch (selectedCategory.value) {
    case 'foh-rs':
      qtyField = 'qty';
      break;
    case 'packaging':
    case 'add':
      qtyField = 'jumlahPermintaan';
      break;
    case 'raw-material':
      qtyField = 'jumlahKebutuhan';
      break;
    default:
      return false; // Jika kategori tidak dipilih, anggap aman dari kelebihan stok
  }

  // Cek setiap item
  return formData.value.items.some(item => isQtyExceeded(item, qtyField));
});

// ** PERBAIKAN UTAMA DI FRONTEND: Memastikan semua field header wajib diisi **
const isFormValid = computed(() => {
  // 1. Cek Kategori, Tanggal, dan Item
  if (!selectedCategory.value || !formData.value.tanggalPermintaan || formData.value.items.length === 0) {
    return false
  }

  // 2. Cek semua item memiliki kode item yang dipilih/terisi (terlepas dari kuantitas)
  const isItemCodeFilled = formData.value.items.every(item => {
    switch (selectedCategory.value) {
      case 'foh-rs':
        return item.kodeItem && item.qty > 0;
      case 'packaging':
      case 'add':
        return item.kodePM && item.jumlahPermintaan > 0;
      case 'raw-material':
        return item.kodeBahan && item.jumlahKebutuhan > 0;
      default:
        return false;
    }
  });

  if (!isItemCodeFilled) {
    return false;
  }

  // 3. Cek validasi form header spesifik
  switch (selectedCategory.value) {
    case 'foh-rs':
      // Memastikan departemen & alasanReservasi terisi (wajib)
      return !!formData.value.departemen && !!formData.value.alasanReservasi
    case 'packaging':
      // Memastikan namaProduk & noBetsFilling terisi (wajib)
      return !!formData.value.namaProduk && !!formData.value.noBetsFilling
    case 'raw-material':
      // Memastikan kodeProduk, noBets, & besarBets terisi (wajib)
      return !!formData.value.kodeProduk && !!formData.value.noBets && formData.value.besarBets > 0
    case 'add':
      // Memastikan namaProduk & noBetsFilling terisi (wajib)
      return !!formData.value.namaProduk && !!formData.value.noBetsFilling
    default:
      return false
  }
})
// END PERBAIKAN FRONTEND

// Filter computed properties
const filteredRequests = computed(() => {
  let result = requests.value

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(req => {
      return req.noReservasi.toLowerCase().includes(query) ||
        getCategoryName(req.type).toLowerCase().includes(query) ||
        getDisplayText(req).toLowerCase().includes(query) ||
        req.status.toLowerCase().includes(query)
    })
  }

  // Category filter
  if (filters.value.category) {
    result = result.filter(req => req.type === filters.value.category)
  }

  // Status filter
  if (filters.value.status) {
    result = result.filter(req => req.status === filters.value.status)
  }

  // Date range filter
  if (filters.value.dateFrom) {
    const fromDate = new Date(filters.value.dateFrom)
    result = result.filter(req => new Date(req.tanggalPermintaan) >= fromDate)
  }

  if (filters.value.dateTo) {
    const toDate = new Date(filters.value.dateTo)
    toDate.setHours(23, 59, 59, 999)
    result = result.filter(req => new Date(req.tanggalPermintaan) <= toDate)
  }

  return result
})

const activeFiltersCount = computed(() => {
  let count = 0
  if (filters.value.category) count++
  if (filters.value.status) count++
  if (filters.value.dateFrom) count++
  if (filters.value.dateTo) count++
  return count
})

// ** DYNAMIC SEARCH METHODS (Tidak Berubah Signifikan) **

const startSearch = (index, field) => {
  // Memberi tahu Vue baris mana yang sedang diisi
  activeSearchIndex.value = { index, field };
  // Hanya membersihkan suggestions, tidak langsung memuat
  materialSuggestions.value = [];
}

const endSearch = () => {
  // Tunggu sebentar sebelum menutup dropdown untuk memberi waktu event mousedown berjalan
  setTimeout(() => {
    activeSearchIndex.value = null;
    materialSuggestions.value = [];
  }, 200);
}

const isSearching = (index, field) => {
  return activeSearchIndex.value &&
    activeSearchIndex.value.index === index &&
    activeSearchIndex.value.field === field;
}

const searchMaterials = async (query, type) => {
  if (!query || query.length < 2) {
    materialSuggestions.value = [];
    return;
  }

  try {
    const response = await axios.get(route('transaction.materials.search'), {
      params: { query, type }
    });
    // Response kini mencakup stokAvailable
    materialSuggestions.value = response.data;
  } catch (error) {
    console.error('Error fetching material suggestions:', error);
    materialSuggestions.value = [];
  }
}

const debounceSearch = (query, index, field) => {
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }
  // Set active search state
  activeSearchIndex.value = { index, field };

  searchTimeout = setTimeout(() => {
    searchMaterials(query, selectedCategory.value);
  }, 300); // Tunggu 300ms setelah input berhenti
}

const selectMaterial = (material, index, field) => {
  const item = formData.value.items[index];

  // Reset fields yang terkait sebelum mengisi yang baru
  item.kodeItem = '';
  item.keterangan = '';
  item.uom = '';
  item.kodePM = '';
  item.namaMaterial = '';
  item.kodeBahan = '';
  item.namaBahan = '';
  item.satuan = ''; // New field for unit of measure/satuan
  item.stokAvailable = material.stokAvailable; // ** NEW: Simpan Stok Tersedia **

  // Auto-fill berdasarkan tipe dan data yang dikembalikan
  if (selectedCategory.value === 'foh-rs') {
    item.kodeItem = material.kodeItem;
    item.keterangan = material.keterangan;
    item.uom = material.uom;
  } else if (selectedCategory.value === 'packaging' || selectedCategory.value === 'add') {
    item.kodePM = material.kodePM;
    item.namaMaterial = material.namaMaterial;
    item.satuan = material.satuan;
  } else if (selectedCategory.value === 'raw-material') {
    item.kodeBahan = material.kodeBahan;
    item.namaBahan = material.namaBahan;
    item.satuan = material.satuan;
  }

  // Tutup suggestions
  materialSuggestions.value = [];
  activeSearchIndex.value = null;
}

// ** END NEW DYNAMIC SEARCH METHODS **


// Methods
const getStatusClass = (status) => {
  const classes = {
    'Draft': 'bg-gray-100 text-gray-700',
    'Submitted': 'bg-blue-100 text-blue-700',
    'Approved': 'bg-green-100 text-green-700',
    'Rejected': 'bg-red-100 text-red-700',
    'Picking': 'bg-yellow-100 text-yellow-700',
    'Done': 'bg-purple-100 text-purple-700'
  }
  return classes[status] || 'bg-yellow-100 text-yellow-700'
}

const getDisplayText = (request) => {
  switch (request.type) {
    case 'foh-rs': return request.departemen
    case 'packaging': return request.namaProduk
    case 'raw-material': return request.kodeProduk
    case 'add': return request.namaProduk
    default: return '-'
  }
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
    });
  } catch {
    return dateString;
  }
}

const resetFilters = () => {
  filters.value = {
    category: '',
    status: '',
    dateFrom: '',
    dateTo: ''
  }
}

const openCreateModal = () => {
  resetForm()
  generateReservationNumber()
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  resetForm()
}

const resetForm = () => {
  uploadedFile.value = null;
  uploadStatus.value = null;
  selectedCategory.value = ''
  formData.value = {
    noReservasi: '',
    tanggalPermintaan: new Date().toISOString().slice(0, 16),
    departemen: '',
    alasanReservasi: '',
    namaProduk: '',
    noBetsFilling: '',
    kodeProduk: '',
    noBets: '',
    besarBets: '',
    items: []
  }
}

const generateReservationNumber = () => {
  const today = new Date().toISOString().slice(0, 10).replace(/-/g, '')
  const count = requests.value.length + 1
  formData.value.noReservasi = `RSV/${today}/${String(count).padStart(4, '0')}`
}

const addNewItem = () => {
  const newItem = {}

  switch (selectedCategory.value) {
    case 'foh-rs':
      newItem.kodeItem = ''
      newItem.keterangan = ''
      newItem.qty = null
      newItem.uom = ''
      newItem.stokAvailable = undefined // ** NEW: Default value **
      break
    case 'packaging':
      newItem.namaMaterial = ''
      newItem.kodePM = ''
      newItem.jumlahPermintaan = null
      newItem.satuan = ''
      newItem.stokAvailable = undefined // ** NEW: Default value **
      break
    case 'raw-material':
      newItem.kodeBahan = ''
      newItem.namaBahan = ''
      newItem.jumlahKebutuhan = null
      newItem.jumlahKirim = null
      newItem.satuan = ''
      newItem.stokAvailable = undefined // ** NEW: Default value **
      break
    case 'add':
      newItem.namaMaterial = ''
      newItem.kodePM = ''
      newItem.alasanPenambahan = ''
      newItem.jumlahPermintaan = null
      newItem.satuan = ''
      newItem.stokAvailable = undefined // ** NEW: Default value **
      break
  }

  formData.value.items.push(newItem)
}

const removeItem = (index) => {
  formData.value.items.splice(index, 1)
}

const submitRequest = async () => {

  if (!isFormValid.value) {
    confirmationMessage.value = 'Mohon lengkapi semua field yang diperlukan (termasuk Tanggal Permintaan, minimal satu Item, dan semua field header wajib) sebelum melanjutkan.';
    showConfirmationModal.value = true;
    return;
  }

  // Jika ada item yang melebihi stok, hentikan dan beri peringatan
  if (hasStockExceededItems.value) {
    confirmationMessage.value = 'Tidak dapat mengirim permintaan: Beberapa item melebihi stok yang tersedia. Harap perbaiki kuantitas permintaan.';
    showConfirmationModal.value = true;
    return;
  }


  // Siapkan payload, tambahkan 'request_type' yang dibutuhkan oleh Controller
  const payload = {
    ...formData.value,
    request_type: selectedCategory.value, // Menggunakan request_type sesuai nama kolom di backend
  };

  // Gunakan router.post dari Inertia untuk submit
  router.post(route('transaction.reservation.store'), payload, {
    preserveScroll: true,

    onSuccess: () => {
      console.log('✅ Reservation request berhasil dibuat!');
      closeModal();
      fetchReservations();
      // Hapus window.location.reload()
    },

    onError: (errors) => {
      console.error('Validation errors:', errors);

      let errorMessage = '❌ Gagal membuat reservasi karena data tidak lengkap:\n\n';
      // Tampilkan error dari backend (termasuk validasi stok)
      for (const field in errors) {
        if (Object.prototype.hasOwnProperty.call(errors, field)) {
          // Hanya ambil error pertama untuk setiap field untuk ringkasan
          const errorMsg = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
          errorMessage += `• ${errorMsg}\n`;

        }
      }
      // Tampilkan error di custom modal
      confirmationMessage.value = errorMessage;
      showConfirmationModal.value = true;
    }
  });
}

const viewDetail = (request) => {
  // PENTING: Gunakan JSON parse/stringify untuk membuat deep copy agar reaktivitas aman
  selectedRequest.value = JSON.parse(JSON.stringify(request));
  showDetailModal.value = true;
}

const closeDetailModal = () => {
  showDetailModal.value = false;
  selectedRequest.value = null;
}

const printForm = (request) => {
  // PENTING: Gunakan deep copy untuk mencegah manipulasi data asli saat mencetak
  const requestToPrint = JSON.parse(JSON.stringify(request));

  if (!requestToPrint.items || !Array.isArray(requestToPrint.items)) {
    console.error("Tidak dapat mencetak: Data item tidak valid atau kosong.");
    confirmationMessage.value = "Tidak dapat mencetak: Data item tidak valid atau kosong.";
    showConfirmationModal.value = true;
    return;
  }

  // Create print window with form template
  const printWindow = window.open('', '_blank')

  let formTitle = ''
  let formContent = ''

  // Pastikan requestToPrint.items digunakan di sini
  const itemsHtml = requestToPrint.items.map((item, idx) => {
    let cells = `<td>${idx + 1}</td>`;
    // Menggunakan requestToPrint.type.toLowerCase() untuk menjamin konsistensi
    const type = requestToPrint.type ? requestToPrint.type.toLowerCase() : '';

    if (type === 'foh-rs') {
      cells += `
            <td>${item.kodeItem || '-'}</td>
            <td>${item.keterangan || '-'}</td>
            <td>${item.qty || '-'}</td>
            <td>${item.uom || '-'}</td>
        `;
    } else if (type === 'packaging') {
      cells += `
            <td>${item.namaMaterial || '-'}</td>
            <td>${item.kodePM || '-'}</td>
            <td>${item.jumlahPermintaan || '-'}</td>
        `;
    } else if (type === 'raw-material') {
      cells += `
            <td>${item.kodeBahan || '-'}</td>
            <td>${item.namaBahan || '-'}</td>
            <td>${item.jumlahKebutuhan || '-'}</td>
            <td>${item.jumlahKirim || '-'}</td>
        `;
    } else if (type === 'add') {
      cells += `
            <td>${item.namaMaterial || '-'}</td>
            <td>${item.kodePM || '-'}</td>
            <td>${item.alasanPenambahan || '-'}</td>
            <td>${item.jumlahPermintaan || '-'}</td>
        `;
    }

    return `<tr>${cells}</tr>`;
  }).join('');

  // FIX: Menggunakan .toLowerCase() pada switch untuk mengatasi masalah case sensitivity
  switch (requestToPrint.type ? requestToPrint.type.toLowerCase() : '') {
    case 'foh-rs':
      formTitle = 'FORM FOH & RS REQUEST'
      formContent = `
        <div class="info-section">
          <div class="info-row">
            <span><strong>No Reservasi:</strong> ${requestToPrint.noReservasi}</span>
            <span><strong>Tanggal:</strong> ${formatDateTime(requestToPrint.tanggalPermintaan)}</span>
          </div>
          <div class="info-row">
            <span><strong>Departemen:</strong> ${requestToPrint.departemen}</span>
            <span><strong>Status:</strong> ${requestToPrint.status}</span>
          </div>
          <div class="info-row">
            <span><strong>Alasan Reservasi:</strong> ${requestToPrint.alasanReservasi || '-'}</span>
          </div>
        </div>

        <table class="items-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Kode Item</th>
              <th>Keterangan</th>
              <th>Qty</th>
              <th>UoM</th>
            </tr>
          </thead>
          <tbody>
          ${itemsHtml}
          </tbody>
        </table>
      `
      break

    case 'packaging':
      formTitle = 'FORM REQUEST PACKAGING MATERIAL'
      formContent = `
        <div class="info-section">
          <div class="info-row">
            <span><strong>No Reservasi:</strong> ${requestToPrint.noReservasi}</span>
            <span><strong>Tanggal & Jam:</strong> ${formatDateTime(requestToPrint.tanggalPermintaan)}</span>
          </div>
          <div class="info-row">
            <span><strong>Nama Produk:</strong> ${requestToPrint.namaProduk}</span>
            <span><strong>No Bets Filling:</strong> ${requestToPrint.noBetsFilling}</span>
          </div>
        </div>

        <table class="items-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Material</th>
              <th>Kode PM</th>
              <th>Jumlah Permintaan</th>
            </tr>
          </thead>
          <tbody>
          ${itemsHtml}
          </tbody>
        </table>
      `
      break

    case 'raw-material':
      formTitle = 'FORM REQUEST RAW MATERIAL'
      formContent = `
        <div class="info-section">
          <div class="info-row">
            <span><strong>No Reservasi:</strong> ${requestToPrint.noReservasi}</span>
            <span><strong>Tanggal:</strong> ${formatDateTime(requestToPrint.tanggalPermintaan)}</span>
          </div>
          <div class="info-row">
            <span><strong>Kode Produk:</strong> ${requestToPrint.kodeProduk}</span>
            <span><strong>No Bets:</strong> ${requestToPrint.noBets}</span>
          </div>
          <div class="info-row">
            <span><strong>Besar Bets:</strong> ${requestToPrint.besarBets} Kg</span>
          </div>
        </div>

        <table class="items-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Kode Bahan</th>
              <th>Nama Bahan</th>
              <th>Jumlah Kebutuhan</th>
              <th>Jumlah Kirim</th>
            </tr>
          </thead>
          <tbody>
          ${itemsHtml}
          </tbody>
        </table>
      `
      break

    case 'add':
      formTitle = 'FORM ADD - ADDITIONAL REQUEST'
      formContent = `
        <div class="info-section">
          <div class="info-row">
            <span><strong>No Reservasi:</strong> ${requestToPrint.noReservasi}</span>
            <span><strong>Tanggal & Jam:</strong> ${formatDateTime(requestToPrint.tanggalPermintaan)}</span>
          </div>
          <div class="info-row">
            <span><strong>Nama Produk:</strong> ${requestToPrint.namaProduk}</span>
            <span><strong>No Bets Filling:</strong> ${requestToPrint.noBetsFilling}</span>
          </div>
        </div>

        <table class="items-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Material</th>
              <th>Kode PM</th>
              <th>Alasan Penambahan</th>
              <th>Jumlah Permintaan</th>
            </tr>
          </thead>
          <tbody>
          ${itemsHtml}
          </tbody>
        </table>
      `
      break
    default:
      console.error("Tipe request tidak dikenal: ", requestToPrint.type);
      confirmationMessage.value = `Tipe request tidak dikenal: ${requestToPrint.type}`;
      showConfirmationModal.value = true;
      return; // Hentikan jika tipe tidak dikenal
  }

  printWindow.document.write(`
    <html>
      <head>
        <title>${formTitle} - ${requestToPrint.noReservasi}</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; }
          .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
          .info-section { margin: 20px 0; }
          .info-row { display: flex; justify-content: space-between; margin: 8px 0; }
          .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
          .items-table th, .items-table td { border: 1px solid #000; padding: 8px; text-align: left; }
          .items-table th { background-color: #f0f0f0; font-weight: bold; }
          .signature { margin-top: 40px; display: flex; justify-content: space-between; }
          .signature div { text-align: center; }
          .signature-line { border-top: 1px solid #000; width: 150px; margin-top: 40px; }
          @media print {
            body { margin: 0; font-size: 11px; }
          }
        </style>
      </head>
      <body>
        <div class="header">
          <h2>${formTitle}</h2>
          <p>No: ${requestToPrint.noReservasi}</p>
        </div>

        ${formContent}

        <div class="signature">
          <div>
            <p>Dibuat Oleh:</p>
            <div class="signature-line"></div>
            <p>Requester</p>
          </div>
          <div>
            <p>Disetujui Oleh:</p>
            <div class="signature-line"></div>
            <p>Supervisor</p>
          </div>
          <div>
            <p>Tanggal:</p>
            <div class="signature-line"></div>
            <p>${new Date().toLocaleDateString('id-ID')}</p>
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
onMounted(() => {})
</script>