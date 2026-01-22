<?php

declare(strict_types=1);

namespace Webard\FilamentTranslatable\Traits;

use Astrotomic\Translatable\Translatable as OriginalTranslatable;

// @phpstan-ignore trait.unused
trait AstrotomicTranslatable
{
    use OriginalTranslatable {
        attributesToArray as astrotomicAttributesToArray;
    }

    public function attributesToArray()
    {
        $attributes = $this->astrotomicAttributesToArray();

        $hiddenAttributes = $this->getHidden();

        $translations = $this->getTranslationsArray();

        foreach ($this->translatedAttributes as $field) {
            if (in_array($field, $hiddenAttributes)) {
                continue;
            }

            foreach ($translations as $locale => $fields) {
                $value = $fields[$field] ?? null;

                $attributes["{$field}:{$locale}"] = $value;
            }
        }

        return $attributes;
    }
}
