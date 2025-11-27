<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::with('roles')->paginate(20);
        $modules = Permission::distinct()->pluck('group_name');
        return view('permissions.index', compact('permissions', 'modules'));
    }

    public function create()
    {
        $modules = Permission::distinct()->pluck('module');
        return view('permissions.create', compact('modules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'permission_name' => 'required|string|max:100',
            'permission_name_en' => 'nullable|string|max:100',
            'permission_code' => 'required|string|max:50|unique:permissions,permission_code',
            'module' => 'required|string|max:50',
            'description' => 'nullable|string',
        ]);

        Permission::create([
            'permission_name' => $request->permission_name,
            'permission_name_en' => $request->permission_name_en,
            'permission_code' => strtoupper($request->permission_code),
            'module' => $request->module,
            'description' => $request->description,
            'is_system' => false,
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('permissions.index')->with('success', 'تم إنشاء الصلاحية بنجاح');
    }

    public function edit(Permission $permission)
    {
        if ($permission->is_system) {
            return back()->with('error', 'لا يمكن تعديل صلاحية النظام');
        }

        $modules = Permission::distinct()->pluck('module');
        return view('permissions.edit', compact('permission', 'modules'));
    }

    public function update(Request $request, Permission $permission)
    {
        if ($permission->is_system) {
            return back()->with('error', 'لا يمكن تعديل صلاحية النظام');
        }

        $request->validate([
            'permission_name' => 'required|string|max:100',
            'permission_name_en' => 'nullable|string|max:100',
            'module' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $permission->update([
            'permission_name' => $request->permission_name,
            'permission_name_en' => $request->permission_name_en,
            'module' => $request->module,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('permissions.index')->with('success', 'تم تحديث الصلاحية بنجاح');
    }

    public function destroy(Permission $permission)
    {
        if ($permission->is_system) {
            return back()->with('error', 'لا يمكن حذف صلاحية النظام');
        }

        if ($permission->roles()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الصلاحية لأنها مرتبطة بأدوار');
        }

        $permission->delete();

        return redirect()->route('permissions.index')->with('success', 'تم حذف الصلاحية بنجاح');
    }
}
