# Changelog

All notable changes to `filament-translatable` will be documented in this file.

## 5.0.0 - unreleased

- The `Translations` component works without a panel plugin; settings resolve: defaults → current panel plugin → `configureUsing()` → instance.
- Astrotomic support without a custom model trait (`AstrotomicTranslatable` is deprecated).
- Pluggable `TranslationDriver` (Spatie and Astrotomic built in).
- Per-locale schemas are cached; locale labels are escaped; tab keys use the component name and locale code (unique across components).
- Actions registered on locale tabs can be resolved again (`Tab::getKey()` ignored `$isAbsolute`).
- Translations are loaded from the record before fields hydrate (casts such as `Toggle`/`TagsInput` see real values) and only from the record that owns the component's state level (not inside JSON repeater items).
- Field macros store settings in field meta (no dynamic properties); `requiredDefaultLocale()` respects its condition.
- Test suite rebuilt: end-to-end tests for Spatie and Astrotomic, including resource pages; CI on Filament 4 and 5.

See [UPGRADING.md](UPGRADING.md) for breaking changes.

## 2.0.0 - 2025-08-12

- initial release
