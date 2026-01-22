# Filament Translatable

[![Latest Version on Packagist](https://img.shields.io/packagist/v/webard/filament-translatable.svg?style=flat-square)](https://packagist.org/packages/webard/filament-translatable)
[![Total Downloads](https://img.shields.io/packagist/dt/webard/filament-translatable.svg?style=flat-square)](https://packagist.org/packages/webard/filament-translatable)

Filament Translatable is a set of tools that help manage translations.

![translatable component](https://raw.githubusercontent.com/webard/filament-translatable/refs/heads/v3/screenshots/component.png)

## Installation

| Filament Version | Filament Translate Field Version |
|------------------|---------------------------|
| 3.x              | 2.x
| 4.x              | 3.x
| 5.x              | 3.x


You can install the package via composer:

```bash
composer require webard/filament-translatable
```

Publish the assets:

```bash
php artisan filament:assets
```


## Configuration

## With `spatie/laravel-translatable`

The package from [Spatie](https://github.com/spatie/laravel-translatable) is the default supported way of handling translations. Follow the instructions in the [README](https://github.com/spatie/laravel-translatable/?tab=readme-ov-file#a-trait-to-make-eloquent-models-translatable) to properly configure your models.

## With `astrotomic/laravel-translatable`

The package from [Astrotomic](https://github.com/astrotomic/laravel-translatable) is an alternative supported way of handling translations.

Follow the [instructions](https://docs.astrotomic.info/laravel-translatable/installation#models) to properly configure your models, but instead of using the `Translatable` trait from the Astrotomic package, please use `Webard\FilamentTranslatable\Traits\AstrotomicTranslatable`.

If you use the Astrotomic package, please configure the plugin to work in Astrotomic mode:

```php
use Webard\FilamentTranslatable\Enums\TranslationMode;

FilamentTranslatablePlugin::make()
    ->translationMode(TranslationMode::Astrotomic)
```

You can also configure `translationMode` per component:

```php
 Translations::make('translations')
    ->translationMode(TranslationMode::Astrotomic)
```

Or per field:

```php
 TextInput::make('name')
    ->translatable()
    ->translationMode(TranslationMode::Astrotomic)
```

## Setup

```php
use Webard\FilamentTranslatable\FilamentTranslatablePlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(FilamentTranslatablePlugin::make());
}
```
  
### Setting translatable locales
 
To set up the locales that can be used to translate content, pass an array of locales to the `locales()` plugin method:
  
```php
FilamentTranslatablePlugin::make()
     ->locales(['en', 'pl', 'fr']),
```


You can set locale labels using key => value array:

```php
FilamentTranslatablePlugin::make()
    ->locales([
        'pl' => __('Polish'),
        'en' => __('English')
    ])
```

Also, you can pass a Closure:
```php
FilamentTranslatablePlugin::make()
    ->locales(fn () => Language::pluck('code', 'name'))
```

### Setting default locale

You can set the default locale using the `defaultLocale()` method:

```php
FilamentTranslatablePlugin::make()
     ->defaultLocale('pl'),
```

### Enable or disable flags in locale labels

You can enable or disable flags in locale labels (disabled by default):

```php
FilamentTranslatablePlugin::make()
    ->displayFlagsInLocaleLabels(true)
```

### Setting flag width

You can set the flag width using:

```php
FilamentTranslatablePlugin::make()
    ->flagWidth('24px')
```

### Enable or disable names in locale labels

You can enable or disable locale names in locale labels (enabled by default):

```php
FilamentTranslatablePlugin::make()
    ->displayNamesInLocaleLabels(false)
```

Otherwise, the config value `app.locale` will be used.

This affects several methods that can be used on fields.

## Usage

### `translatable()` macro

By using the `translatable()` macro, you can quickly configure a single [form field](https://filamentphp.com/docs/4.x/forms/fields/getting-started) to support multiple languages and provide translations for each locale.

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->translatable()
```

![translatable macro](https://raw.githubusercontent.com/webard/filament-translatable/refs/heads/v3/screenshots/macro.png)

#### Marking field as required for specified locale

This way, the "name" field will only be required in the "en" language.

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->requiredLocale('en')
    ->translatable()
```

#### Marking field as required for default locale

This way, the "name" field will only be required in the default language.

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->requiredDefaultLocale()
    ->translatable()
```

#### Decorating language specified fields

You can fully customize language-specific fields using the `decorateTranslationField()` method.

```php
use Filament\Forms\Components\TextInput;

TextInput::make('price')
    ->decorateTranslationField('pl', fn (TextInput $field) => $field->suffix('PLN'))
    ->decorateTranslationField('en', fn (TextInput $field) => $field->prefix('USD')),
    ->translatable()
```

#### Customizing `Translations` component

After using the `translatable()` method, the context of the field is switched to the `Translations` component, so you can use any method that belongs to the component.

```php
use Filament\Forms\Components\TextInput;

TextInput::make('price')
    ->requiredDefaultLocale()
    ->translatable() // Here context is switched from TextInput to Translations component
    ->vertical()
    ->displayFlagsInLocaleLabels(true)
    ->displayNamesInLocaleLabels(false)
    ->flagWidth('48px')
```

![translatable custom macro](https://raw.githubusercontent.com/webard/filament-translatable/refs/heads/v3/screenshots/macro2.png)

> [!CAUTION]
> Be sure to set field-specific methods like `required()` or `requiredDefaultLocale()` **before** calling the `translatable()` method.


### `Translations` component

By using the `Translations` component, you can easily configure your [form fields](https://filamentphp.com/docs/4.x/forms/fields/getting-started) to support multiple languages and provide translations for each locale.

```php
use Webard\FilamentTranslatable\Forms\Component\Translations;

Translations::make('translations') // name is required to properly handle actions
    ->schema([
        TextInput::make('name')
    ])
```

![translatable horizontal component](https://raw.githubusercontent.com/webard/filament-translatable/refs/heads/v3/screenshots/horizontal-component.png)

> [!NOTE]
> Using the `translatable()` method within the `Translations` component is not needed.

> [!IMPORTANT]
> Be sure to set different names for each `Translations` component when using multiple instances.

#### Setting the translatable locales for a particular fields

By default, the translatable locales can be set globally for all translation form components in the plugin configuration. Alternatively, you can customize the translatable locales for a particular resource by overriding the `locales()` method in the `Translate` class:

```php
Translations::make('translations')
    ->locales(['en', 'es'])
```


#### Setting the translatable label for a particular field

You have the flexibility to customize the translation label for each field in each locale. You can use the `fieldTranslatableLabel()` method to provide custom labels based on the field instance and the current locale.

```php
use Webard\FilamentTranslatable\Forms\Component\Translations;

 Translations::make()
    ->schema([
        // Fields
    ])
    ->fieldTranslatableLabel(fn ($field, $locale) => __($field->getName(), locale: $locale))
```

#### Adding prefix/suffix locale label to the field

If you want to add a prefix or suffix locale label to the form field, you can use the `prefixLocaleLabel()` or `suffixLocaleLabel()` method. This makes it easier for users to identify the language associated with each field.

```php
use Webard\FilamentTranslatable\Forms\Component\Translations;

Translations::make('translations')
    ->schema([
        // Fields
    ])
    ->prefixLocaleLabel()
    ->suffixLocaleLabel()
```

#### Setting the locale display name

By default, the prefix/suffix locale display name is generated from the locale code and enclosed in parentheses, "()". You may customize this using the `preformLocaleLabelUsing()` method:

```php
use Webard\FilamentTranslatable\Forms\Component\Translations;

Translations::make('translations')
    ->preformLocaleLabelUsing(fn (string $locale, string $label) => "[{$label}]");
```

#### Injecting the current form field

Additionally, if you need to access the current form field instance, you can inject the `$field` parameter into the callback functions. This allows you to perform specific actions or apply conditions based on the field being processed.

```php
use Filament\Forms\Components\Component;
use Webard\FilamentTranslatable\Forms\Component\Translations;

Translations::make('translations')
    // ...
    ->prefixLocaleLabel(function(Component $field) {
        // need return boolean value
        return $field->getName() == 'title';
    })
    ->suffixLocaleLabel(function(Component $field) {
        // need return boolean value
        return $field->getName() == 'title';
    })

```

#### Adding action 

You may add actions before each container of child components using the `actions()` method:

```php

use Filament\Forms\Components\Actions\Action;
use Webard\FilamentTranslatable\Forms\Component\Translations;

Translations::make('translations')
    ->actions([
        Action::make('fillDumpTitle')
    ])
```

#### Injecting the locale on current child container

If you wish to access the locale that has been passed to the action, define an `$arguments` parameter and get the value of `locale` from `$arguments`:

```php

use Filament\Forms\Components\Actions\Action;
use Webard\FilamentTranslatable\Forms\Component\Translations;

Translations::make()
    ->actions([
        Action::make('fillDumpTitle')
            ->action(function (array $arguments) {
                $locale = $arguments['locale'];
                // ...
            })
    ])
```


#### Injecting the locale to form field

If you wish to access the current locale instance for the field, define a `$locale` parameter:

```php

use Filament\Forms\Components\TextInput;
use Webard\FilamentTranslatable\Forms\Component\Translations;

Translations::make()
    ->schema(fn (string $locale) => [TextInput::make('title')->required($locale == 'en')])
```

#### Removing the styled container
By default, the translate component and its content are wrapped in a container styled as a card. You may remove the styled container using `contained()`:

```php
use Webard\FilamentTranslatable\Forms\Component\Translations;
 
Translations::make()
    ->contained(false)
```

#### Vertical tabs

You can display translations as vertical tabs:

```php
use Webard\FilamentTranslatable\Forms\Component\Translations;
 
Translations::make()
    ->vertical()
```

#### Changing plugin settings

You can customize plugin settings directly on the component:

```php
use Webard\FilamentTranslatable\Forms\Component\Translations;
 
Translations::make()
    ->displayNamesInLocaleLabels(false)
    ->displayFlagsInLocaleLabels(true)
    ->flagWidth('48px')
```

#### Exclude 
The `exclude` feature allows you to specify fields that you don't want to include in the translation process. This can be useful for fields that contain dynamic content or that shouldn't be translated into other languages.

```php
use Webard\FilamentTranslatable\Forms\Component\Translations;
 
Translations::make('translations')
    ->schema([
        Forms\Components\TextInput::make('title'),
        Forms\Components\TextInput::make('description'),
    ])
    ->exclude(['description'])
```
Without `exclude`:
```json
{
    "title": {
        "en": "Dump",
        "es": "Dump",
        "fr": "Dump"
    },
    "description": {
        "en": null,
        "es": null,
        "fr": null
    }
}
```
With `exclude`:
```json
{
    "title": {
        "en": "Dump",
        "es": "Dump",
        "fr": "Dump"
    },
    "description": null
}
```
## Publishing Views

To publish the views, run:

```bash
php artisan vendor:publish --provider="Webard\\FilamentTranslatable\\FilamentTranslatableProvider" --tag="filament-translatable-views"
```

## Testing

```bash
composer test
```

## Changelog

See the [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

See [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

If you discover any security-related issues, please email code@webard.me instead of using the issue tracker.

## Credits

- [Lipis](https://github.com/lipis/flag-icons) for icons
- [Solution Forest](https://github.com/solutionforest/filament-translate-field) for great inspiration
- [Outer Web](https://github.com/outer-web/filament-translatable-fields) for the macro idea
- [All Contributors](../../contributors)

## License

Filament Translatable is open-sourced software licensed under the [MIT license](LICENSE.md).

