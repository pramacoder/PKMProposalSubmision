<x-app-layout title="Dashboard Pimpinan PT">
    {{-- Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="stat-card">
            <span class="stat-label">Total Pengguna</span>
            <span class="stat-value">{{ \App\Models\User::count() }}</span>
            <span class="stat-sub">Semua peran</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Siklus Aktif</span>
            <span class="stat-value">{{ \App\Models\Cycle::active()->count() }}</span>
            <span class="stat-sub">{{ \App\Models\Cycle::active()->first()?->name ?? '—' }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Total Proposal</span>
            <span class="stat-value">{{ \App\Models\Proposal::count() }}</span>
            <span class="stat-sub">Siklus saat ini</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Akun Belum Klaim</span>
            <span class="stat-value">{{ \App\Models\User::unclaimed()->count() }}</span>
            <span class="stat-sub">Menunggu aktivasi</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Quick Actions --}}
        <div class="card lg:col-span-1">
            <div class="card-header">
                <h2 class="card-title">Aksi Cepat</h2>
            </div>
            <div class="card-body space-y-3">
                <a href="{{ route('super-operator.users.create') }}"
                   class="btn-primary btn w-full justify-center">
                    <x-icon name="user-plus" class="w-4 h-4"/>
                    Tambah Akun Baru
                </a>
                <a href="{{ route('super-operator.cycles.create') }}"
                   class="btn-secondary btn w-full justify-center">
                    <x-icon name="calendar" class="w-4 h-4"/>
                    Buat Siklus PKM
                </a>
                <a href="{{ route('operator.control.index') }}"
                   class="btn-secondary btn w-full justify-center">
                    <x-icon name="adjustments-horizontal" class="w-4 h-4"/>
                    Ruang Kontrol
                </a>
            </div>
        </div>

        {{-- Recent users --}}
        <div class="card lg:col-span-2">
            <div class="card-header">
                <h2 class="card-title">Pengguna Terbaru</h2>
                <a href="{{ route('super-operator.users.index') }}" class="text-sm text-blue-600 hover:underline">Lihat semua</a>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Peran</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach(\App\Models\User::with('roleRecords')->latest()->take(8)->get() as $u)
                        <tr>
                            <td>
                                <div class="font-medium text-gray-900">{{ $u->name }}</div>
                                <div class="text-xs text-gray-400">{{ $u->email }}</div>
                            </td>
                            <td>
                                @foreach($u->roles() as $r)
                                    <span class="badge-indigo mr-1">{{ $r->label() }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($u->isClaimed())
                                    <span class="badge-green">Aktif</span>
                                @else
                                    <span class="badge-amber">Belum Klaim</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
