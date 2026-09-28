# Alur Bisnis (sumber: flowchart swimlane pengguna)

```mermaid
flowchart TD
  A[Mahasiswa isi dan submit] --> B{Pembimbing validasi 1}
  B -- Ditolak --> A
  B -- Setuju --> C[Operator: assign reviewer administratif]
  C --> D[Review administratif]
  D --> E[Operator: assign reviewer substantif]
  E --> F[Review substantif awal]
  F --> G[Catatan revisi ke mahasiswa]
  G --> H[Mahasiswa revisi dan unggah]
  H --> I{Pembimbing validasi 2}
  I -- Ditolak --> H
  I -- Setuju --> J[Operator: assign reviewer final, berbeda]
  J --> K[Review final]
  K --> L[Operator: input hasil semifinal]
  L --> M{Lolos?}
  M -- Tidak --> N[Selesai]
  M -- Ya --> O[Operator: assign Dosen Univ]
  O --> P[Mahasiswa unggah revisi akhir]
  P --> Q{Dosen Univ validasi}
  Q -- Ditolak --> P
  Q -- Setuju --> R[Operator: input penilaian final]
  R --> S[Keputusan pendanaan per batch]
  S --> T[Status Lolos Pimnas]
  T --> U[Laporan Simbelmawa dan prestasi; mahasiswa lihat hasil]
```

Pengaturan pendukung: Pimpinan Operator (CRUD akun, hak akses) · Operator (Ruang Kontrol: jadwal, kuota/batas dana; CRUD dan konfirmasi form penilaian) · Registrasi/login mahasiswa · Pembimbing memantau status.

## Celah pada alur (perlu diputuskan)
1. **Review administratif tidak punya cabang gagal.** Alur langsung ke substantif. Perlu hasil: lolos / perlu perbaikan / gugur (DEC-12).
2. **Tidak ada tenggat dan batas ronde.** Loop tolak → revisi → tolak dapat tanpa akhir; mahasiswa atau reviewer yang tidak merespons menahan proposal (DEC-18).
3. **Form dapat berubah saat review berjalan.** Diatasi dengan konfirmasi/kunci dan versi (REQ-06).
4. **Skor mana yang dipakai** setelah dua penilaian substantif tidak disebut (DEC-06).
5. **"Kuota" dan "set 10 proposal" ambigu** (DEC-13, DEC-14).
6. **Tidak ada notifikasi** pada alur; ditambahkan di setiap perubahan status.
7. **Operator memegang hampir semua keputusan.** Audit log wajib, dan keputusan final tercatat per batch dengan peserta.
8. **Klaim akun:** jika kredensial dikirim ke alamat yang diketik pengklaim, akun bisa direbut (Architecture §6).

## Status celah (v0.3)
- Celah 1 (admin tanpa cabang gagal): **selesai** — review administratif tidak dinilai; kekurangan diberikan ke mahasiswa (DEC-12).
- Celah 4 (skor mana): **selesai** — skor review final (DEC-06).
- Celah 5 (kuota, 10 proposal): **selesai** — kuota klaster PT per bidang; dosen maks 10 proposal (DEC-13, DEC-14).
- Celah 8 (klaim akun): **ditangani** — Architecture §6.
- Masih terbuka: tenggat dan batas ronde (DEC-18).
