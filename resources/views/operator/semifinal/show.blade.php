<x-app-layout title="Keputusan Semifinal">
    <x-slot name="breadcrumb">
        <a href="{{ route('operator.semifinal.index') }}" class="hover:text-gray-600">Keputusan Semifinal</a>
        <span>/</span>
        <span>Evaluasi</span>
    </x-slot>

    <div class="flex flex-col lg:flex-row items-start gap-8">
        
        {{-- Kolom Kiri: Hasil Review Final --}}
        <div class="flex-1 w-full space-y-6">
            <div class="card p-6 border-b border-gray-100 bg-white shadow-sm ring-1 ring-gray-900/5">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Proposal</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Judul</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $proposal->title }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Ketua Tim</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $proposal->leader->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Skema</dt>
                        <dd class="mt-1"><span class="badge-indigo">{{ $proposal->scheme->code }}</span></dd>
                    </div>
                </dl>
            </div>

            <div class="card">
                <div class="card-header border-b border-gray-100 flex justify-between items-center">
                    <h3 class="card-title">Hasil Review Final</h3>
                    <div class="text-sm">
                        <span class="text-gray-500">Nilai Rata-rata: </span>
                        <span class="font-black text-emerald-600 text-lg">{{ number_format($averageScore, 2) }}</span>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    @forelse($finalAssignments as $index => $assignment)
                        <div class="border border-gray-100 rounded-lg p-4 bg-gray-50/50">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-bold text-gray-900 text-sm">Reviewer {{ $index + 1 }}</h4>
                                @if($assignment->isSubmitted())
                                    <span class="badge-emerald"><x-icon name="check-circle" class="w-3 h-3 inline"/> Selesai</span>
                                @else
                                    <span class="badge-amber"><x-icon name="clock" class="w-3 h-3 inline"/> Belum Selesai</span>
                                @endif
                            </div>

                            @if($assignment->isSubmitted())
                                @php
                                    $totalReviewer = 0;
                                    foreach ($assignment->scores as $scoreResult) {
                                        $totalReviewer += $scoreResult->score * $scoreResult->criterion->weight;
                                    }
                                @endphp
                                <p class="text-sm text-gray-700 mb-2 font-semibold">Total Nilai: <span class="text-emerald-600">{{ $totalReviewer }}</span></p>
                                
                                <div class="space-y-3 mt-4">
                                    <h5 class="text-xs font-bold text-gray-500 uppercase">Catatan Kriteria:</h5>
                                    @foreach($assignment->scores as $score)
                                        @if($score->comment)
                                            <div class="text-sm bg-white p-3 rounded border border-gray-100">
                                                <p class="font-medium text-gray-700 text-xs mb-1">{{ $score->criterion->label }} (Skor: {{ $score->score }})</p>
                                                <p class="text-gray-600 italic">"{{ $score->comment }}"</p>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 italic">Reviewer ini belum menyelesaikan penilaian.</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center italic">Belum ada penugasan final.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Form Keputusan --}}
        <div class="w-full lg:w-96 flex-shrink-0">
            @if($proposal->status === \App\Enums\ProposalStatus::SemifinalDecision)
            <div class="card bg-white shadow-xl shadow-blue-900/5 ring-1 ring-blue-900/10 sticky top-6">
                <div class="p-6 border-b border-gray-100 bg-blue-50/50 rounded-t-xl">
                    <h3 class="text-lg font-bold text-gray-900">Ambil Keputusan</h3>
                    <p class="text-sm text-gray-500 mt-1">Tentukan apakah proposal ini lolos ke tahap selanjutnya.</p>
                </div>
                <form method="POST" action="{{ route('operator.semifinal.store', $proposal) }}" class="p-6 space-y-5" x-data="{ passed: '1' }">
                    @csrf
                    
                    <input type="hidden" name="total_score" value="{{ $averageScore }}">

                    <div class="space-y-3">
                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none" 
                               :class="passed === '1' ? 'border-emerald-500 ring-1 ring-emerald-500' : 'border-gray-300'">
                            <input type="radio" name="passed" value="1" class="sr-only" x-model="passed">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900">Lolos</span>
                                    <span class="mt-1 flex items-center text-sm text-gray-500">Masuk ke University Assignment.</span>
                                </span>
                            </span>
                            <x-icon name="check-circle" class="h-5 w-5 text-emerald-500" x-show="passed === '1'"/>
                        </label>

                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none"
                               :class="passed === '0' ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300'">
                            <input type="radio" name="passed" value="0" class="sr-only" x-model="passed">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900">Tidak Lolos</span>
                                    <span class="mt-1 flex items-center text-sm text-gray-500">Proposal dinyatakan gugur.</span>
                                </span>
                            </span>
                            <x-icon name="check-circle" class="h-5 w-5 text-red-500" x-show="passed === '0'" style="display: none;"/>
                        </label>
                    </div>

                    <div>
                        <label for="note" class="form-label">Catatan Tambahan (Opsional)</label>
                        <textarea id="note" name="note" rows="3" class="form-input" placeholder="Berikan catatan jika diperlukan..."></textarea>
                        @error('note') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="btn-primary btn w-full justify-center">
                        Simpan Keputusan
                    </button>
                </form>
            </div>
            @else
            <div class="card p-6 bg-gray-50 border border-gray-100 sticky top-6">
                <h3 class="text-gray-900 font-bold mb-4">Keputusan Semifinal</h3>
                @if($proposal->semifinalResult)
                    @if($proposal->semifinalResult->passed)
                        <div class="flex items-center gap-2 text-emerald-700 font-bold mb-2">
                            <x-icon name="check-circle" class="w-6 h-6"/>
                            LOLOS
                        </div>
                    @else
                        <div class="flex items-center gap-2 text-red-700 font-bold mb-2">
                            <x-icon name="x-circle" class="w-6 h-6"/>
                            TIDAK LOLOS
                        </div>
                    @endif
                    <div class="text-sm text-gray-600 bg-white p-3 border border-gray-100 rounded">
                        <p class="font-semibold text-xs text-gray-500 mb-1">Catatan:</p>
                        {{ $proposal->semifinalResult->note ?: '-' }}
                    </div>
                @else
                    <p class="text-sm text-gray-500 italic">Belum ada keputusan.</p>
                @endif
            </div>
            @endif
        </div>

    </div>
</x-app-layout>
