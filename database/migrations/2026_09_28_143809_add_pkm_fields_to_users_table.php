<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('nim', 20)->nullable()->unique()->after('email');
            $table->string('nidn', 20)->nullable()->after('nim');
            $table->string('nuptk', 20)->nullable()->after('nidn');
            $table->string('study_program', 100)->nullable()->after('nuptk');
            $table->unsignedSmallInteger('cohort_year')->nullable()->after('study_program');
            $table->string('notification_email')->nullable()->after('cohort_year');
            $table->timestamp('notification_email_verified_at')->nullable()->after('notification_email');
            $table->boolean('must_change_password')->default(false)->after('notification_email_verified_at');
            $table->timestamp('claimed_at')->nullable()->after('must_change_password');
            $table->boolean('is_active')->default(true)->after('claimed_at');
            // email kolom sudah ada di tabel users bawaan (email student)
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'nim', 'nidn', 'nuptk', 'study_program', 'cohort_year',
                'notification_email', 'notification_email_verified_at',
                'must_change_password', 'claimed_at', 'is_active',
            ]);
        });
    }
};
