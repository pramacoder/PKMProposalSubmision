<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cycle_scheme_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cycle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scheme_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('quota')->default(0)->comment('Kuota klaster PT per bidang dari Belmawa');
            $table->unsignedInteger('min_pt')->default(0)->comment('Dana PT minimum (Rp)');
            $table->unsignedInteger('max_pt')->default(2000000)->comment('Dana PT maksimum (Rp)');
            $table->unsignedInteger('max_partner')->default(1000000)->comment('Dana mitra maksimum (Rp)');
            $table->unsignedInteger('min_belmawa')->default(6000000)->comment('Rekomendasi minimal Belmawa → peringatan jika di bawah');
            $table->unsignedInteger('max_belmawa')->default(8000000)->comment('Batas keras Belmawa (Rp)');
            $table->unsignedTinyInteger('max_admin_percent')->default(20)->comment('Maks % komponen administrasi');
            $table->unsignedTinyInteger('min_months')->default(3);
            $table->unsignedTinyInteger('max_months')->default(4);
            $table->timestamps();
            $table->unique(['cycle_id', 'scheme_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cycle_scheme_settings');
    }
};
