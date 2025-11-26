<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    cycleCount: Object,
});

const form = useForm({
    items: props.cycleCount.items.map(item => ({
        id: item.id,
        physical_qty: item.physical_qty,
    })),
});

const saveCounts = () => {
    form.put(route('cycle-count.update', props.cycleCount.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: show toast
        }
    });
};

const finalizeForm = useForm({});

const finalizeCount = () => {
    if (confirm('Are you sure you want to finalize this cycle count? Inventory adjustments will be made for any discrepancies.')) {
        finalizeForm.post(route('cycle-count.finalize', props.cycleCount.id));
    }
};

const getStatusClass = (status) => {
    switch (status) {
        case 'matched': return 'text-green-600 font-semibold';
        case 'discrepancy': return 'text-red-600 font-bold';
        default: return 'text-gray-500';
    }
};

const isCompleted = computed(() => props.cycleCount.status === 'completed');
</script>

<template>
    <AppLayout pageTitle="Cycle Count Details" pageDescription="Perform counting and review">
        <div class="min-h-screen bg-gray-50 p-6">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <div class="flex items-center gap-3">
                        <Link :href="route('cycle-count.index')" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </Link>
                        <h1 class="text-2xl font-bold text-gray-900">{{ cycleCount.reference_number }}</h1>
                        <span :class="['px-2 py-1 text-xs rounded-full font-semibold uppercase', 
                            cycleCount.status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800']">
                            {{ cycleCount.status }}
                        </span>
                    </div>
                    <p class="text-gray-600 mt-1 ml-8">
                        Warehouse: {{ cycleCount.warehouse?.name }} | Assigned: {{ cycleCount.user?.name }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <button v-if="!isCompleted" @click="saveCounts" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 flex items-center gap-2" :disabled="form.processing">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Save Progress
                    </button>
                    <button v-if="!isCompleted" @click="finalizeCount" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2" :disabled="finalizeForm.processing">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Finalize & Adjust
                    </button>
                </div>
            </div>

            <!-- Counting Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bin Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch/Lot</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">System Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Physical Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difference</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(item, index) in cycleCount.items" :key="item.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ item.bin?.bin_code }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ item.material?.nama_material }} <span class="text-xs text-gray-400">({{ item.material?.kode_item }})</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ item.batch_lot || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ parseFloat(item.system_qty) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input 
                                        v-if="!isCompleted"
                                        v-model="form.items[index].physical_qty" 
                                        type="number" 
                                        step="0.01"
                                        class="w-32 px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                        placeholder="Enter Qty"
                                    >
                                    <span v-else class="font-bold">{{ parseFloat(item.physical_qty) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span v-if="form.items[index].physical_qty !== null && form.items[index].physical_qty !== ''">
                                        {{ (parseFloat(form.items[index].physical_qty) - parseFloat(item.system_qty)).toFixed(2) }}
                                    </span>
                                    <span v-else>-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span :class="getStatusClass(item.status)">
                                        {{ item.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
