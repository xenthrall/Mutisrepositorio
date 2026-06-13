<?php

namespace Modules\Financial\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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

    /**
     * Relación: Un producto puede estar presente en muchas líneas de detalle de factura.
     */
    public function invoiceLines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }

    /**
     * Relación (Atajo): Obtiene directamente todas las facturas en las que se vendió este producto.
     * Ideal para graficar ventas por producto sin hacer JOINs complejos manualmente.
     */
    public function invoices(): HasManyThrough
    {
        return $this->hasManyThrough(
            Invoice::class,       // Modelo final al que queremos llegar
            InvoiceLine::class,   // Modelo intermedio por el que pasamos
            'product_id',         // Llave foránea en el modelo intermedio (invoice_lines)
            'id',                 // Llave foránea en el modelo final (invoices)
            'id',                 // Llave local en este modelo (products)
            'invoice_id'          // Llave local en el modelo intermedio (invoice_lines)
        );
    }
}