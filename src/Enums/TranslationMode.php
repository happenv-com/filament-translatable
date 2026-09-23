<?php

declare(strict_types=1);

namespace Happenv\FilamentTranslatable\Enums;

use Happenv\FilamentTranslatable\Drivers\AstrotomicDriver;
use Happenv\FilamentTranslatable\Drivers\SpatieDriver;
use Happenv\FilamentTranslatable\Drivers\TranslationDriver;

enum TranslationMode: string
{
    case Spatie = 'spatie';

    case Astrotomic = 'astrotomic';

    public function driver(): TranslationDriver
    {
        return match ($this) {
            self::Spatie => new SpatieDriver,
            self::Astrotomic => new AstrotomicDriver,
        };
    }
}
