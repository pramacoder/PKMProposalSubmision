<x-app-layout title="Laporan & Berita Acara">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 leading-tight">Laporan & Berita Acara</h2>
            <p class="text-sm text-gray-500 mt-1">Rekapitulasi capaian PKM untuk keperluan pelaporan dan arsip Pimpinan PT.</p>
        </div>

        <form method="GET" action="{{ route('super-operator.reports.index') }}" class="flex gap-2 w-full md:w-auto">
            <select name="cycle_id" class="form-input" onchange="this.form.submit()">
                @foreach($cycles as $cycle)
                    <option value="{{ $cycle->id }}" {{ $cycle->id == $cycleId ? 'selected' : '' }}>
                        Tahun {{ $cycle->year }} {{ $cycle->is_active ? '(Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Tabs --}}
    <div x-data="{ tab: 'berita-acara' }">
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-6">
                <button @click="tab = 'berita-acara'" :class="tab === 'berita-acara' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Berita Acara per Bidang
                </button>
                <button @click="tab = 'simbelmawa'" :class="tab === 'simbelmawa' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Laporan Simbelmawa
                </button>
                <button @click="tab = 'prestasi'" :class="tab === 'prestasi' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Laporan Prestasi
                </button>
            </nav>
        </div>

        {{-- Tab 1: Berita Acara per Bidang --}}
        <div x-show="tab === 'berita-acara'" class="space-y-6">
            <div class="card p-6 flex flex-col md:flex-row md:items-center justify-between bg-indigo-50/50 border-indigo-100 mb-6">
                <div>
                    <h3 class="font-bold text-indigo-900">Rekapitulasi Evaluasi Internal</h3>
                    <p class="text-sm text-indigo-700">Daftar jumlah proposal yang didanai vs total proposal yang masuk ke tahap akhir untuk setiap bidang.</p>
                </div>
            </div>

            <div class="card">
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Bidang (Skema)</th>
                                <th class="text-center">Total Proposal Akhir</th>
                                <th class="text-center">Lolos Didanai PT</th>
                                <th class="text-center">Persentase</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php
                                $grandTotal = 0;
                                $grandPassed = 0;
                            @endphp
                            @forelse($beritaAcara as $item)
                                @php
                                    $grandTotal += $item->total;
                                    $grandPassed += $item->passed_count;
                                    $percentage = $item->total > 0 ? round(($item->passed_count / $item->total) * 100, 1) : 0;
                                @endphp
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="font-medium text-gray-900">{{ $item->scheme->name }} ({{ $item->scheme->code }})</td>
                                    <td class="text-center">{{ $item->total }}</td>
                                    <td class="text-center font-bold text-indigo-600">{{ $item->passed_count }}</td>
                                    <td class="text-center text-sm text-gray-500">{{ $percentage }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500 italic">Belum ada data rekapitulasi untuk siklus ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($grandTotal > 0)
                        <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                            <tr>
                                <th class="text-right py-3 px-4 font-bold text-gray-900">Total Keseluruhan</th>
                                <th class="text-center py-3 px-4 font-bold text-gray-900">{{ $grandTotal }}</th>
                                <th class="text-center py-3 px-4 font-bold text-indigo-700">{{ $grandPassed }}</th>
                                <th class="text-center py-3 px-4 font-bold text-gray-900">{{ round(($grandPassed / $grandTotal) * 100, 1) }}%</th>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- Tab 2: Laporan Simbelmawa --}}
        <div x-show="tab === 'simbelmawa'" style="display: none;" class="space-y-6">
            <div class="card p-6 flex flex-col md:flex-row md:items-center justify-between bg-blue-50/50 border-blue-100 mb-6">
                <div>
                    <h3 class="font-bold text-blue-900">Daftar Unggah Simbelmawa</h3>
                    <p class="text-sm text-blue-700">Daftar seluruh proposal yang siap untuk diunggah dan diajukan pendanaannya ke kementerian (Simbelmawa).</p>
                </div>
            </div>

            <div class="card">
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ketua Pengusul</th>
                                <th>Proposal</th>
                                <th>Dosen Pembimbing</th>
                                <th>Total Rencana Anggaran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($simbelmawaReports as $proposal)
                            <tr class="hover:bg-gray-50 transition">
                                <td>
                                    <div class="font-medium text-gray-900">{{ $proposal->leader->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $proposal->leader->nim ?? '-' }}</div>
                                </td>
                                <td class="max-w-xs">
                                    <p class="font-medium text-gray-900 line-clamp-2" title="{{ $proposal->title }}">{{ $proposal->title }}</p>
                                    <p class="text-xs text-indigo-600 mt-1 font-semibold">{{ $proposal->scheme->code }}</p>
                                </td>
                                <td class="text-sm text-gray-700">{{ $proposal->supervisor?->name ?? '-' }}</td>
                                <td class="text-sm font-medium text-gray-900">
                                    Rp{{ number_format($proposal->totalFunding(), 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500 italic">Belum ada proposal yang lolos didanai internal.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tab 3: Laporan Prestasi --}}
        <div x-show="tab === 'prestasi'" style="display: none;" class="space-y-6">
            <div class="card p-6 flex flex-col md:flex-row md:items-center justify-between bg-amber-50/50 border-amber-100 mb-6">
                <div>
                    <h3 class="font-bold text-amber-900">Laporan Prestasi (Belmawa & PIMNAS)</h3>
                    <p class="text-sm text-amber-700">Daftar rekam jejak capaian nasional mahasiswa dalam ajang pendanaan Belmawa dan PIMNAS.</p>
                </div>
            </div>

            <div class="card">
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ketua Pengusul</th>
                                <th>Skema & Judul</th>
                                <th>Status Belmawa</th>
                                <th>Capaian PIMNAS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($prestasiReports as $proposal)
                            <tr class="hover:bg-gray-50 transition">
                                <td>
                                    <div class="font-medium text-gray-900">{{ $proposal->leader->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $proposal->leader->nim ?? '-' }}</div>
                                </td>
                                <td class="max-w-xs">
                                    <span class="badge-indigo mb-1 inline-block">{{ $proposal->scheme->code }}</span>
                                    <p class="font-medium text-gray-900 line-clamp-2" title="{{ $proposal->title }}">{{ $proposal->title }}</p>
                                </td>
                                <td>
                                    @if($proposal->belmawa_result === 'Lolos Didanai')
                                        <span class="text-emerald-600 font-bold"><x-icon name="check-badge" class="w-4 h-4 inline"/> Lolos Didanai</span>
                                    @elseif($proposal->belmawa_result === 'Tidak Lolos')
                                        <span class="text-red-600 font-bold">Tidak Lolos</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($proposal->pimnas_status)
                                        <span class="badge-amber font-bold">{{ $proposal->pimnas_status }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500 italic">Belum ada catatan prestasi nasional.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
