<?php

namespace Database\Seeders;

use App\Models\SalesChannel;
use Illuminate\Database\Seeder;

class SalesChannelSeeder extends Seeder
{
    public function run(): void
    {
        $channels = [
            ['ext_code' => null, 'name' => 'MobileGo legacy web', 'type' => 'own_web', 'country_code' => null],
            ['ext_code' => 'Mobilego.sk', 'name' => 'MobileGo Upgates SK', 'type' => 'own_web', 'country_code' => 'SK'],
            ['ext_code' => 'ALZA.CZ', 'name' => 'Alza CZ', 'type' => 'marketplace', 'country_code' => 'CZ'],
            ['ext_code' => 'MALL', 'name' => 'MALL', 'type' => 'marketplace', 'country_code' => null],
            ['ext_code' => 'ALLEGRO.PL', 'name' => 'Allegro PL', 'type' => 'marketplace', 'country_code' => 'PL'],
            ['ext_code' => 'ALLEGRO.CZ', 'name' => 'Allegro CZ', 'type' => 'marketplace', 'country_code' => 'CZ'],
            ['ext_code' => 'ALLEGRO.SK', 'name' => 'Allegro SK', 'type' => 'marketplace', 'country_code' => 'SK'],
            ['ext_code' => 'ALLEGRO.HU', 'name' => 'Allegro HU', 'type' => 'marketplace', 'country_code' => 'HU'],
            ['ext_code' => 'KAUFLAND.DE', 'name' => 'Kaufland DE', 'type' => 'marketplace', 'country_code' => 'DE'],
            ['ext_code' => 'KAUFLAND.SK', 'name' => 'Kaufland SK', 'type' => 'marketplace', 'country_code' => 'SK'],
            ['ext_code' => 'KAUFLAND.CZ', 'name' => 'Kaufland CZ', 'type' => 'marketplace', 'country_code' => 'CZ'],
        ];

        foreach ($channels as $channel) {
            SalesChannel::query()->updateOrCreate(
                ['ext_code' => $channel['ext_code']],
                $channel + ['is_active' => true],
            );
        }
    }
}
