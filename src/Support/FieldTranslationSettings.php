<?php

declare(strict_types=1);

namespace Happenv\FilamentTranslatable\Support;

use Closure;
use Filament\Forms\Components\Field;

/**
 * Stores per-field translation settings in the field's meta, so they survive cloning.
 *
 * @internal
 */
final class FieldTranslationSettings
{
    public const string META_KEY = 'filament-translatable';

    public static function addRequiredLocale(Field $field, string $locale, bool | Closure $condition): void
    {
        self::update($field, function (array $settings) use ($locale, $condition): array {
            $settings['requiredLocales'][$locale] = $condition;

            return $settings;
        });
    }

    public static function setRequiredDefaultLocale(Field $field, bool | Closure $condition): void
    {
        self::update($field, function (array $settings) use ($condition): array {
            $settings['requiredDefaultLocale'] = $condition;

            return $settings;
        });
    }

    public static function addDecorator(Field $field, string $locale, Closure $decorator): void
    {
        self::update($field, function (array $settings) use ($locale, $decorator): array {
            $settings['decorators'][$locale][] = $decorator;

            return $settings;
        });
    }

    public static function markTranslated(Field $field, string $attribute, string $locale): void
    {
        self::update($field, function (array $settings) use ($attribute, $locale): array {
            $settings['attribute'] = $attribute;
            $settings['locale'] = $locale;

            return $settings;
        });
    }

    /**
     * @return array<string, bool|Closure>
     */
    public static function getRequiredLocales(Field $field): array
    {
        return self::get($field)['requiredLocales'] ?? [];
    }

    public static function getRequiredDefaultLocale(Field $field): bool | Closure | null
    {
        return self::get($field)['requiredDefaultLocale'] ?? null;
    }

    /**
     * @return list<Closure>
     */
    public static function getDecorators(Field $field, string $locale): array
    {
        return self::get($field)['decorators'][$locale] ?? [];
    }

    public static function getTranslatedAttribute(Field $field): ?string
    {
        return self::get($field)['attribute'] ?? null;
    }

    public static function getTranslatedLocale(Field $field): ?string
    {
        return self::get($field)['locale'] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    private static function get(Field $field): array
    {
        return $field->getMeta(self::META_KEY) ?? [];
    }

    /**
     * @param  Closure(array<string, mixed>): array<string, mixed>  $callback
     */
    private static function update(Field $field, Closure $callback): void
    {
        $field->meta(self::META_KEY, $callback(self::get($field)));
    }
}
