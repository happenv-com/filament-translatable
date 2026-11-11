---
name: filament-translatable-development
description: >-
  Build multilingual Filament 4/5 forms with happenv-com/filament-translatable (locale tabs over
  spatie/laravel-translatable or astrotomic/laravel-translatable). Use when making a form field
  translatable (the translatable() macro, requiredLocale(), requiredDefaultLocale(),
  decorateTranslationField()), building a Translations component (locales(), defaultLocale(),
  include()/exclude(), actions() with $arguments['locale'], schema(fn (string $locale) => ...),
  prefixLocaleLabel(), fieldTranslatableLabel(), vertical(), flags), configuring
  FilamentTranslatablePlugin or Translations::configureUsing(), choosing TranslationMode::Spatie /
  Astrotomic or a custom TranslationDriver, or testing such a form (fillForm with title.en / title:en).
---

# Filament Translatable

Edits every locale of a translatable attribute on one form: each field is cloned once per locale into a tab, and the clone's state path is the translation's key (`title.en` for Spatie, `title:en` for Astrotomic). Saving is left to Filament and the translation package: the form state already has the shape the model accepts. On load the component reads each locale from the record itself, with no fallback to another locale.

## When to use this skill

- Making fields of a create/edit form (resource, relation manager, action modal, plain Livewire form) translatable.
- Configuring locales, the default locale, labels or flags for a panel or a single component.
- Adding per-locale validation, decoration or tab actions (e.g. "translate from English").
- Switching between Spatie and Astrotomic storage, or plugging in another storage.
- Writing tests that fill or assert a translatable form.

## Installation

```bash
composer require happenv-com/filament-translatable
composer require spatie/laravel-translatable   # or astrotomic/laravel-translatable
php artisan filament:assets
```

Supports Filament 4 and 5 (not 3), Laravel 12 and 13, PHP 8.3 to 8.5. The service provider is auto-discovered and registers the field macros. The plugin is optional:

```php
use Happenv\FilamentTranslatable\FilamentTranslatablePlugin;

$panel->plugin(
    FilamentTranslatablePlugin::make()
        ->locales(['en' => 'English', 'pl' => 'Polski'])
        ->defaultLocale('en'),
);
```

The model is configured as the translation package documents it: Spatie's `HasTranslations` with `public array $translatable = [...]` (a JSON column per attribute), or Astrotomic's own `Astrotomic\Translatable\Translatable` trait with `$translatedAttributes`. Do not use the deprecated `Happenv\FilamentTranslatable\Traits\AstrotomicTranslatable` trait in new code; it is removed in 6.0.

## Where settings come from

Weakest to strongest, every setting accepts a value or a `Closure`:

1. Package defaults: `config('app.fallback_locale')` as the only locale and the default locale, Spatie mode, names shown, flags hidden, `24px` flags.
2. `FilamentTranslatablePlugin` registered on the **current** panel (`FilamentTranslatablePlugin::current()`, `null` without one).
3. `Translations::configureUsing(fn (Translations $translations) => ...)` in a service provider. Use this outside panels (plain Livewire forms) or for app-wide defaults.
4. Methods on the component instance.

With a single locale the tab bar is not rendered at all. "I see no tabs" usually means no locales were configured.

## The `translatable()` macro

Turns one field into a `Translations` component holding a clone of the field per locale.

```php
use Filament\Forms\Components\TextInput;
use Happenv\FilamentTranslatable\Forms\Component\Translations;

TextInput::make('title')
    ->required()                               // every locale
    ->maxLength(255)
    ->requiredDefaultLocale()                  // only the default locale
    ->requiredLocale('pl', fn (): bool => auth()->user()->isEditor())
    ->decorateTranslationField('pl', fn (TextInput $field) => $field->suffix('PLN'))
    ->decorateTranslationField('en', fn (TextInput $field, string $locale) => $field->prefix('USD'))
    ->translatable(
        locales: ['en', 'pl'],                 // optional; otherwise plugin / configureUsing()
        configureUsing: fn (Translations $translations) => $translations->vertical(),
    );
```

- **Order matters.** `translatable()` returns a `Translations` component, not the field. Every field method (`required()`, `maxLength()`, `requiredLocale()`, `decorateTranslationField()`, ...) must come **before** it; methods after it configure the tabs (`->vertical()`, `->displayFlagsInLocaleLabels()`, `->flagWidth('48px')`).
- Its signature is `translatable(bool $condition = true, array|Closure|null $locales = null, ?Closure $configureUsing = null)`; `translatable(false)` returns the field unchanged. Use named arguments.
- Its declared return type is `Translations|Field`, so static analysis does not know `->vertical()` after it. Prefer `configureUsing:` for tab settings in code checked by PHPStan.
- The generated component's label is `{field name}_translations`, so two translatable fields never collide.
- `requiredLocale($locale, $condition = true)`, `requiredDefaultLocale($condition = true)` and `decorateTranslationField($locale, Closure $decorator)` are `Field` macros; they also work on fields inside a `Translations` schema. The decorator receives the clone (`$field`, `$component` or a typed parameter) and `$locale`, and may return a replacement field.

## The `Translations` component

For several fields in one set of tabs, or when tabs need actions or per-locale schemas.

```php
use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Happenv\FilamentTranslatable\Forms\Component\Translations;

Translations::make('content')
    ->locales(['en' => 'English', 'pl' => 'Polski'])
    ->defaultLocale('en')
    ->schema([
        TextInput::make('title')->requiredDefaultLocale(),
        RichEditor::make('body'),
        TextInput::make('slug'),
    ])
    ->exclude(['slug'])                                          // or ->include(['title', 'body'])
    ->suffixLocaleLabel(fn (Field $field): bool => $field->getName() === 'title')
    ->formatLocaleLabelUsing(fn (string $locale, string $label): string => "[{$label}]")
    ->fieldTranslatableLabel(fn (Field $field, string $locale) => __("posts.{$field->getName()}", locale: $locale))
    ->actions([
        Action::make('translateFromEnglish')
            ->action(function (array $arguments): void {
                $locale = $arguments['locale'];
                // ...
            }),
    ])
    ->contained(false);
```

- **Give every `Translations` in a form a distinct first argument.** It is the component's label and the prefix of its tab keys; actions are resolved through those keys. Do not call `translatable()` on fields inside a `Translations` schema.
- `locales()` takes a list of codes (`['en', 'pl']`), a `code => label` map, `Happenv\FilamentTranslatable\Dto\Locale` objects, a `Collection` or a `Closure` returning one of those. From a table: `fn () => Language::pluck('name', 'code')` (value first, key second).
- `schema()` may be a closure receiving the locale code: `->schema(fn (string $locale) => [TextInput::make('title')->required($locale === 'en')])`.
- In every closure of this package `$locale` is the locale **code** (`string`), never a `Locale` object.
- Only `Field` components are translated. Layout components (`Grid`, `Section`, `Fieldset`) inside the schema are kept and their fields translated recursively.
- `include()` / `exclude()` match the field **name**. An excluded field is not translated: it still shows in every tab, but each copy is bound to the same plain state path (`slug`), so one value is saved.
- Other per-component settings: `translationMode()`, `displayFlagsInLocaleLabels()`, `displayNamesInLocaleLabels()`, `flagWidth()`, `prefixLocaleLabel()`, `vertical()` and the rest of Filament's `Tabs` API (`persistTabInQueryString()`, `contained()`).

## Storage modes and form state

| Mode | Field state path | Form state | Saved by |
| --- | --- | --- | --- |
| `TranslationMode::Spatie` (default) | `title.en` | `['title' => ['en' => ..., 'pl' => ...]]` | Spatie's `setAttribute()` with an array |
| `TranslationMode::Astrotomic` | `title:en` | `['title:en' => ..., 'title:pl' => ...]` | Astrotomic's `fill()` |

```php
use Happenv\FilamentTranslatable\Enums\TranslationMode;

FilamentTranslatablePlugin::make()->translationMode(TranslationMode::Astrotomic);
Translations::make('content')->translationMode(TranslationMode::Astrotomic);
TextInput::make('name')->translatable(configureUsing: fn (Translations $t) => $t->translationMode(TranslationMode::Astrotomic));
```

- Spatie mode also works on a plain `array`/`json` cast column without `HasTranslations`: values are read with `data_get($record->title, $locale)`.
- With Astrotomic, the translation model must allow mass assignment of the translated attributes (`$fillable` on the `*Translation` model), as Astrotomic requires.
- Anything that reads or mutates form data (`mutateFormDataBeforeSave()`, `afterStateUpdated()`, `Get`/`Set` utilities) sees these shapes. With Spatie, `$get('title.en')`; with Astrotomic, `$get('title:en')`.
- Own storage: implement `Happenv\FilamentTranslatable\Drivers\TranslationDriver` (`getFieldName(string $attribute, string $locale): string` and `getTranslationFromRecord(Model $record, string $attribute, string $locale): mixed`, returning `null` when the locale has no translation) and pass an instance to `translationMode()`.

## Gotchas

- **Loading:** a field whose state is already set (e.g. by `mutateFormDataBeforeFill()` or `fill([...])`) is not overwritten from the record. A missing locale stays empty; there is no fallback to the default locale.
- **Repeaters:** inside a relationship repeater each item's own record is read. Inside a JSON repeater (no relationship) the parent page's record is never read; item state comes only from the JSON.
- **Flags** are `vendor/filament-translatable/flags/{code}.svg`, published by `php artisan vendor:publish --provider="Happenv\FilamentTranslatable\FilamentTranslatableServiceProvider" --tag=public`, not by `filament:assets`. Region codes have no file of their own (`pt_BR.svg` does not exist): pass a `Locale` with an explicit flag, `new Locale('pt_BR', 'Português (Brasil)', 'vendor/filament-translatable/flags/br.svg')`. Flags are hidden by default (`displayFlagsInLocaleLabels()`).
- Locale labels are HTML-escaped and prefixed/suffixed field labels are plain strings; do not pass HTML in them.
- `preformLocaleLabelUsing()` is a deprecated alias of `formatLocaleLabelUsing()`. `localeLabels()`, `FilamentTranslatablePlugin::get()` and the plugin getters no longer exist (5.x); configure through the methods above.
- Views: `php artisan vendor:publish --tag="filament-translatable-views"`. A published copy has to be re-published after upgrades that change the views.

## Testing

Fill and assert the form in the shape of the active mode:

```php
use function Pest\Livewire\livewire;

// Spatie
livewire(EditPost::class, ['record' => $post->getKey()])
    ->assertSchemaStateSet(['title' => ['en' => 'Hello', 'pl' => 'Cześć']])
    ->fillForm(['title' => ['en' => null, 'pl' => 'Witaj']])
    ->call('save')
    ->assertHasFormErrors(['title.en' => 'required'])     // nothing saved
    ->fillForm(['title' => ['en' => 'Hello', 'pl' => 'Witaj']])
    ->call('save')
    ->assertHasNoFormErrors();

// Astrotomic
livewire(CreatePost::class)
    ->fillForm(['title:en' => 'Hello', 'title:pl' => 'Cześć'])
    ->call('create')
    ->assertHasNoFormErrors();

expect($post->fresh()->getTranslation('title', 'pl', false))->toBe('Witaj');  // Spatie, no fallback
```

A per-locale field is addressed by its state path, e.g. `->assertSchemaComponentExists('title.pl')`. Configure locales in the test (or in `Translations::configureUsing()` in a provider the test boots); with only the fallback locale there is one tab and one field per attribute.
