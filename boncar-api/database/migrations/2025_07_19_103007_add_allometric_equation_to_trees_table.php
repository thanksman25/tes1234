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
        Schema::table('trees', function (Blueprint $table) {
            // Tambahkan kolom ini setelah species_id
            $table->foreignId('allometric_equation_id')->after('species_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trees', function (Blueprint $table) {
            // Drop foreign key constraint dulu sebelum drop kolom
            $table->dropForeign(['allometric_equation_id']);
            $table->dropColumn('allometric_equation_id');
        });
    }
};