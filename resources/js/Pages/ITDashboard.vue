<template>
  <AppLayout title="IT Admin Dashboard">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">IT Admin Dashboard</h2>
          <p class="text-sm text-gray-600">System Monitoring & Statistics</p>
        </div>
        <div class="text-sm text-gray-500">
          Last updated: {{ new Date().toLocaleTimeString() }}
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Activities Today -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <div class="flex items-center justify-between mb-4">
            <div class="bg-blue-100 p-3 rounded-lg">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
            </div>
            <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">+{{ stats.total_today }} Today</span>
          </div>
          <h3 class="text-3xl font-bold text-gray-900">{{ stats.total_today }}</h3>
          <p class="text-sm text-gray-500 mt-1">Total Activities Today</p>
        </div>

        <!-- Active Users -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <div class="flex items-center justify-between mb-4">
            <div class="bg-purple-100 p-3 rounded-lg">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
            <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-1 rounded-full">Active Now</span>
          </div>
          <h3 class="text-3xl font-bold text-gray-900">{{ activeUsers }}</h3>
          <p class="text-sm text-gray-500 mt-1">Unique Users Today</p>
        </div>

        <!-- Weekly Volume -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <div class="flex items-center justify-between mb-4">
            <div class="bg-orange-100 p-3 rounded-lg">
              <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <span class="text-xs font-medium text-gray-600 bg-gray-50 px-2 py-1 rounded-full">Weekly</span>
          </div>
          <h3 class="text-3xl font-bold text-gray-900">{{ stats.total_week }}</h3>
          <p class="text-sm text-gray-500 mt-1">Activities This Week</p>
        </div>

        <!-- Monthly Volume -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <div class="flex items-center justify-between mb-4">
            <div class="bg-green-100 p-3 rounded-lg">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <span class="text-xs font-medium text-gray-600 bg-gray-50 px-2 py-1 rounded-full">Monthly</span>
          </div>
          <h3 class="text-3xl font-bold text-gray-900">{{ stats.total_month }}</h3>
          <p class="text-sm text-gray-500 mt-1">Activities This Month</p>
        </div>
      </div>

      <!-- Charts Row 1 -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Hourly Activity Chart -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Hourly Activity (Last 24h)</h3>
          <div class="h-64">
            <Line :data="hourlyChartData" :options="chartOptions" />
          </div>
        </div>

        <!-- Module Distribution Chart -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
          <h3 class="text-lg font-bold text-gray-900 mb-4">System Usage by Module</h3>
          <div class="h-64 flex justify-center">
            <Doughnut :data="moduleChartData" :options="doughnutOptions" />
          </div>
        </div>
      </div>

      <!-- Charts Row 2 -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Users Chart -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Top 5 Most Active Users</h3>
          <div class="h-64">
            <Bar :data="userChartData" :options="barOptions" />
          </div>
        </div>

        <!-- System Health / Quick Actions -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
          <h3 class="text-lg font-bold text-gray-900 mb-4">System Health & Actions</h3>
          <div class="space-y-4">
            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-100">
              <div class="flex items-center gap-3">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="font-medium text-green-900">Database Connection</span>
              </div>
              <span class="text-sm text-green-700">Healthy</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-100">
              <div class="flex items-center gap-3">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="font-medium text-green-900">Queue Worker</span>
              </div>
              <span class="text-sm text-green-700">Running</span>
            </div>
            
            <div class="pt-4 border-t border-gray-100">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Quick Links</h4>
                <div class="flex gap-3">
                    <Link href="/activity-log" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">View Full Logs</Link>
                    <span class="text-gray-300">|</span>
                    <Link href="/master-data" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">Manage Master Data</Link>
                </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Online Users & recent Activities Row -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Online Users -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Online Users</h3>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full animate-pulse">
                    {{ onlineUsers.length }} Active
                </span>
            </div>
            
            <div v-if="onlineUsers.length > 0" class="space-y-4 max-h-96 overflow-y-auto">
                <div v-for="user in onlineUsers" :key="user.id" class="flex items-center space-x-3 p-2 hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="relative">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                            {{ user.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                        <p class="text-xs text-gray-500">{{ user.email }}</p>
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500 text-sm">
                No users currently online.
            </div>
        </div>

        <!-- Recent Activities Feed -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
             <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Recent Activity Feed</h3>
                <Link href="/activity-log" class="text-sm text-blue-600 hover:text-blue-800 hover:underline">View All</Link>
            </div>

            <div class="space-y-4">
                <div v-for="activity in recentActivities" :key="activity.id + activity.module" class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
                    <div class="flex-shrink-0 mt-1">
                        <span v-if="activity.module === 'Incoming'" class="bg-blue-100 text-blue-600 p-1.5 rounded-lg text-xs">IN</span>
                        <span v-else-if="activity.module === 'QC'" class="bg-purple-100 text-purple-600 p-1.5 rounded-lg text-xs">QC</span>
                        <span v-else-if="activity.module === 'Stock Movement'" class="bg-orange-100 text-orange-600 p-1.5 rounded-lg text-xs">MV</span>
                        <span v-else-if="activity.module === 'Master Data'" class="bg-gray-200 text-gray-700 p-1.5 rounded-lg text-xs">MD</span>
                        <span v-else class="bg-gray-100 text-gray-600 p-1.5 rounded-lg text-xs">{{ activity.module.substring(0,2).toUpperCase() }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">
                            <span class="font-bold">{{ activity.user }}</span> 
                            <span class="font-normal text-gray-600"> performed </span>
                            <span class="text-blue-600">{{ activity.action }}</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5 truncate">{{ activity.description }}</p>
                    </div>
                    <div class="text-xs text-gray-400 whitespace-nowrap">
                        {{ activity.created_at }}
                    </div>
                </div>
                
                <div v-if="recentActivities.length === 0" class="text-center py-8 text-gray-500">
                    No recent activities found.
                </div>
            </div>
        </div>
      </div>
      
      </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  ArcElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'
import { Line, Bar, Doughnut } from 'vue-chartjs'

// Register ChartJS components
ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  ArcElement,
  Title,
  Tooltip,
  Legend
)

const props = defineProps({
  stats: Object,
  activeUsers: Number,
  onlineUsers: Array,
  moduleStats: Object,
  hourlyStats: Array,
  topUsers: Array,
  recentActivities: Array
})

// ... chart configs ...

// Chart Data Configuration

// 1. Hourly Line Chart
const hourlyChartData = computed(() => ({
  labels: Array.from({ length: 24 }, (_, i) => `${i}:00`),
  datasets: [{
    label: 'Activities',
    data: props.hourlyStats,
    borderColor: '#3B82F6',
    backgroundColor: 'rgba(59, 130, 246, 0.1)',
    tension: 0.4,
    fill: true
  }]
}))

// 2. Module Doughnut Chart
const moduleChartData = computed(() => ({
  labels: Object.keys(props.moduleStats),
  datasets: [{
    data: Object.values(props.moduleStats),
    backgroundColor: [
      '#3B82F6', // Blue
      '#F59E0B', // Amber
      '#10B981', // Emerald
      '#EF4444', // Red
      '#8B5CF6', // Violet
      '#EC4899', // Pink
      '#6B7280'  // Gray
    ]
  }]
}))

// 3. Top Users Bar Chart
const userChartData = computed(() => ({
  labels: props.topUsers.map(u => u.name),
  datasets: [{
    label: 'Total Actions',
    data: props.topUsers.map(u => u.count),
    backgroundColor: '#8B5CF6',
    borderRadius: 6
  }]
}))

// Chart Options
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: {
        display: true,
        borderDash: [2, 2]
      }
    },
    x: {
      grid: {
        display: false
      }
    }
  }
}

const doughnutOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'right'
    }
  }
}

const barOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    }
  },
  scales: {
    y: {
      beginAtZero: true
    },
    x: {
      grid: {
        display: false
      }
    }
  }
}
</script>
