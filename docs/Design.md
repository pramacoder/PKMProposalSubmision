# Design

Prioritas: **UX dan fungsi lebih utama dari UI.** Tampilan bersih, netral, mudah dibaca.

## Prinsip UX
- UX-01 Setiap halaman beranda peran menjawab: "Apa yang perlu saya kerjakan sekarang?" (daftar tugas berurutan berdasarkan tenggat).
- UX-02 Setiap proposal menampilkan **pelacak status** (stepper): tahap saat ini, tahap berikutnya, siapa yang sedang memegang giliran, tenggat.
- UX-03 Kesalahan dicegah lebih dulu (pra-cek, batas dana langsung terhitung), bukan hanya diberi pesan setelah submit.
- UX-04 Pesan galat menyebut apa yang salah dan cara memperbaikinya, dengan bahasa sehari-hari.
- UX-05 Formulir panjang dipecah per langkah; draf tersimpan otomatis; tombol utama satu per layar.
- UX-06 Tenggat selalu terlihat, dengan hitung mundur dan zona waktu WITA.
- UX-07 Status memakai **ikon + teks + warna**, tidak bergantung warna saja.
- UX-08 Tindakan yang tidak bisa dibatalkan (submit, keputusan) memakai konfirmasi yang menjelaskan akibatnya.
- UX-09 Kondisi kosong dan memuat informatif ("Belum ada proposal. Mulai dengan …").
- UX-10 Operator: tabel dengan filter, pencarian, dan tindakan massal; ringkasan jumlah per status di atas.
- UX-11 Reviewer: simpan progres, tandai item tak yakin, total skor terlihat saat mengisi.
- UX-12 Dapat dipakai di ponsel; dapat dioperasikan dengan keyboard; label formulir selalu terlihat.

## Token warna (draf; bisa diganti setelah memilih referensi dari Awesome Design)
| Token | Hex | Pakai untuk |
|---|---|---|
| bg | #F8FAFC | Latar halaman |
| surface | #FFFFFF | Kartu, tabel |
| text | #0F172A | Teks utama |
| muted | #475569 | Teks sekunder |
| border | #E2E8F0 | Garis pemisah |
| primary | #2563EB (hover #1D4ED8) | Aksi utama |
| success | #16A34A | Lolos, disetujui |
| warning | #B45309 | Perlu perbaikan, tenggat dekat |
| danger | #DC2626 | Ditolak, galat |
| info | #0891B2 | Informasi |
Kontras teks minimal WCAG AA (4,5:1). Mode gelap: di luar versi 1.

## Tipografi
- Font: Inter (cadangan: system-ui, sans-serif).
- Ukuran dasar 16 px; tabel padat minimal 14 px. Judul halaman 24–28 px, judul seksi 18–20 px.
- Angka dan nominal rupiah memakai angka tabular; format `Rp2.000.000`.

## Layar per peran
| Peran | Layar utama |
|---|---|
| Mahasiswa | Beranda tugas · Daftar proposal · Wizard proposal (Info → Tim → Dana → Berkas → Pra-cek) · Detail + pelacak status · Revisi |
| Dosen | Antrean validasi · Detail proposal bimbingan |
| Reviewer | Daftar penugasan · Form checklist administratif · Form skor substantif |
| Operator | Ringkasan status · Ruang Kontrol · Akun · Penugasan · Seleksi · Audit |
| Pimpinan | Daftar keputusan dengan peringkat · Detail ringkas |

## Referensi visual
Dipilih di Fase 0/6 dari Awesome Design; sampai saat itu gunakan token di atas. Taste Skill dipakai untuk kerapian visual, bukan untuk menambah dekorasi.
