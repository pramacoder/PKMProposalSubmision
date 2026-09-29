<x-app-layout title="Batch Keputusan Akhir">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 leading-tight">Batch Keputusan Akhir</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola penilaian dan keputusan akhir untuk pendanaan proposal per batch.</p>
        </div>
        <a href="{{ route('operator.decision-batches.create') }}" class="btn btn-primary">
            <x-icon name="plus" class="w-4 h-4"/> Buat Batch
        </a>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Siklus</th>
                        <th>Skema</th>
                        <th>Nama Batch</th>
                        <th>Proposal</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($batches as $batch)
                    <tr class="hover:bg-gray-50 transition">
                        <td>{{ $batch->cycle->name }}</td>
                        <td><span class="badge-indigo">{{ $batch->scheme->code }}</span></td>
                        <td class="font-medium text-gray-900">{{ $batch->name }}</td>
                        <td>{{ $batch->decisions_count }} proposal</td>
                        <td>
                            @if($batch->isDecided())
                                <span class="badge-emerald"><x-icon name="check" class="w-3 h-3 inline"/> Selesai ({{ $batch->decided_at->format('d/m/Y') }})</span>
                            @else
                                <span class="badge-amber"><x-icon name="clock" class="w-3 h-3 inline"/> Menunggu Keputusan</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ route('operator.decision-batches.show', $batch) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                Kelola Batch
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">Belum ada batch keputusan yang dibuat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
