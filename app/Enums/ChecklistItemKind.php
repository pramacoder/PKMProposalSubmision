<?php

namespace App\Enums;

enum ChecklistItemKind: string
{
    case Auto = 'auto';
    case Manual = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::Auto => 'Otomatis',
            self::Manual => 'Manual',
        };
    }
}
