<x-app-layout title="Edit Rubrik: {{ $rubric->scheme->code }}">
    <x-slot name="breadcrumb">
        <a href="{{ route('operator.rubric.index') }}" class="hover:text-gray-600">Rubrik Penilaian</a>
        <span>/</span>
        <a href="{{ route('operator.rubric.show', $rubric) }}" class="hover:text-gray-600">{{ $rubric->scheme->code }}</a>
        <span>/</span>
        <span>Edit</span>
    </x-slot>

    <div class="max-w-3xl" x-data="rubricEditor(@js($rubric->criteria->toArray()))">
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">{{ $rubric->scheme->name }}</h2>
                    <div class="text-xs mt-0.5">
                        Total bobot:
                        <span class="font-semibold" :class="totalWeight === 100 ? 'text-emerald-600' : 'text-red-500'"
                              x-text="totalWeight + '/100'"></span>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('operator.rubric.update', $rubric) }}" class="card-body">
                @csrf @method('PATCH')

                <div class="space-y-3 mb-5">
                    <template x-for="(crit, index) in criteria" :key="crit._key">
                        <div class="p-4 rounded-xl border border-gray-200 bg-gray-50 flex gap-3 items-start">
                            <div class="flex-1 grid grid-cols-1 sm:grid-cols-4 gap-3">
                                <input type="hidden" :name="`criteria[${index}][id]`" :value="crit.id">
                                <div>
                                    <label class="form-label text-xs">Grup</label>
                                    <input type="text" :name="`criteria[${index}][group_label]`"
                                           x-model="crit.group_label"
                                           class="form-input" placeholder="Opsional">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="form-label text-xs">Label Kriteria <span class="text-red-500">*</span></label>
                                    <input type="text" :name="`criteria[${index}][label]`" x-model="crit.label"
                                           class="form-input" required>
                                </div>
                                <div>
                                    <label class="form-label text-xs">Bobot <span class="text-red-500">*</span></label>
                                    <input type="number" :name="`criteria[${index}][weight]`" x-model.number="crit.weight"
                                           class="form-input" min="1" max="100" required>
                                </div>
                            </div>
                            <button type="button" @click="removeCriterion(index)"
                                    class="mt-6 text-red-400 hover:text-red-600 transition flex-shrink-0">
                                <x-icon name="trash" class="w-4 h-4"/>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="p-3 rounded-lg text-sm text-center"
                     :class="totalWeight === 100 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'">
                    <span x-text="totalWeight === 100 ? '✓ Total bobot valid (100)' : `⚠ Total bobot: ${totalWeight}/100. Harus tepat 100 untuk bisa dikonfirmasi.`"></span>
                </div>

                <div class="flex items-center justify-between pt-4 mt-4 border-t border-gray-100">
                    <button type="button" @click="addCriterion()" class="btn-secondary btn btn-sm">
                        <x-icon name="plus" class="w-4 h-4"/> Tambah Kriteria
                    </button>
                    <div class="flex gap-2">
                        <a href="{{ route('operator.rubric.show', $rubric) }}" class="btn-secondary btn">Batal</a>
                        <button type="submit" class="btn-primary btn">
                            <x-icon name="check" class="w-4 h-4"/> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function rubricEditor(initialCriteria) {
        return {
            criteria: initialCriteria.map(c => ({ ...c, _key: c.id ?? Math.random() })),
            get totalWeight() {
                return this.criteria.reduce((sum, c) => sum + (parseInt(c.weight) || 0), 0);
            },
            addCriterion() {
                this.criteria.push({ id: null, group_label: '', label: '', weight: 0, _key: Math.random() });
            },
            removeCriterion(index) {
                if (this.criteria.length <= 1) return;
                this.criteria.splice(index, 1);
            }
        };
    }
    </script>
    @endpush
</x-app-layout>
