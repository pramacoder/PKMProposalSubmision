<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('form_id')->constrained('checklist_forms')->cascadeOnDelete();
            $table->string('code', 10)->comment('ADM-01, ADM-14, dll.');
            $table->string('label', 300);
            $table->string('kind', 10)->comment('auto | manual');
            $table->string('applies_to', 20)->default('all')->comment('all | funded | ai | gft');
            $table->unsignedTinyInteger('sort')->default(0);
            $table->timestamps();
            $table->unique(['form_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_items');
    }
};
