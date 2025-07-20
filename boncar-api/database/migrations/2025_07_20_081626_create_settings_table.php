<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('value');
            $table->string('label');
            $table->string('group');
        });

        // Tambahkan nilai default
        DB::table('settings')->insert([
            ['key' => 'carbon_conversion_factor', 'value' => '0.47', 'label' => 'Faktor Konversi Karbon (dari Biomassa)', 'group' => 'Kalkulasi'],
            ['key' => 'co2_conversion_factor', 'value' => '3.67', 'label' => 'Faktor Konversi CO2 (dari Karbon)', 'group' => 'Kalkulasi'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};