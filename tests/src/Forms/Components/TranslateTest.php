<?php

use Webard\FilamentTranslatable\Tests\Forms\Fixtures\TestComponentWithTranslate;
use Webard\FilamentTranslatable\Tests\TestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can fill and assert data in a translate', function (array $list) {

    $data = $list['data'] ?? [];

    $livewireConfig = Arr::except($list, ['data']);

    livewire(TestComponentWithTranslate::class, $livewireConfig)
        ->fillForm($data)
        ->assertSchemaStateSet($data)
        ->assertSchemaComponentExists('translations::data::tabs.::data::tab.title.en', checkComponentUsing: function ($state) {

            expect($state->getDefaultLocale())->toBe('en');

            return true;
        });

})->with(function () {

    $locales = ['en', 'fr'];
    $buildTranslatableArray = fn () => collect($locales)->mapWithKeys(fn ($locale) => [$locale => Str::random()])->all();

    return [
        'normal' => fn () => [
            'data' => [
                'title' => $buildTranslatableArray(),
                'content' => $buildTranslatableArray(),
            ],
            'locales' => $locales,
            'exclude' => [],
        ],
        'exclude_content' => fn () => [
            'data' => [
                'title' => $buildTranslatableArray(),
                'content' => Str::random(),
            ],
            'locales' => $locales,
            'exclude' => ['content'],
        ],
    ];
});
