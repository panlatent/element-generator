<?php

namespace panlatent\craft\element\generator;

use craft\base\FieldInterface;
use craft\fields\Matrix;
use craft\helpers\ArrayHelper;

class FieldValueExpression
{
    public function __construct(public readonly FieldInterface $field, public readonly mixed $value)
    {

    }

    public function getValue()
    {
        if ($this->value instanceof \Closure) {
            return call_user_func($this->value, $this->field);
        }

        if ($this->field instanceof Matrix && is_array($this->value)) {
            if (ArrayHelper::isIndexed($this->value)) {
                $blocks = [];
                $sortOrder = [];
                foreach ($this->value as $i => $value) {
                    $key = 'new:'.$i;
                    $blocks[$key] = $value;
                    $sortOrder[] = $key;
                }
                return [
                    'sortOrder' => $sortOrder,
                    'blocks' => $blocks,
                ];
            }
        }

        return $this->value;
    }
}