<template>
    <AppLayout title="Role & Permission Management">
        <div class="role-permission-management">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Role & Permission Management</h1>
                <div class="flex gap-2">
                    <button 
                        @click="showManagePermissionsModal = true"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Kelola Permission
                    </button>
                    <button 
                        @click="showAddRoleModal = true"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Role
                    </button>
                </div>
            </div>

            <!-- Success/Error Message -->
            <div v-if="$page.props.flash?.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash?.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ $page.props.flash.error }}
            </div>

            <!-- Tabel Daftar Role -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ringkasan Permissions</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="role in roles" :key="role.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ role.name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600">{{ role.description }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <span v-if="role.permissions && role.permissions.length > 0">
                                    Total **{{ role.permissions.length }}** permissions di **{{ getModuleCount(role.permissions) }}** modul.
                                </span>
                                <span v-else class="text-red-500">Tidak ada Permission</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ role.userCount }} users
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button 
                                    @click="openPermissionModal(role)"
                                    class="text-indigo-600 hover:text-indigo-900 transition-colors"
                                >
                                    Atur Permission
                                </button>
                                <button 
                                    @click="deleteRole(role.id)"
                                    class="text-red-600 hover:text-red-900 transition-colors"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="showAddRoleModal" class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: rgba(43, 51, 63, 0.67);"></div>

            <!-- Modal Tambah Role -->
            <div v-if="showAddRoleModal" class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: rgba(43, 51, 63, 0.67);">
                <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-xl">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Tambah Role Baru</h3>
                        <button @click="closeAddRoleModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form @submit.prevent="addRole">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Role</label>
                            <input 
                                v-model="newRole.name"
                                type="text" 
                                required
                                placeholder="Contoh: QC, Supervisor, Operator Gudang"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900 placeholder-gray-400"
                            >
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Role (Opsional)</label>
                            <textarea 
                                v-model="newRole.description"
                                rows="3"
                                placeholder="Role untuk tim QC yang memeriksa barang masuk"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900 placeholder-gray-400"
                            ></textarea>
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button 
                                type="button"
                                @click="closeAddRoleModal"
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors"
                            >
                                Batal
                            </button>
                            <button 
                                type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                            >
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Atur Permission -->
            <div v-if="showPermissionModal" class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: rgba(43, 51, 63, 0.67);">
                <div class="bg-white rounded-xl w-full max-w-5xl max-h-[90vh] flex flex-col shadow-2xl">
                    <!-- Modal Header -->
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-xl">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Atur Otorisasi Role</h3>
                            <p class="text-sm text-gray-600 mt-1">Mengatur hak akses untuk role: <span class="font-bold text-blue-600">{{ selectedRole?.name }}</span></p>
                        </div>
                        <button @click="closePermissionModal" class="text-gray-400 hover:text-gray-600 transition p-2 hover:bg-gray-200 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body (Scrollable) -->
                    <div class="flex-1 overflow-y-auto p-6 bg-gray-100">
                        <div class="grid grid-cols-1 gap-6">
                            <div 
                                v-for="moduleData in allPermissions" 
                                :key="moduleData.module_key" 
                                class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden"
                            >
                                <!-- Module Header -->
                                <div class="bg-blue-50 px-4 py-3 border-b border-blue-100 flex justify-between items-center">
                                    <h4 class="font-bold text-blue-800 flex items-center text-lg">
                                        <span v-html="moduleData.module_name" class="mr-2"></span>
                                        <span class="text-xs bg-blue-200 text-blue-800 px-2 py-0.5 rounded-full">{{ moduleData.permissions.length }}</span>
                                    </h4>
                                    <label class="flex items-center space-x-2 cursor-pointer bg-white px-3 py-1.5 rounded-md border border-blue-200 hover:bg-blue-50 transition">
                                        <input
                                            type="checkbox"
                                            :checked="isModuleAllSelected(moduleData.module_key)"
                                            @change="toggleSelectAllModule(moduleData.module_key, $event)"
                                            class="rounded text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer"
                                        >
                                        <span class="text-sm font-semibold text-blue-700 select-none">Pilih Semua</span>
                                    </label>
                                </div>
                                
                                <!-- Permission Grid -->
                                <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                    <label 
                                        v-for="permission in moduleData.permissions" 
                                        :key="permission.name" 
                                        :class="[
                                            'flex items-start p-3 rounded-lg border cursor-pointer transition-all hover:shadow-md',
                                            hasPermission(permission.name) 
                                                ? 'bg-blue-50 border-blue-300 ring-1 ring-blue-300' 
                                                : 'bg-white border-gray-200 hover:border-blue-300'
                                        ]"
                                    >
                                        <div class="flex items-center h-5">
                                            <input 
                                                type="checkbox" 
                                                :checked="hasPermission(permission.name)"
                                                @change="togglePermission(permission.name, $event)"
                                                class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-gray-300 cursor-pointer"
                                            >
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <span class="font-semibold text-gray-900 block mb-0.5">
                                                {{ permission.display_name }}
                                            </span>
                                            <span class="text-gray-500 text-xs block leading-tight">
                                                {{ permission.description || 'Tidak ada deskripsi' }}
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 border-t border-gray-200 bg-white rounded-b-xl flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            Terpilih: <span class="font-bold text-blue-600 text-lg">{{ selectedPermissionCount }}</span> permission
                        </div>
                        <div class="flex space-x-3">
                            <button 
                                @click="closePermissionModal"
                                class="px-5 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-lg font-medium hover:bg-gray-50 transition focus:ring-2 focus:ring-offset-2 focus:ring-gray-200"
                            >
                                Batal
                            </button>
                            <button 
                                @click="savePermissions"
                                :disabled="isSaving"
                                class="px-5 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition shadow-lg shadow-blue-200 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                            >
                                <svg v-if="isSaving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Kelola Permission (CRUD) -->
            <div v-if="showManagePermissionsModal" class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999]" style="background-color: rgba(43, 51, 63, 0.67);">
                <div class="bg-white rounded-lg p-6 w-full max-w-5xl max-h-[90vh] overflow-y-auto shadow-xl">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Kelola Daftar Permission</h3>
                        <button @click="showManagePermissionsModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Add Permission Form -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h4 class="text-md font-semibold text-blue-800 mb-3">Tambah Permission Baru</h4>
                        <form @submit.prevent="addPermission" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Module</label>
                                <input 
                                    v-model="newPermission.module"
                                    type="text" 
                                    required
                                    placeholder="cycle_count"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                                <input 
                                    v-model="newPermission.action"
                                    type="text" 
                                    required
                                    placeholder="approve"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                <input 
                                    v-model="newPermission.description"
                                    type="text" 
                                    placeholder="Approve Cycle Count"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                >
                            </div>
                            <div class="flex items-end">
                                <button 
                                    type="submit"
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm font-medium"
                                >
                                    + Tambah
                                </button>
                            </div>
                        </form>
                        <p class="text-xs text-gray-500 mt-2">
                            Permission name akan dibuat otomatis: <code class="bg-gray-100 px-1 rounded">{{ newPermission.module || 'module' }}.{{ newPermission.action || 'action' }}</code>
                        </p>
                    </div>

                    <!-- Permission List by Module -->
                    <div class="space-y-4">
                        <div 
                            v-for="moduleData in allPermissions" 
                            :key="moduleData.module_key" 
                            class="border border-gray-200 rounded-lg overflow-hidden"
                        >
                            <div class="bg-gray-100 px-4 py-2 font-semibold text-gray-800 flex items-center">
                                <span v-html="moduleData.module_name"></span>
                                <span class="ml-2 text-xs text-gray-500">({{ moduleData.permissions.length }} permissions)</span>
                            </div>
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Permission Name</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="permission in moduleData.permissions" :key="permission.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-sm text-gray-900 font-mono">{{ permission.name }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-600">{{ permission.display_name }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-500">{{ permission.description || '-' }}</td>
                                        <td class="px-4 py-2 text-right space-x-2">
                                            <button 
                                                @click="openEditPermissionModal(permission, moduleData.module_key)"
                                                class="text-indigo-600 hover:text-indigo-900 text-sm"
                                            >
                                                Edit
                                            </button>
                                            <button 
                                                @click="deletePermission(permission.id, permission.name)"
                                                class="text-red-600 hover:text-red-900 text-sm"
                                            >
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6 pt-4 border-t border-gray-200">
                        <button 
                            @click="showManagePermissionsModal = false"
                            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Edit Permission -->
            <div v-if="showEditPermissionModal" class="fixed inset-0 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[10000]" style="background-color: rgba(43, 51, 63, 0.67);">
                <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-xl">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Edit Permission</h3>
                        <button @click="showEditPermissionModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form @submit.prevent="updatePermission">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Module</label>
                            <input 
                                v-model="editingPermission.module"
                                type="text" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                            <input 
                                v-model="editingPermission.action"
                                type="text" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <input 
                                v-model="editingPermission.description"
                                type="text" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                        </div>
                        <p class="text-xs text-gray-500 mb-4">
                            Permission name akan menjadi: <code class="bg-gray-100 px-1 rounded">{{ editingPermission.module || 'module' }}.{{ editingPermission.action || 'action' }}</code>
                        </p>
                        
                        <div class="flex justify-end space-x-3">
                            <button 
                                type="button"
                                @click="showEditPermissionModal = false"
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors"
                            >
                                Batal
                            </button>
                            <button 
                                type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                            >
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'

interface Role {
    id: number
    name: string
    description: string
    userCount: number
    permissions: string[]
}

interface AllPermissionItem {
    id: number;
    name: string; // permission_name
    display_name: string; // Nama yang lebih mudah dibaca
    description: string;
}

interface ModulePermission {
    module_key: string;
    module_name: string; // Nama modul + emoji
    permissions: AllPermissionItem[];
}

interface PermissionState {
    name: string // permission_name
    allowed: boolean
}

interface Permission {
    id: number
    module: string
    action: string
    permission_name: string
}

const props = defineProps<{
    roles: Role[]
    allPermissions: ModulePermission[] // Menggunakan interface dinamis
}>()

// State
const showAddRoleModal = ref(false)
const showPermissionModal = ref(false)
const showManagePermissionsModal = ref(false)
const showEditPermissionModal = ref(false)
const selectedRole = ref<Role | null>(null)
const isSaving = ref(false)

const newRole = reactive({
    name: '',
    description: ''
})

const newPermission = reactive({
    module: '',
    action: '',
    description: ''
})

const editingPermission = reactive({
    id: 0,
    module: '',
    action: '',
    description: ''
})

const currentPermissions = ref<PermissionState[]>([])

const permissionMap = computed<Map<string, PermissionState>>(() => {
    return new Map(currentPermissions.value.map(p => [p.name, p]));
});

// Computed baru untuk UI
const selectedPermissionCount = computed(() => {
    return currentPermissions.value.filter(p => p.allowed).length;
});

const isModuleAllSelected = (moduleKey: string): boolean => {
    const module = props.allPermissions.find(m => m.module_key === moduleKey)
    if (!module || module.permissions.length === 0) return false

    // Cek apakah setiap permission di modul ini memiliki status 'allowed: true'
    return module.permissions.every(p => {
        const state = permissionMap.value.get(p.name)
        return state && state.allowed
    })
}

const toggleSelectAllModule = (moduleKey: string, event: Event) => {
    const target = event.target as HTMLInputElement
    const checkAll = target.checked
    
    const module = props.allPermissions.find(m => m.module_key === moduleKey)
    if (!module) return

    // Kita update currentPermissions secara reactive
    // Loop permissions di modul ini dan update state mereka
    module.permissions.forEach(permission => {
        const permName = permission.name
        const existingPerm = currentPermissions.value.find(p => p.name === permName)

        if (existingPerm) {
            existingPerm.allowed = checkAll
        } else {
            // Fallback (seharusnya tidak terjadi jika load benar)
             currentPermissions.value.push({ name: permName, allowed: checkAll })
        }
    })
}

// Functions
const addRole = () => {
    router.post('/role-permission', {
        name: newRole.name,
        description: newRole.description
    }, {
        onSuccess: () => {
            closeAddRoleModal()
        }
    })
}

const closeAddRoleModal = () => {
    newRole.name = ''
    newRole.description = ''
    showAddRoleModal.value = false
}

const deleteRole = (roleId: number) => {
    const role = props.roles.find(r => r.id === roleId)
    if (role && confirm(`Apakah Anda yakin ingin menghapus role "${role.name}"?`)) {
        router.delete(`/role-permission/${roleId}`)
    }
}

const openPermissionModal = (role: Role) => {
    selectedRole.value = role
    loadRolePermissions(role)
    showPermissionModal.value = true
}

const closePermissionModal = () => {
    showPermissionModal.value = false
    selectedRole.value = null
    currentPermissions.value = []
}

const loadRolePermissions = (role: Role) => {
    // 1. Ambil semua master permission
    const initialPermissions: PermissionState[] = props.allPermissions
        .flatMap(module => module.permissions)
        .map(p => ({
            name: p.name,
            allowed: false // Default
        }))
    
    // 2. Set allowed = true jika role punya permission tersebut
    const rolePermissionsMap = new Set(role.permissions) // array of permission_name
    
    currentPermissions.value = initialPermissions.map(p => ({
        ...p,
        allowed: rolePermissionsMap.has(p.name)
    }))
}

const hasPermission = (permissionName: string): boolean => {
    const perm = permissionMap.value.get(permissionName)
    return perm?.allowed ?? false
}

const togglePermission = (permissionName: string, event: Event) => {
    const target = event.target as HTMLInputElement
    const allowed = target.checked
    
    const existingPerm = currentPermissions.value.find(p => p.name === permissionName)
    
    if (existingPerm) {
        existingPerm.allowed = allowed;
    }
}

const getPermissionName = (module: string, action: string): string => {
    // Menangani kasus khusus Central Data yang memiliki prefix terpisah di DB
    if (module === 'central_data') {
        // Contoh: central_data.role_management_view
        return `central_data.${action}`; 
    } 
    // Menangani Master Data (sku_management, supplier_management, dll.)
    if (module.endsWith('_management')) {
        return `central_data.${module}_${action}`; 
    }

    // Default: format module.action (incoming.view, putaway.create, dll.)
    return `${module}.${action}`;
}

const savePermissions = () => {
    if (!selectedRole.value) return
    
    isSaving.value = true
    
    // TIDAK PERLU lagi filter yang allowed: true, karena di Controller sudah di-filter
    router.put(`/role-permission/${selectedRole.value.id}/permissions`, {
        permissions: currentPermissions.value
    }, {
        onSuccess: () => {
            closePermissionModal()
            // Penting: Refresh halaman setelah sukses agar tabel utama terupdate
            router.reload({ only: ['roles'] }); 
        },
        onError: (errors) => {
             console.error("Gagal menyimpan permissions:", errors);
             alert("Terjadi kesalahan saat menyimpan permissions. Cek console untuk detail.");
        }
    })
}

// Permission CRUD Functions
const addPermission = () => {
    router.post('/role-permission/permissions', {
        module: newPermission.module,
        action: newPermission.action,
        description: newPermission.description
    }, {
        onSuccess: () => {
            newPermission.module = ''
            newPermission.action = ''
            newPermission.description = ''
            router.reload({ only: ['allPermissions'] });
        }
    })
}

const openEditPermissionModal = (permission: AllPermissionItem, moduleKey: string) => {
    editingPermission.id = permission.id
    editingPermission.module = moduleKey
    editingPermission.action = permission.display_name.toLowerCase().replace(/ /g, '_')
    editingPermission.description = permission.description
    showEditPermissionModal.value = true
}

const updatePermission = () => {
    router.put(`/role-permission/permissions/${editingPermission.id}`, {
        module: editingPermission.module,
        action: editingPermission.action,
        description: editingPermission.description
    }, {
        onSuccess: () => {
            showEditPermissionModal.value = false
            router.reload({ only: ['allPermissions'] });
        }
    })
}

const deletePermission = (permissionId: number, permissionName: string) => {
    if (confirm(`Apakah Anda yakin ingin menghapus permission "${permissionName}"? Permission ini akan dihapus dari semua role.`)) {
        router.delete(`/role-permission/permissions/${permissionId}`, {
            onSuccess: () => {
                router.reload({ only: ['allPermissions', 'roles'] });
            }
        })
    }
}

const getModuleCount = (permissions: string[]): number => {
    const uniqueModules = new Set<string>()
    props.allPermissions.forEach(moduleData => {
        const modulePermissions = new Set(moduleData.permissions.map(p => p.name))
        const hasPermissionInModule = permissions.some(p => modulePermissions.has(p))
        if (hasPermissionInModule) {
            uniqueModules.add(moduleData.module_key)
        }
    })
    return uniqueModules.size
}

// Helper functions
const capitalizeFirst = (str: string): string => {
    return str.charAt(0).toUpperCase() + str.slice(1)
}

const formatActionName = (action: string): string => {
    return action
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ')
}

const formatModuleName = (module: string): string => {
    return module
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ')
}
</script>

<style scoped>
.border-l-4 {
    border-left-width: 4px;
}
</style>