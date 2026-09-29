<x-app-layout title="Penugasan Reviewer Final">
    <x-slot name="breadcrumb">
        <a href="{{ route('operator.final-assignments.index') }}" class="hover:text-gray-600">Penugasan Reviewer Final</a>
        <span>/</span>
        <span>Tugaskan</span>
    </x-slot>

    <div class="max-w-4xl mx-auto">
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
                    <dt class="text-sm font-medium text-gray-500">Dosen Pembimbing</dt>
                    <dd class="mt-1 text-sm text-red-600 font-medium flex items-center gap-1">
                        <x-icon name="exclamation-circle" class="w-4 h-4"/>
                        {{ $proposal->supervisor->name }} (Tidak bisa ditugaskan)
                    </dd>
                </div>
            </dl>
        </div>

        <form method="POST" action="{{ route('operator.final-assignments.store', $proposal) }}" class="card p-6 space-y-8"
              x-data="{ rev1: '{{ old('final_reviewer_id_1') }}', rev2: '{{ old('final_reviewer_id_2') }}' }">
            @csrf

            <div class="border-b border-gray-100 pb-4">
                <h3 class="text-lg font-bold text-gray-900">Reviewer Final (2 Orang)</h3>
                <p class="text-sm text-gray-500 mt-1">Reviewer harus orang yang berbeda, tidak boleh dosen pembimbing, dan tidak boleh reviewer yang pernah mereview proposal ini di tahap sebelumnya.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="final_reviewer_id_1" class="form-label">Reviewer Final 1 <span class="text-red-500">*</span></label>
                    <select name="final_reviewer_id_1" id="final_reviewer_id_1" class="form-input" x-model="rev1" required>
                        <option value="">-- Pilih Reviewer 1 --</option>
                        @foreach($reviewers as $reviewer)
                            <option value="{{ $reviewer->id }}" x-bind:disabled="rev2 == '{{ $reviewer->id }}'">
                                {{ $reviewer->name }} ({{ $reviewer->nidn ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('final_reviewer_id_1') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="final_reviewer_id_2" class="form-label">Reviewer Final 2 <span class="text-red-500">*</span></label>
                    <select name="final_reviewer_id_2" id="final_reviewer_id_2" class="form-input" x-model="rev2" required>
                        <option value="">-- Pilih Reviewer 2 --</option>
                        @foreach($reviewers as $reviewer)
                            <option value="{{ $reviewer->id }}" x-bind:disabled="rev1 == '{{ $reviewer->id }}'">
                                {{ $reviewer->name }} ({{ $reviewer->nidn ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('final_reviewer_id_2') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="border-t border-gray-100 pt-6">
                <div class="max-w-md">
                    <label for="due_at" class="form-label">Batas Waktu Penilaian (Due Date) <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="due_at" id="due_at" class="form-input" 
                           value="{{ old('due_at', now()->addDays(5)->format('Y-m-d\TH:i')) }}" required>
                    @error('due_at') <p class="form-error">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-500 mt-1">Estimasi waktu yang diberikan kepada Reviewer Final untuk menyelesaikan penilaian.</p>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('operator.final-assignments.index') }}" class="btn-secondary btn px-5">Batal</a>
                <button type="submit" class="btn-primary btn px-8">Simpan & Tugaskan</button>
            </div>
        </form>
    </div>
</x-app-layout>
