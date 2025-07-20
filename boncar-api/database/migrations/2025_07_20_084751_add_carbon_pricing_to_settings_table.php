<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->insert([
            ['key' => 'carbon_price_low', 'value' => '10', 'label' => 'Harga Karbon Terendah (USD per Ton CO₂)', 'group' => 'Harga Karbon'],
            ['key' => 'carbon_price_medium', 'value' => '30', 'label' => 'Harga Karbon Menengah (USD per Ton CO₂)', 'group' => 'Harga Karbon'],
            ['key' => 'carbon_price_high', 'value' => '50', 'label' => 'Harga Karbon Tertinggi (USD per Ton CO₂)', 'group' => 'Harga Karbon'],
        ]);
    }

    public function down(): void
    {
        DB::table('settings')
            ->whereIn('key', ['carbon_price_low', 'carbon_price_medium', 'carbon_price_high'])
            ->delete();
    }
};