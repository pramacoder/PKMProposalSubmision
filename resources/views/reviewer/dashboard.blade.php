<x-app-layout title="Dashboard Reviewer">
    <div class="card">
        <div class="card-body text-center py-16">
            <div class="w-16 h-16 rounded-2xl bg-amber-100 flex items-center justify-center mx-auto mb-4">
                <x-icon name="star" class="w-8 h-8 text-amber-600"/>
            </div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Halo, {{ auth()->user()->name }}</h2>
            <p class="text-gray-500 text-sm">Fitur review proposal akan tersedia di Fase 4.</p>
        </div>
    </div>
</x-app-layout>
