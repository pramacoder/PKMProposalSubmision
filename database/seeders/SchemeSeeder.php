<?php

namespace Database\Seeders;

use App\Enums\ChecklistGroup;
use App\Enums\SchemeCode;
use App\Models\Scheme;
use Illuminate\Database\Seeder;

class SchemeSeeder extends Seeder
{
    public function run(): void
    {
        $schemes = [
            [SchemeCode::PkmRe,  'Riset Eksakta',                       true,  ChecklistGroup::Funded],
            [SchemeCode::PkmRsh, 'Riset Sosial Humaniora',              true,  ChecklistGroup::Funded],
            [SchemeCode::PkmK,   'Kewirausahaan',                       true,  ChecklistGroup::Funded],
            [SchemeCode::PkmKi,  'Karya Inovatif',                      true,  ChecklistGroup::Funded],
            [SchemeCode::PkmKc,  'Karsa Cipta',                         true,  ChecklistGroup::Funded],
            [SchemeCode::PkmVgk, 'Video Gagasan Konstruktif',           true,  ChecklistGroup::Funded],
            [SchemeCode::PkmPm,  'Pengabdian kepada Masyarakat',        true,  ChecklistGroup::Funded],
            [SchemeCode::PkmPi,  'Penerapan Iptek',                     true,  ChecklistGroup::Funded],
            [SchemeCode::PkmAi,  'Artikel Ilmiah',                      false, ChecklistGroup::ArticleAi],
            [SchemeCode::PkmGft, 'Gagasan Futuristik Tertulis',         false, ChecklistGroup::ArticleGft],
        ];

        foreach ($schemes as [$code, $name, $isFunded, $group]) {
            Scheme::updateOrCreate(
                ['code' => $code->value],
                [
                    'name' => $name,
                    'is_funded' => $isFunded,
                    'checklist_group' => $group->value,
                ]
            );
        }
    }
}
