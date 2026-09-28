<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Universal Dashboard Redirect
    Route::get('/dashboard', function () {
        $role = auth()->user()->primaryRole()->value;
        return redirect()->route("dashboard.{$role}");
    })->name('dashboard');

    // Role-specific Dashboards (Placeholder for now)
    Route::get('/student/dashboard', function () { return view('dashboard', ['roleName' => 'Student']); })->name('dashboard.student');
    Route::get('/supervisor/dashboard', function () { return view('dashboard', ['roleName' => 'Dosen Pembimbing']); })->name('dashboard.supervisor');
    Route::get('/reviewer/dashboard', function () { return view('dashboard', ['roleName' => 'Reviewer']); })->name('dashboard.reviewer');
    Route::get('/university-lecturer/dashboard', function () { return view('dashboard', ['roleName' => 'Dosen PT']); })->name('dashboard.university_lecturer');
    Route::get('/operator/dashboard', function () { return view('dashboard', ['roleName' => 'Operator']); })->name('dashboard.operator');
    Route::get('/super-operator/dashboard', function () { return view('dashboard', ['roleName' => 'Super Operator']); })->name('dashboard.super_operator');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
