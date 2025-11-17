<?php

namespace Tests\Unit\Services;

use App\Models\DeliveryNote;
use App\Models\RegistrationLog;
use App\Services\DuplicatePreventionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DuplicatePreventionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DuplicatePreventionService();
    }

    /**
     * اختبار التحقق من وجود محاولة سابقة
     */
    public function test_can_detect_previous_attempt()
    {
        $deliveryNote = DeliveryNote::factory()->create();

        // لا توجد محاولة سابقة
        $this->assertFalse($this->service->hasPreviousAttempt($deliveryNote));

        // إنشاء محاولة تسجيل
        RegistrationLog::factory()->create([
            'delivery_note_id' => $deliveryNote->id,
        ]);

        // يجب أن تكون هناك محاولة الآن
        $this->assertTrue($this->service->hasPreviousAttempt($deliveryNote));
    }

    /**
     * اختبار الحصول على آخر محاولة
     */
    public function test_can_get_last_attempt()
    {
        $deliveryNote = DeliveryNote::factory()->create();

        // بدون محاولات
        $this->assertNull($this->service->getLastAttempt($deliveryNote));

        // إنشاء محاولة
        $attempt = RegistrationLog::factory()->create([
            'delivery_note_id' => $deliveryNote->id,
        ]);

        // يجب الحصول على نفس المحاولة
        $lastAttempt = $this->service->getLastAttempt($deliveryNote);
        $this->assertEquals($attempt->id, $lastAttempt->id);
    }

    /**
     * اختبار توليد المفتاح الفريد
     */
    public function test_can_generate_unique_key()
    {
        $deliveryNote = DeliveryNote::factory()->create([
            'note_number' => 'DN-001',
            'supplier_id' => 1,
        ]);

        $key1 = $this->service->generateUniqueKey($deliveryNote);
        $key2 = $this->service->generateUniqueKey($deliveryNote);

        // يجب أن يكون المفتاحان متطابقين
        $this->assertEquals($key1, $key2);

        // يجب أن يكون طول المفتاح 32 (MD5)
        $this->assertEquals(32, strlen($key1));
    }

    /**
     * اختبار التحقق من تجاوز الحد الأقصى للمحاولات
     */
    public function test_can_check_max_attempts_exceeded()
    {
        config(['warehouse-registration.max_attempts' => 3]);

        $deliveryNote = DeliveryNote::factory()->create([
            'registration_attempts' => 2,
        ]);

        $this->assertFalse($this->service->hasExceededMaxAttempts($deliveryNote));

        $deliveryNote->update(['registration_attempts' => 3]);
        $this->assertTrue($this->service->hasExceededMaxAttempts($deliveryNote));
    }

    /**
     * اختبار عتبة التحذير
     */
    public function test_can_check_warning_threshold()
    {
        config(['warehouse-registration.warning_threshold' => 2]);

        $deliveryNote = DeliveryNote::factory()->create([
            'registration_attempts' => 1,
        ]);

        $this->assertFalse($this->service->isWarningThreshold($deliveryNote));

        $deliveryNote->update(['registration_attempts' => 2]);
        $this->assertTrue($this->service->isWarningThreshold($deliveryNote));
    }

    /**
     * اختبار حساب المحاولات المتبقية
     */
    public function test_can_calculate_remaining_attempts()
    {
        config(['warehouse-registration.max_attempts' => 5]);

        $deliveryNote = DeliveryNote::factory()->create([
            'registration_attempts' => 2,
        ]);

        $remaining = $this->service->getRemainingAttempts($deliveryNote);
        $this->assertEquals(3, $remaining);
    }

    /**
     * اختبار الحصول على معلومات الحالة
     */
    public function test_can_get_status_description()
    {
        $deliveryNote = DeliveryNote::factory()->create([
            'registration_attempts' => 1,
        ]);

        $status = $this->service->getStatusDescription($deliveryNote);

        $this->assertArrayHasKey('status', $status);
        $this->assertArrayHasKey('attempts', $status);
        $this->assertArrayHasKey('message', $status);
        $this->assertArrayHasKey('icon', $status);
        $this->assertEquals(1, $status['attempts']);
    }

    /**
     * اختبار مقارنة المحاولات
     */
    public function test_can_compare_attempts()
    {
        $deliveryNote = DeliveryNote::factory()->create();

        // إنشاء محاولتين
        RegistrationLog::factory()->create([
            'delivery_note_id' => $deliveryNote->id,
            'weight_recorded' => 1000,
        ]);

        RegistrationLog::factory()->create([
            'delivery_note_id' => $deliveryNote->id,
            'weight_recorded' => 1100,
        ]);

        $comparison = $this->service->compareAttempts($deliveryNote);

        $this->assertNotEmpty($comparison);
        $this->assertTrue($comparison[0]['changes']['weight_changed']);
    }
}
