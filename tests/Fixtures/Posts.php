<?php

namespace Happenv\FilamentTranslatable\Tests\Fixtures;

use Happenv\FilamentTranslatable\Enums\TranslationMode;
use Happenv\FilamentTranslatable\Tests\Fixtures\Models\AstrotomicPost;
use Happenv\FilamentTranslatable\Tests\Fixtures\Models\SpatiePost;
use Illuminate\Database\Eloquent\Model;

final class Posts
{
    /**
     * @return class-string<SpatiePost|AstrotomicPost>
     */
    public static function modelClass(TranslationMode $mode): string
    {
        return match ($mode) {
            TranslationMode::Spatie => SpatiePost::class,
            TranslationMode::Astrotomic => AstrotomicPost::class,
        };
    }

    /**
     * @param  array<string, array<string, string>>  $translations  e.g. ['title' => ['en' => 'Hello']]
     */
    public static function create(TranslationMode $mode, array $translations, string $author = 'Jane'): Model
    {
        $class = self::modelClass($mode);
        $model = new $class;
        $model->author = $author;

        foreach ($translations as $attribute => $values) {
            foreach ($values as $locale => $value) {
                if ($mode === TranslationMode::Spatie) {
                    $model->setTranslation($attribute, $locale, $value);
                } else {
                    $model->translateOrNew($locale)->{$attribute} = $value;
                }
            }
        }

        $model->save();

        return $model->fresh();
    }

    public static function translation(TranslationMode $mode, Model $model, string $attribute, string $locale): ?string
    {
        $model = $model->fresh();

        if ($mode === TranslationMode::Spatie) {
            $value = $model->getTranslation($attribute, $locale, false);

            return $value === '' ? null : $value;
        }

        return $model->translate($locale, false)?->{$attribute};
    }

    public static function fieldName(TranslationMode $mode, string $attribute, string $locale): string
    {
        return match ($mode) {
            TranslationMode::Spatie => "{$attribute}.{$locale}",
            TranslationMode::Astrotomic => "{$attribute}:{$locale}",
        };
    }

    /**
     * Form state in the shape the given mode uses.
     *
     * @param  array<string, array<string, ?string>>  $translations
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    public static function formData(TranslationMode $mode, array $translations, array $extra = []): array
    {
        $data = $extra;

        foreach ($translations as $attribute => $values) {
            foreach ($values as $locale => $value) {
                if ($mode === TranslationMode::Spatie) {
                    $data[$attribute][$locale] = $value;
                } else {
                    $data["{$attribute}:{$locale}"] = $value;
                }
            }
        }

        return $data;
    }
}
