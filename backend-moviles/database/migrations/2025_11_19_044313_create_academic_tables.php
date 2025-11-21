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
    // 1. Grados (Independiente)
    Schema::create('grados', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->timestamps();
    });

    // 2. Materias (Depende de Grados)
    Schema::create('materias', function (Blueprint $table) {
        $table->id();
        $table->foreignId('grado_id')->constrained('grados')->onDelete('cascade');
        $table->string('nombre');
        $table->timestamps();
    });

    // 3. Actividades (Depende de Materias) - ¡ESTO DEBE IR ANTES DE NOTAS!
    Schema::create('actividades', function (Blueprint $table) {
        $table->id();
        $table->foreignId('materia_id')->constrained('materias')->onDelete('cascade');
        $table->string('nombre'); // Ej: "Examen 1"
        $table->string('periodo'); // Ej: "Periodo 1"
        $table->integer('porcentaje')->default(100);
        $table->timestamps();
    });

    // 4. Matrículas (Depende de Grados y Usuarios)
    Schema::create('matriculas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('grado_id')->constrained('grados')->onDelete('cascade');
        $table->year('anio')->default(date('Y'));
        $table->timestamps();
    });

    // 5. Docente Materias (Asignaciones)
    Schema::create('docente_materias', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('materia_id')->constrained('materias')->onDelete('cascade');
        $table->timestamps();
    });

    // 6. Notas (Depende de Actividades y Matrículas) - ¡ESTO VA AL FINAL!
    Schema::create('notas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('matricula_id')->constrained('matriculas')->onDelete('cascade');
        $table->foreignId('actividad_id')->constrained('actividades')->onDelete('cascade'); // Ahora sí existe 'actividades'
        $table->decimal('valor', 5, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_tables');
    }
};
