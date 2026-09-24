<?php

declare(strict_types=1);

namespace Happenv\FilamentTranslatable\Tests\Fixtures\Panel\Resources\AstrotomicPostResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Happenv\FilamentTranslatable\Tests\Fixtures\Panel\Resources\AstrotomicPostResource;

class ListAstrotomicPosts extends ListRecords
{
    protected static string $resource = AstrotomicPostResource::class;
}
