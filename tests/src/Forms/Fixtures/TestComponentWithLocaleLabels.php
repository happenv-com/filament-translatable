<?php

namespace  Happenv\FilamentTranslatable\Tests\Forms\Fixtures;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Happenv\FilamentTranslatable\Enums\TranslationMode;
use Happenv\FilamentTranslatable\Forms\Component\Translations;

class TestComponentWithLocaleLabels extends Livewire
{
    public $prefixLocaleLabel = false;

    public $suffixLocaleLabel = false;

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Translations::make('translations')
                    // Pass locales with labels as key-value pairs
                    ->locales([
                        'en' => 'English',
                        'pl' => 'Polski',
                    ])
                    ->defaultLocale('en')
                    ->translationMode(TranslationMode::Spatie)
                    ->displayFlagsInLocaleLabels(false)
                    ->displayNamesInLocaleLabels(true)
                    ->prefixLocaleLabel($this->prefixLocaleLabel)
                    ->suffixLocaleLabel($this->suffixLocaleLabel)
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
