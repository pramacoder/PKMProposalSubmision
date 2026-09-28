<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_summaries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->cascadeOnDelete();
            $table->string('stage', 15)->comment('substantive | final');
            $table->decimal('total', 7, 2)->comment('Total nilai = Σ(bobot × skor)');
            $table->timestamp('computed_at')->useCurrent();
            $table->timestamps();
            $table->unique(['proposal_id', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_summaries');
    }
};
