<x-app-layout title="Edit Data Proposal">
    <x-slot name="breadcrumb">
        <a href="{{ route('student.proposals.index') }}" class="hover:text-gray-600">Proposal Saya</a>
        <span>/</span>
        <a href="{{ route('student.proposals.show', $proposal) }}" class="hover:text-gray-600">{{ $proposal->title ? Str::limit($proposal->title, 30) : 'Draf' }}</a>
        <span>/</span><span>Edit Utama</span>
    </x-slot>

    <div class="max-w-4xl grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Side Menu Placeholder --}}
        <div class="md:col-span-1 space-y-2">
            <a href="{{ route('student.proposals.edit', $proposal) }}" class="flex items-center gap-3 p-3 rounded-lg bg-blue-50 text-blue-700 font-medium">
                <x-icon name="document-text" class="w-5 h-5"/> Data Utama
            </a>
            <a href="{{ route('student.proposals.members.index', $proposal) }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                <x-icon name="users" class="w-5 h-5"/> Anggota Tim
            </a>
            @if($proposal->scheme->is_funded)
            <a href="{{ route('student.proposals.funding.index', $proposal) }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                <x-icon name="chart-bar" class="w-5 h-5"/> Rencana Anggaran
            </a>
            @endif
            <a href="{{ route('student.proposals.files.index', $proposal) }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                <x-icon name="arrow-up-tray" class="w-5 h-5"/> Unggah Berkas
            </a>
        </div>

        {{-- Main Form --}}
        <div class="md:col-span-2 card">
            <div class="card-header border-b border-gray-100 mb-0">
                <h2 class="card-title">Data Utama Proposal</h2>
            </div>
            
            <form method="POST" action="{{ route('student.proposals.update', $proposal) }}" class="card-body space-y-5">
                @csrf @method('PATCH')

                <div>
                    <label for="scheme_id" class="form-label">Skema PKM <span class="text-red-500">*</span></label>
                    <select id="scheme_id" name="scheme_id" class="form-input @error('scheme_id') form-input-error @enderror" required>
                        @foreach($schemes as $scheme)
                            <option value="{{ $scheme->id }}" {{ old('scheme_id', $proposal->scheme_id) == $scheme->id ? 'selected' : '' }}>
                                {{ $scheme->code }} - {{ $scheme->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('scheme_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="theme_id" class="form-label">Tema PKM (opsional)</label>
                    <select id="theme_id" name="theme_id" class="form-input @error('theme_id') form-input-error @enderror">
                        <option value="">-- Pilih Tema Jika Relevan --</option>
                        @foreach($themes as $theme)
                            <option value="{{ $theme->id }}" {{ old('theme_id', $proposal->theme_id) == $theme->id ? 'selected' : '' }}>
                                {{ $theme->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('theme_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="title" class="form-label">Judul Proposal <span class="text-red-500">*</span></label>
                    <textarea id="title" name="title" rows="3" class="form-input @error('title') form-input-error @enderror" required>{{ old('title', $proposal->title) }}</textarea>
                    @error('title') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="supervisor_id" class="form-label">Dosen Pendamping <span class="text-red-500">*</span></label>
                    <select id="supervisor_id" name="supervisor_id" class="form-input @error('supervisor_id') form-input-error @enderror" required>
                        <option value="">-- Pilih Dosen Pendamping --</option>
                        @foreach($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}" {{ old('supervisor_id', $proposal->supervisor_id) == $supervisor->id ? 'selected' : '' }}>
                                {{ $supervisor->name }} {{ $supervisor->nidn ? '('.$supervisor->nidn.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('supervisor_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                @if($proposal->scheme->is_funded)
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="form-label">Tgl Mulai <span class="text-red-500">*</span></label>
                            <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $proposal->start_date?->format('Y-m-d')) }}"
                                   class="form-input @error('start_date') form-input-error @enderror" required>
                            @error('start_date') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="end_date" class="form-label">Tgl Selesai <span class="text-red-500">*</span></label>
                            <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $proposal->end_date?->format('Y-m-d')) }}"
                                   class="form-input @error('end_date') form-input-error @enderror" required>
                            @error('end_date') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="admin_cost_amount" class="form-label">Total Biaya Administrasi (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" id="admin_cost_amount" name="admin_cost_amount" value="{{ old('admin_cost_amount', $proposal->admin_cost_amount) }}"
                               class="form-input @error('admin_cost_amount') form-input-error @enderror" min="0" required>
                        <p class="form-hint mt-1">Estimasi biaya habis pakai/administrasi dari total RAB.</p>
                        @error('admin_cost_amount') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div class="pt-4 border-t border-gray-100 flex justify-end gap-3 mt-4">
                    <a href="{{ route('student.proposals.show', $proposal) }}" class="btn-secondary btn">Batal</a>
                    <button type="submit" class="btn-primary btn">
                        <x-icon name="check" class="w-4 h-4"/> Simpan Data Utama
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
