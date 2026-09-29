<x-app-layout title="Kelola Anggota Tim">
    <x-slot name="breadcrumb">
        <a href="{{ route('student.proposals.index') }}" class="hover:text-gray-600">Proposal Saya</a>
        <span>/</span>
        <a href="{{ route('student.proposals.show', $proposal) }}" class="hover:text-gray-600">{{ Str::limit($proposal->title, 30) }}</a>
        <span>/</span><span>Anggota Tim</span>
    </x-slot>

    <div class="max-w-4xl grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Side Menu Placeholder --}}
        <div class="md:col-span-1 space-y-2">
            <a href="{{ route('student.proposals.edit', $proposal) }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                <x-icon name="document-text" class="w-5 h-5"/> Data Utama
            </a>
            <a href="{{ route('student.proposals.members.index', $proposal) }}" class="flex items-center gap-3 p-3 rounded-lg bg-blue-50 text-blue-700 font-medium">
                <x-icon name="users" class="w-5 h-5"/> Anggota Tim
            </a>
            @if($proposal->scheme->is_funded)
            <a href="{{ route('student.proposals.funding.index', $proposal) }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                <x-icon name="chart-bar" class="w-5 h-5"/> Rencana Anggaran
            </a>
            @endif
            <a href="{{ route('student.proposals.files.index', $proposal) }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                <x-icon name="arrow-up-tray" class="w-5 h-5"/> Unggah Berkas
            </a>
        </div>

        {{-- Main Area --}}
        <div class="md:col-span-2 space-y-6">
            {{-- List Anggota --}}
            <div class="card">
                <div class="card-header border-b border-gray-100">
                    <h2 class="card-title">Daftar Anggota Tim</h2>
                </div>
                
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Peran</th>
                                <th>Nama / NIM</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            {{-- Ketua (Selalu Tampil) --}}
                            <tr>
                                <td><span class="badge-indigo">Ketua Tim</span></td>
                                <td>
                                    <div class="font-medium text-gray-900">{{ $proposal->leader->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $proposal->leader->nim ?? '-' }}</div>
                                </td>
                                <td class="text-right">
                                    <span class="text-xs text-gray-400 italic">Tetap</span>
                                </td>
                            </tr>

                            {{-- Anggota --}}
                            @forelse($proposal->members as $member)
                            <tr>
                                <td><span class="badge-gray">{{ $member->role }}</span></td>
                                <td>
                                    <div class="font-medium text-gray-900">{{ $member->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $member->user->nim ?? '-' }}</div>
                                </td>
                                <td class="text-right">
                                    @if($proposal->isEditable())
                                    <form method="POST" action="{{ route('student.proposals.members.destroy', [$proposal, $member]) }}" onsubmit="return confirm('Hapus anggota ini dari tim?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-2">
                                            <x-icon name="trash" class="w-4 h-4"/>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-gray-400 py-6 text-sm">Belum ada anggota tambahan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Form Tambah Anggota --}}
            @if($proposal->isEditable())
            <div class="card">
                <div class="card-header border-b border-gray-100">
                    <h3 class="card-title">Tambah Anggota</h3>
                </div>
                <form method="POST" action="{{ route('student.proposals.members.store', $proposal) }}" class="card-body">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="role" class="form-label">Peran dalam Tim <span class="text-red-500">*</span></label>
                            <select id="role" name="role" class="form-input @error('role') form-input-error @enderror" required>
                                <option value="Anggota 1">Anggota 1</option>
                                <option value="Anggota 2">Anggota 2</option>
                                <option value="Anggota 3">Anggota 3</option>
                                <option value="Anggota 4">Anggota 4</option>
                                <option value="Anggota 5">Anggota 5</option>
                            </select>
                            @error('role') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="user_id" class="form-label">Pilih Mahasiswa <span class="text-red-500">*</span></label>
                            {{-- TODO: Di aplikasi nyata, gunakan Select2 atau AJAX Search, karena jumlah mhs ribuan. --}}
                            {{-- Sebagai POC, kita pakai select sederhana dengan mhs dummy. --}}
                            <select id="user_id" name="user_id" class="form-input @error('user_id') form-input-error @enderror" required>
                                <option value="">-- Cari Nama/NIM --</option>
                                @foreach(\App\Models\User::whereHas('roleRecords', fn($q) => $q->where('role', 'student'))->where('id', '!=', auth()->id())->orderBy('name')->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->nim }} - {{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('user_id') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn-primary btn">
                            <x-icon name="plus" class="w-4 h-4"/> Tambah Anggota
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
