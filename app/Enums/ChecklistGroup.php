<?php

namespace App\Enums;

enum ChecklistGroup: string
{
    case Funded = 'funded';
    case ArticleAi = 'article_ai';
    case ArticleGft = 'article_gft';

    public function label(): string
    {
        return match ($this) {
            self::Funded => '8 Skema Pendanaan',
            self::ArticleAi => 'PKM-AI',
            self::ArticleGft => 'PKM-GFT',
        };
    }
}
