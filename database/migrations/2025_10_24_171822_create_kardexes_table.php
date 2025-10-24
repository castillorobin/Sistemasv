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
        Schema::create('kardexes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->date('fecha');
            $table->string('tipo_movimiento'); // Compra, Venta, Ajuste, etc.
            $table->string('documento_referencia')->nullable();
            $table->integer('entrada')->default(0);
            $table->integer('cantidad')->default(0);
            $table->integer('stock_final')->default(0);
            $table->integer('salida')->default(0);
            $table->integer('saldo')->default(0);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kardexes');
    }
};
