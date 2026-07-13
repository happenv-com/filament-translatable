<?php

namespace Happenv\FilamentTranslatable\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * Spatie translatable model - stores translations in JSON columns
 */
class SpatiePost extends Model
{
    use HasTranslations;

    protected $table = 'spatie_posts';

    protected $fillable = ['author', 'title', 'content'];

    public array $translatable = ['title', 'content'];
}
