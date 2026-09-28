<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('themes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cycle_id')->constrained()->cascadeOnDelete();
            $table->string('name', 200);
            $table->unsignedTinyInteger('sort')->default(0);
            $table->timestamps();
            $table->unique(['cycle_id', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('themes');
    }
};
