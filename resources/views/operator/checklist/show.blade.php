<x-app-layout title="Detail Form Checklist">
    <x-slot name="breadcrumb">
        <a href="{{ route('operator.checklist.index') }}" class="hover:text-gray-600">Form Administratif</a>
        <span>/</span>
        <span>{{ $checklistForm->checklist_group->value }}</span>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">{{ $checklistForm->checklist_group->label() ?? $checklistForm->checklist_group->value }}</h2>
                <div class="text-xs text-gray-400 mt-0.5">Siklus: {{ $checklistForm->cycle->name }}</div>
            </div>
            <div class="flex items-center gap-2">
                @if($checklistForm->isLocked())
                    <span class="badge-green flex items-center gap-1">
                        <x-icon name="lock-closed" class="w-3 h-3"/> Terkunci
                    </span>
                    <form method="POST" action="{{ route('operator.checklist.duplicate', $checklistForm) }}">
                        @csrf
                        <button type="submit" class="btn-secondary btn btn-sm">Buat Versi Baru</button>
                    </form>
                @else
                    <a href="{{ route('operator.checklist.edit', $checklistForm) }}" class="btn-primary btn btn-sm">
                        <x-icon name="pencil" class="w-3.5 h-3.5"/> Edit Item
                    </a>
                    <form method="POST" action="{{ route('operator.checklist.confirm', $checklistForm) }}"
                          onsubmit="return confirm('Konfirmasi dan kunci form ini?')">
                        @csrf
                        <button type="submit" class="btn-success btn btn-sm">
                            <x-icon name="shield-check" class="w-3.5 h-3.5"/> Konfirmasi
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-12">#</th>
                        <th>Kode</th>
                        <th>Item Checklist</th>
                        <th>Jenis</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($checklistForm->items as $item)
                    <tr>
                        <td class="text-gray-400">{{ $loop->iteration }}</td>
                        <td><span class="badge-gray font-mono">{{ $item->code }}</span></td>
                        <td>{{ $item->label }}</td>
                        <td>
                            @if($item->kind->value === 'auto')
                                <span class="badge-blue">Otomatis</span>
                            @else
                                <span class="badge-amber">Manual</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-gray-400 py-8">Belum ada item.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($checklistForm->isLocked())
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 text-xs text-gray-400">
            Dikonfirmasi oleh <strong>{{ $checklistForm->confirmedBy?->name }}</strong>
            pada {{ $checklistForm->confirmed_at?->format('d M Y H:i') }} WITA
        </div>
        @endif
    </div>
</x-app-layout>
