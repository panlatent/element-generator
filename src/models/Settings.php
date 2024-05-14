<?php

namespace panlatent\craft\element\generator\models;

use Craft;
use craft\base\Model;
use panlatent\craft\element\generator\base\GeneratorInterface;
use panlatent\craft\element\generator\Plugin;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;

/**
 * Element Generator Settings
 * @property-read GeneratorInterface $generators
 */
class Settings extends Model
{
    public array $default = [];

    public array $generators = [];

    public function getGenerators(): array
    {
        $generators = [];
        foreach ($this->generators as $name => $config) {
            if ($config instanceof \Closure) {
                $config = Craft::$container->invoke($config);
            }

            if (!is_array($config)) {
                throw new InvalidConfigException("Generator {$name} config must be an array or a closure that returns an array");
            }

            $config = array_merge($this->default, $config);

            if (!isset($config['class'])) {
                if (!isset($config['elementType'])) {
                    throw new InvalidConfigException();
                }
                $config['class'] = $this->getGeneratorClass($config['elementType']);
            }
            if (!isset($config['values'])) {
                $config['values'] = fn() => [];
            }

            $generator = Craft::createObject($config);
            $generators[$name] = $generator;
        }

        return $generators;
    }

    private function getGeneratorClass(string $elementType): string
    {
        foreach (Plugin::getInstance()->getGenerators()->getAllGeneratorTypes() as $class) {
            /** @var GeneratorInterface|string $class */
            if ($class::elementType() === $elementType) {
                return $class;
            }
        }
        throw new NotSupportedException("Element type {$elementType} is not supported");
    }
}
