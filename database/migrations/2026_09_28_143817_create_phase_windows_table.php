<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phase_windows', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cycle_id')->constrained()->cascadeOnDelete();
            $table->string('phase', 40)->comment('Status proposal yang dikontrol jendela ini');
            $table->timestamp('opens_at')->nullable();
            $table->timestamp('closes_at')->nullable();
            $table->boolean('forced_open')->default(false);
            $table->boolean('forced_closed')->default(false);
            $table->string('forced_reason', 255)->nullable();
            $table->foreignId('forced_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['cycle_id', 'phase']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phase_windows');
    }
};
