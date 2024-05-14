<?php

namespace panlatent\craft\element\generator\value;

use Craft;
use Faker\Factory;
use Faker\Generator as FakerGenerator;
use panlatent\craft\element\generator\base\Generator;
use panlatent\craft\element\generator\events\RegisterProvidersEvent;
use yii\base\Component;

/**
 * @property-read FakerGenerator $faker
 * @property-read Random $random
 */
class Context extends Component
{
    public const EVENT_REGISTER_PROVIDERS = 'registerProviders';
    
    private ?FakerGenerator $_faker = null;

    private ?Random $_random = null;

    public function __construct(public readonly Generator $generator, $config = [])
    {
        parent::__construct($config);
    }

    public function getFaker(): FakerGenerator
    {
        if ($this->_faker === null) {
            $this->_faker = $this->createFaker(language: $this->generator->getSite()->language);
        }
        return $this->_faker;
    }

    public function getRandom(): Random
    {
        if ($this->_random === null) {
            $this->_random = new Random();
        }
        return $this->_random;
    }

    protected function getFakerLocaleName(string $language): string
    {
        return match($language) {
            'en' => 'en_US',
            'zh' => 'zh_CN',
            default => $language
        };
    }

    private function createFaker(string $language = ''): FakerGenerator
    {
        $language = $language ?: Craft::$app->getTargetLanguage();
        $locale = $this->getFakerLocaleName($language);
        $faker = Factory::create($locale);

        $event = new RegisterProvidersEvent([
            'faker' => $faker
        ]);
        $this->trigger(self::EVENT_REGISTER_PROVIDERS, $event);
        foreach ($event->providers as $provider) {
            $faker->addProvider($provider);
        }

        return $faker;
    }

}