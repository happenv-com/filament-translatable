<?php

use Happenv\FilamentTranslatable\Enums\TranslationMode;
use Happenv\FilamentTranslatable\Tests\TestCase;

pest()->extend(TestCase::class)->in('Unit', 'Feature');

dataset('drivers', [
    'spatie' => [TranslationMode::Spatie],
    'astrotomic' => [TranslationMode::Astrotomic],
]);
