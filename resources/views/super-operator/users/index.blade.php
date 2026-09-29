<x-app-layout title="Kelola Akun Pengguna">
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-500 mt-0.5">{{ $users->total() }} pengguna terdaftar</p>
        </div>
        <a href="{{ route('super-operator.users.create') }}" class="btn-primary btn">
            <x-icon name="user-plus" class="w-4 h-4"/>
            Tambah Akun
        </a>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama / Email</th>
                        <th>NIM / NIDN</th>
                        <th>Peran</th>
                        <th>Status Klaim</th>
                        <th>Aktif</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="font-medium text-gray-900">{{ $user->name }}</div>
                            <div class="text-xs text-gray-400">{{ $user->email }}</div>
                        </td>
                        <td class="text-xs text-gray-600">
                            @if($user->nim) NIM: {{ $user->nim }} @endif
                            @if($user->nidn) <br>NIDN: {{ $user->nidn }} @endif
                            @if(!$user->nim && !$user->nidn) <span class="text-gray-400">—</span> @endif
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                @foreach($user->roles() as $role)
                                    <span class="badge-indigo">{{ $role->label() }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            @if($user->isClaimed())
                                <span class="badge-green">Sudah Klaim</span>
                            @else
                                <span class="badge-amber">Belum Klaim</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('super-operator.users.toggle-active', $user) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="{{ $user->is_active ? 'badge-green' : 'badge-red' }} cursor-pointer hover:opacity-75 transition"
                                        title="{{ $user->is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('super-operator.users.edit', $user) }}"
                                   class="btn-secondary btn btn-sm">
                                    <x-icon name="pencil" class="w-3.5 h-3.5"/>
                                    Edit
                                </a>
                                @if(!$user->isClaimed())
                                <form method="POST" action="{{ route('super-operator.users.resend-claim', $user) }}">
                                    @csrf
                                    <button type="submit" class="btn-amber btn btn-sm">
                                        <x-icon name="arrow-up-tray" class="w-3.5 h-3.5"/>
                                        Kirim Klaim
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-400 py-10">
                            Belum ada pengguna. <a href="{{ route('super-operator.users.create') }}" class="text-blue-600 hover:underline">Tambah sekarang</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
