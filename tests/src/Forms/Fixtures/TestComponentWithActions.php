<?php

namespace Happenv\FilamentTranslatable\Tests\Forms\Fixtures;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Happenv\FilamentTranslatable\Enums\TranslationMode;
use Happenv\FilamentTranslatable\Forms\Component\Translations;
use Illuminate\Contracts\View\View;

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
                            ->action(function (array $arguments): void {
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
