<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'import_job_id',
    'sales_order_id',
    'ordered_at',
    'order_code',
    'portal',
    'portal_country',
    'brand',
    'model',
    'quantity',
    'category',
    'net_sales_amount',
    'purchase_amount',
    'margin_amount',
    'currency',
    'net_sales_amount_eur',
    'purchase_amount_eur',
    'margin_amount_eur',
    'channel_type',
    'ext',
    'marketplace_display',
    'status',
    'source_hash',
])]
class SalesOrderLine extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'ordered_at' => 'datetime',
            'quantity' => 'decimal:2',
            'net_sales_amount' => 'decimal:2',
            'purchase_amount' => 'decimal:2',
            'margin_amount' => 'decimal:2',
            'net_sales_amount_eur' => 'decimal:2',
            'purchase_amount_eur' => 'decimal:2',
            'margin_amount_eur' => 'decimal:2',
        ];
    }

    public function importJob(): BelongsTo
    {
        return $this->belongsTo(ImportJob::class);
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }
}
