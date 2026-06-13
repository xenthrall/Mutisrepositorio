<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Financial\App\Models\Journal;
use Modules\Financial\App\Enums\JournalTypeEnum;

class JournalSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar journals existentes (opcional, para pruebas)
        // Journal::truncate();

        // Diario 1: Caja Principal (Efectivo)
        Journal::updateOrCreate(
            ['name' => 'Caja Principal'],
            [
                'type' => JournalTypeEnum::Cash,
                'titular' => 'Administración Central',
                'currency' => 'COP',
                'balance' => 5000000.00,
                'is_active' => true,
            ]
        );

        // Diario 2: Banco Comercial (Cuenta Corriente)
        Journal::updateOrCreate(
            ['name' => 'Bancolombia - Cuenta Corriente'],
            [
                'type' => JournalTypeEnum::Bank,
                'titular' => 'Colegio Sistemas',
                'currency' => 'COP',
                'balance' => 12500000.00,
                'is_active' => true,
            ]
        );

        // Diario 3: Caja Menor (Gastos menores)
        Journal::updateOrCreate(
            ['name' => 'Caja Menor'],
            [
                'type' => JournalTypeEnum::Cash,
                'titular' => 'Coordinación Financiera',
                'currency' => 'COP',
                'balance' => 500000.00,
                'is_active' => true,
            ]
        );

        $this->command->info('✅ 3 journals creados exitosamente para el sistema escolar');
    }
}