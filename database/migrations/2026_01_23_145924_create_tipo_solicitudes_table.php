<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_solicitudes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: Cupo Especial, Rectificación, etc.
            $table->string('codigo')->unique(); // Ej: CUPO_ESP, RECT_ACTA
            $table->text('descripcion')->nullable();
            $table->boolean('requiere_archivo')->default(true);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_solicitudes');
    }
};
