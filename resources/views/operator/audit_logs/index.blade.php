<x-app-layout title="Log Sistem">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 leading-tight">Log Sistem (Audit Trail)</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar riwayat perubahan status proposal secara menyeluruh.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="card p-6 border-b border-gray-100 bg-white">
            <h3 class="text-sm font-bold text-gray-500 uppercase">Antrean Email (Jobs)</h3>
            <div class="mt-2 text-2xl font-bold text-gray-900">{{ $pendingJobs }}</div>
            <p class="text-xs text-gray-500 mt-1">Email yang sedang diproses di background</p>
        </div>
        <div class="card p-6 border-b border-gray-100 bg-white">
            <h3 class="text-sm font-bold text-gray-500 uppercase">Antrean Gagal (Failed Jobs)</h3>
            <div class="mt-2 text-2xl font-bold {{ $failedJobs > 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ $failedJobs }}</div>
            <p class="text-xs text-gray-500 mt-1">Gagal mengirim email atau memproses pekerjaan</p>
        </div>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Aktor</th>
                        <th>Proposal</th>
                        <th>Transisi Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="whitespace-nowrap text-sm text-gray-500">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td>
                            <div class="font-medium text-gray-900">{{ $log->actor->name }}</div>
                            <div class="text-xs text-gray-500">{{ $log->actor->primaryRole()->label() }}</div>
                        </td>
                        <td class="max-w-xs">
                            <p class="font-medium text-gray-900 line-clamp-2" title="{{ $log->proposal->title }}">{{ $log->proposal->title }}</p>
                        </td>
                        <td class="whitespace-nowrap">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="text-gray-500">Dari:</span>
                                <span class="badge-gray">{{ \App\Enums\ProposalStatus::tryFrom($log->from_status)?->label() ?? $log->from_status }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm mt-1">
                                <span class="text-gray-500">Ke:</span>
                                <span class="badge-indigo">{{ \App\Enums\ProposalStatus::tryFrom($log->to_status)?->label() ?? $log->to_status }}</span>
                            </div>
                        </td>
                        <td class="max-w-xs">
                            <span class="text-sm text-gray-600 line-clamp-3" title="{{ $log->note }}">{{ $log->note ?: '-' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">Belum ada riwayat aktivitas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
