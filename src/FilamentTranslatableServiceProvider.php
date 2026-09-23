<?php

namespace Happenv\FilamentTranslatable;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Happenv\FilamentTranslatable\Forms\Component\Translations;
use Happenv\FilamentTranslatable\Support\FieldTranslationSettings;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentTranslatableServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-translatable';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews();

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

        Field::macro('requiredLocale', function (string $locale, bool | Closure $condition = true): Field {
            FieldTranslationSettings::addRequiredLocale($this, $locale, $condition);

            return $this;
        });

        Field::macro('requiredDefaultLocale', function (bool | Closure $condition = true): Field {
            FieldTranslationSettings::setRequiredDefaultLocale($this, $condition);

            return $this;
        });

        Field::macro('decorateTranslationField', function (string $locale, Closure $decorator): Field {
            FieldTranslationSettings::addDecorator($this, $locale, $decorator);

            return $this;
        });

        Field::macro('translatable', function (bool $condition = true, array | Closure | null $locales = null, ?Closure $configureUsing = null): Translations | Field {
            if (! $condition) {
                return $this;
            }

            $translations = Translations::make($this->getName() . '_translations')
                ->schema([$this->getClone()]);

            if ($locales !== null) {
                $translations->locales($locales);
            }

            if ($configureUsing !== null) {
                $translations = $configureUsing($translations) ?? $translations;
            }

            return $translations;
        });
    }

    protected function getAssetPackageName(): ?string
    {
        return 'happenv-com/filament-translatable';
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
