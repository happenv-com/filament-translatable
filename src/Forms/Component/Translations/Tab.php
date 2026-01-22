<?php

namespace Webard\FilamentTranslatable\Forms\Component\Translations;

class Tab extends \Filament\Schemas\Components\Tabs\Tab
{
    // @phpstan-ignore property.defaultValue
    protected string $view = 'filament-translatable::forms.components.translation-tab';

    protected ?string $locale = null;

    public function locale(?string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function getKey(bool $isAbsolute = true): ?string
    {
        return parent::getKey() ?? (count($this->getActions()) > 0 ? $this->getId() : null);
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }
}
