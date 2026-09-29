<x-app-layout title="Dashboard Dosen Universitas">
    <div class="card">
        <div class="card-body text-center py-16">
            <div class="w-16 h-16 rounded-2xl bg-teal-100 flex items-center justify-center mx-auto mb-4">
                <x-icon name="shield-check" class="w-8 h-8 text-teal-600"/>
            </div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Halo, {{ auth()->user()->name }}</h2>
            <p class="text-gray-500 text-sm">Fitur validasi akhir proposal akan tersedia di Fase 6.</p>
        </div>
    </div>
</x-app-layout>
