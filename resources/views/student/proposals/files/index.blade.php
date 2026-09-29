<x-app-layout title="Unggah Dokumen">
    <x-slot name="breadcrumb">
        <a href="{{ route('student.proposals.index') }}" class="hover:text-gray-600">Proposal Saya</a>
        <span>/</span>
        <a href="{{ route('student.proposals.show', $proposal) }}" class="hover:text-gray-600">{{ Str::limit($proposal->title, 30) }}</a>
        <span>/</span><span>Unggah Dokumen</span>
    </x-slot>

    <div class="max-w-5xl grid grid-cols-1 md:grid-cols-4 gap-6">
        
        {{-- Side Menu --}}
        <div class="md:col-span-1 space-y-2">
            <a href="{{ route('student.proposals.edit', $proposal) }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                <x-icon name="document-text" class="w-5 h-5"/> Data Utama
            </a>
            <a href="{{ route('student.proposals.members.index', $proposal) }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                <x-icon name="users" class="w-5 h-5"/> Anggota Tim
            </a>
            @if($proposal->scheme->is_funded)
            <a href="{{ route('student.proposals.funding.index', $proposal) }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                <x-icon name="chart-bar" class="w-5 h-5"/> Rencana Anggaran
            </a>
            @endif
            <a href="{{ route('student.proposals.files.index', $proposal) }}" class="flex items-center gap-3 p-3 rounded-lg bg-blue-50 text-blue-700 font-medium">
                <x-icon name="arrow-up-tray" class="w-5 h-5"/> Unggah Dokumen
            </a>
        </div>

        {{-- Main Area --}}
        <div class="md:col-span-3 space-y-6">
            <div class="card">
                <div class="card-header border-b border-gray-100">
                    <h2 class="card-title">Daftar Dokumen Wajib</h2>
                    <p class="text-sm text-gray-500 mt-1">Unggah dokumen sesuai dengan panduan skema {{ $proposal->scheme->code }}.</p>
                </div>
                
                <div class="p-6 space-y-8">
                    @foreach($requirements as $req)
                        @php
                            $currentFile = $files->get($req->id);
                        @endphp
                        
                        <div class="flex flex-col sm:flex-row gap-6 p-5 border {{ $currentFile ? 'border-emerald-200 bg-emerald-50/30' : 'border-gray-200 bg-gray-50' }} rounded-xl">
                            
                            {{-- Info Syarat --}}
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="font-semibold text-gray-900">{{ $req->label }}</h3>
                                    @if($req->is_required)
                                        <span class="text-xs font-medium px-2 py-0.5 rounded bg-red-100 text-red-700">Wajib</span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500 mb-3 space-y-1">
                                    <p>Format diizinkan: <strong>{{ strtoupper($req->allowed_mimes) }}</strong></p>
                                    <p>Ukuran maksimal: <strong>{{ number_format($req->max_kb / 1024, 2) }} MB</strong></p>
                                </div>
                                
                                @if($currentFile)
                                    <div class="mt-4 flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200">
                                        <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded flex items-center justify-center">
                                            <x-icon name="document-check" class="w-6 h-6"/>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $currentFile->original_name }}</p>
                                            <p class="text-xs text-gray-500">Diunggah pada {{ $currentFile->created_at->format('d M Y, H:i') }} (Tahap: {{ $currentFile->stage->label() }})</p>
                                        </div>
                                        <a href="{{ route('student.proposals.files.download', [$proposal, $currentFile]) }}" class="btn-secondary btn px-3 py-1.5 text-sm" target="_blank">
                                            <x-icon name="arrow-down-tray" class="w-4 h-4"/> Unduh
                                        </a>
                                    </div>
                                @else
                                    <div class="mt-4 p-3 border border-dashed border-gray-300 rounded-lg text-center bg-white">
                                        <p class="text-sm text-gray-500">Belum ada file diunggah.</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Form Upload (Jika masih bisa diubah) --}}
                            @if($proposal->isEditable())
                                <div class="sm:w-72 flex-shrink-0 flex flex-col justify-center">
                                    <form action="{{ route('student.proposals.files.store', $proposal) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="requirement_id" value="{{ $req->id }}">
                                        
                                        <div>
                                            <input type="file" name="file" id="file_{{ $req->id }}" class="block w-full text-sm text-gray-500
                                                file:mr-4 file:py-2 file:px-4
                                                file:rounded-full file:border-0
                                                file:text-sm file:font-semibold
                                                file:bg-indigo-50 file:text-indigo-700
                                                hover:file:bg-indigo-100 cursor-pointer" accept=".{{ str_replace(',', ',.', $req->allowed_mimes) }}" required>
                                        </div>
                                        
                                        <button type="submit" class="btn-primary btn w-full justify-center">
                                            <x-icon name="cloud-arrow-up" class="w-4 h-4"/> {{ $currentFile ? 'Timpa dengan Baru' : 'Unggah File' }}
                                        </button>
                                    </form>
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
