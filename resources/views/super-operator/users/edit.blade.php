<x-app-layout title="Edit Akun: {{ $user->name }}">
    <x-slot name="breadcrumb">
        <a href="{{ route('super-operator.users.index') }}" class="hover:text-gray-600">Kelola Akun</a>
        <span>/</span>
        <span>Edit</span>
    </x-slot>

    <div class="max-w-2xl">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Edit Akun: {{ $user->name }}</h2>
                @if(!$user->isClaimed())
                    <span class="badge-amber">Belum Klaim</span>
                @else
                    <span class="badge-green">Sudah Klaim</span>
                @endif
            </div>
            <form method="POST" action="{{ route('super-operator.users.update', $user) }}" class="card-body space-y-5">
                @csrf @method('PATCH')

                <div>
                    <label for="name" class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                           class="form-input @error('name') form-input-error @enderror" required>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="form-label">Email Student <span class="text-red-500">*</span></label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                           class="form-input @error('email') form-input-error @enderror" required>
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="nim" class="form-label">NIM</label>
                        <input id="nim" name="nim" type="text" value="{{ old('nim', $user->nim) }}" class="form-input">
                    </div>
                    <div>
                        <label for="nidn" class="form-label">NIDN</label>
                        <input id="nidn" name="nidn" type="text" value="{{ old('nidn', $user->nidn) }}" class="form-input">
                    </div>
                    <div>
                        <label for="nuptk" class="form-label">NUPTK</label>
                        <input id="nuptk" name="nuptk" type="text" value="{{ old('nuptk', $user->nuptk) }}" class="form-input">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="study_program" class="form-label">Program Studi</label>
                        <input id="study_program" name="study_program" type="text"
                               value="{{ old('study_program', $user->study_program) }}" class="form-input">
                    </div>
                    <div>
                        <label for="cohort_year" class="form-label">Tahun Angkatan</label>
                        <input id="cohort_year" name="cohort_year" type="number"
                               value="{{ old('cohort_year', $user->cohort_year) }}" class="form-input"
                               min="2000" max="2100">
                    </div>
                </div>

                {{-- Peran --}}
                <div>
                    <label class="form-label">Peran <span class="text-red-500">*</span></label>
                    @php $currentRoles = $user->roles(); @endphp
                    <div class="space-y-2">
                        @foreach($roles as $role)
                        @php $isChecked = collect($currentRoles)->contains(fn($r) => $r->value === $role->value); @endphp
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition">
                            <input type="checkbox" name="roles[]" value="{{ $role->value }}"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                   {{ (in_array($role->value, old('roles', [])) || (!old('roles') && $isChecked)) ? 'checked' : '' }}>
                            <div>
                                <div class="font-medium text-sm text-gray-800">{{ $role->label() }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('roles') <p class="form-error mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- Status Aktif --}}
                <div>
                    <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" name="is_active" value="1"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                               {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                        <div>
                            <div class="font-medium text-sm text-gray-800">Akun Aktif</div>
                            <div class="text-xs text-gray-400">Nonaktifkan untuk memblokir login tanpa menghapus data.</div>
                        </div>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                    <a href="{{ route('super-operator.users.index') }}" class="btn-secondary btn">Batal</a>
                    <button type="submit" class="btn-primary btn">
                        <x-icon name="check" class="w-4 h-4"/>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
