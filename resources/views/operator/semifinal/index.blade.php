<x-app-layout title="Keputusan Semifinal">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900 leading-tight">Keputusan Semifinal</h2>
        <p class="text-sm text-gray-500">Tentukan proposal mana yang lolos ke tahap pembimbingan universitas.</p>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Siklus</th>
                        <th>Judul Proposal</th>
                        <th>Skema</th>
                        <th>Status</th>
                        <th>Keputusan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($proposals as $proposal)
                    <tr class="hover:bg-gray-50 transition">
                        <td>{{ $proposal->cycle->name }}</td>
                        <td class="max-w-md">
                            <p class="font-medium text-gray-900 line-clamp-2" title="{{ $proposal->title }}">{{ $proposal->title }}</p>
                            <p class="text-xs text-gray-500 mt-1">Ketua: {{ $proposal->leader->name }}</p>
                        </td>
                        <td><span class="badge-indigo">{{ $proposal->scheme->code }}</span></td>
                        <td>
                            <span class="badge-{{ $proposal->status->color() }}">{{ $proposal->status->label() }}</span>
                        </td>
                        <td>
                            @if($proposal->semifinalResult)
                                @if($proposal->semifinalResult->passed)
                                    <span class="text-xs font-semibold text-emerald-700 bg-emerald-100 px-2 py-1 rounded-full"><x-icon name="check" class="w-3 h-3 inline"/> Lolos</span>
                                @else
                                    <span class="text-xs font-semibold text-red-700 bg-red-100 px-2 py-1 rounded-full"><x-icon name="x-mark" class="w-3 h-3 inline"/> Tidak Lolos</span>
                                @endif
                            @else
                                <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-1 rounded-full">Belum Diputuskan</span>
                            @endif
                        </td>
                        <td class="text-right whitespace-nowrap">
                            @if($proposal->status === \App\Enums\ProposalStatus::SemifinalDecision)
                                <a href="{{ route('operator.semifinal.show', $proposal) }}" class="btn-primary btn px-3 py-1.5 text-sm">
                                    Lihat & Putuskan
                                </a>
                            @else
                                <a href="{{ route('operator.semifinal.show', $proposal) }}" class="btn-secondary btn px-3 py-1.5 text-sm">
                                    Lihat Detail
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">Tidak ada proposal di tahap semifinal.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
