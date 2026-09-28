<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('decision_batches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cycle_id')->constrained()->restrictOnDelete();
            $table->foreignId('scheme_id')->constrained()->restrictOnDelete();
            $table->string('name', 200);
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
        });

        Schema::create('batch_participants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('batch_id')->constrained('decision_batches')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('role', 30)->comment('operator | reviewer | super_operator');
            $table->timestamps();
            $table->unique(['batch_id', 'user_id']);
        });

        Schema::create('batch_decisions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('batch_id')->constrained('decision_batches')->cascadeOnDelete();
            $table->foreignId('proposal_id')->constrained()->restrictOnDelete();
            $table->string('decision', 20)->comment('passed | not_passed');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unique(['batch_id', 'proposal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_decisions');
        Schema::dropIfExists('batch_participants');
        Schema::dropIfExists('decision_batches');
    }
};
