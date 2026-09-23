<?php

use Filament\Facades\Filament;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Happenv\FilamentTranslatable\Forms\Component\Translations;
use Happenv\FilamentTranslatable\Tests\Fixtures\Livewire\SchemaForm;

use function Pest\Livewire\livewire;

function fieldsInTranslations(array $fields, array $locales = ['en', 'pl']): void
{
    SchemaForm::$componentsUsing = fn (): array => [
        Translations::make('translations')->locales($locales)->defaultLocale('en')->schema($fields),
    ];
}

it('requires a field only for the given locale', function (): void {
    fieldsInTranslations([TextInput::make('title')->requiredLocale('pl')]);

    livewire(SchemaForm::class)
        ->assertSchemaComponentExists('title.pl', checkComponentUsing: fn (Field $f): bool => $f->isRequired())
        ->assertSchemaComponentExists('title.en', checkComponentUsing: fn (Field $f): bool => ! $f->isRequired());
});

it('requires a field only for the default locale', function (): void {
    fieldsInTranslations([TextInput::make('title')->requiredDefaultLocale()]);

    livewire(SchemaForm::class)
        ->assertSchemaComponentExists('title.en', checkComponentUsing: fn (Field $f): bool => $f->isRequired())
        ->assertSchemaComponentExists('title.pl', checkComponentUsing: fn (Field $f): bool => ! $f->isRequired());
});

it('respects the requiredDefaultLocale condition', function (bool | Closure $condition): void {
    fieldsInTranslations([TextInput::make('title')->requiredDefaultLocale($condition)]);

    livewire(SchemaForm::class)
        ->assertSchemaComponentExists('title.en', checkComponentUsing: fn (Field $f): bool => ! $f->isRequired());
})->with([
    'false' => [false],
    'closure' => [fn (): Closure => fn (): bool => false],
]);

it('validates required locales on submit', function (): void {
    fieldsInTranslations([TextInput::make('title')->requiredDefaultLocale()]);

    livewire(SchemaForm::class)
        ->fillForm(['title' => ['en' => null, 'pl' => 'Cześć']])
        ->call('validateForm')
        ->assertHasFormErrors(['title.en' => 'required'])
        ->assertHasNoFormErrors(['title.pl']);
});

it('decorates fields per locale with locale injection', function (): void {
    fieldsInTranslations([
        TextInput::make('price')
            ->decorateTranslationField('pl', fn (TextInput $field, string $locale): TextInput => $field->suffix("PLN-{$locale}"))
            ->decorateTranslationField('en', fn (TextInput $field): TextInput => $field->prefix('$')),
    ]);

    livewire(SchemaForm::class)
        ->assertSchemaComponentExists('price.pl', checkComponentUsing: fn (TextInput $f): bool => $f->getSuffixLabel() === 'PLN-pl')
        ->assertSchemaComponentExists('price.en', checkComponentUsing: fn (TextInput $f): bool => $f->getPrefixLabel() === '$');
});

it('translatable() keeps the plugin locales when none are given', function (): void {
    Filament::setCurrentPanel('admin');
    SchemaForm::$componentsUsing = fn (): array => [TextInput::make('title')->translatable()];

    livewire(SchemaForm::class)
        ->assertSchemaComponentExists('title.en')
        ->assertSchemaComponentExists('title.pl');
});

it('translatable() accepts locales and a configuration callback', function (): void {
    SchemaForm::$componentsUsing = fn (): array => [
        TextInput::make('title')->translatable(
            locales: ['de', 'fr'],
            configureUsing: fn (Translations $t): Translations => $t->vertical(),
        ),
    ];

    livewire(SchemaForm::class)
        ->assertSchemaComponentExists('title.de')
        ->assertSchemaComponentExists('title.fr')
        ->assertSchemaComponentExists('title-translations::data::tabs', checkComponentUsing: fn (Translations $t): bool => $t->isVertical());
});

it('translatable(false) leaves the field untouched', function (): void {
    SchemaForm::$componentsUsing = fn (): array => [TextInput::make('title')->translatable(false)];

    livewire(SchemaForm::class)
        ->assertSchemaComponentExists('title', checkComponentUsing: fn ($f): bool => $f instanceof TextInput);
});
