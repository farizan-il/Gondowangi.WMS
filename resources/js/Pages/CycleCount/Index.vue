<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    cycleCounts: Object,
});

const getStatusClass = (status) => {
    switch (status) {
        case 'draft': return 'bg-gray-100 text-gray-800';
        case 'in_progress': return 'bg-blue-100 text-blue-800';
        case 'completed': return 'bg-green-100 text-green-800';
        case 'cancelled': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <AppLayout pageTitle="Cycle Count" pageDescription="Manage inventory cycle counts">
        <div class="min-h-screen bg-gray-50 p-6">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Cycle Count</h1>
                    <p class="text-gray-600 mt-1">Manage inventory counting sessions</p>
                </div>
                <Link :href="route('cycle-count.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Cycle Count
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Started At</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="count in cycleCounts.data" :key="count.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                    {{ count.reference_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    {{ count.warehouse?.name || 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    {{ count.user?.name || 'Unassigned' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusClass(count.status)]">
                                        {{ count.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    {{ count.started_at ? new Date(count.started_at).toLocaleDateString() : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <Link :href="route('cycle-count.show', count.id)" class="text-blue-600 hover:text-blue-900">View</Link>
                                </td>
                            </tr>
                            <tr v-if="cycleCounts.data.length === 0">
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No cycle counts found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination could go here -->
            </div>
        </div>
    </AppLayout>
</template>
