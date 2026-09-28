# Architecture (v0.3)

## 1. Stack (ARC-01)
| Lapisan | Pilihan | Catatan |
|---|---|---|
| Framework | PHP **8.3** + Laravel (versi dicek `php artisan --version`) | CLI dan web wajib memakai PHP 8.3 yang sama (RULE-40) |
| Database | MySQL Laragon via migration; phpMyAdmin hanya untuk melihat data | utf8mb4 |
| Frontend | Blade + Tailwind + Alpine; Livewire bila perlu | Konfirmasi di Fase 0 |
| Auth | Laravel Breeze, dimodifikasi untuk alur klaim akun | |
| Peran | `role_user` + Policy, tanpa paket tambahan | Banyak peran per user |
| Antrean/Cache/Session | `database`/`file` | Tanpa Redis |
| Berkas | Disk lokal privat | Unduh lewat controller berotorisasi |
| Email | `log` saat dev; SMTP saat produksi, lewat antrean | Cek batas kirim |
| Uji | Pest/PHPUnit, Playwright | |
Tidak ada Supabase.

## 2. Alur status proposal (ARC-02)
Status di `proposals.status`; setiap perubahan tercatat di `status_histories`; perubahan hanya lewat `ProposalWorkflowService`.

| Status | Giliran | Ke status berikutnya |
|---|---|---|
| draft | Mahasiswa | submit → supervisor_validation_1 |
| supervisor_validation_1 | Pembimbing | setuju → admin_assignment; tolak → draft |
| admin_assignment | Operator | assign → admin_review |
| admin_review | Reviewer (adm) | submit → substantive_assignment (tanpa skor dan tanpa gugur; kekurangan diteruskan ke mahasiswa, DEC-12/19) |
| substantive_assignment | Operator | assign → substantive_review |
| substantive_review | Reviewer (sub awal) | submit → revision (catatan terkirim) |
| revision | Mahasiswa | unggah → supervisor_validation_2 |
| supervisor_validation_2 | Pembimbing | setuju → final_review_assignment; tolak → revision |
| final_review_assignment | Operator | assign reviewer berbeda → final_review |
| final_review | Reviewer (final) | submit → semifinal_decision |
| semifinal_decision | Operator | tidak → not_passed (akhir); ya → university_assignment |
| university_assignment | Operator | assign Dosen Univ → final_upload |
| final_upload | Mahasiswa | unggah → university_validation |
| university_validation | Dosen Univ | setuju → final_decision; tolak → final_upload |
| final_decision | Operator + Pimpinan PT (batch per bidang) | internal_passed / internal_not_passed (≤ kuota klaster) → Berita Acara; hasil Belmawa dan status Pimnas dicatat kemudian |
| (kapan saja) | Mahasiswa/Operator | withdrawn / disqualified dengan alasan |
Semua tahap bergiliran memiliki tenggat dari `phase_windows` (DEC-18).

## 3. Peran dan akses (ARC-03)
Peran: `student`, `supervisor`, `reviewer`, `university_lecturer`, `operator`, `super_operator` (= Pimpinan PT: Operator dengan wewenang lebih tinggi).
| Aksi | student | supervisor | reviewer | univ_lecturer | operator | super_operator |
|---|---|---|---|---|---|---|
| Susun, submit, revisi, unggah akhir | ✓ (ketua) | | | | | |
| Lihat proposal | tim sendiri | bimbingan | ditugaskan | ditugaskan | semua | semua |
| Validasi 1 dan 2 | | ✓ | | | | |
| Isi review | | | ✓ (per tahap) | | | |
| Validasi akhir | | | | ✓ | | |
| Jadwal, kuota, form, penugasan, hasil | | | | | ✓ | ✓ |
| CRUD akun dan hak akses | | | | | | ✓ |
| Berita Acara, laporan Simbelmawa, prestasi | | | | | | ✓ |
Aturan: dosen tidak mereview proposal bimbingannya; tiga reviewer per proposal berbeda; identitas reviewer disembunyikan dari student dan supervisor (tampil sebagai "Reviewer" tanpa nama).

## 4. Skema database (ARC-04)
Nama tabel/kolom bahasa Inggris; label UI bahasa Indonesia.

**Konfigurasi (dikelola operator)**
- `cycles` (id, year, name, is_active, max_proposals_per_supervisor)
- `schemes` (id, code, name, is_funded, checklist_group)
- `cycle_scheme_settings` (cycle_id, scheme_id, quota [kuota klaster PT], min_pt, max_pt, max_partner, min_belmawa [rekomendasi, peringatan], max_belmawa, max_admin_percent, min_months, max_months)
- `themes` (id, cycle_id, name, sort) — 10 tema PKM Tematik
- `phase_windows` (cycle_id, phase, opens_at, closes_at, forced_open, forced_closed)
- `document_requirements` (scheme_id, code, label, is_required, sort, allowed_mimes, max_kb)
- `checklist_forms` (id, cycle_id, checklist_group, status[draft|confirmed], confirmed_by, confirmed_at) · `checklist_items` (form_id, code, label, kind[auto|manual], sort)
- `rubrics` (id, cycle_id, scheme_id, status[draft|confirmed], confirmed_at) · `rubric_criteria` (rubric_id, group_label, label, weight, sort)
Form/rubrik berstatus `confirmed` tidak dapat diubah; perubahan = versi baru. Penugasan mengacu ke id form/rubrik yang dikunci.

**Pengguna**
- `users` (id, name, email [email student], notification_email, notification_email_verified_at, nim, nidn, nuptk, study_program, cohort_year, password, must_change_password, claimed_at, is_active)
- `role_user` (user_id, role)
- `account_claims` (user_id, token_hash, expires_at, used_at, requested_ip)

**Proposal**
- `proposals` (id, cycle_id, scheme_id, title, leader_id, supervisor_id, status, similarity_percent, start_date, end_date, submitted_at, theme_id, admin_cost_amount, internal_result, belmawa_result, pimnas_status)
- `proposal_members` (proposal_id, cycle_id, user_id, role) — **unik (cycle_id, user_id)**: satu mahasiswa satu proposal per siklus · `proposal_fundings` (proposal_id, source, amount) unik per sumber
- `proposal_files` (id, proposal_id, requirement_id, stage[submission|revision|final], version, path, original_name, size, mime, checksum, uploaded_by, is_current)
- `status_histories` (proposal_id, from_status, to_status, actor_id, note, created_at)
- `supervisor_validations` (proposal_id, round[1|2], supervisor_id, decision, note, decided_at)
- `university_validations` (proposal_id, validator_id, decision, note, decided_at)

**Review dan hasil**
- `reviewer_assignments` (id, proposal_id, reviewer_id, stage[admin|substantive|final], form_id/rubric_id, status, due_at, assigned_by) — unik (proposal_id, reviewer_id)
- `admin_review_results` (assignment_id, checklist_item_id, passed, note)
- `substantive_scores` (assignment_id, criterion_id, score[1,2,3,5,6,7], comment) · `review_summaries` (proposal_id, stage, total, computed_at)
- `revisions` (proposal_id, round, notes, due_at, submitted_at)
- `semifinal_results` (proposal_id, passed, note, decided_by)
- `decision_batches` (id, cycle_id, scheme_id, name, decided_at) · `batch_participants` (batch_id, user_id, role) · `batch_decisions` (batch_id, proposal_id, decision[passed|not_passed], note)

**Layanan**: `notifications` (bawaan), `audit_logs` (actor_id, action, subject, old, new, ip, created_at).

Skor: nilai = bobot × skor; total = jumlah; maksimum 700; ditampilkan juga sebagai persen.

## 5. Kontrol jadwal dan form (ARC-05)
- `PhaseGate` memeriksa `phase_windows` pada setiap aksi tulis (server-side); operator bisa memaksa buka/tutup dengan alasan tercatat.
- `FormLockService`: form/rubrik harus `confirmed` sebelum penugasan pertama; setelah itu tidak bisa diubah.

## 6. Akun dan klaim (ARC-06)
**Prinsip:** identitas mahasiswa dibuktikan dengan **akses ke email student kampus** yang tersimpan di database, bukan dengan alamat yang diketik pengklaim. Gmail pribadi hanya untuk notifikasi, setelah terverifikasi.

Alur:
1. Operator/Pimpinan PT mengimpor data → akun dibuat dengan `claimed_at = null` dan tanpa kata sandi yang bisa dipakai.
2. Mahasiswa membuka halaman klaim dan memasukkan NIM atau email student. Sistem mengirim kata sandi awal (rangkaian kata acak mudah diingat) **ke email student yang tersimpan**. Layar selalu menjawab sama ("jika data cocok, email dikirim").
3. Login pertama wajib mengganti kata sandi. Kata sandi awal sekali pakai dan kedaluwarsa (mis. 48 jam).
4. Opsional: menambahkan Gmail pribadi untuk notifikasi. Gmail baru dipakai setelah diverifikasi dengan kode OTP yang dikirim ke Gmail tersebut.
5. Cadangan: mahasiswa yang tidak bisa mengakses email student → operator memverifikasi manual (mis. KTM) dan tercatat di audit log.

Pengamanan: kata sandi hanya disimpan sebagai hash; kredensial tidak masuk log/layar; pembatasan laju klaim per akun dan IP; pengiriman lewat antrean dengan batas laju; cek batas kirim penyedia SMTP. Alternatif lebih kuat di masa depan: login dengan akun Google kampus (REQ-62), sehingga tidak ada kata sandi yang dikirim.

## 7. Struktur folder (ARC-07)
```
app/
  Enums/  Models/  Policies/
  Http/Controllers/{Student,Supervisor,Reviewer,UniversityLecturer,Operator,SuperOperator}/
  Http/Requests/  Http/Middleware/(EnsureRole, PhaseGate)
  Services/
    ProposalWorkflowService  PreSubmissionCheckService  FormLockService
    ReviewerAssignmentService  ScoringService  FileStorageService
    AccountClaimService  NotificationService  DecisionBatchService
resources/views/{layouts,components,student,supervisor,reviewer,university,operator,super-operator}/
database/{migrations,seeders}/  tests/{Feature,Unit,E2E}/  docs/
```

## 8. Perubahan dari README awal (ARC-08)
| Sebelumnya | Sekarang |
|---|---|
| Satu tahap review substantif | Substantif awal → revisi → review final oleh reviewer berbeda |
| Validasi pembimbing sekali | Dua kali (pengajuan dan revisi) |
| Validasi akhir lewat Dosen | Peran terpisah: Dosen Univ, ditugaskan operator |
| Rubrik dan batas dana bawaan kode | Dikelola operator, berversi dan dikunci |
| Keputusan pimpinan per proposal | Keputusan per batch, peserta tercatat |
| Supabase | Disk lokal + MySQL |

## 9. Sumber aturan (ARC-09)
Panduan PKM 2026 = sumber utama. Bila tidak jelas, Panduan 2025 dan formulir Udayana dipakai. Aturan Udayana yang lebih ketat dari panduan nasional (mis. minimal 2 angkatan berbeda yang di panduan hanya "disarankan") tetap dipakai, dengan tingkat keparahan (blokir/peringatan) yang dapat diatur di konfigurasi.
