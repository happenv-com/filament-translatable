<?php

use Happenv\FilamentTranslatable\Tests\Forms\Fixtures\TestComponentWithDisplayOptions;
use Happenv\FilamentTranslatable\Tests\TestCase;

use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can disable flag display in locale labels', function (): void {
    $component = livewire(TestComponentWithDisplayOptions::class, [
        'displayFlagsInLocaleLabels' => false,
    ]);

    $component->assertSchemaComponentExists('translations::data::tabs', checkComponentUsing: function ($translations): true {
        expect($translations->hasFlagsInLocaleLabels())->toBeFalse();

        return true;
    });
});

it('can enable flag display in locale labels', function (): void {
    $component = livewire(TestComponentWithDisplayOptions::class, [
        'displayFlagsInLocaleLabels' => true,
    ]);

    $component->assertSchemaComponentExists('translations::data::tabs', checkComponentUsing: function ($translations): true {
        expect($translations->hasFlagsInLocaleLabels())->toBeTrue();

        return true;
    });
});

it('can disable name display in locale labels', function (): void {
    $component = livewire(TestComponentWithDisplayOptions::class, [
        'displayNamesInLocaleLabels' => false,
    ]);

    $component->assertSchemaComponentExists('translations::data::tabs', checkComponentUsing: function ($translations): true {
        expect($translations->hasNamesInLocaleLabels())->toBeFalse();

        return true;
    });
});

it('can set custom flag width', function (): void {
    $component = livewire(TestComponentWithDisplayOptions::class, [
        'flagWidth' => '48px',
    ]);

    $component->assertSchemaComponentExists('translations::data::tabs', checkComponentUsing: function ($translations): true {
        expect($translations->getFlagWidth())->toBe('48px');

        return true;
    });
});

it('creates component with all display options', function (): void {
    $component = livewire(TestComponentWithDisplayOptions::class);

    $component->assertSchemaComponentExists('translations::data::tabs')
        ->assertSchemaComponentExists('title.en')
        ->assertSchemaComponentExists('title.pl');
});
