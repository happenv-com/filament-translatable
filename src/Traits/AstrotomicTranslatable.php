<?php

declare(strict_types=1);

namespace Happenv\FilamentTranslatable\Traits;

use Astrotomic\Translatable\Translatable as OriginalTranslatable;

/**
 * @deprecated Since 5.0 the Translations component loads Astrotomic translations itself.
 *             Use `Astrotomic\Translatable\Translatable` directly. Will be removed in 6.0.
 */
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
