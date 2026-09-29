<x-app-layout title="Detail Proposal">
    <x-slot name="breadcrumb">
        <a href="{{ route('student.proposals.index') }}" class="hover:text-gray-600">Proposal Saya</a>
        <span>/</span>
        <span>{{ $proposal->title ? Str::limit($proposal->title, 40) : 'Draf' }}</span>
    </x-slot>

    <div class="flex items-start justify-between mb-6 flex-wrap gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="badge-indigo">{{ $proposal->scheme->code }}</span>
                <span class="badge-{{ $proposal->status->color() }}">{{ $proposal->status->label() }}</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 leading-tight">
                {{ $proposal->title ?: 'Judul Proposal Belum Diisi' }}
            </h1>
            <div class="text-sm text-gray-500 mt-2">
                Ketua: {{ auth()->user()->name }} · Siklus: {{ $proposal->cycle->name }}
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            @if($proposal->isEditable())
                <form method="POST" action="{{ route('student.proposals.withdraw', $proposal) }}" onsubmit="return confirm('Tarik proposal ini? Status akan menjadi Ditarik.')">
                    @csrf
                    <button class="btn-secondary btn text-red-600 hover:text-red-700 hover:bg-red-50">Tarik Proposal</button>
                </form>
            @endif
        </div>
    </div>

    {{-- Kesiapan Submit --}}
    @if(in_array($proposal->status, [\App\Enums\ProposalStatus::Draft, \App\Enums\ProposalStatus::Revision, \App\Enums\ProposalStatus::FinalUpload]))
        @if(count($readinessErrors) > 0)
            <div class="alert-error mb-6">
                <div class="flex items-start gap-3">
                    <x-icon name="x-mark" class="w-6 h-6 flex-shrink-0 mt-0.5 text-red-600"/>
                    <div>
                        <h3 class="font-semibold text-red-800">Proposal belum siap diajukan</h3>
                        <p class="text-sm text-red-700 mt-1 mb-2">Lengkapi data berikut sebelum mengajukan proposal:</p>
                        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                            @foreach($readinessErrors as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5 mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <x-icon name="check" class="w-6 h-6 flex-shrink-0 text-emerald-600 mt-0.5"/>
                    <div>
                        <h3 class="font-semibold text-emerald-800">
                            @if($proposal->status === \App\Enums\ProposalStatus::Revision)
                                Revisi siap diajukan!
                            @elseif($proposal->status === \App\Enums\ProposalStatus::FinalUpload)
                                Revisi akhir siap diajukan!
                            @else
                                Proposal siap diajukan!
                            @endif
                        </h3>
                        <p class="text-sm text-emerald-700 mt-1">Seluruh data wajib telah terisi lengkap. Anda dapat mengajukan proposal ini.</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('student.proposals.submit', $proposal) }}">
                    @csrf
                    <button class="btn btn-success whitespace-nowrap px-6 py-2.5">
                        <x-icon name="arrow-up-tray" class="w-4 h-4"/>
                        @if(in_array($proposal->status, [\App\Enums\ProposalStatus::Revision, \App\Enums\ProposalStatus::FinalUpload]))
                            Ajukan Revisi
                        @else
                            Ajukan Proposal
                        @endif
                    </button>
                </form>
            </div>
        @endif
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Kolom Kiri: Navigasi Edit --}}
        <div class="md:col-span-1 space-y-2">
            @if($proposal->isEditable())
                <a href="{{ route('student.proposals.edit', $proposal) }}" class="flex items-center justify-between p-3 rounded-lg text-gray-600 hover:bg-gray-100 transition border border-transparent hover:border-gray-200">
                    <div class="flex items-center gap-3"><x-icon name="document-text" class="w-5 h-5"/> Data Utama</div>
                    <x-icon name="pencil" class="w-4 h-4"/>
                </a>
                <a href="{{ route('student.proposals.members.index', $proposal) }}" class="flex items-center justify-between p-3 rounded-lg text-gray-600 hover:bg-gray-100 transition border border-transparent hover:border-gray-200">
                    <div class="flex items-center gap-3"><x-icon name="users" class="w-5 h-5"/> Anggota Tim</div>
                    <x-icon name="pencil" class="w-4 h-4"/>
                </a>
                @if($proposal->scheme->is_funded)
                <a href="{{ route('student.proposals.funding.index', $proposal) }}" class="flex items-center justify-between p-3 rounded-lg text-gray-600 hover:bg-gray-100 transition border border-transparent hover:border-gray-200">
                    <div class="flex items-center gap-3"><x-icon name="chart-bar" class="w-5 h-5"/> Pendanaan</div>
                    <x-icon name="pencil" class="w-4 h-4"/>
                </a>
                @endif
                <a href="{{ route('student.proposals.files.index', $proposal) }}" class="flex items-center justify-between p-3 rounded-lg text-gray-600 hover:bg-gray-100 transition border border-transparent hover:border-gray-200">
                    <div class="flex items-center gap-3"><x-icon name="arrow-up-tray" class="w-5 h-5"/> Unggah Dokumen</div>
                    <x-icon name="pencil" class="w-4 h-4"/>
                </a>
            @else
                <div class="p-4 bg-gray-50 border border-gray-100 rounded-xl text-sm text-gray-500 text-center">
                    <x-icon name="lock-closed" class="w-6 h-6 mx-auto mb-2 text-gray-400"/>
                    Proposal saat ini berada pada tahap <strong>{{ $proposal->status->label() }}</strong> dan tidak dapat diubah oleh mahasiswa.
                </div>
            @endif
        </div>

        {{-- Kolom Kanan: Ringkasan --}}
        <div class="md:col-span-2 space-y-6">

            {{-- Info Kelulusan Semifinal --}}
            @if(in_array($proposal->status, [\App\Enums\ProposalStatus::NotPassed, \App\Enums\ProposalStatus::UniversityAssignment]) && $proposal->semifinalResult)
            <div class="card {{ $proposal->status === \App\Enums\ProposalStatus::NotPassed ? 'bg-red-50 border-red-200' : 'bg-emerald-50 border-emerald-200' }} border">
                <div class="card-header border-b {{ $proposal->status === \App\Enums\ProposalStatus::NotPassed ? 'border-red-100 bg-red-100/50' : 'border-emerald-100 bg-emerald-100/50' }} flex items-center gap-2">
                    @if($proposal->status === \App\Enums\ProposalStatus::NotPassed)
                        <x-icon name="x-circle" class="w-6 h-6 text-red-600"/>
                        <h3 class="card-title text-red-900 font-bold">Proposal Tidak Lolos</h3>
                    @else
                        <x-icon name="check-circle" class="w-6 h-6 text-emerald-600"/>
                        <h3 class="card-title text-emerald-900 font-bold">Selamat! Proposal Lolos Semifinal</h3>
                    @endif
                </div>
                <div class="p-6 space-y-4">
                    @if($proposal->status === \App\Enums\ProposalStatus::NotPassed)
                        <p class="text-sm text-red-800">Mohon maaf, berdasarkan hasil evaluasi final, proposal Anda belum dapat dilanjutkan ke tahap berikutnya. Jangan patah semangat dan coba lagi di kesempatan selanjutnya!</p>
                        @if($proposal->semifinalResult->note)
                            <div class="bg-white p-4 rounded border border-red-100 mt-4">
                                <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Catatan Evaluasi</h4>
                                <div class="text-sm text-gray-700">
                                    {{ $proposal->semifinalResult->note }}
                                </div>
                            </div>
                        @endif
                    @else
                        <p class="text-sm text-emerald-800">Berdasarkan hasil evaluasi final, proposal Anda dinyatakan layak untuk dilanjutkan ke tahap pembimbingan universitas. Pantau terus status proposal Anda untuk informasi penugasan pembimbing universitas.</p>
                        @if($proposal->semifinalResult->note)
                            <div class="bg-white p-4 rounded border border-emerald-100 mt-4">
                                <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Catatan Evaluasi</h4>
                                <div class="text-sm text-gray-700">
                                    {{ $proposal->semifinalResult->note }}
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            @endif

            {{-- Info Keputusan Final --}}
            @if(in_array($proposal->status, [\App\Enums\ProposalStatus::InternalPassed, \App\Enums\ProposalStatus::InternalNotPassed]))
            <div class="card {{ $proposal->status === \App\Enums\ProposalStatus::InternalNotPassed ? 'bg-red-50 border-red-200' : 'bg-emerald-50 border-emerald-200' }} border">
                <div class="card-header border-b {{ $proposal->status === \App\Enums\ProposalStatus::InternalNotPassed ? 'border-red-100 bg-red-100/50' : 'border-emerald-100 bg-emerald-100/50' }} flex items-center gap-2">
                    @if($proposal->status === \App\Enums\ProposalStatus::InternalNotPassed)
                        <x-icon name="x-circle" class="w-6 h-6 text-red-600"/>
                        <h3 class="card-title text-red-900 font-bold">Proposal Tidak Lolos Pendanaan</h3>
                    @else
                        <x-icon name="check-circle" class="w-6 h-6 text-emerald-600"/>
                        <h3 class="card-title text-emerald-900 font-bold">Selamat! Proposal Didanai (Internal PT)</h3>
                    @endif
                </div>
                <div class="p-6 space-y-4">
                    @if($proposal->status === \App\Enums\ProposalStatus::InternalNotPassed)
                        <p class="text-sm text-red-800">Mohon maaf, berdasarkan hasil evaluasi akhir, proposal Anda belum terpilih untuk didanai. Jangan patah semangat dan coba lagi di tahun depan!</p>
                    @else
                        <p class="text-sm text-emerald-800">Berdasarkan Keputusan Pimpinan PT, proposal Anda dinyatakan layak dan akan didanai! Harap persiapkan diri Anda dan pantau informasi terkait pencairan dana, pendampingan lanjutan, serta persiapan menuju seleksi Belmawa dan PIMNAS.</p>
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

            {{-- Info Revisi --}}
            @if($proposal->status === \App\Enums\ProposalStatus::Revision && $proposal->currentRevision)
            <div class="card bg-amber-50 border border-amber-200">
                <div class="card-header border-b border-amber-100 bg-amber-100/50 flex justify-between items-center">
                    <h3 class="card-title text-amber-900 flex items-center gap-2">
                        <x-icon name="exclamation-triangle" class="w-5 h-5"/>
                        Catatan Revisi
                    </h3>
                    <span class="text-xs font-bold text-amber-700">Tenggat: {{ $proposal->currentRevision->due_at?->format('d/m/Y H:i') ?? '-' }}</span>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-sm text-amber-800">Proposal Anda telah direview dan memerlukan revisi sesuai dengan catatan berikut. Silakan lengkapi kekurangan atau ubah file sesuai arahan, lalu ajukan kembali sebelum batas waktu.</p>
                    
                    @if($proposal->currentRevision->admin_notes)
                        <div class="bg-white p-4 rounded border border-amber-100">
                            <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Kekurangan Administratif</h4>
                            <div class="prose prose-sm prose-amber max-w-none text-gray-700">
                                {!! Str::markdown($proposal->currentRevision->admin_notes) !!}
                            </div>
                        </div>
                    @endif

                    @if($proposal->currentRevision->notes)
                        <div class="bg-white p-4 rounded border border-amber-100">
                            <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Catatan Substantif</h4>
                            <div class="prose prose-sm prose-amber max-w-none text-gray-700">
                                {!! Str::markdown($proposal->currentRevision->notes) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Data Utama --}}
            <div class="card">
                <div class="card-header border-b border-gray-100">
                    <h3 class="card-title">Data Utama</h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dosen Pendamping</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $proposal->supervisor?->name ?? '-' }}</dd>
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
                            <dt class="text-sm font-medium text-gray-500">Biaya Administrasi</dt>
                            <dd class="mt-1 text-sm text-gray-900">Rp{{ number_format($proposal->admin_cost_amount, 0, ',', '.') }} ({{ $proposal->adminCostPercent() }}%)</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Anggota Tim --}}
            <div class="card">
                <div class="card-header border-b border-gray-100 flex items-center justify-between">
                    <h3 class="card-title">Anggota Tim</h3>
                </div>
                <div class="p-0">
                    <table class="table w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="px-6 py-4"><span class="badge-indigo">Ketua Tim</span></td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $proposal->leader->name }} ({{ $proposal->leader->nim ?? '-' }})</td>
                            </tr>
                            @forelse($proposal->members as $member)
                            <tr>
                                <td class="px-6 py-4"><span class="badge-gray">{{ $member->role }}</span></td>
                                <td class="px-6 py-4 text-gray-700">{{ $member->user->name }} ({{ $member->user->nim ?? '-' }})</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-6 py-6 text-center text-gray-500 italic">Belum ada anggota tambahan yang didaftarkan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pendanaan --}}
            @if($proposal->scheme->is_funded)
            <div class="card">
                <div class="card-header border-b border-gray-100">
                    <h3 class="card-title">Rencana Anggaran (Total: Rp{{ number_format($proposal->totalFunding(), 0, ',', '.') }})</h3>
                </div>
                <div class="p-0">
                    <table class="table w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            @foreach($proposal->fundings as $funding)
                            <tr>
                                <td class="px-6 py-4 text-gray-600">{{ $funding->source->label() }}</td>
                                <td class="px-6 py-4 font-medium text-right text-gray-900">Rp{{ number_format($funding->amount, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                            @if($proposal->fundings->isEmpty())
                            <tr>
                                <td colspan="2" class="px-6 py-6 text-center text-gray-500 italic">Data pendanaan belum diisi.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Berkas --}}
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

        </div>
    </div>
</x-app-layout>
