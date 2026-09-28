# PRD — Sistem Pengajuan dan Review Proposal PKM (v0.3)

Alur bisnis resmi: `domain/alur-bisnis.md` (menggantikan alur README awal).

## 1. Deskripsi produk
Aplikasi web untuk mengelola siklus proposal PKM Universitas Udayana: pengajuan tim mahasiswa, validasi dosen pembimbing, review administratif, review substantif awal, revisi, review final, seleksi semifinal, validasi akhir oleh dosen universitas, sampai keputusan pendanaan dan status Pimnas. Aturan administratif dan substantif dapat dikelola operator lewat aplikasi. Sistem ini adalah tahap **evaluasi internal PT** sebelum usulan diunggah ke Simbelmawa; hasil akhirnya Berita Acara per bidang sesuai kuota klaster PT (Panduan 2026).

## 2. Pengguna
| Peran | Tugas utama | Kebutuhan UX utama |
|---|---|---|
| Mahasiswa (ketua/anggota) | Klaim akun, susun/ajukan proposal, revisi, unggah revisi akhir | Tahu apa yang kurang sebelum submit; tahu status dan langkah berikutnya |
| Dosen Pembimbing | Validasi proposal (pengajuan dan revisi), pantau status | Antrean jelas, keputusan cepat |
| Reviewer | Review administratif, substantif awal, atau final (per penugasan) | Form terstruktur, simpan progres, total skor otomatis |
| Dosen Univ | Validasi akhir setelah revisi akhir | Daftar tugas dan berkas dalam satu layar |
| Operator | Jadwal, kuota/batas dana, form penilaian, penugasan, hasil semifinal/final | Ringkasan status semua proposal, tindakan massal |
| Pimpinan PT (Bidang Kemahasiswaan) | Semua tugas Operator + kelola akun/hak akses, keputusan batch, Berita Acara, laporan Simbelmawa dan prestasi | Ringkasan hasil per bidang dan dokumen siap cetak |
Pimpinan PT adalah Operator dengan wewenang lebih tinggi dan memakai sistem langsung (DEC-15).

Skala: 34.215 mahasiswa aktif, 1.446 dosen ber-NIDN (populasi total; pengguna aktif jauh lebih sedikit). Beban puncak: unggahan menjelang tenggat dan klaim akun di awal siklus.

## 3. Sasaran
- Kesalahan administratif tertangkap **sebelum** proposal dikirim (pra-cek otomatis).
- Setiap pengguna melihat "apa yang perlu saya lakukan sekarang".
- Rubrik, checklist, batas dana, kuota, jadwal adalah **data yang dikelola operator**, bukan kode.
- Identitas reviewer rahasia; semua perubahan status dan keputusan tercatat.

## 4. Aturan pendanaan (skema didanai)
| Sumber | Wajib | Batas |
|---|---|---|
| Perguruan Tinggi | Ya | Rp2.000.000 |
| Mitra/sponsor | Tidak | Rp1.000.000 |
| Belmawa | Ya | Rp8.000.000 |
Nilai batas disimpan per siklus dan skema, diatur operator di Ruang Kontrol. Panduan 2026: dana Belmawa direkomendasikan Rp6.000.000–Rp8.000.000 (di bawah Rp6 juta = peringatan, di atas Rp8 juta = ditolak); komposisi minimum 80% operasional dan maksimum 20% administrasi.

## 5. Fitur
M = wajib versi 1, N = nanti.

| ID | Fitur | Prio |
|---|---|---|
| REQ-01 | Login; satu akun boleh punya beberapa peran | M |
| REQ-02 | Impor data mahasiswa/dosen oleh operator → akun "belum diklaim" | M |
| REQ-03 | Klaim akun oleh mahasiswa dengan email student; kredensial dikirim ke email; wajib ganti kata sandi | M |
| REQ-04 | Pimpinan PT: CRUD akun dan hak akses | M |
| REQ-05 | Ruang Kontrol: jadwal fase, kuota, batas dana per siklus dan skema | M |
| REQ-06 | Operator CRUD form penilaian (checklist administratif, rubrik substantif) per skema; konfirmasi (kunci) sebelum penugasan; berversi | M |
| REQ-10 | Buat draf: skema, judul (maks 20 kata), pembimbing, anggota (3–5 termasuk ketua, ≥2 angkatan), satu dari 10 tema; satu mahasiswa hanya di satu proposal per siklus | M |
| REQ-11 | Input dana 3 sumber + porsi administrasi; validasi batas dan komposisi 80/20 | M |
| REQ-12 | Unggah berkas per slot persyaratan, indikator kelengkapan, versi berkas | M |
| REQ-13 | Pra-cek sebelum submit (item ADM bertipe Otomatis) | M |
| REQ-14 | Draf tersimpan otomatis | M |
| REQ-20 | Validasi pembimbing tahap 1 (pengajuan): setuju/tolak, catatan wajib bila tolak | M |
| REQ-21 | Validasi pembimbing tahap 2 (revisi) | M |
| REQ-22 | Maks 10 proposal bimbingan per dosen per siklus (semua skema); dosen wajib punya NUPTK | M |
| REQ-23 | Dosen dan mahasiswa memantau status proposal | M |
| REQ-30 | Operator menugaskan reviewer administratif | M |
| REQ-31 | Operator menugaskan reviewer substantif awal | M |
| REQ-32 | Operator menugaskan reviewer final; tiga reviewer per proposal harus orang berbeda; cek konflik | M |
| REQ-33 | Form review administratif ✓/✗ + keterangan; **tanpa skor**; kekurangan diberikan ke mahasiswa | M |
| REQ-34 | Form review substantif/final: skor per kriteria, nilai = bobot × skor | M |
| REQ-35 | Catatan revisi terkirim ke mahasiswa tanpa identitas reviewer | M |
| REQ-36 | Revisi: unggah dalam jendela waktu, riwayat ronde | M |
| REQ-40 | Operator input hasil semifinal (lolos/tidak) | M |
| REQ-41 | Operator menugaskan Dosen Univ; mahasiswa unggah revisi akhir; Dosen Univ setuju/tolak | M |
| REQ-42 | Input penilaian final dan keputusan pendanaan per **batch** (peserta keputusan tercatat) | M |
| REQ-43 | Penetapan status Lolos Pimnas | M |
| REQ-44 | Halaman hasil final dan pendanaan untuk mahasiswa dan pembimbing | M |
| REQ-50 | Notifikasi in-app + email tiap perubahan status | M |
| REQ-51 | Log audit | M |
| REQ-52 | Pimpinan PT: Berita Acara Evaluasi Internal per bidang (Lampiran 2 Panduan), laporan Simbelmawa, laporan prestasi | M |
| REQ-60 | Dashboard statistik | N |
| REQ-64 | Usulan pergantian anggota tim (oleh operator) | N |
| REQ-61 | Pengingat tenggat otomatis | N |
| REQ-62 | SSO kampus | N |
| REQ-63 | Luaran pasca-pendanaan: laporan kemajuan/akhir, tautan video YouTube (2–4 menit, ≥720p 30 fps), akun media sosial | N |

## 6. Di luar cakupan versi 1
Pemeriksaan otomatis isi PDF (font, margin), integrasi Turnitin/Simbelmawa, RAB per baris (hanya total per sumber), pencairan dana, luaran dan penilaian pasca-pendanaan (REQ-63).

## 7. Non-fungsional
- **UX:** `Design.md`.
- **Keamanan:** Policy per proposal; berkas privat; kata sandi awal berlaku sekali dan wajib diganti; kredensial tidak disimpan dalam bentuk terbaca; batas percobaan login; data pribadi minimal.
- **Email:** pengiriman lewat antrean dengan pembatasan laju (klaim akun serentak); SMTP dipilih di Fase 0 (batas kuota Gmail perlu dicek).
- **Waktu:** Asia/Makassar (WITA).
- **Cadangan:** backup database dan berkas terjadwal.

## 8. Keputusan
### Sudah diputuskan
| ID | Keputusan |
|---|---|
| DEC-01 | Per proposal: 1 reviewer administratif, 1 substantif awal, 1 substantif final; ketiganya berbeda; final dipilih operator |
| DEC-03 | PKM-AI dan PKM-GFT tanpa pendanaan proyek (skema insentif) |
| DEC-04 | Identitas reviewer rahasia bagi pengusul |
| DEC-05 | Berkas proposal = **satu file PDF** (naskah utama + seluruh lampiran digabung jadi satu; selaras dengan format Simbelmawa) |
| DEC-06 | Skor seleksi = skor review final |
| DEC-08 | Keputusan batch diberikan oleh Pimpinan PT, reviewer, dan operator |
| DEC-09 | Halaman inti skema didanai maksimal 10 (Bab 1–Daftar Pustaka) |
| DEC-10 | Video PKM-VGK = luaran pasca-pendanaan (REQ-63) |
| DEC-11 | Judul bab per skema diambil dari Panduan 2026 (`domain/panduan-pkm-2026.md`) |
| DEC-12 | Review administratif tidak dinilai dan tidak menggugurkan; hanya daftar kekurangan untuk mahasiswa |
| DEC-13 | "Kuota" = kuota klaster PT per bidang dari Belmawa (Panduan 2026); jumlah proposal lolos evaluasi internal ≤ kuota |
| DEC-05 | Proposal diunggah sebagai **satu file PDF** (naskah utama + lampiran dijadikan satu); unduhan juga satu PDF |
| DEC-18 | Tenggat per fase ditetapkan di `phase_windows`; pihak yang tidak merespons setelah tenggat dicatat sebagai "tidak menindaklanjuti" |
| DEC-19 | Kekurangan administratif disampaikan ke mahasiswa **bersama catatan revisi** di akhir review substantif awal (bukan langsung setelah review admin) |
| DEC-20 | Laporan prestasi dan laporan Simbelmawa = rekap per tim: hasil evaluasi internal, hasil Belmawa, status Pimnas, juara (input manual oleh operator) |
| DEC-21 | Keputusan batch = lolos evaluasi internal PT (≤ kuota klaster); hasil pendanaan Belmawa dan status Pimnas dicatat operator kemudian |
| DEC-22 | Penyedia SMTP = **Brevo** (dulu Sendinblue); konfigurasi di Fase 0 (PH0-08) |
| DEC-14 | Dosen pendamping maks 10 proposal per siklus (semua skema), wajib NUPTK |
| DEC-15 | Pimpinan Operator = Pimpinan PT = Operator + wewenang lebih tinggi + Berita Acara/laporan Simbelmawa/prestasi |
| DEC-16 | Gmail apa saja diperbolehkan **untuk notifikasi**, tetapi identitas dibuktikan lewat email student (Architecture §6) |
| DEC-17 | Panduan 2026 utama; Panduan 2025 dan formulir Udayana sebagai cadangan; aturan Udayana yang lebih ketat tetap dipakai |
| DEC-18 | Tenggat per fase; lewat tenggat → status "tidak menindaklanjuti" (proposal tertahan) |
| DEC-19 | Kekurangan administratif diberikan ke mahasiswa **bersama catatan revisi di akhir review substantif awal** |
| DEC-20 | Laporan Simbelmawa dan laporan prestasi = rekap per tim: hasil evaluasi internal, hasil Belmawa, status Pimnas, juara (input manual operator) |
| DEC-21 | Keputusan batch = lolos evaluasi internal PT (≤ kuota klaster); hasil pendanaan Belmawa dan status Pimnas dicatat operator kemudian |

### Terbuka
*(Semua keputusan utama sudah dijawab. Keputusan baru dicatat saat muncul.)*
