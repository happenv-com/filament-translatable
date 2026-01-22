<?php

use Webard\FilamentTranslatable\Tests\DatabaseTestCase;
use Webard\FilamentTranslatable\Tests\Models\SpatiePost;

uses(DatabaseTestCase::class);

it('can save translations to JSON column with Spatie package', function (): void {
    $post = new SpatiePost;
    $post->author = 'John Doe';
    $post->setTranslation('title', 'en', 'English Title');
    $post->setTranslation('title', 'fr', 'French Title');
    $post->setTranslation('title', 'pl', 'Polish Title');
    $post->setTranslation('content', 'en', 'English Content');
    $post->setTranslation('content', 'fr', 'French Content');
    $post->setTranslation('content', 'pl', 'Polish Content');
    $post->save();

    // Verify the post was saved
    $savedPost = SpatiePost::find($post->id);

    expect($savedPost)->not->toBeNull();
    expect($savedPost->author)->toBe('John Doe');

    // Verify translations are retrieved correctly
    expect($savedPost->getTranslation('title', 'en'))->toBe('English Title');
    expect($savedPost->getTranslation('title', 'fr'))->toBe('French Title');
    expect($savedPost->getTranslation('title', 'pl'))->toBe('Polish Title');
    expect($savedPost->getTranslation('content', 'en'))->toBe('English Content');
    expect($savedPost->getTranslation('content', 'fr'))->toBe('French Content');
    expect($savedPost->getTranslation('content', 'pl'))->toBe('Polish Content');

    // Verify translations are stored in the same table as JSON
    $rawData = $savedPost->getRawOriginal('title');
    $titleTranslations = json_decode((string) $rawData, true);

    expect($titleTranslations)->toBeArray();
    expect($titleTranslations)->toHaveKey('en');
    expect($titleTranslations)->toHaveKey('fr');
    expect($titleTranslations)->toHaveKey('pl');
});

it('stores Spatie translations in single database table', function (): void {
    $post = new SpatiePost;
    $post->author = 'Jane Doe';
    $post->setTranslation('title', 'en', 'Title EN');
    $post->setTranslation('title', 'fr', 'Title FR');
    $post->save();

    // There should be exactly 1 row in spatie_posts
    $this->assertDatabaseCount('spatie_posts', 1);

    // Verify the data structure
    $this->assertDatabaseHas('spatie_posts', [
        'id' => $post->id,
        'author' => 'Jane Doe',
    ]);
});

it('can update Spatie translations', function (): void {
    $post = new SpatiePost;
    $post->author = 'Author';
    $post->setTranslation('title', 'en', 'Original Title');
    $post->save();

    // Update the translation
    $post->setTranslation('title', 'en', 'Updated Title');
    $post->save();

    $savedPost = SpatiePost::find($post->id);
    expect($savedPost->getTranslation('title', 'en'))->toBe('Updated Title');
});

it('can create Spatie post with array of translations', function (): void {
    $post = SpatiePost::create([
        'author' => 'Array Author',
        'title' => [
            'en' => 'Array EN Title',
            'fr' => 'Array FR Title',
        ],
        'content' => [
            'en' => 'Array EN Content',
            'fr' => 'Array FR Content',
        ],
    ]);

    expect($post->getTranslation('title', 'en'))->toBe('Array EN Title');
    expect($post->getTranslation('title', 'fr'))->toBe('Array FR Title');
    expect($post->getTranslation('content', 'en'))->toBe('Array EN Content');
    expect($post->getTranslation('content', 'fr'))->toBe('Array FR Content');
});

it('returns current locale translation by default for Spatie', function (): void {
    app()->setLocale('fr');

    $post = new SpatiePost;
    $post->author = 'Locale Author';
    $post->setTranslation('title', 'en', 'English');
    $post->setTranslation('title', 'fr', 'Français');
    $post->save();

    $savedPost = SpatiePost::find($post->id);

    // Since current locale is 'fr', accessing title should return French translation
    expect($savedPost->title)->toBe('Français');

    // Switch locale
    app()->setLocale('en');
    expect($savedPost->title)->toBe('English');
});
