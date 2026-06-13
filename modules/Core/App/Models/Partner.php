<?php

namespace Modules\Core\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use Modules\Academic\App\Enums\DocumentTypeEnum;

class Partner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'company_name',
        'document_type', 'document_number',
        'email', 'phone', 'address',
        'is_student', 'is_guardian', 'is_teacher', 'is_customer', 'is_supplier'
    ];

    protected $casts = [
        'is_student' => 'boolean',
        'is_guardian' => 'boolean',
        'is_teacher' => 'boolean',
        'is_customer' => 'boolean',
        'is_supplier' => 'boolean',
        'document_type' => DocumentTypeEnum::class,
    ];

    /**
     * Accessor para obtener el nombre a mostrar (Persona o Empresa)
     */
    public function getFullNameAttribute(): string
    {
        if ($this->company_name) {
            return $this->company_name;
        }
        
        return trim("{$this->first_name} {$this->last_name}");
    }

    // Un Partner puede tener un Usuario para loguearse
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    // Relación hacia el dominio académico (Si el Partner es estudiante)
    public function studentProfile(): HasOne
    {
        return $this->hasOne(\Modules\Academic\App\Models\Student::class, 'partner_id');
    }
    
    // Relación hacia el dominio académico (Si el Partner es acudiente y tiene estudiantes a cargo)
    public function dependentStudents(): HasMany
    {
        return $this->hasMany(\Modules\Academic\App\Models\Student::class, 'guardian_id');
    }
}