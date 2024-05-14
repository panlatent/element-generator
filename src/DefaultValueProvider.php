<?php

namespace panlatent\craft\element\generator;

use Craft;
use craft\base\FieldInterface;
use craft\fields\BaseOptionsField;
use craft\fields\BaseRelationField;
use craft\fields\Date;
use craft\fields\Lightswitch;
use craft\fields\Matrix;
use craft\fields\Money;
use craft\fields\PlainText;
use panlatent\craft\element\generator\value\Context;
use yii\base\Component;
use yii\base\NotSupportedException;

class DefaultValueProvider extends Component
{
    public array $attributes = [];

    public array $fields = [];

    public function __construct(protected readonly Context $context, array $config = [])
    {
        $config['attributes'] = array_merge($this->defaultAttributes(), $config['attributes'] ?? []);
        $config['fields'] = array_merge($this->defaultFields(), $config['fields'] ?? []);
        parent::__construct($config);
    }

    public function getValues(array $fields): array
    {
        $values = [];
        foreach ($fields as $field) {
            $values[$field->handle] = $this->getValue($field);
        }
        return $values;
    }

    private function defaultAttributes(): array
    {
        return [
            'title' => $this->context->faker->text(...),
        ];
    }

    private function defaultFields(): array
    {
        return [
            BaseRelationField::class => fn($field) => $this->context->random->elements($field),
            Matrix::class => fn($field) => $this->context->random->matrix($field, $this),
            Lightswitch::class => fn() => $this->context->random->bool(),
            BaseOptionsField::class => fn($field) => $this->context->random->option($field),
            Date::class => fn() => $this->context->faker->date(),
            Money::class => fn($field) => $this->context->random->money($field),
            PlainText::class => function(PlainText $field) {
                if ($field->multiline) {
                    $value = $this->context->faker->text();
                } elseif (in_array($field->handle, ['phone', 'tel', 'mobile'])) {
                    $value = $this->context->faker->phoneNumber();
                } else {
                    $value = $this->context->faker->text($field->charLimit >= 5 ? $field->charLimit : 5);
                }
                return mb_substr($value, 0, $field->charLimit);
            },
            '\craft\ckeditor\Field' => fn() =>$this->context->faker->realText(),
        ];
    }

    public function getValue(FieldInterface $field): mixed
    {
        foreach ($this->fields as $class => $callback) {
            if (is_a($field, $class)) {
                return Craft::$container->invoke($callback, [$field]);
            }
        }

        throw new NotSupportedException($field::class . " is not supported in $field->handle");
    }
}