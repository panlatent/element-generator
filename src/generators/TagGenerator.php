<?php

namespace panlatent\craft\element\generator\generators;

use craft\base\ElementInterface;
use craft\elements\Tag;
use craft\models\TagGroup;
use panlatent\craft\element\generator\base\Generator;
use yii\base\InvalidConfigException;

class TagGenerator extends Generator
{
    public string $group;

    public static function elementType(): string
    {
        return Tag::class;
    }

    public function beforeGenerate(ElementInterface $element): void
    {
        /** @var Tag $element */
        $element->groupId = $this->getGroup()->id;

        parent::beforeGenerate($element);
    }

    public function getGroup(): TagGroup
    {
        $group = \Craft::$app->getTags()->getTagGroupByHandle($this->group);
        if (!$group) {
            throw new InvalidConfigException();
        }
        return $group;
    }
}