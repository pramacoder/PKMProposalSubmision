<x-app-layout title="Proposal Validasi">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900 leading-tight">Daftar Validasi Proposal</h2>
        <p class="text-sm text-gray-500">Daftar proposal yang ditugaskan kepada Anda untuk validasi tahap akhir universitas.</p>
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
                        <td class="text-right whitespace-nowrap">
                            @if($proposal->status === \App\Enums\ProposalStatus::UniversityValidation)
                                <a href="{{ route('university.proposals.show', $proposal) }}" class="btn-primary btn px-3 py-1.5 text-sm">
                                    Lakukan Validasi
                                </a>
                            @else
                                <a href="{{ route('university.proposals.show', $proposal) }}" class="btn-secondary btn px-3 py-1.5 text-sm">
                                    Lihat Detail
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">Belum ada proposal yang ditugaskan kepada Anda.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
