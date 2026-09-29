<x-app-layout title="Penugasan Reviewer">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900 leading-tight">Penugasan Reviewer</h2>
        <p class="text-sm text-gray-500">Pilih proposal untuk ditugaskan kepada tim reviewer.</p>
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
                        </td>
                        <td><span class="badge-indigo">{{ $proposal->scheme->code }}</span></td>
                        <td>
                            <span class="badge-{{ $proposal->status->color() }}">{{ $proposal->status->label() }}</span>
                            @if($proposal->status === \App\Enums\ProposalStatus::AdministrativeReview || $proposal->status === \App\Enums\ProposalStatus::SubstantiveReview)
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $proposal->assignments->count() }} Reviewer
                                </div>
                            @endif
                        </td>
                        <td class="text-right whitespace-nowrap">
                            @if($proposal->status === \App\Enums\ProposalStatus::AdminAssignment)
                                <a href="{{ route('operator.assignments.create', $proposal) }}" class="btn-primary btn px-3 py-1.5 text-sm">Tugaskan Reviewer</a>
                            @else
                                <button class="btn-secondary btn px-3 py-1.5 text-sm cursor-not-allowed opacity-50" disabled>Sudah Ditugaskan</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">Tidak ada proposal yang membutuhkan penugasan reviewer saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
