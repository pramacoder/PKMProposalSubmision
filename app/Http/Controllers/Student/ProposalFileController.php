<?php

namespace App\Http\Controllers\Student;

use App\Enums\DocumentStage;
use App\Enums\ProposalStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\DocumentRequirement;
use App\Models\Proposal;
use App\Models\ProposalFile;
use App\Services\FileStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProposalFileController extends Controller
{
    public function __construct(
        private readonly FileStorageService $fileService
    ) {}

    public function index(Proposal $proposal): View
    {
        $this->authorizeAccess($proposal);

        // Ambil syarat dokumen untuk skema proposal ini
        $requirements = $proposal->scheme->documentRequirements;

        // Ambil semua file proposal yang current, key by requirement_id untuk kemudahan view
        $files = $proposal->files()->with('uploadedBy')->get()->keyBy('requirement_id');

        return view('student.proposals.files.index', compact('proposal', 'requirements', 'files'));
    }

    public function store(Request $request, Proposal $proposal): RedirectResponse
    {
        $this->authorizeAccess($proposal);

        if (! $proposal->isEditable()) {
            return back()->with('error', 'Proposal sudah terkunci, tidak dapat mengunggah dokumen.');
        }

        $request->validate([
            'requirement_id' => ['required', 'integer', 'exists:document_requirements,id'],
            'file' => ['required', 'file'],
        ]);

        $requirement = DocumentRequirement::findOrFail($request->input('requirement_id'));
        if ($requirement->scheme_id !== $proposal->scheme_id) {
            abort(403, 'Persyaratan dokumen ini bukan untuk skema proposal Anda.');
        }

        $file = $request->file('file');

        // Validasi ekstensi
        $ext = strtolower($file->getClientOriginalExtension());
        $allowed = explode(',', str_replace(' ', '', strtolower($requirement->allowed_mimes))); // ex: "pdf,doc,docx"

        if (! in_array($ext, $allowed, true)) {
            return back()->with('error', "Ekstensi file tidak diizinkan. Hanya mendukung: {$requirement->allowed_mimes}");
        }

        // Validasi ukuran
        if ($file->getSize() > $requirement->max_kb * 1024) {
            return back()->with('error', "Ukuran file terlalu besar. Maksimal {$requirement->max_kb} KB.");
        }

        // Tentukan stage berdasarkan status proposal.
        // Jika status FinalUpload, berarti file tahap revisi final.
        $stage = DocumentStage::Submission->value;
        if ($proposal->status === ProposalStatus::FinalUpload) {
            $stage = DocumentStage::Final->value;
        } elseif ($proposal->status === ProposalStatus::Revision) {
            $stage = DocumentStage::Revision->value;
        }

        $this->fileService->uploadProposalFile($proposal, $requirement->id, $stage, $file, auth()->id());

        return back()->with('success', 'Dokumen berhasil diunggah.');
    }

    public function download(Proposal $proposal, ProposalFile $file)
    {
        // Siapapun yang bisa mengakses proposal, boleh download file-nya
        // Untuk sekarang, kita cek apakah ini ketua, atau (nanti) dosen/reviewer
        if ($proposal->leader_id !== auth()->id() && ! auth()->user()->hasRole(Role::SuperOperator) && ! auth()->user()->hasRole(Role::Operator)) {
            // Nanti ditambahkan logic untuk Reviewer & Supervisor
        }

        if ($file->proposal_id !== $proposal->id) {
            abort(404);
        }

        return $this->fileService->download($file);
    }

    private function authorizeAccess(Proposal $proposal): void
    {
        if ($proposal->leader_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }
    }
}
