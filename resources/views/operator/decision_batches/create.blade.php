<x-app-layout title="Buat Batch Keputusan">
    <x-slot name="breadcrumb">
        <a href="{{ route('operator.decision-batches.index') }}" class="hover:text-gray-600">Batch Keputusan</a>
        <span>/</span>
        <span>Buat Baru</span>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="card p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Buat Batch Keputusan</h2>
            
            <form method="POST" action="{{ route('operator.decision-batches.store') }}" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="cycle_id" class="form-label">Siklus <span class="text-red-500">*</span></label>
                        <select name="cycle_id" id="cycle_id" class="form-input" required>
                            @foreach($cycles as $cycle)
                                <option value="{{ $cycle->id }}" {{ old('cycle_id') == $cycle->id ? 'selected' : '' }}>{{ $cycle->name }}</option>
                            @endforeach
                        </select>
                        @error('cycle_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label for="scheme_id" class="form-label">Skema PKM <span class="text-red-500">*</span></label>
                        <select name="scheme_id" id="scheme_id" class="form-input" required>
                            <option value="">-- Pilih Skema --</option>
                            @foreach($schemes as $scheme)
                                <option value="{{ $scheme->id }}" {{ old('scheme_id') == $scheme->id ? 'selected' : '' }}>{{ $scheme->code }} - {{ Str::limit($scheme->name, 30) }}</option>
                            @endforeach
                        </select>
                        @error('scheme_id') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="name" class="form-label">Nama Batch <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" class="form-input" required value="{{ old('name') }}" placeholder="Misal: Batch 1 - Skema PKM-RE Tahun 2026">
                    <p class="text-xs text-gray-500 mt-1">Gunakan nama yang representatif untuk memudahkan pencarian.</p>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="participants" class="form-label">Tim Penilai (Opsional)</label>
                    <select name="participants[]" id="participants" class="form-input h-32" multiple>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ in_array($user->id, old('participants', [])) ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->primaryRole()->label() }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Tahan tombol Ctrl/Command (Mac) untuk memilih lebih dari satu.</p>
                    @error('participants') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('operator.decision-batches.index') }}" class="btn-secondary btn px-5">Batal</a>
                    <button type="submit" class="btn-primary btn px-8">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
