<?php

use Webard\FilamentTranslatable\Enums\TranslationMode;
use Webard\FilamentTranslatable\Tests\Forms\Fixtures\TestComponentWithTranslate;
use Webard\FilamentTranslatable\Tests\TestCase;

use function Pest\Livewire\livewire;

uses(TestCase::class);

it('uses spatie translation mode by default', function (): void {
    $locales = ['en', 'fr'];

    $component = livewire(TestComponentWithTranslate::class, [
        'locales' => $locales,
        'exclude' => [],
    ]);

    $component->assertSchemaComponentExists('translations::data::tabs', checkComponentUsing: function ($translations): true {
        expect($translations->getTranslationMode())->toBe(TranslationMode::Spatie);

        return true;
    });
});

it('creates fields with dot separator for spatie mode', function (): void {
    $locales = ['en', 'fr'];

    $component = livewire(TestComponentWithTranslate::class, [
        'locales' => $locales,
        'exclude' => [],
    ]);

    // In Spatie mode, field names should use dot separator (title.en)
    $schemaInstance = $component->instance()->form;
    $keys = array_keys($schemaInstance->getFlatComponents(withHidden: true));

    // Field names in Spatie mode use dots
    expect($keys)->toContain('title.en')
        ->and($keys)->toContain('title.fr')
        ->and($keys)->toContain('content.en')
        ->and($keys)->toContain('content.fr');
});

it('can fill and assert data in spatie mode', function (): void {
    $locales = ['en', 'fr'];

    $data = [
        'title' => ['en' => 'English Title', 'fr' => 'French Title'],
        'content' => ['en' => 'English Content', 'fr' => 'French Content'],
    ];

    livewire(TestComponentWithTranslate::class, [
        'locales' => $locales,
        'exclude' => [],
    ])
        ->fillForm($data)
        ->assertSchemaStateSet($data);
});
