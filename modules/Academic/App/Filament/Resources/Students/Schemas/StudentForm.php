<?php

namespace Modules\Academic\App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Modules\Academic\App\Enums\StudentGradeEnum;
use Modules\Academic\App\Enums\DocumentTypeEnum;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Información Personal')
                    ->icon('heroicon-m-user')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('first_name')
                                ->label('Nombres')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('last_name')
                                ->label('Apellidos')
                                ->required()
                                ->maxLength(255),

                            DatePicker::make('birth_date')
                                ->label('Fecha de Nacimiento')
                                ->required()
                                ->maxDate(now())
                                ->native(false)
                                ->displayFormat('d/m/Y'),
                        ]),
                    ]),

                Section::make('Documentación')
                    ->icon('heroicon-m-identification')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('document_type')
                                ->label('Tipo de Documento')
                                ->options(DocumentTypeEnum::class)
                                ->default(DocumentTypeEnum::TI->value)
                                ->required()
                                ->native(false),

                            TextInput::make('document_number')
                                ->label('Número de Documento')
                                ->required()
                                ->unique(
                                    table: 'partners',
                                    column: 'document_number',
                                    ignorable: fn($record) => $record?->partner
                                )
                                ->maxLength(255),
                        ]),
                    ]),

                Section::make('Información Académica y Familiar')
                    ->icon('heroicon-m-academic-cap')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('grade')
                                ->label('Grado a Cursar')
                                ->options(StudentGradeEnum::class)
                                ->required()
                                ->native(false)
                                ->searchable(),

                            Select::make('guardian_id')
                                ->label('Acudiente (Opcional)')
                                ->relationship('guardian', 'document_number')
                                ->getOptionLabelFromRecordUsing(fn($record) => "{$record->full_name} ({$record->document_number})")
                                ->searchable(['first_name', 'last_name', 'document_number', 'email'])
                                ->preload()
                                ->nullable()
                                ->placeholder('Seleccione un acudiente o deje vacío')
                                ->helperText('Opcional - Puede asignar el acudiente más tarde'),
                        ]),
                    ]),

                Section::make('Contacto')
                    ->icon('heroicon-m-phone')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('phone')
                                ->label('Teléfono / Celular')
                                ->tel()
                                ->maxLength(255)
                                ->nullable(),

                            TextInput::make('email')
                                ->label('Correo Electrónico')
                                ->email()
                                ->unique(
                                    table: 'partners',
                                    column: 'email',
                                    ignorable: fn($record) => $record?->partner
                                )
                                ->maxLength(255)
                                ->nullable(),
                        ]),
                    ])
                    ->collapsed(),
            ]);
    }
}
