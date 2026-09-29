<?php

namespace App\Http\Controllers\SuperOperator;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperOperator\StoreCycleRequest;
use App\Models\Cycle;
use App\Models\Scheme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CycleController extends Controller
{
    public function index(): View
    {
        $cycles = Cycle::withCount('proposals')->orderByDesc('year')->get();

        return view('super-operator.cycles.index', compact('cycles'));
    }

    public function create(): View
    {
        return view('super-operator.cycles.create');
    }

    public function store(StoreCycleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated): void {
            // Nonaktifkan siklus lain jika ini diset aktif
            if ($validated['is_active']) {
                Cycle::query()->update(['is_active' => false]);
            }

            $cycle = Cycle::create($validated);

            // Auto-create CycleSchemeSetting default untuk semua skema
            $schemes = Scheme::all();
            foreach ($schemes as $scheme) {
                $cycle->schemeSettings()->create([
                    'scheme_id' => $scheme->id,
                    'quota' => 0,
                    'min_pt' => 0,
                    'max_pt' => 0,
                    'max_partner' => 1000000,
                    'min_belmawa' => 6000000,
                    'max_belmawa' => 8000000,
                    'max_admin_percent' => 20,
                    'min_months' => 3,
                    'max_months' => 5,
                ]);
            }
        });

        return redirect()->route('super-operator.cycles.index')
            ->with('success', 'Siklus PKM berhasil dibuat.');
    }

    public function show(Cycle $cycle): View
    {
        $cycle->load(['schemeSettings.scheme', 'phaseWindows', 'themes']);

        return view('super-operator.cycles.show', compact('cycle'));
    }

    public function edit(Cycle $cycle): View
    {
        return view('super-operator.cycles.edit', compact('cycle'));
    }

    public function update(StoreCycleRequest $request, Cycle $cycle): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $cycle): void {
            if ($validated['is_active'] && ! $cycle->is_active) {
                Cycle::where('id', '!=', $cycle->id)->update(['is_active' => false]);
            }

            $cycle->update($validated);
        });

        return redirect()->route('super-operator.cycles.index')
            ->with('success', 'Siklus berhasil diperbarui.');
    }

    public function destroy(Cycle $cycle): RedirectResponse
    {
        if ($cycle->proposals()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus siklus yang sudah memiliki proposal.');
        }

        $cycle->delete();

        return redirect()->route('super-operator.cycles.index')
            ->with('success', 'Siklus berhasil dihapus.');
    }
}
