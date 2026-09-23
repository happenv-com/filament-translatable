<?php

use Filament\Actions\Action;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Happenv\FilamentTranslatable\Enums\TranslationMode;
use Happenv\FilamentTranslatable\Forms\Component\Translations;
use Happenv\FilamentTranslatable\Forms\Component\Translations\Tab;
use Happenv\FilamentTranslatable\Tests\Fixtures\Livewire\PostForm;
use Happenv\FilamentTranslatable\Tests\Fixtures\Livewire\SchemaForm;
use Happenv\FilamentTranslatable\Tests\Fixtures\Posts;

use function Pest\Livewire\livewire;

it('shows tab panels under the same key the tab buttons select', function (): void {
    SchemaForm::$componentsUsing = fn (): array => [
        Translations::make('translations')
            ->locales(['en', 'pl'])
            ->actions([Action::make('fill')->action(fn () => null)])
            ->schema([TextInput::make('title')]),
    ];

    $component = livewire(SchemaForm::class);

    $keys = [];
    $component->assertSchemaComponentExists('translations::data::tabs', checkComponentUsing: function (Translations $t) use (&$keys): bool {
        foreach ($t->getChildSchemas() as $schema) {
            $tab = $schema->getComponents()[0];
            expect($tab)->toBeInstanceOf(Tab::class);
            $keys[] = $tab->getKey(isAbsolute: false);
        }

        return true;
    });

    expect($keys)->toHaveCount(2);

    foreach ($keys as $key) {
        // panel and per-tab action bar visibility must compare against the key the buttons set
        $component->assertSeeHtml("tab !== '{$key}'");
    }

    $component->assertDontSeeHtml("tab !== 'form.");
});

it('loads casted field types from the record', function (TranslationMode $mode): void {
    $record = Posts::create($mode, ['title' => ['en' => '1'], 'content' => ['en' => 'a,b']]);

    PostForm::$configureTranslationsUsing = fn (Translations $t) => $t->schema([
        Toggle::make('title'),
        TagsInput::make('content')->separator(','),
    ]);

    $data = livewire(PostForm::class, ['mode' => $mode->value, 'recordId' => $record->getKey()])->get('data');

    expect(data_get($data, Posts::fieldName($mode, 'title', 'en')))->toBeTrue()
        ->and(data_get($data, Posts::fieldName($mode, 'content', 'en')))->toBe(['a', 'b']);
})->with('drivers');

it('does not load translations of the page record into JSON repeater items', function (TranslationMode $mode): void {
    $record = Posts::create($mode, ['title' => ['en' => 'Parent EN', 'pl' => 'Parent PL']]);

    PostForm::$componentsUsing = fn (): array => [
        Repeater::make('items')->schema([
            Translations::make('item')
                ->locales(['en', 'pl'])
                ->translationMode($mode)
                ->schema([TextInput::make('title')]),
        ]),
    ];

    $component = livewire(PostForm::class, [
        'mode' => $mode->value,
        'recordId' => $record->getKey(),
        'overrides' => ['items' => [Posts::formData($mode, ['title' => ['en' => 'Item EN']])]],
    ]);

    $item = array_values($component->get('data.items'))[0];

    expect(data_get($item, Posts::fieldName($mode, 'title', 'en')))->toBe('Item EN')
        ->and(data_get($item, Posts::fieldName($mode, 'title', 'pl')))->toBeNull();
})->with('drivers');

it('gives tabs of two translations components distinct keys and actions', function (): void {
    SchemaForm::$componentsUsing = fn (): array => [
        Translations::make('main')->locales(['en', 'pl'])
            ->actions([Action::make('act')->action(fn (SchemaForm $livewire) => $livewire->actionCalls[] = ['action' => 'main', 'locale' => null])])
            ->schema([TextInput::make('title')]),
        Translations::make('seo')->locales(['en', 'pl'])
            ->actions([Action::make('act')->action(fn (SchemaForm $livewire) => $livewire->actionCalls[] = ['action' => 'seo', 'locale' => null])])
            ->schema([TextInput::make('meta')]),
    ];

    $component = livewire(SchemaForm::class);

    $tabKeys = collect($component->instance()->form->getFlatComponents(withAbsoluteKeys: true))
        ->filter(fn ($c): bool => $c instanceof Tab)
        ->keys()
        ->all();

    expect($tabKeys)->toHaveCount(4)
        ->and($tabKeys)->toContain('form.main::pl::data::tab', 'form.seo::pl::data::tab');

    $component->callAction(TestAction::make('act')->schemaComponent('seo::pl::data::tab', schema: 'form'));

    expect($component->get('actionCalls'))->toBe([['action' => 'seo', 'locale' => null]]);
});
