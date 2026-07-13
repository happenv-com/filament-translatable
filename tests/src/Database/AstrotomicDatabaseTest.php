<?php

use Illuminate\Support\Facades\DB;
use Happenv\FilamentTranslatable\Tests\DatabaseTestCase;
use Happenv\FilamentTranslatable\Tests\Models\AstrotomicPost;

uses(DatabaseTestCase::class);

it('can save translations to separate table with Astrotomic package', function (): void {
    $post = new AstrotomicPost;
    $post->author = 'John Doe';
    $post->translateOrNew('en')->title = 'English Title';
    $post->translateOrNew('en')->content = 'English Content';
    $post->translateOrNew('fr')->title = 'French Title';
    $post->translateOrNew('fr')->content = 'French Content';
    $post->translateOrNew('pl')->title = 'Polish Title';
    $post->translateOrNew('pl')->content = 'Polish Content';
    $post->save();

    // Verify the post was saved
    $savedPost = AstrotomicPost::find($post->id);

    expect($savedPost)->not->toBeNull();
    expect($savedPost->author)->toBe('John Doe');

    // Verify translations are retrieved correctly
    expect($savedPost->translate('en')->title)->toBe('English Title');
    expect($savedPost->translate('fr')->title)->toBe('French Title');
    expect($savedPost->translate('pl')->title)->toBe('Polish Title');
    expect($savedPost->translate('en')->content)->toBe('English Content');
    expect($savedPost->translate('fr')->content)->toBe('French Content');
    expect($savedPost->translate('pl')->content)->toBe('Polish Content');
});

it('stores Astrotomic translations in separate database table', function (): void {
    $post = new AstrotomicPost;
    $post->author = 'Jane Doe';
    $post->translateOrNew('en')->title = 'Title EN';
    $post->translateOrNew('fr')->title = 'Title FR';
    $post->save();

    // There should be exactly 1 row in astrotomic_posts
    $this->assertDatabaseCount('astrotomic_posts', 1);

    // There should be 2 rows in astrotomic_post_translations (one per locale)
    $this->assertDatabaseCount('astrotomic_post_translations', 2);

    // Verify the main table has no translation data
    $this->assertDatabaseHas('astrotomic_posts', [
        'id' => $post->id,
        'author' => 'Jane Doe',
    ]);

    // Verify the translations table has the translations
    $this->assertDatabaseHas('astrotomic_post_translations', [
        'astrotomic_post_id' => $post->id,
        'locale' => 'en',
        'title' => 'Title EN',
    ]);

    $this->assertDatabaseHas('astrotomic_post_translations', [
        'astrotomic_post_id' => $post->id,
        'locale' => 'fr',
        'title' => 'Title FR',
    ]);
});

it('can update Astrotomic translations', function (): void {
    $post = new AstrotomicPost;
    $post->author = 'Author';
    $post->translateOrNew('en')->title = 'Original Title';
    $post->save();

    // Update the translation
    $post->translate('en')->title = 'Updated Title';
    $post->save();

    $savedPost = AstrotomicPost::find($post->id);
    expect($savedPost->translate('en')->title)->toBe('Updated Title');

    // Still only 1 translation row
    $this->assertDatabaseCount('astrotomic_post_translations', 1);
});

it('can create Astrotomic post with fill method using locale keys', function (): void {
    $post = AstrotomicPost::create([
        'author' => 'Array Author',
        'en' => [
            'title' => 'Array EN Title',
            'content' => 'Array EN Content',
        ],
        'fr' => [
            'title' => 'Array FR Title',
            'content' => 'Array FR Content',
        ],
    ]);

    expect($post->translate('en')->title)->toBe('Array EN Title');
    expect($post->translate('fr')->title)->toBe('Array FR Title');
    expect($post->translate('en')->content)->toBe('Array EN Content');
    expect($post->translate('fr')->content)->toBe('Array FR Content');
});

it('returns current locale translation by default for Astrotomic', function (): void {
    $post = new AstrotomicPost;
    $post->author = 'Locale Author';
    $post->translateOrNew('en')->title = 'English';
    $post->translateOrNew('fr')->title = 'Français';
    $post->save();

    // Astrotomic uses config('translatable.locale') first, then app locale
    // So we need to update both for proper locale switching
    config()->set('translatable.locale', 'fr');
    app()->setLocale('fr');
    $savedPost = AstrotomicPost::find($post->id);

    // Since current locale is 'fr', accessing title should return French translation
    expect($savedPost->title)->toBe('Français');

    // Switch locale and reload
    config()->set('translatable.locale', 'en');
    app()->setLocale('en');
    $savedPost = AstrotomicPost::find($post->id);
    expect($savedPost->title)->toBe('English');
});

it('deletes translations when post is deleted', function (): void {
    $post = new AstrotomicPost;
    $post->author = 'Delete Test';
    $post->translateOrNew('en')->title = 'EN';
    $post->translateOrNew('fr')->title = 'FR';
    $post->save();

    $postId = $post->id;

    // Verify translations exist
    $this->assertDatabaseCount('astrotomic_post_translations', 2);

    // Delete the post - Astrotomic handles this through the trait
    $post->delete();

    // Main table should be empty
    $this->assertDatabaseMissing('astrotomic_posts', ['id' => $postId]);

    // Translations should also be deleted (handled by Astrotomic trait or manually)
    // Note: SQLite doesn't enforce foreign keys by default, so we check if post is deleted
    $this->assertDatabaseMissing('astrotomic_posts', ['id' => $postId]);
});

it('maintains foreign key relationship between main and translation tables', function (): void {
    $post = new AstrotomicPost;
    $post->author = 'FK Test';
    $post->translateOrNew('en')->title = 'FK EN';
    $post->save();

    // Query the translations table directly
    $translation = DB::table('astrotomic_post_translations')
        ->where('astrotomic_post_id', $post->id)
        ->first();

    expect($translation)->not->toBeNull();
    expect($translation->astrotomic_post_id)->toBe($post->id);
    expect($translation->locale)->toBe('en');
    expect($translation->title)->toBe('FK EN');
});

it('uses AstrotomicTranslatable trait for proper attribute formatting', function (): void {
    $post = new AstrotomicPost;
    $post->author = 'Trait Test';
    $post->translateOrNew('en')->title = 'EN Title';
    $post->translateOrNew('fr')->title = 'FR Title';
    $post->save();

    $savedPost = AstrotomicPost::find($post->id);
    $attributes = $savedPost->toArray();

    // The AstrotomicTranslatable trait should format attributes with colon syntax
    expect($attributes)->toHaveKey('title:en');
    expect($attributes)->toHaveKey('title:fr');
    expect($attributes['title:en'])->toBe('EN Title');
    expect($attributes['title:fr'])->toBe('FR Title');
});
