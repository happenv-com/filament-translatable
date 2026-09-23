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

    /**
     * @param  iterable<int|string, string|Locale|null>  $locales
     * @return array<string, Locale>
     */
    public static function collect(iterable $locales): array
    {
        $collected = [];

        foreach ($locales as $key => $value) {
            $locale = match (true) {
                $value instanceof self => $value,
                is_string($key) => new self($key, $value),
                default => new self((string) $value),
            };

            $collected[$locale->code] = $locale;
        }

        return $collected;
    }
}
