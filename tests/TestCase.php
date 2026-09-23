<?php

namespace Happenv\FilamentTranslatable\Tests;

use Astrotomic\Translatable\TranslatableServiceProvider;
use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Happenv\FilamentTranslatable\FilamentTranslatableServiceProvider;
use Happenv\FilamentTranslatable\Tests\Fixtures\Livewire\PostForm;
use Happenv\FilamentTranslatable\Tests\Fixtures\Livewire\SchemaForm;
use Happenv\FilamentTranslatable\Tests\Fixtures\Panel\AdminPanelProvider;
use Happenv\FilamentTranslatable\Tests\Fixtures\Panel\BarePanelProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            ActionsServiceProvider::class,
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            SchemasServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            TranslatableServiceProvider::class,
            FilamentTranslatableServiceProvider::class,
            AdminPanelProvider::class,
            BarePanelProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Laravel only logs deprecations; make them fail the test instead.
        $this->withoutDeprecationHandling();
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('app.fallback_locale', 'en');
        $app['config']->set('translatable.locales', ['en', 'pl', 'fr', 'de', 'pt-BR']);
        $app['config']->set('translatable.locale', 'en');
        $app['config']->set('translatable.fallback_locale', 'en');
        $app['config']->set('translatable.use_fallback', true);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Fixtures/database/migrations');
    }

    protected function tearDown(): void
    {
        SchemaForm::$componentsUsing = null;
        PostForm::$configureTranslationsUsing = null;

        parent::tearDown();
    }
}
