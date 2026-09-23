<?php

declare(strict_types=1);

namespace Happenv\FilamentTranslatable\Drivers;

use Illuminate\Database\Eloquent\Model;

final class SpatieDriver implements TranslationDriver
{
    public function getFieldName(string $attribute, string $locale): string
    {
        return "{$attribute}.{$locale}";
    }

    public function getTranslationFromRecord(Model $record, string $attribute, string $locale): mixed
    {
        if (method_exists($record, 'getTranslation') && method_exists($record, 'isTranslatableAttribute') && $record->isTranslatableAttribute($attribute)) {
            $value = $record->getTranslation($attribute, $locale, false);

            return $value === '' ? null : $value;
        }

        return data_get($record->getAttribute($attribute), $locale);
    }
}
