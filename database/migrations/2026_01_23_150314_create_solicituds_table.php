<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitud', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tipo_solicitud_id')->constrained('tipo_solicitudes');

            // Relación Opcional: Solo si la solicitud es sobre un curso específico
            $table->foreignId('programacion_id')->nullable()->constrained('programacion_academica');

            // Campo Mágico: Aquí guardaremos cualquier dato extra en formato JSON
            // Ej: {"ciclo_origen": "2025-1", "motivo_personal": "...", "prioridad": "alta"}
            $table->json('metadatos')->nullable();

            $table->text('motivo');
            $table->enum('estado', ['pendiente', 'revisado', 'validado', 'aprobado', 'rechazado'])
                ->default('pendiente');

            // Archivos
            $table->string('firma_digital_path')->nullable();
            $table->string('archivo_sustento_path')->nullable();

            // Auditoría y Seguimiento
            $table->foreignId('asignado_a')->nullable()->constrained('users'); // Quién la está revisando
            $table->text('observaciones_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
