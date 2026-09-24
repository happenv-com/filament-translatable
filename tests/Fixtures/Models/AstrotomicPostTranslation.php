<?php

declare(strict_types=1);

namespace Happenv\FilamentTranslatable\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

class AstrotomicPostTranslation extends Model
{
    protected $table = 'astrotomic_post_translations';

    public $timestamps = false;

    protected $fillable = ['title', 'content'];
}
