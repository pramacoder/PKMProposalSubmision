<x-app-layout title="Rubrik Penilaian Substantif">
    @if(!$cycle)
        <div class="alert-warning"><x-icon name="cog" class="w-5 h-5"/><span>Belum ada siklus aktif.</span></div>
    @else
        <div class="mb-4"><span class="badge-green">Siklus: {{ $cycle->name }}</span></div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($schemes as $scheme)
                @php $schemeRubrics = $rubrics->get($scheme->code, collect()); @endphp
                <div class="card">
                    <div class="card-header">
                        <div>
                            <span class="badge-indigo">{{ $scheme->code }}</span>
                            <div class="font-medium text-sm text-gray-800 mt-1">{{ $scheme->name }}</div>
                        </div>
                    </div>
                    <div class="card-body space-y-2">
                        @forelse($schemeRubrics as $rubric)
                            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 border border-gray-100">
                                <div>
                                    <div class="text-xs font-medium text-gray-700">
                                        {{ $rubric->criteria->count() }} kriteria · Total bobot: {{ $rubric->totalWeight() }}/100
                                    </div>
                                    @if($rubric->totalWeight() === 100)
                                        <div class="text-xs text-emerald-600 mt-0.5">✓ Bobot valid</div>
                                    @else
                                        <div class="text-xs text-red-500 mt-0.5">⚠ Bobot belum 100</div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5">
                                    @if($rubric->isLocked())
                                        <span class="badge-green text-xs">Terkunci</span>
                                    @else
                                        <span class="badge-amber text-xs">Draft</span>
                                    @endif
                                    <a href="{{ route('operator.rubric.show', $rubric) }}"
                                       class="btn-secondary btn btn-sm">
                                        <x-icon name="eye" class="w-3.5 h-3.5"/>
                                    </a>
                                    @if(!$rubric->isLocked())
                                        <a href="{{ route('operator.rubric.edit', $rubric) }}"
                                           class="btn-primary btn btn-sm">
                                            <x-icon name="pencil" class="w-3.5 h-3.5"/>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 italic">Belum ada rubrik untuk skema ini.</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
