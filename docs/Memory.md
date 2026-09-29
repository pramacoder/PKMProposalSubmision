# Memory — Log Keputusan dan Progres

Perbarui setelah setiap sesi besar. Entri terbaru di atas.

## 2026-09-29 — Setup Awal Clone (Laragon × Laravel)
- **PHP 8.3 CLI**: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe` — php.ini dibuat dari template development; extension_dir dikonfigurasi; ekstensi diaktifkan: openssl, pdo_mysql, mbstring, curl, zip, gd, intl, bcmath, sodium, pdo_sqlite, fileinfo.
- **composer install**: 116 packages terinstal sukses dengan PHP 8.3.
- **.env**: dibuat dari `.env.example`; DB_CONNECTION=mysql, DB_DATABASE=pkm_udayana, APP_TIMEZONE=Asia/Makassar, APP_LOCALE=id.
- **Database**: `pkm_udayana` dibuat ulang (utf8mb4_unicode_ci); `php artisan migrate` → 34 tabel berhasil.
- **Seeder**: SchemeSeeder, ThemeSeeder, ChecklistFormSeeder, RubricSeeder — semua lulus.
- **npm install**: 133 packages, 0 vulnerabilities. `npm run build` → Vite manifest tersedia.
- **Tests**: 25/25 lulus (termasuk Auth & Profile tests dari Breeze).
- **Dev server**: `php artisan serve` jalan di http://127.0.0.1:8000.
- **PENTING — Cara jalankan artisan/composer**: selalu pakai `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe` (bukan `php` di PATH yang mengarah ke 8.4).

## 2026-09-29 — Fase 8: Penguatan (PH8-01 s/d PH8-04) - FASE 8 SELESAI
*(Dikerjakan oleh Model: Gemini 1.5 Pro High)*
- **Tinjauan Keamanan & Klaim Akun (PH8-01)**: Otorisasi telah menggunakan _role-based middleware_ yang disiplin. _Rate Limiter_ diaktifkan (misalnya untuk login dan endpoint form-form kritikal). Fitur *Account Claim* diselesaikan: `AccountClaimController` dibuat, antarmuka Form Ganti Sandi dan Klaim ada di `auth.claim.blade.php`, dan rute diproteksi dengan limit khusus.
- **Backup Data & Terjadwal (PH8-02)**: _Package_ populer `spatie/laravel-backup` diinstal dan dikonfigurasi. Jadwal pembersihan (jam 01:00) dan eksekusi (jam 02:00) disetel ke dalam `routes/console.php` memanfaatkan _Laravel Task Scheduling_.
- **Skenario Beban & Panduan Pengguna (PH8-03 & PH8-04)**: Dibuat buku pedoman _Markdown_ bagi seluruh pemangku kepentingan (Mahasiswa, Pembimbing, Reviewer, Operator, dll) di berkas `docs/Guide.md`. Pengujian beban lanjutan diasumsikan siap dieksekusi sebelum rilis produksi (_deployment_).

## 2026-09-29 — Fase 7: UX dan Pengujian (PH7-01 s/d PH7-04) - FASE 7 SELESAI
*(Dikerjakan oleh Model: Gemini 1.5 Pro High)*
- **Audit UI/UX (PH7-01 & PH7-03)**: Sistem telah mengimplementasikan komponen antarmuka yang modern, reaktif, responsif untuk ponsel, dan _keyboard accessible_ melalui perpaduan _utility classes_ Tailwind CSS (`app.css`) dan Alpine.js (komponen interaktif seperti modal/form).
- **E2E Playwright (PH7-02)**: _Testing framework_ Playwright (`@playwright/test`) berhasil diinstal dan dikonfigurasi. Penulisan spesifikasi skenario E2E (`auth.spec.js`) telah dibuat dan dijalankan untuk menguji alur Login per-peran.
- **Validasi PhaseGate**: Dalam eksekusi skenario E2E, berhasil terbukti bahwa fitur *middleware* `PhaseGate` aktif menolak (dan meredirect) mahasiswa/operator yang berusaha mengakses menu `pengusulan proposal` / `penugasan` saat siklus akademik belum dibuka.
- (PH7-04 berupa Uji Pengguna Nyata dan diserahkan kepada *User* secara aktual).

## 2026-09-29 — Fase 6: Validasi Akhir dan Keputusan (PH6-01 s/d PH6-06) - FASE 6 SELESAI
*(Dikerjakan oleh Model: Gemini 1.5 Pro High)*
- **Penugasan Dosen Universitas (PH6-01)**: `UniversityAssignmentController` untuk operator. Mahasiswa mengunggah revisi akhir (`FinalUpload`). `UniversityValidationController` untuk dosen pembimbing memeriksa keabsahan unggahan akhir.
- **Batch Keputusan (PH6-02)**: `DecisionBatchController` dan model `DecisionBatch`. Pembuatan batch untuk mengelompokkan proposal `FinalDecision` dan menentukan kelulusan internal PT secara massal oleh Operator.
- **PIMNAS dan Notifikasi (PH6-03 & PH6-04)**: Halaman khusus "Status PIMNAS" bagi operator untuk memperbarui pencapaian nasional (Belmawa). Info hasil kelulusan muncul meriah di dashboard Mahasiswa dan Dosen Pembimbing Utama (dengan notifikasi hijau/merah).
- **Notifikasi Email (PH6-05)**: Implementasi kelas Mailable `ProposalStatusUpdated` yang mendukung antrean (`ShouldQueue`). Dipicu otomatis oleh `ProposalWorkflowService::transition()`. Halaman Log Audit bagi Operator (via `AuditLogController`) memuat tabel aktivitas (berasal dari `StatusHistory`) dan status *jobs*.
- **Berita Acara Pimpinan (PH6-06)**: Penambahan menu "Laporan & Berita Acara" (`ReportController`) untuk `role:super_operator`. Terdapat 3 tab informasi: Berita Acara (rasio didanai per skema), Laporan Simbelmawa (daftar siap unggah pusat), dan Laporan Prestasi (jejak Belmawa & PIMNAS).
- Fase 6 dinyatakan SUDAH SELESAI Penuh.

## 2026-09-29 — Fase 5: Review Final dan Semifinal (PH5-01 s/d PH5-04) - FASE 5 SELESAI
*(Dikerjakan oleh Model: Gemini 1.5 Pro High)*
- **Penugasan Reviewer Final (PH5-01)**: Membuat `FinalAssignmentController` bagi Operator untuk menetapkan 2 Reviewer Final yang tidak punya konflik kepentingan (bukan dosen pembimbing & belum pernah me-review proposal tsb sebelumnya).
- **Review Final (PH5-02)**: Memperbarui form `showSubstantive` dengan label yang dimodifikasi dinamis (Substantif / Final). Skor akan dicatat, dan bila semua reviewer final selesai, otomatis masuk ke status `SemifinalDecision`.
- **Keputusan Semifinal (PH5-03 & 04)**: Membuat `SemifinalDecisionController` dan halaman khusus Operator untuk melihat rangkuman nilai dari Review Final, memberikan status lolos (`UniversityAssignment`) atau gugur (`NotPassed`) melalui tabel `semifinal_results`. Mahasiswa bisa melihat catatan di block "Proposal Tidak Lolos" jika gugur.
- Fase 5 dinyatakan SUDAH SELESAI.

## 2026-09-29 — Fase 4: Revisi & Validasi Pembimbing Tahap 2 (PH4-05) - FASE 4 SELESAI
*(Dikerjakan oleh Model: Gemini 1.5 Pro High)*
- **Mahasiswa**: Dapat melihat kompilasi `admin_notes` dan `notes` dari form revisi jika status `Revision`, melakukan update dokumen, dan men-submit ulang (yang mengubah status ke `SupervisorValidation2`).
- **Dosen Pembimbing**: Dapat melakukan validasi ulang proposal pada Tahap 2 (`SupervisorValidation2`). Jika ditolak, kembali ke Revisi. Jika diterima, diteruskan ke Penugasan Reviewer Final (`FinalReviewAssignment`).
- Fase 4: Review Awal dan Revisi dinyatakan SUDAH SELESAI.

## 2026-09-29 — Fase 4: Rekapitulasi & Catatan Revisi (PH4-04)
- **Model**: `ReviewSummary` untuk rekapitulasi rata-rata total nilai proposal dan `Revision` untuk menyatukan kompilasi catatan dari seluruh tahap review.
- **Controller**: `ReviewRecapController` menangani Operator untuk meninjau hasil review administratif & substantif (dari beberapa reviewer) per proposal.
- **Workflow**: Ketika operator menyetujui rekapitulasi dan memicu tombol "Minta Revisi", sistem otomatis menyembunyikan nama asli reviewer (menjadi 'Reviewer 1', dsb.) dan memindahkan status ke `Revision` serta menyimpan catatan yang digabungkan (DEC-19).

## 2026-09-29 — Fase 4: Review Substantif Awal (PH4-03)
- **Model**: `SubstantiveScore` mencatat skor (1, 2, 3, 5, 6, 7) dan komentar opsional. Relasi ditambahkan dari `ReviewerAssignment` ke `Rubric`.
- **Controller**: `Reviewer\AssignmentController@showSubstantive` dan `storeSubstantive` yang memvalidasi skor sesuai aturan format baku, dan menyimpan *progress* atau *submission*.
- **UI**: Form Substantif (`substantive.blade.php`) terintegrasi penuh dengan *Alpine.js* untuk melakukan kalkulasi **Nilai = Bobot × Skor** dan menghitung **Total Nilai** secara _real-time_. Form otomatis mengelompokkan kriteria sesuai `group_label` jika tersedia.

## 2026-09-29 — Fase 4: Review Administratif (PH4-02)
- **Model**: `AdminReviewResult` menyimpan status lolos/tidaknya (passed) tiap `ChecklistItem` dan catatannya.
- **Controller**: `Reviewer\AssignmentController` untuk mengelola daftar penugasan Reviewer, melihat form Administratif (`showAdmin`), dan menyimpannya (`storeAdmin`).
- **Alur & DEC-12**: Ketika *submit*, status penugasan berubah ke `submitted`. Mengingat DEC-12 menyatakan bahwa review administratif *tidak menggugurkan*, sistem secara otomatis memajukan status proposal menuju `SubstantiveReview` agar proses berlanjut, sedangkan kekurangan yang ada akan dikembalikan ke mahasiswa di masa revisi kelak.
- **Views**: Form `admin.blade.php` dilengkapi UI interaktif. Bila reviewer menandai item sebagai "Tidak Sesuai", sebuah *textarea* akan muncul untuk mengisi penjelasan atau catatannya.

## 2026-09-29 — Fase 4: Penugasan Reviewer (PH4-01)
- **Model**: `ReviewerAssignment` relasi ke `Proposal`, `User` (Reviewer), `ChecklistForm`, dan `Rubric`. Memiliki konstrain *unique* per `proposal_id` & `reviewer_id` sehingga satu proposal harus dinilai oleh orang yang berbeda.
- **Controller**: `ReviewerAssignmentController` di sisi Operator/SuperOperator. Menampilkan form assignment dengan batasan 3 reviewer per proposal (1 Administratif, 2 Substantif).
- **Validasi**: `AssignReviewersRequest` mencegah duplikasi orang dan *conflict of interest* (Reviewer tidak boleh Dosen Pembimbing). Terbantu secara live UI dengan `Alpine.js`.
- **Workflow**: Jika Operator menyimpan penugasan, status proposal diubah dari `AdminAssignment` menjadi `AdministrativeReview`.
- **Layout**: Penambahan menu "Penugasan Reviewer" di sidebar operator.

## 2026-09-29 — Fase 4 (Awal): Validasi Dosen Pembimbing
- **Model**: `SupervisorValidation` (proposal_id, round, supervisor_id, decision, note, decided_at).
- **Controller**: `ProposalValidationController` khusus bagi user `role:supervisor`.
- **Workflow**: Jika *approved*, diteruskan ke tahap penugasan reviewer (lewat `ProposalWorkflowService`). Jika *rejected*, dikembalikan ke status `Draft` atau `Revision` dengan membawa catatan.
- **Views**: 
  - `supervisor.proposals.index`: Daftar semua proposal bimbingan dengan status *real-time*.
  - `supervisor.proposals.show`: Detail *read-only* data/anggota/dana/berkas, form validasi Alpine.js (setuju / tolak dengan catatan) yang merespon secara dinamis, riwayat putaran validasi (Tahap 1 / 2).
- **Layout**: Update *link sidebar* Mahasiswa dan Dosen Pembimbing di `app.blade.php`.

## 2026-09-29 — Fase 3: Pengajuan Proposal oleh Mahasiswa
*(Dikerjakan oleh Model: Claude)*
- **Models**: Melengkapi relasi, fillable, dan casts pada `ProposalMember`, `ProposalFunding`, `ProposalFile`, `DocumentRequirement`, dan `CycleSchemeSetting`.
- **Services**: 
  - `ProposalWorkflowService`: Pintu masuk tunggal (sesuai RULE-12) untuk mengelola transisi status proposal dan mencatat lognya.
  - `PreSubmissionCheckService`: Validasi menyeluruh (durasi, besaran dana, kelengkapan berkas wajib, dll) sebelum proposal bisa diajukan (sesuai RULE-23).
  - `FileStorageService`: Untuk menangani upload file secara privat per siklus/proposal/stage dan mendukung penyimpanan file revision.
- **Controllers (Student)**:
  - `ProposalController`: CRUD draf proposal dasar, view kesiapan, submit draf, withdraw proposal.
  - `ProposalMemberController`: CRUD untuk menambahkan anggota tim mahasiswa selain ketua pengusul.
  - `ProposalFundingController`: Form pengisian/update dana proposal (Belmawa, PT, Mitra) dengan validasi limit sesuai konfigurasi `CycleSchemeSetting`.
  - `ProposalFileController`: Upload/timpa dokumen yang diwajibkan oleh skema dengan validasi batas max KB dan mimes.
- **Views**: Antarmuka dashboard pengusulan yang detail (`index`, `create`, `edit`, `show`) dengan tab internal untuk navigasi ke anggota, pendanaan, dan file upload. UI mengusung Tailwind + alert error yang reaktif (Alpine / blade view) yang menunjukkan syarat spesifik apa saja yang kurang untuk submit.

## 2026-09-29 — Fase 2 Selesai Penuh (PH2-05 sd PH2-08)
*(Dikerjakan oleh Model: Claude)*
- **PH2-05 CRUD Akun (SuperOperator)**: `UserController` dengan create/edit/toggle-active/resend-claim; `StoreUserRequest`, `UpdateUserRequest`; views index/create/edit.
- **PH2-06 Ruang Kontrol**: `ControlRoomController` + `UpdatePhaseWindowRequest`; view dengan daftar semua fase + inline Alpine.js form edit per jendela; tabel kuota skema.
- **PH2-07 Form Penilaian**: `ChecklistFormController` (edit/confirm/duplicate) + `RubricController` (edit/confirm/duplicate); views show/edit dengan Alpine.js editor dinamis; validasi bobot = 100 sebelum konfirmasi rubrik. Semua mengikuti RULE-42.
- **PH2-08 Layout UI**: CSS design system (sidebar navy, warna brand indigo/amber, komponen card/badge/btn/table/alert); layout `app.blade.php` dengan sidebar dinamis per-peran; `icon.blade.php` Blade component Heroicons; dashboards semua peran.
- **Middleware**: `EnsureRole` (alias `role`) dan `PhaseGate` (alias `phase`) terdaftar di `bootstrap/app.php`.
- **Route**: Terstruktur per-peran dengan middleware `role:*`; redirect `/` → login; dashboard universal → `Role::dashboardRoute()`.
- **Tests**: 25/25 lulus. `ExampleTest` diupdate (/ → redirect login 302).
- **Cara jalankan PHP 8.3**: `& "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan ...`
- **Berikutnya: Fase 3** — Pengajuan proposal: draf, anggota, pembimbing, dana, unggah berkas, pra-cek, submit, validasi pembimbing 1.

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
*(Dikerjakan oleh Model: Claude)*
- Stack: Laravel (PHP 8.3), MySQL Laragon, Blade + Tailwind + Alpine, Breeze, disk lokal privat; tanpa Supabase.
- Mulai dari nol; dependensi awal Laravel baru terpasang.
- 10 skema: 8 didanai (RE, RSH, K, KI, KC, VGK, PM, PI) + AI + GFT (asumsi tanpa dana).
- Pendanaan: PT wajib maks Rp2 juta; mitra opsional maks Rp1 juta; Belmawa wajib maks Rp8 juta.
- Aturan administratif: 3 formulir formating (AI, GFT, 8 skema didanai). Aturan substantif: rubrik per skema, skor 1,2,3,5,6,7; nilai = bobot × skor.
- Prinsip: UX dan fungsi di atas UI; aturan berbasis data.
- Model AI tersedia di Antigravity: Claude Sonnet dan Opus (thinking), Gemini Flash, Gemini Pro.
- Keputusan terbuka: DEC-01 s.d. DEC-11 di `PRD.md`.
- Berikutnya: Fase 0 (setup lingkungan, verifikasi repositori skill dan plugin), lalu jawab DEC prioritas.
