<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('partner_id')->constrained('partners')->restrictOnDelete();
            
            // ESTILO ODOO: Secuencia oficial de la factura (Ej: S-0001)
            $table->string('number')->unique()->nullable();
            
            // Referencia o nota corta (Ej: "Mensualidad Mayo", "Acuerdo de pago")
            $table->string('memo')->nullable(); 
            
            // Fechas clave
            $table->date('invoice_date');
            $table->date('due_date')->nullable(); // Vencimiento (para reportes de cartera)
            
            // Totales (Permite manejar descuentos o impuestos a futuro sin romper el sistema)
            $table->decimal('subtotal', 15, 2)->default(0); // Suma de las líneas
            $table->decimal('total_amount', 15, 2)->default(0); // Total a pagar
            
            // 🚀 ESTILO ODOO: Control de Cartera
            $table->decimal('amount_due', 15, 2)->default(0); // Cuánto falta por pagar
            
            $table->string('status')->default('quotation');
            $table->text('notes')->nullable(); // Términos y condiciones o notas largas
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};