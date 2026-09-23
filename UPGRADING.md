# Upgrading

## From 4.x to 5.x

### The plugin is optional

- `FilamentTranslatablePlugin::get()` was removed. Use `FilamentTranslatablePlugin::current()`, which returns `null` when the current panel has no plugin registered.
- The plugin getters (`getLocales()`, `getDefaultLocale()`, `getTranslationMode()`, `getDisplayFlagsInLocaleLabels()`, `getDisplayNamesInLocaleLabels()`, `getFlagWidth()`) were removed. The plugin now applies its settings to each `Translations` component when the component is set up.
- Without a plugin the component no longer throws an exception; it uses defaults. Settings resolve from: package defaults → plugin of the current panel → `Translations::configureUsing()` → the component instance. See "Where settings come from" in the README.

### Astrotomic

- Replace `Happenv\FilamentTranslatable\Traits\AstrotomicTranslatable` with `Astrotomic\Translatable\Translatable` in your models. The component now loads each locale from the record itself. The old trait still works but is deprecated and will be removed in 6.0.
- As a result, your models' `toArray()` / JSON output no longer contains `title:en`-style keys.

### `Translations` component

- `localeLabels()` was removed — pass labels via `locales(['pl' => 'Polski'])`.
- `getTranslationMode()` was replaced by `getTranslationDriver()`. `translationMode()` also accepts a `TranslationDriver` instance.
- `TranslationMode` is now a string-backed enum (`spatie`, `astrotomic`).
- `preformLocaleLabelUsing()` is deprecated — use `formatLocaleLabelUsing()`.
- In all closures of the package `$locale` is now the locale **code** (`string`). In 4.x, `fieldTranslatableLabel()`, `prefixLocaleLabel()`, `suffixLocaleLabel()` and `preformLocaleLabelUsing()` received a `Locale` object.
- Field labels with a locale prefix/suffix are plain strings (no `HtmlString`), and locale labels are HTML-escaped.
- Tab keys are built from the locale code (e.g. `en::data::tab`) instead of the label.
- Only `Field` components are translated; other components inside the schema are traversed for nested fields.

### Field macros

- The `Field::defaultLocale()` and `Field::getDefaultLocale()` macros were removed.
- `requiredDefaultLocale($condition)` now respects `$condition`.
- The third argument of `translatable()` was renamed to `configureUsing`. Prefer named arguments: `translatable(locales: [...], configureUsing: fn (Translations $translations) => ...)`.
