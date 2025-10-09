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
        Schema::table('compras', function (Blueprint $table) {
            $table->foreignId('proveedor_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
   
public function down()
{
    Schema::table('compras', function (Blueprint $table) {
        $table->dropForeign(['proveedor_id']);
        $table->dropColumn('proveedor_id');
    });
}
};
