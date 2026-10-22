<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable([
    'case_number',
    'order_reference',
    'product_sku',
    'customer_reference',
    'status',
    'reason_code',
    'resolution_type',
    'opened_at',
    'deadline_at',
    'assigned_user_id',
    'notes',
])]
class Rma extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'deadline_at' => 'datetime',
        ];
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'source');
    }
}
