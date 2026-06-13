<?php

namespace Modules\Financial\App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\App\Models\Partner;

class Expense extends Model
{
    use SoftDeletes;
    protected $fillable = ['expense_category_id', 'journal_id', 'partner_id', 'amount', 'expense_date', 'description'];
    protected $casts = ['amount' => 'decimal:2', 'expense_date' => 'date'];

    public function category() { return $this->belongsTo(ExpenseCategory::class, 'expense_category_id'); }
    public function journal() { return $this->belongsTo(Journal::class); }
    public function partner() { return $this->belongsTo(Partner::class); }
}