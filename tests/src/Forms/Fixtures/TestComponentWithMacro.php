<?php

namespace  Happenv\FilamentTranslatable\Tests\Forms\Fixtures;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Happenv\FilamentTranslatable\Enums\TranslationMode;
use Happenv\FilamentTranslatable\Forms\Component\Translations;

class TestComponentWithMacro extends Livewire
{
    public $locales = ['en', 'fr'];

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                // Using Translations component directly instead of macro to avoid panel dependency
                Translations::make('title-translations')
                    ->locales($this->locales)
                    ->defaultLocale('en')
                    ->translationMode(TranslationMode::Spatie)
                    ->displayFlagsInLocaleLabels(false)
                    ->displayNamesInLocaleLabels(true)
                    ->schema([
                        TextInput::make('title')
                            ->requiredDefaultLocale(),
                    ]),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}
