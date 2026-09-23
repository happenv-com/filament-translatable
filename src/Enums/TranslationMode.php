<?php

declare(strict_types=1);

namespace Happenv\FilamentTranslatable\Enums;

enum TranslationMode: string
{
    case Spatie = 'spatie';

    case Astrotomic = 'astrotomic';
}
