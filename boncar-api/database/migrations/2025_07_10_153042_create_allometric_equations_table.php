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
        Schema::create('allometric_equations', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('equation_template');
        $table->string('reference');
        $table->foreignId('submission_id')->nullable()->constrained('formula_submissions');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allometric_equations');
    }
};
