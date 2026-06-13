<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            $table->string('code')->nullable()->unique(); 
            
            $table->string('name'); // Ej: Pensión Octavo Grado, Sudadera Talla M
            $table->string('type'); // Guarda 'good' o 'service'
            
            $table->decimal('price', 15, 2)->default(0); // Valor base
            
            $table->boolean('is_active')->default(true); // Para ocultarlos si ya no se venden
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};