<x-app-layout title="Penugasan Review">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900 leading-tight">Daftar Penugasan Review</h2>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Siklus</th>
                        <th>Judul Proposal</th>
                        <th>Skema</th>
                        <th>Tahap Review</th>
                        <th>Status Tugas</th>
                        <th>Batas Waktu</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($assignments as $assignment)
                    <tr class="hover:bg-gray-50 transition">
                        <td>{{ $assignment->proposal->cycle->name }}</td>
                        <td class="max-w-md">
                            <p class="font-medium text-gray-900 line-clamp-2" title="{{ $assignment->proposal->title }}">{{ $assignment->proposal->title }}</p>
                        </td>
                        <td><span class="badge-indigo">{{ $assignment->proposal->scheme->code }}</span></td>
                        <td>
                            <span class="badge-gray">{{ $assignment->stage->label() }}</span>
                        </td>
                        <td>
                            @if($assignment->status === 'submitted')
                                <span class="badge-emerald">Selesai</span>
                            @elseif($assignment->status === 'in_progress')
                                <span class="badge-blue">Sedang Diproses</span>
                            @else
                                <span class="badge-gray">Belum Dikerjakan</span>
                            @endif
                        </td>
                        <td>
                            @if($assignment->due_at)
                                <span class="{{ $assignment->due_at->isPast() && $assignment->status !== 'submitted' ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                    {{ $assignment->due_at->format('d M Y, H:i') }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right whitespace-nowrap">
                            @if($assignment->stage === \App\Enums\ReviewStage::Admin)
                                <a href="{{ route('reviewer.assignments.admin', $assignment) }}" class="{{ $assignment->status === 'submitted' ? 'btn-secondary' : 'btn-primary' }} btn px-3 py-1.5 text-sm">
                                    {{ $assignment->status === 'submitted' ? 'Lihat Hasil' : 'Lakukan Review' }}
                                </a>
                            @elseif($assignment->stage === \App\Enums\ReviewStage::Substantive)
                                <a href="{{ route('reviewer.assignments.substantive', $assignment) }}" class="{{ $assignment->status === 'submitted' ? 'btn-secondary' : 'btn-primary' }} btn px-3 py-1.5 text-sm">
                                    {{ $assignment->status === 'submitted' ? 'Lihat Hasil' : 'Lakukan Review' }}
                                </a>
                            @elseif($assignment->stage === \App\Enums\ReviewStage::Final)
                                <a href="{{ route('reviewer.assignments.final', $assignment) }}" class="{{ $assignment->status === 'submitted' ? 'btn-secondary' : 'btn-primary' }} btn px-3 py-1.5 text-sm">
                                    {{ $assignment->status === 'submitted' ? 'Lihat Hasil' : 'Lakukan Review Final' }}
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500 italic">Belum ada penugasan review untuk Anda.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
