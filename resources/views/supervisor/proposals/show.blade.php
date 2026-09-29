<x-app-layout title="Detail Proposal">
    <x-slot name="breadcrumb">
        <a href="{{ route('supervisor.proposals.index') }}" class="hover:text-gray-600">Daftar Bimbingan</a>
        <span>/</span>
        <span>{{ Str::limit($proposal->title, 40) }}</span>
    </x-slot>

    <div class="flex flex-col lg:flex-row items-start gap-8">
        
        {{-- Kolom Kiri: Detail Proposal --}}
        <div class="flex-1 space-y-6 w-full">
            <div class="flex items-start justify-between flex-wrap gap-4 mb-2">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="badge-indigo">{{ $proposal->scheme->code }}</span>
                        <span class="badge-{{ $proposal->status->color() }}">{{ $proposal->status->label() }}</span>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 leading-tight">
                        {{ $proposal->title }}
                    </h1>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-b border-gray-100">
                    <h3 class="card-title">Informasi Utama</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ketua Tim</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $proposal->leader->name }} ({{ $proposal->leader->nim ?? '-' }})</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tema</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $proposal->theme?->name ?? '-' }}</dd>
                        </div>
                        @if($proposal->scheme->is_funded)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Pelaksanaan</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($proposal->start_date && $proposal->end_date)
                                    {{ $proposal->start_date->format('d/m/Y') }} – {{ $proposal->end_date->format('d/m/Y') }}
                                    ({{ $proposal->start_date->diffInMonths($proposal->end_date) }} bulan)
                                @else
                                    -
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Rencana Anggaran</dt>
                            <dd class="mt-1 text-sm text-gray-900">Rp{{ number_format($proposal->totalFunding(), 0, ',', '.') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Info Keputusan Final --}}
            @if(in_array($proposal->status, [\App\Enums\ProposalStatus::InternalPassed, \App\Enums\ProposalStatus::InternalNotPassed]))
            <div class="card {{ $proposal->status === \App\Enums\ProposalStatus::InternalNotPassed ? 'bg-red-50 border-red-200' : 'bg-emerald-50 border-emerald-200' }} border mb-6">
                <div class="card-header border-b {{ $proposal->status === \App\Enums\ProposalStatus::InternalNotPassed ? 'border-red-100 bg-red-100/50' : 'border-emerald-100 bg-emerald-100/50' }} flex items-center gap-2">
                    @if($proposal->status === \App\Enums\ProposalStatus::InternalNotPassed)
                        <x-icon name="x-circle" class="w-6 h-6 text-red-600"/>
                        <h3 class="card-title text-red-900 font-bold">Proposal Bimbingan Anda Tidak Lolos Pendanaan</h3>
                    @else
                        <x-icon name="check-circle" class="w-6 h-6 text-emerald-600"/>
                        <h3 class="card-title text-emerald-900 font-bold">Proposal Bimbingan Anda Lolos Pendanaan!</h3>
                    @endif
                </div>
                <div class="p-6 space-y-4">
                    @if($proposal->status === \App\Enums\ProposalStatus::InternalPassed)
                        @if($proposal->belmawa_result)
                            <div class="bg-white p-4 rounded border border-emerald-100 mt-4 flex items-center justify-between">
                                <div>
                                    <h4 class="text-xs font-bold text-gray-500 uppercase">Status Seleksi Nasional (Belmawa)</h4>
                                    <div class="text-sm text-gray-900 font-bold mt-1">{{ $proposal->belmawa_result }}</div>
                                </div>
                                @if($proposal->pimnas_status)
                                <div>
                                    <h4 class="text-xs font-bold text-gray-500 uppercase">Pencapaian PIMNAS</h4>
                                    <div class="text-sm text-gray-900 font-bold mt-1"><span class="badge-amber">{{ $proposal->pimnas_status }}</span></div>
                                </div>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            @endif

            {{-- Anggota Tim --}}
            <div class="card">
                <div class="card-header border-b border-gray-100">
                    <h3 class="card-title">Anggota Tambahan</h3>
                </div>
                <div class="p-0">
                    <table class="table w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            @forelse($proposal->members as $member)
                            <tr>
                                <td class="px-6 py-4"><span class="badge-gray">{{ $member->role }}</span></td>
                                <td class="px-6 py-4 text-gray-700">{{ $member->user->name }} ({{ $member->user->nim ?? '-' }})</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-6 py-6 text-center text-gray-500 italic">Tidak ada anggota tambahan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Berkas Terunggah --}}
            <div class="card">
                <div class="card-header border-b border-gray-100">
                    <h3 class="card-title">Berkas Terunggah</h3>
                </div>
                <div class="p-0">
                    <table class="table w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            @forelse($proposal->files as $file)
                            <tr>
                                <td class="px-6 py-4 text-gray-600">{{ $file->requirement->label }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $file->original_name }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('student.proposals.files.download', [$proposal, $file]) }}" class="btn-secondary btn px-3 py-1.5 text-xs" target="_blank">Unduh</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-6 text-center text-gray-500 italic">Belum ada berkas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Riwayat Validasi Sebelumnya --}}
            @if($validations->count() > 0)
            <div class="card">
                <div class="card-header border-b border-gray-100">
                    <h3 class="card-title">Riwayat Validasi</h3>
                </div>
                <div class="p-0">
                    <table class="table w-full text-sm">
                        <thead>
                            <tr>
                                <th>Tahap</th>
                                <th>Keputusan</th>
                                <th>Catatan</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($validations as $val)
                            <tr>
                                <td>Tahap {{ $val->round }}</td>
                                <td>
                                    @if($val->decision === 'approved')
                                        <span class="text-emerald-600 font-medium"><x-icon name="check" class="w-4 h-4 inline"/> Disetujui</span>
                                    @else
                                        <span class="text-red-600 font-medium"><x-icon name="x-mark" class="w-4 h-4 inline"/> Ditolak/Revisi</span>
                                    @endif
                                </td>
                                <td class="text-gray-600 max-w-xs truncate" title="{{ $val->note }}">{{ $val->note ?: '-' }}</td>
                                <td class="text-gray-500">{{ $val->decided_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>

        {{-- Kolom Kanan: Form Validasi --}}
        <div class="w-full lg:w-96 flex-shrink-0">
            @if(in_array($proposal->status->value, ['supervisor_validation_1', 'supervisor_validation_2']))
            <div class="card bg-white shadow-xl shadow-blue-900/5 ring-1 ring-blue-900/10 sticky top-6">
                <div class="p-6 border-b border-gray-100 bg-blue-50/50 rounded-t-xl">
                    <h3 class="text-lg font-bold text-gray-900">Validasi Proposal</h3>
                    <p class="text-sm text-gray-500 mt-1">Anda diminta untuk memverifikasi proposal ini sebelum diteruskan ke admin universitas.</p>
                </div>
                <form method="POST" action="{{ route('supervisor.proposals.validate', $proposal) }}" class="p-6 space-y-5" x-data="{ decision: 'approved' }">
                    @csrf
                    
                    <div class="space-y-3">
                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none" 
                               :class="decision === 'approved' ? 'border-emerald-500 ring-1 ring-emerald-500' : 'border-gray-300'">
                            <input type="radio" name="decision" value="approved" class="sr-only" x-model="decision">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900">Setujui Proposal</span>
                                    <span class="mt-1 flex items-center text-sm text-gray-500">Proposal dinilai layak.</span>
                                </span>
                            </span>
                            <x-icon name="check-circle" class="h-5 w-5 text-emerald-500" x-show="decision === 'approved'"/>
                        </label>

                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none"
                               :class="decision === 'rejected' ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300'">
                            <input type="radio" name="decision" value="rejected" class="sr-only" x-model="decision">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900">Tolak / Minta Revisi</span>
                                    <span class="mt-1 flex items-center text-sm text-gray-500">Kembalikan ke mahasiswa.</span>
                                </span>
                            </span>
                            <x-icon name="check-circle" class="h-5 w-5 text-red-500" x-show="decision === 'rejected'" style="display: none;"/>
                        </label>
                    </div>

                    <div x-show="decision === 'rejected'" style="display: none;">
                        <label for="note" class="form-label text-red-700">Catatan Revisi <span class="text-red-500">*</span></label>
                        <textarea id="note" name="note" rows="4" class="form-input border-red-200 focus:border-red-500 focus:ring-red-500" placeholder="Tuliskan bagian mana yang perlu diperbaiki oleh mahasiswa..."></textarea>
                        @error('note') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="btn-primary btn w-full justify-center">
                        Simpan Keputusan
                    </button>
                </form>
            </div>
            @else
            <div class="card p-6 bg-gray-50 border border-gray-100 text-center sticky top-6">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                    <x-icon name="information-circle" class="w-8 h-8 text-blue-500"/>
                </div>
                <h3 class="text-gray-900 font-semibold mb-1">Tidak Ada Aksi</h3>
                <p class="text-sm text-gray-500">Proposal ini saat ini tidak membutuhkan validasi dari Anda.</p>
            </div>
            @endif
        </div>

    </div>
</x-app-layout>
