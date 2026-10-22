<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['currency', 'rate_to_eur', 'note'])]
class ExchangeRate extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'rate_to_eur' => 'decimal:8',
        ];
    }
}
