<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Danh sách quyền
        $permissions = [
            'view swap',
            'edit swap',
            'delete swap',
            'import swap',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Tạo vai trò admin
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Vai trò editor
        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $editorRole->givePermissionTo(['view swap', 'edit swap']);

        // Gán role cho user đầu tiên (ví dụ)
        $user = \App\Models\User::first();
        if ($user) {
            $user->assignRole('admin');
        }
    }
}
