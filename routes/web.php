<?php

use App\Http\Controllers\Operator\AuditLogController;
use App\Http\Controllers\Operator\ChecklistFormController;
use App\Http\Controllers\Operator\ControlRoomController;
use App\Http\Controllers\Operator\FinalAssignmentController;
use App\Http\Controllers\Operator\PimnasController;
use App\Http\Controllers\Operator\ReviewerAssignmentController;
use App\Http\Controllers\Operator\ReviewRecapController;
use App\Http\Controllers\Operator\RubricController;
use App\Http\Controllers\Operator\DecisionBatchController;
use App\Http\Controllers\Operator\SemifinalDecisionController;
use App\Http\Controllers\Operator\UniversityAssignmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Reviewer\AssignmentController;
use App\Http\Controllers\Student\ProposalController;
use App\Http\Controllers\Student\ProposalFileController;
use App\Http\Controllers\Student\ProposalFundingController;
use App\Http\Controllers\Student\ProposalMemberController;
use App\Http\Controllers\SuperOperator\CycleController;
use App\Http\Controllers\SuperOperator\ReportController;
use App\Http\Controllers\SuperOperator\UserController;
use App\Http\Controllers\Supervisor\ProposalValidationController;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────────────────────
// Public
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('login'));

// ─────────────────────────────────────────────────────────────────────────────
// Authenticated + verified
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function (): void {

    // Universal Dashboard Redirect
    Route::get('/dashboard', function () {
        $role = auth()->user()->primaryRole();

        return redirect()->route($role->dashboardRoute());
    })->name('dashboard');

    // ── Mahasiswa ──────────────────────────────────────────────────────────
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function (): void {
        Route::get('/dashboard', fn () => view('student.dashboard'))->name('dashboard');

        // Proposal (Fase 3)
        Route::resource('proposals', ProposalController::class);
        Route::post('/proposals/{proposal}/submit', [ProposalController::class, 'submit'])->name('proposals.submit');
        Route::post('/proposals/{proposal}/withdraw', [ProposalController::class, 'withdraw'])->name('proposals.withdraw');

        // Sub-resources
        Route::get('/proposals/{proposal}/members', [ProposalMemberController::class, 'index'])->name('proposals.members.index');
        Route::post('/proposals/{proposal}/members', [ProposalMemberController::class, 'store'])->name('proposals.members.store');
        Route::delete('/proposals/{proposal}/members/{member}', [ProposalMemberController::class, 'destroy'])->name('proposals.members.destroy');

        Route::get('/proposals/{proposal}/funding', [ProposalFundingController::class, 'index'])->name('proposals.funding.index');
        Route::post('/proposals/{proposal}/funding', [ProposalFundingController::class, 'store'])->name('proposals.funding.store');

        Route::get('/proposals/{proposal}/files', [ProposalFileController::class, 'index'])->name('proposals.files.index');
        Route::post('/proposals/{proposal}/files', [ProposalFileController::class, 'store'])->name('proposals.files.store');
        Route::get('/proposals/{proposal}/files/{file}/download', [ProposalFileController::class, 'download'])->name('proposals.files.download');
    });

    // ── Dosen Pembimbing ───────────────────────────────────────────────────
    Route::middleware('role:supervisor')->prefix('supervisor')->name('supervisor.')->group(function (): void {
        Route::get('/dashboard', fn () => view('supervisor.dashboard'))->name('dashboard');

        Route::get('/proposals', [ProposalValidationController::class, 'index'])->name('proposals.index');
        Route::get('/proposals/{proposal}', [ProposalValidationController::class, 'show'])->name('proposals.show');
        Route::post('/proposals/{proposal}/validate', [ProposalValidationController::class, 'store'])->name('proposals.validate');
    });

    // ── Reviewer ───────────────────────────────────────────────────────────
    Route::middleware('role:reviewer')->prefix('reviewer')->name('reviewer.')->group(function (): void {
        Route::get('/dashboard', fn () => view('reviewer.dashboard'))->name('dashboard');

        // Assignments
        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');

        // Admin Review
        Route::get('/assignments/{assignment}/admin', [AssignmentController::class, 'showAdmin'])->name('assignments.admin');
        Route::post('/assignments/{assignment}/admin', [AssignmentController::class, 'storeAdmin'])->name('assignments.admin.store');

        // Substantive Review
        Route::get('/assignments/{assignment}/substantive', [AssignmentController::class, 'showSubstantive'])->name('assignments.substantive');
        Route::post('/assignments/{assignment}/substantive', [AssignmentController::class, 'storeSubstantive'])->name('assignments.substantive.store');

        // Final Review
        Route::get('/assignments/{assignment}/final', [AssignmentController::class, 'showFinal'])->name('assignments.final');
        Route::post('/assignments/{assignment}/final', [AssignmentController::class, 'storeFinal'])->name('assignments.final.store');
    });

    // ── Dosen Universitas ──────────────────────────────────────────────────
    Route::middleware('role:university_lecturer')->prefix('university')->name('university.')->group(function (): void {
        Route::get('/dashboard', fn () => view('university.dashboard'))->name('dashboard');

        // Validasi Akhir (Fase 6 - PH6-01)
        Route::get('/proposals', [\App\Http\Controllers\University\UniversityValidationController::class, 'index'])->name('proposals.index');
        Route::get('/proposals/{proposal}', [\App\Http\Controllers\University\UniversityValidationController::class, 'show'])->name('proposals.show');
        Route::post('/proposals/{proposal}/validate', [\App\Http\Controllers\University\UniversityValidationController::class, 'store'])->name('proposals.validate');
    });

    // ── Operator ───────────────────────────────────────────────────────────
    Route::middleware('role:operator,super_operator')->prefix('operator')->name('operator.')->group(function (): void {
        Route::get('/dashboard', fn () => view('operator.dashboard'))->name('dashboard');

        // Ruang Kontrol (PH2-06)
        Route::prefix('control')->name('control.')->group(function (): void {
            Route::get('/', [ControlRoomController::class, 'index'])->name('index');
            Route::patch('/phase/{cycle}/{phase}', [ControlRoomController::class, 'updatePhaseWindow'])->name('phase.update');
        });

        // Form Checklist (PH2-07)
        Route::prefix('checklist')->name('checklist.')->group(function (): void {
            Route::get('/', [ChecklistFormController::class, 'index'])->name('index');
            Route::get('/{checklistForm}', [ChecklistFormController::class, 'show'])->name('show');
            Route::get('/{checklistForm}/edit', [ChecklistFormController::class, 'edit'])->name('edit');
            Route::patch('/{checklistForm}', [ChecklistFormController::class, 'update'])->name('update');
            Route::post('/{checklistForm}/confirm', [ChecklistFormController::class, 'confirm'])->name('confirm');
            Route::post('/{checklistForm}/duplicate', [ChecklistFormController::class, 'duplicate'])->name('duplicate');
        });

        // Rubrik Penilaian (PH2-07)
        Route::prefix('rubric')->name('rubric.')->group(function (): void {
            Route::get('/', [RubricController::class, 'index'])->name('index');
            Route::get('/{rubric}', [RubricController::class, 'show'])->name('show');
            Route::get('/{rubric}/edit', [RubricController::class, 'edit'])->name('edit');
            Route::patch('/{rubric}', [RubricController::class, 'update'])->name('update');
            Route::post('/{rubric}/confirm', [RubricController::class, 'confirm'])->name('confirm');
            Route::post('/{rubric}/duplicate', [RubricController::class, 'duplicate'])->name('duplicate');
        });

        // Penugasan Reviewer (Fase 4)
        Route::get('/assignments', [ReviewerAssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/{proposal}/create', [ReviewerAssignmentController::class, 'create'])->name('assignments.create');
        Route::post('/assignments/{proposal}', [ReviewerAssignmentController::class, 'store'])->name('assignments.store');

        // Penugasan Reviewer Final (Fase 5 - PH5-01)
        Route::get('/final-assignments', [FinalAssignmentController::class, 'index'])->name('final-assignments.index');
        Route::get('/final-assignments/{proposal}/create', [FinalAssignmentController::class, 'create'])->name('final-assignments.create');
        Route::post('/final-assignments/{proposal}', [FinalAssignmentController::class, 'store'])->name('final-assignments.store');

        // Rekapitulasi Review (Fase 4 - PH4-04)
        Route::get('/recap', [ReviewRecapController::class, 'index'])->name('recap.index');
        Route::get('/recap/{proposal}', [ReviewRecapController::class, 'show'])->name('recap.show');
        Route::post('/recap/{proposal}/revision', [ReviewRecapController::class, 'requestRevision'])->name('recap.revision');

        // Keputusan Semifinal (Fase 5 - PH5-03 & 04)
        Route::get('/semifinal', [SemifinalDecisionController::class, 'index'])->name('semifinal.index');
        Route::get('/semifinal/{proposal}', [SemifinalDecisionController::class, 'show'])->name('semifinal.show');
        Route::post('/semifinal/{proposal}', [SemifinalDecisionController::class, 'store'])->name('semifinal.store');

        // Penugasan Dosen Universitas (Fase 6 - PH6-01)
        Route::get('/university-assignments', [UniversityAssignmentController::class, 'index'])->name('university-assignments.index');
        Route::get('/university-assignments/{proposal}/create', [UniversityAssignmentController::class, 'create'])->name('university-assignments.create');
        Route::post('/university-assignments/{proposal}', [UniversityAssignmentController::class, 'store'])->name('university-assignments.store');

        // Batch Keputusan Akhir (Fase 6 - PH6-02)
        Route::resource('decision-batches', DecisionBatchController::class)->except(['edit', 'update', 'destroy']);
        Route::post('decision-batches/{batch}/add', [DecisionBatchController::class, 'addProposal'])->name('decision-batches.add');
        Route::delete('decision-batches/{batch}/remove/{decision}', [DecisionBatchController::class, 'removeProposal'])->name('decision-batches.remove');
        Route::patch('decision-batches/{batch}/decision/{decision}', [DecisionBatchController::class, 'updateDecision'])->name('decision-batches.update-decision');
        Route::post('decision-batches/{batch}/finalize', [DecisionBatchController::class, 'finalize'])->name('decision-batches.finalize');

        // Status PIMNAS dan Belmawa (Fase 6 - PH6-03)
        Route::get('/pimnas', [PimnasController::class, 'index'])->name('pimnas.index');
        Route::patch('/pimnas/{proposal}', [PimnasController::class, 'update'])->name('pimnas.update');

        // Log Audit (Fase 6 - PH6-05)
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    // ── Super Operator / Pimpinan PT ───────────────────────────────────────
    Route::middleware('role:super_operator')->prefix('super-operator')->name('super-operator.')->group(function (): void {
        Route::get('/dashboard', fn () => view('super-operator.dashboard'))->name('dashboard');

        // CRUD Akun (PH2-05)
        Route::resource('users', UserController::class)->except(['show']);
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::post('/users/{user}/resend-claim', [UserController::class, 'resendClaim'])->name('users.resend-claim');
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

        // CRUD Siklus PKM
        Route::resource('cycles', CycleController::class);

        // Laporan Pimpinan PT (Fase 6 - PH6-06)
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

    // ── Profile (semua peran) ──────────────────────────────────────────────
    Route::prefix('profile')->name('profile.')->group(function (): void {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/auth.php';
