<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\User;
use App\Models\UserFavoriteArtist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory(20)->create();
        $artists = Artist::get();
        $now = now()->toDateTimeString();

        $userFavoriteArtists = [];
        foreach ($users as $user) {
            $randomCount = rand(1, 6);
            $selectedArtists = $artists->random($randomCount);

            foreach ($selectedArtists as $artist) {
                $userFavoriteArtists[] = [
                    UserFavoriteArtist::USER_ID => $user->id,
                    UserFavoriteArtist::ARTIST_ID => $artist->id,
                    UserFavoriteArtist::CREATED_AT => $now,
                    UserFavoriteArtist::UPDATED_AT => $now,
                    UserFavoriteArtist::LAST_CHECKED_ALBUM => $artist->albums->random()->id,
                    UserFavoriteArtist::LISTENING_NOW => rand(0, 10) < 2,
                ];
            }
        }

        UserFavoriteArtist::insert($userFavoriteArtists);
    }
}
