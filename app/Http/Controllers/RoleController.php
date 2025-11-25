<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Get hierarchy structure dynamically from database
     */
    private function getHierarchyStructure()
    {
        // Fetch all menu permissions and their operations from database
        $menuPermissions = Permission::where('name', 'like', 'MENU_%')
            ->orderBy('name')
            ->get();

        $hierarchy = [];

        foreach ($menuPermissions as $menu) {
            $menuCode = $menu->name;

            // Get parent menu (MENU_WAREHOUSE) and its children (MENU_WAREHOUSE_MATERIALS, etc)
            if ($this->isParentMenu($menuCode)) {
                $hierarchy[$menuCode] = [
                    'label' => $menu->display_name,
                    'icon' => $this->getMenuIcon($menuCode),
                    'items' => $this->getMenuItems($menuCode)
                ];
            }
        }

        return $hierarchy;
    }

    /**
     * Check if menu is a parent menu (like MENU_WAREHOUSE, not MENU_WAREHOUSE_MATERIALS)
     */
    private function isParentMenu($menuCode): bool
    {
        // Parent menus have exactly 2 parts: MENU_NAME
        $parts = explode('_', $menuCode);
        return count($parts) == 2;
    }

    /**
     * Get child items for a parent menu
     */
    private function getMenuItems($parentMenuCode): array
    {
        $items = [];

        // Get all child menu items
        $childMenus = Permission::where('name', 'like', $parentMenuCode . '_%')
            ->where('name', '!=', $parentMenuCode)
            ->orderBy('name')
            ->get();

        foreach ($childMenus as $childMenu) {
            $childCode = $childMenu->name;

            // Get operations for this child menu
            $operations = Permission::where('name', 'like', str_replace('MENU_', '', $childCode) . '_%')
                ->where('name', 'not like', 'MENU_%')
                ->orderBy('name')
                ->pluck('name')
                ->toArray();

            $items[] = [
                'code' => $childCode,
                'label' => $childMenu->display_name,
                'operations' => $operations
            ];
        }

        return $items;
    }

    /**
     * Get icon for menu based on its code
     */
    private function getMenuIcon($menuCode): string
    {
        $icons = [
            'MENU_DASHBOARD' => 'fa-tachometer-alt',
            'MENU_WAREHOUSE' => 'fa-warehouse',
            'MENU_STAGE1_STANDS' => 'fa-industry',
            'MENU_STAGE2_PROCESSING' => 'fa-cogs',
            'MENU_STAGE3_COILS' => 'fa-circle-notch',
            'MENU_STAGE4_PACKAGING' => 'fa-box',
            'MENU_PRODUCTION_TRACKING' => 'fa-chart-line',
            'MENU_SHIFTS_WORKERS' => 'fa-users',
            'MENU_QUALITY_WASTE' => 'fa-check-circle',
            'MENU_PRODUCTION_REPORTS' => 'fa-file-chart-line',
            'MENU_MANAGEMENT' => 'fa-sliders-h',
            'MENU_SETTINGS' => 'fa-cog',
        ];

        return $icons[$menuCode] ?? 'fa-folder';
    }

    public function index()
    {
        $roles = Role::with('permissions')->paginate(20);
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('group_name')->orderBy('display_name')->get();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'display_name' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:50', 'unique:roles,role_code'],
            'description' => ['nullable', 'string'],
            'level' => ['required', 'integer', 'min:0', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        DB::beginTransaction();
        try {
            $userId = 1;
            if (auth('web')->check()) {
                $userId = auth('web')->id();
            }

            $role = Role::create([
                'role_name' => $data['display_name'],
                'role_name_en' => $data['name'] ?? $data['display_name'],
                'role_code' => strtoupper($data['name']),
                'description' => $data['description'] ?? null,
                'level' => $data['level'],
                'is_active' => $data['is_active'] ?? true,
                'created_by' => $userId,
            ]);

            // Attach permissions
            if (!empty($data['permission_ids'])) {
                $role->permissions()->sync($data['permission_ids']);
            }

            DB::commit();
            return redirect()->route('roles.index')->with('success', 'تم إنشاء الدور بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('group_name')->orderBy('display_name')->get();
        $role->load('permissions');
        $assigned = $role->permissions->pluck('id')->toArray();
        return view('roles.edit', compact('role', 'permissions', 'assigned'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'display_name' => ['required', 'string', 'max:100'],
            'name' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'level' => ['required', 'integer', 'min:0', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        DB::beginTransaction();
        try {
            $role->update([
                'role_name' => $data['display_name'],
                'role_name_en' => $data['name'] ?? $data['display_name'],
                'description' => $data['description'] ?? null,
                'level' => $data['level'],
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Sync permissions
            if (!empty($data['permission_ids'])) {
                $role->permissions()->sync($data['permission_ids']);
            } else {
                $role->permissions()->detach();
            }

            DB::commit();
            return redirect()->route('roles.index')->with('success', 'تم تحديث الدور بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الدور لأنه مرتبط بمستخدمين');
        }

        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'تم حذف الدور بنجاح');
    }

    public function assignUsers(Role $role)
    {
        $users = User::where('is_active', true)->get();
        $assignedUsers = $role->users;
        return view('roles.assign-users', compact('role', 'users', 'assignedUsers'));
    }

    public function updateUsers(Request $request, Role $role)
    {
        $request->validate([
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        User::whereIn('id', $request->user_ids ?? [])->update(['role_id' => $role->id]);

        return redirect()->route('roles.index')->with('success', 'تم تحديث المستخدمين بنجاح');
    }
}
