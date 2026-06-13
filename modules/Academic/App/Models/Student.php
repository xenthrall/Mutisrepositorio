<?php

namespace Modules\Academic\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Modules\Academic\App\Enums\DocumentTypeEnum;
use Modules\Academic\App\Enums\StudentGradeEnum;
use Modules\Core\App\Models\Partner;


class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'partner_id',
        'guardian_id',
        'birth_date',
        'grade',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'grade' => StudentGradeEnum::class,
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'guardian_id');
    }

    public function getAgeAttribute(): int
    {
        return Carbon::parse($this->birth_date)->age;
    }
}
