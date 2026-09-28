<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cycle_id')->constrained()->restrictOnDelete();
            $table->foreignId('scheme_id')->constrained()->restrictOnDelete();
            $table->string('title', 300);
            $table->foreignId('leader_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('theme_id')->nullable()->constrained('themes')->nullOnDelete();
            $table->string('status', 40)->default('draft');
            $table->decimal('similarity_percent', 5, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedInteger('admin_cost_amount')->nullable()->comment('Komponen administrasi (Rp)');
            $table->string('internal_result', 20)->nullable()->comment('passed | not_passed');
            $table->string('belmawa_result', 50)->nullable()->comment('Input manual operator setelah pengumuman Belmawa');
            $table->string('pimnas_status', 50)->nullable()->comment('Input manual operator');
            $table->timestamps();
            $table->index(['cycle_id', 'status']);
            $table->index('leader_id');
            $table->index('supervisor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
