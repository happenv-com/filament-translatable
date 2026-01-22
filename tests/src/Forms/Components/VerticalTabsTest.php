<?php

use Webard\FilamentTranslatable\Forms\Component\Translations;
use Webard\FilamentTranslatable\Tests\Forms\Fixtures\TestComponentWithVerticalTranslate;
use Webard\FilamentTranslatable\Tests\TestCase;

use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can create vertical tabs', function (): void {
    $locales = ['en', 'fr'];

    livewire(TestComponentWithVerticalTranslate::class, [
        'locales' => $locales,
        'exclude' => [],
    ])
        ->assertSchemaComponentExists('translations::data::tabs', checkComponentUsing: function ($component): true {
            expect($component)->toBeInstanceOf(Translations::class);

            return true;
        });
});

it('can fill data in vertical tabs', function (): void {
    $locales = ['en', 'fr'];

    $data = [
        'title' => ['en' => 'English Title', 'fr' => 'French Title'],
        'content' => ['en' => 'English Content', 'fr' => 'French Content'],
    ];

    livewire(TestComponentWithVerticalTranslate::class, [
        'locales' => $locales,
        'exclude' => [],
    ])
        ->fillForm($data)
        ->assertSchemaStateSet($data);
});

it('creates fields for each locale in vertical tabs', function (): void {
    $locales = ['en', 'fr', 'pl'];

    livewire(TestComponentWithVerticalTranslate::class, [
        'locales' => $locales,
        'exclude' => [],
    ])
        ->assertSchemaComponentExists('title.en')
        ->assertSchemaComponentExists('title.fr')
        ->assertSchemaComponentExists('title.pl')
        ->assertSchemaComponentExists('content.en')
        ->assertSchemaComponentExists('content.fr')
        ->assertSchemaComponentExists('content.pl');
});
