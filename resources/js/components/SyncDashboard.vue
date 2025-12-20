<template>
    <div class="sync-dashboard p-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">لوحة تحكم المزامنة</h1>
                <p class="text-gray-600 mt-1">مراقبة وإدارة عمليات المزامنة Offline/Online</p>
            </div>
            
            <!-- أزرار التحكم -->
            <div class="flex gap-3">
                <button @click="refresh" class="btn btn-primary" :disabled="loading">
                    <i class="fas fa-sync-alt" :class="{ 'animate-spin': loading }"></i>
                    تحديث
                </button>
                <button @click="retryAllFailed" class="btn btn-warning" v-if="stats.total_failed > 0">
                    <i class="fas fa-redo"></i>
                    إعادة محاولة الكل
                </button>
                <button @click="showCleanupModal = true" class="btn btn-secondary">
                    <i class="fas fa-trash"></i>
                    تنظيف
                </button>
            </div>
        </div>

        <!-- حالة الاتصال -->
        <div class="mb-6 p-4 rounded-lg" :class="connectionStatusClass">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full animate-pulse" :class="connectionDotClass"></div>
                    <span class="font-semibold text-lg">{{ connectionStatusText }}</span>
                </div>
                <div class="text-sm" v-if="syncStatus.lastSyncTime">
                    آخر مزامنة: {{ formatTime(syncStatus.lastSyncTime) }}
                </div>
            </div>
        </div>

        <!-- إحصائيات سريعة -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- العمليات المعلقة -->
            <div class="stat-card bg-orange-50 border-orange-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-600 text-sm font-medium">العمليات المعلقة</p>
                        <p class="text-3xl font-bold text-orange-700 mt-1">{{ stats.total_pending || 0 }}</p>
                    </div>
                    <div class="stat-icon bg-orange-200">
                        <i class="fas fa-clock text-orange-600"></i>
                    </div>
                </div>
            </div>

            <!-- العمليات الفاشلة -->
            <div class="stat-card bg-red-50 border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-600 text-sm font-medium">العمليات الفاشلة</p>
                        <p class="text-3xl font-bold text-red-700 mt-1">{{ stats.total_failed || 0 }}</p>
                    </div>
                    <div class="stat-icon bg-red-200">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                </div>
            </div>

            <!-- العمليات المزامنة -->
            <div class="stat-card bg-green-50 border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-600 text-sm font-medium">تمت المزامنة</p>
                        <p class="text-3xl font-bold text-green-700 mt-1">{{ stats.total_synced || 0 }}</p>
                    </div>
                    <div class="stat-icon bg-green-200">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- معدل النجاح -->
            <div class="stat-card bg-blue-50 border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-600 text-sm font-medium">معدل النجاح</p>
                        <p class="text-3xl font-bold text-blue-700 mt-1">{{ stats.success_rate || 0 }}%</p>
                    </div>
                    <div class="stat-icon bg-blue-200">
                        <i class="fas fa-chart-line text-blue-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- الرسم البياني -->
        <div class="card mb-6" v-if="chartData.length">
            <div class="card-header">
                <h3 class="text-xl font-semibold">إحصائيات آخر 7 أيام</h3>
            </div>
            <div class="card-body">
                <canvas ref="chartCanvas" height="100"></canvas>
            </div>
        </div>

        <!-- Tabs -->
        <div class="card">
            <div class="tabs">
                <button 
                    v-for="tab in tabs" 
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    class="tab"
                    :class="{ 'active': activeTab === tab.id }"
                >
                    <i :class="tab.icon"></i>
                    {{ tab.label }}
                    <span v-if="tab.badge" class="badge">{{ tab.badge }}</span>
                </button>
            </div>

            <div class="card-body">
                <!-- Tab: العمليات المعلقة -->
                <div v-if="activeTab === 'pending'">
                    <pending-syncs-table 
                        :items="pendingItems" 
                        @retry="retrySync"
                        @delete="deleteSync"
                    />
                </div>

                <!-- Tab: العمليات الفاشلة -->
                <div v-else-if="activeTab === 'failed'">
                    <failed-syncs-table 
                        :items="failedItems"
                        @retry="retrySync"
                        @delete="deleteSync"
                    />
                </div>

                <!-- Tab: السجل -->
                <div v-else-if="activeTab === 'history'">
                    <sync-history-table :items="historyItems" />
                </div>

                <!-- Tab: المستخدمين -->
                <div v-else-if="activeTab === 'users'">
                    <users-sync-status :users="users" />
                </div>
            </div>
        </div>

        <!-- Modal: التنظيف -->
        <cleanup-modal 
            v-if="showCleanupModal"
            @close="showCleanupModal = false"
            @cleanup="performCleanup"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useSync } from '@/composables/useSync'
import axios from 'axios'
import Chart from 'chart.js/auto'

// Composables
const { isOnline, syncStatusText, syncStatusColor } = useSync()

// State
const loading = ref(false)
const stats = ref({})
const chartData = ref([])
const pendingItems = ref([])
const failedItems = ref([])
const historyItems = ref([])
const users = ref([])
const activeTab = ref('pending')
const showCleanupModal = ref(false)
const chartCanvas = ref(null)
const chartInstance = ref(null)
const syncStatus = ref({
    lastSyncTime: null
})

// Computed
const connectionStatusClass = computed(() => {
    return {
        'bg-green-100 border-2 border-green-300': isOnline.value,
        'bg-red-100 border-2 border-red-300': !isOnline.value,
    }
})

const connectionDotClass = computed(() => {
    return isOnline.value ? 'bg-green-500' : 'bg-red-500'
})

const connectionStatusText = computed(() => {
    return isOnline.value ? '🟢 متصل - Online' : '🔴 غير متصل - Offline'
})

const tabs = computed(() => [
    { 
        id: 'pending', 
        label: 'العمليات المعلقة', 
        icon: 'fas fa-clock',
        badge: stats.value.total_pending 
    },
    { 
        id: 'failed', 
        label: 'العمليات الفاشلة', 
        icon: 'fas fa-times-circle',
        badge: stats.value.total_failed 
    },
    { 
        id: 'history', 
        label: 'السجل', 
        icon: 'fas fa-history' 
    },
    { 
        id: 'users', 
        label: 'المستخدمين', 
        icon: 'fas fa-users',
        badge: stats.value.total_users 
    },
])

// Methods
async function loadStats() {
    loading.value = true
    try {
        const response = await axios.get('/api/sync-dashboard/stats')
        stats.value = response.data
    } catch (error) {
        console.error('Failed to load stats:', error)
    } finally {
        loading.value = false
    }
}

async function loadChartData() {
    try {
        const response = await axios.get('/api/sync-dashboard/chart-data', {
            params: { days: 7 }
        })
        chartData.value = response.data
        renderChart()
    } catch (error) {
        console.error('Failed to load chart data:', error)
    }
}

async function loadPending() {
    try {
        const response = await axios.get('/api/sync-dashboard/pending')
        pendingItems.value = response.data.data
    } catch (error) {
        console.error('Failed to load pending:', error)
    }
}

async function loadFailed() {
    try {
        const response = await axios.get('/api/sync-dashboard/failed')
        failedItems.value = response.data.data
    } catch (error) {
        console.error('Failed to load failed:', error)
    }
}

async function loadHistory() {
    try {
        const response = await axios.get('/api/sync-dashboard/history')
        historyItems.value = response.data.data
    } catch (error) {
        console.error('Failed to load history:', error)
    }
}

async function loadUsers() {
    try {
        const response = await axios.get('/api/sync-dashboard/users')
        users.value = response.data
    } catch (error) {
        console.error('Failed to load users:', error)
    }
}

async function retrySync(id) {
    try {
        await axios.post(`/api/sync-dashboard/retry/${id}`)
        await refresh()
    } catch (error) {
        console.error('Failed to retry:', error)
    }
}

async function deleteSync(id) {
    if (!confirm('هل أنت متأكد من الحذف؟')) return
    
    try {
        await axios.delete(`/api/sync-dashboard/delete/${id}`)
        await refresh()
    } catch (error) {
        console.error('Failed to delete:', error)
    }
}

async function retryAllFailed() {
    if (!confirm(`هل تريد إعادة محاولة ${stats.value.total_failed} عملية؟`)) return
    
    loading.value = true
    try {
        const response = await axios.post('/api/sync-dashboard/retry-all')
        alert(response.data.message)
        await refresh()
    } catch (error) {
        console.error('Failed to retry all:', error)
    } finally {
        loading.value = false
    }
}

async function performCleanup(days) {
    loading.value = true
    try {
        const response = await axios.post('/api/sync-dashboard/cleanup', { days })
        alert(response.data.message)
        showCleanupModal.value = false
        await refresh()
    } catch (error) {
        console.error('Failed to cleanup:', error)
    } finally {
        loading.value = false
    }
}

async function refresh() {
    await loadStats()
    await loadChartData()
    
    if (activeTab.value === 'pending') await loadPending()
    if (activeTab.value === 'failed') await loadFailed()
    if (activeTab.value === 'history') await loadHistory()
    if (activeTab.value === 'users') await loadUsers()
}

function renderChart() {
    if (!chartCanvas.value || !chartData.value.length) return

    const ctx = chartCanvas.value.getContext('2d')
    
    if (chartInstance.value) {
        chartInstance.value.destroy()
    }

    chartInstance.value = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.value.map(d => d.date),
            datasets: [
                {
                    label: 'تمت المزامنة',
                    data: chartData.value.map(d => d.synced),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.3
                },
                {
                    label: 'معلقة',
                    data: chartData.value.map(d => d.pending),
                    borderColor: 'rgb(251, 146, 60)',
                    backgroundColor: 'rgba(251, 146, 60, 0.1)',
                    tension: 0.3
                },
                {
                    label: 'فاشلة',
                    data: chartData.value.map(d => d.failed),
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    })
}

function formatTime(time) {
    return new Date(time).toLocaleString('ar-EG')
}

// Auto refresh
let refreshInterval = null

onMounted(() => {
    refresh()
    refreshInterval = setInterval(refresh, 30000) // كل 30 ثانية
})

onUnmounted(() => {
    if (refreshInterval) clearInterval(refreshInterval)
    if (chartInstance.value) chartInstance.value.destroy()
})
</script>

<style scoped>
.stat-card {
    @apply p-6 rounded-lg border-2 transition-all hover:shadow-lg;
}

.stat-icon {
    @apply w-16 h-16 rounded-full flex items-center justify-center text-2xl;
}

.tabs {
    @apply flex border-b border-gray-200;
}

.tab {
    @apply px-6 py-3 font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 transition-colors relative;
}

.tab.active {
    @apply text-blue-600 border-b-2 border-blue-600;
}

.badge {
    @apply ml-2 px-2 py-1 text-xs rounded-full bg-red-500 text-white;
}

.card {
    @apply bg-white rounded-lg shadow-md overflow-hidden;
}

.card-header {
    @apply p-4 bg-gray-50 border-b border-gray-200;
}

.card-body {
    @apply p-6;
}
</style>
