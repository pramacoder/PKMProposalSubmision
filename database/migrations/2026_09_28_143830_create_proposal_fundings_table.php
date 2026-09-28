<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_fundings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->cascadeOnDelete();
            $table->string('source', 20)->comment('belmawa | university | partner');
            $table->unsignedInteger('amount')->default(0)->comment('Nominal Rp');
            $table->timestamps();
            $table->unique(['proposal_id', 'source']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_fundings');
    }
};
