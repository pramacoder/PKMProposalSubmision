<?php

namespace App\Services;

use App\Models\Proposal;

/**
 * Mengecek apakah proposal sudah memenuhi semua syarat sebelum di-submit
 * (RULE-23: Sistem otomatis mencegah submit jika data belum lengkap).
 */
class PreSubmissionCheckService
{
    /**
     * Memeriksa kesiapan proposal untuk di-submit.
     * Mengembalikan array pesan error jika ada yang belum lengkap, atau array kosong jika siap.
     *
     * @return string[]
     */
    public function check(Proposal $proposal): array
    {
        $errors = [];

        // 1. Data utama (judul, pembimbing, skema)
        if (empty($proposal->title)) {
            $errors[] = 'Judul proposal belum diisi.';
        }
        if (empty($proposal->scheme_id)) {
            $errors[] = 'Skema PKM belum dipilih.';
        }
        if (empty($proposal->supervisor_id)) {
            $errors[] = 'Dosen pendamping belum dipilih.';
        }

        // Jika skema belum ada, stop cek lebih lanjut karena sisanya butuh skema
        if (! $proposal->scheme) {
            return $errors;
        }

        $setting = $proposal->scheme->settingForCycle($proposal->cycle_id);
        if (! $setting) {
            $errors[] = 'Konfigurasi skema untuk siklus ini tidak ditemukan (hubungi admin).';

            return $errors;
        }

        // 2. Pendanaan (jika skema didanai)
        if ($proposal->scheme->is_funded) {
            $totalFund = $proposal->totalFunding();

            // Cek sumber dana Belmawa
            $belmawa = $proposal->fundings()->where('source', 'belmawa')->first();
            $belmawaAmount = $belmawa ? $belmawa->amount : 0;
            if ($belmawaAmount < $setting->min_belmawa || $belmawaAmount > $setting->max_belmawa) {
                $errors[] = sprintf(
                    'Dana Belmawa (Rp%s) di luar batas (Rp%s - Rp%s).',
                    number_format($belmawaAmount, 0, ',', '.'),
                    number_format($setting->min_belmawa, 0, ',', '.'),
                    number_format($setting->max_belmawa, 0, ',', '.')
                );
            }

            // Cek sumber dana PT (wajib maks Rp2jt)
            $pt = $proposal->fundings()->where('source', 'university')->first();
            $ptAmount = $pt ? $pt->amount : 0;
            if ($ptAmount < $setting->min_pt || $ptAmount > $setting->max_pt) {
                $errors[] = sprintf(
                    'Dana Perguruan Tinggi (Rp%s) di luar batas (Rp%s - Rp%s).',
                    number_format($ptAmount, 0, ',', '.'),
                    number_format($setting->min_pt, 0, ',', '.'),
                    number_format($setting->max_pt, 0, ',', '.')
                );
            }

            // Cek porsi administrasi
            $adminPercent = $proposal->adminCostPercent();
            if ($adminPercent > $setting->max_admin_percent) {
                $errors[] = "Biaya administrasi ({$adminPercent}%) melebihi batas maksimal ({$setting->max_admin_percent}%).";
            }
        }

        // 3. Durasi (start_date dan end_date)
        if ($proposal->scheme->is_funded) {
            if (! $proposal->start_date || ! $proposal->end_date) {
                $errors[] = 'Durasi pelaksanaan belum diatur (tanggal mulai & selesai).';
            } else {
                $months = $proposal->start_date->diffInMonths($proposal->end_date);
                if ($months < $setting->min_months || $months > $setting->max_months) {
                    $errors[] = "Durasi pelaksanaan ({$months} bulan) di luar batas yang diizinkan ({$setting->min_months}-{$setting->max_months} bulan).";
                }
            }
        }

        // 4. Kelengkapan File (DocumentRequirements)
        $requirements = $proposal->scheme->documentRequirements()->where('is_required', true)->get();
        $uploadedFileReqIds = $proposal->files()->pluck('requirement_id')->toArray();

        foreach ($requirements as $req) {
            if (! in_array($req->id, $uploadedFileReqIds, true)) {
                $errors[] = "Dokumen wajib belum diunggah: {$req->label}";
            }
        }

        return $errors;
    }
}
