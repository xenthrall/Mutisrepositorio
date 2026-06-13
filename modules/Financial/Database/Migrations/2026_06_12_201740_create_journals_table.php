<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            
            $table->string('name'); // Ej: Caja Principal, Cuenta Bancolombia
            $table->string('type'); // Guarda 'cash' o 'bank'
            $table->string('titular')->nullable(); // Ej: Institución Educativa Mutis
            
            // Usamos COP por defecto como solicitó el cliente
            $table->string('currency')->default('COP'); 
            
            // Decimal (15,2) es el estándar contable para manejar hasta billones con 2 centavos
            $table->decimal('balance', 15, 2)->default(0); 
            
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};