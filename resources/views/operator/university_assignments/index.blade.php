<x-app-layout title="Penugasan Dosen Universitas">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900 leading-tight">Penugasan Dosen Universitas</h2>
        <p class="text-sm text-gray-500">Tetapkan Dosen Universitas untuk proposal yang lolos seleksi.</p>
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
                        <th>Dosen Universitas</th>
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
                            @if($proposal->universityLecturer)
                                <span class="font-medium text-gray-900 text-sm">{{ $proposal->universityLecturer->name }}</span>
                            @else
                                <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-1 rounded-full">Belum Ditugaskan</span>
                            @endif
                        </td>
                        <td class="text-right whitespace-nowrap">
                            @if($proposal->status === \App\Enums\ProposalStatus::UniversityAssignment)
                                <a href="{{ route('operator.university-assignments.create', $proposal) }}" class="btn-primary btn px-3 py-1.5 text-sm">
                                    Tugaskan Dosen
                                </a>
                            @else
                                <span class="text-xs text-gray-500 font-medium italic">Sedang/Selesai Proses</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">Tidak ada proposal yang menunggu penugasan dosen universitas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
