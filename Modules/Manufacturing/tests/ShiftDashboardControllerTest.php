<?php

namespace Modules\Manufacturing\Tests;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\ShiftAssignment;
use Carbon\Carbon;

class ShiftDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test shift dashboard loads successfully
     */
    public function test_shift_dashboard_loads()
    {
        $response = $this->get(route('manufacturing.reports.shift-dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('manufacturing::reports.shift-dashboard');
    }

    /**
     * Test shift dashboard with custom date and shift
     */
    public function test_shift_dashboard_with_parameters()
    {
        $date = Carbon::now()->format('Y-m-d');
        $shift = 'evening';

        $response = $this->get(route('manufacturing.reports.shift-dashboard', [
            'date' => $date,
            'shift' => $shift
        ]));

        $response->assertStatus(200);
    }

    /**
     * Test live stats endpoint
     */
    public function test_live_stats_endpoint()
    {
        $response = $this->get(route('manufacturing.reports.shift-dashboard.liveStats', [
            'date' => Carbon::now()->format('Y-m-d'),
            'shift_type' => 'evening'
        ]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'summary' => [
                    'total_items',
                    'total_output_kg',
                    'total_waste_kg',
                    'efficiency'
                ],
                'wip_count',
                'top_performers',
                'by_stage'
            ],
            'timestamp'
        ]);
    }

    /**
     * Test export report endpoint
     */
    public function test_export_report_endpoint()
    {
        $response = $this->post(route('manufacturing.reports.shift-dashboard.exportReport'), [
            'date' => Carbon::now()->format('Y-m-d'),
            'shift' => 'evening'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }

    /**
     * Test morning shift data retrieval
     */
    public function test_morning_shift_data()
    {
        $response = $this->get(route('manufacturing.reports.shift-dashboard', [
            'shift' => 'morning'
        ]));

        $response->assertStatus(200);
    }

    /**
     * Test error handling for invalid date
     */
    public function test_invalid_date_handling()
    {
        $response = $this->get(route('manufacturing.reports.shift-dashboard', [
            'date' => 'invalid-date'
        ]));

        // Should handle gracefully, not crash
        $response->assertStatus(200);
    }

    /**
     * Test data structure of shift summary
     */
    public function test_shift_summary_data_structure()
    {
        $response = $this->get(route('manufacturing.reports.shift-dashboard'));

        $response->assertViewHas('summary');

        $summary = $response->viewData('summary');

        $this->assertArrayHasKey('total_items', $summary);
        $this->assertArrayHasKey('total_output_kg', $summary);
        $this->assertArrayHasKey('total_waste_kg', $summary);
        $this->assertArrayHasKey('efficiency', $summary);
        $this->assertArrayHasKey('workers_count', $summary);
    }

    /**
     * Test stage efficiency details structure
     */
    public function test_stage_efficiency_structure()
    {
        $response = $this->get(route('manufacturing.reports.shift-dashboard'));

        $response->assertViewHas('stageEfficiency');

        $stages = $response->viewData('stageEfficiency');

        foreach ($stages as $stage) {
            $this->assertArrayHasKey('stage', $stage);
            $this->assertArrayHasKey('name', $stage);
            $this->assertArrayHasKey('items', $stage);
            $this->assertArrayHasKey('output', $stage);
            $this->assertArrayHasKey('waste', $stage);
            $this->assertArrayHasKey('efficiency', $stage);
            $this->assertArrayHasKey('workers_count', $stage);
        }
    }

    /**
     * Test attendance data structure
     */
    public function test_attendance_data_structure()
    {
        $response = $this->get(route('manufacturing.reports.shift-dashboard'));

        $response->assertViewHas('attendance');

        $attendance = $response->viewData('attendance');

        if (!empty($attendance)) {
            foreach ($attendance as $worker) {
                $this->assertArrayHasKey('worker_id', $worker);
                $this->assertArrayHasKey('worker_name', $worker);
                $this->assertArrayHasKey('total_items', $worker);
                $this->assertArrayHasKey('efficiency', $worker);
            }
        }
    }

    /**
     * Test top performers data structure
     */
    public function test_top_performers_structure()
    {
        $response = $this->get(route('manufacturing.reports.shift-dashboard'));

        $response->assertViewHas('topPerformers');

        $performers = $response->viewData('topPerformers');

        if (!empty($performers)) {
            foreach ($performers as $performer) {
                $this->assertArrayHasKey('worker_id', $performer);
                $this->assertArrayHasKey('worker_name', $performer);
                $this->assertArrayHasKey('items', $performer);
                $this->assertArrayHasKey('efficiency', $performer);
            }
        }
    }

    /**
     * Test time range calculation
     */
    public function test_time_range_calculation()
    {
        // Test evening shift
        $controller = app('Modules\Manufacturing\Http\Controllers\ShiftDashboardController');
        $timeRange = $controller->getShiftTimeRange('2025-12-10', 'evening');

        $this->assertArrayHasKey('start', $timeRange);
        $this->assertArrayHasKey('end', $timeRange);

        // Check time format
        $this->assertStringContainsString('2025-12-10', $timeRange['start']);

        // Test morning shift
        $timeRange = $controller->getShiftTimeRange('2025-12-10', 'morning');

        $this->assertArrayHasKey('start', $timeRange);
        $this->assertArrayHasKey('end', $timeRange);
    }
}
