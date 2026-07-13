<?php

declare(strict_types=1);

namespace Happenv\FilamentTranslatable\Dto;

final readonly class Locale
{
    public string $flag;

    public string $label;

    public function __construct(
        public string $code,
        ?string $label = null,
        ?string $flag = null,
    ) {

        $this->label = $label ?? $code;
        $this->flag = $flag ?? 'vendor/filament-translatable/flags/' . $code . '.svg';
    }
}
