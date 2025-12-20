<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Exception;

class CentralServerService
{
    protected $baseUrl;
    protected $token;
    protected $localServerId;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = config('sync.central_server_url');
        $this->token = config('sync.central_server_token');
        $this->localServerId = config('sync.local_server_id');
        $this->timeout = config('sync.connection_timeout', 30);
    }

    /**
     * التحقق من الاتصال بالسيرفر المركزي
     */
    public function checkConnection()
    {
        try {
            // CENTRAL_SERVER_URL يشير إلى المسار الأساسي /api/sync
            // لذلك هنا نستخدم المسار النسبي داخل مجموعة sync فقط
            $response = $this->makeRequest('GET', '/health');
            return $response['success'] ?? false;
        } catch (Exception $e) {
            Log::error('Connection check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * رفع البيانات للسيرفر المركزي
     */
    public function push(array $data)
    {
        try {
            $payload = $this->preparePayload($data);
            
            // يتم بناء الرابط النهائي: CENTRAL_SERVER_URL + '/push'
            $response = $this->makeRequest('POST', '/push', [
                'data' => $payload,
                'server_id' => $this->localServerId,
            ]);

            if ($response['success'] ?? false) {
                Log::info('Push to central server successful', [
                    'synced_count' => $response['synced_count'] ?? 0
                ]);
            }

            return $response;

        } catch (Exception $e) {
            Log::error('Push to central server failed', [
                'error' => $e->getMessage(),
                'data_count' => count($data)
            ]);
            throw $e;
        }
    }

    /**
     * سحب البيانات من السيرفر المركزي
     */
    public function pull($lastSyncTime = null)
    {
        try {
            $params = [
                'server_id' => $this->localServerId,
            ];

            if ($lastSyncTime) {
                $params['last_sync_time'] = $lastSyncTime;
            }

            // يتم بناء الرابط النهائي: CENTRAL_SERVER_URL + '/pull'
            $response = $this->makeRequest('GET', '/pull', $params);

            if ($response['success'] ?? false) {
                Log::info('Pull from central server successful', [
                    'total_items' => $response['total_items'] ?? 0
                ]);
            }

            return $response;

        } catch (Exception $e) {
            Log::error('Pull from central server failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * تسجيل السيرفر المحلي في السيرفر المركزي
     */
    public function register()
    {
        try {
            $response = $this->makeRequest('POST', '/api/servers/register', [
                'server_id' => $this->localServerId,
                'server_name' => config('sync.local_server_name'),
                'server_url' => config('app.url'),
                'server_ip' => request()->ip(),
            ]);

            return $response;

        } catch (Exception $e) {
            Log::error('Server registration failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * إرسال heartbeat للسيرفر المركزي
     */
    public function heartbeat()
    {
        try {
            $response = $this->makeRequest('POST', '/api/servers/heartbeat', [
                'server_id' => $this->localServerId,
                'status' => 'online',
                'stats' => $this->getLocalStats(),
            ]);

            return $response;

        } catch (Exception $e) {
            Log::warning('Heartbeat failed', ['error' => $e->getMessage()]);
            return ['success' => false];
        }
    }

    /**
     * الحصول على إحصائيات السيرفر المحلي
     */
    protected function getLocalStats()
    {
        return [
            'pending_count' => \App\Models\PendingSync::pending()->count(),
            'failed_count' => \App\Models\PendingSync::failed()->count(),
            'synced_count' => \App\Models\SyncLog::synced()->whereDate('synced_at', today())->count(),
        ];
    }

    /**
     * تحضير البيانات للإرسال
     */
    protected function preparePayload(array $data)
    {
        // تشفير البيانات إذا كان مفعل
        if (config('sync.encrypt_data')) {
            return $this->encryptData($data);
        }

        return $data;
    }

    /**
     * تشفير البيانات
     */
    protected function encryptData($data)
    {
        // يمكن استخدام Laravel Encryption
        return encrypt($data);
    }

    /**
     * عمل HTTP Request للسيرفر المركزي
     */
    protected function makeRequest($method, $endpoint, $data = [])
    {
        $url = $this->baseUrl . $endpoint;

        $request = Http::timeout($this->timeout)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'X-Server-ID' => $this->localServerId,
                'Accept' => 'application/json',
            ]);

        // تعطيل SSL verification إذا كان مطلوب
        if (!config('sync.verify_ssl')) {
            $request->withoutVerifying();
        }

        // Proxy إذا كان مفعل
        if (config('sync.use_proxy')) {
            $request->withOptions([
                'proxy' => config('sync.proxy_url')
            ]);
        }

        $response = match(strtoupper($method)) {
            'GET' => $request->get($url, $data),
            'POST' => $request->post($url, $data),
            'PUT' => $request->put($url, $data),
            'DELETE' => $request->delete($url, $data),
            default => throw new Exception("Unsupported HTTP method: {$method}"),
        };

        if (!$response->successful()) {
            throw new Exception(
                "Request failed: " . $response->status() . " - " . $response->body()
            );
        }

        return $response->json();
    }

    /**
     * اختبار الاتصال بالسيرفر المركزي
     */
    public static function test()
    {
        $service = new static();
        
        $tests = [
            'connection' => false,
            'authentication' => false,
            'push' => false,
            'pull' => false,
        ];

        try {
            // Test 1: Connection
            $tests['connection'] = $service->checkConnection();

            if ($tests['connection']) {
                // Test 2: Authentication (implicit in checkConnection)
                $tests['authentication'] = true;

                // Test 3: Push (with dummy data)
                try {
                    $testData = [[
                        'entity_type' => 'test',
                        'action' => 'create',
                        'data' => ['test' => true],
                        'local_id' => 'test-' . uniqid(),
                    ]];
                    $pushResult = $service->push($testData);
                    $tests['push'] = $pushResult['success'] ?? false;
                } catch (Exception $e) {
                    Log::info('Push test failed (expected for test data)');
                }

                // Test 4: Pull
                try {
                    $pullResult = $service->pull();
                    $tests['pull'] = $pullResult['success'] ?? false;
                } catch (Exception $e) {
                    Log::warning('Pull test failed', ['error' => $e->getMessage()]);
                }
            }

        } catch (Exception $e) {
            Log::error('Connection test failed', ['error' => $e->getMessage()]);
        }

        return $tests;
    }
}
