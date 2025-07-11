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
        Schema::create('trees', function (Blueprint $table) {
        $table->id();
        $table->foreignId('calculation_project_id')->constrained()->onDelete('cascade');
        $table->foreignId('species_id')->constrained();
        // Gunakan JSON untuk menyimpan parameter pohon yg dinamis
        // Sesuai kebutuhan rumus (e.g., {"diameter": 25, "height": 15})
        $table->json('parameters');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trees');
    }
};
