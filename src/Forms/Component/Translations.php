<?php

namespace Webard\FilamentTranslatable\Forms\Component;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Webard\FilamentTranslatable\Dto\Locale;
use Webard\FilamentTranslatable\Enums\TranslationMode;
use Webard\FilamentTranslatable\FilamentTranslatablePlugin;
use Webard\FilamentTranslatable\Forms\Component\Translations\Tab;

class Translations extends Tabs
{
    /**
     * @var view-string
     */
    // @phpstan-ignore property.defaultValue
    protected string $view = 'filament-translatable::forms.components.translations';

    /**
     * @var null|Closure|array<string>|Collection<int,string>
     */
    protected null | Closure | array | Collection $locales = null;

    protected ?string $defaultLocale = null;

    /**
     * @var null|Closure|array<string>|Collection<int,string>
     */
    protected null | Closure | array | Collection $exclude = [];

    /**
     * @var null|Closure|array<string,string>|Collection<string,string>
     */
    protected null | Closure | array | Collection $localeLabels = null;

    protected Closure | bool $hasPrefixLocaleLabel = false;

    protected Closure | bool $hasSuffixLocaleLabel = false;

    protected ?Closure $fieldTranslatableLabel = null;

    protected ?Closure $preformLocaleLabelUsing = null;

    protected int | Closure $activeTab = 1;

    protected string | Closure | null $tabQueryStringKey = null;

    protected string | Closure | null $livewireProperty = null;

    protected bool | Closure $isVertical = false;

    protected string | Closure | null $flagWidth = null;

    protected bool | Closure | null $displayFlagsInLocaleLabels = null;

    protected bool | Closure | null $displayNamesInLocaleLabels = null;

    protected Closure | TranslationMode | null $translationMode = null;

    /**
     * @param  Closure|array<string>|Collection<int,string>  $exclude
     */
    public function exclude(Closure | array | Collection $exclude): static
    {
        $this->exclude = $exclude;

        return $this;
    }

    public function translationMode(TranslationMode | Closure | null $mode): static
    {
        $this->translationMode = $mode;

        return $this;
    }

    public function getTranslationMode(): TranslationMode
    {
        return $this->evaluate($this->translationMode ?? FilamentTranslatablePlugin::get()->getTranslationMode());
    }

    public function defaultLocale(string | Closure | null $locale): static
    {
        $this->defaultLocale = $locale;

        return $this;
    }

    public function getDefaultLocale(): ?string
    {
        return $this->evaluate($this->defaultLocale ?? FilamentTranslatablePlugin::get()->getDefaultLocale());
    }

    /**
     * @param  Closure|array<string>|Collection<int,string>|null  $locales
     */
    public function locales(Closure | array | Collection | null $locales): static
    {
        $this->locales = $locales;

        return $this;
    }

    /**
     * @param  Closure|array<string>|Collection<int,string>  $labels
     */
    public function localeLabels(Closure | array | Collection $labels): static
    {
        $this->localeLabels = $labels;

        return $this;
    }

    public function prefixLocaleLabel(Closure | bool $condition = true): static
    {
        $this->hasPrefixLocaleLabel = $condition;

        return $this;
    }

    public function suffixLocaleLabel(Closure | bool $condition = true): static
    {
        $this->hasSuffixLocaleLabel = $condition;

        return $this;
    }

    public function fieldTranslatableLabel(?Closure $fieldTranslatableLabel = null): static
    {
        $this->fieldTranslatableLabel = $fieldTranslatableLabel;

        return $this;
    }

    public function preformLocaleLabelUsing(?Closure $preformLocaleLabelUsing = null): static
    {
        $this->preformLocaleLabelUsing = $preformLocaleLabelUsing;

        return $this;
    }

    /**
     * @param  Closure|Action[]|null  $actions
     */
    public function actions(null | Closure | array $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * @return array<Locale>
     */
    public function getLocales(): array
    {
        $locales = $this->evaluate($this->locales ?? FilamentTranslatablePlugin::get()->getLocales());

        if ($locales instanceof Collection) {
            $locales = $locales->all();
        }

        $preparedLocales = [];

        foreach ($locales as $key => $value) {
            // If the key is an integer, create a new Locale with the code only
            if (is_int($key)) {
                if (is_string($value)) {
                    $preparedLocales[$key] = new Locale($value);

                    continue;
                }
            }

            // If the key is a string, create a new Locale with the key as code and value as label
            if (is_string($key)) {
                $preparedLocales[$key] = new Locale($key, $value);

                continue;
            }

            // Otherwise, treat the value as a Locale
            $preparedLocales[] = $value;
        }

        return $preparedLocales;
    }

    public function getLocaleLabel(Locale $locale, bool $withFlag = true): string | Htmlable
    {
        $label = null;

        if ($this->hasFlagsInLocaleLabels() && $withFlag === true) {
            $label .= '<img src="' . \asset($locale->flag) . '" style="width:' . $this->getFlagWidth() . ';max-width:' . $this->getFlagWidth() . '" alt="' . $locale->label . '" class="inline-block align-middle' . ($this->hasNamesInLocaleLabels() ? ' me-2' : '') . '" />';
        }

        if ($this->hasNamesInLocaleLabels()) {
            $label .= $locale->label;

        }

        $label = $label ?? $locale->code;
        if ($this->hasFlagsInLocaleLabels() && $withFlag === true) {
            $label = new HtmlString('<div class="text-nowrap">' . $label . '</div>');
        }

        return $label;
    }

    public function hasPrefixLocaleLabel(Component $component, Locale $locale): bool
    {
        return boolval($this->evaluate($this->hasPrefixLocaleLabel, [
            'field' => $component,
            'locale' => $locale,
        ]) ?? false);
    }

    public function hasSuffixLocaleLabel(Component $component, Locale $locale): bool
    {
        return boolval($this->evaluate($this->hasSuffixLocaleLabel, [
            'field' => $component,
            'locale' => $locale,
        ]) ?? false);
    }

    public function getFieldTranslatableLabel(Component $component, Locale $locale): ?string
    {
        return $this->evaluate($this->fieldTranslatableLabel, [
            'field' => $component,
            'locale' => $locale,
        ]);
    }

    /**
     * @return array<Component>
     */
    public function getChildComponentsByLocale(string $locale): array
    {
        /** @var array<Component> */
        return $this->evaluate($this->childComponents, [
            'locale' => $locale,
        ]);
    }

    public function getActiveTab(): int
    {
        if ($this->isTabPersistedInQueryString()) {

            $queryStringTab = request()->query($this->getTabQueryStringKey());

            $tabs = collect($this->getChildSchemas())
                ->map(fn (Schema $schema) => collect($schema->getComponents())->first() ?? null)
                ->values();

            foreach ($tabs as $index => $tab) {

                if ($tab?->getId() !== $queryStringTab) {
                    continue;
                }

                return $index + 1;
            }
        }

        return $this->evaluate($this->activeTab, ['locales' => $this->getLocales()]);
    }

    /**
     * @return array<Schema>
     */
    public function getChildSchemas(bool $withHidden = false): array
    {
        $containers = [];

        $locales = $this->getLocales();

        foreach ($locales as $locale) {

            $containers[$locale->code] = Schema::make($this->getLivewire())
                ->parentComponent($this)
                ->components([
                    Tab::make($locale->label)
                        ->registerActions($this->getActions())
                        ->label($this->getLocaleLabel($locale))
                        ->locale($locale->code)

                        ->schema(
                            (new Collection($this->getChildComponentsByLocale($locale->code)['default']))
                                ->map(fn ($component) => $this->prepareTranslateLocaleComponent($component, $locale))
                                ->all()
                        ),
                ])
                ->getClone();
        }

        return $containers;
    }

    protected function prepareTranslateLocaleComponent(Component | Htmlable | string $component, Locale $locale): Component | Htmlable | string
    {

        if (($component instanceof Htmlable && ! $component instanceof Component) || \is_string($component)) {
            return $component;
        }

        $localeComponent = clone $component;

        if ($localeComponent instanceof Field || method_exists($localeComponent, 'getName')) {

            $localeComponentName = $localeComponent->getName();

            if (filled($localeComponentName) && is_string($localeComponentName) && ! in_array($localeComponentName, $this->exclude)) {

                // this is macro
                // @phpstan-ignore method.notFound
                $localeComponent->defaultLocale($this->getDefaultLocale());

                if ($localeComponent->requiredDefaultLocale ?? false) {
                    // this is macro
                    // @phpstan-ignore method.notFound, method.notFound
                    $localeComponent->requiredLocale($localeComponent->getDefaultLocale() ?? $this->getDefaultLocale());
                }

                assert(\method_exists($localeComponent, 'label'));
                assert(\method_exists($localeComponent, 'getLabel'));
                assert(\method_exists($component, 'getLabel'));

                $localeComponent->label($this->getFieldTranslatableLabel($component, $locale) ?? $component->getLabel());

                $localeLabel = $this->getLocaleLabel($locale, false);
                $performedLocaleLabel = $this->preformLocaleLabelUsing
                    ? $this->evaluate($this->preformLocaleLabelUsing, [
                        'locale' => $locale,
                        'label' => $localeLabel,
                    ])
                    : null;
                if (! $performedLocaleLabel) {
                    $performedLocaleLabel = "({$localeLabel})";
                }
                if ($this->hasPrefixLocaleLabel($component, $locale)) {
                    $localeComponent->label(new HtmlString("{$performedLocaleLabel} {$localeComponent->getLabel()}"));
                }
                if ($this->hasSuffixLocaleLabel($component, $locale)) {
                    $localeComponent->label("{$localeComponent->getLabel()} {$performedLocaleLabel}");
                }

                if (method_exists($localeComponent, 'name')) {
                    switch ($this->getTranslationMode()) {
                        case TranslationMode::Astrotomic:
                            $localeComponentName = "{$localeComponentName}:{$locale->code}";

                            break;
                        case TranslationMode::Spatie:
                        default:
                            $localeComponentName = "{$localeComponentName}.{$locale->code}";

                            break;
                    }

                    $localeComponent->name($localeComponentName);
                }

                $localeComponent->statePath($localeComponent->getName());
                $localeComponent->flushCachedAbsoluteStatePath();

                $decorator = $localeComponent->translationFieldDecorators ?? null;

                if (isset($decorator[$locale->code]) && is_array($decorator[$locale->code])) {
                    foreach ($decorator[$locale->code] as $callback) {
                        $localeComponent = $callback($localeComponent);
                    }
                }

            }

        } else {

            $childComponents = $localeComponent->getDefaultChildComponents();

            if ($childComponents) {

                $localeComponent->schema(
                    collect($childComponents)
                        ->map(fn ($childComponent) => $this->prepareTranslateLocaleComponent($childComponent, $locale))
                        ->all()
                );
            }
        }

        return $localeComponent;
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        if ($parameterName == 'locales') {
            return [$this->getLocales()];
        }

        return parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName);
    }

    public function displayFlagsInLocaleLabels(bool | Closure $condition = true): static
    {
        $this->displayFlagsInLocaleLabels = $condition;

        return $this;
    }

    public function hasFlagsInLocaleLabels(): bool
    {
        return $this->displayFlagsInLocaleLabels !== null ? $this->evaluate($this->displayFlagsInLocaleLabels) : FilamentTranslatablePlugin::get()->getDisplayFlagsInLocaleLabels();
    }

    public function displayNamesInLocaleLabels(bool $condition = true): static
    {
        $this->displayNamesInLocaleLabels = $condition;

        return $this;
    }

    public function hasNamesInLocaleLabels(): bool
    {
        return $this->displayNamesInLocaleLabels !== null ? $this->evaluate($this->displayNamesInLocaleLabels) : FilamentTranslatablePlugin::get()->getDisplayNamesInLocaleLabels();
    }

    public function flagWidth(string | Closure $width): static
    {
        $this->flagWidth = $width;

        return $this;
    }

    public function getFlagWidth(): string
    {
        return $this->flagWidth !== null ? $this->evaluate($this->flagWidth) : FilamentTranslatablePlugin::get()->getFlagWidth();
    }
}
