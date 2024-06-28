<?php

namespace panlatent\craft\element\generator\value;

use craft\helpers\ArrayHelper;
use panlatent\craft\element\generator\base\Generator;
use yii\di\ServiceLocator;

/**
 * @property-read Faker $faker
 * @property-read Openai $openai
 * @property-read Random $random
 */
class Context extends ServiceLocator
{
    public function __construct(public readonly Generator $generator, $config = [])
    {
        $config = ArrayHelper::merge([
            'components' => [
                'faker' => ['class' => Faker::class],
                'openai' => ['class' => Openai::class],
                'random' => ['class' => Random::class],
            ],
        ], $config);

        parent::__construct($config);
    }

    public function getFaker(): Faker
    {
        return $this->get('faker');
    }

    public function getOpenai(): Openai
    {
        return $this->get('openai');
    }

    public function getRandom(): Random
    {
        return $this->get('random');
    }
}