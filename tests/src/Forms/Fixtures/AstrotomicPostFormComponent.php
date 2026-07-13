<?php

namespace  Happenv\FilamentTranslatable\Tests\Forms\Fixtures;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Happenv\FilamentTranslatable\Enums\TranslationMode;
use Happenv\FilamentTranslatable\Forms\Component\Translations;
use Happenv\FilamentTranslatable\Tests\Models\AstrotomicPost;

class AstrotomicPostFormComponent extends Livewire
{
    public ?AstrotomicPost $record = null;

    #[\Override]
    public function mount(?AstrotomicPost $record = null): void
    {
        $this->record = $record ?? new AstrotomicPost;
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
                    ->translationMode(TranslationMode::Astrotomic)
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
