<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('allometric_equations')->insert([
            'id' => 4, // Ganti ID ini jika sudah terpakai
            'name' => 'Ketterings et al. (2001)',
            'reference' => 'Ketterings, Q. M., Coe, R., van Noordwijk, M., Ambagau, Y., & Palm, C. A. (2001). Reducing uncertainty in the use of allometric biomass equations for tropical trees: a practical methodology.',
            'formula_agb' => '0.11 * D^2.62', // D adalah Diameter
            'formula_bgb' => 'AGB * 0.2',
            'formula_carbon' => '(AGB + BGB) * 0.47',
            'required_parameters' => json_encode(['circumference']), // Hanya butuh keliling (untuk menghitung D)
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('allometric_equations')->where('id', 4)->delete();
    }
};