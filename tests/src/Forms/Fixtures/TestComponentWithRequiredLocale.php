<?php

namespace Happenv\FilamentTranslatable\Tests\Forms\Fixtures;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Happenv\FilamentTranslatable\Enums\TranslationMode;
use Happenv\FilamentTranslatable\Forms\Component\Translations;
use Illuminate\Contracts\View\View;

class TestComponentWithRequiredLocale extends Livewire
{
    public $locales = ['en', 'fr', 'pl'];

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Translations::make('translations')
                    ->locales($this->locales)
                    ->defaultLocale('en')
                    ->translationMode(TranslationMode::Spatie)
                    ->displayFlagsInLocaleLabels(false)
                    ->displayNamesInLocaleLabels(true)
                    ->schema([
                        TextInput::make('title')
                            ->requiredLocale('en')
                            ->requiredLocale('fr'),
                    ]),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}
