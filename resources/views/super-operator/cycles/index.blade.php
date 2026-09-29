<x-app-layout title="Siklus PKM">
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">{{ $cycles->count() }} siklus terdaftar</p>
        <a href="{{ route('super-operator.cycles.create') }}" class="btn-primary btn">
            <x-icon name="plus" class="w-4 h-4"/> Buat Siklus Baru
        </a>
    </div>

    <div class="space-y-4">
        @forelse($cycles as $cycle)
        <div class="card">
            <div class="card-header">
                <div class="flex items-center gap-3">
                    @if($cycle->is_active)
                        <span class="badge-green">Aktif</span>
                    @else
                        <span class="badge-gray">Tidak Aktif</span>
                    @endif
                    <div>
                        <h2 class="card-title">{{ $cycle->name }}</h2>
                        <div class="text-xs text-gray-400 mt-0.5">
                            Tahun {{ $cycle->year }} · {{ $cycle->proposals_count }} proposal · Maks {{ $cycle->max_proposals_per_supervisor }} proposal/dosen
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('super-operator.cycles.edit', $cycle) }}" class="btn-secondary btn btn-sm">
                        <x-icon name="pencil" class="w-3.5 h-3.5"/> Edit
                    </a>
                    @if($cycle->proposals_count === 0)
                    <form method="POST" action="{{ route('super-operator.cycles.destroy', $cycle) }}"
                          onsubmit="return confirm('Hapus siklus ini?')">
                        @csrf @method('DELETE')
                        <button class="btn-danger btn btn-sm">
                            <x-icon name="trash" class="w-3.5 h-3.5"/>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="card">
            <div class="card-body text-center py-12">
                <p class="text-gray-400 mb-4">Belum ada siklus PKM.</p>
                <a href="{{ route('super-operator.cycles.create') }}" class="btn-primary btn">Buat Siklus Pertama</a>
            </div>
        </div>
        @endforelse
    </div>
</x-app-layout>
