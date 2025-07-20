<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calculation_projects', function (Blueprint $table) {
            // Perintah ini akan mengubah kolom yang sudah ada tanpa menghapus data
            $table->decimal('total_carbon_stock', 20, 10)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('calculation_projects', function (Blueprint $table) {
            // Kode untuk mengembalikan jika diperlukan
            $table->decimal('total_carbon_stock', 15, 5)->nullable()->change();
        });
    }
};