<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'supplier_name',
    'supplier_code',
    'stock_status',
    'stock_quantity',
    'source_name',
    'checked_at',
])]
class ProductStatSupplierStock extends Model
{
    protected function casts(): array
    {
        return [
            'checked_at' => 'datetime',
            'stock_quantity' => 'float',
        ];
    }
}
