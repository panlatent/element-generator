<?php

namespace panlatent\craft\element\generator\services;

use craft\events\RegisterComponentTypesEvent;
use panlatent\craft\element\generator\base\Generator;
use panlatent\craft\element\generator\generators\EntryGenerator;
use panlatent\craft\element\generator\generators\UserGenerator;
use panlatent\craft\element\generator\Plugin;
use yii\base\Component;


class Generators extends Component
{
    public const EVENT_REGISTER_GENERATOR_TYPES = 'registerGeneratorTypes';

    private ?array $_generators = null;

    /**
     * @return string[]
     */
    public function getAllGeneratorTypes(): array
    {
        $types = [
            EntryGenerator::class,
            UserGenerator::class,
        ];

        $event = new RegisterComponentTypesEvent([
            'types' => $types,
        ]);

        $this->trigger(self::EVENT_REGISTER_GENERATOR_TYPES, $event);

        return $event->types;
    }

    public function getAllGenerators(): array
    {
        if ($this->_generators === null) {
            $this->_generators = Plugin::getInstance()->getSettings()->getGenerators();
        }
        return $this->_generators;
    }

    public function getGeneratorByName(string $name): ?Generator
    {
        return $this->getAllGenerators()[$name] ?? null;
    }
}