<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionController extends Controller
{
    /**
     * Hiển thị danh sách roles và permissions
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        $users = User::with('roles')->get();

        return view('backend.roles.index', ['pageTitle' => 'Roler manager'], compact('roles', 'permissions', 'users'));
    }

    /**
     * Tạo mới role
     */
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name'
        ]);

        Role::create(['name' => $request->name]);
        return redirect()->back()->with('success', 'Tạo vai trò thành công!');
    }

    /**
     * Tạo mới permission
     */
    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name'
        ]);

        Permission::create(['name' => $request->name]);
        return redirect()->back()->with('success', 'Tạo quyền thành công!');
    }

    /**
     * Gán quyền cho role
     */
    public function givePermissionToRole(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array'
        ]);

        $role = Role::findOrFail($request->role_id);

        if (!empty($request->permissions)) {
            foreach ($request->permissions as $perm) {
                $role->givePermissionTo($perm); // chỉ thêm, không xóa permission khác
            }
        }

        return redirect()->back()->with('success', 'Cập nhật quyền cho vai trò thành công!');
    }


    /**
     * Gán role cho user
     */
    public function assignRoleToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|exists:roles,name'
        ]);

        $user = User::findOrFail($request->user_id);
        $user->syncRoles([$request->role]);

        return redirect()->back()->with('success', 'Gán vai trò cho người dùng thành công!');
    }

    public function removePermissionFromRole(Request $request, $roleId)
    {
        $request->validate([
            'permission' => 'required|exists:permissions,name',
        ]);

        $role = Role::findOrFail($roleId);
        $role->revokePermissionTo($request->permission);

        return redirect()->back()->with('success', 'Đã xóa permission khỏi role thành công!');
    }

    // remove per from roles
    public function removePermission(Role $role, Permission $permission)
    {
        // Gỡ permission khỏi role
        $role->revokePermissionTo($permission);

        return response()->json([
            'success' => true,
            'message' => "Đã xóa quyền '{$permission->name}' khỏi role '{$role->name}'"
        ]);
    }
}
