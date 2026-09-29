<?php

namespace App\Http\Controllers\Student;

use App\Enums\ProposalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreProposalRequest;
use App\Http\Requests\Student\UpdateProposalRequest;
use App\Models\Cycle;
use App\Models\Proposal;
use App\Models\Scheme;
use App\Models\Theme;
use App\Models\User;
use App\Services\PreSubmissionCheckService;
use App\Services\ProposalWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProposalController extends Controller
{
    public function __construct(
        private readonly ProposalWorkflowService $workflowService,
        private readonly PreSubmissionCheckService $preCheckService
    ) {}

    public function index(): View
    {
        $proposals = auth()->user()->ledProposals()->with(['scheme', 'cycle'])->latest()->get();

        return view('student.proposals.index', compact('proposals'));
    }

    public function create(): View|RedirectResponse
    {
        $cycle = Cycle::active()->first();
        if (! $cycle) {
            return redirect()->route('student.dashboard')->with('error', 'Tidak ada siklus PKM yang aktif saat ini.');
        }

        $schemes = Scheme::orderBy('code')->get();

        return view('student.proposals.create', compact('schemes', 'cycle'));
    }

    public function store(StoreProposalRequest $request): RedirectResponse
    {
        $cycle = Cycle::active()->firstOrFail();

        $proposal = Proposal::create([
            ...$request->validated(),
            'cycle_id' => $cycle->id,
            'leader_id' => auth()->id(),
            'status' => ProposalStatus::Draft,
        ]);

        return redirect()->route('student.proposals.edit', $proposal)
            ->with('success', 'Draf proposal berhasil dibuat. Silakan lengkapi data.');
    }

    public function show(Proposal $proposal): View
    {
        $this->authorizeAccess($proposal);

        $proposal->load(['scheme', 'theme', 'supervisor', 'members.user', 'fundings', 'files.requirement']);

        $readinessErrors = $this->preCheckService->check($proposal);

        return view('student.proposals.show', compact('proposal', 'readinessErrors'));
    }

    public function edit(Proposal $proposal): View|RedirectResponse
    {
        $this->authorizeAccess($proposal);

        if (! $proposal->isEditable()) {
            return redirect()->route('student.proposals.show', $proposal)
                ->with('error', 'Proposal sudah tidak dapat diubah (terkunci).');
        }

        $proposal->load(['scheme', 'theme', 'supervisor', 'members.user', 'fundings', 'files.requirement']);

        $schemes = Scheme::orderBy('code')->get();
        $themes = Theme::orderBy('name')->get();

        // Hanya ambil user dengan peran Supervisor
        $supervisors = User::whereHas('roleRecords', fn ($q) => $q->where('role', 'supervisor'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('student.proposals.edit', compact('proposal', 'schemes', 'themes', 'supervisors'));
    }

    public function update(UpdateProposalRequest $request, Proposal $proposal): RedirectResponse
    {
        $this->authorizeAccess($proposal);

        if (! $proposal->isEditable()) {
            return back()->with('error', 'Proposal sudah terkunci.');
        }

        $proposal->update($request->validated());

        return back()->with('success', 'Data proposal berhasil disimpan.');
    }

    public function submit(Proposal $proposal): RedirectResponse
    {
        $this->authorizeAccess($proposal);

        if (! in_array($proposal->status, [ProposalStatus::Draft, ProposalStatus::Revision, ProposalStatus::FinalUpload])) {
            return back()->with('error', 'Hanya proposal berstatus Draf, Revisi, atau Unggah Revisi Akhir yang dapat diajukan.');
        }

        $errors = $this->preCheckService->check($proposal);
        if (count($errors) > 0) {
            return back()->with('error', 'Tidak dapat mengajukan proposal. Masih ada data yang belum lengkap.');
        }

        $this->workflowService->submit($proposal, auth()->user());

        return redirect()->route('student.proposals.show', $proposal)
            ->with('success', 'Proposal berhasil diajukan ke Dosen Pendamping.');
    }

    public function withdraw(Proposal $proposal): RedirectResponse
    {
        $this->authorizeAccess($proposal);

        if ($proposal->status->isTerminal()) {
            return back()->with('error', 'Proposal ini sudah berada di status akhir dan tidak dapat ditarik.');
        }

        $this->workflowService->withdraw($proposal, auth()->user(), 'Ditarik oleh mahasiswa ketua tim.');

        return redirect()->route('student.proposals.show', $proposal)
            ->with('success', 'Proposal berhasil ditarik.');
    }

    private function authorizeAccess(Proposal $proposal): void
    {
        if ($proposal->leader_id !== auth()->id()) {
            abort(403, 'Akses ditolak. Anda bukan ketua pengusul proposal ini.');
        }
    }
}
