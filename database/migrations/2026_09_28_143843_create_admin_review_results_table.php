<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_review_results', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('assignment_id')->constrained('reviewer_assignments')->cascadeOnDelete();
            $table->foreignId('checklist_item_id')->constrained('checklist_items')->restrictOnDelete();
            $table->boolean('passed')->nullable()->comment('null = belum dinilai');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unique(['assignment_id', 'checklist_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_review_results');
    }
};
