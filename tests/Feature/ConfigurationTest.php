<?php

use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Happenv\FilamentTranslatable\Drivers\AstrotomicDriver;
use Happenv\FilamentTranslatable\Drivers\SpatieDriver;
use Happenv\FilamentTranslatable\Drivers\TranslationDriver;
use Happenv\FilamentTranslatable\Enums\TranslationMode;
use Happenv\FilamentTranslatable\FilamentTranslatablePlugin;
use Happenv\FilamentTranslatable\Forms\Component\Translations;
use Happenv\FilamentTranslatable\Tests\Fixtures\Livewire\SchemaForm;

use function Pest\Livewire\livewire;

function bareTranslations(): void
{
    SchemaForm::$componentsUsing = fn (): array => [
        Translations::make('translations')->schema([TextInput::make('title')]),
    ];
}

function assertTranslations(Closure $assert): void
{
    livewire(SchemaForm::class)
        ->assertSchemaComponentExists('translations::data::tabs', checkComponentUsing: function (Translations $t) use ($assert): bool {
            $assert($t);

            return true;
        });
}

it('works without any panel using safe defaults', function (): void {
    filament()->setCurrentPanel(null);
    bareTranslations();

    assertTranslations(function (Translations $t): void {
        expect(array_keys($t->getLocales()))->toBe(['en'])
            ->and($t->getDefaultLocale())->toBe('en')
            ->and($t->getTranslationDriver())->toBeInstanceOf(SpatieDriver::class)
            ->and($t->hasNamesInLocaleLabels())->toBeTrue()
            ->and($t->hasFlagsInLocaleLabels())->toBeFalse()
            ->and($t->getFlagWidth())->toBe('24px');
    });
});

it('works in a panel that has no plugin registered', function (): void {
    Filament::setCurrentPanel('bare');
    bareTranslations();

    assertTranslations(function (Translations $t): void {
        expect(array_keys($t->getLocales()))->toBe(['en']);
    });
});

it('does not expose a plugin outside of its panel', function (): void {
    Filament::setCurrentPanel('bare');

    expect(FilamentTranslatablePlugin::current())->toBeNull();
});

it('applies plugin settings from the current panel, including closures', function (): void {
    Filament::setCurrentPanel('admin');
    FilamentTranslatablePlugin::current()
        ->displayFlagsInLocaleLabels(fn (): bool => true)
        ->displayNamesInLocaleLabels(fn (): bool => false)
        ->flagWidth(fn (): string => '32px')
        ->translationMode(TranslationMode::Astrotomic);
    bareTranslations();

    assertTranslations(function (Translations $t): void {
        expect(array_keys($t->getLocales()))->toBe(['en', 'pl'])
            ->and($t->hasFlagsInLocaleLabels())->toBeTrue()
            ->and($t->hasNamesInLocaleLabels())->toBeFalse()
            ->and($t->getFlagWidth())->toBe('32px')
            ->and($t->getTranslationDriver())->toBeInstanceOf(AstrotomicDriver::class);
    });
});

it('lets configureUsing override the plugin', function (): void {
    Filament::setCurrentPanel('admin');
    bareTranslations();

    Translations::configureUsing(
        fn (Translations $t): Translations => $t->locales(['de', 'fr']),
        during: fn () => assertTranslations(function (Translations $t): void {
            expect(array_keys($t->getLocales()))->toBe(['de', 'fr']);
        }),
    );
});

it('lets the instance override configureUsing', function (): void {
    Filament::setCurrentPanel('admin');
    SchemaForm::$componentsUsing = fn (): array => [
        Translations::make('translations')->locales(['fr'])->schema([TextInput::make('title')]),
    ];

    Translations::configureUsing(
        fn (Translations $t): Translations => $t->locales(['de']),
        during: fn () => assertTranslations(function (Translations $t): void {
            expect(array_keys($t->getLocales()))->toBe(['fr']);
        }),
    );
});

it('accepts closures for every option', function (): void {
    filament()->setCurrentPanel(null);
    SchemaForm::$componentsUsing = fn (): array => [
        Translations::make('translations')
            ->locales(fn (): array => ['en' => 'English', 'pl' => 'Polski'])
            ->defaultLocale(fn (): string => 'pl')
            ->displayNamesInLocaleLabels(fn (): bool => false)
            ->translationMode(fn (): TranslationDriver => new AstrotomicDriver)
            ->schema([TextInput::make('title')]),
    ];

    assertTranslations(function (Translations $t): void {
        expect($t->getDefaultLocale())->toBe('pl')
            ->and($t->getLocales()['pl']->label)->toBe('Polski')
            ->and($t->hasNamesInLocaleLabels())->toBeFalse()
            ->and($t->getTranslationDriver())->toBeInstanceOf(AstrotomicDriver::class);
    });
});
