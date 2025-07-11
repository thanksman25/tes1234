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
        Schema::create('formula_submissions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('formula_name');
        $table->text('equation_template'); // e.g., "0.117 * (DBH^2.5)"
        $table->string('reference'); // "Chave et al. (2014)"
        $table->string('supporting_document_path'); // Path file jurnal/paper
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->text('rejection_reason')->nullable();
        $table->foreignId('reviewed_by')->nullable()->constrained('users'); // Admin yg review
        $table->timestamp('reviewed_at')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formula_submissions');
    }
};
