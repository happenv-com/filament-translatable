<?php

use Webard\FilamentTranslatable\Tests\Forms\Fixtures\TestComponentWithRequiredLocale;
use Webard\FilamentTranslatable\Tests\TestCase;

use function Pest\Livewire\livewire;

uses(TestCase::class);

it('makes field required for specific locale', function (): void {
    $component = livewire(TestComponentWithRequiredLocale::class);

    // English field should be required
    $component->assertSchemaComponentExists('title.en', checkComponentUsing: function ($field): true {
        expect($field->isRequired())->toBeTrue();

        return true;
    });

    // French field should also be required
    $component->assertSchemaComponentExists('title.fr', checkComponentUsing: function ($field): true {
        expect($field->isRequired())->toBeTrue();

        return true;
    });

    // Polish field should NOT be required
    $component->assertSchemaComponentExists('title.pl', checkComponentUsing: function ($field): true {
        expect($field->isRequired())->toBeFalse();

        return true;
    });
});

it('fills data correctly with required locale', function (): void {
    $data = [
        'title' => ['en' => 'English Title', 'fr' => 'French Title', 'pl' => 'Polish Title'],
    ];

    livewire(TestComponentWithRequiredLocale::class)
        ->fillForm($data)
        ->assertSchemaStateSet($data);
});
