<?php

namespace panlatent\craft\element\generator\generators;

use craft\base\ElementInterface;
use craft\elements\Entry;
use craft\helpers\ArrayHelper;
use craft\models\EntryType;
use craft\models\Section;
use panlatent\craft\element\generator\base\Generator;
use panlatent\craft\element\generator\ValueBag;
use yii\base\InvalidConfigException;

class EntryGenerator extends Generator
{
    public ?string $section = null;

    public ?string $type = null;

    public static function elementType(): string
    {
        return Entry::class;
    }

    protected function makeAttributes(ElementInterface $element, ValueBag $values): array
    {
        $attributes = parent::makeAttributes($element, $values);
        $attributes['sectionId'] = $this->getSection()->id;
        $attributes['typeId'] = $this->getType()->id;

        if ($values->has('author')) {
            $attributes['authorId'] = $values->get('author');
            // (new ValueExpression($fieldValues['author']))->getValue($this->getValueContext());;
            //unset($fieldValues['author']);
        } else {
            $attributes['authorId'] = 1;
        }

        return $attributes;
    }

    private function getSection(): Section
    {
        $section = \Craft::$app->getSections()->getSectionByHandle($this->section);
        if (!$section) {
            throw new InvalidConfigException('Not found section: ' . $this->section);
        }
        return $section;
    }

    private function getType(): EntryType
    {
        $types = $this->getSection()->getEntryTypes();
        if ($this->type === null && count($types) === 1) {
            return reset($types);
        } else {
            return ArrayHelper::firstWhere($this->getSection()->getEntryTypes(), 'handle', $this->type);
        }
    }
}