<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('substantive_scores', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('assignment_id')->constrained('reviewer_assignments')->cascadeOnDelete();
            $table->foreignId('criterion_id')->constrained('rubric_criteria')->restrictOnDelete();
            // Skor valid: 1, 2, 3, 5, 6, 7 (angka 4 tidak ada — format baku kementerian)
            $table->unsignedTinyInteger('score')->comment('1|2|3|5|6|7');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['assignment_id', 'criterion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('substantive_scores');
    }
};
