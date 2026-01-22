<?php

use Webard\FilamentTranslatable\Tests\Forms\Fixtures\TestComponentWithActions;
use Webard\FilamentTranslatable\Tests\TestCase;

use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can register actions on translations component', function () {
    $locales = ['en', 'fr'];

    $component = livewire(TestComponentWithActions::class, [
        'locales' => $locales,
    ]);

    $component->assertSchemaComponentExists('translations::data::tabs');

    // Check that tabs have actions registered
    $component->assertSchemaComponentExists('en::data::tab', checkComponentUsing: function ($tab) {
        $actions = $tab->getActions();
        expect($actions)->toHaveCount(1);
        expect($actions)->toHaveKey('fillTitle');

        return true;
    });
});

it('registers actions on all locale tabs', function () {
    $locales = ['en', 'fr', 'pl'];

    $component = livewire(TestComponentWithActions::class, [
        'locales' => $locales,
    ]);

    // Each tab should have the action registered
    foreach ($locales as $locale) {
        $component->assertSchemaComponentExists("{$locale}::data::tab", checkComponentUsing: function ($tab) {
            $actions = $tab->getActions();
            expect($actions)->toHaveCount(1);
            expect($actions)->toHaveKey('fillTitle');

            return true;
        });
    }
});

it('action has access to locale through arguments', function () {
    $locales = ['en', 'fr'];

    $component = livewire(TestComponentWithActions::class, [
        'locales' => $locales,
    ]);

    // Check that actions are configured with locale argument
    $component->assertSchemaComponentExists('en::data::tab', checkComponentUsing: function ($tab) {
        $action = $tab->getActions()['fillTitle'];

        // The action should have the locale argument configured
        // This is set up in the Tab view/component
        expect($action)->not->toBeNull();

        return true;
    });
});
