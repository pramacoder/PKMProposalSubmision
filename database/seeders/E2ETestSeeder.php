<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\RoleUser;
use App\Enums\Role;
use Illuminate\Support\Facades\Hash;

class E2ETestSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Student
        $student = User::firstOrCreate(['email' => 'student@example.com'], [
            'name' => 'E2E Student',
            'password' => $password,
            'is_active' => true,
        ]);
        RoleUser::firstOrCreate([
            'user_id' => $student->id,
            'role' => Role::Student->value,
        ]);

        // 2. Operator
        $operator = User::firstOrCreate(['email' => 'operator@example.com'], [
            'name' => 'E2E Operator',
            'password' => $password,
            'is_active' => true,
        ]);
        RoleUser::firstOrCreate([
            'user_id' => $operator->id,
            'role' => Role::Operator->value,
        ]);

        // 3. Supervisor
        $supervisor = User::firstOrCreate(['email' => 'supervisor@example.com'], [
            'name' => 'E2E Supervisor',
            'password' => $password,
            'is_active' => true,
        ]);
        RoleUser::firstOrCreate([
            'user_id' => $supervisor->id,
            'role' => Role::Supervisor->value,
        ]);
    }
}
