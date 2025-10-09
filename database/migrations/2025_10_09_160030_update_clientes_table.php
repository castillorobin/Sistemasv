<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Eliminar columnas antiguas si ya no se usarán
            $table->dropColumn([
                'tipo_documento',
                'numero_documento',
                'giro',
                'direccion'
            ]);

            // Cambiar nombre de columnas si es necesario
            $table->renameColumn('nrc', 'nrc'); // permanece igual, solo aclaratorio
            $table->renameColumn('telefono', 'telefono'); // permanece igual
            $table->renameColumn('correo', 'correo'); // permanece igual

            // Nuevas columnas
            $table->string('nombre_comercial')->nullable()->after('nombre');
            $table->string('nit')->nullable()->after('nombre_comercial');
            $table->string('dui')->nullable()->after('nit');

            // Claves foráneas
            $table->unsignedBigInteger('actividad_economica_id')->nullable()->after('dui');
            $table->unsignedBigInteger('departamento_id')->nullable()->after('actividad_economica_id');
            $table->unsignedBigInteger('municipio_id')->nullable()->after('departamento_id');

            // Relaciones (si las tablas existen)
            $table->foreign('actividad_economica_id')->references('id')->on('actividades_economicas')->nullOnDelete();
            $table->foreign('departamento_id')->references('id')->on('departamentos')->nullOnDelete();
            $table->foreign('municipio_id')->references('id')->on('municipios')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['actividad_economica_id']);
            $table->dropForeign(['departamento_id']);
            $table->dropForeign(['municipio_id']);

            $table->dropColumn([
                'nombre_comercial',
                'nit',
                'dui',
                'actividad_economica_id',
                'departamento_id',
                'municipio_id'
            ]);

            $table->string('tipo_documento')->nullable();
            $table->string('numero_documento')->nullable();
            $table->string('giro')->nullable();
            $table->text('direccion')->nullable();
        });
    }
};