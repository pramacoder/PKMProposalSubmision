<?php

namespace Database\Seeders;

use App\Enums\ChecklistGroup;
use App\Models\ChecklistForm;
use App\Models\ChecklistItem;
use App\Models\Cycle;
use Illuminate\Database\Seeder;

class ChecklistFormSeeder extends Seeder
{
    /**
     * Daftar item berdasarkan checklist-administratif.md.
     * Kolom: [code, label, kind, applies_to, sort]
     * kind: auto = dicek otomatis sistem, manual = dicek reviewer admin
     */
    private array $items = [
        // ── Umum (berlaku untuk semua) ─────────────────────────────────
        ['ADM-01', 'Judul proposal tidak mengandung kata "implementasi", "penerapan", "sosialisasi", dll. untuk skema penelitian', 'manual', 'all', 1],
        ['ADM-02', 'Judul proposal sesuai dengan skema yang dipilih', 'manual', 'all', 2],
        ['ADM-03', 'Proposal ditulis menggunakan Bahasa Indonesia yang baik dan benar', 'manual', 'all', 3],
        ['ADM-04', 'Nama ketua dan anggota tidak ada yang merangkap di proposal lain dalam siklus yang sama', 'auto', 'all', 4],
        ['ADM-05', 'Dosen pembimbing memiliki NUPTK', 'auto', 'all', 5],
        ['ADM-06', 'Dosen pembimbing hanya membimbing ≤10 proposal dalam satu siklus', 'auto', 'all', 6],
        ['ADM-07', 'Ketua tim adalah mahasiswa aktif (bukan mahasiswa akhir untuk PKM-RE/RSH/K/KI/KC/VGK/PM/PI)', 'manual', 'all', 7],
        ['ADM-08', 'Anggota tim adalah mahasiswa aktif', 'manual', 'all', 8],
        ['ADM-09', 'Ketua berasal dari program studi yang relevan dengan skema', 'manual', 'all', 9],
        ['ADM-10', 'Identitas lengkap (nama, NIM, program studi, angkatan) tercantum', 'auto', 'all', 10],
        ['ADM-11', 'Tanda tangan ketua, anggota, dosen pembimbing, dan pimpinan PT ada', 'manual', 'all', 11],
        ['ADM-12', 'Setiap mahasiswa hanya ada di satu proposal PKM per siklus', 'auto', 'all', 12],
        ['ADM-13', 'Dosen pendamping memiliki NUPTK dan membimbing ≤10 proposal', 'auto', 'all', 13],
        ['ADM-14', 'Tema tematik dipilih (wajib semua skema termasuk AI dan GFT)', 'auto', 'all', 14],

        // ── Khusus skema pendanaan (funded) ────────────────────────────
        ['ADM-20', 'Format naskah sesuai panduan (font, margin, spasi)', 'manual', 'funded', 20],
        ['ADM-21', 'Jumlah halaman naskah sesuai ketentuan skema', 'manual', 'funded', 21],
        ['ADM-22', 'Daftar pustaka menggunakan format IEEE/Harvard/Vancouver (sesuai panduan)', 'manual', 'funded', 22],
        ['ADM-23', 'Bab 1 Pendahuluan ada dan memuat latar belakang, rumusan masalah, tujuan', 'manual', 'funded', 23],
        ['ADM-24', 'Bab 2 (Tinjauan Pustaka/Gambaran Umum) ada', 'manual', 'funded', 24],
        ['ADM-25', 'Bab 3 Metode Pelaksanaan ada', 'manual', 'funded', 25],
        ['ADM-26', 'Bab 4 Jadwal Kegiatan ada', 'manual', 'funded', 26],
        ['ADM-27', 'Daftar Pustaka ada', 'manual', 'funded', 27],
        ['ADM-51', 'Rencana Anggaran Biaya (RAB) ada dan diperinci', 'manual', 'funded', 28],
        ['ADM-52a', 'Komponen administrasi ≤20% dari total anggaran', 'auto', 'funded', 29],
        ['ADM-52b', 'Dana Belmawa dalam batas ketentuan (Rp 6–8 juta)', 'auto', 'funded', 30],
        ['ADM-59', 'Dokumen pernyataan originalitas ditandatangani ketua dan dosen', 'manual', 'funded', 31],
        ['ADM-60', 'Biodata ketua, anggota, dan dosen pembimbing terlampir', 'manual', 'funded', 32],
        ['ADM-61', 'Susunan organisasi tim dan pembagian tugas terlampir', 'manual', 'funded', 33],

        // ── Khusus PKM-KC/KI (ada lampiran tambahan) ───────────────────
        ['ADM-65', 'Gambaran produk/prototipe dilampirkan (khusus KC/KI)', 'manual', 'funded', 40],

        // ── PKM-AI ─────────────────────────────────────────────────────
        ['ADM-AI-01', 'Artikel ditulis dalam format jurnal ilmiah', 'manual', 'article_ai', 50],
        ['ADM-AI-02', 'Abstrak tersedia dalam Bahasa Indonesia dan Inggris', 'manual', 'article_ai', 51],
        ['ADM-AI-03', 'Jumlah halaman sesuai ketentuan PKM-AI', 'manual', 'article_ai', 52],
        ['ADM-AI-04', 'Daftar pustaka minimal 10 referensi ilmiah', 'manual', 'article_ai', 53],
        ['ADM-AI-05', 'Tema tematik dipilih (wajib)', 'auto', 'article_ai', 54],

        // ── PKM-GFT ────────────────────────────────────────────────────
        ['ADM-GFT-01', 'Gagasan ditulis sistematis: pendahuluan, gagasan, kesimpulan', 'manual', 'article_gft', 60],
        ['ADM-GFT-02', 'Jumlah halaman sesuai ketentuan PKM-GFT', 'manual', 'article_gft', 61],
        ['ADM-GFT-03', 'Referensi minimal 10 sumber ilmiah', 'manual', 'article_gft', 62],
        ['ADM-GFT-04', 'Tema tematik dipilih (wajib)', 'auto', 'article_gft', 63],
    ];

    public function run(): void
    {
        $cycles = Cycle::all();

        if ($cycles->isEmpty()) {
            $this->command->warn('Tidak ada siklus. Jalankan ThemeSeeder terlebih dahulu.');

            return;
        }

        foreach ($cycles as $cycle) {
            foreach (ChecklistGroup::cases() as $group) {
                $form = ChecklistForm::firstOrCreate(
                    ['cycle_id' => $cycle->id, 'checklist_group' => $group->value],
                    ['status' => 'draft']
                );

                foreach ($this->items as [$code, $label, $kind, $appliesTo, $sort]) {
                    ChecklistItem::updateOrCreate(
                        ['form_id' => $form->id, 'code' => $code],
                        [
                            'label' => $label,
                            'kind' => $kind,
                            'applies_to' => $appliesTo,
                            'sort' => $sort,
                        ]
                    );
                }
            }
        }

        $this->command->info('ChecklistForm dan item berhasil di-seed untuk semua siklus.');
    }
}
