<?php

namespace Database\Seeders;

use App\Enums\PermissionCode;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\RoleNames;
use Backpack\PermissionManager\app\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use function Laravel\Prompts\warning;

class PermissionAndRoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPermissions();
        $this->seedRoles();
    }

    private function seedPermissions(): void
    {
        if (Permission::whereGuardName('web')->exists()) {
            warning('Permission table is filled already. New permissions were not created');
            return;
        }

        $now = now()->toDateTimeString();
        $codes = PermissionCode::getValues();

        $entries = array_map(fn ($code) => [
            'name' => $code,
            'guard_name' => 'web',
            'created_at' => $now,
            'updated_at' => $now,
        ], $codes);

        Permission::insert($entries);
    }

    private function seedRoles(): void
    {
        if (Role::whereGuardName('web')->exists()) {
            warning('Roles table is filled already. New roles were not created');
            return;
        }

        $adminRole = Role::create([
            'name' => RoleNames::ADMIN,
            'guard_name' => 'web',
        ]);
        $adminPermission = Permission::findByName(PermissionCode::FULL_ACCESS, 'web');
        $adminRole->givePermissionTo($adminPermission);
    }
}
