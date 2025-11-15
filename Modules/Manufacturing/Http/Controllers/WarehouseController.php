<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Modules\Manufacturing\Http\Requests\StoreWarehouseRequest;
use Modules\Manufacturing\Http\Requests\UpdateWarehouseRequest;
use Modules\Manufacturing\Repositories\WarehouseRepository;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
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

            $warehouse = $this->warehouseRepository->create($data);

            return redirect()
                ->route('manufacturing.warehouses.index')
                ->with('success', 'تم إضافة المستودع بنجاح');
        } catch (\Exception $e) {
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
        $warehouse = $this->warehouseRepository->getById($id);

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
        $warehouse = $this->warehouseRepository->getById($id);

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
            $warehouse = $this->warehouseRepository->getById($id);

            if (!$warehouse) {
                return redirect()
                    ->route('manufacturing.warehouses.index')
                    ->with('error', 'المستودع غير موجود');
            }

            $data = $request->validated();

            $this->warehouseRepository->update($id, $data);

            return redirect()
                ->route('manufacturing.warehouses.index')
                ->with('success', 'تم تحديث بيانات المستودع بنجاح');
        } catch (\Exception $e) {
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
            $warehouse = $this->warehouseRepository->getById($id);

            if (!$warehouse) {
                return redirect()
                    ->route('manufacturing.warehouses.index')
                    ->with('error', 'المستودع غير موجود');
            }

            $this->warehouseRepository->delete($id);

            return redirect()
                ->route('manufacturing.warehouses.index')
                ->with('success', 'تم حذف المستودع بنجاح');
        } catch (\Exception $e) {
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
}