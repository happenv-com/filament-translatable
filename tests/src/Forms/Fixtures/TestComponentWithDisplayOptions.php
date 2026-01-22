<?php

namespace Webard\FilamentTranslatable\Tests\Forms\Fixtures;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Webard\FilamentTranslatable\Enums\TranslationMode;
use Webard\FilamentTranslatable\Forms\Component\Translations;

class TestComponentWithDisplayOptions extends Livewire
{
    public $locales = ['en', 'pl'];

    public $displayFlagsInLocaleLabels = false;

    public $displayNamesInLocaleLabels = true;

    public $flagWidth = '24px';

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Translations::make('translations')
                    ->locales($this->locales)
                    ->defaultLocale('en')
                    ->translationMode(TranslationMode::Spatie)
                    ->displayFlagsInLocaleLabels($this->displayFlagsInLocaleLabels)
                    ->displayNamesInLocaleLabels($this->displayNamesInLocaleLabels)
                    ->flagWidth($this->flagWidth)
                    ->schema([
                        TextInput::make('title'),
                    ]),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}
