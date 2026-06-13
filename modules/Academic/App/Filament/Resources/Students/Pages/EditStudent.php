<?php

namespace Modules\Academic\App\Filament\Resources\Students\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Academic\App\Filament\Resources\Students\StudentResource;
use Illuminate\Database\Eloquent\Model;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    /**
     * Llena el formulario trayendo los datos desde la relación Partner.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $student = $this->getRecord();
        $partner = $student->partner;

        if ($partner) {
            // Inyectamos los datos del Partner en el array que llena el formulario
            $data['first_name'] = $partner->first_name;
            $data['last_name'] = $partner->last_name;
            $data['document_type'] = $partner->document_type instanceof \UnitEnum ? $partner->document_type->value : $partner->document_type;
            $data['document_number'] = $partner->document_number;
            $data['phone'] = $partner->phone;
            $data['email'] = $partner->email;
        }

        return $data;
    }

    /**
     * Intercepta el guardado para actualizar el Partner antes de guardar el Student.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if ($record->partner) {
            $record->partner->update([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'document_type' => $data['document_type'],
                'document_number' => $data['document_number'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
            ]);
        }

        $studentData = [
            'guardian_id' => $data['guardian_id'] ?? null,
            'birth_date' => $data['birth_date'],
            'grade' => $data['grade'],
        ];

        $record->update($studentData);

        return $record;
    }
}