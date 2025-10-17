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
                                    Edit Permission
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
                        <!-- Incoming / Receipt -->
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                                <span class="mr-2">📥</span>
                                Incoming / Receipt
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                                <label v-for="action in ['view', 'create', 'edit', 'delete', 'approve']" :key="`incoming-${action}`" class="flex items-center text-gray-700">
                                    <input 
                                        type="checkbox" 
                                        :checked="hasPermission('incoming', action)"
                                        @change="togglePermission('incoming', action, $event)"
                                        class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    {{ capitalizeFirst(action) }}
                                </label>
                            </div>
                        </div>

                        <!-- Quality Control -->
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                                <span class="mr-2">🔍</span>
                                Quality Control
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <label v-for="action in ['view', 'input_qc_result', 'approve', 'reject']" :key="`qc-${action}`" class="flex items-center text-gray-700">
                                    <input 
                                        type="checkbox" 
                                        :checked="hasPermission('qc', action)"
                                        @change="togglePermission('qc', action, $event)"
                                        class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    {{ formatActionName(action) }}
                                </label>
                            </div>
                        </div>

                        <!-- Label Karantina -->
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                                <span class="mr-2">🏷️</span>
                                Label Karantina
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <label v-for="action in ['view', 'cetak_label', 'release', 'reject']" :key="`label-${action}`" class="flex items-center text-gray-700">
                                    <input 
                                        type="checkbox" 
                                        :checked="hasPermission('label_karantina', action)"
                                        @change="togglePermission('label_karantina', action, $event)"
                                        class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    {{ formatActionName(action) }}
                                </label>
                            </div>
                        </div>

                        <!-- Putaway & Transfer Order -->
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                                <span class="mr-2">📦</span>
                                Putaway & Transfer Order
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                <label v-for="action in ['view', 'kerjakan_to', 'cetak_slip']" :key="`putaway-${action}`" class="flex items-center text-gray-700">
                                    <input 
                                        type="checkbox" 
                                        :checked="hasPermission('putaway', action)"
                                        @change="togglePermission('putaway', action, $event)"
                                        class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    {{ formatActionName(action) }}
                                </label>
                            </div>
                        </div>

                        <!-- Reservation -->
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                                <span class="mr-2">📋</span>
                                Reservation (FOH, RS, RM, Packaging, ADD)
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <label v-for="action in ['view', 'create_request', 'approve_request', 'cetak_form']" :key="`reservation-${action}`" class="flex items-center text-gray-700">
                                    <input 
                                        type="checkbox" 
                                        :checked="hasPermission('reservation', action)"
                                        @change="togglePermission('reservation', action, $event)"
                                        class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    {{ formatActionName(action) }}
                                </label>
                            </div>
                        </div>

                        <!-- Picking -->
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                                <span class="mr-2">🛒</span>
                                Picking
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                <label v-for="action in ['view', 'kerjakan_picking', 'cetak_picking_list']" :key="`picking-${action}`" class="flex items-center text-gray-700">
                                    <input 
                                        type="checkbox" 
                                        :checked="hasPermission('picking', action)"
                                        @change="togglePermission('picking', action, $event)"
                                        class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    {{ formatActionName(action) }}
                                </label>
                            </div>
                        </div>

                        <!-- Return -->
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                                <span class="mr-2">↩️</span>
                                Return (Supplier / Production)
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <label v-for="action in ['view', 'create_return', 'approve_return', 'cetak_slip']" :key="`return-${action}`" class="flex items-center text-gray-700">
                                    <input 
                                        type="checkbox" 
                                        :checked="hasPermission('return', action)"
                                        @change="togglePermission('return', action, $event)"
                                        class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    {{ formatActionName(action) }}
                                </label>
                            </div>
                        </div>

                        <!-- Central Data -->
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                                <span class="mr-2">⚙️</span>
                                Central Data
                            </h4>
                            <div class="space-y-3">
                                <div v-for="module in ['sku_management', 'supplier_management', 'bin_management', 'user_management', 'role_management']" 
                                     :key="module" 
                                     class="border-l-4 border-gray-300 pl-4">
                                    <h5 class="font-medium text-gray-700 mb-2">{{ formatModuleName(module) }}</h5>
                                    <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                                        <label v-for="action in ['view', 'create', 'edit', 'delete', 'admin']" :key="`${module}-${action}`" class="flex items-center text-sm text-gray-700">
                                            <input 
                                                type="checkbox" 
                                                :checked="hasPermission('central_data', `${module}_${action}`)"
                                                @change="togglePermission('central_data', `${module}_${action}`, $event)"
                                                class="mr-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                            >
                                            {{ capitalizeFirst(action) }}
                                        </label>
                                    </div>
                                </div>
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
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'

interface Role {
    id: number
    name: string
    description: string
    userCount: number
    permissions: Permission[]
}

interface Permission {
    id: number
    module: string
    action: string
    permission_name: string
}

interface PermissionState {
    module: string
    action: string
    allowed: boolean
}

// Props
const props = defineProps<{
    roles: Role[]
    allPermissions: any
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
    currentPermissions.value = role.permissions.map(p => ({
        module: p.module,
        action: p.action,
        allowed: true
    }))
}

const hasPermission = (module: string, action: string): boolean => {
    return currentPermissions.value.some(p => 
        p.module === module && 
        p.action === action && 
        p.allowed
    )
}

const togglePermission = (module: string, action: string, event: Event) => {
    const target = event.target as HTMLInputElement
    const allowed = target.checked
    
    const existingIndex = currentPermissions.value.findIndex(
        p => p.module === module && p.action === action
    )
    
    if (existingIndex >= 0) {
        currentPermissions.value[existingIndex].allowed = allowed
    } else {
        currentPermissions.value.push({ module, action, allowed })
    }
}

const savePermissions = () => {
    if (!selectedRole.value) return
    
    router.put(`/role-permission/${selectedRole.value.id}/permissions`, {
        permissions: currentPermissions.value
    }, {
        onSuccess: () => {
            closePermissionModal()
        }
    })
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