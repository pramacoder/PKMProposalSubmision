<?php

namespace App\Enums;

enum SchemeCode: string
{
    case PkmRe = 'PKM-RE';
    case PkmRsh = 'PKM-RSH';
    case PkmK = 'PKM-K';
    case PkmKi = 'PKM-KI';
    case PkmKc = 'PKM-KC';
    case PkmVgk = 'PKM-VGK';
    case PkmPm = 'PKM-PM';
    case PkmPi = 'PKM-PI';
    case PkmAi = 'PKM-AI';
    case PkmGft = 'PKM-GFT';

    public function label(): string
    {
        return match ($this) {
            self::PkmRe => 'Riset Eksakta',
            self::PkmRsh => 'Riset Sosial Humaniora',
            self::PkmK => 'Kewirausahaan',
            self::PkmKi => 'Karya Inovatif',
            self::PkmKc => 'Karsa Cipta',
            self::PkmVgk => 'Video Gagasan Konstruktif',
            self::PkmPm => 'Pengabdian kepada Masyarakat',
            self::PkmPi => 'Penerapan Iptek',
            self::PkmAi => 'Artikel Ilmiah',
            self::PkmGft => 'Gagasan Futuristik Tertulis',
        };
    }

    public function isFunded(): bool
    {
        return ! in_array($this, [self::PkmAi, self::PkmGft]);
    }

    public function checklistGroup(): ChecklistGroup
    {
        return match ($this) {
            self::PkmAi => ChecklistGroup::ArticleAi,
            self::PkmGft => ChecklistGroup::ArticleGft,
            default => ChecklistGroup::Funded,
        };
    }

    /** Judul Bab 2 sesuai Panduan PKM 2026. */
    public function chapter2Title(): string
    {
        return match ($this) {
            self::PkmK => 'Gambaran Umum Rencana Usaha',
            self::PkmPm => 'Gambaran Umum Masyarakat Mitra',
            self::PkmVgk => 'Gagasan',
            default => 'Tinjauan Pustaka',
        };
    }
}
