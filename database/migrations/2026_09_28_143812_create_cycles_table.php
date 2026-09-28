<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cycles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('name', 100);
            $table->boolean('is_active')->default(false);
            $table->unsignedTinyInteger('max_proposals_per_supervisor')->default(10);
            $table->timestamps();
            $table->unique(['year', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cycles');
    }
};
