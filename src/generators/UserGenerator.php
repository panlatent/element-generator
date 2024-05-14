<?php

namespace panlatent\craft\element\generator\generators;

use Craft;
use craft\base\ElementInterface;
use craft\elements\User;
use craft\helpers\ArrayHelper;
use panlatent\craft\element\generator\base\Generator;
use panlatent\craft\element\generator\base\SavableGeneratorInterface;
use panlatent\craft\element\generator\base\SavableGeneratorTrait;
use panlatent\craft\element\generator\ValueBag;
use yii\base\InvalidConfigException;

class UserGenerator extends Generator implements SavableGeneratorInterface
{
    use SavableGeneratorTrait;

    public array $groups = [];

    public static function elementType(): string
    {
        return User::class;
    }

    protected function makeAttributes(ElementInterface $element, ValueBag $values): array
    {
        /** @var User $element */
        $attributes = parent::makeAttributes($element, $values);

        $attributes['username'] = $values->get('username', fn() => $this->context->faker->userName());
        $attributes['email'] = $values->get('email', fn() => $attributes['username'] . '@'. $this->context->faker->freeEmailDomain());
        $attributes['fullName'] = $values->get('fullName', fn() => $this->context->faker->name());
        $attributes['newPassword'] = $values->get('password');

        if ($values->has('photo')) {
            $photo = $values->getAsset('photo');
            $element->setPhoto($photo);
        }

        $groups = $this->getGroups();
        if (!empty($groups)) {
            $element->setGroups($groups);
        }

        return $attributes;
    }

    protected function setStatusAttribute(string $status, array &$attributes): void
    {
        if ($status === User::STATUS_DISABLED || $status === User::STATUS_ARCHIVED) {
            parent::setStatusAttribute($status, $attributes);
            return;
        }

        $attributes['enabled'] = true;
        switch ($status) {
            case User::STATUS_SUSPENDED:
                $attributes['suspended'] = true;
                break;
            case User::STATUS_ARCHIVED:
                $attributes['archived'] = true;
                break;
            case User::STATUS_PENDING:
                $attributes['pending'] = true;
                break;
            case User::STATUS_ACTIVE:
                $attributes['active'] = true;
                break;
            case User::STATUS_INACTIVE:
                break;
        }
    }

    protected function afterSave(ElementInterface $element): bool
    {
        /** @var User $element */
        if (!Craft::$app->getUsers()->assignUserToGroups($element->id, ArrayHelper::getColumn($element->getGroups(), 'id'))) {
            return false;
        }

        $photo = $element->getPhoto();
        if ($photo !== null) {
            if (!$photo->id) {
                Craft::$app->getUsers()->saveUserPhoto($photo->tempFilePath, $element);
            }
        }

        return true;
    }

    private function getGroups(): array
    {
        $groups = [];
        foreach ($this->groups as $handle) {
            $group = Craft::$app->getUserGroups()->getGroupByHandle($handle);
            if (!$group) {
                throw new InvalidConfigException('Not found user group: ' . $handle);
            }
            $groups[]  = $group;
        }
        return $groups;
    }
}