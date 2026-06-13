<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Financial\App\Models\Product;
use Modules\Financial\App\Enums\ProductTypeEnum;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Productos físicos
            [
                'code' => 'PRD-001',
                'name' => 'Uniforme de Educación Física',
                'type' => ProductTypeEnum::Good,
                'price' => 85000.00,
                'is_active' => true,
            ],
            [
                'code' => 'PRD-002',
                'name' => 'Agenda Institucional',
                'type' => ProductTypeEnum::Good,
                'price' => 25000.00,
                'is_active' => true,
            ],
            [
                'code' => 'PRD-003',
                'name' => 'Carnet Estudiantil',
                'type' => ProductTypeEnum::Good,
                'price' => 12000.00,
                'is_active' => true,
            ],
            [
                'code' => 'PRD-004',
                'name' => 'Kit Escolar Básico',
                'type' => ProductTypeEnum::Good,
                'price' => 45000.00,
                'is_active' => true,
            ],
            [
                'code' => 'PRD-005',
                'name' => 'Camibuso Institucional',
                'type' => ProductTypeEnum::Good,
                'price' => 70000.00,
                'is_active' => true,
            ],
            [
                'code' => 'PRD-006',
                'name' => 'Silla Plástica',
                'type' => ProductTypeEnum::Good,
                'price' => 28000.00,
                'is_active' => true,
            ],

            // Servicios
            [
                'code' => 'SRV-001',
                'name' => 'Matrícula Académica',
                'type' => ProductTypeEnum::Service,
                'price' => 120000.00,
                'is_active' => true,
            ],
            [
                'code' => 'SRV-002',
                'name' => 'Pensión Mensual',
                'type' => ProductTypeEnum::Service,
                'price' => 95000.00,
                'is_active' => true,
            ],
            [
                'code' => 'SRV-003',
                'name' => 'Derechos de Grado',
                'type' => ProductTypeEnum::Service,
                'price' => 180000.00,
                'is_active' => true,
            ],
            [
                'code' => 'SRV-004',
                'name' => 'Certificado de Estudio',
                'type' => ProductTypeEnum::Service,
                'price' => 15000.00,
                'is_active' => true,
            ],
            [
                'code' => 'SRV-005',
                'name' => 'Curso de Refuerzo',
                'type' => ProductTypeEnum::Service,
                'price' => 60000.00,
                'is_active' => true,
            ],
            [
                'code' => 'SRV-006',
                'name' => 'Recargo por Mora',
                'type' => ProductTypeEnum::Service,
                'price' => 10000.00,
                'is_active' => true,
            ],

            // Más datos de prueba
            [
                'code' => 'PRD-007',
                'name' => 'Cuaderno Rayado',
                'type' => ProductTypeEnum::Good,
                'price' => 6500.00,
                'is_active' => true,
            ],
            [
                'code' => 'PRD-008',
                'name' => 'Lápiz HB',
                'type' => ProductTypeEnum::Good,
                'price' => 1500.00,
                'is_active' => true,
            ],
            [
                'code' => 'SRV-007',
                'name' => 'Mensualidad Transporte Escolar',
                'type' => ProductTypeEnum::Service,
                'price' => 80000.00,
                'is_active' => true,
            ],
            [
                'code' => 'SRV-008',
                'name' => 'Inscripción de Curso Vacacional',
                'type' => ProductTypeEnum::Service,
                'price' => 40000.00,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['code' => $product['code']],
                $product
            );
        }
    }
}