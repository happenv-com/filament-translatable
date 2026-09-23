<?php

namespace Happenv\FilamentTranslatable\Tests\Fixtures\Livewire;

use Closure;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Happenv\FilamentTranslatable\Enums\TranslationMode;
use Happenv\FilamentTranslatable\Forms\Component\Translations;
use Happenv\FilamentTranslatable\Tests\Fixtures\Posts;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

/**
 * Mirrors Filament's Create/EditRecord: fills from attributesToArray(), saves with fill()->save().
 */
class PostForm extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    /** @var (Closure(Translations): mixed)|null */
    public static ?Closure $configureTranslationsUsing = null;

    /** @var (Closure(Translations): array<mixed>)|null  replaces the default [author, translations] components */
    public static ?Closure $componentsUsing = null;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public string $mode = 'spatie';

    public ?int $recordId = null;

    /**
     * @param  array<string, mixed>  $overrides  merged into the fill data (like mutateFormDataBeforeFill)
     */
    public function mount(string $mode = 'spatie', ?int $recordId = null, array $overrides = []): void
    {
        $this->mode = $mode;
        $this->recordId = $recordId;

        $this->form->fill(array_replace_recursive($this->getRecord()?->attributesToArray() ?? [], $overrides));
    }

    public function getMode(): TranslationMode
    {
        return TranslationMode::from($this->mode);
    }

    public function getRecord(): ?Model
    {
        if ($this->recordId === null) {
            return null;
        }

        return Posts::modelClass($this->getMode())::find($this->recordId);
    }

    public function form(Schema $schema): Schema
    {
        $translations = Translations::make('translations')
            ->locales(['en', 'pl'])
            ->defaultLocale('en')
            ->translationMode($this->getMode())
            ->schema([
                TextInput::make('title')->requiredDefaultLocale(),
                Textarea::make('content'),
            ]);

        if (static::$configureTranslationsUsing) {
            (static::$configureTranslationsUsing)($translations);
        }

        return $schema
            ->components(static::$componentsUsing ? (static::$componentsUsing)($translations) : [
                TextInput::make('author'),
                $translations,
            ])
            ->statePath('data')
            ->model($this->getRecord() ?? Posts::modelClass($this->getMode()));
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $class = Posts::modelClass($this->getMode());
        $record = $this->getRecord() ?? new $class;
        $record->fill($data)->save();

        $this->recordId = $record->getKey();
    }

    public function render(): string
    {
        return '<div>{{ $this->form }}</div>';
    }
}
