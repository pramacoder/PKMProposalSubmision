<x-app-layout title="Ruang Kontrol">

    @if(!$cycle)
        <div class="alert-warning mb-6">
            <x-icon name="cog" class="w-5 h-5 flex-shrink-0"/>
            <span>Belum ada siklus aktif. <a href="{{ route('super-operator.cycles.create') }}" class="font-semibold underline">Buat siklus PKM</a> terlebih dahulu.</span>
        </div>
    @else
        <div class="mb-6 flex items-center gap-3">
            <span class="badge-green text-sm px-3 py-1">Siklus Aktif: {{ $cycle->name }} ({{ $cycle->year }})</span>
            <span class="text-sm text-gray-400">Maks {{ $cycle->max_proposals_per_supervisor }} proposal/dosen</span>
        </div>

        {{-- Phase Windows --}}
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Jadwal Fase</h2>
                <span class="text-xs text-gray-400">Waktu WITA (UTC+8)</span>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($phases as $phaseKey => $phaseLabel)
                    @php
                        $window = $cycle->phaseWindows->firstWhere('phase', $phaseKey);
                        $isOpen = $window?->isOpen();
                    @endphp
                    <div x-data="{ editing: false }" class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div>
                                    <div class="font-medium text-sm text-gray-800">{{ $phaseLabel }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        @if($window)
                                            @if($window->opens_at) Buka: {{ $window->opens_at->setTimezone('Asia/Makassar')->format('d M Y H:i') }} WITA @else <span class="italic">Belum diset</span> @endif
                                            @if($window->closes_at) · Tutup: {{ $window->closes_at->setTimezone('Asia/Makassar')->format('d M Y H:i') }} WITA @endif
                                        @else
                                            <span class="italic">Belum dikonfigurasi</span>
                                        @endif
                                    </div>
                                </div>
                                @if($window?->forced_open)
                                    <span class="phase-forced">Paksa Buka</span>
                                @elseif($window?->forced_closed)
                                    <span class="phase-closed">Paksa Tutup</span>
                                @elseif($isOpen)
                                    <span class="phase-open">Terbuka</span>
                                @else
                                    <span class="phase-closed">Tertutup</span>
                                @endif
                            </div>
                            <button @click="editing = !editing" class="btn-secondary btn btn-sm">
                                <x-icon name="pencil" class="w-3.5 h-3.5"/>
                                <span x-text="editing ? 'Tutup' : 'Atur'"></span>
                            </button>
                        </div>

                        {{-- Inline Edit Form --}}
                        <div x-show="editing" x-cloak class="mt-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <form method="POST"
                                  action="{{ route('operator.control.phase.update', [$cycle, $phaseKey]) }}">
                                @csrf @method('PATCH')
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="form-label">Tanggal & Waktu Buka (WITA)</label>
                                        <input type="datetime-local" name="opens_at"
                                               value="{{ $window?->opens_at?->setTimezone('Asia/Makassar')->format('Y-m-d\TH:i') }}"
                                               class="form-input">
                                    </div>
                                    <div>
                                        <label class="form-label">Tanggal & Waktu Tutup (WITA)</label>
                                        <input type="datetime-local" name="closes_at"
                                               value="{{ $window?->closes_at?->setTimezone('Asia/Makassar')->format('Y-m-d\TH:i') }}"
                                               class="form-input">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="forced_open" value="1"
                                               class="rounded text-amber-500"
                                               {{ $window?->forced_open ? 'checked' : '' }}>
                                        <span class="text-sm font-medium text-gray-700">Paksa Buka (override jadwal)</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="forced_closed" value="1"
                                               class="rounded text-red-500"
                                               {{ $window?->forced_closed ? 'checked' : '' }}>
                                        <span class="text-sm font-medium text-gray-700">Paksa Tutup (override jadwal)</span>
                                    </label>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Alasan (wajib jika paksa buka/tutup)</label>
                                    <input type="text" name="forced_reason"
                                           value="{{ $window?->forced_reason }}"
                                           class="form-input"
                                           placeholder="Misal: Perpanjangan karena kendala teknis">
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="editing = false" class="btn-secondary btn btn-sm">Batal</button>
                                    <button type="submit" class="btn-primary btn btn-sm">
                                        <x-icon name="check" class="w-3.5 h-3.5"/>
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Kuota per Skema --}}
        <div class="card mt-6">
            <div class="card-header">
                <h2 class="card-title">Kuota & Batas Dana per Skema</h2>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Skema</th>
                            <th class="text-center">Kuota</th>
                            <th class="text-center">Min PT</th>
                            <th class="text-center">Max PT</th>
                            <th>Belmawa (Min–Max)</th>
                            <th class="text-center">Admin %</th>
                            <th class="text-center">Durasi (Bln)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($cycle->schemeSettings as $setting)
                        <tr>
                            <td>
                                <span class="badge-indigo">{{ $setting->scheme->code }}</span>
                                <span class="ml-2 text-xs text-gray-600">{{ $setting->scheme->name }}</span>
                            </td>
                            <td class="text-center font-medium">{{ $setting->quota ?: '—' }}</td>
                            <td class="text-center text-xs">{{ number_format($setting->min_pt) }}</td>
                            <td class="text-center text-xs">{{ number_format($setting->max_pt) }}</td>
                            <td class="text-xs text-gray-600">
                                Rp{{ number_format($setting->min_belmawa/1e6, 1) }}jt – Rp{{ number_format($setting->max_belmawa/1e6, 1) }}jt
                            </td>
                            <td class="text-center text-xs">{{ $setting->max_admin_percent }}%</td>
                            <td class="text-center text-xs">{{ $setting->min_months }}–{{ $setting->max_months }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-app-layout>
