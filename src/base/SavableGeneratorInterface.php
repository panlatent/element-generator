<?php

namespace panlatent\craft\element\generator\base;

use craft\base\ElementInterface;

interface SavableGeneratorInterface
{
    public function save(ElementInterface $element): bool;
}