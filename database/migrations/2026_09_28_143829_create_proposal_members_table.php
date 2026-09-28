<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cycle_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('role', 20)->default('member')->comment('leader | member');
            $table->timestamps();
            // Satu mahasiswa hanya di satu proposal per siklus (RULE-47, ADM-12)
            $table->unique(['cycle_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_members');
    }
};
