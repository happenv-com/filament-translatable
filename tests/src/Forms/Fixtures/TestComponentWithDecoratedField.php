<?php

namespace  Happenv\FilamentTranslatable\Tests\Forms\Fixtures;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Happenv\FilamentTranslatable\Enums\TranslationMode;
use Happenv\FilamentTranslatable\Forms\Component\Translations;

class TestComponentWithDecoratedField extends Livewire
{
    public $locales = ['en', 'pl'];

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                // Using Translations component directly instead of macro to avoid panel dependency
                Translations::make('price_translations')
                    ->locales($this->locales)
                    ->defaultLocale('en')
                    ->translationMode(TranslationMode::Spatie)
                    ->displayFlagsInLocaleLabels(false)
                    ->displayNamesInLocaleLabels(true)
                    ->schema([
                        TextInput::make('price')
                            ->decorateTranslationField('pl', fn (TextInput $field): TextInput => $field->suffix('PLN'))
                            ->decorateTranslationField('en', fn (TextInput $field): TextInput => $field->prefix('$')),
                    ]),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}
