<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\StoresNotifications;
use Modules\Manufacturing\Http\Requests\StoreWarehouseRequest;
use Modules\Manufacturing\Http\Requests\UpdateWarehouseRequest;
use Modules\Manufacturing\Repositories\WarehouseRepository;
use Modules\Manufacturing\Traits\LogsOperations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    use LogsOperations, StoresNotifications;

    private WarehouseRepository $warehouseRepository;

    public function __construct(WarehouseRepository $warehouseRepository)
    {
        $this->warehouseRepository = $warehouseRepository;
    }

    /**
     * Display a listing of the warehouses.
     */
    public function index(Request $request)
    {
        // Check if search/filter parameters exist
        if ($request->has('search') || $request->has('status')) {
            $warehouses = $this->warehouseRepository->search($request->all());
        } else {
            $warehouses = $this->warehouseRepository->getAllPaginated(10);
        }

        return view('manufacturing::warehouses.warehouse.index', [
            'warehouses' => $warehouses,
        ]);
    }

    /**
     * Show the form for creating a new warehouse.
     */
    public function create()
    {
        // Get all users to populate the manager dropdown
        $managers = User::where('is_active', 1)->get();

        return view('manufacturing::warehouses.warehouse.create', [
            'managers' => $managers,
        ]);
    }

    /**
     * Store a newly created warehouse in storage.
     */
    public function store(StoreWarehouseRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = Auth::id() ?? 1;

            // توليد الكود إذا لم يكن موجوداً
            if (empty($data['warehouse_code'])) {
                $prefix = 'WH-';
                $date = now();
                $year = $date->format('y');
                $month = $date->format('m');
                $day = $date->format('d');
                $random = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
                $data['warehouse_code'] = $prefix . $year . $month . $day . '-' . $random;
            }

            // تحويل is_active إلى boolean إذا لم تكن موجودة أو تأكد من قيمتها
            if (!isset($data['is_active']) || is_null($data['is_active'])) {
                $data['is_active'] = 1;
            } else {
                $data['is_active'] = (int) $data['is_active'];
            }

            $warehouse = $this->warehouseRepository->create($data);

            // تسجيل العملية
            try {
                $this->logOperation(
                    'create',
                    'Create Warehouse',
                    'تم إنشاء مستودع جديد: ' . $warehouse->warehouse_name,
                    'warehouses',
                    $warehouse->id,
                    null,
                    $warehouse->toArray()
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log warehouse creation: ' . $logError->getMessage());
            }

            // ✅ تخزين الإشعار
            $this->notifyCreate(
                'مستودع',
                $warehouse->warehouse_name,
                route('manufacturing.warehouses.show', $warehouse->id)
            );

            return redirect()
                ->route('manufacturing.warehouses.index')
                ->with('success', 'تم إضافة المستودع بنجاح');
        } catch (\Exception $e) {
            Log::error('Error creating warehouse: ' . $e->getMessage(), [
                'exception' => $e,
                'input' => $request->all()
            ]);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة المستودع: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified warehouse.
     */
    public function show($id)
    {
        $warehouse = $this->warehouseRepository->getById((int) $id);

        if (!$warehouse) {
            return redirect()
                ->route('manufacturing.warehouses.index')
                ->with('error', 'المستودع غير موجود');
        }

        return view('manufacturing::warehouses.warehouse.show', [
            'warehouse' => $warehouse,
        ]);
    }

    /**
     * Show the form for editing the specified warehouse.
     */
    public function edit($id)
    {
        $warehouse = $this->warehouseRepository->getById((int) $id);

        if (!$warehouse) {
            return redirect()
                ->route('manufacturing.warehouses.index')
                ->with('error', 'المستودع غير موجود');
        }

        // Get all users to populate the manager dropdown
        $managers = User::where('is_active', 1)->get();

        return view('manufacturing::warehouses.warehouse.edit', [
            'warehouse' => $warehouse,
            'managers' => $managers,
        ]);
    }

    /**
     * Update the specified warehouse in storage.
     */
    public function update(UpdateWarehouseRequest $request, $id)
    {
        try {
            $warehouse = $this->warehouseRepository->getById((int) $id);

            if (!$warehouse) {
                return redirect()
                    ->route('manufacturing.warehouses.index')
                    ->with('error', 'المستودع غير موجود');
            }

            $oldValues = $warehouse->toArray();
            $data = $request->validated();

            $this->warehouseRepository->update((int) $id, $data);

            $warehouse = $this->warehouseRepository->getById((int) $id);
            $newValues = $warehouse->toArray();

            // تسجيل العملية
            try {
                $this->logOperation(
                    'update',
                    'Update Warehouse',
                    'تم تحديث مستودع: ' . $warehouse->warehouse_name,
                    'warehouses',
                    $warehouse->id,
                    $oldValues,
                    $newValues
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log warehouse update: ' . $logError->getMessage());
            }

            // ✅ تخزين الإشعار
            $this->notifyUpdate(
                'مستودع',
                $warehouse->warehouse_name,
                route('manufacturing.warehouses.show', $warehouse->id)
            );

            return redirect()
                ->route('manufacturing.warehouses.index')
                ->with('success', 'تم تحديث بيانات المستودع بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating warehouse: ' . $e->getMessage(), [
                'exception' => $e,
                'warehouse_id' => $id,
                'input' => $request->all()
            ]);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث المستودع: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified warehouse from storage.
     */
    public function destroy($id)
    {
        try {
            $warehouse = $this->warehouseRepository->getById((int) $id);

            if (!$warehouse) {
                return redirect()
                    ->route('manufacturing.warehouses.index')
                    ->with('error', 'المستودع غير موجود');
            }

            $oldValues = $warehouse->toArray();

            // تسجيل العملية قبل الحذف
            try {
                $this->logOperation(
                    'delete',
                    'Delete Warehouse',
                    'تم حذف مستودع: ' . $warehouse->warehouse_name,
                    'warehouses',
                    $warehouse->id,
                    $oldValues,
                    null
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log warehouse deletion: ' . $logError->getMessage());
            }

            $this->warehouseRepository->delete((int) $id);

            // ✅ تخزين الإشعار
            $this->notifyDelete(
                'مستودع',
                $warehouse->warehouse_name,
                route('manufacturing.warehouses.index')
            );

            return redirect()
                ->route('manufacturing.warehouses.index')
                ->with('success', 'تم حذف المستودع بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting warehouse: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'حدث خطأ أثناء حذف المستودع: ' . $e->getMessage());
        }
    }

    /**
     * Get warehouse statistics
     */
    public function statistics()
    {
        $totalWarehouses = $this->warehouseRepository->count();
        $activeWarehouses = $this->warehouseRepository->countByStatus(true);
        $inactiveWarehouses = $this->warehouseRepository->countByStatus(false);

        return response()->json([
            'total' => $totalWarehouses,
            'active' => $activeWarehouses,
            'inactive' => $inactiveWarehouses,
        ]);
    }

    /**
     * Get active warehouses only (for dropdowns, etc.)
     */
    public function getActive()
    {
        $warehouses = $this->warehouseRepository->getActive();

        return response()->json($warehouses);
    }

    /**
     * Toggle warehouse status (active/inactive).
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $warehouse = $this->warehouseRepository->getById((int) $id);

            if (!$warehouse) {
                return redirect()->back()->with('error', 'المستودع غير موجود');
            }

            $oldStatus = $warehouse->is_active;
            $newStatus = !$oldStatus;

            $this->warehouseRepository->update((int) $id, ['is_active' => $newStatus]);

            // Log the status change
            try {
                $this->logOperation(
                    'update',
                    'Toggle Status',
                    'تم تغيير حالة المستودع من ' . ($oldStatus ? 'مفعل' : 'معطل') . ' إلى ' . ($newStatus ? 'مفعل' : 'معطل'),
                    'warehouses',
                    $warehouse->id,
                    ['is_active' => $oldStatus],
                    ['is_active' => $newStatus]
                );
            } catch (\Exception $logError) {
                Log::error('Failed to log warehouse status change: ' . $logError->getMessage());
            }

            // ✅ تخزين الإشعار لتغيير الحالة
            $this->notifyStatusChange(
                'مستودع',
                $oldStatus ? 'مفعل' : 'معطل',
                $newStatus ? 'مفعل' : 'معطل',
                $warehouse->warehouse_name,
                route('manufacturing.warehouses.show', $warehouse->id)
            );

            return redirect()->back()
                           ->with('success', 'تم تغيير حالة المستودع بنجاح');
        } catch (\Exception $e) {
            Log::error('Error toggling warehouse status: ' . $e->getMessage());
            return redirect()->back()
                           ->withErrors(['error' => 'فشل في تغيير حالة المستودع: ' . $e->getMessage()]);
        }
    }
}
