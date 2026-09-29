<?php

namespace App\Http\Controllers\Reviewer;

use App\Enums\ProposalStatus;
use App\Enums\ReviewStage;
use App\Http\Controllers\Controller;
use App\Models\AdminReviewResult;
use App\Models\ChecklistForm;
use App\Models\ReviewerAssignment;
use App\Models\SubstantiveScore;
use App\Services\ProposalWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function __construct(
        private readonly ProposalWorkflowService $workflowService
    ) {}

    public function index(): View
    {
        // Tampilkan semua penugasan untuk reviewer ini
        $assignments = ReviewerAssignment::with(['proposal.scheme', 'proposal.cycle'])
            ->where('reviewer_id', auth()->id())
            ->latest('updated_at')
            ->get();

        return view('reviewer.assignments.index', compact('assignments'));
    }

    public function showAdmin(ReviewerAssignment $assignment): View
    {
        $this->authorizeAccess($assignment, ReviewStage::Admin);

        $assignment->load([
            'proposal.leader', 'proposal.members.user', 'proposal.files.requirement', 'proposal.scheme',
        ]);

        // Ambil checklist items terkait form ini
        $checklistForm = ChecklistForm::with('items')->findOrFail($assignment->form_id);

        // Ambil hasil review admin jika sudah pernah dinilai (draft/progress)
        $results = AdminReviewResult::where('assignment_id', $assignment->id)->get()->keyBy('checklist_item_id');

        return view('reviewer.assignments.admin', compact('assignment', 'checklistForm', 'results'));
    }

    public function storeAdmin(Request $request, ReviewerAssignment $assignment): RedirectResponse
    {
        $this->authorizeAccess($assignment, ReviewStage::Admin);

        if ($assignment->isSubmitted()) {
            return back()->with('error', 'Penilaian administratif ini sudah disubmit dan tidak dapat diubah.');
        }

        $validated = $request->validate([
            'results' => ['required', 'array'],
            'results.*.passed' => ['required', 'boolean'],
            'results.*.note' => ['nullable', 'string', 'max:2000'],
            'action' => ['required', 'string', 'in:save,submit'],
        ]);

        foreach ($validated['results'] as $itemId => $data) {
            AdminReviewResult::updateOrCreate(
                [
                    'assignment_id' => $assignment->id,
                    'checklist_item_id' => $itemId,
                ],
                [
                    'passed' => $data['passed'],
                    'note' => $data['passed'] ? null : $data['note'], // Reset note jika passed = true
                ]
            );
        }

        if ($validated['action'] === 'submit') {
            $assignment->update(['status' => 'submitted']);

            // Periksa apakah ini membuat proposal siap maju ke substantif.
            // Sesuai DEC-12, review administratif tidak menggugurkan. Jadi kita langsung memajukannya ke tahap SubstantiveReview
            // Tapi pastikan dulu jika status saat ini masih AdministrativeReview
            if ($assignment->proposal->status === ProposalStatus::AdministrativeReview) {
                // Semua item checklist dicatat.
                // Transisi ke SubstantiveReview.
                $this->workflowService->transition(
                    $assignment->proposal,
                    ProposalStatus::SubstantiveReview,
                    auth()->user(),
                    'Review Administratif selesai (DEC-12)'
                );
            }

            return redirect()->route('reviewer.assignments.index')->with('success', 'Review Administratif berhasil disubmit.');
        }

        $assignment->update(['status' => 'in_progress']);

        return back()->with('success', 'Progress Review Administratif berhasil disimpan.');
    }

    public function showSubstantive(ReviewerAssignment $assignment): View
    {
        return $this->handleShowScoreBased($assignment, ReviewStage::Substantive);
    }

    public function storeSubstantive(Request $request, ReviewerAssignment $assignment): RedirectResponse
    {
        return $this->handleStoreScoreBased($request, $assignment, ReviewStage::Substantive);
    }

    public function showFinal(ReviewerAssignment $assignment): View
    {
        return $this->handleShowScoreBased($assignment, ReviewStage::Final);
    }

    public function storeFinal(Request $request, ReviewerAssignment $assignment): RedirectResponse
    {
        return $this->handleStoreScoreBased($request, $assignment, ReviewStage::Final);
    }

    private function handleShowScoreBased(ReviewerAssignment $assignment, ReviewStage $stage): View
    {
        $this->authorizeAccess($assignment, $stage);

        $assignment->load([
            'proposal.leader', 'proposal.members.user', 'proposal.files.requirement', 'proposal.scheme', 'rubric.criteria',
        ]);

        $results = SubstantiveScore::where('assignment_id', $assignment->id)->get()->keyBy('criterion_id');

        return view('reviewer.assignments.substantive', compact('assignment', 'results', 'stage'));
    }

    private function handleStoreScoreBased(Request $request, ReviewerAssignment $assignment, ReviewStage $stage): RedirectResponse
    {
        $this->authorizeAccess($assignment, $stage);

        if ($assignment->isSubmitted()) {
            return back()->with('error', 'Penilaian ini sudah disubmit.');
        }

        $validated = $request->validate([
            'scores' => ['required', 'array'],
            'scores.*.score' => ['required', 'integer', 'in:1,2,3,5,6,7'],
            'scores.*.comment' => ['nullable', 'string', 'max:2000'],
            'action' => ['required', 'string', 'in:save,submit'],
        ]);

        foreach ($validated['scores'] as $criterionId => $data) {
            SubstantiveScore::updateOrCreate(
                [
                    'assignment_id' => $assignment->id,
                    'criterion_id' => $criterionId,
                ],
                [
                    'score' => $data['score'],
                    'comment' => $data['comment'],
                ]
            );
        }

        if ($validated['action'] === 'submit') {
            $assignment->update(['status' => 'submitted']);

            // Jika Final, cek jika semua reviewer final sudah submit
            if ($stage === ReviewStage::Final) {
                $allSubmitted = ReviewerAssignment::where('proposal_id', $assignment->proposal_id)
                    ->where('stage', ReviewStage::Final)
                    ->where('status', '!=', 'submitted')
                    ->doesntExist();

                if ($allSubmitted && $assignment->proposal->status === ProposalStatus::FinalReview) {
                    // Semua reviewer final sudah submit, transisikan status proposal ke SemifinalDecision
                    $this->workflowService->transition(
                        $assignment->proposal,
                        ProposalStatus::SemifinalDecision,
                        auth()->user(),
                        'Semua Reviewer Final telah mensubmit nilai.'
                    );
                }
            }

            return redirect()->route('reviewer.assignments.index')->with('success', 'Review berhasil disubmit.');
        }

        $assignment->update(['status' => 'in_progress']);

        return back()->with('success', 'Progress Review berhasil disimpan.');
    }

    private function authorizeAccess(ReviewerAssignment $assignment, ReviewStage $expectedStage): void
    {
        if ($assignment->reviewer_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        if ($assignment->stage !== $expectedStage) {
            abort(404, 'Tahap review tidak sesuai.');
        }
    }
}
