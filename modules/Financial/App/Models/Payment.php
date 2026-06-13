<?php

namespace Modules\Financial\App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;
    protected $fillable = ['invoice_id', 'journal_id', 'amount', 'payment_date', 'memo'];
    protected $casts = ['amount' => 'decimal:2', 'payment_date' => 'date'];

    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function journal() { return $this->belongsTo(Journal::class); }
}