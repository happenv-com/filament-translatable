<?php

use Filament\Support\Facades\FilamentAsset;
use Happenv\FilamentTranslatable\Tests\Forms\Fixtures\TestComponentWithVerticalTranslate;
use Happenv\FilamentTranslatable\Tests\TestCase;

use function Pest\Livewire\livewire;

uses(TestCase::class);

it('drives its tabs with its own Alpine component instead of the Filament tabs component', function (): void {
    // Filament's tabsSchemaComponent requires the markup of Filament's own tabs view (#37)
    livewire(TestComponentWithVerticalTranslate::class, [
        'locales' => ['en', 'fr'],
        'exclude' => [],
    ])
        ->assertSeeHtml('x-data="translationsTabsComponent(')
        ->assertSeeHtml(e(FilamentAsset::getAlpineComponentSrc('translations-tabs', 'happenv-com/filament-translatable')))
        ->assertDontSeeHtml('tabsSchemaComponent(')
        ->assertDontSeeHtml(e(FilamentAsset::getAlpineComponentSrc('tabs', 'filament/schemas')));
});
