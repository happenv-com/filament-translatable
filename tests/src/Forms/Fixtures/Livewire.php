<?php

namespace Happenv\FilamentTranslatable\Tests\Forms\Fixtures;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Support\MessageBag;
use Livewire\Component;

class Livewire extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $data;

    public static function make(): static
    {
        return new static;
    }

    #[\Override]
    public function getErrorBag()
    {
        return new MessageBag;
    }

    public function mount(): void
    {
        $this->resetErrorBag();
        $this->form->fill();

    }

    public function data($data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }
}
