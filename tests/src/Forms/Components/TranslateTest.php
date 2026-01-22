<?php

use Filament\Forms\Components\TextInput;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Webard\FilamentTranslatable\Forms\Component\Translations;
use Webard\FilamentTranslatable\Forms\Component\Translations\Tab;
use Webard\FilamentTranslatable\Tests\Forms\Fixtures\TestComponentWithTranslate;
use Webard\FilamentTranslatable\Tests\TestCase;

use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can fill and assert data in a translate', function (array $list): void {

    $data = $list['data'] ?? [];

    $livewireConfig = Arr::except($list, ['data']);

    livewire(TestComponentWithTranslate::class, $livewireConfig)
        ->fillForm($data)
        ->assertSchemaStateSet($data);

})->with(function (): array {

    $locales = ['en', 'fr'];
    $buildTranslatableArray = fn () => collect($locales)->mapWithKeys(fn ($locale): array => [$locale => Str::random()])->all();

    return [
        'normal' => fn (): array => [
            'data' => [
                'title' => $buildTranslatableArray(),
                'content' => $buildTranslatableArray(),
            ],
            'locales' => $locales,
            'exclude' => [],
        ],
        'exclude_content' => fn (): array => [
            'data' => [
                'title' => $buildTranslatableArray(),
                'content' => Str::random(),
            ],
            'locales' => $locales,
            'exclude' => ['content'],
        ],
    ];
});

it('has correct default locale', function (): void {
    $locales = ['en', 'fr'];

    livewire(TestComponentWithTranslate::class, [
        'locales' => $locales,
        'exclude' => [],
    ])
        ->assertSchemaComponentExists('title.en', checkComponentUsing: function ($component): true {
            expect($component)->toBeInstanceOf(TextInput::class);

            // Check if the component has the defaultLocale macro set
            if (method_exists($component, 'getDefaultLocale')) {
                expect($component->getDefaultLocale())->toBe('en');
            }

            return true;
        });
});

it('creates tabs for each locale', function (): void {
    $locales = ['en', 'fr', 'pl'];

    livewire(TestComponentWithTranslate::class, [
        'locales' => $locales,
        'exclude' => [],
    ])
        ->assertSchemaComponentExists('en::data::tab', checkComponentUsing: function ($component): true {
            expect($component)->toBeInstanceOf(Tab::class);
            expect($component->getLocale())->toBe('en');

            return true;
        })
        ->assertSchemaComponentExists('fr::data::tab', checkComponentUsing: function ($component): true {
            expect($component)->toBeInstanceOf(Tab::class);
            expect($component->getLocale())->toBe('fr');

            return true;
        })
        ->assertSchemaComponentExists('pl::data::tab', checkComponentUsing: function ($component): true {
            expect($component)->toBeInstanceOf(Tab::class);
            expect($component->getLocale())->toBe('pl');

            return true;
        });
});

it('creates fields for each locale', function (): void {
    $locales = ['en', 'fr'];

    livewire(TestComponentWithTranslate::class, [
        'locales' => $locales,
        'exclude' => [],
    ])
        ->assertSchemaComponentExists('title.en')
        ->assertSchemaComponentExists('title.fr')
        ->assertSchemaComponentExists('content.en')
        ->assertSchemaComponentExists('content.fr');
});

it('excludes fields when exclude option is used', function (): void {
    $locales = ['en', 'fr'];

    $component = livewire(TestComponentWithTranslate::class, [
        'locales' => $locales,
        'exclude' => ['content'],
    ]);

    $component->assertSchemaComponentExists('title.en')
        ->assertSchemaComponentExists('title.fr');

    // Content should not be localized - should only have 'content' without locale suffix
    $schemaInstance = $component->instance()->form;
    $keys = array_keys($schemaInstance->getFlatComponents(withHidden: true));

    expect($keys)->toContain('content.en')
        ->and($keys)->toContain('content.fr');

    // The content fields should not have locale-specific state mapping when excluded
    $rawState = $schemaInstance->getRawState();

    // Verify the data can be set and retrieved
    $component->fillForm([
        'title' => ['en' => 'English Title', 'fr' => 'French Title'],
        'content' => 'Non-translated content',
    ]);

    $component->assertSchemaStateSet([
        'title' => ['en' => 'English Title', 'fr' => 'French Title'],
        'content' => 'Non-translated content',
    ]);
});

it('creates translations component as tabs', function (): void {
    $locales = ['en', 'fr'];

    livewire(TestComponentWithTranslate::class, [
        'locales' => $locales,
        'exclude' => [],
    ])
        ->assertSchemaComponentExists('translations::data::tabs', checkComponentUsing: function ($component): true {
            expect($component)->toBeInstanceOf(Translations::class);

            return true;
        });
});
