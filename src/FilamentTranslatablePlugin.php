<?php

namespace Happenv\FilamentTranslatable;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Happenv\FilamentTranslatable\Drivers\TranslationDriver;
use Happenv\FilamentTranslatable\Enums\TranslationMode;
use Happenv\FilamentTranslatable\Forms\Component\Translations;
use Illuminate\Support\Collection;

class FilamentTranslatablePlugin implements Plugin
{
    public const ID = 'filament-translatable';

    /**
     * @var array<int|string, mixed>|Collection<int|string, mixed>|Closure|null
     */
    protected array | Collection | Closure | null $locales = null;

    protected string | Closure | null $defaultLocale = null;

    protected TranslationMode | TranslationDriver | Closure | null $translationMode = null;

    protected bool | Closure | null $displayFlagsInLocaleLabels = null;

    protected bool | Closure | null $displayNamesInLocaleLabels = null;

    protected string | Closure | null $flagWidth = null;

    public function getId(): string
    {
        return self::ID;
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * The plugin registered on the current panel, if any.
     */
    public static function current(): ?static
    {
        $panel = Filament::getCurrentPanel();

        if (! $panel?->hasPlugin(self::ID)) {
            return null;
        }

        /** @var static */
        return $panel->getPlugin(self::ID);
    }

    /**
     * Applies the explicitly configured settings to the component.
     */
    public function configureComponent(Translations $component): void
    {
        if ($this->locales !== null) {
            $component->locales($this->locales);
        }

        if ($this->defaultLocale !== null) {
            $component->defaultLocale($this->defaultLocale);
        }

        if ($this->translationMode !== null) {
            $component->translationMode($this->translationMode);
        }

        if ($this->displayFlagsInLocaleLabels !== null) {
            $component->displayFlagsInLocaleLabels($this->displayFlagsInLocaleLabels);
        }

        if ($this->displayNamesInLocaleLabels !== null) {
            $component->displayNamesInLocaleLabels($this->displayNamesInLocaleLabels);
        }

        if ($this->flagWidth !== null) {
            $component->flagWidth($this->flagWidth);
        }
    }

    /**
     * @param  array<int|string, mixed>|Collection<int|string, mixed>|Closure|null  $locales
     */
    public function locales(array | Collection | Closure | null $locales): static
    {
        $this->locales = $locales;

        return $this;
    }

    public function defaultLocale(string | Closure | null $locale): static
    {
        $this->defaultLocale = $locale;

        return $this;
    }

    public function translationMode(TranslationMode | TranslationDriver | Closure | null $mode): static
    {
        $this->translationMode = $mode;

        return $this;
    }

    public function displayFlagsInLocaleLabels(bool | Closure $condition = true): static
    {
        $this->displayFlagsInLocaleLabels = $condition;

        return $this;
    }

    public function displayNamesInLocaleLabels(bool | Closure $condition = true): static
    {
        $this->displayNamesInLocaleLabels = $condition;

        return $this;
    }

    public function flagWidth(string | Closure $width): static
    {
        $this->flagWidth = $width;

        return $this;
    }
}
