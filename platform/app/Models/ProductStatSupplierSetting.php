<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'supplier_name',
    'portal_url_template',
    'stock_feed_url',
    'incoming_feed_url',
    'target_coverage_days',
])]
class ProductStatSupplierSetting extends Model
{
    protected function casts(): array
    {
        return [
            'target_coverage_days' => 'integer',
        ];
    }
}
