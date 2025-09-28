<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionAndRoleSeeder::class,
            AdminUserSeeder::class,
        ]);
    }

    public function mock(): void
    {
        $this->call([
            CountrySeeder::class,
            ArtistAndAlbumSeeder::class,
            UserSeeder::class,
        ]);
    }
}
