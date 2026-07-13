<?php

use Happenv\FilamentTranslatable\Tests\Forms\Fixtures\TestComponentWithLocaleLabels;
use Happenv\FilamentTranslatable\Tests\TestCase;

use function Pest\Livewire\livewire;

uses(TestCase::class);

it('uses custom locale labels', function (): void {
    $component = livewire(TestComponentWithLocaleLabels::class);

    // Check that the tab for English has the custom label
    // Note: tab key is derived from the label (lowercase)
    $component->assertSchemaComponentExists('english::data::tab', checkComponentUsing: function ($tab): true {
        $label = $tab->getLabel();
        expect((string) $label)->toContain('English');

        return true;
    });

    // Check that the tab for Polish has the custom label
    $component->assertSchemaComponentExists('polski::data::tab', checkComponentUsing: function ($tab): true {
        $label = $tab->getLabel();
        expect((string) $label)->toContain('Polski');

        return true;
    });
});

it('can add prefix locale label to fields', function (): void {
    $component = livewire(TestComponentWithLocaleLabels::class, [
        'prefixLocaleLabel' => true,
    ]);

    // Check that the field label includes the locale prefix
    $component->assertSchemaComponentExists('title.en', checkComponentUsing: function ($field): true {
        $label = $field->getLabel();
        expect((string) $label)->toContain('(English)');

        return true;
    });
});

it('can add suffix locale label to fields', function (): void {
    $component = livewire(TestComponentWithLocaleLabels::class, [
        'suffixLocaleLabel' => true,
    ]);

    // Check that the field label includes the locale suffix
    $component->assertSchemaComponentExists('title.en', checkComponentUsing: function ($field): true {
        $label = $field->getLabel();
        expect((string) $label)->toContain('(English)');

        return true;
    });
});

it('fills data correctly with custom locale labels', function (): void {
    $data = [
        'title' => ['en' => 'English Title', 'pl' => 'Polski Tytuł'],
    ];

    livewire(TestComponentWithLocaleLabels::class)
        ->fillForm($data)
        ->assertSchemaStateSet($data);
});
