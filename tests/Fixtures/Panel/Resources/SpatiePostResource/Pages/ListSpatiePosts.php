<?php

declare(strict_types=1);

namespace Happenv\FilamentTranslatable\Tests\Fixtures\Panel\Resources\SpatiePostResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Happenv\FilamentTranslatable\Tests\Fixtures\Panel\Resources\SpatiePostResource;

class ListSpatiePosts extends ListRecords
{
    protected static string $resource = SpatiePostResource::class;
}
