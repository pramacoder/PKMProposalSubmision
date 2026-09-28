<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_forms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cycle_id')->constrained()->cascadeOnDelete();
            $table->string('checklist_group', 20)->comment('funded | article_ai | article_gft');
            $table->string('status', 10)->default('draft')->comment('draft | confirmed');
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            $table->index(['cycle_id', 'checklist_group', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_forms');
    }
};
