<?php

namespace Webard\FilamentTranslatable\Tests\Forms\Fixtures;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Webard\FilamentTranslatable\Enums\TranslationMode;
use Webard\FilamentTranslatable\Forms\Component\Translations;

class TestComponentWithActions extends Livewire
{
    public array $translateConfig = [];

    public $locales;

    public array $actionCallLog = [];

    public function form(Schema $form): Schema
    {
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
                    ->translationMode(TranslationMode::Spatie)
                    ->actions([
                        Action::make('fillTitle')
                            ->label('Fill Title')
                            ->action(function (array $arguments) {
                                $locale = $arguments['locale'];
                                $this->actionCallLog[] = ['action' => 'fillTitle', 'locale' => $locale];
                            }),
                    ])
                    ->schema([
                        TextInput::make('title'),
                        Textarea::make('content'),
                    ]),
            ])
            ->statePath('data');
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}
