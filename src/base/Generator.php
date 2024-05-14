<?php

namespace panlatent\craft\element\generator\base;

use Craft;
use craft\base\ElementInterface;
use craft\base\FieldInterface;
use craft\events\ElementEvent;
use craft\fields\BaseRelationField;
use panlatent\craft\element\generator\DefaultValueProvider;
use panlatent\craft\element\generator\value\Context;
use panlatent\craft\element\generator\ValueBag;
use yii\base\Component;

abstract class Generator extends Component implements GeneratorInterface
{
    use ElementAttributes;

    /**
     * @event ElementEvent
     */
    public const EVENT_BEFORE_GENERATE = 'beforeGenerate';

    /**
     * @event ElementEvent
     */
    public const EVENT_AFTER_GENERATE = 'afterGenerate';

    public const MODE_MINIMUM = 'minimum';
    public const MODE_FULL = 'full';

    public string $elementType;

    public string $mode = self::MODE_MINIMUM;

    public array $select = [];

    public \Closure $values;

    protected DefaultValueProvider $defaultValues;

    protected Context $context;

    public function __construct($config = [])
    {
        $contextConfig = ['class' => Context::class];
        if (isset($config['context'])) {
            $contextConfig = array_merge($contextConfig, $config['context']);
            unset($config['context']);
        }
        $this->context = Craft::createObject($contextConfig, [$this]);

        $defaultValuesConfig = ['class' => DefaultValueProvider::class];
        if (isset($config['defaultValues'])) {
            $defaultValuesConfig = array_merge($defaultValuesConfig, $config['defaultValues']);
            unset($config['defaultValues']);
        }
        $this->defaultValues = Craft::createObject($defaultValuesConfig, [$this->context]);

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function generate(): ElementInterface
    {
        $element = $this->createElement();

        $this->beforeGenerate($element);

        $values = $this->makeValues();

        $attributeValues = $this->makeAttributes($element, $values);
        $element->setAttributes($attributeValues, false);

        $fieldValues = $this->makeFieldValues($element, $values);
        $element->setFieldValues($fieldValues);

        $element->setScenario($this->scenario);

        $this->afterGenerate($element);

        return $element;
    }

    protected function beforeGenerate(ElementInterface $element): void
    {
        if ($this->hasEventHandlers(self::EVENT_BEFORE_GENERATE)) {
            $this->trigger(self::EVENT_BEFORE_GENERATE, new ElementEvent([
                'element' => $element
            ]));
        }
    }

    protected function afterGenerate(ElementInterface $element): void
    {
        if ($this->hasEventHandlers(self::EVENT_AFTER_GENERATE)) {
            $this->trigger(self::EVENT_AFTER_GENERATE, new ElementEvent([
                'element' => $element
            ]));
        }
    }

    protected function createElement(): ElementInterface
    {
        return new $this->elementType();
    }

    public function getValueAttributes(): array
    {
        // @todo
        $arr = call_user_func($this->values, $this->context);
        return array_keys($arr);
    }

    protected function makeValues(): ValueBag
    {
        $arr = call_user_func($this->values, $this->context);
        return new ValueBag($arr);
    }

    protected function makeFieldValues(ElementInterface $element, ValueBag $values): array
    {
        $fieldValues = [];
        $customFields = $element->getFieldLayout()->getCustomFieldElements();
        foreach ($customFields as $key => $customField) {
            $field = $customField->getField();
            if ($values->has($field->handle)) {
                $fieldValues[$field->handle] = $values->fieldValue($field);
                unset($customFields[$key]);
            }
        }

        if ($this->mode == self::MODE_MINIMUM) {
            $customFields = array_filter($customFields, fn($field) => $field->required);
        }

        $customFields = array_map(function($field) {
            $originField = $field->getField();
            if ($originField instanceof BaseRelationField && $field->required && !$originField->minRelations) {
                $originField->minRelations = 1;
            }
            return $originField;
        }  , $customFields);

        return array_merge($fieldValues, $this->makeFieldDefaultValues($customFields));
    }

    /**
     * @param FieldInterface[] $fields
     * @return array
     */
    protected function makeFieldDefaultValues(array $fields): array
    {
        return $this->defaultValues->getValues($fields);
    }
}