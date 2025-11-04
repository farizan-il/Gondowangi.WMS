<template>
    <AppLayout title="Role & Permission Management">
        <div class="role-permission-management">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Role & Permission Management</h1>
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
        <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Atur Permission - {{ selectedRole?.name }}</h3>
                <button @click="closePermissionModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="space-y-6">
                <div 
                    v-for="moduleData in allPermissions" 
                    :key="moduleData.module_key" 
                    class="border border-gray-200 rounded-lg p-4 bg-gray-50"
                >
                    <div class="flex justify-between items-center mb-3 border-b border-gray-200 pb-2">
                        <h4 class="text-md font-semibold text-gray-800 flex items-center">
                            <span v-html="moduleData.module_name"></span>
                        </h4>

                        <label class="flex items-center text-blue-600 font-medium cursor-pointer">
                            <input
                                type="checkbox"
                                :checked="isModuleAllSelected(moduleData.module_key)"
                                @change="toggleSelectAllModule(moduleData.module_key, $event)"
                                class="mr-2 rounded border-blue-600 text-blue-600 focus:ring-blue-500 h-4 w-4"
                            >
                            Pilih Semua
                        </label>
                        </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                        <label 
                            v-for="permission in moduleData.permissions" 
                            :key="permission.name" 
                            class="flex items-center text-gray-700 cursor-pointer" 
                            :title="permission.description"
                        >
                            <input 
                                type="checkbox" 
                                :checked="hasPermission(permission.name)"
                                @change="togglePermission(permission.name, $event)"
                                class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >
                            <span class="text-sm">{{ permission.display_name }}</span>
                        </label>
                    </div>
                </div>
           
            </div>
                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                        <button 
                            @click="closePermissionModal"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors"
                        >
                            Batal
                        </button>
                        <button 
                            @click="savePermissions"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
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
const selectedRole = ref<Role | null>(null)

const newRole = reactive({
    name: '',
    description: ''
})

const currentPermissions = ref<PermissionState[]>([])

const permissionMap = computed<Map<string, PermissionState>>(() => {
    return new Map(currentPermissions.value.map(p => [p.name, p]));
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

    module.permissions.forEach(permission => {
        const permName = permission.name
        const existingPerm = currentPermissions.value.find(p => p.name === permName)

        if (existingPerm) {
            existingPerm.allowed = checkAll
        } else if (checkAll) {
            // Jika belum ada di state (seharusnya tidak terjadi jika loadRolePermissions sudah benar), 
            // tambahkan sebagai allowed: true
            currentPermissions.value.push({ name: permName, allowed: true })
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
    const initialPermissions: PermissionState[] = props.allPermissions
        .flatMap(module => module.permissions)
        .map(p => ({
            name: p.name,
            allowed: false // Default ke false
        }))
    
    // Override status allowed berdasarkan permission yang dimiliki role
    const rolePermissionsMap = new Set(role.permissions) // permissions dari role sekarang adalah array of string
    
    currentPermissions.value = initialPermissions.map(p => ({
        ...p,
        allowed: rolePermissionsMap.has(p.name) // Cek apakah role memiliki permission ini
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
    } else {
        currentPermissions.value.push({ name: permissionName, allowed: allowed });
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