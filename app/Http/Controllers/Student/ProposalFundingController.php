<?php

namespace App\Http\Controllers\Student;

use App\Enums\FundingSource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreProposalFundingRequest;
use App\Models\Proposal;
use App\Models\ProposalFunding;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProposalFundingController extends Controller
{
    public function index(Proposal $proposal): View
    {
        $this->authorizeAccess($proposal);

        $proposal->load('fundings');
        $setting = $proposal->scheme->settingForCycle($proposal->cycle_id);

        return view('student.proposals.funding.index', compact('proposal', 'setting'));
    }

    public function store(StoreProposalFundingRequest $request, Proposal $proposal): RedirectResponse
    {
        $this->authorizeAccess($proposal);

        if (! $proposal->isEditable()) {
            return back()->with('error', 'Proposal sudah terkunci.');
        }

        $validated = $request->validated();

        $setting = $proposal->scheme->settingForCycle($proposal->cycle_id);

        foreach (FundingSource::cases() as $source) {
            $amount = $validated['amounts'][$source->value] ?? 0;

            // Validasi manual sesuai limit dari setting (lebih ketat bisa di form request)
            if ($source === FundingSource::Belmawa && ($amount < $setting->min_belmawa || $amount > $setting->max_belmawa)) {
                return back()->with('error', 'Dana Belmawa harus antara Rp'.number_format($setting->min_belmawa, 0, ',', '.').' s.d. Rp'.number_format($setting->max_belmawa, 0, ',', '.'));
            }
            if ($source === FundingSource::University && ($amount < $setting->min_pt || $amount > $setting->max_pt)) {
                return back()->with('error', 'Dana Perguruan Tinggi harus antara Rp'.number_format($setting->min_pt, 0, ',', '.').' s.d. Rp'.number_format($setting->max_pt, 0, ',', '.'));
            }
            if ($source === FundingSource::Partner && $amount > $setting->max_partner) {
                return back()->with('error', 'Dana Mitra maksimal Rp'.number_format($setting->max_partner, 0, ',', '.'));
            }

            ProposalFunding::updateOrCreate(
                ['proposal_id' => $proposal->id, 'source' => $source->value],
                ['amount' => $amount]
            );
        }

        return back()->with('success', 'Rencana anggaran berhasil diperbarui.');
    }

    private function authorizeAccess(Proposal $proposal): void
    {
        if ($proposal->leader_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }
    }
}
