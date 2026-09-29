<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Operator\UpdatePhaseWindowRequest;
use App\Models\Cycle;
use App\Models\PhaseWindow;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ControlRoomController extends Controller
{
    /**
     * Tampilkan ruang kontrol: jadwal fase dan kuota skema siklus aktif.
     */
    public function index(): View
    {
        $cycle = Cycle::active()
            ->with(['phaseWindows', 'schemeSettings.scheme'])
            ->first();

        $phases = $this->phaseList();

        return view('operator.control.index', compact('cycle', 'phases'));
    }

    /**
     * Update jendela fase (opens_at, closes_at, forced_open, forced_closed).
     */
    public function updatePhaseWindow(UpdatePhaseWindowRequest $request, Cycle $cycle, string $phase): RedirectResponse
    {
        $validated = $request->validated();

        $window = PhaseWindow::firstOrNew([
            'cycle_id' => $cycle->id,
            'phase' => $phase,
        ]);

        $window->fill([
            'opens_at' => $validated['opens_at'] ?? null,
            'closes_at' => $validated['closes_at'] ?? null,
            'forced_open' => $validated['forced_open'] ?? false,
            'forced_closed' => $validated['forced_closed'] ?? false,
            'forced_reason' => $validated['forced_reason'] ?? null,
            'forced_by' => ($validated['forced_open'] || $validated['forced_closed'])
                ? auth()->id()
                : null,
        ]);

        $window->save();

        return back()->with('success', "Jendela fase \"{$phase}\" berhasil diperbarui.");
    }

    /**
     * Daftar fase yang dikelola PhaseGate, dengan label bahasa Indonesia.
     *
     * @return array<string, string>
     */
    private function phaseList(): array
    {
        return [
            'student_submission' => 'Pengajuan Proposal (Mahasiswa)',
            'supervisor_validation_1' => 'Validasi Pembimbing Tahap 1',
            'admin_review' => 'Review Administratif',
            'substantive_review' => 'Review Substantif Awal',
            'revision' => 'Revisi Proposal',
            'supervisor_validation_2' => 'Validasi Pembimbing Tahap 2',
            'final_review' => 'Review Final',
            'final_upload' => 'Unggah Dokumen Final',
            'university_validation' => 'Validasi Dosen Universitas',
            'final_decision' => 'Keputusan Akhir (Batch)',
        ];
    }
}
