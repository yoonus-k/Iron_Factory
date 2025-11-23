<?php

namespace Modules\Manufacturing\Repositories;

use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class WarehouseRepository
{
    /**
     * Get all warehouses with pagination
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Warehouse::orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get all warehouses
     */
    public function getAll(): Collection
    {
        return Warehouse::all();
    }

    /**
     * Search warehouses by filters
     */
    public function search(array $filters): LengthAwarePaginator
    {
        $query = Warehouse::query();

        // Search by name or code
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('warehouse_name', 'like', "%{$search}%")
                ->orWhere('warehouse_name_en', 'like', "%{$search}%")
                ->orWhere('warehouse_code', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%")
                ->orWhere('location_en', 'like', "%{$search}%");
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $isActive = $filters['status'] === 'active' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        // ترتيب البيانات حسب الأحدث أولاً مع الباجنيشن
        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    /**
     * Get warehouse by ID
     */
    public function getById(int $id): ?Warehouse
    {
        return Warehouse::find($id);
    }

    /**
     * Get warehouse by code
     */
    public function getByCode(string $code): ?Warehouse
    {
        return Warehouse::where('warehouse_code', $code)->first();
    }

    /**
     * Create new warehouse
     */
    public function create(array $data): Warehouse
    {
        $warehouse = new Warehouse();
        $warehouse->warehouse_code = $data['code'];
        $warehouse->warehouse_name = $data['name'];
        $warehouse->warehouse_name_en = $data['name_en'] ?? null;
        $warehouse->location = $data['location'] ?? null;
        $warehouse->location_en = $data['location_en'] ?? null;
        $warehouse->description = $data['description'] ?? null;
        $warehouse->description_en = $data['description_en'] ?? null;
        $warehouse->capacity = $data['capacity'] ?? null;
        $warehouse->capacity_unit = $data['capacity_unit'] ?? 'متر مكعب';
        $warehouse->manager_name = $data['manager_id'] ?? null;
        $warehouse->contact_number = $data['phone'] ?? null;

        $warehouse->created_by = Auth::check() ? Auth::id() : 1;

        $warehouse->save();

        return $warehouse;
    }

    /**
     * Update warehouse
     */
    public function update(int $id, array $data): ?Warehouse
    {
        $warehouse = Warehouse::find($id);

        if (!$warehouse) {
            return null;
        }

        if (isset($data['code'])) {
            $warehouse->warehouse_code = $data['code'];
        }
        if (isset($data['name'])) {
            $warehouse->warehouse_name = $data['name'];
        }
        if (isset($data['name_en'])) {
            $warehouse->warehouse_name_en = $data['name_en'];
        }
        if (isset($data['location'])) {
            $warehouse->location = $data['location'];
        }
        if (isset($data['location_en'])) {
            $warehouse->location_en = $data['location_en'];
        }
        if (isset($data['description'])) {
            $warehouse->description = $data['description'];
        }
        if (isset($data['description_en'])) {
            $warehouse->description_en = $data['description_en'];
        }
        if (isset($data['capacity'])) {
            $warehouse->capacity = $data['capacity'];
        }
        if (isset($data['manager_id'])) {
            $warehouse->manager_name = $data['manager_id'];
        }
        if (isset($data['phone'])) {
            $warehouse->contact_number = $data['phone'];
        }

        $warehouse->save();

        return $warehouse;
    }

    /**
     * Delete warehouse
     */
    public function delete(int $id): bool
    {
        $warehouse = Warehouse::find($id);

        if (!$warehouse) {
            return false;
        }

        return $warehouse->delete();
    }

    /**
     * Check if warehouse code exists
     */
    public function codeExists(string $code, int $excludeId = null): bool
    {
        $query = Warehouse::where('warehouse_code', $code);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Get active warehouses only
     */
    public function getActive(): Collection
    {
        return Warehouse::where('is_active', 1)->get();
    }

    /**
     * Get warehouses count
     */
    public function count(): int
    {
        return Warehouse::count();
    }

    /**
     * Get warehouses count by status
     */
    public function countByStatus(bool $isActive): int
    {
        return Warehouse::where('is_active', $isActive ? 1 : 0)->count();
    }
}
