<?php

namespace App\Http\Controllers\Operator;

use App\Enums\ProposalStatus;
use App\Enums\ReviewStage;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Operator\AssignReviewersRequest;
use App\Models\ChecklistForm;
use App\Models\Proposal;
use App\Models\ReviewerAssignment;
use App\Models\Rubric;
use App\Models\User;
use App\Services\ProposalWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewerAssignmentController extends Controller
{
    public function __construct(
        private readonly ProposalWorkflowService $workflowService
    ) {}

    public function index(): View
    {
        // Tampilkan semua proposal yang berstatus admin_assignment (menunggu ditugaskan)
        // Atau sedang di-review untuk dipantau.
        $proposals = Proposal::with(['scheme', 'cycle', 'assignments.reviewer'])
            ->whereIn('status', [
                ProposalStatus::AdminAssignment,
                ProposalStatus::AdministrativeReview,
                ProposalStatus::SubstantiveReview,
            ])
            ->latest('updated_at')
            ->get();

        return view('operator.assignments.index', compact('proposals'));
    }

    public function create(Proposal $proposal): View
    {
        // Hanya bisa assign kalau statusnya AdminAssignment (Penugasan Reviewer)
        if ($proposal->status !== ProposalStatus::AdminAssignment) {
            abort(403, 'Proposal ini sudah ditugaskan atau statusnya tidak sesuai.');
        }

        // Ambil semua reviewer aktif (yang punya role Reviewer)
        $reviewers = User::whereHas('roles', fn ($q) => $q->where('name', Role::Reviewer->value))
            ->where('is_active', true)
            ->get();

        return view('operator.assignments.create', compact('proposal', 'reviewers'));
    }

    public function store(AssignReviewersRequest $request, Proposal $proposal): RedirectResponse
    {
        if ($proposal->status !== ProposalStatus::AdminAssignment) {
            return back()->with('error', 'Proposal ini sudah tidak berada di tahap penugasan.');
        }

        // Cek konflik: apakah ada reviewer yang menjadi pembimbing proposal ini?
        $reviewers = collect([
            $request->admin_reviewer_id,
            $request->substantive_reviewer_id_1,
            $request->substantive_reviewer_id_2,
        ]);

        if ($reviewers->contains($proposal->supervisor_id)) {
            return back()->with('error', 'Terdapat reviewer yang juga merupakan pembimbing dari proposal ini. (Konflik Kepentingan)');
        }

        // Ambil form_id untuk admin review
        $checklistForm = ChecklistForm::where('cycle_id', $proposal->cycle_id)
            ->where('checklist_group', $proposal->scheme->checklist_group->value)
            ->where('status', 'confirmed')
            ->first();

        // Ambil rubric_id untuk substantive review
        $rubric = Rubric::where('cycle_id', $proposal->cycle_id)
            ->where('scheme_id', $proposal->scheme_id)
            ->where('status', 'confirmed')
            ->first();

        // Assign Admin Reviewer
        ReviewerAssignment::create([
            'proposal_id' => $proposal->id,
            'reviewer_id' => $request->admin_reviewer_id,
            'stage' => ReviewStage::Admin,
            'form_id' => $checklistForm?->id,
            'due_at' => $request->due_at,
            'assigned_by' => auth()->id(),
        ]);

        // Assign Substantive Reviewer 1
        ReviewerAssignment::create([
            'proposal_id' => $proposal->id,
            'reviewer_id' => $request->substantive_reviewer_id_1,
            'stage' => ReviewStage::Substantive,
            'rubric_id' => $rubric?->id,
            'due_at' => $request->due_at,
            'assigned_by' => auth()->id(),
        ]);

        // Assign Substantive Reviewer 2
        ReviewerAssignment::create([
            'proposal_id' => $proposal->id,
            'reviewer_id' => $request->substantive_reviewer_id_2,
            'stage' => ReviewStage::Substantive,
            'rubric_id' => $rubric?->id,
            'due_at' => $request->due_at,
            'assigned_by' => auth()->id(),
        ]);

        // Ganti status proposal ke administrative_review
        $this->workflowService->transition(
            $proposal,
            ProposalStatus::AdministrativeReview,
            auth()->user(),
            'Penugasan 3 Reviewer (1 Admin, 2 Substantif)'
        );

        return redirect()->route('operator.assignments.index')->with('success', 'Penugasan 3 Reviewer berhasil disimpan.');
    }
}
