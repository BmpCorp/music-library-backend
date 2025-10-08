<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Country;
use App\Services\OpenRouterService;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Artisan;
use function Laravel\Prompts\warning;

class ArtistAndAlbumSeeder extends Seeder
{
    public function run(): void
    {
        $prompt = 'Please generate a list of music artists in minified JSON format (20 of them is enough). ' .
            'The list should be presented as an array of objects with the the following fields: countryCode, name, albums. ' .
            '"countryCode" should be two-letter ISO 3166-1 alpha-2 code of the artist\'s country; ' .
            '"name" should be a title of the artist in English or Russian; ' .
            '"albums" should be an array of objects (1 to 4 are enough) with the following fields: title, genres. ' .
            'Here, "title" should be a title of the album; ' .
            '"genres" should be a list of the album\'s music genres (1 to 3) in English, separated by comma and space.';

        $response = (new OpenRouterService())->request($prompt, true);
        if (!json_validate($response)) {
            logger()->debug($response);
            warning('Empty or wrong response from AI, no seeding was performed. Please check logs');
            return;
        }

        $artists = json_decode($response, true);
        if (isset($artists['artists'])) {
            $artists = $artists['artists'];
        }

        $now = now()->toDateTimeString();

        // Here we make a dictionary of all countries to minimize further queries
        $countryMap = Country::pluck(Country::ID, Country::CODE);

        foreach ($artists as $entry) {
            $artist = Artist::create([
                Artist::TITLE => $entry['name'],
                Artist::DESCRIPTION => fake()->text(),
                Artist::COUNTRY_ID => $countryMap[$entry['countryCode']] ?? null,
            ]);

            $albumData = array_map(fn ($subEntry) => [
                Album::CREATED_AT => $now,
                Album::UPDATED_AT => $now,
                Album::TITLE => $subEntry['title'],
                Album::DESCRIPTION => fake()->text(),
                Album::GENRES => $subEntry['genres'],
                Album::ARTIST_ID => $artist->id,
                Album::YEAR => fake()->numberBetween(1980, 2025),
                Album::SONG_COUNT => fake()->numberBetween(4, 16),
                Album::HAS_EXPLICIT_LYRICS => rand(0, 10) < 2,
            ], $entry['albums']);
            Album::insert($albumData);

            // Make artist genres from its albums
            $allGenres = collect($entry['albums'])->pluck('genres')->join(', ');
            $artistGenres = implode(', ', array_unique(explode(', ', $allGenres)));

            $artist->update([Artist::GENRES => $artistGenres]);
        }

        // Index data after insert
        Artisan::call('scout:run');
    }
}
