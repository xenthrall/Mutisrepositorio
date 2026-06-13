<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_category_id')->constrained()->restrictOnDelete();
            $table->foreignId('journal_id')->constrained()->restrictOnDelete(); // De dónde salió la plata

            // El proveedor al que le pagamos (opcional, puede ser un gasto menor sin registro)
            $table->foreignId('partner_id')->nullable()->constrained('partners')->nullOnDelete();

            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->string('description'); // Concepto del gasto

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
