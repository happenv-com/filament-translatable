<?php

use Filament\Actions\Action;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Happenv\FilamentTranslatable\Enums\TranslationMode;
use Happenv\FilamentTranslatable\Forms\Component\Translations;
use Happenv\FilamentTranslatable\Forms\Component\Translations\Tab;
use Happenv\FilamentTranslatable\Tests\Fixtures\Livewire\PostForm;
use Happenv\FilamentTranslatable\Tests\Fixtures\Livewire\SchemaForm;
use Happenv\FilamentTranslatable\Tests\Fixtures\Posts;

use function Pest\Livewire\livewire;

it('creates a tab per locale', function (TranslationMode $mode): void {
    livewire(PostForm::class, ['mode' => $mode->value])
        ->assertSchemaComponentExists('translations::en::data::tab', checkComponentUsing: fn (Tab $tab): bool => $tab->getLocale() === 'en')
        ->assertSchemaComponentExists('translations::pl::data::tab', checkComponentUsing: fn (Tab $tab): bool => $tab->getLocale() === 'pl');
})->with('drivers');

it('creates a localized field per locale named by the driver', function (TranslationMode $mode): void {
    livewire(PostForm::class, ['mode' => $mode->value])
        ->assertSchemaComponentExists(Posts::fieldName($mode, 'title', 'en'), checkComponentUsing: fn ($field): bool => $field instanceof TextInput)
        ->assertSchemaComponentExists(Posts::fieldName($mode, 'title', 'pl'))
        ->assertSchemaComponentExists(Posts::fieldName($mode, 'content', 'en'), checkComponentUsing: fn ($field): bool => $field instanceof Textarea)
        ->assertSchemaComponentExists(Posts::fieldName($mode, 'content', 'pl'));
})->with('drivers');

it('fills and reads localized state', function (TranslationMode $mode): void {
    $data = Posts::formData($mode, ['title' => ['en' => 'Hello', 'pl' => 'Cześć']]);

    livewire(PostForm::class, ['mode' => $mode->value])
        ->fillForm($data)
        ->assertSchemaStateSet($data);
})->with('drivers');

it('keeps excluded fields untranslated', function (TranslationMode $mode): void {
    PostForm::$configureTranslationsUsing = fn (Translations $t) => $t->exclude(['content']);

    $component = livewire(PostForm::class, ['mode' => $mode->value])
        ->fillForm(Posts::formData($mode, ['title' => ['en' => 'Hello']], ['content' => 'Plain']));

    $state = $component->instance()->form->getState();

    expect($state['content'])->toBe('Plain');
})->with('drivers');

it('translates only included fields', function (TranslationMode $mode): void {
    PostForm::$configureTranslationsUsing = fn (Translations $t) => $t->include(['title']);

    $component = livewire(PostForm::class, ['mode' => $mode->value])
        ->fillForm(Posts::formData($mode, ['title' => ['en' => 'Hello']], ['content' => 'Plain']));

    $state = $component->instance()->form->getState();

    expect($state['content'])->toBe('Plain');
})->with('drivers');

it('registers actions on every locale tab and passes the locale', function (): void {
    SchemaForm::$componentsUsing = fn (): array => [
        Translations::make('translations')
            ->locales(['en', 'pl'])
            ->defaultLocale('en')
            ->translationMode(TranslationMode::Spatie)
            ->actions([
                Action::make('fillTitle')->action(function (array $arguments, SchemaForm $livewire): void {
                    $livewire->actionCalls[] = ['action' => 'fillTitle', 'locale' => $arguments['locale'] ?? null];
                }),
            ])
            ->schema([TextInput::make('title')]),
    ];

    $component = livewire(SchemaForm::class)
        ->assertSchemaComponentExists('translations::en::data::tab', checkComponentUsing: fn (Tab $tab): bool => array_key_exists('fillTitle', $tab->getActions()))
        ->assertSchemaComponentExists('translations::pl::data::tab', checkComponentUsing: fn (Tab $tab): bool => array_key_exists('fillTitle', $tab->getActions()));

    $component->callAction(TestAction::make('fillTitle')->schemaComponent('translations::pl::data::tab', schema: 'form')->arguments(['locale' => 'pl']));

    expect($component->get('actionCalls'))->toBe([['action' => 'fillTitle', 'locale' => 'pl']]);
});

it('supports vertical tabs', function (): void {
    PostForm::$configureTranslationsUsing = fn (Translations $t) => $t->vertical();

    livewire(PostForm::class)
        ->assertSchemaComponentExists('translations::data::tabs', checkComponentUsing: fn (Translations $t): bool => $t->isVertical());
});

it('exposes display options', function (): void {
    PostForm::$configureTranslationsUsing = fn (Translations $t) => $t
        ->displayFlagsInLocaleLabels()
        ->displayNamesInLocaleLabels(false)
        ->flagWidth('48px');

    livewire(PostForm::class)
        ->assertSchemaComponentExists('translations::data::tabs', checkComponentUsing: function (Translations $t): bool {
            expect($t->hasFlagsInLocaleLabels())->toBeTrue()
                ->and($t->hasNamesInLocaleLabels())->toBeFalse()
                ->and($t->getFlagWidth())->toBe('48px');

            return true;
        });
});
