<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    zones: Array,
    bins: Array,
});

const form = useForm({
    reference_number: 'CC-' + new Date().toISOString().slice(0,10).replace(/-/g,'') + '-' + Math.floor(Math.random() * 1000),
    warehouse_id: 1, // Default to 1 for now, or select
    zone_id: '',
    bin_ids: [],
    notes: '',
});

const submit = () => {
    form.post(route('cycle-count.store'));
};
</script>

<template>
    <AppLayout pageTitle="New Cycle Count" pageDescription="Start a new counting session">
        <div class="min-h-screen bg-gray-50 p-6">
            <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Start New Cycle Count</h1>
                    <p class="text-gray-600">Select criteria to generate counting list</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reference Number</label>
                        <input v-model="form.reference_number" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        <div v-if="form.errors.reference_number" class="text-red-500 text-sm mt-1">{{ form.errors.reference_number }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Warehouse</label>
                        <select v-model="form.warehouse_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option :value="1">Main Warehouse</option>
                            <!-- Add more warehouses if available -->
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Zone (Optional)</label>
                        <select v-model="form.zone_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">All Zones</option>
                            <option v-for="zone in zones" :key="zone.id" :value="zone.id">{{ zone.zone_name }}</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Select a zone to count all bins in that zone.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea v-model="form.notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <Link :href="route('cycle-count.index')" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</Link>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg" :disabled="form.processing">
                            {{ form.processing ? 'Creating...' : 'Start Count' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
