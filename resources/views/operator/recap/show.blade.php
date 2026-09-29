<x-app-layout title="Rekapitulasi Proposal">
    <x-slot name="breadcrumb">
        <a href="{{ route('operator.recap.index') }}" class="hover:text-gray-600">Rekapitulasi Review</a>
        <span>/</span>
        <span>Rekap</span>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        
        <div class="card p-6 border-l-4 border-blue-500 flex justify-between items-start">
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Informasi Proposal</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-3">
                        <dt class="text-sm font-medium text-gray-500">Judul</dt>
                        <dd class="text-sm text-gray-900 font-medium">{{ $proposal->title }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Ketua Tim</dt>
                        <dd class="text-sm text-gray-900">{{ $proposal->leader->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Skema</dt>
                        <dd class="text-sm text-gray-900">{{ $proposal->scheme->code }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Siklus</dt>
                        <dd class="text-sm text-gray-900">{{ $proposal->cycle->name }}</dd>
                    </div>
                </dl>
            </div>
            <div class="text-center px-4">
                <span class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Rata-Rata Nilai</span>
                <span class="text-3xl font-black text-blue-600">{{ number_format($averageScore, 1) }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Review Administratif --}}
            <div class="card p-0 h-fit">
                <div class="card-header bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900">Hasil Review Administratif</h3>
                    @if($adminAssignment && $adminAssignment->isSubmitted())
                        <span class="badge-emerald">Selesai</span>
                    @else
                        <span class="badge-amber">Belum Selesai</span>
                    @endif
                </div>
                <div class="p-6">
                    @if($adminAssignment)
                        <ul class="space-y-4">
                            @php $hasIssues = false; @endphp
                            @foreach($adminAssignment->adminResults as $res)
                                @if(!$res->passed)
                                    @php $hasIssues = true; @endphp
                                    <li class="bg-red-50 p-3 rounded-lg border border-red-100">
                                        <p class="text-xs font-bold text-red-600 uppercase mb-1">{{ $res->checklistItem->label }}</p>
                                        <p class="text-sm text-red-900">{{ $res->note ?: 'Tidak ada catatan khusus.' }}</p>
                                    </li>
                                @endif
                            @endforeach
                            
                            @if(!$hasIssues)
                                <div class="text-center py-6 text-gray-500 italic">
                                    Semua persyaratan administratif terpenuhi (Tidak ada catatan perbaikan).
                                </div>
                            @endif
                        </ul>
                    @else
                        <div class="text-center py-6 text-gray-500 italic">Belum ada penugasan administratif.</div>
                    @endif
                </div>
            </div>

            {{-- Review Substantif --}}
            <div class="card p-0 h-fit">
                <div class="card-header bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900">Catatan Review Substantif</h3>
                </div>
                <div class="p-6 space-y-6">
                    @forelse($substantiveAssignments as $idx => $sub)
                        <div class="border border-gray-100 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2 border-b border-gray-100 flex justify-between items-center">
                                <h4 class="font-bold text-sm text-gray-700">Reviewer {{ $idx + 1 }}</h4>
                                @if($sub->isSubmitted())
                                    <span class="text-xs text-emerald-600 font-bold">Selesai</span>
                                @else
                                    <span class="text-xs text-amber-600 font-bold">Belum Selesai</span>
                                @endif
                            </div>
                            <div class="p-4 bg-white space-y-3">
                                @php $hasNotes = false; @endphp
                                @foreach($sub->scores as $res)
                                    @if($res->comment)
                                        @php $hasNotes = true; @endphp
                                        <div>
                                            <p class="text-xs font-bold text-gray-500 uppercase mb-0.5">{{ $res->criterion->label }}</p>
                                            <p class="text-sm text-gray-900 bg-gray-50 p-2 rounded">{{ $res->comment }}</p>
                                        </div>
                                    @endif
                                @endforeach
                                @if(!$hasNotes)
                                    <p class="text-sm text-gray-500 italic">Tidak ada catatan.</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-500 italic">Belum ada penugasan substantif.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <form action="{{ route('operator.recap.revision', $proposal) }}" method="POST" class="card p-6 bg-blue-50/30 border border-blue-100">
            @csrf
            <h3 class="text-lg font-bold text-gray-900 mb-4">Tindak Lanjut & Keputusan</h3>
            
            <div class="alert-info mb-6">
                <x-icon name="information-circle" class="w-5 h-5 flex-shrink-0"/>
                <span>Dengan menekan tombol di bawah, semua catatan administratif dan substantif di atas akan dikompilasi secara otomatis dan dikirimkan kepada mahasiswa (tanpa menyebutkan identitas reviewer). Status proposal akan berubah menjadi <strong>Revisi</strong>.</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-end">
                <div>
                    <label for="due_at" class="form-label">Batas Waktu Revisi <span class="text-red-500">*</span></label>
                    <input type="datetime-local" id="due_at" name="due_at" value="{{ old('due_at', now()->addDays(3)->format('Y-m-d\TH:i')) }}" class="form-input" required>
                    @error('due_at') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                
                {{-- Hidden input for total score calculation --}}
                <input type="hidden" name="total_score" value="{{ $averageScore }}">

                <div class="flex justify-end h-10">
                    <button type="submit" class="btn-primary btn px-8 h-full">
                        Minta Revisi ke Mahasiswa
                    </button>
                </div>
            </div>
        </form>

    </div>
</x-app-layout>
