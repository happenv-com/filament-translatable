<?php

namespace  Happenv\FilamentTranslatable\Tests\Forms\Fixtures;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Happenv\FilamentTranslatable\Enums\TranslationMode;
use Happenv\FilamentTranslatable\Forms\Component\Translations;

class TestComponentWithAstrotomic extends Livewire
{
    public array $translateConfig = [];

    public $locales;

    public function form(Schema $form): Schema
    {
        $exclude = $this->translateConfig['exclude'] ?? [];

        return $form
            ->components([
                Translations::make('translations')
                    ->localeLabels([
                        'pl' => 'pl',
                        'fr' => 'fr',
                    ])
                    ->displayFlagsInLocaleLabels(false)
                    ->displayNamesInLocaleLabels(true)
                    ->locales($this->locales)
                    ->defaultLocale('en')
                    ->translationMode(TranslationMode::Astrotomic)
                    ->schema([
                        TextInput::make('title')->requiredDefaultLocale(),
                        Textarea::make('content'),
                    ])

                    ->exclude($exclude),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}
