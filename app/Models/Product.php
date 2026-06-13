<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Financial\App\Enums\ProductTypeEnum;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
        'price',
        'is_active',
    ];

    protected $casts = [
        'type' => ProductTypeEnum::class,
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}