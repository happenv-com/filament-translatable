<?php

namespace Happenv\FilamentTranslatable\Tests\Fixtures\Panel;

use Filament\Panel;
use Filament\PanelProvider;

class BarePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('bare')
            ->path('bare');
    }
}
