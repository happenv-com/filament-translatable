<?php

use Happenv\FilamentTranslatable\Drivers\AstrotomicDriver;
use Happenv\FilamentTranslatable\Drivers\SpatieDriver;
use Happenv\FilamentTranslatable\Drivers\TranslationDriver;
use Happenv\FilamentTranslatable\Enums\TranslationMode;
use Happenv\FilamentTranslatable\Tests\Fixtures\Posts;

it('maps modes to drivers', function (): void {
    expect(TranslationMode::Spatie->driver())->toBeInstanceOf(SpatieDriver::class)
        ->and(TranslationMode::Astrotomic->driver())->toBeInstanceOf(AstrotomicDriver::class);
});

it('names fields per driver', function (): void {
    expect((new SpatieDriver)->getFieldName('title', 'pt-BR'))->toBe('title.pt-BR')
        ->and((new AstrotomicDriver)->getFieldName('title', 'pt-BR'))->toBe('title:pt-BR');
});

it('reads translations from a record without falling back', function (TranslationMode $mode): void {
    $record = Posts::create($mode, ['title' => ['en' => 'Hello']]);

    $driver = $mode->driver();

    expect($driver)->toBeInstanceOf(TranslationDriver::class)
        ->and($driver->getTranslationFromRecord($record, 'title', 'en'))->toBe('Hello')
        ->and($driver->getTranslationFromRecord($record, 'title', 'pl'))->toBeNull()
        ->and($driver->getTranslationFromRecord($record, 'content', 'en'))->toBeNull();
})->with('drivers');
