<?php

namespace App\Services;

use App\Models\Proposal;
use App\Models\ProposalFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileStorageService
{
    /**
     * Upload file and save to proposal_files.
     */
    public function uploadProposalFile(
        Proposal $proposal,
        int $requirementId,
        string $stage,
        UploadedFile $file,
        int $userId
    ): ProposalFile {
        // Tentukan path: proposals/{cycle_id}/{proposal_id}/{stage}/
        $dir = sprintf('proposals/%d/%d/%s', $proposal->cycle_id, $proposal->id, $stage);

        // Buat nama unik aman (timestamp_random.ext)
        $filename = sprintf('%d_%s.%s', time(), Str::random(8), $file->getClientOriginalExtension());

        // Simpan ke disk 'local' (privat)
        $path = $file->storeAs($dir, $filename, 'local');

        // Nonaktifkan is_current file sebelumnya dengan requirement & stage yang sama
        ProposalFile::where('proposal_id', $proposal->id)
            ->where('requirement_id', $requirementId)
            ->where('stage', $stage)
            ->update(['is_current' => false]);

        // Cari versi terakhir
        $latestVersion = ProposalFile::where('proposal_id', $proposal->id)
            ->where('requirement_id', $requirementId)
            ->where('stage', $stage)
            ->max('version') ?? 0;

        // Simpan record DB
        return ProposalFile::create([
            'proposal_id' => $proposal->id,
            'requirement_id' => $requirementId,
            'stage' => $stage,
            'version' => $latestVersion + 1,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
            'checksum' => hash_file('sha256', $file->getRealPath()),
            'uploaded_by' => $userId,
            'is_current' => true,
        ]);
    }

    /**
     * Download file (return Response)
     */
    public function download(ProposalFile $proposalFile)
    {
        if (! Storage::disk('local')->exists($proposalFile->path)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        return Storage::disk('local')->download($proposalFile->path, $proposalFile->original_name);
    }
}
