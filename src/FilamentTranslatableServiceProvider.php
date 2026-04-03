<?php

namespace Webard\FilamentTranslatable;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Webard\FilamentTranslatable\Forms\Component\Translations;

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

        Field::macro('requiredDefaultLocale', function (bool | Closure $condition = true): Field {
            /**
             * @var Field $this
             */
            // @phpstan-ignore property.notFound, varTag.nativeType
            $this->requiredDefaultLocale = true;

            return $this;
        });

        Field::macro('getDefaultLocale', function (): ?string {
            /**
             * @var Field $this
             */
            // @phpstan-ignore varTag.nativeType
            return $this->defaultLocale ?? null;
        });

        Field::macro('defaultLocale', function (?string $locale = null): Field {
            /**
             * @var Field $this
             */
            // @phpstan-ignore property.notFound, varTag.nativeType
            $this->defaultLocale = $locale;

            return $this;
        });

        Field::macro('requiredLocale', function (string $locale, bool | Closure $condition = true): Field {
            /**
             * @var Field $this
             */
            // @phpstan-ignore property.notFound, varTag.nativeType
            $this->translationFieldDecorators[$locale][] = function (Field $field) use ($condition): Field {
                $field->required($condition);

                return $field;
            };

            return $this;
        });

        Field::macro('decorateTranslationField', function (string $locale, ?Closure $decorator = null): Field {
            /**
             * @var Field $this
             */
            // @phpstan-ignore property.notFound, varTag.nativeType
            $this->translationFieldDecorators[$locale][] = $decorator;

            return $this;
        });

        Field::macro('translatable', function (bool $translatable = true, ?array $locales = null, ?Closure $translationFieldDecorator = null): Translations | Field {
            /**
             * @var Field $this
             */
            // @phpstan-ignore varTag.nativeType
            if (! $translatable) {
                return $this;
            }

            /**
             * @var Field $field
             * @var Field $this
             */
            $field = $this->getClone();

            $tabsField = Translations::make($field->getName() . '_translations')
                ->locales($locales)
                ->schema([
                    $field,
                ]);

            if ($translationFieldDecorator instanceof Closure) {
                return $translationFieldDecorator($tabsField);
            }

            return $tabsField;
        });
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
