<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_requirements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('scheme_id')->constrained()->cascadeOnDelete();
            $table->string('code', 10)->comment('ADM-59, ADM-59a, dll.');
            $table->string('label', 200);
            $table->boolean('is_required')->default(true);
            $table->unsignedTinyInteger('sort')->default(0);
            $table->string('allowed_mimes', 100)->default('pdf')->comment('pdf,jpg,png dll. dipisah koma');
            $table->unsignedInteger('max_kb')->default(10240)->comment('10 MB default');
            $table->timestamps();
            $table->unique(['scheme_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_requirements');
    }
};
