<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'import_id',
    'sku',
    'ean',
    'stock_code',
    'supplier_raw',
    'supplier_name',
    'supplier_code',
    'product_name',
    'stock_s1',
    'stock_s2',
    'sales_period',
    'market_noa',
    'market_alza',
    'market_pr1',
    'market_pr2',
    'sales_p30',
    'sales_pop',
    'external_stock',
    'clearance_flag',
    'purchase_cost',
    'last_order_at',
    'product_age_days',
    'raw_payload',
])]
class ProductStatItem extends Model
{
    protected function casts(): array
    {
        return [
            'stock_s1' => 'integer',
            'stock_s2' => 'integer',
            'sales_period' => 'integer',
            'market_noa' => 'integer',
            'market_alza' => 'integer',
            'market_pr1' => 'integer',
            'market_pr2' => 'integer',
            'sales_p30' => 'integer',
            'sales_pop' => 'integer',
            'external_stock' => 'integer',
            'purchase_cost' => 'decimal:2',
            'last_order_at' => 'datetime',
            'product_age_days' => 'integer',
            'raw_payload' => 'array',
        ];
    }

    public function import(): BelongsTo
    {
        return $this->belongsTo(ProductStatImport::class, 'import_id');
    }
}
