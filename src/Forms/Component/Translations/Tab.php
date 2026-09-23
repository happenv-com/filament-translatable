<?php

namespace Happenv\FilamentTranslatable\Forms\Component\Translations;

use Happenv\FilamentTranslatable\Forms\Component\Translations;
use Illuminate\Support\Str;

class Tab extends \Filament\Schemas\Components\Tabs\Tab
{
    /**
     * @var view-string
     */
    // @phpstan-ignore property.defaultValue
    protected string $view = 'filament-translatable::forms.components.translation-tab';

    protected ?string $locale = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Prefixed with the owning Translations component, so several components can share locales.
        $this->key(function (Tab $component): string {
            $translations = $component->getContainer()->getParentComponent();
            $prefix = ($translations instanceof Translations)
                ? Str::slug(Str::transliterate((string) $translations->getLabel(), strict: true))
                : null;
            $statePath = $component->getStatePath();

            return (filled($prefix) ? "{$prefix}::" : '') . $component->getLocale() . '::' . (filled($statePath) ? "{$statePath}::tab" : 'tab');
        }, isInheritable: false);
    }

    public function locale(?string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }
}
