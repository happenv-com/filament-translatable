<?php

namespace Webard\FilamentTranslatable;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Webard\FilamentTranslatable\Forms\Component\Translations;
use Webard\FilamentTranslatable\Testing\TestsFilamentTranslateField;

class FilamentTranslatableServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-translatable';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews()
            ->hasConfigFile();

        $this->publishes([
            __DIR__ . '/../resources/flags' => public_path('vendor/filament-translatable/flags'),
        ], 'public');
    }

    public function packageBooted(): void
    {
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        Field::macro('requiredDefaultLocale', function (bool | Closure $condition = true) {
            // @phpstan-ignore property.notFound
            $this->requiredDefaultLocale = true;

            return $this;
        });

        Field::macro('getDefaultLocale', function (): ?string {
            return $this->defaultLocale ?? null;
        });

        Field::macro('defaultLocale', function (?string $locale = null) {
            // @phpstan-ignore property.notFound
            $this->defaultLocale = $locale;

            return $this;
        });

        Field::macro('requiredLocale', function (string $locale, bool | Closure $condition = true) {
            // @phpstan-ignore property.notFound
            $this->translationFieldDecorators[$locale][] = function (Field $field) use ($condition) {
                $field->required($condition);

                return $field;
            };

            return $this;
        });

        Field::macro('decorateTranslationField', function (string $locale, ?Closure $decorator = null) {
            // @phpstan-ignore property.notFound
            $this->translationFieldDecorators[$locale][] = $decorator;

            return $this;
        });

        Field::macro('translatable', function (bool $translatable = true, ?array $locales = null, ?Closure $translationFieldDecorator = null) {

            if (! $translatable) {
                return $this;
            }

            /**
             * @var Field $field
             * @var Field $this
             */
            // @phpstan-ignore varTag.nativeType
            $field = $this->getClone();

            $tabsField = Translations::make($field->getName() . '_translations')
                ->locales($locales)
                ->schema([
                    $field,
                ]);

            if ($translationFieldDecorator) {
                $tabsField = $translationFieldDecorator($tabsField);
            }

            return $tabsField;
        });

        // Testing
        Testable::mixin(new TestsFilamentTranslateField);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'webard/filament-translatable';
    }

    /**
     * @return Asset[]
     */
    protected function getAssets(): array
    {
        return [
            Css::make('filament-translatable-styles', __DIR__ . '/../resources/dist/filament-translatable.css'),
        ];
    }
}
