<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Financial\App\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Nómina Docente',
                'description' => 'Salarios y prestaciones sociales del personal docente.',
            ],
            [
                'name' => 'Nómina Administrativa',
                'description' => 'Salarios y prestaciones del personal administrativo.',
            ],
            [
                'name' => 'Servicios Públicos',
                'description' => 'Pago de energía, agua, internet, telefonía y otros servicios.',
            ],
            [
                'name' => 'Mantenimiento e Infraestructura',
                'description' => 'Reparaciones, adecuaciones y mantenimiento de instalaciones.',
            ],
            [
                'name' => 'Material Pedagógico',
                'description' => 'Compra de libros, guías, material didáctico y recursos educativos.',
            ],
            [
                'name' => 'Tecnología y Sistemas',
                'description' => 'Equipos de cómputo, licencias de software y soporte tecnológico.',
            ],
            [
                'name' => 'Papelería y Útiles',
                'description' => 'Insumos de oficina y materiales de uso institucional.',
            ],
            [
                'name' => 'Transporte Escolar',
                'description' => 'Gastos relacionados con rutas y transporte estudiantil.',
            ],
            [
                'name' => 'Alimentación y Cafetería',
                'description' => 'Compra de alimentos e insumos para cafetería o restaurante escolar.',
            ],
            [
                'name' => 'Actividades Académicas',
                'description' => 'Ferias, proyectos, salidas pedagógicas y eventos académicos.',
            ],
            [
                'name' => 'Actividades Deportivas',
                'description' => 'Torneos, implementos deportivos y eventos recreativos.',
            ],
            [
                'name' => 'Seguridad y Vigilancia',
                'description' => 'Servicios de vigilancia, monitoreo y seguridad institucional.',
            ],
            [
                'name' => 'Seguros',
                'description' => 'Pólizas institucionales y seguros estudiantiles.',
            ],
            [
                'name' => 'Capacitación y Formación',
                'description' => 'Cursos, seminarios y programas de formación para el personal.',
            ],
            [
                'name' => 'Impuestos y Tasas',
                'description' => 'Pagos tributarios y obligaciones legales.',
            ],
            [
                'name' => 'Gastos Bancarios',
                'description' => 'Comisiones, cuotas de manejo y servicios financieros.',
            ],
            [
                'name' => 'Honorarios Profesionales',
                'description' => 'Pagos a asesores, consultores y profesionales externos.',
            ],
            [
                'name' => 'Bienestar Estudiantil',
                'description' => 'Programas de apoyo, orientación y bienestar para estudiantes.',
            ],
            [
                'name' => 'Eventos Institucionales',
                'description' => 'Ceremonias, celebraciones y actividades institucionales.',
            ],
            [
                'name' => 'Otros Gastos Operativos',
                'description' => 'Gastos generales no clasificados en otras categorías.',
            ],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate(
                ['name' => $category['name']],
                [
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}