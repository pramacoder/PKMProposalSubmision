<x-app-layout title="Status PIMNAS">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 leading-tight">Status PIMNAS & Belmawa</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola dan perbarui status pencapaian proposal yang telah lolos evaluasi internal (didanai PT).</p>
        </div>
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Proposal</th>
                        <th>Status Belmawa</th>
                        <th>Status PIMNAS</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($proposals as $proposal)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="max-w-xs">
                            <p class="font-medium text-gray-900 line-clamp-2" title="{{ $proposal->title }}">{{ $proposal->title }}</p>
                            <p class="text-xs text-gray-500 mt-1">Ketua: {{ $proposal->leader->name }} | Skema: {{ $proposal->scheme->code }}</p>
                        </td>
                        
                        <form method="POST" action="{{ route('operator.pimnas.update', $proposal) }}">
                            @csrf
                            @method('PATCH')
                            <td>
                                <select name="belmawa_result" class="form-input text-sm py-1.5 h-auto">
                                    <option value="" {{ is_null($proposal->belmawa_result) ? 'selected' : '' }}>-- Belum Diketahui --</option>
                                    <option value="Lolos Didanai" {{ $proposal->belmawa_result === 'Lolos Didanai' ? 'selected' : '' }}>Lolos Didanai</option>
                                    <option value="Tidak Lolos" {{ $proposal->belmawa_result === 'Tidak Lolos' ? 'selected' : '' }}>Tidak Lolos</option>
                                </select>
                            </td>
                            <td>
                                <select name="pimnas_status" class="form-input text-sm py-1.5 h-auto">
                                    <option value="" {{ is_null($proposal->pimnas_status) ? 'selected' : '' }}>-- Belum Ada --</option>
                                    <option value="Peserta PIMNAS" {{ $proposal->pimnas_status === 'Peserta PIMNAS' ? 'selected' : '' }}>Peserta PIMNAS</option>
                                    <option value="Medali Emas (Presentasi)" {{ $proposal->pimnas_status === 'Medali Emas (Presentasi)' ? 'selected' : '' }}>Medali Emas (Presentasi)</option>
                                    <option value="Medali Perak (Presentasi)" {{ $proposal->pimnas_status === 'Medali Perak (Presentasi)' ? 'selected' : '' }}>Medali Perak (Presentasi)</option>
                                    <option value="Medali Perunggu (Presentasi)" {{ $proposal->pimnas_status === 'Medali Perunggu (Presentasi)' ? 'selected' : '' }}>Medali Perunggu (Presentasi)</option>
                                    <option value="Medali Emas (Poster)" {{ $proposal->pimnas_status === 'Medali Emas (Poster)' ? 'selected' : '' }}>Medali Emas (Poster)</option>
                                    <option value="Medali Perak (Poster)" {{ $proposal->pimnas_status === 'Medali Perak (Poster)' ? 'selected' : '' }}>Medali Perak (Poster)</option>
                                    <option value="Medali Perunggu (Poster)" {{ $proposal->pimnas_status === 'Medali Perunggu (Poster)' ? 'selected' : '' }}>Medali Perunggu (Poster)</option>
                                </select>
                            </td>
                            <td class="text-right">
                                <button type="submit" class="btn-primary btn px-3 py-1.5 text-xs whitespace-nowrap">Simpan</button>
                            </td>
                        </form>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500 italic">Belum ada proposal yang lolos evaluasi internal.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
