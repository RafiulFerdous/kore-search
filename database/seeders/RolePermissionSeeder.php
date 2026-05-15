<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::findOrCreate('view dashboard');
        Permission::findOrCreate('manage courses');
        Permission::findOrCreate('manage users');
        Permission::findOrCreate('view orders');
        Permission::findOrCreate('purchase courses');

        $admin = Role::findOrCreate('admin');
        $admin->givePermissionTo(['view dashboard', 'manage courses', 'manage users', 'view orders']);

        $instructor = Role::findOrCreate('instructor');
        $instructor->givePermissionTo(['view dashboard', 'manage courses']);

        $student = Role::findOrCreate('student');
        $student->givePermissionTo(['purchase courses']);
    }
}
