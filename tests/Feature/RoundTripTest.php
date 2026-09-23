<?php

use Astrotomic\Translatable\Translatable;
use Happenv\FilamentTranslatable\Enums\TranslationMode;
use Happenv\FilamentTranslatable\Tests\Fixtures\Livewire\PostForm;
use Happenv\FilamentTranslatable\Tests\Fixtures\Models\AstrotomicPost;
use Happenv\FilamentTranslatable\Tests\Fixtures\Posts;
use Happenv\FilamentTranslatable\Traits\AstrotomicTranslatable;

use function Pest\Livewire\livewire;

it('creates a record with translations for every locale', function (TranslationMode $mode): void {
    $component = livewire(PostForm::class, ['mode' => $mode->value])
        ->fillForm(Posts::formData($mode, [
            'title' => ['en' => 'Hello', 'pl' => 'Cześć'],
            'content' => ['en' => 'Body', 'pl' => 'Treść'],
        ], ['author' => 'Jane']))
        ->call('save')
        ->assertHasNoFormErrors();

    $record = Posts::modelClass($mode)::findOrFail($component->get('recordId'));

    expect(Posts::translation($mode, $record, 'title', 'en'))->toBe('Hello')
        ->and(Posts::translation($mode, $record, 'title', 'pl'))->toBe('Cześć')
        ->and(Posts::translation($mode, $record, 'content', 'pl'))->toBe('Treść')
        ->and($record->author)->toBe('Jane');
})->with('drivers');

it('loads every locale of an existing record into the form', function (TranslationMode $mode): void {
    $record = Posts::create($mode, ['title' => ['en' => 'Hello', 'pl' => 'Cześć']]);

    livewire(PostForm::class, ['mode' => $mode->value, 'recordId' => $record->getKey()])
        ->assertSchemaStateSet(Posts::formData($mode, ['title' => ['en' => 'Hello', 'pl' => 'Cześć']]));
})->with('drivers');

it('updates translations of an existing record', function (TranslationMode $mode): void {
    $record = Posts::create($mode, ['title' => ['en' => 'Hello', 'pl' => 'Cześć']]);

    livewire(PostForm::class, ['mode' => $mode->value, 'recordId' => $record->getKey()])
        ->fillForm(Posts::formData($mode, ['title' => ['pl' => 'Witaj']]))
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Posts::translation($mode, $record, 'title', 'pl'))->toBe('Witaj')
        ->and(Posts::translation($mode, $record, 'title', 'en'))->toBe('Hello');
})->with('drivers');

it('leaves missing translations empty instead of using the fallback locale', function (TranslationMode $mode): void {
    $record = Posts::create($mode, ['title' => ['en' => 'Hello']]);

    livewire(PostForm::class, ['mode' => $mode->value, 'recordId' => $record->getKey()])
        ->assertSchemaStateSet(Posts::formData($mode, ['title' => ['en' => 'Hello', 'pl' => null]]));
})->with('drivers');

it('does not overwrite state passed explicitly to fill()', function (TranslationMode $mode): void {
    $record = Posts::create($mode, ['title' => ['en' => 'Hello', 'pl' => 'Cześć']]);

    livewire(PostForm::class, [
        'mode' => $mode->value,
        'recordId' => $record->getKey(),
        'overrides' => Posts::formData($mode, ['title' => ['pl' => 'Override']]),
    ])->assertSchemaStateSet(Posts::formData($mode, ['title' => ['en' => 'Hello', 'pl' => 'Override']]));
})->with('drivers');

it('validates the default locale as required', function (TranslationMode $mode): void {
    livewire(PostForm::class, ['mode' => $mode->value])
        ->fillForm(Posts::formData($mode, ['title' => ['en' => null, 'pl' => 'Cześć']]))
        ->call('save')
        ->assertHasFormErrors([Posts::fieldName($mode, 'title', 'en') => 'required']);
})->with('drivers');

it('works with the original Astrotomic trait and keeps serialization untouched', function (): void {
    $uses = class_uses_recursive(AstrotomicPost::class);

    expect($uses)->toContain(Translatable::class)
        ->not->toContain(AstrotomicTranslatable::class);

    $record = Posts::create(TranslationMode::Astrotomic, ['title' => ['en' => 'Hello', 'pl' => 'Cześć']]);

    expect(array_keys($record->toArray()))->not->toContain('title:en', 'title:pl');
});
