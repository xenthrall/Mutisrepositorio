<?php

namespace Modules\Academic\App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum DocumentTypeEnum: string implements HasLabel, HasColor, HasIcon
{
    case CC = 'CC';           // Cédula de Ciudadanía
    case TI = 'TI';           // Tarjeta de Identidad
    case CE = 'CE';           // Cédula de Extranjería
    case PASSPORT = 'PASAPORTE';
    case NIT = 'NIT';         // Opcional: para empresas
    case OTHER = 'OTRO';      // Opcional: otros documentos

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CC => 'Cédula de Ciudadanía',
            self::TI => 'Tarjeta de Identidad',
            self::CE => 'Cédula de Extranjería',
            self::PASSPORT => 'Pasaporte',
            self::NIT => 'NIT',
            self::OTHER => 'Otro',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::CC, self::TI => 'primary',     // Documentos colombianos estándar
            self::CE, self::PASSPORT => 'info',   // Documentos internacionales
            self::NIT => 'warning',               // Documento empresarial
            self::OTHER => 'gray',                // Otros tipos
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::CC, self::TI => 'heroicon-m-identification',
            self::CE => 'heroicon-m-globe-alt',
            self::PASSPORT => 'heroicon-m-book-open',
            self::NIT => 'heroicon-m-building-office',
            self::OTHER => 'heroicon-m-document',
        };
    }

    /**
     * Validar si el número de documento es válido según el tipo
     */
    public function validateNumber(string $number): bool
    {
        return match ($this) {
            self::CC, self::TI, self::CE => preg_match('/^\d{6,12}$/', $number),
            self::PASSPORT => preg_match('/^[A-Z0-9]{6,12}$/', $number),
            self::NIT => preg_match('/^\d{1,9}(-\d{1})?$/', $number),
            self::OTHER => true,
        };
    }

    /**
     * Obtener formato de ejemplo para el documento
     */
    public function getExampleFormat(): string
    {
        return match ($this) {
            self::CC => '12345678',
            self::TI => '987654321',
            self::CE => 'ABC123456',
            self::PASSPORT => 'AB123456',
            self::NIT => '123456789-0',
            self::OTHER => 'Sin formato específico',
        };
    }
}
