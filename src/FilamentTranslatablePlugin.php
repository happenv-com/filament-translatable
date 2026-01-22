<?php

namespace Webard\FilamentTranslatable;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Webard\FilamentTranslatable\Enums\TranslationMode;

class FilamentTranslatablePlugin implements Plugin
{
    use EvaluatesClosures;

    /**
     * @var array<string>
     */
    protected array $locales = [];

    protected ?string $defaultLocale = null;

    protected ?Closure $getLocaleLabelUsing = null;

    protected bool | Closure $displayFlagsInLocaleLabels = false;

    protected bool | Closure $displayNamesInLocaleLabels = true;

    protected string | Closure $flagWidth = '24px';

    protected TranslationMode $translationMode = TranslationMode::Spatie;

    public function getId(): string
    {
        return 'filament-translatable';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    /**
     * @param  array<string> | null  $locales
     */
    public function locales(?array $locales = null): static
    {
        $this->locales = $locales;

        return $this;
    }

    public function translationMode(TranslationMode $mode): static
    {
        $this->translationMode = $mode;

        return $this;
    }

    public function getTranslationMode(): TranslationMode
    {
        return $this->translationMode;
    }

    public function displayFlagsInLocaleLabels(bool $condition = true): static
    {
        $this->displayFlagsInLocaleLabels = $condition;

        return $this;
    }

    public function getDisplayFlagsInLocaleLabels(): bool
    {
        return $this->displayFlagsInLocaleLabels;
    }

    public function displayNamesInLocaleLabels(bool $condition = true): static
    {
        $this->displayNamesInLocaleLabels = $condition;

        return $this;
    }

    public function getDisplayNamesInLocaleLabels(): bool
    {
        return $this->displayNamesInLocaleLabels;
    }

    public function getLocaleLabelUsing(?Closure $callback): static
    {
        $this->getLocaleLabelUsing = $callback;

        return $this;
    }

    public function flagWidth(string | Closure $width): static
    {
        $this->flagWidth = $width;

        return $this;
    }

    public function getFlagWidth(): string
    {
        return $this->evaluate($this->flagWidth);
    }

    /**
     * @return array<string>
     */
    public function getLocales(): array
    {
        return $this->locales;
    }

    public function defaultLocale(string | Closure $locale): static
    {
        $this->defaultLocale = $locale;

        return $this;
    }

    public function getDefaultLocale(): string
    {
        return $this->evaluate($this->defaultLocale ?? config('app.locale', 'en'));
    }

    public function getLocaleLabel(string $locale, ?string $displayLocale = null): ?string
    {
        $displayLocale ??= app()->getLocale();

        $label = null;

        if ($callback = $this->getLocaleLabelUsing) {
            $label = $callback($locale, $displayLocale);
        }

        return $label ?? (locale_get_display_name($locale, $displayLocale) ?: null);
    }
}
