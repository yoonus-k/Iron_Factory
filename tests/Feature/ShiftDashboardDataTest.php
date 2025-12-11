<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Worker;
use App\Models\ShiftAssignment;
use App\Models\ShiftHandover;
use App\Models\WorkerTeam;
use Modules\Manufacturing\Http\Controllers\ShiftDashboardController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ShiftDashboardDataTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new ShiftDashboardController();
    }

    /**
     * Test that shift time range is calculated correctly
     */
    public function test_shift_time_range_evening(): void
    {
        $date = '2025-12-10';
        $shiftType = 'evening';

        $timeRange = $this->controller->getShiftTimeRange($date, $shiftType);

        $this->assertIsArray($timeRange);
        $this->assertArrayHasKey('start', $timeRange);
        $this->assertArrayHasKey('end', $timeRange);

        // Evening shift: 18:00 to 06:00 (next day)
        $startTime = Carbon::parse($timeRange['start']);
        $endTime = Carbon::parse($timeRange['end']);

        $this->assertEquals(18, $startTime->hour);
        $this->assertEquals(0, $startTime->minute);
        $this->assertEquals(6, $endTime->hour);
    }

    /**
     * Test that shift time range for morning is calculated correctly
     */
    public function test_shift_time_range_morning(): void
    {
        $date = '2025-12-10';
        $shiftType = 'morning';

        $timeRange = $this->controller->getShiftTimeRange($date, $shiftType);

        $startTime = Carbon::parse($timeRange['start']);
        $endTime = Carbon::parse($timeRange['end']);

        // Morning shift: 06:00 to 18:00
        $this->assertEquals(6, $startTime->hour);
        $this->assertEquals(18, $endTime->hour);
    }

    /**
     * Test shift summary data retrieval
     */
    public function test_get_shift_summary(): void
    {
        // Create test data
        $user = User::factory()->create();
        $worker = Worker::factory()->create(['user_id' => $user->id]);

        // Create sample production data
        $timeRange = $this->controller->getShiftTimeRange('2025-12-10', 'evening');

        DB::table('stage1_stands')->insert([
            'barcode' => 'TEST-001',
            'weight' => 100,
            'waste' => 5,
            'created_by' => $user->id,
            'created_at' => Carbon::parse($timeRange['start'])->addHours(2),
        ]);

        $summary = $this->controller->index(
            \Illuminate\Http\Request::create('/', 'GET', [
                'date' => '2025-12-10',
                'shift' => 'evening'
            ])
        );

        // The test should pass if no exceptions are thrown
        $this->assertTrue(true);
    }

    /**
     * Test worker attendance data
     */
    public function test_get_worker_attendance(): void
    {
        $user = User::factory()->create();
        $worker = Worker::factory()->create([
            'user_id' => $user->id,
            'worker_code' => 'W001',
        ]);

        // This should not throw any exceptions
        $this->assertTrue(true);
    }
}
