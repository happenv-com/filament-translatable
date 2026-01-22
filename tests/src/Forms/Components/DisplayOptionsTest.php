<?php

use Webard\FilamentTranslatable\Tests\Forms\Fixtures\TestComponentWithDisplayOptions;
use Webard\FilamentTranslatable\Tests\TestCase;

use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can disable flag display in locale labels', function () {
    $component = livewire(TestComponentWithDisplayOptions::class, [
        'displayFlagsInLocaleLabels' => false,
    ]);

    $component->assertSchemaComponentExists('translations::data::tabs', checkComponentUsing: function ($translations) {
        expect($translations->hasFlagsInLocaleLabels())->toBeFalse();

        return true;
    });
});

it('can enable flag display in locale labels', function () {
    $component = livewire(TestComponentWithDisplayOptions::class, [
        'displayFlagsInLocaleLabels' => true,
    ]);

    $component->assertSchemaComponentExists('translations::data::tabs', checkComponentUsing: function ($translations) {
        expect($translations->hasFlagsInLocaleLabels())->toBeTrue();

        return true;
    });
});

it('can disable name display in locale labels', function () {
    $component = livewire(TestComponentWithDisplayOptions::class, [
        'displayNamesInLocaleLabels' => false,
    ]);

    $component->assertSchemaComponentExists('translations::data::tabs', checkComponentUsing: function ($translations) {
        expect($translations->hasNamesInLocaleLabels())->toBeFalse();

        return true;
    });
});

it('can set custom flag width', function () {
    $component = livewire(TestComponentWithDisplayOptions::class, [
        'flagWidth' => '48px',
    ]);

    $component->assertSchemaComponentExists('translations::data::tabs', checkComponentUsing: function ($translations) {
        expect($translations->getFlagWidth())->toBe('48px');

        return true;
    });
});

it('creates component with all display options', function () {
    $component = livewire(TestComponentWithDisplayOptions::class);

    $component->assertSchemaComponentExists('translations::data::tabs')
        ->assertSchemaComponentExists('title.en')
        ->assertSchemaComponentExists('title.pl');
});
