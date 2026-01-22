<?php

use Filament\Forms\Components\TextInput;
use Webard\FilamentTranslatable\Forms\Component\Translations;
use Webard\FilamentTranslatable\Tests\Forms\Fixtures\TestComponentWithDecoratedField;
use Webard\FilamentTranslatable\Tests\Forms\Fixtures\TestComponentWithMacro;
use Webard\FilamentTranslatable\Tests\TestCase;

use function Pest\Livewire\livewire;

uses(TestCase::class);

it('can use translations component with requiredDefaultLocale', function () {
    $component = livewire(TestComponentWithMacro::class);

    // The component should create a Translations component
    $schemaInstance = $component->instance()->form;
    $keys = array_keys($schemaInstance->getFlatComponents(withHidden: true));

    // Should have translations tabs (note: hyphen in name comes from make())
    expect($keys)->toContain('title-translations::data::tabs');
});

it('translations component creates fields for each locale', function () {
    $component = livewire(TestComponentWithMacro::class);

    $component->assertSchemaComponentExists('title.en', checkComponentUsing: function ($field) {
        expect($field)->toBeInstanceOf(TextInput::class);

        return true;
    });

    $component->assertSchemaComponentExists('title.fr', checkComponentUsing: function ($field) {
        expect($field)->toBeInstanceOf(TextInput::class);

        return true;
    });
});

it('can fill data in translations component', function () {
    $data = [
        'title' => ['en' => 'English Title', 'fr' => 'French Title'],
    ];

    livewire(TestComponentWithMacro::class)
        ->fillForm($data)
        ->assertSchemaStateSet($data);
});

it('applies field decorators per locale', function () {
    $component = livewire(TestComponentWithDecoratedField::class);

    // Check English field has prefix '$'
    $component->assertSchemaComponentExists('price.en', checkComponentUsing: function ($field) {
        expect($field)->toBeInstanceOf(TextInput::class);
        expect($field->getPrefixLabel())->toBe('$');

        return true;
    });

    // Check Polish field has suffix 'PLN'
    $component->assertSchemaComponentExists('price.pl', checkComponentUsing: function ($field) {
        expect($field)->toBeInstanceOf(TextInput::class);
        expect($field->getSuffixLabel())->toBe('PLN');

        return true;
    });
});

it('requiredDefaultLocale makes field required only for default locale', function () {
    $component = livewire(TestComponentWithMacro::class);

    // The default locale is 'en'
    // The field should be required for 'en' locale
    $component->assertSchemaComponentExists('title.en', checkComponentUsing: function ($field) {
        return $field->isRequired();
    });

    // French field should not be required
    $component->assertSchemaComponentExists('title.fr', checkComponentUsing: function ($field) {
        return ! $field->isRequired();
    });
});
