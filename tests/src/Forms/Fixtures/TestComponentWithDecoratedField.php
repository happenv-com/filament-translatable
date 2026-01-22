<?php

namespace Webard\FilamentTranslatable\Tests\Forms\Fixtures;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Webard\FilamentTranslatable\Enums\TranslationMode;
use Webard\FilamentTranslatable\Forms\Component\Translations;

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
                            ->decorateTranslationField('pl', fn (TextInput $field) => $field->suffix('PLN'))
                            ->decorateTranslationField('en', fn (TextInput $field) => $field->prefix('$')),
                    ]),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}
