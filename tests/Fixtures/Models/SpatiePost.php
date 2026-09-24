<?php

declare(strict_types=1);

namespace Happenv\FilamentTranslatable\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class SpatiePost extends Model
{
    use HasTranslations;

    protected $table = 'spatie_posts';

    protected $guarded = [];

    /** @var array<string> */
    public array $translatable = ['title', 'content'];
}
