<?php

namespace Webard\FilamentTranslatable\Tests\Forms\Fixtures;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Webard\FilamentTranslatable\Enums\TranslationMode;
use Webard\FilamentTranslatable\Forms\Component\Translations;
use Webard\FilamentTranslatable\Tests\Models\SpatiePost;

class SpatiePostFormComponent extends Livewire
{
    public ?SpatiePost $record = null;

    public function mount(?SpatiePost $record = null): void
    {
        $this->record = $record ?? new SpatiePost;
        $this->form->fill($this->record->toArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                TextInput::make('author'),
                Translations::make('translations')
                    ->locales(['en', 'fr', 'pl'])
                    ->defaultLocale('en')
                    ->translationMode(TranslationMode::Spatie)
                    ->displayFlagsInLocaleLabels(false)
                    ->displayNamesInLocaleLabels(true)
                    ->schema([
                        TextInput::make('title'),
                        Textarea::make('content'),
                    ]),
            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->record->fill($data);
        $this->record->save();
    }

    public function render(): View
    {
        return view('forms.fixtures.form');
    }
}
