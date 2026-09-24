# Changelog

All notable changes to `filament-translatable` are documented in this file. Each section is written automatically from the GitHub release notes when a release is published — do not edit it by hand.

## v5.0.0 - 2026-09-24

## What's Changed
* Bump actions/setup-node from 6 to 7 by @dependabot[bot] in https://github.com/happenv-com/filament-translatable/pull/20
* Bump axios from 1.16.0 to 1.18.1 by @dependabot[bot] in https://github.com/happenv-com/filament-translatable/pull/21
* Bump postcss from 8.5.14 to 8.5.25 by @dependabot[bot] in https://github.com/happenv-com/filament-translatable/pull/22
* Bump browserslist from 4.28.2 to 4.28.9 by @dependabot[bot] in https://github.com/happenv-com/filament-translatable/pull/23
* Bump postcss-selector-parser from 7.1.1 to 7.1.6 by @dependabot[bot] in https://github.com/happenv-com/filament-translatable/pull/24
* Bump nanoid from 3.3.16 to 3.3.18 by @dependabot[bot] in https://github.com/happenv-com/filament-translatable/pull/25
- The `Translations` component works without a panel plugin; settings resolve: defaults → current panel plugin → `configureUsing()` → instance.
- Astrotomic support without a custom model trait (`AstrotomicTranslatable` is deprecated).
- Pluggable `TranslationDriver` (Spatie and Astrotomic built in).
- Per-locale schemas are cached; locale labels are escaped; tab keys use the component name and locale code (unique across components).
- Actions registered on locale tabs can be resolved again (`Tab::getKey()` ignored `$isAbsolute`).
- Translations are loaded from the record before fields hydrate (casts such as `Toggle`/`TagsInput` see real values) and only from the record that owns the component's state level (not inside JSON repeater items).
- Field macros store settings in field meta (no dynamic properties); `requiredDefaultLocale()` respects its condition.
- Test suite rebuilt: end-to-end tests for Spatie and Astrotomic, including resource pages; CI on Filament 4 and 5.

See [UPGRADING.md](UPGRADING.md) for breaking changes.


**Full Changelog**: https://github.com/happenv-com/filament-translatable/compare/v4.0.1...v5.0.0

## v4.0.1 - 2026-07-13

**Full Changelog**: https://github.com/happenv-com/filament-translatable/compare/v4.0.0...v4.0.1

## v4.0.0 - 2026-07-13

## What's Changed
* Bump axios from 1.14.0 to 1.15.0 by @dependabot[bot] in https://github.com/happenv-com/filament-translatable/pull/11
* Bump follow-redirects from 1.15.11 to 1.16.0 by @dependabot[bot] in https://github.com/happenv-com/filament-translatable/pull/12
* Bump dependabot/fetch-metadata from 3.0.0 to 3.1.0 by @dependabot[bot] in https://github.com/happenv-com/filament-translatable/pull/13
* Bump axios from 1.15.0 to 1.16.0 by @dependabot[bot] in https://github.com/happenv-com/filament-translatable/pull/14
* Bump postcss from 8.5.8 to 8.5.14 by @dependabot[bot] in https://github.com/happenv-com/filament-translatable/pull/15
* Bump form-data from 4.0.5 to 4.0.6 by @dependabot[bot] in https://github.com/happenv-com/filament-translatable/pull/17
* feat: add include option for translatable fields by @kihoro2d in https://github.com/happenv-com/filament-translatable/pull/19
* Bump actions/cache from 5 to 6 by @dependabot[bot] in https://github.com/happenv-com/filament-translatable/pull/18
* Bump actions/checkout from 6 to 7 by @dependabot[bot] in https://github.com/happenv-com/filament-translatable/pull/16

## New Contributors
* @kihoro2d made their first contribution in https://github.com/happenv-com/filament-translatable/pull/19

**Full Changelog**: https://github.com/happenv-com/filament-translatable/compare/v3.3.2...v4.0.0

## v3.3.2 - 2026-04-03

## What's Changed
* Bump actions/checkout from 4 to 6 by @dependabot[bot] in https://github.com/webard/filament-translatable/pull/5
* Bump picomatch from 4.0.3 to 4.0.4 by @dependabot[bot] in https://github.com/webard/filament-translatable/pull/9
* Bump dependabot/fetch-metadata from 2.5.0 to 3.0.0 by @dependabot[bot] in https://github.com/webard/filament-translatable/pull/10
* Bump ramsey/composer-install from 3 to 4 by @dependabot[bot] in https://github.com/webard/filament-translatable/pull/8
* Update translations.blade.php use qualified class name for Arr by @wienczny in https://github.com/webard/filament-translatable/pull/7

## New Contributors
* @wienczny made their first contribution in https://github.com/webard/filament-translatable/pull/7

**Full Changelog**: https://github.com/webard/filament-translatable/compare/v3.3.1...v3.3.2

## v3.3.1 - 2026-02-12

## What's Changed
* Feature: tests by @webard in https://github.com/webard/filament-translatable/pull/2
* Bump actions/checkout from 4 to 6 by @dependabot[bot] in https://github.com/webard/filament-translatable/pull/3
* New readme badges by @webard in https://github.com/webard/filament-translatable/pull/4
* Fix: evaluate by @webard in https://github.com/webard/filament-translatable/pull/6

## New Contributors
* @webard made their first contribution in https://github.com/webard/filament-translatable/pull/2

**Full Changelog**: https://github.com/webard/filament-translatable/compare/v3.3.0...v3.3.1

## v3.3.0 - 2026-01-22

## What's Changed
* Bump tar from 7.4.3 to 7.5.6 by @dependabot[bot] in https://github.com/webard/filament-translatable/pull/1

## New Contributors
* @dependabot[bot] made their first contribution in https://github.com/webard/filament-translatable/pull/1

**Full Changelog**: https://github.com/webard/filament-translatable/compare/v3.2.0...v3.3.0

## v2.1.0 - 2026-01-22

**Full Changelog**: https://github.com/webard/filament-translatable/compare/v2.0.3...v2.1.0
