<?php

declare(strict_types=1);

namespace Happenv\FilamentTranslatable\Tests\Fixtures\Panel\Resources\SpatiePostResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Happenv\FilamentTranslatable\Tests\Fixtures\Panel\Resources\SpatiePostResource;

class EditSpatiePost extends EditRecord
{
    protected static string $resource = SpatiePostResource::class;
}
