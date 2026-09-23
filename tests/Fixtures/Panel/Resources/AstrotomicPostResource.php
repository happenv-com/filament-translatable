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
use Happenv\FilamentTranslatable\Tests\Fixtures\Models\AstrotomicPost;
use Happenv\FilamentTranslatable\Tests\Fixtures\Panel\Resources\AstrotomicPostResource\Pages;

class AstrotomicPostResource extends Resource
{
    protected static ?string $model = AstrotomicPost::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('author')->required(),
            Translations::make('translations')
                ->translationMode(TranslationMode::Astrotomic)
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
            'index' => Pages\ListAstrotomicPosts::route('/'),
            'create' => Pages\CreateAstrotomicPost::route('/create'),
            'edit' => Pages\EditAstrotomicPost::route('/{record}/edit'),
        ];
    }
}
