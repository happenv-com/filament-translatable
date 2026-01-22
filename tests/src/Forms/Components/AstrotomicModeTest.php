<?php

use Webard\FilamentTranslatable\Enums\TranslationMode;
use Webard\FilamentTranslatable\Tests\Forms\Fixtures\TestComponentWithAstrotomic;
use Webard\FilamentTranslatable\Tests\TestCase;

use function Pest\Livewire\livewire;

uses(TestCase::class);

it('uses astrotomic translation mode', function (): void {
    $locales = ['en', 'fr'];

    $component = livewire(TestComponentWithAstrotomic::class, [
        'locales' => $locales,
        'exclude' => [],
    ]);

    $component->assertSchemaComponentExists('translations::data::tabs', checkComponentUsing: function ($translations): true {
        expect($translations->getTranslationMode())->toBe(TranslationMode::Astrotomic);

        return true;
    });
});

it('creates fields with colon separator for astrotomic mode', function (): void {
    $locales = ['en', 'fr'];

    $component = livewire(TestComponentWithAstrotomic::class, [
        'locales' => $locales,
        'exclude' => [],
    ]);

    // In Astrotomic mode, field names should use colon separator (title:en instead of title.en)
    // But the state path is still using dots for Livewire
    $schemaInstance = $component->instance()->form;
    $keys = array_keys($schemaInstance->getFlatComponents(withHidden: true));

    // Field names in Astrotomic mode use colons
    expect($keys)->toContain('title:en')
        ->and($keys)->toContain('title:fr')
        ->and($keys)->toContain('content:en')
        ->and($keys)->toContain('content:fr');
});

it('can fill and assert data in astrotomic mode', function (): void {
    $locales = ['en', 'fr'];

    $data = [
        'title' => ['en' => 'English Title', 'fr' => 'French Title'],
        'content' => ['en' => 'English Content', 'fr' => 'French Content'],
    ];

    livewire(TestComponentWithAstrotomic::class, [
        'locales' => $locales,
        'exclude' => [],
    ])
        ->fillForm($data)
        ->assertSchemaStateSet($data);
});
