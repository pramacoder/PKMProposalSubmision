<?php

namespace App\Http\Controllers\Supervisor;

use App\Enums\ProposalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Supervisor\ValidateProposalRequest;
use App\Models\Proposal;
use App\Models\SupervisorValidation;
use App\Services\ProposalWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProposalValidationController extends Controller
{
    public function __construct(
        private readonly ProposalWorkflowService $workflowService
    ) {}

    public function index(): View
    {
        // Tampilkan semua proposal di mana user adalah supervisornya
        // Dan hanya yang tidak Draft, Withdrawn, atau Disqualified
        $proposals = Proposal::with(['scheme', 'cycle', 'leader'])
            ->where('supervisor_id', auth()->id())
            ->whereNotIn('status', [ProposalStatus::Draft, ProposalStatus::Withdrawn, ProposalStatus::Disqualified])
            ->latest('updated_at')
            ->get();

        return view('supervisor.proposals.index', compact('proposals'));
    }

    public function show(Proposal $proposal): View
    {
        $this->authorizeAccess($proposal);

        $proposal->load(['scheme', 'theme', 'leader', 'members.user', 'fundings', 'files.requirement']);

        $validations = $proposal->supervisorValidations()->orderBy('round', 'desc')->get();

        return view('supervisor.proposals.show', compact('proposal', 'validations'));
    }

    public function store(ValidateProposalRequest $request, Proposal $proposal): RedirectResponse
    {
        $this->authorizeAccess($proposal);

        // Hanya bisa divalidasi jika statusnya SupervisorValidation1 atau SupervisorValidation2
        if (! in_array($proposal->status, [ProposalStatus::SupervisorValidation1, ProposalStatus::SupervisorValidation2], true)) {
            return back()->with('error', 'Proposal ini tidak sedang dalam tahap validasi pembimbing.');
        }

        $validated = $request->validated();
        $decision = $validated['decision']; // 'approved' atau 'rejected'
        $note = $validated['note'];

        $round = $proposal->status === ProposalStatus::SupervisorValidation1 ? 1 : 2;

        // Catat di supervisor_validations
        SupervisorValidation::updateOrCreate(
            ['proposal_id' => $proposal->id, 'round' => $round],
            [
                'supervisor_id' => auth()->id(),
                'decision' => $decision,
                'note' => $note,
                'decided_at' => now(),
            ]
        );

        // Ubah status proposal via workflow service
        if ($decision === 'approved') {
            if ($round === 1) {
                $this->workflowService->supervisorApprove($proposal, auth()->user(), 'Validasi ke-1 disetujui');
            } else {
                // Di round 2, setelah disetujui, lanjut ke final_review_assignment (atau substantive sesuai alur)
                // Kita gunakan transition langsung karena supervisorApprove di service default-nya ke AdminAssignment
                $this->workflowService->transition($proposal, ProposalStatus::FinalReviewAssignment, auth()->user(), 'Validasi ke-2 disetujui');
            }
        } else {
            // Rejected -> kembalikan ke Draft (jika round 1) atau Revision (jika round 2)
            $targetStatus = $round === 1 ? ProposalStatus::Draft : ProposalStatus::Revision;
            $this->workflowService->transition($proposal, $targetStatus, auth()->user(), 'Dikembalikan untuk direvisi: '.$note);
        }

        return back()->with('success', 'Keputusan validasi berhasil disimpan.');
    }

    private function authorizeAccess(Proposal $proposal): void
    {
        if ($proposal->supervisor_id !== auth()->id()) {
            abort(403, 'Akses ditolak. Anda bukan dosen pendamping untuk proposal ini.');
        }
    }
}
