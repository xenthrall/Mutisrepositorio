<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Financial\App\Enums\JournalTypeEnum;

class Journal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'titular',
        'currency',
        'balance',
        'is_active',
    ];

    protected $casts = [
        'type' => JournalTypeEnum::class,
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}