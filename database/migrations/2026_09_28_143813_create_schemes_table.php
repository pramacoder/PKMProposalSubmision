<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schemes', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 10)->unique();    // PKM-RE, PKM-AI, dll.
            $table->string('name', 100);
            $table->boolean('is_funded')->default(true);
            $table->string('checklist_group', 20);  // funded | article_ai | article_gft
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schemes');
    }
};
