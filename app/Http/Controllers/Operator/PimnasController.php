<?php

namespace App\Http\Controllers\Operator;

use App\Enums\ProposalStatus;
use App\Http\Controllers\Controller;
use App\Models\Proposal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PimnasController extends Controller
{
    public function index(): View
    {
        // Hanya proposal yang lolos evaluasi internal
        $proposals = Proposal::with(['scheme', 'cycle', 'leader'])
            ->where('status', ProposalStatus::InternalPassed)
            ->latest()
            ->get();

        return view('operator.pimnas.index', compact('proposals'));
    }

    public function update(Request $request, Proposal $proposal): RedirectResponse
    {
        if ($proposal->status !== ProposalStatus::InternalPassed) {
            abort(403);
        }

        $validated = $request->validate([
            'belmawa_result' => ['nullable', 'string', 'max:50'],
            'pimnas_status' => ['nullable', 'string', 'max:50'],
        ]);

        $proposal->update($validated);

        return back()->with('success', 'Status Belmawa / PIMNAS berhasil diperbarui.');
    }
}
