<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'name',
    'type',
    'engine_type',
    'marketplace',
    'target_url',
    'tracked_field',
    'condition_operator',
    'check_interval_hours',
    'alert_email',
    'is_active',
    'should_create_task',
    'last_value',
    'last_checked_at',
    'last_triggered_at',
    'last_test_status',
    'last_test_message',
    'last_test_challenge',
    'notes',
    'created_by',
])]
class Monitor extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'should_create_task' => 'boolean',
            'last_value' => 'decimal:2',
            'last_checked_at' => 'datetime',
            'last_triggered_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
