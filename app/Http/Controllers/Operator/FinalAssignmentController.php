<?php

namespace App\Http\Controllers\Operator;

use App\Enums\ProposalStatus;
use App\Enums\ReviewStage;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ReviewerAssignment;
use App\Models\Rubric;
use App\Models\User;
use App\Services\ProposalWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FinalAssignmentController extends Controller
{
    public function __construct(
        private readonly ProposalWorkflowService $workflowService
    ) {}

    public function index(): View
    {
        // Tampilkan semua proposal yang berstatus final_review_assignment (menunggu ditugaskan)
        // Atau sedang di-review final untuk dipantau.
        $proposals = Proposal::with(['scheme', 'cycle', 'assignments' => function ($q) {
            $q->where('stage', ReviewStage::Final)->with('reviewer');
        }])
            ->whereIn('status', [
            ProposalStatus::FinalReviewAssignment,
            ProposalStatus::FinalReview,
        ])
            ->latest('updated_at')
            ->get();

        return view('operator.final_assignments.index', compact('proposals'));
    }

    public function create(Proposal $proposal): View
    {
        if ($proposal->status !== ProposalStatus::FinalReviewAssignment) {
            abort(403, 'Proposal ini sudah ditugaskan reviewer final atau statusnya tidak sesuai.');
        }

        // Ambil reviewer yang sudah ditugaskan sebelumnya (Admin & Substantif)
        $previousReviewerIds = $proposal->assignments()
            ->whereIn('stage', [ReviewStage::Admin, ReviewStage::Substantive])
            ->pluck('reviewer_id')
            ->toArray();

        // Tambahkan dosen pembimbing ke daftar exclusion
        $excludedUserIds = array_merge($previousReviewerIds, [$proposal->supervisor_id]);

        // Ambil semua reviewer aktif (yang punya role Reviewer) kecuali yang diexclude
        $reviewers = User::whereHas('roles', fn ($q) => $q->where('name', Role::Reviewer->value))
            ->whereNotIn('id', $excludedUserIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('operator.final_assignments.create', compact('proposal', 'reviewers'));
    }

    public function store(Request $request, Proposal $proposal): RedirectResponse
    {
        if ($proposal->status !== ProposalStatus::FinalReviewAssignment) {
            return back()->with('error', 'Proposal ini sudah tidak berada di tahap penugasan reviewer final.');
        }

        $previousReviewerIds = $proposal->assignments()
            ->whereIn('stage', [ReviewStage::Admin, ReviewStage::Substantive])
            ->pluck('reviewer_id')
            ->toArray();
        $excludedUserIds = array_merge($previousReviewerIds, [$proposal->supervisor_id]);

        $validated = $request->validate([
            'final_reviewer_id_1' => [
                'required', 'exists:users,id', 'different:final_reviewer_id_2',
                Rule::notIn($excludedUserIds),
            ],
            'final_reviewer_id_2' => [
                'required', 'exists:users,id', 'different:final_reviewer_id_1',
                Rule::notIn($excludedUserIds),
            ],
            'due_at' => ['required', 'date', 'after:now'],
        ], [
            'final_reviewer_id_1.not_in' => 'Reviewer 1 tidak valid (konflik kepentingan atau sudah pernah mereview proposal ini).',
            'final_reviewer_id_2.not_in' => 'Reviewer 2 tidak valid (konflik kepentingan atau sudah pernah mereview proposal ini).',
            'final_reviewer_id_1.different' => 'Reviewer 1 dan Reviewer 2 harus orang yang berbeda.',
        ]);

        // Ambil rubric_id untuk review final (sama dengan rubrik substantif)
        $rubric = Rubric::where('cycle_id', $proposal->cycle_id)
            ->where('scheme_id', $proposal->scheme_id)
            ->where('status', 'confirmed')
            ->first();

        // Assign Final Reviewer 1
        ReviewerAssignment::create([
            'proposal_id' => $proposal->id,
            'reviewer_id' => $validated['final_reviewer_id_1'],
            'stage' => ReviewStage::Final,
            'rubric_id' => $rubric?->id,
            'due_at' => $validated['due_at'],
            'assigned_by' => auth()->id(),
        ]);

        // Assign Final Reviewer 2
        ReviewerAssignment::create([
            'proposal_id' => $proposal->id,
            'reviewer_id' => $validated['final_reviewer_id_2'],
            'stage' => ReviewStage::Final,
            'rubric_id' => $rubric?->id,
            'due_at' => $validated['due_at'],
            'assigned_by' => auth()->id(),
        ]);

        $this->workflowService->transition(
            $proposal,
            ProposalStatus::FinalReview,
            auth()->user(),
            'Penugasan 2 Reviewer Final'
        );

        return redirect()->route('operator.final-assignments.index')->with('success', 'Penugasan 2 Reviewer Final berhasil disimpan.');
    }
}
