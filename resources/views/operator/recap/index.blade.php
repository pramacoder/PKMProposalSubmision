<x-app-layout title="Rekapitulasi Review">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900 leading-tight">Rekapitulasi Review</h2>
        <p class="text-sm text-gray-500">Pilih proposal untuk melihat kompilasi hasil review dan menetapkan status revisi.</p>
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
                        <th>Progres Review</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($proposals as $proposal)
                    @php
                        $totalReviewers = $proposal->assignments->count();
                        $submittedReviewers = $proposal->assignments->where('status', 'submitted')->count();
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td>{{ $proposal->cycle->name }}</td>
                        <td class="max-w-md">
                            <p class="font-medium text-gray-900 line-clamp-2" title="{{ $proposal->title }}">{{ $proposal->title }}</p>
                        </td>
                        <td><span class="badge-indigo">{{ $proposal->scheme->code }}</span></td>
                        <td>
                            <span class="badge-{{ $proposal->status->color() }}">{{ $proposal->status->label() }}</span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $totalReviewers > 0 ? ($submittedReviewers / $totalReviewers) * 100 : 0 }}%"></div>
                                </div>
                                <span class="text-xs font-semibold text-gray-700">{{ $submittedReviewers }}/{{ $totalReviewers }}</span>
                            </div>
                        </td>
                        <td class="text-right whitespace-nowrap">
                            @if($proposal->status === \App\Enums\ProposalStatus::Revision)
                                <a href="#" class="btn-secondary btn px-3 py-1.5 text-sm">Lihat Catatan (Revisi)</a>
                            @else
                                <a href="{{ route('operator.recap.show', $proposal) }}" class="btn-primary btn px-3 py-1.5 text-sm">Buka Rekap</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">Tidak ada proposal yang membutuhkan rekapitulasi saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
