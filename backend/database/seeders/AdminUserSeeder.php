<?php

namespace Database\Seeders;

use App\Enums\RoleNames;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use function Laravel\Prompts\info;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('backpack.base.admin_initial_login');
        $password = config('backpack.base.admin_initial_password');

        if (!isset($email) || !isset($password)) {
            throw new \InvalidArgumentException(
                'Check if both ADMIN_INITIAL_LOGIN and ADMIN_INITIAL_PASSWORD are set in .env'
            );
        }

        $user = User::firstWhere(User::EMAIL, $email);
        if ($user) {
            info("User with email {$email} already exists, skipping creation");
        } else {
            $user = User::create([
                User::NAME => 'Администратор',
                User::EMAIL => $email,
                User::PASSWORD => $password,
            ]);
        }

        $role = Role::findByName(RoleNames::ADMIN, 'web');
        $user->assignRole($role);
    }
}
