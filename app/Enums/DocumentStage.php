<?php

namespace App\Enums;

enum DocumentStage: string
{
    case Submission = 'submission';
    case Revision = 'revision';
    case Final = 'final';

    public function label(): string
    {
        return match ($this) {
            self::Submission => 'Pengajuan',
            self::Revision => 'Revisi',
            self::Final => 'Revisi Akhir',
        };
    }
}
