<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supervisor_validations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('round')->comment('1 = pengajuan, 2 = setelah revisi');
            $table->foreignId('supervisor_id')->constrained('users')->restrictOnDelete();
            $table->string('decision', 10)->comment('approved | rejected');
            $table->text('note')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
            $table->unique(['proposal_id', 'round']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supervisor_validations');
    }
};
