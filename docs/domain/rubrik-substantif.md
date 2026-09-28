# Rubrik Substantif — Verifikasi

Sumber utama: berkas `penilaian.md` milik Anda (simpan di folder ini). Seeder membaca isi rubrik dari sumber tersebut, bukan dari berkas ini.

## Hasil pemeriksaan bobot
| Skema | Jumlah kriteria | Total bobot | Catatan |
|---|---|---|---|
| PKM-RE | 9 | 100 | |
| PKM-RSH | 9 | 100 | |
| PKM-PM | 7 | 100 | |
| PKM-PI | 7 | 100 | |
| PKM-KC | 7 | 100 | |
| PKM-K | 7 | 100 | |
| PKM-KI | 7 | 100 | Kriteria 3 = Potensi Produk: 3a "dampak ekonomi nasional" (bobot 10) + 3b "Ketepatan Iptek, Standar, Regulasi dan Metode" (bobot 25); kriteria 4 = Penjadwalan. **Dikonfirmasi.** |
| PKM-VGK | 8 | 100 | Ada kriteria "Sumber Informasi" (5) |
| PKM-AI | 7 | 100 | Menilai artikel, tanpa penjadwalan/anggaran |
| PKM-GFT | 5 | 100 | Kriteria memiliki sub-butir a–d tanpa bobot sendiri |

## Catatan desain
- Rubrik disimpan di `rubrics` dan `rubric_criteria` dengan versi, agar revisi rubrik tahun depan tidak mengubah hasil lama.
- Skala skor: 1, 2, 3, 5, 6, 7 (angka 4 tidak ada — format baku kementerian, **sudah dikonfirmasi**).
- PKM-GFT: sub-butir (a, b, c…) ditampilkan sebagai petunjuk di bawah kriteria; skor diberikan per kriteria bernomor.
- PKM-KI baris 3: baris "Ketepatan Iptek, Standar, Regulasi dan Metode" (bobot 25) ditafsirkan sebagai kriteria 3b; pastikan penafsiran 3a/3b sesuai panduan resmi sebelum seeder ditulis.
