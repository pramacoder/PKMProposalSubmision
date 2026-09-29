<x-app-layout title="Detail Rubrik: {{ $rubric->scheme->code }}">
    <x-slot name="breadcrumb">
        <a href="{{ route('operator.rubric.index') }}" class="hover:text-gray-600">Rubrik Penilaian</a>
        <span>/</span>
        <span>{{ $rubric->scheme->code }}</span>
    </x-slot>

    <div class="max-w-3xl">
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">{{ $rubric->scheme->name }}</h2>
                    <div class="text-xs text-gray-400 mt-0.5">
                        Total bobot:
                        <span class="{{ $rubric->totalWeight() === 100 ? 'text-emerald-600 font-semibold' : 'text-red-500 font-semibold' }}">
                            {{ $rubric->totalWeight() }}/100
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($rubric->isLocked())
                        <span class="badge-green flex items-center gap-1">
                            <x-icon name="lock-closed" class="w-3 h-3"/> Terkunci
                        </span>
                        <form method="POST" action="{{ route('operator.rubric.duplicate', $rubric) }}">
                            @csrf
                            <button class="btn-secondary btn btn-sm">Buat Versi Baru</button>
                        </form>
                    @else
                        <a href="{{ route('operator.rubric.edit', $rubric) }}" class="btn-primary btn btn-sm">
                            <x-icon name="pencil" class="w-3.5 h-3.5"/> Edit
                        </a>
                        <form method="POST" action="{{ route('operator.rubric.confirm', $rubric) }}"
                              onsubmit="return confirm('Konfirmasi dan kunci rubrik ini? Total bobot harus 100.')">
                            @csrf
                            <button class="btn-success btn btn-sm">
                                <x-icon name="shield-check" class="w-3.5 h-3.5"/> Konfirmasi
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-12">#</th>
                            <th>Grup</th>
                            <th>Kriteria</th>
                            <th class="text-right">Bobot</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($rubric->criteria as $criterion)
                        <tr>
                            <td class="text-gray-400">{{ $loop->iteration }}</td>
                            <td class="text-xs text-gray-500">{{ $criterion->group_label ?? '—' }}</td>
                            <td class="text-sm">{{ $criterion->label }}</td>
                            <td class="text-right font-semibold">{{ $criterion->weight }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-gray-400 py-8">Belum ada kriteria.</td></tr>
                        @endforelse
                        <tr class="bg-gray-50 font-semibold">
                            <td colspan="3" class="text-right text-sm">Total Bobot</td>
                            <td class="text-right {{ $rubric->totalWeight() === 100 ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ $rubric->totalWeight() }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($rubric->isLocked())
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 text-xs text-gray-400">
                Dikonfirmasi oleh <strong>{{ $rubric->confirmedBy?->name }}</strong>
                pada {{ $rubric->confirmed_at?->format('d M Y H:i') }} WITA
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
