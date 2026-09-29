<x-app-layout title="Edit Form Checklist">
    <x-slot name="breadcrumb">
        <a href="{{ route('operator.checklist.index') }}" class="hover:text-gray-600">Form Administratif</a>
        <span>/</span>
        <a href="{{ route('operator.checklist.show', $checklistForm) }}" class="hover:text-gray-600">Detail</a>
        <span>/</span>
        <span>Edit</span>
    </x-slot>

    <div class="max-w-3xl" x-data="checklistEditor(@js($checklistForm->items->toArray()))">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Edit Item: {{ $checklistForm->checklist_group->value }}</h2>
                <span class="badge-amber">Draft</span>
            </div>
            <form method="POST" action="{{ route('operator.checklist.update', $checklistForm) }}" class="card-body">
                @csrf @method('PATCH')

                <div class="space-y-3 mb-5">
                    <template x-for="(item, index) in items" :key="item._key">
                        <div class="p-4 rounded-xl border border-gray-200 bg-gray-50 flex gap-3 items-start">
                            <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <input type="hidden" :name="`items[${index}][id]`" :value="item.id">
                                <div>
                                    <label class="form-label text-xs">Kode</label>
                                    <input type="text" :name="`items[${index}][code]`" x-model="item.code"
                                           class="form-input" placeholder="ADM-01" required>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="form-label text-xs">Label</label>
                                    <input type="text" :name="`items[${index}][label]`" x-model="item.label"
                                           class="form-input" placeholder="Deskripsi item checklist" required>
                                </div>
                                <div>
                                    <label class="form-label text-xs">Jenis</label>
                                    <select :name="`items[${index}][kind]`" x-model="item.kind" class="form-input">
                                        <option value="auto">Otomatis</option>
                                        <option value="manual">Manual</option>
                                    </select>
                                </div>
                            </div>
                            <button type="button" @click="removeItem(index)"
                                    class="mt-6 text-red-400 hover:text-red-600 transition flex-shrink-0">
                                <x-icon name="trash" class="w-4 h-4"/>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <button type="button" @click="addItem()" class="btn-secondary btn btn-sm">
                        <x-icon name="plus" class="w-4 h-4"/> Tambah Item
                    </button>
                    <div class="flex gap-2">
                        <a href="{{ route('operator.checklist.show', $checklistForm) }}" class="btn-secondary btn">Batal</a>
                        <button type="submit" class="btn-primary btn">
                            <x-icon name="check" class="w-4 h-4"/> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function checklistEditor(initialItems) {
        return {
            items: initialItems.map(i => ({ ...i, _key: i.id ?? Math.random() })),
            addItem() {
                this.items.push({ id: null, code: '', label: '', kind: 'manual', _key: Math.random() });
            },
            removeItem(index) {
                if (this.items.length <= 1) return;
                this.items.splice(index, 1);
            }
        };
    }
    </script>
    @endpush
</x-app-layout>
