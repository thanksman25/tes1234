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
        Schema::table('allometric_equations', function (Blueprint $table) {
            // Kolom untuk menyimpan rumus-rumus terpisah
            $table->text('formula_agb')->after('equation_template')->nullable();
            $table->text('formula_bgb')->after('formula_agb')->nullable();
            $table->text('formula_carbon')->after('formula_bgb')->nullable();

            // Kolom JSON untuk menyimpan parameter apa saja yang dibutuhkan rumus ini
            // Contoh: ['diameter', 'height']
            $table->json('required_parameters')->after('formula_carbon')->nullable();

            // Ubah kolom equation_template menjadi nullable karena akan digantikan
            $table->text('equation_template')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('allometric_equations', function (Blueprint $table) {
            $table->dropColumn(['formula_agb', 'formula_bgb', 'formula_carbon', 'required_parameters']);
            $table->text('equation_template')->nullable(false)->change();
        });
    }
};