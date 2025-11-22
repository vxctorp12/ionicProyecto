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
    Schema::create('grados', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->timestamps();
    });

    Schema::create('materias', function (Blueprint $table) {
        $table->id();
        $table->foreignId('grado_id')->constrained('grados')->onDelete('cascade');
        $table->string('nombre');
        $table->timestamps();
    });

    Schema::create('actividades', function (Blueprint $table) {
        $table->id();
        $table->foreignId('materia_id')->constrained('materias')->onDelete('cascade');
        $table->string('nombre'); 
        $table->string('periodo'); 
        $table->integer('porcentaje')->default(100);
        $table->timestamps();
    });

    Schema::create('matriculas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('grado_id')->constrained('grados')->onDelete('cascade');
        $table->year('anio')->default(date('Y'));
        $table->timestamps();
    });
    Schema::create('matriculas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('grado_id')->constrained('grados')->onDelete('cascade');
        $table->year('anio')->default(date('Y'));
        $table->timestamps();
    });

    Schema::create('docente_materias', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('materia_id')->constrained('materias')->onDelete('cascade');
        $table->timestamps();
    });

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
