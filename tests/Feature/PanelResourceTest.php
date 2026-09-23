<?php

use Filament\Facades\Filament;
use Happenv\FilamentTranslatable\Enums\TranslationMode;
use Happenv\FilamentTranslatable\Tests\Fixtures\Models\User;
use Happenv\FilamentTranslatable\Tests\Fixtures\Panel\Resources\AstrotomicPostResource\Pages\CreateAstrotomicPost;
use Happenv\FilamentTranslatable\Tests\Fixtures\Panel\Resources\AstrotomicPostResource\Pages\EditAstrotomicPost;
use Happenv\FilamentTranslatable\Tests\Fixtures\Panel\Resources\SpatiePostResource\Pages\CreateSpatiePost;
use Happenv\FilamentTranslatable\Tests\Fixtures\Panel\Resources\SpatiePostResource\Pages\EditSpatiePost;
use Happenv\FilamentTranslatable\Tests\Fixtures\Posts;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function (): void {
    Filament::setCurrentPanel('admin');

    actingAs(User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'secret']));
});

function pages(TranslationMode $mode): array
{
    return match ($mode) {
        TranslationMode::Spatie => [CreateSpatiePost::class, EditSpatiePost::class],
        TranslationMode::Astrotomic => [CreateAstrotomicPost::class, EditAstrotomicPost::class],
    };
}

it('creates a record through the resource create page', function (TranslationMode $mode): void {
    [$create] = pages($mode);

    livewire($create)
        ->fillForm(Posts::formData($mode, ['title' => ['en' => 'Hello', 'pl' => 'Cześć']], ['author' => 'Jane']))
        ->call('create')
        ->assertHasNoFormErrors();

    $record = Posts::modelClass($mode)::query()->latest('id')->firstOrFail();

    expect(Posts::translation($mode, $record, 'title', 'en'))->toBe('Hello')
        ->and(Posts::translation($mode, $record, 'title', 'pl'))->toBe('Cześć');
})->with('drivers');

it('edits a record through the resource edit page', function (TranslationMode $mode): void {
    [, $edit] = pages($mode);
    $record = Posts::create($mode, ['title' => ['en' => 'Hello', 'pl' => 'Cześć']]);

    livewire($edit, ['record' => $record->getRouteKey()])
        ->assertSchemaStateSet(Posts::formData($mode, ['title' => ['en' => 'Hello', 'pl' => 'Cześć']]))
        ->fillForm(Posts::formData($mode, ['title' => ['pl' => 'Witaj']]))
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Posts::translation($mode, $record, 'title', 'pl'))->toBe('Witaj')
        ->and(Posts::translation($mode, $record, 'title', 'en'))->toBe('Hello');
})->with('drivers');

it('uses the plugin locales on resource pages', function (TranslationMode $mode): void {
    [$create] = pages($mode);

    livewire($create)
        ->assertSchemaComponentExists(Posts::fieldName($mode, 'title', 'en'))
        ->assertSchemaComponentExists(Posts::fieldName($mode, 'title', 'pl'));
})->with('drivers');
