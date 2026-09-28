<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_files', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requirement_id')->constrained('document_requirements')->restrictOnDelete();
            $table->string('stage', 15)->comment('submission | revision | final');
            $table->unsignedTinyInteger('version')->default(1);
            $table->string('path', 500)->comment('Path relatif di disk privat; tidak diekspos langsung');
            $table->string('original_name', 255);
            $table->unsignedInteger('size')->comment('Ukuran bytes');
            $table->string('mime', 100);
            $table->string('checksum', 64)->nullable()->comment('SHA-256');
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->boolean('is_current')->default(true);
            $table->timestamps();
            $table->index(['proposal_id', 'requirement_id', 'stage', 'is_current']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_files');
    }
};
