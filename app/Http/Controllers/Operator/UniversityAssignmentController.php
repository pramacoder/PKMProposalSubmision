<?php

namespace App\Http\Controllers\Operator;

use App\Enums\ProposalStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\User;
use App\Services\ProposalWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UniversityAssignmentController extends Controller
{
    public function __construct(
        private readonly ProposalWorkflowService $workflowService
    ) {}

    public function index(): View
    {
        // Tampilkan proposal di tahap penugasan universitas atau sedang validasi universitas
        $proposals = Proposal::with(['scheme', 'cycle', 'universityLecturer'])
            ->whereIn('status', [
                ProposalStatus::UniversityAssignment,
                ProposalStatus::FinalUpload,
                ProposalStatus::UniversityValidation,
            ])
            ->latest('updated_at')
            ->get();

        return view('operator.university_assignments.index', compact('proposals'));
    }

    public function create(Proposal $proposal): View
    {
        if ($proposal->status !== ProposalStatus::UniversityAssignment) {
            abort(403, 'Proposal ini sudah ditugaskan dosen universitas atau statusnya tidak sesuai.');
        }

        // Ambil semua pengguna dengan peran university_lecturer
        $lecturers = User::whereHas('roles', fn ($q) => $q->where('name', Role::UniversityLecturer->value))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('operator.university_assignments.create', compact('proposal', 'lecturers'));
    }

    public function store(Request $request, Proposal $proposal): RedirectResponse
    {
        if ($proposal->status !== ProposalStatus::UniversityAssignment) {
            return back()->with('error', 'Proposal ini sudah tidak berada di tahap penugasan dosen universitas.');
        }

        $validated = $request->validate([
            'university_lecturer_id' => ['required', 'exists:users,id'],
        ]);

        // Tetapkan dosen universitas
        $proposal->update([
            'university_lecturer_id' => $validated['university_lecturer_id']
        ]);

        // Pindahkan status ke FinalUpload
        $this->workflowService->transition(
            $proposal,
            ProposalStatus::FinalUpload,
            auth()->user(),
            'Penugasan Dosen Universitas, menunggu unggah revisi akhir dari mahasiswa.'
        );

        return redirect()->route('operator.university-assignments.index')
            ->with('success', 'Dosen Universitas berhasil ditugaskan dan proposal masuk ke tahap Unggah Revisi Akhir.');
    }
}
