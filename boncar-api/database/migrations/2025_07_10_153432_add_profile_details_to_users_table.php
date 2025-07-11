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
        Schema::table('users', function (Blueprint $table) {
        $table->enum('role', ['admin', 'user'])->default('user')->after('password');
        $table->string('npm_nik')->nullable()->after('role');
        $table->string('institution')->nullable()->after('npm_nik');
        $table->string('phone_number')->nullable()->after('institution');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['role', 'npm_nik', 'institution', 'phone_number']);
    });
    }
};
