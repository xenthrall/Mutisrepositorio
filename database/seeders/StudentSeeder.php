<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\App\Models\Partner;
use Modules\Academic\App\Models\Student;
use Modules\Academic\App\Enums\StudentGradeEnum;
use Modules\Academic\App\Enums\DocumentTypeEnum;
use Carbon\Carbon;
use Faker\Factory as Faker;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_ES');
        
        // 1. Crear acudientes (Partners con is_guardian = true) si no existen
        if (Partner::where('is_guardian', true)->count() === 0) {
            $this->command->info('Creando 30 acudientes (Partners)...');
            $progressBar = $this->command->getOutput()->createProgressBar(30);
            
            for ($i = 0; $i < 30; $i++) {
                $hasPhone = $faker->boolean(80);
                $hasEmail = $faker->boolean(70);
                
                Partner::create([
                    'first_name' => $faker->firstName(),
                    'last_name' => $faker->lastName() . ' ' . $faker->lastName(),
                    // Si tienes el Enum DocumentTypeEnum implementado, extraemos un caso aleatorio
                    'document_type' => $faker->randomElement(DocumentTypeEnum::cases()),
                    'document_number' => $faker->unique()->numerify('##########'),
                    'phone' => $hasPhone ? $faker->numerify('3#########') : null,
                    'email' => $hasEmail ? $faker->unique()->safeEmail() : null,
                    'is_guardian' => true, // Identificador clave para tu nueva arquitectura
                ]);
                
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->command->newLine();
            $this->command->info('✅ 30 acudientes creados');
        }
        
        // Obtener los acudientes para asignarlos aleatoriamente
        $guardians = Partner::where('is_guardian', true)->get();
        
        // 2. Crear estudiantes (Partners + registro en tabla students)
        $this->command->info('Creando 100 estudiantes...');
        $progressBar = $this->command->getOutput()->createProgressBar(100);
        
        for ($i = 0; $i < 100; $i++) {
            $birthDate = $faker->dateTimeBetween('2010-01-01', '2018-12-31');
            $age = Carbon::parse($birthDate)->age;
            $hasPhone = $faker->boolean(70);
            $hasEmail = $faker->boolean(60);
            
            // Primero creamos el "Tercero" o "Partner" que representa físicamente al estudiante
            $studentPartner = Partner::create([
                'first_name' => $faker->firstName(),
                'last_name' => $faker->lastName() . ' ' . $faker->lastName(),
                'document_type' => DocumentTypeEnum::TI, // Los estudiantes suelen tener TI por defecto
                'document_number' => $faker->unique()->numerify('##########'),
                'phone' => $hasPhone ? $faker->numerify('3#########') : null,
                'email' => $hasEmail ? $faker->unique()->safeEmail() : null,
                'is_student' => true, // Lo clasificamos
            ]);

            // Luego creamos el perfil estrictamente académico ligado a ese Partner
            Student::create([
                'partner_id' => $studentPartner->id,
                'guardian_id' => $faker->boolean(80) ? $guardians->random()->id : null,
                'birth_date' => $birthDate,
                'grade' => $this->getGradeByAge($age), // Ahora devuelve el Enum
            ]);
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->command->newLine(2);
        $this->command->info('✅ 100 estudiantes creados exitosamente bajo el patrón Partner!');
        
        // 3. Mostrar estadísticas
        $this->command->newLine();
        $this->command->info('📊 Estadísticas generadas:');
        $this->command->line('  • Total estudiantes (Perfiles): ' . Student::count());
        $this->command->line('  • Estudiantes con acudiente asignado: ' . Student::whereNotNull('guardian_id')->count());
        $this->command->line('  • Estudiantes sin acudiente: ' . Student::whereNull('guardian_id')->count());
        $this->command->line('  • Total acudientes en base de datos: ' . Partner::where('is_guardian', true)->count());
        $this->command->line('  • Total de Partners (Terceros) en el sistema: ' . Partner::count());
    }
    
    /**
     * Retorna el caso correspondiente del BackedEnum en base a la edad
     */
    private function getGradeByAge(int $age): StudentGradeEnum
    {
        return match(true) {
            $age <= 5 => StudentGradeEnum::First,
            $age == 6 => StudentGradeEnum::Second,
            $age == 7 => StudentGradeEnum::Third,
            $age == 8 => StudentGradeEnum::Fourth,
            $age == 9 => StudentGradeEnum::Fifth,
            $age == 10 => StudentGradeEnum::Sixth,
            $age == 11 => StudentGradeEnum::Seventh,
            $age == 12 => StudentGradeEnum::Eighth,
            $age == 13 => StudentGradeEnum::Ninth,
            $age == 14 => StudentGradeEnum::Tenth,
            $age >= 15 => StudentGradeEnum::Eleventh,
            default => StudentGradeEnum::First,
        };
    }
}