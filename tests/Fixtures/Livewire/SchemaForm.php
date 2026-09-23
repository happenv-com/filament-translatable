<?php

namespace Happenv\FilamentTranslatable\Tests\Fixtures\Livewire;

use Closure;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;

/**
 * Generic form whose components are supplied by the test.
 */
class SchemaForm extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    /** @var (Closure(): array<mixed>)|null */
    public static ?Closure $componentsUsing = null;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    /** @var array<int, array{action: string, locale: ?string}> */
    public array $actionCalls = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(static::$componentsUsing ? (static::$componentsUsing)() : [])
            ->statePath('data');
    }

    public function validateForm(): void
    {
        $this->form->validate();
    }

    public function render(): string
    {
        return '<div>{{ $this->form }}</div>';
    }
}
