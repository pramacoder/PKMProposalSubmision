<?php

namespace Database\Seeders;

use App\Models\Cycle;
use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    /**
     * 10 Tema PKM Tematik 2026.
     * Sumber: Panduan Umum PKM 2026 — wajib untuk semua skema termasuk AI dan GFT (ADM-14, DEC-22).
     */
    public function run(): void
    {
        $themes = [
            'Ketahanan Pangan dan Pertanian Berkelanjutan',
            'Energi Terbarukan dan Transisi Energi',
            'Perubahan Iklim dan Lingkungan Hidup',
            'Kesehatan Masyarakat dan Gizi',
            'Pendidikan Berkualitas dan Inovasi Pembelajaran',
            'Ekonomi Kreatif, Digital, dan UMKM',
            'Teknologi Informasi dan Kecerdasan Buatan',
            'Infrastruktur dan Tata Ruang Kota Berkelanjutan',
            'Sosial, Hukum, dan Budaya',
            'Maritim, Kelautan, dan Sumber Daya Alam',
        ];

        // Seed untuk semua siklus aktif; jika belum ada siklus, buat placeholder 2026
        $cycles = Cycle::all();

        if ($cycles->isEmpty()) {
            $cycle = Cycle::create([
                'year' => 2026,
                'name' => 'PKM 2026',
                'is_active' => true,
            ]);
            $cycles = collect([$cycle]);
        }

        foreach ($cycles as $cycle) {
            foreach ($themes as $index => $name) {
                Theme::updateOrCreate(
                    ['cycle_id' => $cycle->id, 'sort' => $index + 1],
                    ['name' => $name]
                );
            }
        }
    }
}
