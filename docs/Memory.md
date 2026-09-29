# Memory — Log Keputusan dan Progres

Perbarui setelah setiap sesi besar. Entri terbaru di atas.

## 2026-09-29 — Fase 2: Database, Model, Enum, dan Routing (PH2-01 sd PH2-04)
- **Breeze (Blade)** terinstal penuh.
- **10 Enum** diimplementasikan (Role, ProposalStatus, SchemeCode, ReviewStage, dsb).
- **30 Tabel Database (Migration)** berhasil di-*migrate* tanpa *error*, mengimplementasikan struktur data kompleks dari *cycles*, skema, rubrik, hingga *decision_batches*.
- **28 Model Eloquent** dibuat lengkap beserta *type casting* ke Enum dan metode pembantu (*helpers*), misal `User::primaryRole()`.
- **4 Seeder Master** selesai dibuat dan dijalankan:
  1. `SchemeSeeder`: 10 Skema PKM.
  2. `ThemeSeeder`: 10 Tema Tematik 2026 wajib.
  3. `ChecklistFormSeeder`: 36 item administratif, mencakup 3 grup (*funded*, *article_ai*, *article_gft*).
  4. `RubricSeeder`: Kriteria dan bobot *substantive* 100% untuk semua skema sesuai panduan `penilaian.md`.
- **PH2-03 (Routing Login)**: *Universal dashboard route* (`/dashboard`) telah dipasang yang mendeteksi peran tertinggi pengguna (*SuperOperator* ke bawah) lalu melakukan *redirect* ke *sub-dashboard* peran yang sesuai (mis. `/student/dashboard`).
- **PH2-04 (Account Claim)**: `AccountClaimService` telah disiapkan untuk menghasilkan token klaim acak yang kedaluwarsa setelah 7 hari bagi mahasiswa yang diundang sebagai anggota proposal.
- **Sisa Fase 2:** CRUD Akun, Ruang Kontrol (PhaseGate), Form Penilaian, dan *Styling UI Layout*.


## 2026-09-28 — Fase 0 LULUS
- MySQL 8.0.30 aktif; `pkm_udayana` (utf8mb4_unicode_ci) dibuat.
- `.env` dikonfigurasi: MySQL, `APP_TIMEZONE=Asia/Makassar`, `APP_LOCALE=id`, SMTP Brevo.
- `php artisan migrate` sukses (3 tabel default); `php artisan test` 2/2 lulus.
- Git repo diinisialisasi; initial commit `f623608`.
- `composer.json` platform PHP 8.3 dikunci; Symfony downgrade ke 7.4.x.
- Semua DEC terjawab: DEC-05 (1 PDF), 18 (tenggat/tidak menindaklanjuti), 19 (admin bersama revisi sub awal), 20 (rekap manual), 21 (batch=lolos internal), 22 (Brevo).
- ADM-14 diperbarui: tema wajib SEMUA skema termasuk AI dan GFT.
- PKM-KI rubrik 3a/3b dikonfirmasi; skor 4 tidak ada = format baku kementerian.
- PH0-05 dan PH0-06 (skill/plugin desain) belum dipasang — dapat dilakukan di Fase 7.
- **Berikutnya: Fase 1 (tinjauan dokumen final, lalu Fase 2 coding dimulai).**


- `docs/domain/penilaian.md` **sudah terisi** lengkap: rubrik 10 skema dengan bobot per kriteria, total 100 per skema.
- Skor 4 **tidak ada** adalah format baku resmi kementerian; penilai wajib memakai 6 nilai: 1, 2, 3, 5, 6, 7.
- **DEC dijawab sesi ini:** DEC-05 (satu PDF), DEC-18 (tenggat per fase → tidak menindaklanjuti), DEC-19 (kekurangan admin bersama catatan revisi substantif awal), DEC-20 (rekap manual), DEC-21 (batch = lolos evaluasi internal), DEC-22 (SMTP = Brevo).
- `composer.json`: `config.platform.php = "8.3"` ditambahkan; `composer update` dijalankan (PH0-01 sebagian selesai — masih perlu PATH CLI juga diarahkan ke 8.3).
- Celah yang masih perlu ditangani sebelum Fase 0 lulus:
  - PATH PHP CLI ke 8.3 (RULE-40, PH0-01) — platform sudah dikunci di composer, tapi `php -v` masih 8.4.
  - PKM-KI rubrik baris 3: penafsiran kriteria 3a/3b perlu dikonfirmasi sebelum seeder.
  - Tema untuk PKM-AI dan PKM-GFT: opsional atau wajib?
  - Urutan lampiran tambahan KC/KI terhadap 5 lampiran standar.


- Sistem = tahap evaluasi internal PT sebelum Simbelmawa; keluaran: Berita Acara per bidang sesuai kuota klaster.
- Pimpinan Operator = Pimpinan PT = operator + akun/hak akses + Berita Acara/laporan Simbelmawa/prestasi.
- Review administratif tidak dinilai; skor seleksi = review final; dosen maks 10 proposal (NUPTK); 1 mahasiswa 1 proposal per siklus.
- Dana Belmawa rekomendasi Rp6–8 juta; komposisi 80/20; 10 tema wajib dipilih.
- Klaim akun: identitas dibuktikan lewat email student; Gmail hanya notifikasi dengan OTP; fallback verifikasi manual operator.
- Panduan 2026 utama, Panduan 2025/formulir Udayana cadangan.
- Terbuka: DEC-05, 18, 19, 20, 21. PHP CLI/web belum dikonfirmasi selaras.

## 2026-09-28 — Pembaruan v0.2 (alur swimlane, jawaban DEC)
- Alur bisnis resmi = flowchart swimlane pengguna (`domain/alur-bisnis.md`); menggantikan README awal.
- Peran: student, supervisor, reviewer, university_lecturer, operator, super_operator (Pimpinan Operator). Pimpinan PT memutuskan per batch bersama reviewer dan operator.
- Review: 1 administratif + 1 substantif awal + 1 final; ketiganya berbeda; reviewer rahasia.
- Akun: impor data → klaim lewat email student → kata sandi acak berupa kata mudah diingat → wajib ganti. Kredensial hanya ke email tersimpan.
- Halaman inti maks 10; video VGK = luaran pasca-pendanaan; lampiran tambahan KC, KI, PM, PI dicatat.
- Lingkungan: PHP CLI 8.4.1 vs web Laragon 8.3.22 → samakan ke 8.3 (RULE-40, PH0-01).
- Terbuka: DEC-05, 06, 11–18. Berikutnya: samakan PHP, lalu Fase 0 (verifikasi repositori skill dan plugin).

## 2026-09-28 — Fase 1 (draf dokumen)
- Stack: Laravel (PHP 8.3), MySQL Laragon, Blade + Tailwind + Alpine, Breeze, disk lokal privat; tanpa Supabase.
- Mulai dari nol; dependensi awal Laravel baru terpasang.
- 10 skema: 8 didanai (RE, RSH, K, KI, KC, VGK, PM, PI) + AI + GFT (asumsi tanpa dana).
- Pendanaan: PT wajib maks Rp2 juta; mitra opsional maks Rp1 juta; Belmawa wajib maks Rp8 juta.
- Aturan administratif: 3 formulir formating (AI, GFT, 8 skema didanai). Aturan substantif: rubrik per skema, skor 1,2,3,5,6,7; nilai = bobot × skor.
- Prinsip: UX dan fungsi di atas UI; aturan berbasis data.
- Model AI tersedia di Antigravity: Claude Sonnet dan Opus (thinking), Gemini Flash, Gemini Pro.
- Keputusan terbuka: DEC-01 s.d. DEC-11 di `PRD.md`.
- Berikutnya: Fase 0 (setup lingkungan, verifikasi repositori skill dan plugin), lalu jawab DEC prioritas.
