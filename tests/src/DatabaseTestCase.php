<?php

namespace  Happenv\FilamentTranslatable\Tests;

use Astrotomic\Translatable\TranslatableServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DatabaseTestCase extends TestCase
{
    use RefreshDatabase;

    #[\Override]
    protected function getPackageProviders($app): array
    {
        return [
            ...parent::getPackageProviders($app),
            TranslatableServiceProvider::class, // Astrotomic
        ];
    }

    #[\Override]
    public function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        // Configure database for testing
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Configure Astrotomic translatable
        config()->set('translatable.locales', ['en', 'fr', 'pl']);
        config()->set('translatable.locale', 'en');
        config()->set('translatable.fallback_locale', 'en');
        config()->set('translatable.use_fallback', true);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
