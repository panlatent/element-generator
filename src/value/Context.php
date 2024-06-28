<?php

namespace panlatent\craft\element\generator\value;

use panlatent\craft\element\generator\base\Generator;
use yii\di\ServiceLocator;

/**
 * @property-read Faker $faker
 * @property-read Random $random
 */
class Context extends ServiceLocator
{
    public function __construct(public readonly Generator $generator, $config = [])
    {
        $config = array_merge([
            'components' => [
                'faker' => ['class' => Faker::class],
                'random' => ['class' => Random::class],
            ],
        ], $config);

        parent::__construct($config);
    }

    public function getFaker(): FakerGenerator
    {
        return $this->get('faker');
    }

    public function getRandom(): Random
    {
        return $this->get('random');
    }
}