<?php

namespace Modules\Financial\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\App\Models\Partner;
use Modules\Financial\App\Enums\InvoiceStatusEnum;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'partner_id',
        'number',
        'memo',
        'invoice_date',
        'due_date',
        'subtotal',
        'total_amount',
        'amount_due',
        'status',
        'notes'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'status' => InvoiceStatusEnum::class
    ];

    /**
     */
    protected static function boot()
    {
        parent::boot();

        // Antes de crear la factura en la base de datos...
        static::creating(function ($invoice) {
            // 1. Si no tiene número, generamos el consecutivo automático (S-0001)
            if (empty($invoice->number)) {
                $invoice->number = self::generateNextInvoiceNumber();
            }

            $invoice->subtotal ??= 0;
            $invoice->total_amount ??= 0;
            $invoice->amount_due ??= $invoice->total_amount;
        });
    }

    /**
     * Lógica para buscar la última factura y sumar 1 a la secuencia
     */
    public static function generateNextInvoiceNumber(): string
    {
        $prefix = 'S-';

        $lastInvoice = self::where('number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if (! $lastInvoice) {
            return $prefix . '0001';
        }
        $lastNumber = (int) Str::replaceFirst($prefix, '', $lastInvoice->number);
        $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . $nextNumber;
    }

    // --- Relaciones ---

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function lines()
    {
        return $this->hasMany(InvoiceLine::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
