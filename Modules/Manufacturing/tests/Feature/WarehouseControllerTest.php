<?php

namespace Modules\Manufacturing\Tests\Feature;

use Tests\TestCase;
use App\Models\Warehouse;
use App\Models\User;

class WarehouseControllerTest extends TestCase
{
    protected $user;
    protected $warehouse;

    public function setUp(): void
    {
        parent::setUp();

        // إنشاء مستخدم للاختبار
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => 1,
        ]);

        // إنشاء مستودع للاختبار
        $this->warehouse = Warehouse::create([
            'warehouse_code' => 'WH-TEST',
            'warehouse_name' => 'Test Warehouse',
            'location' => 'Test Location',
            'is_active' => 1,
            'created_by' => $this->user->id,
        ]);
    }

    /**
     * اختبار عرض قائمة المستودعات
     */
    public function test_can_view_warehouse_index()
    {
        $response = $this->actingAs($this->user)
            ->get(route('manufacturing.warehouses.index'));

        $response->assertStatus(200);
        $response->assertViewIs('manufacturing::warehouses.warehouse.index');
    }

    /**
     * اختبار عرض صفحة إنشاء مستودع
     */
    public function test_can_view_warehouse_create()
    {
        $response = $this->actingAs($this->user)
            ->get(route('manufacturing.warehouses.create'));

        $response->assertStatus(200);
        $response->assertViewIs('manufacturing::warehouses.warehouse.create');
    }

    /**
     * اختبار إنشاء مستودع جديد
     */
    public function test_can_create_warehouse()
    {
        $data = [
            'name' => 'New Warehouse',
            'code' => 'WH-NEW-001',
            'location' => 'Cairo',
            'description' => 'Test warehouse',
            'capacity' => 1000,
            'status' => 'active',
            'phone' => '01234567890',
            'email' => 'warehouse@example.com',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('manufacturing.warehouses.store'), $data);

        $response->assertRedirect(route('manufacturing.warehouses.index'));

        // التحقق من أن المستودع تم إضافته
        $this->assertDatabaseHas('warehouses', [
            'warehouse_code' => 'WH-NEW-001',
            'warehouse_name' => 'New Warehouse',
        ]);
    }

    /**
     * اختبار عدم إمكانية إنشاء مستودع بنفس الرمز
     */
    public function test_cannot_create_warehouse_with_duplicate_code()
    {
        $data = [
            'name' => 'Duplicate Warehouse',
            'code' => 'WH-TEST', // نفس الرمز الموجود
            'location' => 'Cairo',
            'status' => 'active',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('manufacturing.warehouses.store'), $data);

        $response->assertSessionHasErrors('code');
    }

    /**
     * اختبار عرض تفاصيل مستودع
     */
    public function test_can_view_warehouse_show()
    {
        $response = $this->actingAs($this->user)
            ->get(route('manufacturing.warehouses.show', $this->warehouse->id));

        $response->assertStatus(200);
        $response->assertViewIs('manufacturing::warehouses.warehouse.show');
    }

    /**
     * اختبار عرض صفحة تعديل مستودع
     */
    public function test_can_view_warehouse_edit()
    {
        $response = $this->actingAs($this->user)
            ->get(route('manufacturing.warehouses.edit', $this->warehouse->id));

        $response->assertStatus(200);
        $response->assertViewIs('manufacturing::warehouses.warehouse.edit');
    }

    /**
     * اختبار تحديث مستودع
     */
    public function test_can_update_warehouse()
    {
        $data = [
            'name' => 'Updated Warehouse',
            'code' => 'WH-TEST',
            'location' => 'Alexandria',
            'status' => 'inactive',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('manufacturing.warehouses.update', $this->warehouse->id), $data);

        $response->assertRedirect(route('manufacturing.warehouses.index'));

        $this->assertDatabaseHas('warehouses', [
            'id' => $this->warehouse->id,
            'warehouse_name' => 'Updated Warehouse',
            'location' => 'Alexandria',
        ]);
    }

    /**
     * اختبار حذف مستودع
     */
    public function test_can_delete_warehouse()
    {
        $response = $this->actingAs($this->user)
            ->delete(route('manufacturing.warehouses.destroy', $this->warehouse->id));

        $response->assertRedirect(route('manufacturing.warehouses.index'));

        $this->assertDatabaseMissing('warehouses', [
            'id' => $this->warehouse->id,
        ]);
    }

    /**
     * اختبار البحث عن مستودع
     */
    public function test_can_search_warehouse()
    {
        $response = $this->actingAs($this->user)
            ->get(route('manufacturing.warehouses.index', [
                'search' => 'Test',
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('warehouses');
    }

    /**
     * اختبار تصفية المستودعات حسب الحالة
     */
    public function test_can_filter_warehouse_by_status()
    {
        $response = $this->actingAs($this->user)
            ->get(route('manufacturing.warehouses.index', [
                'status' => 'active',
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('warehouses');
    }

    /**
     * اختبار الحصول على الإحصائيات
     */
    public function test_can_get_warehouse_statistics()
    {
        $response = $this->actingAs($this->user)
            ->get(route('manufacturing.warehouses.statistics'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'total',
            'active',
            'inactive',
        ]);
    }

    /**
     * اختبار الحصول على المستودعات النشطة
     */
    public function test_can_get_active_warehouses()
    {
        $response = $this->actingAs($this->user)
            ->get(route('manufacturing.warehouses.active'));

        $response->assertStatus(200);
        $response->assertJsonIsArray();
    }
}
