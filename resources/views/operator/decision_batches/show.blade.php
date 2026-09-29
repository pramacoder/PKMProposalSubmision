<x-app-layout title="Kelola Batch Keputusan">
    <x-slot name="breadcrumb">
        <a href="{{ route('operator.decision-batches.index') }}" class="hover:text-gray-600">Batch Keputusan</a>
        <span>/</span>
        <span>Kelola Batch</span>
    </x-slot>

    <div class="flex flex-col lg:flex-row items-start gap-6 mb-6">
        <div class="card p-6 flex-1 w-full bg-white border border-gray-100">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 leading-tight">{{ $batch->name }}</h2>
                    <div class="flex items-center gap-3 mt-2 text-sm">
                        <span class="badge-indigo">{{ $batch->scheme->code }}</span>
                        <span class="text-gray-500">Siklus: {{ $batch->cycle->name }}</span>
                    </div>
                </div>
                <div>
                    @if($batch->isDecided())
                        <span class="badge-emerald py-1.5 px-3 text-sm"><x-icon name="check-circle" class="w-4 h-4 inline"/> Difinalisasi pada {{ $batch->decided_at->format('d/m/Y H:i') }}</span>
                    @else
                        <span class="badge-amber py-1.5 px-3 text-sm"><x-icon name="clock" class="w-4 h-4 inline"/> Menunggu Finalisasi</span>
                    @endif
                </div>
            </div>
            
            <div class="border-t border-gray-100 pt-4 mt-4">
                <h3 class="text-sm font-bold text-gray-900 mb-2">Tim Penilai (Partisipan):</h3>
                @if($batch->participants->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($batch->participants as $participant)
                            <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-800 text-xs px-2.5 py-1 rounded border border-gray-200">
                                <x-icon name="user" class="w-3 h-3 text-gray-500"/>
                                {{ $participant->user->name }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 italic">Tidak ada tim penilai khusus yang ditambahkan.</p>
                @endif
            </div>
        </div>

        @if(!$batch->isDecided())
        <div class="card p-6 w-full lg:w-80 flex-shrink-0 bg-emerald-50 border-emerald-100">
            <h3 class="font-bold text-emerald-900 mb-2">Finalisasi Batch</h3>
            <p class="text-xs text-emerald-800 mb-4">Finalisasi akan menetapkan status semua proposal dalam batch ini menjadi "Lolos" atau "Tidak Lolos" tahap internal (pendanaan).</p>
            
            <form method="POST" action="{{ route('operator.decision-batches.finalize', $batch) }}" onsubmit="return confirm('Apakah Anda yakin ingin memfinalisasi batch ini? Keputusan yang telah difinalisasi tidak dapat diubah lagi.')">
                @csrf
                <button type="submit" class="btn btn-success w-full justify-center" @if($batch->decisions->isEmpty()) disabled @endif>
                    <x-icon name="check-circle" class="w-4 h-4"/> Finalisasi Sekarang
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- Daftar Proposal di dalam Batch --}}
    <div class="card mb-6">
        <div class="card-header border-b border-gray-100 flex items-center justify-between">
            <h3 class="card-title">Proposal dalam Batch ({{ $batch->decisions->count() }})</h3>
        </div>
        <div class="p-0">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>Judul Proposal</th>
                        <th>Ketua Tim</th>
                        <th>Keputusan</th>
                        <th>Catatan</th>
                        @if(!$batch->isDecided())
                            <th class="text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($batch->decisions as $decision)
                        <tr class="{{ $decision->decision === 'passed' ? 'bg-emerald-50/30' : 'bg-red-50/30' }}">
                            <td class="max-w-xs">
                                <p class="font-medium text-gray-900 line-clamp-2" title="{{ $decision->proposal->title }}">{{ $decision->proposal->title }}</p>
                            </td>
                            <td class="whitespace-nowrap">{{ $decision->proposal->leader->name }}</td>
                            
                            @if(!$batch->isDecided())
                                {{-- Inline Edit Keputusan --}}
                                <td colspan="2">
                                    <form method="POST" action="{{ route('operator.decision-batches.update-decision', [$batch, $decision]) }}" class="flex gap-2 w-full max-w-lg">
                                        @csrf
                                        @method('PATCH')
                                        <select name="decision" class="form-input text-sm py-1.5 h-auto">
                                            <option value="passed" {{ $decision->decision === 'passed' ? 'selected' : '' }}>Passed (Didanai)</option>
                                            <option value="not_passed" {{ $decision->decision === 'not_passed' ? 'selected' : '' }}>Not Passed</option>
                                        </select>
                                        <input type="text" name="note" class="form-input text-sm py-1.5 h-auto flex-1" value="{{ $decision->note }}" placeholder="Catatan...">
                                        <button type="submit" class="btn-primary btn px-3 py-1.5 text-xs whitespace-nowrap">Simpan</button>
                                    </form>
                                </td>
                                <td class="text-right">
                                    <form method="POST" action="{{ route('operator.decision-batches.remove', [$batch, $decision]) }}" onsubmit="return confirm('Keluarkan proposal ini dari batch?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-sm">Hapus</button>
                                    </form>
                                </td>
                            @else
                                {{-- Read Only Mode --}}
                                <td>
                                    @if($decision->decision === 'passed')
                                        <span class="badge-emerald"><x-icon name="check" class="w-3 h-3 inline"/> Lolos</span>
                                    @else
                                        <span class="badge-red"><x-icon name="x-mark" class="w-3 h-3 inline"/> Tidak Lolos</span>
                                    @endif
                                </td>
                                <td><span class="text-sm text-gray-700">{{ $decision->note ?: '-' }}</span></td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $batch->isDecided() ? '4' : '5' }}" class="px-6 py-8 text-center text-gray-500 italic">Belum ada proposal yang ditambahkan ke batch ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Daftar Proposal yang bisa ditambahkan --}}
    @if(!$batch->isDecided())
    <div class="card">
        <div class="card-header border-b border-gray-100 flex items-center justify-between">
            <h3 class="card-title">Tersedia untuk Ditambahkan</h3>
        </div>
        <div class="p-0">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>Judul Proposal</th>
                        <th>Ketua Tim</th>
                        <th>Validasi Universitas</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($availableProposals as $availProp)
                        <tr>
                            <td class="max-w-md">
                                <p class="font-medium text-gray-900 line-clamp-2" title="{{ $availProp->title }}">{{ $availProp->title }}</p>
                            </td>
                            <td>{{ $availProp->leader->name }}</td>
                            <td>
                                @if($availProp->universityLecturer)
                                    <span class="text-sm text-gray-600">Oleh: {{ $availProp->universityLecturer->name }}</span>
                                @else
                                    <span class="text-sm text-gray-500 italic">Tidak ada</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <form method="POST" action="{{ route('operator.decision-batches.add', $batch) }}">
                                    @csrf
                                    <input type="hidden" name="proposal_id" value="{{ $availProp->id }}">
                                    <button type="submit" class="btn-secondary btn px-3 py-1.5 text-sm">
                                        <x-icon name="plus" class="w-4 h-4"/> Tambahkan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">Tidak ada proposal dengan status Keputusan Akhir (FinalDecision) untuk skema dan siklus ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</x-app-layout>
