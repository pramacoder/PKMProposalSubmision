<x-app-layout title="Rencana Anggaran">
    <x-slot name="breadcrumb">
        <a href="{{ route('student.proposals.index') }}" class="hover:text-gray-600">Proposal Saya</a>
        <span>/</span>
        <a href="{{ route('student.proposals.show', $proposal) }}" class="hover:text-gray-600">{{ Str::limit($proposal->title, 30) }}</a>
        <span>/</span><span>Anggaran</span>
    </x-slot>

    <div class="max-w-4xl grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Side Menu Placeholder --}}
        <div class="md:col-span-1 space-y-2">
            <a href="{{ route('student.proposals.edit', $proposal) }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                <x-icon name="document-text" class="w-5 h-5"/> Data Utama
            </a>
            <a href="{{ route('student.proposals.members.index', $proposal) }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                <x-icon name="users" class="w-5 h-5"/> Anggota Tim
            </a>
            <a href="{{ route('student.proposals.funding.index', $proposal) }}" class="flex items-center gap-3 p-3 rounded-lg bg-blue-50 text-blue-700 font-medium">
                <x-icon name="chart-bar" class="w-5 h-5"/> Rencana Anggaran
            </a>
            <a href="{{ route('student.proposals.files.index', $proposal) }}" class="flex items-center gap-3 p-3 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                <x-icon name="arrow-up-tray" class="w-5 h-5"/> Unggah Berkas
            </a>
        </div>

        {{-- Main Area --}}
        <div class="md:col-span-2">
            <div class="card">
                <div class="card-header border-b border-gray-100">
                    <div>
                        <h2 class="card-title">Rencana Pendanaan</h2>
                        <p class="text-sm text-gray-500 mt-1">Total saat ini: <strong>Rp{{ number_format($proposal->totalFunding(), 0, ',', '.') }}</strong></p>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('student.proposals.funding.store', $proposal) }}" class="card-body space-y-5">
                    @csrf
                    
                    @php
                        $belmawa = $proposal->fundings->firstWhere('source', \App\Enums\FundingSource::Belmawa);
                        $university = $proposal->fundings->firstWhere('source', \App\Enums\FundingSource::University);
                        $partner = $proposal->fundings->firstWhere('source', \App\Enums\FundingSource::Partner);
                    @endphp

                    {{-- Belmawa --}}
                    <div class="p-4 rounded-xl border border-gray-200 bg-gray-50">
                        <label for="amount_belmawa" class="form-label text-base font-semibold text-gray-800">Dana Belmawa (Kemdikbudristek) <span class="text-red-500">*</span></label>
                        <p class="text-xs text-gray-500 mb-2">Batas: Rp{{ number_format($setting->min_belmawa, 0, ',', '.') }} s.d. Rp{{ number_format($setting->max_belmawa, 0, ',', '.') }}</p>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-medium">Rp</span>
                            <input type="number" id="amount_belmawa" name="amounts[belmawa]" 
                                   value="{{ old('amounts.belmawa', $belmawa?->amount) }}"
                                   class="form-input pl-9 font-mono" required 
                                   min="{{ $setting->min_belmawa }}" max="{{ $setting->max_belmawa }}" {{ !$proposal->isEditable() ? 'disabled' : '' }}>
                        </div>
                    </div>

                    {{-- Perguruan Tinggi --}}
                    <div class="p-4 rounded-xl border border-gray-200 bg-gray-50">
                        <label for="amount_pt" class="form-label text-base font-semibold text-gray-800">Dana Perguruan Tinggi (Wajib)</label>
                        <p class="text-xs text-gray-500 mb-2">Batas: Rp{{ number_format($setting->min_pt, 0, ',', '.') }} s.d. Rp{{ number_format($setting->max_pt, 0, ',', '.') }}</p>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-medium">Rp</span>
                            <input type="number" id="amount_pt" name="amounts[university]" 
                                   value="{{ old('amounts.university', $university?->amount) }}"
                                   class="form-input pl-9 font-mono" 
                                   min="{{ $setting->min_pt }}" max="{{ $setting->max_pt }}" {{ !$proposal->isEditable() ? 'disabled' : '' }}>
                        </div>
                    </div>

                    {{-- Mitra --}}
                    <div class="p-4 rounded-xl border border-gray-200 bg-gray-50">
                        <label for="amount_partner" class="form-label text-base font-semibold text-gray-800">Dana Mitra/Sponsor (Opsional)</label>
                        <p class="text-xs text-gray-500 mb-2">Batas maksimal: Rp{{ number_format($setting->max_partner, 0, ',', '.') }}</p>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-medium">Rp</span>
                            <input type="number" id="amount_partner" name="amounts[partner]" 
                                   value="{{ old('amounts.partner', $partner?->amount) }}"
                                   class="form-input pl-9 font-mono" 
                                   min="0" max="{{ $setting->max_partner }}" {{ !$proposal->isEditable() ? 'disabled' : '' }}>
                        </div>
                    </div>

                    @if($proposal->isEditable())
                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="btn-primary btn">
                            <x-icon name="check" class="w-4 h-4"/> Simpan Anggaran
                        </button>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
