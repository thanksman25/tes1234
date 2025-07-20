<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calculation_projects', function (Blueprint $table) {
            // Luas total dari semua plot sampel dalam hektar
            $table->decimal('sample_area', 10, 4)->nullable()->after('land_area');
        });
    }

    public function down(): void
    {
        Schema::table('calculation_projects', function (Blueprint $table) {
            $table->dropColumn('sample_area');
        });
    }
};