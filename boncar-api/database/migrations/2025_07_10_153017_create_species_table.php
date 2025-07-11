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
        Schema::create('species', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Nama umum (e.g., Mangga)
        $table->string('scientific_name')->unique(); // Nama ilmiah (e.g., Mangifera indica)
        $table->unsignedBigInteger('inaturalist_id')->nullable()->index(); // ID dari iNaturalist
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('species');
    }
};
