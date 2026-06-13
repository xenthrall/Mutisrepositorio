<?php

namespace Modules\Academic\App\Filament\Resources\Students\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Academic\App\Filament\Resources\Students\StudentResource;
use Modules\Core\App\Models\Partner;
use Modules\Academic\App\Enums\DocumentTypeEnum;
use Illuminate\Database\Eloquent\Model;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $partnerData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'document_type' => $data['document_type'] ?? DocumentTypeEnum::TI->value, 
            'document_number' => $data['document_number'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'is_student' => true,
        ];

        $partner = Partner::create($partnerData);

        return [
            'partner_id' => $partner->id,
            'guardian_id' => $data['guardian_id'] ?? null,
            'birth_date' => $data['birth_date'],
            'grade' => $data['grade'],
        ];
    }
}