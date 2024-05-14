<?php

namespace panlatent\craft\element\generator;

class ValueExpression
{
    public function __construct(public readonly mixed $expression)
    {

    }

    public static function parse(mixed $expression): mixed
    {
        return (new static($expression))->getValue();
    }

    public function __invoke(): mixed
    {
        return $this->getValue();
    }

    public function getValue(): mixed
    {
        if ($this->expression instanceof \Closure) {
            return \Craft::$container->invoke($this->expression, []);
        } elseif ($this->expression instanceof \Stringable) {
            return (string)$this->expression;
        }
        return $this->expression;
    }
}