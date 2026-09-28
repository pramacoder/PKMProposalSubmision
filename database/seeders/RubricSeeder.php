<?php

namespace Database\Seeders;

use App\Enums\SchemeCode;
use App\Models\Cycle;
use App\Models\Rubric;
use App\Models\RubricCriterion;
use App\Models\Scheme;
use Illuminate\Database\Seeder;

class RubricSeeder extends Seeder
{
    public function run(): void
    {
        $cycles = Cycle::all();

        if ($cycles->isEmpty()) {
            $this->command->warn('Tidak ada siklus. Jalankan ThemeSeeder terlebih dahulu.');

            return;
        }

        $rubricsData = $this->getRubricsData();

        foreach ($cycles as $cycle) {
            foreach ($rubricsData as $schemeCodeValue => $criteriaList) {
                $scheme = Scheme::where('code', $schemeCodeValue)->first();

                if (! $scheme) {
                    continue;
                }

                $rubric = Rubric::firstOrCreate(
                    ['cycle_id' => $cycle->id, 'scheme_id' => $scheme->id],
                    ['status' => 'draft']
                );

                foreach ($criteriaList as $index => $c) {
                    RubricCriterion::updateOrCreate(
                        ['rubric_id' => $rubric->id, 'label' => $c['label']],
                        [
                            'group_label' => $c['group_label'] ?? null,
                            'weight' => $c['weight'],
                            'sort' => $index + 1,
                        ]
                    );
                }
            }
        }

        $this->command->info('Rubrik penilaian substantif berhasil di-seed.');
    }

    private function getRubricsData(): array
    {
        return [
            SchemeCode::PkmRe->value => [
                ['group_label' => 'Kreativitas', 'label' => 'Gagasan (orisinalitas, unik dan bermanfaat)', 'weight' => 15],
                ['group_label' => 'Kreativitas', 'label' => 'Penyajian rumusan masalah (data lengkap, fokus dan atraktif)', 'weight' => 15],
                ['group_label' => 'Kreativitas', 'label' => 'Perbandingan dengan riset terdahulu (kebaruan)', 'weight' => 10],
                ['group_label' => 'Kesesuaian dan Kemutakhiran Metode Riset', 'label' => 'Kesesuaian dan Kemutakhiran Metode Riset', 'weight' => 15],
                ['group_label' => 'Potensi Program', 'label' => 'Kontribusi Perkembangan Ilmu dan Teknologi', 'weight' => 10],
                ['group_label' => 'Potensi Program', 'label' => 'Sintesis Telaah Literatur, Potensi dan Prediksi Hasil Riset', 'weight' => 15],
                ['group_label' => 'Potensi Program', 'label' => 'Kemanfaatan', 'weight' => 10],
                ['group_label' => 'Penjadwalan Kegiatan dan Personalia', 'label' => 'Penjadwalan Kegiatan dan Personalia (lengkap, jelas, waktu, dan personalianya sesuai)', 'weight' => 5],
                ['group_label' => 'Penyusunan Anggaran Biaya', 'label' => 'Penyusunan Anggaran Biaya (lengkap, rinci, wajar dan jelas peruntukannya)', 'weight' => 5],
            ],
            SchemeCode::PkmRsh->value => [
                ['group_label' => 'Kreativitas', 'label' => 'Gagasan (orisinalitas, unik dan bermanfaat)', 'weight' => 15],
                ['group_label' => 'Kreativitas', 'label' => 'Penyajian rumusan masalah (data lengkap, fokus dan atraktif)', 'weight' => 15],
                ['group_label' => 'Kreativitas', 'label' => 'Perbandingan dengan riset terdahulu (state of the art)', 'weight' => 10],
                ['group_label' => 'Kesesuaian dan Kemutakhiran Metode Riset', 'label' => 'Kesesuaian dan Kemutakhiran Metode Riset', 'weight' => 15],
                ['group_label' => 'Potensi Program', 'label' => 'Kontribusi Perkembangan Ilmu dan Teknologi', 'weight' => 10],
                ['group_label' => 'Potensi Program', 'label' => 'Sintesis Telaah Literatur, Potensi dan Prediksi Hasil Riset', 'weight' => 15],
                ['group_label' => 'Potensi Program', 'label' => 'Kemanfaatan', 'weight' => 10],
                ['group_label' => 'Penjadwalan Kegiatan dan Personalia', 'label' => 'Penjadwalan Kegiatan dan Personalia (lengkap, jelas, waktu, dan personalianya sesuai)', 'weight' => 5],
                ['group_label' => 'Penyusunan Anggaran Biaya', 'label' => 'Penyusunan Anggaran Biaya (lengkap, rinci, wajar dan jelas peruntukannya)', 'weight' => 5],
            ],
            SchemeCode::PkmPm->value => [
                ['group_label' => 'Kreativitas', 'label' => 'Perumusan Masalah', 'weight' => 10],
                ['group_label' => 'Kreativitas', 'label' => 'Ketepatan Solusi (fokus dan atraktif)', 'weight' => 20],
                ['group_label' => 'Ketepatan Masyarakat Mitra dan Kondisi Existing Mitra', 'label' => 'Ketepatan Masyarakat Mitra dan Kondisi Existing Mitra', 'weight' => 15],
                ['group_label' => 'Potensi Program', 'label' => 'Potensi Nilai Tambah untuk Mitra Program', 'weight' => 25],
                ['group_label' => 'Potensi Program', 'label' => 'Potensi Keberlanjutan Program', 'weight' => 20],
                ['group_label' => 'Penjadwalan Kegiatan dan Personalia', 'label' => 'Penjadwalan Kegiatan dan Personalia (lengkap, jelas, waktu, dan personalianya sesuai)', 'weight' => 5],
                ['group_label' => 'Penyusunan Anggaran Biaya', 'label' => 'Penyusunan Anggaran Biaya (lengkap, rinci, wajar dan jelas peruntukannya)', 'weight' => 5],
            ],
            SchemeCode::PkmPi->value => [
                ['group_label' => 'Kreativitas', 'label' => 'Identifikasi Permasalahan atau Kebutuhan Mitra', 'weight' => 10],
                ['group_label' => 'Kreativitas', 'label' => 'Ketepatan Solusi yang Ditawarkan', 'weight' => 20],
                ['group_label' => 'Ketepatan Mitra Program', 'label' => 'Ketepatan Mitra Program', 'weight' => 15],
                ['group_label' => 'Potensi Program', 'label' => 'Potensi Nilai Tambah untuk Mitra Program', 'weight' => 25],
                ['group_label' => 'Potensi Program', 'label' => 'Potensi Keberlanjutan Program', 'weight' => 20],
                ['group_label' => 'Penjadwalan Kegiatan dan Personalia', 'label' => 'Penjadwalan Kegiatan dan Personalia (lengkap, jelas, waktu, dan personalianya sesuai)', 'weight' => 5],
                ['group_label' => 'Penyusunan Anggaran Biaya', 'label' => 'Penyusunan Anggaran Biaya (lengkap, rinci, wajar dan jelas peruntukannya)', 'weight' => 5],
            ],
            SchemeCode::PkmKc->value => [
                ['group_label' => 'Kreativitas', 'label' => 'Gagasan (orisinalitas, unik dan manfaat masa depan)', 'weight' => 20],
                ['group_label' => 'Kreativitas', 'label' => 'Kemutakhiran ipteks yang diadopsi', 'weight' => 20],
                ['group_label' => 'Kesesuaian Tahap Pelaksanaan', 'label' => 'Kesesuaian Tahap Pelaksanaan', 'weight' => 15],
                ['group_label' => 'Potensi Program', 'label' => 'Kontribusi produk luaran terhadap solusi permasalahan dan perkembangan IPTEKS', 'weight' => 25],
                ['group_label' => 'Potensi Program', 'label' => 'Potensi Publikasi Artikel Ilmiah/Kekayaan Intelektual', 'weight' => 10],
                ['group_label' => 'Penjadwalan Kegiatan dan Personalia', 'label' => 'Penjadwalan Kegiatan dan Personalia (Lengkap, Jelas, Waktu, dan Personalianya Sesuai)', 'weight' => 5],
                ['group_label' => 'Penyusunan Anggaran Biaya', 'label' => 'Penyusunan Anggaran Biaya (Lengkap, Rinci, Wajar dan Jelas peruntukannya)', 'weight' => 5],
            ],
            SchemeCode::PkmK->value => [
                ['group_label' => 'Kreativitas', 'label' => 'Gagasan Usaha (analisis peluang pasar, dukungan sumber data yang berkualitas)', 'weight' => 15],
                ['group_label' => 'Kreativitas', 'label' => 'Keunggulan Produk (berbasis iptek, unik, dan bermanfaat)', 'weight' => 20],
                ['group_label' => 'Rancangan Usaha', 'label' => 'Rancangan Usaha', 'weight' => 20],
                ['group_label' => 'Potensi Program', 'label' => 'Potensi Pelaksanaan dan Perolehan Profit', 'weight' => 20],
                ['group_label' => 'Potensi Program', 'label' => 'Potensi Keberlanjutan Usaha', 'weight' => 15],
                ['group_label' => 'Penjadwalan Kegiatan dan Personalia', 'label' => 'Penjadwalan Kegiatan dan Personalia (lengkap, jelas, waktu, dan personalianya sesuai)', 'weight' => 5],
                ['group_label' => 'Penyusunan Anggaran Biaya', 'label' => 'Penyusunan Anggaran Biaya (lengkap, rinci, wajar dan jelas peruntukannya)', 'weight' => 5],
            ],
            SchemeCode::PkmKi->value => [
                ['group_label' => 'Kreativitas', 'label' => 'Urgensi Permasalahan, Cakupan Pengguna', 'weight' => 15],
                ['group_label' => 'Kreativitas', 'label' => 'Kreativitas Gagasan Solusi (orisinalitas, problem based, specific, measurable)', 'weight' => 25],
                ['group_label' => 'Kesesuaian Tahap Pelaksanaan', 'label' => 'Kesesuaian Tahap Pelaksanaan', 'weight' => 15],
                ['group_label' => 'Potensi Produk', 'label' => 'Dampak ekonomi nasional', 'weight' => 10],
                ['group_label' => 'Potensi Produk', 'label' => 'Ketepatan Iptek, Standar, Regulasi dan Metode yang Digunakan', 'weight' => 25],
                ['group_label' => 'Penjadwalan Kegiatan dan Personalia', 'label' => 'Penjadwalan Kegiatan dan Personalia (lengkap, jelas, dan personalianya sesuai)', 'weight' => 5],
                ['group_label' => 'Penyusunan Anggaran Biaya', 'label' => 'Penyusunan Anggaran Biaya (lengkap, rinci, wajar dan jelas peruntukannya)', 'weight' => 5],
            ],
            SchemeCode::PkmVgk->value => [
                ['group_label' => 'Kreativitas', 'label' => 'Kreativitas Gagasan (ketepatan solusi, komprehensif, unik, originalitas, dan konstruktif)', 'weight' => 20],
                ['group_label' => 'Kreativitas', 'label' => 'Kreativitas Komunikasi pada Skenario Konten Video (informatif, kejelasan alur, unik, objektif, & originalitas)', 'weight' => 20],
                ['group_label' => 'Kesesuaian Tahap Pelaksanaan', 'label' => 'Kesesuaian Tahap Pelaksanaan', 'weight' => 15],
                ['group_label' => 'Potensi Program', 'label' => 'Kontribusi Gagasan Terhadap Isu Keprihatinan Bangsa', 'weight' => 15],
                ['group_label' => 'Potensi Program', 'label' => 'Potensi Efektivitas Informasi Pada Skenario Video', 'weight' => 15],
                ['group_label' => 'Sumber Informasi', 'label' => 'Sumber Informasi', 'weight' => 5],
                ['group_label' => 'Penjadwalan Kegiatan dan Personalia', 'label' => 'Penjadwalan Kegiatan dan Personalia (lengkap, jelas, waktu, dan personalianya sesuai)', 'weight' => 5],
                ['group_label' => 'Penyusunan Anggaran Biaya', 'label' => 'Penyusunan Anggaran Biaya (lengkap, rinci, wajar dan jelas peruntukannya)', 'weight' => 5],
            ],
            SchemeCode::PkmAi->value => [
                ['group_label' => 'JUDUL', 'label' => 'Kesesuaian isi dan judul artikel.', 'weight' => 5],
                ['group_label' => 'ABSTRAK / ABSTRACT', 'label' => 'Latar belakang, Tujuan, Metode, Hasil, Kesimpulan, Kata kunci.', 'weight' => 10],
                ['group_label' => 'PENDAHULUAN', 'label' => 'Persoalan yang mendasari pelaksanaan dan uraian dasar keilmuan yang mendukung kemutakhiran substansi kajian.', 'weight' => 15],
                ['group_label' => 'METODE', 'label' => 'Kesesuaian dengan persoalan yang telah diselesaikan, Pengembangan metode baru, Penggunaan metode yang sudah ada.', 'weight' => 25],
                ['group_label' => 'HASIL DAN PEMBAHASAN', 'label' => 'Kumpulan dan kejelasan penampilan data Proses/teknik pengolahan data, Ketajaman analisis dan sintesis data, Perbandingan hasil dengan hipotesis atau hasil sejenis sebelumnya.', 'weight' => 30],
                ['group_label' => 'KESIMPULAN', 'label' => 'Tingkat ketercapaian hasil dengan tujuan.', 'weight' => 10],
                ['group_label' => 'DAFTAR PUSTAKA', 'label' => 'Ditulis dengan sistem Harvard (nama, tahun), Sesuai dengan uraian sitasi, Kemutakhiran Pustaka.', 'weight' => 5],
            ],
            SchemeCode::PkmGft->value => [
                ['group_label' => 'Format Makalah', 'label' => 'Tata tulis, Penggunaan Bahasa Indonesia, Kesesuaian dengan format penulisan', 'weight' => 10],
                ['group_label' => 'Gagasan', 'label' => 'Kreativitas gagasan, Kelayakan realisasi, Ruang lingkup permasalahan', 'weight' => 35],
                ['group_label' => 'Tahapan solusi yang ditawarkan dan prediksi keberhasilan', 'label' => 'Ketepatan solusi, Pemanfaatan iptek, Keterlibatan pihak terkait, Jangka waktu', 'weight' => 30],
                ['group_label' => 'Sumber informasi', 'label' => 'Kesesuaian sumber informasi dengan gagasan, Akurasi dan kemutakhiran', 'weight' => 15],
                ['group_label' => 'Kesimpulan', 'label' => 'Prediksi dampak terealisasikannya gagasan', 'weight' => 10],
            ],
        ];
    }
}
