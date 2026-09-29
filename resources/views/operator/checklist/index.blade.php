<x-app-layout title="Form Administratif (Checklist)">
    @if(!$cycle)
        <div class="alert-warning">
            <x-icon name="cog" class="w-5 h-5"/>
            <span>Belum ada siklus aktif.</span>
        </div>
    @else
        <div class="mb-4 flex items-center gap-2">
            <span class="badge-green">Siklus: {{ $cycle->name }}</span>
        </div>

        <div class="space-y-4">
            @forelse($forms as $form)
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">{{ $form->checklist_group->label() ?? $form->checklist_group->value }}</h2>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $form->items->count() }} item</div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($form->isLocked())
                            <span class="badge-green flex items-center gap-1">
                                <x-icon name="lock-closed" class="w-3 h-3"/> Terkunci
                            </span>
                        @else
                            <span class="badge-amber flex items-center gap-1">
                                <x-icon name="lock-open" class="w-3 h-3"/> Draft
                            </span>
                        @endif
                        <a href="{{ route('operator.checklist.show', $form) }}" class="btn-secondary btn btn-sm">
                            <x-icon name="eye" class="w-3.5 h-3.5"/> Lihat
                        </a>
                        @if(!$form->isLocked())
                            <a href="{{ route('operator.checklist.edit', $form) }}" class="btn-primary btn btn-sm">
                                <x-icon name="pencil" class="w-3.5 h-3.5"/> Edit
                            </a>
                            <form method="POST" action="{{ route('operator.checklist.confirm', $form) }}"
                                  onsubmit="return confirm('Konfirmasi dan kunci form ini? Tidak dapat diubah setelah dikunci.')">
                                @csrf
                                <button type="submit" class="btn-success btn btn-sm">
                                    <x-icon name="shield-check" class="w-3.5 h-3.5"/> Konfirmasi
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('operator.checklist.duplicate', $form) }}">
                                @csrf
                                <button type="submit" class="btn-secondary btn btn-sm">
                                    <x-icon name="document-arrow-down" class="w-3.5 h-3.5"/> Buat Versi Baru
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                @if($form->isLocked())
                    <div class="px-6 py-2 text-xs text-gray-400 bg-gray-50">
                        Dikonfirmasi oleh {{ $form->confirmedBy?->name }} pada {{ $form->confirmed_at?->format('d M Y H:i') }}
                    </div>
                @endif
            </div>
            @empty
            <div class="alert-info">
                <x-icon name="document-text" class="w-5 h-5"/>
                <span>Belum ada form checklist untuk siklus ini. Jalankan seeder atau buat manual.</span>
            </div>
            @endforelse
        </div>
    @endif
</x-app-layout>
