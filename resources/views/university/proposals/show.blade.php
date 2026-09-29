<x-app-layout title="Validasi Universitas">
    <x-slot name="breadcrumb">
        <a href="{{ route('university.proposals.index') }}" class="hover:text-gray-600">Validasi Universitas</a>
        <span>/</span>
        <span>Detail</span>
    </x-slot>

    <div class="flex flex-col lg:flex-row items-start gap-8">
        
        {{-- Kolom Kiri: Data Proposal --}}
        <div class="flex-1 w-full space-y-6">
            <div class="card p-6 border-b border-gray-100 bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="flex items-center gap-3 mb-2">
                    <span class="badge-indigo">{{ $proposal->scheme->code }}</span>
                    <span class="badge-{{ $proposal->status->color() }}">{{ $proposal->status->label() }}</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 leading-tight mb-4">
                    {{ $proposal->title }}
                </h1>
                
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Ketua Tim</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $proposal->leader->name }} ({{ $proposal->leader->nim ?? '-' }})</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tema</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $proposal->theme?->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dosen Pendamping Awal</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $proposal->supervisor?->name ?? '-' }}</dd>
                    </div>
                    @if($proposal->scheme->is_funded)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Anggaran</dt>
                        <dd class="mt-1 text-sm text-gray-900">Rp{{ number_format($proposal->totalFunding(), 0, ',', '.') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Anggota Tim --}}
            <div class="card">
                <div class="card-header border-b border-gray-100 flex items-center justify-between">
                    <h3 class="card-title">Anggota Tim</h3>
                </div>
                <div class="p-0">
                    <table class="table w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            @foreach($proposal->members as $member)
                            <tr>
                                <td class="px-6 py-4"><span class="badge-gray">{{ $member->role }}</span></td>
                                <td class="px-6 py-4 text-gray-700">{{ $member->user->name }} ({{ $member->user->nim ?? '-' }})</td>
                            </tr>
                            @endforeach
                            @if($proposal->members->isEmpty())
                            <tr>
                                <td colspan="2" class="px-6 py-6 text-center text-gray-500 italic">Tidak ada anggota tambahan.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Berkas Terakhir --}}
            <div class="card">
                <div class="card-header border-b border-gray-100">
                    <h3 class="card-title">Berkas Terunggah (Revisi Akhir)</h3>
                </div>
                <div class="p-0">
                    <table class="table w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            @forelse($proposal->files as $file)
                            <tr>
                                <td class="px-6 py-4 text-gray-600">{{ $file->requirement->label }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $file->original_name }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('student.proposals.files.download', [$proposal, $file]) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs" target="_blank">Unduh</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-6 text-center text-gray-500 italic">Belum ada berkas yang diunggah.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Histori Validasi Universitas --}}
            @if($proposal->universityValidations->isNotEmpty())
            <div class="card">
                <div class="card-header border-b border-gray-100">
                    <h3 class="card-title">Riwayat Validasi</h3>
                </div>
                <div class="p-0">
                    <table class="table w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            @foreach($proposal->universityValidations as $val)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-xs text-gray-500">{{ $val->decided_at?->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($val->decision === 'approved')
                                        <span class="badge-emerald">Disetujui</span>
                                    @else
                                        <span class="badge-red">Ditolak (Perlu Revisi)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    {{ $val->note ?: '-' }}
                                </td>
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
            @if($proposal->status === \App\Enums\ProposalStatus::UniversityValidation)
            <div class="card bg-white shadow-xl shadow-blue-900/5 ring-1 ring-blue-900/10 sticky top-6">
                <div class="p-6 border-b border-gray-100 bg-blue-50/50 rounded-t-xl">
                    <h3 class="text-lg font-bold text-gray-900">Validasi Universitas</h3>
                    <p class="text-sm text-gray-500 mt-1">Berikan keputusan validasi akhir Anda terhadap proposal ini.</p>
                </div>
                <form method="POST" action="{{ route('university.proposals.validate', $proposal) }}" class="p-6 space-y-5" x-data="{ decision: 'approved' }">
                    @csrf
                    
                    <div class="space-y-3">
                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none" 
                               :class="decision === 'approved' ? 'border-emerald-500 ring-1 ring-emerald-500' : 'border-gray-300'">
                            <input type="radio" name="decision" value="approved" class="sr-only" x-model="decision">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900">Setujui Proposal</span>
                                    <span class="mt-1 flex items-center text-sm text-gray-500">Proposal lolos ke tahap seleksi final PT.</span>
                                </span>
                            </span>
                            <x-icon name="check-circle" class="h-5 w-5 text-emerald-500" x-show="decision === 'approved'"/>
                        </label>

                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none"
                               :class="decision === 'rejected' ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300'">
                            <input type="radio" name="decision" value="rejected" class="sr-only" x-model="decision">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900">Kembalikan (Tolak)</span>
                                    <span class="mt-1 flex items-center text-sm text-gray-500">Kembalikan ke mahasiswa untuk revisi ulang berkas akhir.</span>
                                </span>
                            </span>
                            <x-icon name="check-circle" class="h-5 w-5 text-red-500" x-show="decision === 'rejected'" style="display: none;"/>
                        </label>
                    </div>

                    <div>
                        <label for="note" class="form-label">Catatan <span x-show="decision === 'rejected'" class="text-red-500">*</span></label>
                        <textarea id="note" name="note" rows="3" class="form-input" :required="decision === 'rejected'" placeholder="Tuliskan catatan perbaikan atau komentar akhir..."></textarea>
                        @error('note') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="btn-primary btn w-full justify-center">
                        Simpan Validasi
                    </button>
                </form>
            </div>
            @else
            <div class="card p-6 bg-gray-50 border border-gray-100 sticky top-6">
                <h3 class="text-gray-900 font-bold mb-4">Status Proposal</h3>
                <p class="text-sm text-gray-600">Saat ini proposal berada pada tahap <strong>{{ $proposal->status->label() }}</strong> dan tidak dalam antrean validasi Anda.</p>
            </div>
            @endif
        </div>

    </div>
</x-app-layout>
