<?php

namespace Happenv\FilamentTranslatable\Tests\Fixtures\Panel;

use Filament\Panel;
use Filament\PanelProvider;
use Happenv\FilamentTranslatable\FilamentTranslatablePlugin;
use Happenv\FilamentTranslatable\Tests\Fixtures\Panel\Resources\AstrotomicPostResource;
use Happenv\FilamentTranslatable\Tests\Fixtures\Panel\Resources\SpatiePostResource;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->default()
            ->resources([
                SpatiePostResource::class,
                AstrotomicPostResource::class,
            ])
            ->plugin(
                FilamentTranslatablePlugin::make()
                    ->locales(['en', 'pl'])
                    ->defaultLocale('en'),
            );
    }
}
