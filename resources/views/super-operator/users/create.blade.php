<x-app-layout title="Tambah Akun Pengguna">
    <x-slot name="breadcrumb">
        <a href="{{ route('super-operator.users.index') }}" class="hover:text-gray-600">Kelola Akun</a>
        <span>/</span>
        <span>Tambah Baru</span>
    </x-slot>

    <div class="max-w-2xl">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Data Pengguna Baru</h2>
            </div>
            <form method="POST" action="{{ route('super-operator.users.store') }}" class="card-body space-y-5">
                @csrf

                {{-- Nama --}}
                <div>
                    <label for="name" class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}"
                           class="form-input @error('name') form-input-error @enderror"
                           placeholder="Nama sesuai identitas resmi" required>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Email Student --}}
                <div>
                    <label for="email" class="form-label">Email Student <span class="text-red-500">*</span></label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}"
                           class="form-input @error('email') form-input-error @enderror"
                           placeholder="nim@student.unud.ac.id" required>
                    <p class="form-hint">Token klaim akan dikirim ke email ini. Harus bisa diakses oleh pengguna.</p>
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- NIM / NIDN / NUPTK --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="nim" class="form-label">NIM</label>
                        <input id="nim" name="nim" type="text" value="{{ old('nim') }}"
                               class="form-input @error('nim') form-input-error @enderror"
                               placeholder="Nomor Induk Mahasiswa">
                        @error('nim') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="nidn" class="form-label">NIDN</label>
                        <input id="nidn" name="nidn" type="text" value="{{ old('nidn') }}"
                               class="form-input @error('nidn') form-input-error @enderror"
                               placeholder="No. Induk Dosen">
                        @error('nidn') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="nuptk" class="form-label">NUPTK</label>
                        <input id="nuptk" name="nuptk" type="text" value="{{ old('nuptk') }}"
                               class="form-input @error('nuptk') form-input-error @enderror"
                               placeholder="No. Unik PTK">
                        @error('nuptk') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Program Studi & Angkatan --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="study_program" class="form-label">Program Studi</label>
                        <input id="study_program" name="study_program" type="text" value="{{ old('study_program') }}"
                               class="form-input"
                               placeholder="Misal: Teknik Informatika">
                    </div>
                    <div>
                        <label for="cohort_year" class="form-label">Tahun Angkatan</label>
                        <input id="cohort_year" name="cohort_year" type="number" value="{{ old('cohort_year') }}"
                               class="form-input"
                               placeholder="{{ date('Y') - 2 }}" min="2000" max="2100">
                    </div>
                </div>

                {{-- Peran --}}
                <div>
                    <label class="form-label">Peran <span class="text-red-500">*</span></label>
                    <p class="form-hint mb-2">Pilih minimal satu peran.</p>
                    <div class="space-y-2">
                        @foreach($roles as $role)
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition">
                            <input type="checkbox" name="roles[]" value="{{ $role->value }}"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                   {{ in_array($role->value, old('roles', [])) ? 'checked' : '' }}>
                            <div>
                                <div class="font-medium text-sm text-gray-800">{{ $role->label() }}</div>
                                <div class="text-xs text-gray-400">{{ $role->value }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('roles') <p class="form-error mt-2">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                    <a href="{{ route('super-operator.users.index') }}" class="btn-secondary btn">Batal</a>
                    <button type="submit" class="btn-primary btn">
                        <x-icon name="check" class="w-4 h-4"/>
                        Simpan & Kirim Email Klaim
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
