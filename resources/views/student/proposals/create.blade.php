<x-app-layout title="Buat Draf Proposal">
    <x-slot name="breadcrumb">
        <a href="{{ route('student.proposals.index') }}" class="hover:text-gray-600">Proposal Saya</a>
        <span>/</span><span>Baru</span>
    </x-slot>

    <div class="max-w-2xl">
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Buat Draf Proposal Baru</h2>
                    <p class="text-sm text-gray-500 mt-1">Siklus aktif: {{ $cycle->name }}</p>
                </div>
            </div>
            
            <form method="POST" action="{{ route('student.proposals.store') }}" class="card-body space-y-5">
                @csrf

                <div>
                    <label for="scheme_id" class="form-label">Skema PKM <span class="text-red-500">*</span></label>
                    <select id="scheme_id" name="scheme_id" class="form-input @error('scheme_id') form-input-error @enderror" required>
                        <option value="">-- Pilih Skema --</option>
                        @foreach($schemes as $scheme)
                            <option value="{{ $scheme->id }}" {{ old('scheme_id') == $scheme->id ? 'selected' : '' }}>
                                {{ $scheme->code }} - {{ $scheme->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('scheme_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="title" class="form-label">Judul Proposal <span class="text-red-500">*</span></label>
                    <textarea id="title" name="title" rows="3" class="form-input @error('title') form-input-error @enderror" required placeholder="Judul maksimal 20 kata sesuai pedoman...">{{ old('title') }}</textarea>
                    <p class="form-hint mt-1">Judul masih dapat diubah setelah draf dibuat.</p>
                    @error('title') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                    <a href="{{ route('student.proposals.index') }}" class="btn-secondary btn">Batal</a>
                    <button type="submit" class="btn-primary btn">
                        Buat Draf & Lanjut Isi Data <x-icon name="arrow-up-tray" class="w-4 h-4 ml-1 rotate-90"/>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
