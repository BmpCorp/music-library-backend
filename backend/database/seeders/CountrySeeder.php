<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Services\OpenRouterService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        if (Country::exists()) {
            info('Countries already exist');
            return;
        }

        $prompt = 'Please generate a list of countries in minified JSON format. ' .
            'The list should be presented as an array of objects with the following fields: isoCode, name. ' .
            'The values of these fields should be, respectively: ' .
            'two-letter ISO 3166-1 alpha-2 code of the country; name of the country in Russian.';

        $response = (new OpenRouterService())->request($prompt, true);
        $countries = json_decode($response, true);
        if (!$countries) {
            logger()->debug($response);
            warning('Empty or wrong response from AI, no seeding was performed. Please check logs');
            return;
        }

        $now = now()->toDateTimeString();
        $data = array_map(fn ($entry) => [
            Country::CREATED_AT => $now,
            Country::UPDATED_AT => $now,
            Country::CODE => $entry['isoCode'],
            Country::NAME => $entry['name'],
        ], $countries);

        Country::insert($data);
    }
}
