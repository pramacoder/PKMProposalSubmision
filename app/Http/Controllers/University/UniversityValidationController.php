<?php

namespace App\Http\Controllers\University;

use App\Enums\ProposalStatus;
use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\UniversityValidation;
use App\Services\ProposalWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UniversityValidationController extends Controller
{
    public function __construct(
        private readonly ProposalWorkflowService $workflowService
    ) {}

    public function index(): View
    {
        // Ambil proposal yang ditugaskan ke dosen universitas yang sedang login
        $proposals = Proposal::with(['scheme', 'cycle', 'leader'])
            ->where('university_lecturer_id', auth()->id())
            ->latest('updated_at')
            ->get();

        return view('university.proposals.index', compact('proposals'));
    }

    public function show(Proposal $proposal): View
    {
        $this->authorizeAccess($proposal);

        $proposal->load([
            'scheme', 'theme', 'supervisor', 'leader', 'members.user', 
            'fundings.source', 'files.requirement', 'universityValidations'
        ]);

        return view('university.proposals.show', compact('proposal'));
    }

    public function store(Request $request, Proposal $proposal): RedirectResponse
    {
        $this->authorizeAccess($proposal);

        if ($proposal->status !== ProposalStatus::UniversityValidation) {
            return back()->with('error', 'Proposal ini tidak dalam status menunggu validasi universitas.');
        }

        $validated = $request->validate([
            'decision' => ['required', 'in:approved,rejected'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $isApproved = $validated['decision'] === 'approved';

        // Rekam histori validasi
        UniversityValidation::create([
            'proposal_id' => $proposal->id,
            'validator_id' => auth()->id(),
            'decision' => $validated['decision'],
            'note' => $validated['note'],
            'decided_at' => now(),
        ]);

        // Transisi status
        $targetStatus = $isApproved ? ProposalStatus::FinalDecision : ProposalStatus::FinalUpload;
        $noteText = 'Validasi Universitas: ' . ($isApproved ? 'Disetujui' : 'Ditolak, perlu perbaikan');
        
        $this->workflowService->transition(
            $proposal,
            $targetStatus,
            auth()->user(),
            $noteText
        );

        return redirect()->route('university.proposals.show', $proposal)
            ->with('success', 'Validasi berhasil disimpan.');
    }

    private function authorizeAccess(Proposal $proposal): void
    {
        if ($proposal->university_lecturer_id !== auth()->id()) {
            abort(403, 'Akses ditolak. Anda bukan dosen pembimbing universitas untuk proposal ini.');
        }
    }
}
