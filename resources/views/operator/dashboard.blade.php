<x-app-layout title="Dashboard Operator">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
        <div class="stat-card">
            <span class="stat-label">Total Proposal</span>
            <span class="stat-value">{{ \App\Models\Proposal::count() }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Siklus Aktif</span>
            <span class="stat-value">{{ \App\Models\Cycle::active()->first()?->name ?? '—' }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-label">Penugasan Reviewer</span>
            <span class="stat-value">{{ \App\Models\ReviewerAssignment::count() }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <a href="{{ route('operator.control.index') }}"
           class="card p-6 hover:shadow-md transition group">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center mb-3 group-hover:bg-blue-200 transition">
                <x-icon name="adjustments-horizontal" class="w-5 h-5 text-blue-700"/>
            </div>
            <h3 class="font-semibold text-gray-800">Ruang Kontrol</h3>
            <p class="text-sm text-gray-500 mt-1">Kelola jadwal fase dan kuota skema</p>
        </a>
        <a href="{{ route('operator.checklist.index') }}"
           class="card p-6 hover:shadow-md transition group">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center mb-3 group-hover:bg-emerald-200 transition">
                <x-icon name="clipboard-document-check" class="w-5 h-5 text-emerald-700"/>
            </div>
            <h3 class="font-semibold text-gray-800">Form Administratif</h3>
            <p class="text-sm text-gray-500 mt-1">Checklist kelengkapan dokumen</p>
        </a>
        <a href="{{ route('operator.rubric.index') }}"
           class="card p-6 hover:shadow-md transition group">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center mb-3 group-hover:bg-amber-200 transition">
                <x-icon name="chart-bar" class="w-5 h-5 text-amber-700"/>
            </div>
            <h3 class="font-semibold text-gray-800">Rubrik Penilaian</h3>
            <p class="text-sm text-gray-500 mt-1">Kriteria dan bobot substantif</p>
        </a>
    </div>
</x-app-layout>
