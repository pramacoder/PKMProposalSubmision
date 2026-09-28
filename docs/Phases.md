# Phases (v0.3)

Centang hanya setelah **diuji**, bukan sekadar dilihat.

## Fase 0 — Setup lingkungan ✅ LULUS
- [x] PH0-01 `composer.json` memuat `config.platform.php = 8.3`; `composer update` dijalankan; web Laragon PHP 8.3. *Catatan: PHP CLI masih 8.4 (warning mongodb.dll diabaikan — ekstensi tidak terpakai di proyek ini).*
- [x] PH0-02 Laragon: MySQL 8.0.30 aktif; database `pkm_udayana` (utf8mb4_unicode_ci) dibuat
- [x] PH0-03 Composer ✓, Node LTS ✓, Git ✓; repositori Git dibuat (initial commit f623608)
- [x] PH0-04 Proyek dibuka di Antigravity; `docs/` terbaca agen ✓
- [ ] PH0-05 Skill desain dipasang setelah repositori diverifikasi (Taste Skill, Web Design Guidelines, Image to Code, Awesome Design, Playwright CLI)
- [ ] PH0-06 Plugin dipasang setelah diverifikasi (Agent Skills, Graphify; OmniRoute dan Ponytail bila perlu)
- [x] PH0-07 `.env` memakai MySQL; `php artisan migrate` sukses (3 tabel default); 2/2 tes lulus
- [x] PH0-08 Penyedia SMTP dipilih: **Brevo** (smtp-relay.brevo.com:587); credential diisi saat produksi

## Fase 1 — Dokumen fondasi
- [ ] PH1-01 Keputusan terbuka terjawab (DEC-05, 18, 19, 20, 21)
- [ ] PH1-02 `domain/penilaian.md` dan formulir formating terbaru disimpan di `docs/domain/`
- [ ] PH1-03 Semua dokumen ditinjau dan disetujui

## Fase 2 — Fondasi sistem
- [ ] PH2-01 Migration konfigurasi, pengguna, proposal (Architecture §4)
- [ ] PH2-02 Enum dan seeder: 10 skema, persyaratan berkas, checklist dan rubrik awal
- [ ] PH2-03 Login, peran ganda, redirect beranda per peran
- [ ] PH2-04 Impor akun (operator) dan alur klaim akun; kata sandi awal ke email student, wajib ganti; verifikasi Gmail dengan OTP
- [ ] PH2-05 Pimpinan Operator: CRUD akun dan hak akses
- [ ] PH2-06 Ruang Kontrol: jadwal fase, kuota, batas dana; `PhaseGate`
- [ ] PH2-07 CRUD form penilaian + konfirmasi/kunci + versi
- [ ] PH2-08 Layout dan komponen UI dasar
- Tes: klaim akun hanya mengirim ke email tersimpan; aksi di luar jendela ditolak; form terkunci tidak bisa diubah; bobot rubrik 100

## Fase 3 — Pengajuan dan validasi pembimbing
- [ ] PH3-01 Draf: skema, tema (1 dari 10), anggota (3–5, ≥2 angkatan, satu mahasiswa satu proposal per siklus), pembimbing (maks 10, wajib NUPTK)
- [ ] PH3-02 Dana 3 sumber + porsi administrasi (maks 20%); batas dan rekomendasi dari database
- [ ] PH3-03 Unggah berkas per slot, versi, unduh berotorisasi
- [ ] PH3-04 Pra-cek dan submit
- [ ] PH3-05 Validasi pembimbing tahap 1 (tolak → draft)
- [ ] PH3-06 Notifikasi in-app dan pemantauan status (mahasiswa, pembimbing)
- Tes: proposal invalid tidak terkirim dan alasan jelas; akses proposal orang lain ditolak

## Fase 4 — Review awal dan revisi
- [ ] PH4-01 Penugasan reviewer administratif dan substantif (cek konflik, tiga reviewer berbeda)
- [ ] PH4-02 Review administratif; hasil sesuai DEC-12
- [ ] PH4-03 Review substantif awal: skor, total otomatis, simpan progres
- [ ] PH4-04 Catatan revisi ke mahasiswa tanpa identitas reviewer
- [ ] PH4-05 Revisi + unggah; validasi pembimbing tahap 2 (tolak → revisi)
- Tes: skor sama dengan hitungan manual; identitas reviewer tidak bocor di halaman/URL/email

## Fase 5 — Review final dan semifinal
- [ ] PH5-01 Penugasan reviewer final (berbeda dari sebelumnya)
- [ ] PH5-02 Review final
- [ ] PH5-03 Input hasil semifinal; ya/tidak; ringkasan peringkat (DEC-06)
- [ ] PH5-04 Proposal tidak lolos berakhir dengan pemberitahuan dan catatan

## Fase 6 — Validasi akhir dan keputusan
- [ ] PH6-01 Penugasan Dosen Univ; unggah revisi akhir; validasi (tolak → unggah ulang)
- [ ] PH6-02 Penilaian final dan keputusan pendanaan per batch (peserta tercatat)
- [ ] PH6-03 Status Lolos Pimnas
- [ ] PH6-04 Halaman hasil final dan pendanaan (mahasiswa, pembimbing)
- [ ] PH6-05 Email notifikasi lewat antrean; log audit terlihat operator
- [ ] PH6-06 Pimpinan PT: Berita Acara per bidang, laporan Simbelmawa, laporan prestasi

## Fase 7 — UX dan pengujian
- [ ] PH7-01 Audit dengan Web Design Guidelines; perbaiki temuan
- [ ] PH7-02 E2E Playwright: satu alur lengkap tiap peran
- [ ] PH7-03 Uji ponsel dan keyboard-only
- [ ] PH7-04 Uji dengan 3–5 pengguna nyata; catat kesulitan

## Fase 8 — Penguatan
- [ ] PH8-01 Tinjau keamanan (otorisasi, unggahan, rate limit, klaim akun)
- [ ] PH8-02 Backup database dan berkas terjadwal, uji pemulihan
- [ ] PH8-03 Uji beban dasar (skenario tenggat dan klaim akun serentak)
- [ ] PH8-04 Panduan singkat per peran; persiapan deploy
