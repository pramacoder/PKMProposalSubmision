<x-app-layout title="Proposal Saya">
    <div class="flex items-center justify-between mb-6">
        <p class="text-sm text-gray-500">Menampilkan proposal yang Anda ketuai.</p>
        <a href="{{ route('student.proposals.create') }}" class="btn-primary btn">
            <x-icon name="plus" class="w-4 h-4"/> Buat Proposal Baru
        </a>
    </div>

    <div class="space-y-4">
        @forelse($proposals as $proposal)
            <div class="card hover:shadow-md transition">
                <div class="card-body flex items-start gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="badge-indigo">{{ $proposal->scheme->code }}</span>
                            <span class="badge-{{ $proposal->status->color() }}">{{ $proposal->status->label() }}</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">
                            {{ $proposal->title ?: 'Judul belum diisi' }}
                        </h3>
                        <div class="text-sm text-gray-500 mt-2 flex items-center gap-4">
                            <span>Siklus: {{ $proposal->cycle->name }}</span>
                            <span>•</span>
                            <span>Diperbarui: {{ $proposal->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 min-w-[120px]">
                        @if($proposal->isEditable())
                            <a href="{{ route('student.proposals.edit', $proposal) }}" class="btn-secondary btn w-full justify-center">
                                <x-icon name="pencil" class="w-4 h-4"/> Edit
                            </a>
                        @endif
                        <a href="{{ route('student.proposals.show', $proposal) }}" class="btn-primary btn w-full justify-center">
                            <x-icon name="eye" class="w-4 h-4"/> Detail
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="document-text" class="w-8 h-8 text-gray-400"/>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada proposal</h3>
                <p class="text-gray-500 mb-6">Anda belum membuat draf proposal pada siklus aktif.</p>
                <a href="{{ route('student.proposals.create') }}" class="btn-primary btn mx-auto w-fit">Buat Draf Pertama</a>
            </div>
        @endforelse
    </div>
</x-app-layout>
