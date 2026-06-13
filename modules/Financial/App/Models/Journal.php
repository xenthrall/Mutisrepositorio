<?php

namespace Modules\Financial\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    /**
     * Relación: Un diario (caja/banco) recibe muchos pagos de facturas (Ingresos).
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Relación: De un diario (caja/banco) salen los fondos para muchos gastos (Egresos).
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}