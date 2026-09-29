<x-app-layout title="Penugasan Dosen Universitas">
    <x-slot name="breadcrumb">
        <a href="{{ route('operator.university-assignments.index') }}" class="hover:text-gray-600">Penugasan Dosen Universitas</a>
        <span>/</span>
        <span>Tugaskan</span>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="card p-6 border-b border-gray-100 bg-white mb-6 shadow-sm ring-1 ring-gray-900/5">
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
                <div>
                    <dt class="text-sm font-medium text-gray-500">Dosen Pembimbing Sebelumnya</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-medium">{{ $proposal->supervisor->name }}</dd>
                </div>
            </dl>
        </div>

        <form method="POST" action="{{ route('operator.university-assignments.store', $proposal) }}" class="card p-6 space-y-6">
            @csrf

            <div class="border-b border-gray-100 pb-4">
                <h3 class="text-lg font-bold text-gray-900">Pilih Dosen Universitas</h3>
                <p class="text-sm text-gray-500 mt-1">Dosen universitas akan melakukan validasi akhir sebelum proposal diikutsertakan dalam penilaian batch final/Simbelmawa.</p>
            </div>

            <div>
                <label for="university_lecturer_id" class="form-label">Dosen Universitas <span class="text-red-500">*</span></label>
                <select name="university_lecturer_id" id="university_lecturer_id" class="form-input" required>
                    <option value="">-- Pilih Dosen Universitas --</option>
                    @foreach($lecturers as $lecturer)
                        <option value="{{ $lecturer->id }}" {{ old('university_lecturer_id') == $lecturer->id ? 'selected' : '' }}>
                            {{ $lecturer->name }} ({{ $lecturer->nidn ?? '-' }})
                        </option>
                    @endforeach
                </select>
                @error('university_lecturer_id') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded p-4 flex gap-3 text-blue-800">
                <x-icon name="information-circle" class="w-5 h-5 flex-shrink-0 mt-0.5"/>
                <div class="text-sm">
                    <strong>Catatan:</strong> Setelah disimpan, status proposal akan berubah menjadi <span class="font-bold">Final Revision</span>. Mahasiswa diharuskan untuk mengunggah revisi akhir mereka berdasarkan catatan dari tahap semifinal sebelum divalidasi oleh dosen universitas yang dipilih.
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('operator.university-assignments.index') }}" class="btn-secondary btn px-5">Batal</a>
                <button type="submit" class="btn-primary btn px-8">Simpan Penugasan</button>
            </div>
        </form>
    </div>
</x-app-layout>
