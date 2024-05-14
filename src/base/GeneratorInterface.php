<?php

namespace panlatent\craft\element\generator\base;

use craft\base\ElementInterface;

interface GeneratorInterface
{
    public static function elementType(): string;

    /**
     * Generator a element object.
     * @return ElementInterface
     */
    public function generate(): ElementInterface;
}