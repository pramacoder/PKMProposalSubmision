<?php

namespace App\Http\Controllers\Operator;

use App\Enums\ProposalStatus;
use App\Http\Controllers\Controller;
use App\Models\BatchDecision;
use App\Models\BatchParticipant;
use App\Models\Cycle;
use App\Models\DecisionBatch;
use App\Models\Proposal;
use App\Models\Scheme;
use App\Models\User;
use App\Services\ProposalWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DecisionBatchController extends Controller
{
    public function __construct(
        private readonly ProposalWorkflowService $workflowService
    ) {}

    public function index(): View
    {
        $batches = DecisionBatch::with(['cycle', 'scheme'])
            ->withCount('decisions')
            ->latest()
            ->get();

        return view('operator.decision_batches.index', compact('batches'));
    }

    public function create(): View
    {
        $cycles = Cycle::active()->get();
        $schemes = Scheme::orderBy('code')->get();
        
        // Ambil semua pengguna operator atau reviewer untuk ditambahkan sebagai partisipan (tim penilai)
        $users = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['operator', 'super_operator', 'reviewer']);
        })->where('is_active', true)->orderBy('name')->get();

        return view('operator.decision_batches.create', compact('cycles', 'schemes', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cycle_id' => ['required', 'exists:cycles,id'],
            'scheme_id' => ['required', 'exists:schemes,id'],
            'name' => ['required', 'string', 'max:200'],
            'participants' => ['nullable', 'array'],
            'participants.*' => ['exists:users,id'],
        ]);

        $batch = DecisionBatch::create([
            'cycle_id' => $validated['cycle_id'],
            'scheme_id' => $validated['scheme_id'],
            'name' => $validated['name'],
        ]);

        if (!empty($validated['participants'])) {
            foreach ($validated['participants'] as $userId) {
                // Tentukan role dengan ngambil dari primary role user tsb (sederhananya)
                $user = User::find($userId);
                $role = $user->primaryRole()->value;
                BatchParticipant::create([
                    'batch_id' => $batch->id,
                    'user_id' => $userId,
                    'role' => $role,
                ]);
            }
        }

        return redirect()->route('operator.decision-batches.show', $batch)
            ->with('success', 'Batch Keputusan berhasil dibuat.');
    }

    public function show(DecisionBatch $batch): View
    {
        $batch->load(['cycle', 'scheme', 'participants.user', 'decisions.proposal.leader']);
        
        // Ambil daftar proposal yang siap dinilai (status FinalDecision) sesuai siklus dan skema
        $availableProposals = Proposal::with(['leader', 'universityLecturer'])
            ->where('cycle_id', $batch->cycle_id)
            ->where('scheme_id', $batch->scheme_id)
            ->where('status', ProposalStatus::FinalDecision)
            ->whereDoesntHave('batchDecisions') // pastikan belum masuk batch lain
            ->get();

        return view('operator.decision_batches.show', compact('batch', 'availableProposals'));
    }

    public function addProposal(Request $request, DecisionBatch $batch): RedirectResponse
    {
        if ($batch->isDecided()) {
            return back()->with('error', 'Batch ini sudah difinalisasi.');
        }

        $validated = $request->validate([
            'proposal_id' => ['required', 'exists:proposals,id'],
        ]);

        $proposal = Proposal::findOrFail($validated['proposal_id']);
        
        if ($proposal->status !== ProposalStatus::FinalDecision || $proposal->cycle_id !== $batch->cycle_id || $proposal->scheme_id !== $batch->scheme_id) {
            return back()->with('error', 'Proposal tidak memenuhi syarat untuk ditambahkan ke batch ini.');
        }

        BatchDecision::firstOrCreate([
            'batch_id' => $batch->id,
            'proposal_id' => $proposal->id,
        ], [
            'decision' => 'not_passed', // default
        ]);

        return back()->with('success', 'Proposal berhasil ditambahkan ke batch.');
    }

    public function removeProposal(DecisionBatch $batch, BatchDecision $decision): RedirectResponse
    {
        if ($batch->isDecided()) {
            return back()->with('error', 'Batch ini sudah difinalisasi.');
        }

        if ($decision->batch_id !== $batch->id) {
            abort(403);
        }

        $decision->delete();

        return back()->with('success', 'Proposal dikeluarkan dari batch.');
    }

    public function updateDecision(Request $request, DecisionBatch $batch, BatchDecision $decision): RedirectResponse
    {
        if ($batch->isDecided()) {
            return back()->with('error', 'Batch ini sudah difinalisasi.');
        }

        if ($decision->batch_id !== $batch->id) {
            abort(403);
        }

        $validated = $request->validate([
            'decision' => ['required', 'in:passed,not_passed'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $decision->update($validated);

        return back()->with('success', 'Keputusan untuk proposal berhasil diperbarui.');
    }

    public function finalize(DecisionBatch $batch): RedirectResponse
    {
        if ($batch->isDecided()) {
            return back()->with('error', 'Batch ini sudah difinalisasi sebelumnya.');
        }

        if ($batch->decisions->isEmpty()) {
            return back()->with('error', 'Tidak ada proposal dalam batch ini untuk difinalisasi.');
        }

        $batch->load('decisions.proposal');

        foreach ($batch->decisions as $decision) {
            $proposal = $decision->proposal;
            
            // Lakukan transisi
            $targetStatus = $decision->decision === 'passed' 
                ? ProposalStatus::InternalPassed 
                : ProposalStatus::InternalNotPassed;
                
            $this->workflowService->transition(
                $proposal,
                $targetStatus,
                auth()->user(),
                'Keputusan Akhir Batch: ' . $batch->name
            );
        }

        $batch->update(['decided_at' => now()]);

        return back()->with('success', 'Batch berhasil difinalisasi. Semua proposal di dalamnya telah diperbarui statusnya.');
    }
}
