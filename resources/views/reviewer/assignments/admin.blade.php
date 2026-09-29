<x-app-layout title="Review Administratif">
    <x-slot name="breadcrumb">
        <a href="{{ route('reviewer.assignments.index') }}" class="hover:text-gray-600">Daftar Penugasan</a>
        <span>/</span>
        <span>Review Administratif</span>
    </x-slot>

    <div class="flex flex-col xl:flex-row items-start gap-8">
        
        {{-- Kolom Kiri: Detail Proposal & Berkas --}}
        <div class="w-full xl:w-1/3 flex-shrink-0 space-y-6">
            <div class="card p-6 border-t-4 border-indigo-500">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Informasi Proposal</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Judul</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $assignment->proposal->title }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Skema</dt>
                        <dd class="mt-1"><span class="badge-indigo">{{ $assignment->proposal->scheme->code }}</span></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ketua Tim</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assignment->proposal->leader->name }} ({{ $assignment->proposal->leader->nim ?? '-' }})</dd>
                    </div>
                </dl>
            </div>

            <div class="card">
                <div class="card-header border-b border-gray-100">
                    <h3 class="card-title">Berkas Terunggah</h3>
                </div>
                <div class="p-0">
                    <table class="table w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            @forelse($assignment->proposal->files as $file)
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900">{{ $file->requirement->label }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $file->original_name }}</p>
                                </td>
                                <td class="px-6 py-4 text-right align-middle">
                                    <a href="{{ route('student.proposals.files.download', [$assignment->proposal_id, $file]) }}" class="btn-secondary btn px-3 py-1.5 text-xs" target="_blank">Unduh</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-6 py-6 text-center text-gray-500 italic">Belum ada berkas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Form Penilaian --}}
        <div class="flex-1 w-full">
            <div class="card bg-white shadow-xl shadow-blue-900/5 ring-1 ring-blue-900/10">
                <div class="p-6 border-b border-gray-100 bg-blue-50/50 rounded-t-xl">
                    <h2 class="text-xl font-bold text-gray-900">Form Checklist Administratif</h2>
                    <p class="text-sm text-gray-500 mt-1">Sesuai DEC-12, review administratif tidak dinilai dan tidak menggugurkan proposal. Daftar kekurangan akan diteruskan ke mahasiswa.</p>
                </div>

                <form method="POST" action="{{ route('reviewer.assignments.admin.store', $assignment) }}" class="p-0">
                    @csrf
                    
                    <div class="table-wrapper rounded-none border-0">
                        <table class="table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="w-16 text-center">Kode</th>
                                    <th>Kriteria Penilaian</th>
                                    <th class="w-48 text-center">Sesuai?</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($checklistForm->items as $item)
                                    @php
                                        $res = $results->get($item->id);
                                        $passedVal = old("results.{$item->id}.passed", $res ? ($res->passed ? '1' : '0') : '');
                                        $noteVal = old("results.{$item->id}.note", $res?->note ?? '');
                                    @endphp
                                    <tr x-data="{ passed: '{{ $passedVal }}' }" class="{{ $loop->even ? 'bg-gray-50/50' : 'bg-white' }}">
                                        <td class="text-center font-mono text-xs text-gray-500 align-top pt-5">{{ $item->code }}</td>
                                        <td class="align-top pt-4 pb-4">
                                            <p class="text-sm text-gray-800 font-medium leading-relaxed">{{ $item->label }}</p>
                                            
                                            <div x-show="passed === '0'" style="display: none;" class="mt-3">
                                                <label class="block text-xs font-semibold text-red-600 mb-1">Catatan Kekurangan <span class="text-red-500">*</span></label>
                                                <textarea name="results[{{ $item->id }}][note]" rows="2" 
                                                          class="form-input text-sm border-red-200 focus:border-red-500 focus:ring-red-500" 
                                                          placeholder="Jelaskan apa yang kurang/tidak sesuai format...">{{ $noteVal }}</textarea>
                                                @error("results.{$item->id}.note")
                                                    <p class="form-error mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </td>
                                        <td class="align-top pt-4 text-center">
                                            <div class="inline-flex rounded-lg shadow-sm">
                                                <label class="relative flex items-center justify-center px-4 py-2 text-sm font-medium border border-gray-200 rounded-l-lg hover:bg-gray-50 cursor-pointer transition-colors"
                                                       :class="passed === '1' ? 'bg-emerald-50 border-emerald-200 text-emerald-700 z-10' : 'bg-white text-gray-700'">
                                                    <input type="radio" name="results[{{ $item->id }}][passed]" value="1" x-model="passed" class="sr-only" {{ $assignment->isSubmitted() ? 'disabled' : '' }}>
                                                    Ya
                                                </label>
                                                <label class="relative flex items-center justify-center px-4 py-2 text-sm font-medium border border-l-0 border-gray-200 rounded-r-lg hover:bg-gray-50 cursor-pointer transition-colors"
                                                       :class="passed === '0' ? 'bg-red-50 border-red-200 text-red-700 z-10' : 'bg-white text-gray-700'">
                                                    <input type="radio" name="results[{{ $item->id }}][passed]" value="0" x-model="passed" class="sr-only" {{ $assignment->isSubmitted() ? 'disabled' : '' }}>
                                                    Tidak
                                                </label>
                                            </div>
                                            @error("results.{$item->id}.passed")
                                                <p class="text-xs text-red-600 mt-1 font-medium">Wajib diisi.</p>
                                            @enderror
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(! $assignment->isSubmitted())
                    <div class="p-6 bg-gray-50 flex items-center justify-end gap-3 rounded-b-xl border-t border-gray-100">
                        <button type="submit" name="action" value="save" class="btn-secondary btn px-5">
                            Simpan Progres
                        </button>
                        <button type="submit" name="action" value="submit" class="btn-primary btn px-8"
                                onclick="return confirm('Apakah Anda yakin ingin mensubmit hasil review ini? Setelah disubmit, Anda tidak dapat mengubahnya lagi.')">
                            Submit Review
                        </button>
                    </div>
                    @else
                    <div class="p-6 bg-emerald-50 rounded-b-xl border-t border-emerald-100 flex items-center gap-3 text-emerald-800">
                        <x-icon name="check-circle" class="w-6 h-6"/>
                        <div>
                            <p class="font-bold">Review Telah Disubmit</p>
                            <p class="text-sm text-emerald-700">Anda sudah menyelesaikan review administratif untuk proposal ini.</p>
                        </div>
                    </div>
                    @endif
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
