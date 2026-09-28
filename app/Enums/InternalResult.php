<?php

namespace App\Enums;

enum InternalResult: string
{
    case Passed = 'passed';
    case NotPassed = 'not_passed';

    public function label(): string
    {
        return match ($this) {
            self::Passed => 'Lolos',
            self::NotPassed => 'Tidak Lolos',
        };
    }
}
