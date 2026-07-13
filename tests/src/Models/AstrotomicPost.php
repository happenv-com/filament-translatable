<?php

namespace Happenv\FilamentTranslatable\Tests\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Happenv\FilamentTranslatable\Traits\AstrotomicTranslatable;
use Illuminate\Database\Eloquent\Model;

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
