<x-mail::message>
# Halo,

Status proposal Anda yang berjudul **"{{ $proposal->title }}"** telah diperbarui.

**Status Saat Ini:** {{ $proposal->status->label() }}

@if($note)
<x-mail::panel>
**Catatan:**  
{{ $note }}
</x-mail::panel>
@endif

Silakan login ke sistem untuk melihat rincian lebih lanjut.

<x-mail::button :url="route('login')">
Lihat Proposal
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>
