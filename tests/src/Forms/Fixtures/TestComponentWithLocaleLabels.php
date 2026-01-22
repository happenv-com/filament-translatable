<?php

namespace Webard\FilamentTranslatable\Tests\Forms\Fixtures;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Webard\FilamentTranslatable\Enums\TranslationMode;
use Webard\FilamentTranslatable\Forms\Component\Translations;

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
