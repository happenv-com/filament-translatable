<?php

namespace Happenv\FilamentTranslatable\Tests\Fixtures\Panel\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Happenv\FilamentTranslatable\Enums\TranslationMode;
use Happenv\FilamentTranslatable\Forms\Component\Translations;
use Happenv\FilamentTranslatable\Tests\Fixtures\Models\SpatiePost;
use Happenv\FilamentTranslatable\Tests\Fixtures\Panel\Resources\SpatiePostResource\Pages;

class SpatiePostResource extends Resource
{
    protected static ?string $model = SpatiePost::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('author')->required(),
            Translations::make('translations')
                ->translationMode(TranslationMode::Spatie)
                ->schema([
                    TextInput::make('title')->requiredDefaultLocale(),
                    Textarea::make('content'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('author')]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpatiePosts::route('/'),
            'create' => Pages\CreateSpatiePost::route('/create'),
            'edit' => Pages\EditSpatiePost::route('/{record}/edit'),
        ];
    }
}
