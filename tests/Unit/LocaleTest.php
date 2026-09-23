<?php

use Happenv\FilamentTranslatable\Dto\Locale;

it('normalizes a list of codes', function (): void {
    $locales = Locale::collect(['en', 'pl']);

    expect(array_keys($locales))->toBe(['en', 'pl'])
        ->and($locales['pl']->label)->toBe('pl')
        ->and($locales['pl']->flag)->toBe('vendor/filament-translatable/flags/pl.svg');
});

it('normalizes a code => label map', function (): void {
    $locales = Locale::collect(['en' => 'English', 'pl' => 'Polski']);

    expect($locales['pl']->code)->toBe('pl')
        ->and($locales['pl']->label)->toBe('Polski');
});

it('accepts Locale instances and collections', function (): void {
    $locales = Locale::collect(collect([new Locale('de', 'Deutsch', 'flags/de.png'), 'fr']));

    expect(array_keys($locales))->toBe(['de', 'fr'])
        ->and($locales['de']->flag)->toBe('flags/de.png');
});

it('keys region locales by their code', function (): void {
    expect(array_keys(Locale::collect(['pt-BR', 'en_US'])))->toBe(['pt-BR', 'en_US']);
});
