<?php

namespace App\Http\Controllers\Operator;

use App\Enums\ProposalStatus;
use App\Enums\ReviewStage;
use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ReviewSummary;
use App\Models\SemifinalResult;
use App\Services\ProposalWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SemifinalDecisionController extends Controller
{
    public function __construct(
        private readonly ProposalWorkflowService $workflowService
    ) {}

    public function index(): View
    {
        // Tampilkan proposal di tahap semifinal
        // Atau yang sudah dinilai semifinalnya
        $proposals = Proposal::with(['scheme', 'cycle', 'semifinalResult'])
            ->whereIn('status', [
                ProposalStatus::SemifinalDecision,
                ProposalStatus::UniversityAssignment,
                ProposalStatus::NotPassed,
            ])
            ->latest('updated_at')
            ->get();

        return view('operator.semifinal.index', compact('proposals'));
    }

    public function show(Proposal $proposal): View
    {
        if (! in_array($proposal->status, [ProposalStatus::SemifinalDecision, ProposalStatus::UniversityAssignment, ProposalStatus::NotPassed])) {
            abort(404);
        }

        $proposal->load([
            'assignments' => fn ($q) => $q->where('stage', ReviewStage::Final)->with(['reviewer', 'scores.criterion']),
            'semifinalResult',
        ]);

        $finalAssignments = $proposal->assignments;

        $averageScore = 0;
        if ($finalAssignments->count() > 0) {
            $totalAll = 0;
            $count = 0;
            foreach ($finalAssignments as $sub) {
                if ($sub->isSubmitted()) {
                    $totalReviewer = 0;
                    foreach ($sub->scores as $scoreResult) {
                        $totalReviewer += $scoreResult->score * $scoreResult->criterion->weight;
                    }
                    $totalAll += $totalReviewer;
                    $count++;
                }
            }
            if ($count > 0) {
                $averageScore = $totalAll / $count;
            }
        }

        return view('operator.semifinal.show', compact('proposal', 'finalAssignments', 'averageScore'));
    }

    public function store(Request $request, Proposal $proposal): RedirectResponse
    {
        if ($proposal->status !== ProposalStatus::SemifinalDecision) {
            return back()->with('error', 'Proposal ini tidak dalam tahap keputusan semifinal.');
        }

        $validated = $request->validate([
            'passed' => ['required', 'boolean'],
            'note' => ['nullable', 'string', 'max:1000'],
            'total_score' => ['required', 'numeric'],
        ]);

        // Simpan nilai rekap akhir
        ReviewSummary::updateOrCreate(
            ['proposal_id' => $proposal->id, 'stage' => 'final'],
            ['total' => $validated['total_score'], 'computed_at' => now()]
        );

        // Simpan keputusan semifinal
        SemifinalResult::updateOrCreate(
            ['proposal_id' => $proposal->id],
            [
                'passed' => $validated['passed'],
                'note' => $validated['note'],
                'decided_by' => auth()->id(),
            ]
        );

        // Transisi ke stage selanjutnya berdasarkan kelulusan
        $targetStatus = $validated['passed'] ? ProposalStatus::UniversityAssignment : ProposalStatus::NotPassed;

        $this->workflowService->transition(
            $proposal,
            $targetStatus,
            auth()->user(),
            'Keputusan Semifinal: '.($validated['passed'] ? 'Lolos' : 'Tidak Lolos')
        );

        return redirect()->route('operator.semifinal.index')->with('success', 'Keputusan semifinal berhasil disimpan.');
    }
}
