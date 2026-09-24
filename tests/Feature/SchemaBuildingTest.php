<?php

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Happenv\FilamentTranslatable\Dto\Locale;
use Happenv\FilamentTranslatable\Enums\TranslationMode;
use Happenv\FilamentTranslatable\Forms\Component\Translations;
use Happenv\FilamentTranslatable\Tests\Fixtures\Livewire\SchemaForm;

use function Pest\Livewire\livewire;

function translationsWith(Closure $configure): void
{
    SchemaForm::$componentsUsing = fn (): array => [
        $configure(
            Translations::make('translations')
                ->locales(['en', 'pl'])
                ->defaultLocale('en')
                ->schema([TextInput::make('title')->label('Title')]),
        ),
    ];
}

it('caches the per-locale schemas', function (): void {
    translationsWith(fn (Translations $t): Translations => $t);

    livewire(SchemaForm::class)
        ->assertSchemaComponentExists('translations::data::tabs', checkComponentUsing: function (Translations $t): bool {
            $first = $t->getChildSchemas();

            expect(array_keys($first))->toBe(['en', 'pl'])
                ->and($t->getChildSchemas()['pl'])->toBe($first['pl']);

            return true;
        });
});

it('passes the locale code to a schema closure', function (): void {
    SchemaForm::$componentsUsing = fn (): array => [
        Translations::make('translations')
            ->locales(['en', 'pl'])
            ->schema(fn (string $locale): array => [
                TextInput::make('title')->label("Title {$locale}")->required($locale === 'en'),
            ]),
    ];

    livewire(SchemaForm::class)
        ->assertSchemaComponentExists('title.pl', checkComponentUsing: fn (Field $f): bool => $f->getLabel() === 'Title pl' && ! $f->isRequired())
        ->assertSchemaComponentExists('title.en', checkComponentUsing: fn (Field $f): bool => $f->isRequired());
});

it('keys tabs by locale code even with labels and flags', function (): void {
    translationsWith(fn (Translations $t): Translations => $t
        ->locales(['en' => 'English', 'pt-BR' => 'Português'])
        ->displayFlagsInLocaleLabels());

    livewire(SchemaForm::class)
        ->assertSchemaComponentExists('translations::en::data::tab')
        ->assertSchemaComponentExists('translations::pt-BR::data::tab')
        ->assertSchemaComponentExists('title.pt-BR');
});

it('supports two translations components in one form', function (): void {
    SchemaForm::$componentsUsing = fn (): array => [
        Translations::make('main')->locales(['en', 'pl'])->schema([TextInput::make('title')]),
        Translations::make('seo')->locales(['en', 'pl'])->translationMode(TranslationMode::Astrotomic)->schema([TextInput::make('meta')]),
    ];

    livewire(SchemaForm::class)
        ->assertSchemaComponentExists('title.pl')
        ->assertSchemaComponentExists('meta:pl')
        ->fillForm(['title' => ['pl' => 'A'], 'meta:pl' => 'B'])
        ->assertSchemaStateSet(['title' => ['en' => null, 'pl' => 'A'], 'meta:pl' => 'B']);
});

it('escapes locale labels and flag alt text', function (): void {
    translationsWith(fn (Translations $t): Translations => $t
        ->locales(['en' => '<script>alert(1)</script>'])
        ->displayFlagsInLocaleLabels());

    livewire(SchemaForm::class)
        ->assertDontSeeHtml('<script>alert(1)</script>')
        ->assertSchemaComponentExists('translations::data::tabs', checkComponentUsing: function (Translations $t): bool {
            $html = (string) $t->getLocaleLabel(new Locale('en', '<script>alert(1)</script>'));

            expect($html)->not->toContain('<script>')
                ->and($html)->toContain('&lt;script&gt;');

            return true;
        });
});

it('does not render field labels as HTML when adding locale prefixes', function (): void {
    SchemaForm::$componentsUsing = fn (): array => [
        Translations::make('translations')
            ->locales(['en'])
            ->prefixLocaleLabel()
            ->schema([TextInput::make('title')->label('<b>Title</b>')]),
    ];

    livewire(SchemaForm::class)->assertDontSeeHtml('<b>Title</b>');
});

it('formats locale labels with a callback receiving the code and label', function (): void {
    translationsWith(fn (Translations $t): Translations => $t
        ->locales(['en' => 'English', 'pl' => 'Polski'])
        ->suffixLocaleLabel()
        ->formatLocaleLabelUsing(fn (string $locale, string $label): string => "[{$locale}|{$label}]"));

    livewire(SchemaForm::class)
        ->assertSchemaComponentExists('title.pl', checkComponentUsing: fn (Field $f): bool => $f->getLabel() === 'Title [pl|Polski]');
});

it('keeps the deprecated preformLocaleLabelUsing alias', function (): void {
    translationsWith(fn (Translations $t): Translations => $t
        ->prefixLocaleLabel()
        ->preformLocaleLabelUsing(fn (string $label): string => "<{$label}>"));

    livewire(SchemaForm::class)
        ->assertSchemaComponentExists('title.pl', checkComponentUsing: fn (Field $f): bool => $f->getLabel() === '<pl> Title');
});

it('uses a per-locale field label callback receiving the code', function (): void {
    translationsWith(fn (Translations $t): Translations => $t
        ->fieldTranslatableLabel(fn (Field $field, string $locale): string => "{$field->getName()}-{$locale}"));

    livewire(SchemaForm::class)
        ->assertSchemaComponentExists('title.pl', checkComponentUsing: fn (Field $f): bool => $f->getLabel() === 'title-pl');
});

it('applies prefix labels conditionally per field', function (): void {
    SchemaForm::$componentsUsing = fn (): array => [
        Translations::make('translations')
            ->locales(['pl'])
            ->prefixLocaleLabel(fn (Field $field): bool => $field->getName() === 'title')
            ->schema([TextInput::make('title')->label('Title'), TextInput::make('slug')->label('Slug')]),
    ];

    livewire(SchemaForm::class)
        ->assertSchemaComponentExists('title.pl', checkComponentUsing: fn (Field $f): bool => $f->getLabel() === '(pl) Title')
        ->assertSchemaComponentExists('slug.pl', checkComponentUsing: fn (Field $f): bool => $f->getLabel() === 'Slug');
});
