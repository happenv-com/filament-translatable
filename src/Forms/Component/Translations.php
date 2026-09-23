<?php

namespace Happenv\FilamentTranslatable\Forms\Component;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Happenv\FilamentTranslatable\Drivers\TranslationDriver;
use Happenv\FilamentTranslatable\Dto\Locale;
use Happenv\FilamentTranslatable\Enums\TranslationMode;
use Happenv\FilamentTranslatable\FilamentTranslatablePlugin;
use Happenv\FilamentTranslatable\Forms\Component\Translations\Tab;
use Happenv\FilamentTranslatable\Support\FieldTranslationSettings;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class Translations extends Tabs
{
    /**
     * @var view-string
     */
    // @phpstan-ignore property.defaultValue
    protected string $view = 'filament-translatable::forms.components.translations';

    /**
     * @var array<int|string, mixed>|Collection<int|string, mixed>|Closure|null
     */
    protected array | Collection | Closure | null $locales = null;

    protected string | Closure | null $defaultLocale = null;

    /**
     * @var null|Closure|array<string>|Collection<int,string>
     */
    protected null | Closure | array | Collection $include = null;

    /**
     * @var null|Closure|array<string>|Collection<int,string>
     */
    protected null | Closure | array | Collection $exclude = [];

    protected Closure | bool $hasPrefixLocaleLabel = false;

    protected Closure | bool $hasSuffixLocaleLabel = false;

    protected ?Closure $fieldTranslatableLabel = null;

    protected ?Closure $formatLocaleLabelUsing = null;

    protected TranslationMode | TranslationDriver | Closure $translationMode = TranslationMode::Spatie;

    protected bool | Closure $displayFlagsInLocaleLabels = false;

    protected bool | Closure $displayNamesInLocaleLabels = true;

    protected string | Closure $flagWidth = '24px';

    protected function setUp(): void
    {
        parent::setUp();

        FilamentTranslatablePlugin::current()?->configureComponent($this);
    }

    /**
     * @param  Closure|array<string>|Collection<int,string>  $include
     */
    public function include(Closure | array | Collection $include): static
    {
        $this->include = $include;

        return $this;
    }

    /**
     * @param  Closure|array<string>|Collection<int,string>  $exclude
     */
    public function exclude(Closure | array | Collection $exclude): static
    {
        $this->exclude = $exclude;

        return $this;
    }

    public function translationMode(TranslationMode | TranslationDriver | Closure $mode): static
    {
        $this->translationMode = $mode;

        return $this;
    }

    public function getTranslationDriver(): TranslationDriver
    {
        $mode = $this->evaluate($this->translationMode);

        return $mode instanceof TranslationDriver ? $mode : $mode->driver();
    }

    public function defaultLocale(string | Closure | null $locale): static
    {
        $this->defaultLocale = $locale;

        return $this;
    }

    public function getDefaultLocale(): string
    {
        return $this->evaluate($this->defaultLocale) ?? (string) config('app.fallback_locale', 'en');
    }

    /**
     * @param  array<int|string, mixed>|Collection<int|string, mixed>|Closure|null  $locales
     */
    public function locales(array | Collection | Closure | null $locales): static
    {
        $this->locales = $locales;

        return $this;
    }

    /**
     * @return array<string, Locale>
     */
    public function getLocales(): array
    {
        return Locale::collect($this->evaluate($this->locales) ?? [$this->getDefaultLocale()]);
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

    public function formatLocaleLabelUsing(?Closure $callback): static
    {
        $this->formatLocaleLabelUsing = $callback;

        return $this;
    }

    /**
     * @deprecated Use `formatLocaleLabelUsing()` instead.
     */
    public function preformLocaleLabelUsing(?Closure $callback = null): static
    {
        return $this->formatLocaleLabelUsing($callback);
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
     * @return array<string, Schema>
     */
    #[\Override]
    public function getDefaultChildSchemas(): array
    {
        $schemas = [];

        foreach ($this->getLocales() as $code => $locale) {
            $components = $this->evaluate($this->childComponents['default'] ?? [], ['locale' => $code]) ?? [];

            if ($components instanceof Schema) {
                $components = $components->getComponents(withHidden: true);
            }

            $schemas[$code] = Schema::make($this->getLivewire())
                ->parentComponent($this)
                ->components([
                    Tab::make($locale->label)
                        ->locale($code)
                        ->label($this->getLocaleLabel($locale))
                        ->registerActions($this->getActions())
                        ->schema(array_map(
                            fn (Component | Htmlable | string $component): Component | Htmlable | string => $this->prepareLocaleComponent($component, $locale),
                            $components,
                        )),
                ]);
        }

        return $schemas;
    }

    #[\Override]
    public function getActiveTab(): int
    {
        if ($this->isTabPersistedInQueryString()) {
            $queryStringTab = request()->query($this->getTabQueryStringKey());
            $position = 1;

            foreach ($this->getChildSchemas() as $schema) {
                if (Arr::first($schema->getComponents())?->getId() === $queryStringTab) {
                    return $position;
                }

                $position++;
            }
        }

        return $this->evaluate($this->activeTab);
    }

    #[\Override]
    public function callAfterStateHydrated(): static
    {
        parent::callAfterStateHydrated();

        $record = $this->getRecord();

        if (! $record instanceof Model) {
            return $this;
        }

        $driver = $this->getTranslationDriver();

        foreach ($this->getChildSchemas(withHidden: true) as $schema) {
            foreach ($schema->getFlatFields(withHidden: true) as $field) {
                $attribute = FieldTranslationSettings::getTranslatedAttribute($field);
                $locale = FieldTranslationSettings::getTranslatedLocale($field);

                if (($attribute === null) || ($locale === null) || ($field->getRawState() !== null)) {
                    continue;
                }

                $field->state($driver->getTranslationFromRecord($record, $attribute, $locale));
            }
        }

        return $this;
    }

    protected function prepareLocaleComponent(Component | Htmlable | string $component, Locale $locale): Component | Htmlable | string
    {
        if (! $component instanceof Component) {
            return $component;
        }

        $localeComponent = $component->getClone();

        if ($localeComponent instanceof Field) {
            return $this->isTranslatable($localeComponent->getName())
                ? $this->translateField($localeComponent, $locale)
                : $localeComponent;
        }

        $childComponents = $localeComponent->getDefaultChildComponents();

        if (is_array($childComponents) && filled($childComponents)) {
            $localeComponent->schema(array_map(
                fn (Component | Htmlable | string $child): Component | Htmlable | string => $this->prepareLocaleComponent($child, $locale),
                $childComponents,
            ));
        }

        return $localeComponent;
    }

    protected function isTranslatable(string $name): bool
    {
        $include = $this->evaluate($this->include);
        $include = $include instanceof Collection ? $include->all() : $include;

        if ($include !== null && ! in_array($name, $include, true)) {
            return false;
        }

        $exclude = $this->evaluate($this->exclude);
        $exclude = $exclude instanceof Collection ? $exclude->all() : ($exclude ?? []);

        return ! in_array($name, $exclude, true);
    }

    protected function translateField(Field $field, Locale $locale): Field
    {
        $attribute = $field->getName();

        $label = $this->evaluate($this->fieldTranslatableLabel, [
            'field' => $field,
            'locale' => $locale->code,
        ]) ?? $field->getLabel();

        $localeLabel = $this->formatLocaleLabel($locale);

        if ($this->evaluateForField($this->hasPrefixLocaleLabel, $field, $locale)) {
            $label = "{$localeLabel} {$label}";
        }

        if ($this->evaluateForField($this->hasSuffixLocaleLabel, $field, $locale)) {
            $label = "{$label} {$localeLabel}";
        }

        $name = $this->getTranslationDriver()->getFieldName($attribute, $locale->code);

        $field
            ->label($label)
            ->name($name)
            ->statePath($name);

        $field->flushCachedAbsoluteStatePath();

        FieldTranslationSettings::markTranslated($field, $attribute, $locale->code);

        $requiredLocales = FieldTranslationSettings::getRequiredLocales($field);
        $requiredDefaultLocale = FieldTranslationSettings::getRequiredDefaultLocale($field);

        if (array_key_exists($locale->code, $requiredLocales)) {
            $field->required($requiredLocales[$locale->code]);
        } elseif (($requiredDefaultLocale !== null) && ($locale->code === $this->getDefaultLocale())) {
            $field->required($requiredDefaultLocale);
        }

        foreach (FieldTranslationSettings::getDecorators($field, $locale->code) as $decorator) {
            $decorated = $field->evaluate(
                $decorator,
                namedInjections: ['field' => $field, 'component' => $field, 'locale' => $locale->code],
                typedInjections: [Field::class => $field, $field::class => $field],
            );

            if ($decorated instanceof Field) {
                $field = $decorated;
            }
        }

        return $field;
    }

    protected function evaluateForField(bool | Closure $condition, Field $field, Locale $locale): bool
    {
        return (bool) $this->evaluate($condition, [
            'field' => $field,
            'locale' => $locale->code,
        ]);
    }

    public function getLocaleLabel(Locale $locale, bool $withFlag = true): string | Htmlable
    {
        $withName = $this->hasNamesInLocaleLabels();

        if (! ($withFlag && $this->hasFlagsInLocaleLabels())) {
            return $withName ? $locale->label : $locale->code;
        }

        $width = e($this->getFlagWidth());

        $html = '<img src="' . e(asset($locale->flag)) . '"'
            . ' style="width:' . $width . ';max-width:' . $width . '"'
            . ' alt="' . e($locale->label) . '"'
            . ' class="inline-block align-middle' . ($withName ? ' me-2' : '') . '" />';

        if ($withName) {
            $html .= e($locale->label);
        }

        return new HtmlString('<div class="text-nowrap">' . $html . '</div>');
    }

    public function formatLocaleLabel(Locale $locale): string
    {
        $label = (string) $this->getLocaleLabel($locale, withFlag: false);

        $formatted = $this->evaluate($this->formatLocaleLabelUsing, [
            'locale' => $locale->code,
            'label' => $label,
        ]);

        return filled($formatted) ? (string) $formatted : "({$label})";
    }

    /**
     * @return array<mixed>
     */
    #[\Override]
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        if ($parameterName === 'locales') {
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
        return (bool) $this->evaluate($this->displayFlagsInLocaleLabels);
    }

    public function displayNamesInLocaleLabels(bool | Closure $condition = true): static
    {
        $this->displayNamesInLocaleLabels = $condition;

        return $this;
    }

    public function hasNamesInLocaleLabels(): bool
    {
        return (bool) $this->evaluate($this->displayNamesInLocaleLabels);
    }

    public function flagWidth(string | Closure $width): static
    {
        $this->flagWidth = $width;

        return $this;
    }

    public function getFlagWidth(): string
    {
        return (string) $this->evaluate($this->flagWidth);
    }
}
