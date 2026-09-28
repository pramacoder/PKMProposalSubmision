<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviewer_assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->restrictOnDelete();
            $table->string('stage', 15)->comment('admin | substantive | final');
            $table->unsignedBigInteger('form_id')->nullable()->comment('checklist_forms.id untuk admin stage');
            $table->unsignedBigInteger('rubric_id')->nullable()->comment('rubrics.id untuk substantive/final stage');
            $table->string('status', 20)->default('pending')->comment('pending | in_progress | submitted');
            $table->timestamp('due_at')->nullable();
            $table->foreignId('assigned_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            // Tiga reviewer per proposal harus berbeda (RULE-43, DEC-01)
            $table->unique(['proposal_id', 'reviewer_id']);
            $table->index(['proposal_id', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviewer_assignments');
    }
};
