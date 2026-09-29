<?php

namespace App\Http\Controllers\Operator;

use App\Enums\ProposalStatus;
use App\Enums\ReviewStage;
use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ReviewSummary;
use App\Models\Revision;
use App\Services\ProposalWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewRecapController extends Controller
{
    public function __construct(
        private readonly ProposalWorkflowService $workflowService
    ) {}

    public function index(): View
    {
        // Tampilkan proposal yang berada di SubstantiveReview (menunggu recap atau sedang berjalan)
        // Atau yang sudah masuk tahap revisi.
        $proposals = Proposal::with(['scheme', 'cycle', 'assignments'])
            ->whereIn('status', [
                ProposalStatus::SubstantiveReview,
                ProposalStatus::Revision,
            ])
            ->latest('updated_at')
            ->get();

        return view('operator.recap.index', compact('proposals'));
    }

    public function show(Proposal $proposal): View
    {
        $proposal->load([
            'assignments.reviewer',
            'assignments.adminResults.checklistItem',
            'assignments.scores.criterion',
        ]);

        $adminAssignment = $proposal->assignments->where('stage', ReviewStage::Admin)->first();
        $substantiveAssignments = $proposal->assignments->where('stage', ReviewStage::Substantive);

        $summary = ReviewSummary::where('proposal_id', $proposal->id)->where('stage', 'substantive')->first();

        // Hitung skor rata-rata otomatis untuk preview jika belum disimpan
        $averageScore = 0;
        if ($substantiveAssignments->count() > 0) {
            $totalAll = 0;
            $count = 0;
            foreach ($substantiveAssignments as $sub) {
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

        return view('operator.recap.show', compact('proposal', 'adminAssignment', 'substantiveAssignments', 'summary', 'averageScore'));
    }

    public function requestRevision(Request $request, Proposal $proposal): RedirectResponse
    {
        $validated = $request->validate([
            'due_at' => ['required', 'date', 'after:now'],
            'total_score' => ['required', 'numeric', 'min:0', 'max:700'],
        ]);

        if ($proposal->status !== ProposalStatus::SubstantiveReview) {
            return back()->with('error', 'Status proposal tidak valid untuk meminta revisi saat ini.');
        }

        // Ambil semua hasil admin yang "TIDAK SESUAI" (passed = false)
        $adminNotesArr = [];
        $adminAssignment = $proposal->assignments->where('stage', ReviewStage::Admin)->first();
        if ($adminAssignment) {
            foreach ($adminAssignment->adminResults->where('passed', false) as $res) {
                $adminNotesArr[] = "- {$res->checklistItem->label}: {$res->note}";
            }
        }
        $adminNotes = implode("\n", $adminNotesArr);

        // Ambil semua komentar substantif
        $subNotesArr = [];
        $substantiveAssignments = $proposal->assignments->where('stage', ReviewStage::Substantive);
        foreach ($substantiveAssignments as $idx => $sub) {
            $reviewerNumber = $idx + 1; // Tanpa identitas asli
            $hasComment = false;
            foreach ($sub->scores->whereNotNull('comment') as $res) {
                if (! $hasComment) {
                    $subNotesArr[] = '**Catatan Substantif:**';
                    $hasComment = true;
                }
                $subNotesArr[] = "- {$res->criterion->label}: {$res->comment}";
            }
        }
        $subNotes = implode("\n", $subNotesArr);

        // Simpan ke ReviewSummary (Rekap total nilai)
        ReviewSummary::updateOrCreate(
            ['proposal_id' => $proposal->id, 'stage' => 'substantive'],
            ['total' => $validated['total_score'], 'computed_at' => now()]
        );

        // Buka revisi
        Revision::updateOrCreate(
            ['proposal_id' => $proposal->id, 'round' => 1],
            [
                'admin_notes' => empty($adminNotes) ? null : $adminNotes,
                'notes' => empty($subNotes) ? null : $subNotes,
                'due_at' => $validated['due_at'],
                'submitted_at' => null,
            ]
        );

        $this->workflowService->transition(
            $proposal,
            ProposalStatus::Revision,
            auth()->user(),
            'Rekap selesai. Mengembalikan ke mahasiswa untuk direvisi.'
        );

        return redirect()->route('operator.recap.index')->with('success', 'Catatan revisi berhasil dikirim ke mahasiswa tanpa menyertakan identitas reviewer.');
    }
}
