<x-app-layout title="Daftar Proposal Bimbingan">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900 leading-tight">Daftar Proposal Bimbingan</h2>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Siklus</th>
                        <th>Judul Proposal</th>
                        <th>Skema</th>
                        <th>Ketua Tim</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($proposals as $proposal)
                    <tr class="hover:bg-gray-50 transition">
                        <td>{{ $proposal->cycle->name }}</td>
                        <td class="max-w-md">
                            <p class="font-medium text-gray-900 line-clamp-2" title="{{ $proposal->title }}">{{ $proposal->title }}</p>
                        </td>
                        <td><span class="badge-indigo">{{ $proposal->scheme->code }}</span></td>
                        <td>
                            <p class="font-medium text-gray-900">{{ $proposal->leader->name }}</p>
                            <p class="text-xs text-gray-500">{{ $proposal->leader->nim ?? '-' }}</p>
                        </td>
                        <td>
                            <span class="badge-{{ $proposal->status->color() }}">{{ $proposal->status->label() }}</span>
                        </td>
                        <td class="text-right whitespace-nowrap">
                            @if(in_array($proposal->status->value, ['supervisor_validation_1', 'supervisor_validation_2']))
                                <a href="{{ route('supervisor.proposals.show', $proposal) }}" class="btn-primary btn px-3 py-1.5 text-sm">Validasi</a>
                            @else
                                <a href="{{ route('supervisor.proposals.show', $proposal) }}" class="btn-secondary btn px-3 py-1.5 text-sm">Lihat Detail</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">Tidak ada proposal bimbingan yang aktif.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
