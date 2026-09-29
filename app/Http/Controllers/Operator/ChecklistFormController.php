<?php

namespace App\Http\Controllers\Operator;

use App\Enums\FormStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Operator\StoreChecklistFormRequest;
use App\Models\ChecklistForm;
use App\Models\ChecklistItem;
use App\Models\Cycle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ChecklistFormController extends Controller
{
    public function index(): View
    {
        $cycle = Cycle::active()->first();

        $forms = ChecklistForm::with(['cycle', 'items', 'confirmedBy'])
            ->when($cycle, fn ($q) => $q->where('cycle_id', $cycle->id))
            ->orderBy('checklist_group')
            ->get();

        return view('operator.checklist.index', compact('forms', 'cycle'));
    }

    public function show(ChecklistForm $checklistForm): View
    {
        $checklistForm->load(['items' => fn ($q) => $q->orderBy('sort'), 'cycle', 'confirmedBy']);

        return view('operator.checklist.show', compact('checklistForm'));
    }

    public function edit(ChecklistForm $checklistForm): View
    {
        if ($checklistForm->isLocked()) {
            return redirect()->route('operator.checklist.show', $checklistForm)
                ->with('error', 'Form sudah dikonfirmasi dan tidak dapat diubah. Buat versi baru jika diperlukan.');
        }

        $checklistForm->load(['items' => fn ($q) => $q->orderBy('sort')]);

        return view('operator.checklist.edit', compact('checklistForm'));
    }

    /** Tambah/ubah item checklist (batch update via request JSON) */
    public function update(StoreChecklistFormRequest $request, ChecklistForm $checklistForm): RedirectResponse
    {
        if ($checklistForm->isLocked()) {
            return back()->with('error', 'Form sudah terkunci. Tidak dapat diubah.');
        }

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $checklistForm): void {
            // Hapus item yang tidak ada di request (kecuali yang sudah ada asalkan id-nya dikirim)
            $keepIds = collect($validated['items'])->pluck('id')->filter()->all();

            $checklistForm->items()
                ->whereNotIn('id', $keepIds)
                ->delete();

            foreach ($validated['items'] as $index => $itemData) {
                if (! empty($itemData['id'])) {
                    ChecklistItem::where('id', $itemData['id'])->update([
                        'code' => $itemData['code'],
                        'label' => $itemData['label'],
                        'kind' => $itemData['kind'],
                        'sort' => $index + 1,
                    ]);
                } else {
                    $checklistForm->items()->create([
                        'code' => $itemData['code'],
                        'label' => $itemData['label'],
                        'kind' => $itemData['kind'],
                        'sort' => $index + 1,
                    ]);
                }
            }
        });

        return redirect()->route('operator.checklist.show', $checklistForm)
            ->with('success', 'Form checklist berhasil diperbarui.');
    }

    /** Konfirmasi/kunci form checklist (RULE-42) */
    public function confirm(ChecklistForm $checklistForm): RedirectResponse
    {
        if ($checklistForm->isLocked()) {
            return back()->with('error', 'Form sudah dikonfirmasi sebelumnya.');
        }

        if ($checklistForm->items()->count() === 0) {
            return back()->with('error', 'Form harus memiliki minimal satu item sebelum dikonfirmasi.');
        }

        $checklistForm->update([
            'status' => FormStatus::Confirmed,
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        return back()->with('success', 'Form checklist berhasil dikonfirmasi dan dikunci.');
    }

    /** Buat versi baru dari form yang sudah terkunci */
    public function duplicate(ChecklistForm $checklistForm): RedirectResponse
    {
        $checklistForm->load('items');

        $newForm = DB::transaction(function () use ($checklistForm): ChecklistForm {
            $newForm = ChecklistForm::create([
                'cycle_id' => $checklistForm->cycle_id,
                'checklist_group' => $checklistForm->checklist_group,
                'status' => FormStatus::Draft,
            ]);

            foreach ($checklistForm->items as $item) {
                $newForm->items()->create([
                    'code' => $item->code,
                    'label' => $item->label,
                    'kind' => $item->kind,
                    'sort' => $item->sort,
                ]);
            }

            return $newForm;
        });

        return redirect()->route('operator.checklist.edit', $newForm)
            ->with('success', 'Versi baru form checklist berhasil dibuat.');
    }
}
