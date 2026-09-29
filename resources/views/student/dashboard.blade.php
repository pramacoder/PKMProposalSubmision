<x-app-layout title="Dashboard Mahasiswa">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
        <div class="stat-card">
            <span class="stat-label">Proposal Saya</span>
            <span class="stat-value">{{ auth()->user()->ledProposals()->count() }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Status</span>
            <span class="stat-value text-lg">{{ auth()->user()->ledProposals()->latest()->first()?->status?->label() ?? '—' }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Siklus Aktif</span>
            <span class="stat-value text-lg">{{ \App\Models\Cycle::active()->first()?->name ?? '—' }}</span>
        </div>
    </div>

    <div class="card">
        <div class="card-body text-center py-16">
            <div class="w-16 h-16 rounded-2xl bg-blue-100 flex items-center justify-center mx-auto mb-4">
                <x-icon name="document-text" class="w-8 h-8 text-blue-600"/>
            </div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Selamat datang, {{ auth()->user()->name }}</h2>
            <p class="text-gray-500 text-sm mb-6">Fitur pengajuan proposal akan tersedia di Fase 3. Pantau status akun Anda melalui halaman profil.</p>
            <a href="{{ route('profile.edit') }}" class="btn-primary btn">
                <x-icon name="cog" class="w-4 h-4"/> Lengkapi Profil
            </a>
        </div>
    </div>
</x-app-layout>
