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
        Schema::create('calculation_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('allometric_equation_id')->constrained();
            $table->string('project_name');
            $table->string('province');
            $table->string('city');
            $table->string('district');
            $table->string('village');
            $table->decimal('land_area', 10, 2);
            $table->enum('method', ['census', 'sampling']);
            $table->decimal('sample_area', 10, 4)->nullable();
            // --- PERBESAR UKURAN KOLOM INI ---
            $table->decimal('total_carbon_stock', 20, 10)->nullable(); // Total 20 digit, 10 di antaranya di belakang koma
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calculation_projects');
    }
};