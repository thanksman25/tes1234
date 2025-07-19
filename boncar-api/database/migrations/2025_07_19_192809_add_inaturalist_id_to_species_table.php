<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('species', function (Blueprint $table) {
        if (!Schema::hasColumn('species', 'inaturalist_id')) {
            $table->unsignedBigInteger('inaturalist_id')->nullable()->unique()->after('id');
        }
    });
}

    public function down()
    {
        Schema::table('species', function (Blueprint $table) {
            $table->dropColumn('inaturalist_id');
        });
    }
};