// Composable للتعامل مع المزامنة Offline/Online
// resources/js/composables/useSync.js

import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

export function useSync() {
    // الحالة
    const isOnline = ref(navigator.onLine)
    const isSyncing = ref(false)
    const pendingCount = ref(0)
    const failedCount = ref(0)
    const lastSyncTime = ref(null)
    const syncStats = ref({})
    
    // Device ID
    const deviceId = ref(getOrCreateDeviceId())

    /**
     * الحصول على أو إنشاء Device ID
     */
    function getOrCreateDeviceId() {
        let id = localStorage.getItem('device_id')
        if (!id) {
            id = generateUUID()
            localStorage.setItem('device_id', id)
        }
        return id
    }

    /**
     * توليد UUID
     */
    function generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0
            const v = c === 'x' ? r : (r & 0x3 | 0x8)
            return v.toString(16)
        })
    }

    /**
     * إعداد axios مع Device ID
     */
    function setupAxiosInterceptor() {
        axios.interceptors.request.use(config => {
            config.headers['X-Device-ID'] = deviceId.value
            return config
        })
    }

    /**
     * رفع البيانات للسيرفر (Push)
     */
    async function push(data) {
        try {
            const response = await axios.post('/api/sync/push', { data })
            return response.data
        } catch (error) {
            console.error('Push failed:', error)
            throw error
        }
    }

    /**
     * سحب البيانات من السيرفر (Pull)
     */
    async function pull() {
        try {
            const params = lastSyncTime.value ? { last_sync_time: lastSyncTime.value } : {}
            const response = await axios.get('/api/sync/pull', { params })
            
            if (response.data.success) {
                lastSyncTime.value = response.data.last_sync_time
                localStorage.setItem('last_sync_time', lastSyncTime.value)
            }
            
            return response.data
        } catch (error) {
            console.error('Pull failed:', error)
            throw error
        }
    }

    /**
     * إضافة عملية للانتظار (عند الأوفلاين)
     */
    async function queue(entityType, action, data, priority = 0) {
        const pendingSync = {
            entity_type: entityType,
            action: action,
            data: data,
            local_id: generateUUID(),
            priority: priority,
            created_at: new Date().toISOString(),
        }

        // حفظ في LocalStorage
        const pending = getPendingFromLocalStorage()
        pending.push(pendingSync)
        localStorage.setItem('pending_syncs', JSON.stringify(pending))
        
        pendingCount.value = pending.length

        return pendingSync
    }

    /**
     * معالجة العمليات المعلقة
     */
    async function processPending() {
        if (!isOnline.value || isSyncing.value) {
            return { success: false, message: 'Not online or already syncing' }
        }

        isSyncing.value = true

        try {
            // الحصول على العمليات المعلقة من LocalStorage
            const pending = getPendingFromLocalStorage()
            
            if (pending.length === 0) {
                isSyncing.value = false
                return { success: true, synced_count: 0 }
            }

            // رفعها للسيرفر
            const result = await push(pending)

            if (result.success) {
                // حذف العمليات الناجحة من LocalStorage
                const syncedLocalIds = result.synced_items.map(item => item.local_id)
                const remaining = pending.filter(item => !syncedLocalIds.includes(item.local_id))
                
                localStorage.setItem('pending_syncs', JSON.stringify(remaining))
                pendingCount.value = remaining.length

                // تحديث آخر وقت مزامنة
                lastSyncTime.value = new Date().toISOString()
                localStorage.setItem('last_sync_time', lastSyncTime.value)
            }

            isSyncing.value = false
            return result

        } catch (error) {
            isSyncing.value = false
            console.error('Process pending failed:', error)
            throw error
        }
    }

    /**
     * الحصول على العمليات المعلقة من LocalStorage
     */
    function getPendingFromLocalStorage() {
        const stored = localStorage.getItem('pending_syncs')
        return stored ? JSON.parse(stored) : []
    }

    /**
     * الحصول على إحصائيات المزامنة
     */
    async function getStats() {
        try {
            const response = await axios.get('/api/sync/stats')
            
            if (response.data.success) {
                syncStats.value = response.data.stats
                pendingCount.value = response.data.stats.pending_count
                failedCount.value = response.data.stats.failed_count
            }
            
            return response.data
        } catch (error) {
            console.error('Get stats failed:', error)
            throw error
        }
    }

    /**
     * فحص حالة الاتصال
     */
    async function checkHealth() {
        try {
            const response = await axios.get('/api/sync/health')
            return response.data.success
        } catch (error) {
            return false
        }
    }

    /**
     * معالجة تغيير حالة الاتصال
     */
    function handleOnline() {
        isOnline.value = true
        console.log('🟢 Online - سيتم معالجة العمليات المعلقة')
        
        // معالجة العمليات المعلقة تلقائياً
        setTimeout(() => {
            processPending().catch(console.error)
        }, 1000)
    }

    function handleOffline() {
        isOnline.value = false
        console.log('🔴 Offline - سيتم حفظ العمليات محلياً')
    }

    /**
     * مزامنة دورية
     */
    let syncInterval = null
    function startAutoSync(intervalSeconds = 60) {
        if (syncInterval) {
            clearInterval(syncInterval)
        }

        syncInterval = setInterval(async () => {
            if (isOnline.value && !isSyncing.value) {
                try {
                    await processPending()
                    await pull()
                } catch (error) {
                    console.error('Auto sync failed:', error)
                }
            }
        }, intervalSeconds * 1000)
    }

    function stopAutoSync() {
        if (syncInterval) {
            clearInterval(syncInterval)
            syncInterval = null
        }
    }

    // Lifecycle
    onMounted(() => {
        setupAxiosInterceptor()
        
        // تحميل آخر وقت مزامنة
        const stored = localStorage.getItem('last_sync_time')
        if (stored) {
            lastSyncTime.value = stored
        }

        // تحميل عدد العمليات المعلقة
        pendingCount.value = getPendingFromLocalStorage().length

        // مراقبة حالة الاتصال
        window.addEventListener('online', handleOnline)
        window.addEventListener('offline', handleOffline)

        // بدء المزامنة التلقائية (كل دقيقة)
        startAutoSync(60)

        // معالجة العمليات المعلقة عند التحميل
        if (isOnline.value) {
            setTimeout(() => {
                processPending().catch(console.error)
            }, 2000)
        }
    })

    onUnmounted(() => {
        window.removeEventListener('online', handleOnline)
        window.removeEventListener('offline', handleOffline)
        stopAutoSync()
    })

    // Computed
    const hasPending = computed(() => pendingCount.value > 0)
    const hasFailed = computed(() => failedCount.value > 0)
    const syncStatusText = computed(() => {
        if (!isOnline.value) return 'غير متصل'
        if (isSyncing.value) return 'جاري المزامنة...'
        if (hasPending.value) return `${pendingCount.value} عملية معلقة`
        return 'متزامن'
    })

    const syncStatusColor = computed(() => {
        if (!isOnline.value) return 'red'
        if (isSyncing.value) return 'blue'
        if (hasPending.value) return 'orange'
        return 'green'
    })

    return {
        // State
        isOnline,
        isSyncing,
        pendingCount,
        failedCount,
        lastSyncTime,
        syncStats,
        deviceId,
        
        // Computed
        hasPending,
        hasFailed,
        syncStatusText,
        syncStatusColor,
        
        // Methods
        push,
        pull,
        queue,
        processPending,
        getStats,
        checkHealth,
        startAutoSync,
        stopAutoSync,
    }
}
