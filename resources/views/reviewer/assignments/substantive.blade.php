<x-app-layout title="Review {{ $stage === \App\Enums\ReviewStage::Final ? 'Final' : 'Substantif Awal' }}">
    <x-slot name="breadcrumb">
        <a href="{{ route('reviewer.assignments.index') }}" class="hover:text-gray-600">Daftar Penugasan</a>
        <span>/</span>
        <span>Review {{ $stage === \App\Enums\ReviewStage::Final ? 'Final' : 'Substantif' }}</span>
    </x-slot>

    <div class="flex flex-col xl:flex-row items-start gap-8">
        
        {{-- Kolom Kiri: Detail Proposal & Berkas --}}
        <div class="w-full xl:w-1/3 flex-shrink-0 space-y-6">
            <div class="card p-6 border-t-4 border-emerald-500">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Informasi Proposal</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Judul</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $assignment->proposal->title }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Skema</dt>
                        <dd class="mt-1"><span class="badge-indigo">{{ $assignment->proposal->scheme->code }}</span></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ketua Tim</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assignment->proposal->leader->name }}</dd>
                    </div>
                </dl>
            </div>

            <div class="card">
                <div class="card-header border-b border-gray-100">
                    <h3 class="card-title">Berkas Terunggah</h3>
                </div>
                <div class="p-0">
                    <table class="table w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            @forelse($assignment->proposal->files as $file)
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900">{{ $file->requirement->label }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $file->original_name }}</p>
                                </td>
                                <td class="px-6 py-4 text-right align-middle">
                                    <a href="{{ route('student.proposals.files.download', [$assignment->proposal_id, $file]) }}" class="btn-secondary btn px-3 py-1.5 text-xs" target="_blank">Unduh</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-6 py-6 text-center text-gray-500 italic">Belum ada berkas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Form Penilaian --}}
        <div class="flex-1 w-full">
            <div class="card bg-white shadow-xl shadow-emerald-900/5 ring-1 ring-emerald-900/10"
                 x-data="substantiveForm({{ $assignment->rubric->criteria->toJson() }}, {{ $results->toJson() }})">
                <div class="p-6 border-b border-gray-100 bg-emerald-50/50 rounded-t-xl flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Form Penilaian {{ $stage === \App\Enums\ReviewStage::Final ? 'Final' : 'Substantif' }}</h2>
                        <p class="text-sm text-gray-500 mt-1">Berikan skor (1, 2, 3, 5, 6, 7) untuk masing-masing kriteria.</p>
                    </div>
                    <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-emerald-100 text-center">
                        <p class="text-xs text-emerald-600 font-bold uppercase tracking-wider mb-1">Total Nilai</p>
                        <p class="text-2xl font-black text-gray-900" x-text="calculateTotal()"></p>
                    </div>
                </div>

                <form method="POST" action="{{ $stage === \App\Enums\ReviewStage::Final ? route('reviewer.assignments.final.store', $assignment) : route('reviewer.assignments.substantive.store', $assignment) }}" class="p-0">
                    @csrf
                    
                    <div class="table-wrapper rounded-none border-0">
                        <table class="table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th>Kriteria Penilaian</th>
                                    <th class="w-16 text-center">Bobot</th>
                                    <th class="w-24 text-center">Skor</th>
                                    <th class="w-24 text-center">Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @php $currentGroup = null; @endphp
                                @foreach($assignment->rubric->criteria as $criterion)
                                    @if($criterion->group_label && $criterion->group_label !== $currentGroup)
                                        <tr class="bg-gray-100/80">
                                            <td colspan="4" class="px-4 py-2 text-xs font-bold text-gray-700 uppercase tracking-wider">
                                                {{ $criterion->group_label }}
                                            </td>
                                        </tr>
                                        @php $currentGroup = $criterion->group_label; @endphp
                                    @endif

                                    @php
                                        $res = $results->get($criterion->id);
                                        $scoreVal = old("scores.{$criterion->id}.score", $res?->score ?? '');
                                        $commentVal = old("scores.{$criterion->id}.comment", $res?->comment ?? '');
                                    @endphp
                                    <tr class="hover:bg-emerald-50/30 transition-colors">
                                        <td class="align-top pt-4 pb-4">
                                            <p class="text-sm text-gray-800 font-medium leading-relaxed">{{ $criterion->label }}</p>
                                            <div class="mt-3">
                                                <input type="text" name="scores[{{ $criterion->id }}][comment]" 
                                                       value="{{ $commentVal }}"
                                                       class="form-input text-sm border-gray-200 bg-gray-50 focus:bg-white placeholder-gray-400" 
                                                       placeholder="Komentar opsional untuk kriteria ini..."
                                                       {{ $assignment->isSubmitted() ? 'disabled' : '' }}>
                                                @error("scores.{$criterion->id}.comment")
                                                    <p class="form-error mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </td>
                                        <td class="align-top pt-4 text-center">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-700 text-xs font-bold">
                                                {{ $criterion->weight }}%
                                            </span>
                                        </td>
                                        <td class="align-top pt-4 text-center">
                                            <select name="scores[{{ $criterion->id }}][score]" 
                                                    x-model="scores[{{ $criterion->id }}]"
                                                    class="form-input text-center text-sm font-semibold {{ $assignment->isSubmitted() ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                                    required
                                                    {{ $assignment->isSubmitted() ? 'disabled' : '' }}>
                                                <option value="">-</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                            </select>
                                            @error("scores.{$criterion->id}.score")
                                                <p class="text-xs text-red-600 mt-1 font-medium">Wajib diisi.</p>
                                            @enderror
                                        </td>
                                        <td class="align-top pt-4 text-center">
                                            <div class="text-lg font-bold text-emerald-600 bg-emerald-50 rounded px-2 py-1 inline-block min-w-[3rem]"
                                                 x-text="calculateRow({{ $criterion->id }}, {{ $criterion->weight }})">
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(! $assignment->isSubmitted())
                    <div class="p-6 bg-gray-50 flex items-center justify-end gap-3 rounded-b-xl border-t border-gray-100">
                        <button type="submit" name="action" value="save" class="btn-secondary btn px-5">
                            Simpan Progres
                        </button>
                        <button type="submit" name="action" value="submit" class="btn-primary btn px-8"
                                onclick="return confirm('Apakah Anda yakin ingin mensubmit hasil review ini? Total nilai Anda adalah ' + calculateTotal() + '. Setelah disubmit, Anda tidak dapat mengubahnya lagi.')">
                            Submit Review
                        </button>
                    </div>
                    @else
                    <div class="p-6 bg-emerald-50 rounded-b-xl border-t border-emerald-100 flex items-center gap-3 text-emerald-800">
                        <x-icon name="check-circle" class="w-6 h-6"/>
                        <div>
                            <p class="font-bold">Review Telah Disubmit</p>
                            <p class="text-sm text-emerald-700">Anda sudah menyelesaikan review {{ $stage === \App\Enums\ReviewStage::Final ? 'final' : 'substantif' }} untuk proposal ini.</p>
                        </div>
                    </div>
                    @endif
                </form>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('substantiveForm', (criteria, initialResults) => ({
                scores: {},
                weights: {},
                init() {
                    // Populate weights
                    criteria.forEach(c => {
                        this.weights[c.id] = c.weight;
                        this.scores[c.id] = initialResults[c.id]?.score || '';
                    });
                },
                calculateRow(id, weight) {
                    const score = parseInt(this.scores[id]);
                    if (isNaN(score)) return '-';
                    return score * weight;
                },
                calculateTotal() {
                    let total = 0;
                    for (const id in this.scores) {
                        const score = parseInt(this.scores[id]);
                        if (!isNaN(score) && this.weights[id]) {
                            total += score * this.weights[id];
                        }
                    }
                    return total;
                }
            }));
        });
    </script>
</x-app-layout>
