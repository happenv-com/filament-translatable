<?php

declare(strict_types=1);

namespace Happenv\FilamentTranslatable\Drivers;

use Illuminate\Database\Eloquent\Model;

interface TranslationDriver
{
    /**
     * Name (and state path) of the field holding the attribute's translation for the locale.
     */
    public function getFieldName(string $attribute, string $locale): string;

    /**
     * The stored translation, or null when the record has none for the locale (no fallback).
     */
    public function getTranslationFromRecord(Model $record, string $attribute, string $locale): mixed;
}
