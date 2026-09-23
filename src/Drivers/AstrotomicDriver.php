<?php

declare(strict_types=1);

namespace Happenv\FilamentTranslatable\Drivers;

use Illuminate\Database\Eloquent\Model;

final class AstrotomicDriver implements TranslationDriver
{
    public function getFieldName(string $attribute, string $locale): string
    {
        return "{$attribute}:{$locale}";
    }

    public function getTranslationFromRecord(Model $record, string $attribute, string $locale): mixed
    {
        if (! method_exists($record, 'translate')) {
            return null;
        }

        return $record->translate($locale, false)?->getAttribute($attribute);
    }
}
