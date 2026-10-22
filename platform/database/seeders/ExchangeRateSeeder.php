<?php

namespace Database\Seeders;

use App\Models\ExchangeRate;
use Illuminate\Database\Seeder;

class ExchangeRateSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            ['currency' => '€', 'rate_to_eur' => 1, 'note' => 'Base currency'],
            ['currency' => 'Kč', 'rate_to_eur' => null, 'note' => 'Set manually in Sales Intelligence settings'],
            ['currency' => 'Ft', 'rate_to_eur' => null, 'note' => 'Set manually in Sales Intelligence settings'],
            ['currency' => 'Zł', 'rate_to_eur' => null, 'note' => 'Set manually in Sales Intelligence settings'],
            ['currency' => 'lei', 'rate_to_eur' => null, 'note' => 'Set manually in Sales Intelligence settings'],
        ];

        foreach ($rates as $rate) {
            ExchangeRate::query()->updateOrCreate(
                ['currency' => $rate['currency']],
                $rate,
            );
        }
    }
}
