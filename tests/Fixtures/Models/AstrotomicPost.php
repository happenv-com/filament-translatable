<?php

namespace Happenv\FilamentTranslatable\Tests\Fixtures\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class AstrotomicPost extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = 'astrotomic_posts';

    protected $fillable = ['author'];

    /** @var array<string> */
    public array $translatedAttributes = ['title', 'content'];

    public string $translationModel = AstrotomicPostTranslation::class;

    public string $translationForeignKey = 'astrotomic_post_id';
}
