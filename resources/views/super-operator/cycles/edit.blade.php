<x-app-layout title="Edit Siklus: {{ $cycle->name }}">
    <x-slot name="breadcrumb">
        <a href="{{ route('super-operator.cycles.index') }}" class="hover:text-gray-600">Siklus PKM</a>
        <span>/</span><span>Edit</span>
    </x-slot>

    <div class="max-w-lg">
        <div class="card">
            <div class="card-header"><h2 class="card-title">Edit Siklus</h2></div>
            <form method="POST" action="{{ route('super-operator.cycles.update', $cycle) }}" class="card-body space-y-5">
                @csrf @method('PATCH')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="year" class="form-label">Tahun <span class="text-red-500">*</span></label>
                        <input id="year" name="year" type="number" value="{{ old('year', $cycle->year) }}"
                               class="form-input @error('year') form-input-error @enderror" required>
                        @error('year') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="max_proposals_per_supervisor" class="form-label">Maks Proposal/Dosen</label>
                        <input id="max_proposals_per_supervisor" name="max_proposals_per_supervisor"
                               type="number" value="{{ old('max_proposals_per_supervisor', $cycle->max_proposals_per_supervisor) }}"
                               class="form-input" min="1" max="50" required>
                    </div>
                </div>
                <div>
                    <label for="name" class="form-label">Nama Siklus <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text" value="{{ old('name', $cycle->name) }}"
                           class="form-input" required>
                </div>
                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                           class="rounded text-blue-600"
                           {{ old('is_active', $cycle->is_active) ? 'checked' : '' }}>
                    <div>
                        <div class="font-medium text-sm">Siklus Aktif</div>
                        <div class="text-xs text-gray-400">Siklus lain akan dinonaktifkan jika ini diaktifkan.</div>
                    </div>
                </label>
                <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                    <a href="{{ route('super-operator.cycles.index') }}" class="btn-secondary btn">Batal</a>
                    <button type="submit" class="btn-primary btn">
                        <x-icon name="check" class="w-4 h-4"/> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
