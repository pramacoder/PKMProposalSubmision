<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revisions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('round')->default(1);
            $table->text('notes')->nullable()->comment('Catatan dari reviewer (tanpa identitas)');
            $table->text('admin_notes')->nullable()->comment('Kekurangan administratif — disampaikan bersama (DEC-19)');
            $table->timestamp('due_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique(['proposal_id', 'round']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revisions');
    }
};
