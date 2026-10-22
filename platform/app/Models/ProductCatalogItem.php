<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['item_id', 'product_name', 'product_type', 'image_url'])]
class ProductCatalogItem extends Model
{
    use HasFactory;
}
