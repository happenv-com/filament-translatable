<?php

namespace Webard\FilamentTranslatable\Tests\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\Model;
use Webard\FilamentTranslatable\Traits\AstrotomicTranslatable;

/**
 * Astrotomic translatable model - stores translations in a separate table
 */
class AstrotomicPost extends Model implements TranslatableContract
{
    use AstrotomicTranslatable;

    protected $table = 'astrotomic_posts';

    protected $fillable = ['author'];

    public array $translatedAttributes = ['title', 'content'];
}
