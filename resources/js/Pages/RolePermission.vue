<template>
    <AppLayout title="Riwayat Aktivitas">
        <div class="role-permission-management">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Role & Permission Management</h1>
      <button 
        @click="showAddRoleModal = true"
        class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah Role
      </button>
    </div>

    <!-- Tabel Daftar Role -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg dark:shadow-gray-900/50 overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-900">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deskripsi</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jumlah User</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-for="role in roles" :key="role.id" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ role.name }}</div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm text-gray-600 dark:text-gray-400">{{ role.description }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                {{ role.userCount }} users
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
              <button 
                @click="openPermissionModal(role)"
                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors"
              >
                Edit Permission
              </button>
              <button 
                @click="deleteRole(role.id)"
                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors"
              >
                Hapus Role
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Tambah Role -->
    <div v-if="showAddRoleModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 dark:bg-opacity-90 backdrop-blur-sm flex items-center justify-center z-[9999]">
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tambah Role Baru</h3>
          <button @click="showAddRoleModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <form @submit.prevent="addRole">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Role</label>
            <input 
              v-model="newRole.name"
              type="text" 
              required
              placeholder="Contoh: QC, Supervisor, Operator Gudang"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500"
            >
          </div>
          
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Deskripsi Role (Opsional)</label>
            <textarea 
              v-model="newRole.description"
              rows="3"
              placeholder="Role untuk tim QC yang memeriksa barang masuk"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500"
            ></textarea>
          </div>
          
          <div class="flex justify-end space-x-3">
            <button 
              type="button"
              @click="showAddRoleModal = false"
              class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
            >
              Batal
            </button>
            <button 
              type="submit"
              class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors"
            >
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Atur Permission -->
    <div v-if="showPermissionModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 dark:bg-opacity-90 backdrop-blur-sm flex items-center justify-center z-[9999]">
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto shadow-xl">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Atur Permission - {{ selectedRole?.name }}</h3>
          <button @click="showPermissionModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div class="space-y-6">
          <!-- Incoming / Receipt -->
          <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
              <span class="mr-2">📥</span>
              Incoming / Receipt
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
              <label v-for="action in ['View', 'Create', 'Edit', 'Delete', 'Approve']" :key="`incoming-${action}`" class="flex items-center text-gray-700 dark:text-gray-300">
                <input 
                  type="checkbox" 
                  :checked="hasPermission('incoming', action.toLowerCase())"
                  @change="togglePermission('incoming', action.toLowerCase(), $event)"
                  class="mr-2 rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700"
                >
                {{ action }}
              </label>
            </div>
          </div>

          <!-- Quality Control -->
          <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
              <span class="mr-2">🔍</span>
              Quality Control
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
              <label v-for="action in ['View', 'Input QC Result', 'Approve', 'Reject']" :key="`qc-${action}`" class="flex items-center text-gray-700 dark:text-gray-300">
                <input 
                  type="checkbox" 
                  :checked="hasPermission('qc', action.toLowerCase().replace(/ /g, '_'))"
                  @change="togglePermission('qc', action.toLowerCase().replace(/ /g, '_'), $event)"
                  class="mr-2 rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700"
                >
                {{ action }}
              </label>
            </div>
          </div>

          <!-- Label Karantina -->
          <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
              <span class="mr-2">🏷️</span>
              Label Karantina
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
              <label v-for="action in ['View', 'Cetak Label', 'Release', 'Reject']" :key="`label-${action}`" class="flex items-center text-gray-700 dark:text-gray-300">
                <input 
                  type="checkbox" 
                  :checked="hasPermission('label_karantina', action.toLowerCase().replace(/ /g, '_'))"
                  @change="togglePermission('label_karantina', action.toLowerCase().replace(/ /g, '_'), $event)"
                  class="mr-2 rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700"
                >
                {{ action }}
              </label>
            </div>
          </div>

          <!-- Putaway & Transfer Order -->
          <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
              <span class="mr-2">📦</span>
              Putaway & Transfer Order
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
              <label v-for="action in ['View', 'Kerjakan TO', 'Cetak Slip']" :key="`putaway-${action}`" class="flex items-center text-gray-700 dark:text-gray-300">
                <input 
                  type="checkbox" 
                  :checked="hasPermission('putaway', action.toLowerCase().replace(/ /g, '_'))"
                  @change="togglePermission('putaway', action.toLowerCase().replace(/ /g, '_'), $event)"
                  class="mr-2 rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700"
                >
                {{ action }}
              </label>
            </div>
          </div>

          <!-- Reservation -->
          <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
              <span class="mr-2">📋</span>
              Reservation (FOH, RS, RM, Packaging, ADD)
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
              <label v-for="action in ['View', 'Create Request', 'Approve Request', 'Cetak Form']" :key="`reservation-${action}`" class="flex items-center text-gray-700 dark:text-gray-300">
                <input 
                  type="checkbox" 
                  :checked="hasPermission('reservation', action.toLowerCase().replace(/ /g, '_'))"
                  @change="togglePermission('reservation', action.toLowerCase().replace(/ /g, '_'), $event)"
                  class="mr-2 rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700"
                >
                {{ action }}
              </label>
            </div>
          </div>

          <!-- Picking -->
          <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
              <span class="mr-2">🛒</span>
              Picking
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
              <label v-for="action in ['View', 'Kerjakan Picking', 'Cetak Picking List']" :key="`picking-${action}`" class="flex items-center text-gray-700 dark:text-gray-300">
                <input 
                  type="checkbox" 
                  :checked="hasPermission('picking', action.toLowerCase().replace(/ /g, '_'))"
                  @change="togglePermission('picking', action.toLowerCase().replace(/ /g, '_'), $event)"
                  class="mr-2 rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700"
                >
                {{ action }}
              </label>
            </div>
          </div>

          <!-- Return -->
          <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
              <span class="mr-2">↩️</span>
              Return (Supplier / Production)
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
              <label v-for="action in ['View', 'Create Return', 'Approve Return', 'Cetak Slip']" :key="`return-${action}`" class="flex items-center text-gray-700 dark:text-gray-300">
                <input 
                  type="checkbox" 
                  :checked="hasPermission('return', action.toLowerCase().replace(/ /g, '_'))"
                  @change="togglePermission('return', action.toLowerCase().replace(/ /g, '_'), $event)"
                  class="mr-2 rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700"
                >
                {{ action }}
              </label>
            </div>
          </div>

          <!-- Central Data -->
          <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
              <span class="mr-2">⚙️</span>
              Central Data
            </h4>
            <div class="space-y-3">
              <div v-for="module in ['SKU Management', 'Supplier Management', 'Bin Management', 'User Management', 'Role Management']" 
                   :key="module" 
                   class="border-l-4 border-gray-300 dark:border-gray-600 pl-4">
                <h5 class="font-medium text-gray-700 dark:text-gray-300 mb-2">{{ module }}</h5>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                  <label v-for="action in ['View', 'Create', 'Edit', 'Delete', 'Admin']" :key="`${module}-${action}`" class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                    <input 
                      type="checkbox" 
                      :checked="hasPermission('central_data', `${module.toLowerCase().replace(/ /g, '_')}_${action.toLowerCase()}`)"
                      @change="togglePermission('central_data', `${module.toLowerCase().replace(/ /g, '_')}_${action.toLowerCase()}`, $event)"
                      class="mr-1 rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700"
                    >
                    {{ action }}
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
          <button 
            @click="showPermissionModal = false"
            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
          >
            Batal
          </button>
          <button 
            @click="savePermissions"
            class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors"
          >
            Simpan Permission
          </button>
        </div>
      </div>
    </div>
  </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'

import { ref, reactive } from 'vue'

interface Role {
  id: number
  name: string
  description: string
  userCount: number
}

interface Permission {
  module: string
  action: string
  allowed: boolean
}

// State
const showAddRoleModal = ref(false)
const showPermissionModal = ref(false)
const selectedRole = ref<Role | null>(null)

const newRole = reactive({
  name: '',
  description: ''
})

// Dummy data untuk roles
const roles = ref<Role[]>([
  {
    id: 1,
    name: 'Super Admin',
    description: 'Administrator dengan akses penuh ke seluruh sistem',
    userCount: 2
  },
  {
    id: 2,
    name: 'QC Inspector',
    description: 'Role untuk tim QC yang memeriksa barang masuk',
    userCount: 5
  },
  {
    id: 3,
    name: 'Warehouse Supervisor',
    description: 'Supervisor gudang dengan akses approve dan monitoring',
    userCount: 3
  },
  {
    id: 4,
    name: 'Operator Gudang',
    description: 'Operator untuk kegiatan operasional gudang sehari-hari',
    userCount: 12
  },
  {
    id: 5,
    name: 'Purchasing Staff',
    description: 'Staff purchasing untuk mengelola pembelian dan supplier',
    userCount: 4
  }
])

// State untuk menyimpan permissions sementara
const currentPermissions = ref<Permission[]>([])

// Functions
const addRole = () => {
  const newId = Math.max(...roles.value.map(r => r.id)) + 1
  roles.value.push({
    id: newId,
    name: newRole.name,
    description: newRole.description,
    userCount: 0
  })
  
  // Reset form
  newRole.name = ''
  newRole.description = ''
  showAddRoleModal.value = false
  
  alert('Role berhasil ditambahkan!')
}

const deleteRole = (roleId: number) => {
  const role = roles.value.find(r => r.id === roleId)
  if (role && confirm(`Apakah Anda yakin ingin menghapus role "${role.name}"?`)) {
    roles.value = roles.value.filter(r => r.id !== roleId)
    alert('Role berhasil dihapus!')
  }
}

const openPermissionModal = (role: Role) => {
  selectedRole.value = role
  showPermissionModal.value = true
  
  // Load existing permissions (dummy data)
  loadRolePermissions(role.id)
}

const loadRolePermissions = (roleId: number) => {
  // Dummy permissions berdasarkan role
  const dummyPermissions: { [key: number]: Permission[] } = {
    1: [ // Super Admin - full access
      { module: 'incoming', action: 'view', allowed: true },
      { module: 'incoming', action: 'create', allowed: true },
      { module: 'incoming', action: 'edit', allowed: true },
      { module: 'incoming', action: 'delete', allowed: true },
      { module: 'incoming', action: 'approve', allowed: true },
      { module: 'qc', action: 'view', allowed: true },
      { module: 'qc', action: 'input_qc_result', allowed: true },
      { module: 'qc', action: 'approve', allowed: true },
      { module: 'qc', action: 'reject', allowed: true },
    ],
    2: [ // QC Inspector - limited to QC functions
      { module: 'qc', action: 'view', allowed: true },
      { module: 'qc', action: 'input_qc_result', allowed: true },
      { module: 'label_karantina', action: 'view', allowed: true },
      { module: 'label_karantina', action: 'cetak_label', allowed: true },
    ],
    3: [ // Warehouse Supervisor - approve access
      { module: 'incoming', action: 'view', allowed: true },
      { module: 'incoming', action: 'approve', allowed: true },
      { module: 'putaway', action: 'view', allowed: true },
      { module: 'putaway', action: 'kerjakan_to', allowed: true },
      { module: 'picking', action: 'view', allowed: true },
    ],
    4: [ // Operator Gudang - operational only
      { module: 'putaway', action: 'view', allowed: true },
      { module: 'putaway', action: 'kerjakan_to', allowed: true },
      { module: 'picking', action: 'view', allowed: true },
      { module: 'picking', action: 'kerjakan_picking', allowed: true },
    ],
    5: [ // Purchasing Staff
      { module: 'incoming', action: 'view', allowed: true },
      { module: 'incoming', action: 'create', allowed: true },
      { module: 'return', action: 'view', allowed: true },
      { module: 'return', action: 'create_return', allowed: true },
    ]
  }
  
  currentPermissions.value = dummyPermissions[roleId] || []
}

const hasPermission = (module: string, action: string): boolean => {
  return currentPermissions.value.some(p => p.module === module && p.action === action && p.allowed)
}

const togglePermission = (module: string, action: string, event: Event) => {
  const target = event.target as HTMLInputElement
  const allowed = target.checked
  
  const existingIndex = currentPermissions.value.findIndex(p => p.module === module && p.action === action)
  
  if (existingIndex >= 0) {
    currentPermissions.value[existingIndex].allowed = allowed
  } else {
    currentPermissions.value.push({ module, action, allowed })
  }
}

const savePermissions = () => {
  // Simulate saving to database
  console.log(`Saving permissions for role ${selectedRole.value?.name}:`, currentPermissions.value)
  
  showPermissionModal.value = false
  alert('Permission berhasil disimpan!')
}
</script>