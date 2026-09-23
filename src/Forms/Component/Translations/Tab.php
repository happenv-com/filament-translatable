<?php

namespace Happenv\FilamentTranslatable\Forms\Component\Translations;

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

        $this->key(function (Tab $component): string {
            $statePath = $component->getStatePath();

            return $component->getLocale() . '::' . (filled($statePath) ? "{$statePath}::tab" : 'tab');
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
