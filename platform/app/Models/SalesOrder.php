<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'import_job_id',
    'sales_channel_id',
    'ordered_at',
    'order_code',
    'ext',
    'marketplace_display',
    'ext_id',
    'portal',
    'portal_country',
    'product_models',
    'gross_amount',
    'currency',
    'gross_amount_eur',
    'channel_type',
    'status',
    'shipping_method',
    'shipping_amount',
    'payment_method',
    'payment_amount',
    'invoice_number',
])]
class SalesOrder extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'ordered_at' => 'datetime',
            'gross_amount' => 'decimal:2',
            'gross_amount_eur' => 'decimal:2',
            'shipping_amount' => 'decimal:2',
            'payment_amount' => 'decimal:2',
        ];
    }

    public function importJob(): BelongsTo
    {
        return $this->belongsTo(ImportJob::class);
    }

    public function salesChannel(): BelongsTo
    {
        return $this->belongsTo(SalesChannel::class);
    }
}
