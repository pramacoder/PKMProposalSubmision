# Sistem Pengajuan Proposal PKM — Indeks Dokumen

Versi dokumen: 0.3 (draf) · Diperbarui: 2026-09-28

## Cara memakai di Antigravity
1. **Awal setiap sesi:** lampirkan `00-INDEX.md`, `Rules.md`, `Memory.md`.
2. **Sebelum membangun sebuah fase:** lampirkan bagian fase itu dari `Phases.md` + dokumen domain yang relevan.
3. **Setelah fase lulus tes:** centang `Phases.md`, tambahkan entri di `Memory.md`.
4. Jika ada keputusan baru, catat sebagai `DEC-xx` di `PRD.md` (bagian Keputusan) lalu ringkas di `Memory.md`.

**Sumber aturan:** Panduan PKM 2026 utama; Panduan 2025 dan formulir Udayana sebagai cadangan (RULE-45).

## Dokumen inti
| Berkas | Isi | Muat kapan |
|---|---|---|
| [PRD.md](PRD.md) | Apa yang dibangun, untuk siapa, fitur (REQ-xx), keputusan terbuka (DEC-xx) | Awal proyek, saat menambah fitur |
| [Architecture.md](Architecture.md) | Stack, alur status, skema database, peran & hak akses, struktur folder | Setiap fase teknis |
| [Rules.md](Rules.md) | Yang dipakai dan dihindari (RULE-xx), protokol kerja agen | **Setiap sesi** |
| [Phases.md](Phases.md) | Fase 0–8 beserta kriteria lulus (PHx-xx) | Setiap fase |
| [Design.md](Design.md) | Prinsip UX (UX-xx), token warna/tipografi, daftar layar per peran | Fase UI dan Fase 6 |
| [Memory.md](Memory.md) | Log keputusan dan progres | **Setiap sesi** |

## Dokumen domain (aturan PKM)
| Berkas | Isi |
|---|---|
| [domain/alur-bisnis.md](domain/alur-bisnis.md) | Alur bisnis resmi (diagram) dan celah yang ditemukan |
| [domain/panduan-pkm-2026.md](domain/panduan-pkm-2026.md) | Struktur proposal, lampiran wajib, ketentuan PKM-VGK |
| [domain/skema-pkm.md](domain/skema-pkm.md) | 10 skema, aturan pendanaan, skala skor |
| [domain/checklist-administratif.md](domain/checklist-administratif.md) | Item review administratif (ADM-xx), otomatis vs manual |
| [domain/rubrik-substantif.md](domain/rubrik-substantif.md) | Verifikasi rubrik penilaian per skema. Sumber utama: `domain/penilaian.md` (berkas Anda) |

## Konvensi ID (untuk pencarian dengan Ctrl+F / grep)
`REQ-` fitur · `DEC-` keputusan · `ARC-` arsitektur · `RULE-` aturan · `PHx-` kriteria fase · `UX-` prinsip UX · `ADM-` item administratif
