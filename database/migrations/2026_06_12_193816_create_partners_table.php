<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            
            // Datos Personales / Corporativos
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('company_name')->nullable(); // Para proveedores o clientes empresa
            
            // Documentación
            $table->string('document_type')->default('CC');
            $table->string('document_number')->unique();
            
            // Contacto y Ubicación
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            
            // Clasificación (La magia del patrón Partner)
            $table->boolean('is_student')->default(false);
            $table->boolean('is_guardian')->default(false);
            $table->boolean('is_teacher')->default(false);
            $table->boolean('is_customer')->default(false);
            $table->boolean('is_supplier')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};