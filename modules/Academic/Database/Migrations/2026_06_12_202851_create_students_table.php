<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // Relación con el Partner que ES el estudiante
            $table->foreignId('partner_id')->constrained('partners')->restrictOnDelete();
            
            // Relación con el Partner que ES el acudiente
            $table->foreignId('guardian_id')->nullable()->constrained('partners')->restrictOnDelete();

            $table->date('birth_date');
            $table->string('grade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
