<template>
  <AppLayout title="PutAway & TO">
    <div class="min-h-screen bg-gray-50 p-6">
      <!-- Header -->
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Scan-Based Putaway</h1>
        <p class="text-gray-600">Scan QR codes to process putaway transfers.</p>
      </div>

      <!-- Scan Form -->
      <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Scan Quarantine Bin</label>
            <input v-model="scan.quarantine_qr" type="text" placeholder="Scan QR Karantina..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Scan Material</label>
            <input v-model="scan.material_qr" type="text" placeholder="Scan QR Material..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Scan Destination Bin</label>
            <input v-model="scan.destination_qr" type="text" placeholder="Scan QR Tujuan..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
        </div>
        <div class="mt-4">
          <button @click="processScan"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
            Process Transfer
          </button>
        </div>
      </div>

      <!-- Transfer Orders Table -->
      <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">Daftar Transfer Order</h2>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TO Number</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creation Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="to in transferOrders" :key="to.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">{{ to.toNumber }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(to.creationDate) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ to.warehouse }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ to.type }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ to.status }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ to.items.length }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import axios from 'axios'

interface TransferOrder {
  id: string
  toNumber: string
  creationDate: Date | string
  warehouse: string
  type: string
  status: 'Pending' | 'In Progress' | 'Completed'
  items: any[]
}

const page = usePage()
const transferOrders = ref<TransferOrder[]>((page.props.transferOrders as TransferOrder[]) || [])
const scan = ref({
  quarantine_qr: '',
  material_qr: '',
  destination_qr: ''
})

const processScan = async () => {
  // Frontend validation
  if (!scan.value.quarantine_qr || !scan.value.material_qr || !scan.value.destination_qr) {
    alert('Harap isi semua kolom pemindaian sebelum melanjutkan.');
    return;
  }

  try {
    const response = await axios.post('/transaction/putaway-transfer/scan', scan.value);
    if (response.data.success) {
      alert(response.data.message);
      scan.value = { quarantine_qr: '', material_qr: '', destination_qr: '' };
      router.reload({ only: ['transferOrders'] });
    } else {
      alert(response.data.message || 'Terjadi kesalahan yang tidak diketahui.');
    }
  } catch (error) {
    console.error('Error processing scan:', error);
    if (error.response && error.response.status === 422) {
      // Handle validation errors from Laravel
      const errors = error.response.data.errors;
      let errorMessage = 'Gagal memproses pemindaian:\n';
      for (const key in errors) {
        errorMessage += `- ${errors[key][0]}\n`;
      }
      alert(errorMessage);
    } else if (error.response) {
      // Handle other server errors
      alert(`Gagal memproses pemindaian: ${error.response.data.message || 'Server error'}`);
    }
    else {
      // Handle network errors or other issues
      alert('Gagal memproses pemindaian. Periksa koneksi Anda.');
    }
  }
};

const formatDate = (date: Date | string) => {
  const dateObj = typeof date === 'string' ? new Date(date) : date
  if (isNaN(dateObj.getTime())) return 'Invalid Date'
  return new Intl.DateTimeFormat('id-ID', {
    year: 'numeric', month: '2-digit', day: '2-digit',
    hour: '2-digit', minute: '2-digit'
  }).format(dateObj)
}
</script>
