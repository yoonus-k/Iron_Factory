<?php

namespace Modules\Manufacturing\Services;

use Modules\Manufacturing\Repositories\WarehouseRepository;
use App\Models\Warehouse;

class WarehouseService
{
    private WarehouseRepository $repository;

    public function __construct(WarehouseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create a new warehouse with validation
     */
    public function createWarehouse(array $data): Warehouse
    {
        // Check if warehouse code already exists
        if ($this->repository->codeExists($data['code'])) {
            throw new \Exception('رمز المستودع موجود بالفعل');
        }

        return $this->repository->create($data);
    }

    /**
     * Update warehouse with validation
     */
    public function updateWarehouse(int $id, array $data): ?Warehouse
    {
        $warehouse = $this->repository->getById($id);

        if (!$warehouse) {
            throw new \Exception('المستودع غير موجود');
        }

        // Check if new code already exists for another warehouse
        if (isset($data['code']) && $data['code'] !== $warehouse->warehouse_code) {
            if ($this->repository->codeExists($data['code'], $id)) {
                throw new \Exception('رمز المستودع موجود بالفعل');
            }
        }

        return $this->repository->update($id, $data);
    }

    /**
     * Delete warehouse
     */
    public function deleteWarehouse(int $id): bool
    {
        $warehouse = $this->repository->getById($id);

        if (!$warehouse) {
            throw new \Exception('المستودع غير موجود');
        }

        return $this->repository->delete($id);
    }

    /**
     * Get warehouse details
     */
    public function getWarehouseDetails(int $id): ?Warehouse
    {
        return $this->repository->getById($id);
    }

    /**
     * Search warehouses
     */
    public function searchWarehouses(array $filters)
    {
        return $this->repository->search($filters);
    }

    /**
     * Get all active warehouses
     */
    public function getActiveWarehouses()
    {
        return $this->repository->getActive();
    }

    /**
     * Get warehouse statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->repository->count(),
            'active' => $this->repository->countByStatus(true),
            'inactive' => $this->repository->countByStatus(false),
        ];
    }
}
