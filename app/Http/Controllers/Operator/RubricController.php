<?php

namespace App\Http\Controllers\Operator;

use App\Enums\FormStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Operator\StoreRubricCriterionRequest;
use App\Models\Cycle;
use App\Models\Rubric;
use App\Models\RubricCriterion;
use App\Models\Scheme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RubricController extends Controller
{
    public function index(): View
    {
        $cycle = Cycle::active()->first();

        $rubrics = Rubric::with(['scheme', 'confirmedBy', 'criteria'])
            ->when($cycle, fn ($q) => $q->where('cycle_id', $cycle->id))
            ->get()
            ->groupBy('scheme.code');

        $schemes = Scheme::all();

        return view('operator.rubric.index', compact('rubrics', 'schemes', 'cycle'));
    }

    public function show(Rubric $rubric): View
    {
        $rubric->load(['scheme', 'criteria' => fn ($q) => $q->orderBy('sort'), 'confirmedBy', 'cycle']);

        return view('operator.rubric.show', compact('rubric'));
    }

    public function edit(Rubric $rubric): View
    {
        if ($rubric->isLocked()) {
            return redirect()->route('operator.rubric.show', $rubric)
                ->with('error', 'Rubrik sudah dikonfirmasi dan tidak dapat diubah. Buat versi baru jika diperlukan.');
        }

        $rubric->load(['criteria' => fn ($q) => $q->orderBy('sort'), 'scheme']);

        return view('operator.rubric.edit', compact('rubric'));
    }

    public function update(StoreRubricCriterionRequest $request, Rubric $rubric): RedirectResponse
    {
        if ($rubric->isLocked()) {
            return back()->with('error', 'Rubrik sudah terkunci. Tidak dapat diubah.');
        }

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $rubric): void {
            $keepIds = collect($validated['criteria'])->pluck('id')->filter()->all();

            $rubric->criteria()->whereNotIn('id', $keepIds)->delete();

            foreach ($validated['criteria'] as $index => $data) {
                if (! empty($data['id'])) {
                    RubricCriterion::where('id', $data['id'])->update([
                        'group_label' => $data['group_label'] ?? null,
                        'label' => $data['label'],
                        'weight' => $data['weight'],
                        'sort' => $index + 1,
                    ]);
                } else {
                    $rubric->criteria()->create([
                        'group_label' => $data['group_label'] ?? null,
                        'label' => $data['label'],
                        'weight' => $data['weight'],
                        'sort' => $index + 1,
                    ]);
                }
            }
        });

        return redirect()->route('operator.rubric.show', $rubric)
            ->with('success', 'Rubrik berhasil diperbarui.');
    }

    /** Konfirmasi/kunci rubrik (RULE-42) */
    public function confirm(Rubric $rubric): RedirectResponse
    {
        if ($rubric->isLocked()) {
            return back()->with('error', 'Rubrik sudah dikonfirmasi sebelumnya.');
        }

        $totalWeight = $rubric->totalWeight();
        if ($totalWeight !== 100) {
            return back()->with('error', "Total bobot harus tepat 100. Saat ini: {$totalWeight}. Sesuaikan bobot sebelum mengonfirmasi.");
        }

        $rubric->update([
            'status' => FormStatus::Confirmed,
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        return back()->with('success', 'Rubrik berhasil dikonfirmasi dan dikunci.');
    }

    /** Buat versi baru dari rubrik yang sudah terkunci */
    public function duplicate(Rubric $rubric): RedirectResponse
    {
        $rubric->load('criteria');

        $newRubric = DB::transaction(function () use ($rubric): Rubric {
            $newRubric = Rubric::create([
                'cycle_id' => $rubric->cycle_id,
                'scheme_id' => $rubric->scheme_id,
                'status' => FormStatus::Draft,
            ]);

            foreach ($rubric->criteria as $criterion) {
                $newRubric->criteria()->create([
                    'group_label' => $criterion->group_label,
                    'label' => $criterion->label,
                    'weight' => $criterion->weight,
                    'sort' => $criterion->sort,
                ]);
            }

            return $newRubric;
        });

        return redirect()->route('operator.rubric.edit', $newRubric)
            ->with('success', 'Versi baru rubrik berhasil dibuat.');
    }
}
