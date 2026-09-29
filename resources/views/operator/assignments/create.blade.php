<x-app-layout title="Form Penugasan Reviewer">
    <x-slot name="breadcrumb">
        <a href="{{ route('operator.assignments.index') }}" class="hover:text-gray-600">Penugasan Reviewer</a>
        <span>/</span>
        <span>Tugaskan</span>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        
        <div class="card p-6 border-l-4 border-indigo-500">
            <h2 class="text-xl font-bold text-gray-900 mb-2">Informasi Proposal</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Judul</dt>
                    <dd class="text-sm text-gray-900 font-medium">{{ $proposal->title }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Ketua Tim</dt>
                    <dd class="text-sm text-gray-900">{{ $proposal->leader->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Skema</dt>
                    <dd class="text-sm text-gray-900">{{ $proposal->scheme->code }} - {{ $proposal->scheme->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Dosen Pembimbing</dt>
                    <dd class="text-sm text-gray-900">{{ $proposal->supervisor?->name ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        <form action="{{ route('operator.assignments.store', $proposal) }}" method="POST" class="card p-6 space-y-6" x-data="{
            admin: '{{ old('admin_reviewer_id') }}',
            sub1: '{{ old('substantive_reviewer_id_1') }}',
            sub2: '{{ old('substantive_reviewer_id_2') }}',
            isConflict() {
                const arr = [this.admin, this.sub1, this.sub2].filter(Boolean);
                return new Set(arr).size !== arr.length;
            }
        }">
            @csrf

            <div class="alert-info" role="alert">
                <x-icon name="information-circle" class="w-5 h-5 flex-shrink-0"/>
                <span>Anda harus memilih 3 reviewer yang <strong>berbeda</strong> untuk proposal ini. Reviewer juga tidak boleh merupakan Dosen Pembimbing dari proposal bersangkutan.</span>
            </div>

            <div x-show="isConflict()" class="alert-error" role="alert" style="display: none;" x-cloak>
                <x-icon name="x-circle" class="w-5 h-5 flex-shrink-0"/>
                <span><strong>Konflik:</strong> Anda telah memilih reviewer yang sama untuk lebih dari satu posisi. Silakan pilih orang yang berbeda.</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="admin_reviewer_id" class="form-label">Reviewer Administratif <span class="text-red-500">*</span></label>
                    <select id="admin_reviewer_id" name="admin_reviewer_id" class="form-input" required x-model="admin">
                        <option value="">-- Pilih Reviewer Administratif --</option>
                        @foreach($reviewers as $rev)
                            <option value="{{ $rev->id }}" {{ $rev->id === $proposal->supervisor_id ? 'disabled' : '' }}>
                                {{ $rev->name }} {{ $rev->id === $proposal->supervisor_id ? '(Pembimbing - Konflik)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('admin_reviewer_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="substantive_reviewer_id_1" class="form-label">Reviewer Substantif 1 <span class="text-red-500">*</span></label>
                    <select id="substantive_reviewer_id_1" name="substantive_reviewer_id_1" class="form-input" required x-model="sub1">
                        <option value="">-- Pilih Reviewer Substantif 1 --</option>
                        @foreach($reviewers as $rev)
                            <option value="{{ $rev->id }}" {{ $rev->id === $proposal->supervisor_id ? 'disabled' : '' }}>
                                {{ $rev->name }} {{ $rev->id === $proposal->supervisor_id ? '(Pembimbing)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('substantive_reviewer_id_1') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="substantive_reviewer_id_2" class="form-label">Reviewer Substantif 2 <span class="text-red-500">*</span></label>
                    <select id="substantive_reviewer_id_2" name="substantive_reviewer_id_2" class="form-input" required x-model="sub2">
                        <option value="">-- Pilih Reviewer Substantif 2 --</option>
                        @foreach($reviewers as $rev)
                            <option value="{{ $rev->id }}" {{ $rev->id === $proposal->supervisor_id ? 'disabled' : '' }}>
                                {{ $rev->name }} {{ $rev->id === $proposal->supervisor_id ? '(Pembimbing)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('substantive_reviewer_id_2') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2 border-t border-gray-100 pt-4 mt-2">
                    <label for="due_at" class="form-label">Batas Waktu Review <span class="text-red-500">*</span></label>
                    <input type="datetime-local" id="due_at" name="due_at" value="{{ old('due_at', now()->addDays(7)->format('Y-m-d\TH:i')) }}" class="form-input md:w-1/3" required>
                    <p class="text-xs text-gray-500 mt-1">Default: 7 hari dari sekarang.</p>
                    @error('due_at') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="btn-primary btn px-8" :disabled="isConflict()">
                    Simpan Penugasan
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
