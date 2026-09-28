# Rules

## Protokol kerja agen (wajib)
- RULE-01 Awal sesi: baca `00-INDEX.md`, `Rules.md`, `Memory.md`. Kerjakan **satu fase** per waktu sesuai `Phases.md`.
- RULE-02 Jika ada ambiguitas aturan bisnis, **tanya**, jangan menebak. Catat jawabannya sebagai `DEC-xx`.
- RULE-03 Jangan menambah fitur di luar `PRD.md`. Usulan fitur baru ditulis sebagai saran, bukan langsung dibuat.
- RULE-04 Sebelum mengubah berkas yang sudah ada, tampilkan rencana perubahan singkat. Ubah seperlunya, jangan menulis ulang seluruh berkas.
- RULE-05 Setiap fase selesai hanya setelah tes dijalankan dan lulus. Ringkasan hasil masuk `Memory.md`.

## Yang dipakai
- RULE-10 Nama kode, tabel, kolom, route dalam bahasa Inggris; teks UI dalam bahasa Indonesia.
- RULE-11 Skema database hanya diubah lewat **migration**. Migration yang sudah dijalankan tidak diedit; buat migration baru.
- RULE-12 Validasi input lewat `FormRequest`. Otorisasi lewat `Policy`. Alur status hanya lewat `ProposalWorkflowService`.
- RULE-13 Logika bisnis di `Services`, controller tipis, Blade tanpa logika bisnis.
- RULE-14 Enum PHP untuk peran, status, sumber dana, fase.
- RULE-15 Batas dana, rubrik, checklist, jadwal, persyaratan berkas dibaca dari database (seeder untuk isi awal).
- RULE-16 Eager loading (`with`) untuk relasi; hindari N+1.
- RULE-17 Setiap fitur baru disertai tes (Pest/PHPUnit). Alur per peran diuji dengan Playwright di Fase 6.
- RULE-18 Semua waktu memakai zona Asia/Makassar; simpan UTC di database bila konfigurasi Laravel memerlukannya, tampilkan WITA.
- RULE-19 Pesan galat berbahasa Indonesia, menjelaskan apa yang salah dan cara memperbaikinya.

## Yang dihindari
- RULE-30 Jangan memakai Supabase atau layanan luar untuk data/berkas.
- RULE-31 Jangan mengubah struktur tabel lewat phpMyAdmin.
- RULE-32 Jangan menyimpan berkas di disk publik; jangan menautkan langsung ke path penyimpanan.
- RULE-33 Jangan hardcode angka aturan (Rp2.000.000, 25%, bobot rubrik, dll.) di controller atau view.
- RULE-34 Jangan menambah paket Composer/npm tanpa persetujuan; sebutkan alasan dan alternatifnya.
- RULE-35 Jangan mengubah stack (framework, database, pola frontend) di tengah pembangunan.
- RULE-36 Jangan menaruh data penting di `localStorage`/browser storage.
- RULE-37 Jangan commit `.env`, kunci, atau data pengguna nyata.
- RULE-38 Jangan menampilkan identitas reviewer kepada pengusul (bila DEC-04 aktif).
- RULE-39 Jangan mengorbankan alur atau kejelasan UX demi tampilan; kesederhanaan lebih utama daripada dekorasi.

## Tambahan v0.2
- RULE-40 Sebelum menjalankan perintah php/composer/artisan, agen memeriksa `php -v` dan memastikan 8.3.x. Jangan memakai PHP 8.4 CLI.
- RULE-41 Kredensial akun tidak pernah ditulis ke log, layar, atau berkas; hanya dikirim ke email student yang tersimpan di database.
- RULE-42 Form penilaian yang sudah `confirmed` tidak diubah; buat versi baru.
- RULE-43 Tiga reviewer pada satu proposal harus orang berbeda; diperiksa di `ReviewerAssignmentService`.
- RULE-44 Identitas reviewer tidak boleh muncul di view, URL, email, atau ekspor yang dilihat mahasiswa/pembimbing.
- RULE-45 Sumber aturan: Panduan 2026 utama; Panduan 2025/formulir Udayana sebagai cadangan. Catat rujukan panduan pada setiap aturan yang diimplementasikan.
- RULE-46 Review administratif tidak menghasilkan skor dan tidak mengubah status menjadi gugur; hanya daftar kekurangan.
- RULE-47 Satu mahasiswa satu proposal per siklus dan satu dosen maks 10 proposal: ditegakkan di service **dan** constraint database.
