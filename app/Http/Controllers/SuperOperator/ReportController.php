<?php

namespace App\Http\Controllers\SuperOperator;

use App\Enums\ProposalStatus;
use App\Http\Controllers\Controller;
use App\Models\Cycle;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $activeCycle = Cycle::active()->first();
        $cycleId = $request->query('cycle_id', $activeCycle?->id);
        
        $cycles = Cycle::orderByDesc('year')->get();

        // 1. Berita Acara per Bidang (Skema) - Rekap jumlah proposal didanai per skema
        $beritaAcara = Proposal::where('cycle_id', $cycleId)
            ->whereIn('status', [ProposalStatus::InternalPassed, ProposalStatus::InternalNotPassed])
            ->select('scheme_id', DB::raw('count(*) as total'), DB::raw('sum(case when status = "internal_passed" then 1 else 0 end) as passed_count'))
            ->groupBy('scheme_id')
            ->with('scheme')
            ->get();

        // 2. Laporan Simbelmawa - Detail proposal yang lolos didanai internal (siap diunggah ke Simbelmawa)
        $simbelmawaReports = Proposal::with(['scheme', 'leader', 'supervisor', 'fundings.source'])
            ->where('cycle_id', $cycleId)
            ->where('status', ProposalStatus::InternalPassed)
            ->get();

        // 3. Laporan Prestasi (PIMNAS & Belmawa)
        $prestasiReports = Proposal::with(['scheme', 'leader', 'supervisor'])
            ->where('cycle_id', $cycleId)
            ->whereNotNull('belmawa_result') // Minimal sudah ada status nasional
            ->get();

        return view('super_operator.reports.index', compact(
            'cycles', 
            'cycleId', 
            'beritaAcara', 
            'simbelmawaReports', 
            'prestasiReports'
        ));
    }
}
