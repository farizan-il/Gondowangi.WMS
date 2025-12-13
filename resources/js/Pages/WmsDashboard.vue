<template>
  <AppLayout pageTitle="WMS Dashboard" pageDescription="Overview of warehouse key metrics">
    <div class="min-h-screen bg-gray-50 p-6">
      
      <!-- Date Filter -->
      <div class="mb-6 bg-white rounded-lg shadow p-4 flex items-end gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
          <input type="date" v-model="filters.date_start" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
          <input type="date" v-model="filters.date_end" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
        </div>
        <button @click="fetchData" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
          Filter
        </button>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <!-- Total Incoming -->
        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-500">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm font-medium text-gray-500">Total Incoming</p>
              <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ metrics.totalIncoming }}</h3>
            </div>
            <div class="p-2 bg-blue-50 rounded-lg">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
            </div>
          </div>
          <p class="text-xs text-gray-400 mt-2">Receiving Documents</p>
        </div>

        <!-- Total Outgoing -->
        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm font-medium text-gray-500">Total Outgoing</p>
              <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ metrics.totalOutgoing }}</h3>
            </div>
             <div class="p-2 bg-green-50 rounded-lg">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
            </div>
          </div>
          <p class="text-xs text-gray-400 mt-2">Completed Picking Tasks</p>
        </div>

        <!-- Lead Time -->
        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-purple-500">
           <div class="flex justify-between items-start">
            <div>
              <p class="text-sm font-medium text-gray-500">Picking Lead Time</p>
              <h3 class="text-lg font-bold text-gray-900 mt-1">{{ metrics.leadTime }}</h3>
            </div>
             <div class="p-2 bg-purple-50 rounded-lg">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
          </div>
          <p class="text-xs text-gray-400 mt-2">Start Pick - End Pick</p>
        </div>

        <!-- Stock Accuracy -->
        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-yellow-500">
           <div class="flex justify-between items-start">
            <div>
              <p class="text-sm font-medium text-gray-500">Stock Accuracy</p>
              <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ metrics.stockAccuracy }}%</h3>
            </div>
             <div class="p-2 bg-yellow-50 rounded-lg">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
          </div>
          <p class="text-xs text-gray-400 mt-2">Based on Cycle Counts</p>
        </div>

        <!-- SKU On Hand -->
         <div class="bg-white rounded-xl shadow p-5 border-l-4 border-indigo-500">
           <div class="flex justify-between items-start">
            <div>
              <p class="text-sm font-medium text-gray-500">SKU On Hand</p>
              <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ metrics.skuOnHand }}</h3>
            </div>
             <div class="p-2 bg-indigo-50 rounded-lg">
              <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
            </div>
          </div>
          <p class="text-xs text-gray-400 mt-2">Distinct Items with Stock > 0</p>
        </div>
      </div>

      <!-- Detailed Metrics Charts -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        
        <!-- Incoming Trend -->
        <div class="bg-white p-5 rounded-xl shadow">
           <h4 class="text-sm font-semibold text-gray-600 mb-2">Incoming Trend (Daily)</h4>
           <div class="h-48 relative">
              <Line v-if="chartData.trends.incoming.labels.length" :data="chartData.trends.incoming" :options="lineChartOptions" />
              <div v-else class="flex h-full items-center justify-center text-gray-400 text-xs">No Data</div>
           </div>
        </div>

        <!-- Outgoing Trend -->
        <div class="bg-white p-5 rounded-xl shadow">
           <h4 class="text-sm font-semibold text-gray-600 mb-2">Outgoing Trend (Daily)</h4>
           <div class="h-48 relative">
              <Line v-if="chartData.trends.outgoing.labels.length" :data="chartData.trends.outgoing" :options="lineChartOptions" />
              <div v-else class="flex h-full items-center justify-center text-gray-400 text-xs">No Data</div>
           </div>
        </div>

        <!-- Lead Time Trend -->
        <div class="bg-white p-5 rounded-xl shadow">
           <h4 class="text-sm font-semibold text-gray-600 mb-2">Lead Time Trend (Avg Mins)</h4>
           <div class="h-48 relative">
              <Bar v-if="chartData.trends.leadTime.labels.length" :data="chartData.trends.leadTime" :options="verticalBarOptions" />
              <div v-else class="flex h-full items-center justify-center text-gray-400 text-xs">No Data</div>
           </div>
        </div>

        <!-- Stock Accuracy Trend -->
        <div class="bg-white p-5 rounded-xl shadow">
           <h4 class="text-sm font-semibold text-gray-600 mb-2">Stock Accuracy Trend (%)</h4>
           <div class="h-48 relative">
              <Line v-if="chartData.trends.accuracy.labels.length" :data="chartData.trends.accuracy" :options="lineChartOptions" />
              <div v-else class="flex h-full items-center justify-center text-gray-400 text-xs">No Data</div>
           </div>
        </div>
      </div>

       <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Incoming Breakdown Chart (Horizontal Bar) -->
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Incoming by Category</h3>
          <div class="h-64 relative">
             <Bar v-if="chartData.incoming.labels.length" :data="chartData.incoming" :options="barChartOptions" />
             <div v-else class="flex h-full items-center justify-center text-gray-400">No Data Available</div>
          </div>
        </div>

        <!-- RM vs PM Comparison (Pie) -->
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Material Breakdown (RM vs PM)</h3>
          <div class="h-64 relative">
             <Pie v-if="chartData.typeBreakdown.labels.length" :data="chartData.typeBreakdown" :options="chartOptions" />
             <div v-else class="flex h-full items-center justify-center text-gray-400">No Data Available</div>
          </div>
        </div>
      </div>

       <!-- Detailed Material List Table (Below Charts) -->
       <div class="bg-white p-6 rounded-lg shadow">
          <div class="flex justify-between items-center mb-4">
             <h3 class="text-lg font-semibold text-gray-800">Incoming By Category Details</h3>
             <select v-model="sortOrder" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
               <option value="high">Highest First</option>
               <option value="low">Lowest First</option>
             </select>
          </div>
          
          <div class="overflow-y-auto max-h-64">
             <table class="min-w-full divide-y divide-gray-200">
               <thead class="bg-gray-50">
                 <tr>
                   <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                   <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Items</th>
                 </tr>
               </thead>
               <tbody class="bg-white divide-y divide-gray-200">
                 <tr v-for="(item, index) in sortedCategoryData" :key="index" class="hover:bg-gray-50">
                   <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.kategori || 'Uncategorized' }}</td>
                   <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ item.total }}</td>
                 </tr>
                 <tr v-if="sortedCategoryData.length === 0">
                    <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">No data found</td>
                 </tr>
               </tbody>
             </table>
          </div>
        </div>

    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted, reactive, computed } from 'vue';
import axios from 'axios';
import { Chart as ChartJS, ArcElement, Tooltip, Legend, BarElement, CategoryScale, LinearScale, PointElement, LineElement } from 'chart.js'
import { Pie, Bar, Line } from 'vue-chartjs'

ChartJS.register(ArcElement, Tooltip, Legend, BarElement, CategoryScale, LinearScale, PointElement, LineElement)

const filters = reactive({
  date_start: new Date().toISOString().slice(0, 8) + '01', // First day of current month
  date_end: new Date().toISOString().slice(0, 10) // Today
});

const metrics = ref({
  totalIncoming: 0,
  totalOutgoing: 0,
  leadTime: '0 Mins',
  stockAccuracy: 0,
  skuOnHand: 0
});

const rawCategoryData = ref([]);
const sortOrder = ref('high');

const chartData = ref({
  incoming: { labels: [], datasets: [] },
  typeBreakdown: { labels: [], datasets: [] },
  trends: {
    incoming: { labels: [], datasets: [] },
    outgoing: { labels: [], datasets: [] },
    leadTime: { labels: [], datasets: [] },
    accuracy: { labels: [], datasets: [] },
  }
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false
}

const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  indexAxis: 'y', // Horizontal Bar
}

const verticalBarOptions = {
    responsive: true,
    maintainAspectRatio: false,
}

const lineChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    tension: 0.3, // Curve
    scales: {
        y: { beginAtZero: true }
    }
}

const fetchData = async () => {
  try {
    const response = await axios.get(route('wms-dashboard.data'), {
      params: filters
    });
    
    const data = response.data;
    
    // Update Metrics
    metrics.value.totalIncoming = data.totalIncoming;
    metrics.value.totalOutgoing = data.totalOutgoing;
    metrics.value.leadTime = data.leadTime;
    metrics.value.stockAccuracy = data.stockAccuracy;
    metrics.value.skuOnHand = data.skuOnHand;

    // Save Raw Category Data
    rawCategoryData.value = data.incomingByCategory;

    // Update Existing Charts (Main)
    const labels = data.incomingByCategory.map(item => item.kategori || 'Uncategorized');
    const values = data.incomingByCategory.map(item => item.total);
    
    chartData.value.incoming = {
      labels: labels,
      datasets: [{
        label: 'Total Items',
        backgroundColor: '#3B82F6', 
        data: values
      }]
    };

    if (data.incomingByType) {
       const typeLabels = data.incomingByType.map(item => item.kategori || 'Other');
       const typeValues = data.incomingByType.map(item => item.total);
       
       chartData.value.typeBreakdown = {
         labels: typeLabels,
         datasets: [{
           backgroundColor: ['#F59E0B', '#10B981', '#3B82F6', '#EF4444'], 
           data: typeValues
         }]
       };
    }

    // --- POPULATE TREND CHARTS ---
    if (data.trends) {
        // Helper to map trend
        const mapTrend = (trendData, label, color) => ({
            labels: trendData.map(d => d.date),
            datasets: [{
                label: label,
                borderColor: color,
                backgroundColor: color,
                data: trendData.map(d => d.value),
                fill: false
            }]
        });

        const mapBar = (trendData, label, color) => ({
             labels: trendData.map(d => d.date),
             datasets: [{
                 label: label,
                 backgroundColor: color,
                 data: trendData.map(d => d.value)
             }]
        });

        chartData.value.trends.incoming = mapTrend(data.trends.incoming, 'Incoming Items', '#3B82F6');
        chartData.value.trends.outgoing = mapTrend(data.trends.outgoing, 'Completed Picking', '#10B981'); // Green
        chartData.value.trends.leadTime = mapBar(data.trends.leadTime, 'Avg Minutes', '#8B5CF6'); // Purple
        chartData.value.trends.accuracy = mapTrend(data.trends.accuracy, 'Accuracy %', '#F59E0B'); // Yellow
    }
    
  } catch (error) {
    console.error("Failed to fetch dashboard data", error);
  }
};

const sortedCategoryData = computed(() => {
  return [...rawCategoryData.value].sort((a, b) => {
     if (sortOrder.value === 'high') {
         return b.total - a.total;
     } else {
         return a.total - b.total;
     }
  });
});

onMounted(() => {
  fetchData();
});

</script>
