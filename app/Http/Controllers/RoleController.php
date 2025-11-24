<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->paginate(20);
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::where('is_active', true)->get()->groupBy('module');
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:100',
            'role_name_en' => 'nullable|string|max:100',
            'role_code' => 'required|string|max:50|unique:roles,role_code',
            'description' => 'nullable|string',
            'level' => 'required|integer|min:0|max:100',
            'permissions' => 'nullable|array',
            'permissions.*.permission_id' => 'required|exists:permissions,id',
            'permissions.*.can_create' => 'nullable|boolean',
            'permissions.*.can_read' => 'nullable|boolean',
            'permissions.*.can_update' => 'nullable|boolean',
            'permissions.*.can_delete' => 'nullable|boolean',
            'permissions.*.can_approve' => 'nullable|boolean',
            'permissions.*.can_export' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'role_name' => $request->role_name,
                'role_name_en' => $request->role_name_en,
                'role_code' => strtoupper($request->role_code),
                'description' => $request->description,
                'level' => $request->level,

                'created_by' => auth()->id(),
            ]);

            // Attach permissions
            if ($request->has('permissions')) {
                foreach ($request->permissions as $permission) {
                    $role->permissions()->attach($permission['permission_id'], [
                        'can_create' => $permission['can_create'] ?? false,
                        'can_read' => $permission['can_read'] ?? false,
                        'can_update' => $permission['can_update'] ?? false,
                        'can_delete' => $permission['can_delete'] ?? false,
                        'can_approve' => $permission['can_approve'] ?? false,
                        'can_export' => $permission['can_export'] ?? false,
                    ]);
                }
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
        $permissions = Permission::where('is_active', true)->get()->groupBy('module');
        $role->load('permissions');
        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'role_name' => 'required|string|max:100',
            'role_name_en' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'level' => 'required|integer|min:0|max:100',
            // 'is_active' => 'nullable|boolean',
            'permissions' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $role->update([
                'role_name' => $request->role_name,
                'role_name_en' => $request->role_name_en,
                'description' => $request->description,
                'level' => $request->level,
                // 'is_active' => $request->has('is_active'),
            ]);

            // Sync permissions
            $role->permissions()->detach();
            if ($request->has('permissions')) {
                foreach ($request->permissions as $permission) {
                    $role->permissions()->attach($permission['permission_id'], [
                        'can_create' => $permission['can_create'] ?? false,
                        'can_read' => $permission['can_read'] ?? false,
                        'can_update' => $permission['can_update'] ?? false,
                        'can_delete' => $permission['can_delete'] ?? false,
                        'can_approve' => $permission['can_approve'] ?? false,
                        'can_export' => $permission['can_export'] ?? false,
                    ]);
                }
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
