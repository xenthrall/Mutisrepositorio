<?php

namespace Modules\Academic\App\Filament\Resources\Students\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Modules\Academic\App\Enums\DocumentTypeEnum;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Tipo de Documento (Apunta a partner)
                TextColumn::make('partner.document_type')
                    ->label('Tipo Doc.')
                    ->formatStateUsing(fn ($state): string => $state instanceof DocumentTypeEnum 
                        ? $state->getLabel() 
                        : (string) $state)
                    ->badge()
                    ->color(fn ($state): string => $state instanceof DocumentTypeEnum 
                        ? $state->getColor() 
                        : 'gray')
                    ->icon(fn ($state): ?string => $state instanceof DocumentTypeEnum 
                        ? $state->getIcon() 
                        : null)
                    ->sortable(),

                // 2. Número de Documento (Apunta a partner)
                TextColumn::make('partner.document_number')
                    ->label('Número')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Número de documento copiado')
                    ->icon('heroicon-m-identification'),

                // 3. Nombre completo (Apunta al accessor en partner y busca en sus columnas)
                TextColumn::make('partner.full_name')
                    ->label('Estudiante')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name'])
                    ->weight('bold')
                    ->icon('heroicon-m-user'),

                // 4. Grado (Sigue en student)
                TextColumn::make('grade')
                    ->label('Grado')
                    ->badge()
                    ->sortable(),

                // 5. Edad y fecha de nacimiento (Sigue en student)
                TextColumn::make('birth_date')
                    ->label('Edad')
                    ->formatStateUsing(fn ($record): string => "{$record->age} años")
                    ->description(fn ($record): string => $record->birth_date ? $record->birth_date->format('d/m/Y') : '')
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('birth_date', $direction);
                    }),

                // 6. Acudiente (Apunta a la relación guardian, que a su vez es un partner)
                TextColumn::make('guardian.full_name')
                    ->label('Acudiente')
                    ->icon('heroicon-m-user-group')
                    ->placeholder('Sin acudiente')
                    ->toggleable(),

                // 7. Teléfono (Apunta a partner)
                TextColumn::make('partner.phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('No registrado')
                    ->icon('heroicon-m-phone'),

                // 8. Email (Apunta a partner)
                TextColumn::make('partner.email')
                    ->label('Correo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('No registrado')
                    ->icon('heroicon-m-envelope')
                    ->copyable()
                    ->copyMessage('Correo copiado'),

                // 9. Fecha de registro (Sigue en student)
                TextColumn::make('created_at')
                    ->label('Registro')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(12)
            ->paginationPageOptions([12, 24, 50, 'all'])
            ->striped(); 
    }
}