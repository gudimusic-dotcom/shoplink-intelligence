<?php

namespace Database\Factories;

use App\Models\ProductStatImport;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductStatImportFactory extends Factory
{
    protected $model = ProductStatImport::class;

    public function definition(): array
    {
        return [
            'source_name' => 'predaj.xlsx',
            'sheet_name' => 'Sheet1',
            'row_count' => 0,
            'imported_by' => null,
        ];
    }
}
