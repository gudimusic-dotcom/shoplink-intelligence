<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'supplier_name',
    'source_name',
    'supplier_product_id',
    'ean',
    'qty_incoming',
    'raw_payload',
    'imported_by',
])]
class ProductStatIncomingItem extends Model
{
    protected function casts(): array
    {
        return [
            'qty_incoming' => 'integer',
            'raw_payload' => 'array',
        ];
    }

    public function importedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}
