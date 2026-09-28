<?php

namespace App\Enums;

enum FundingSource: string
{
    case Belmawa = 'belmawa';
    case University = 'university';
    case Partner = 'partner';

    public function label(): string
    {
        return match ($this) {
            self::Belmawa => 'Belmawa',
            self::University => 'Perguruan Tinggi',
            self::Partner => 'Mitra / Sponsor',
        };
    }

    public function isRequired(): bool
    {
        return match ($this) {
            self::Belmawa, self::University => true,
            self::Partner => false,
        };
    }
}
