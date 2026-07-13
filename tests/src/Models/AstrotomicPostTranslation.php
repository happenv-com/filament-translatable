<?php

namespace Happenv\FilamentTranslatable\Tests\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Translation model for AstrotomicPost
 */
class AstrotomicPostTranslation extends Model
{
    protected $table = 'astrotomic_post_translations';

    public $timestamps = false;

    protected $fillable = ['title', 'content'];
}
