<?php

namespace App\Enums;

enum FormStatus: string
{
    case Draft = 'draft';
    case Confirmed = 'confirmed';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf',
            self::Confirmed => 'Dikonfirmasi (Terkunci)',
        };
    }

    public function isLocked(): bool
    {
        return $this === self::Confirmed;
    }
}
