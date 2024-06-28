<?php

namespace panlatent\craft\element\generator\value;

use craft\helpers\ArrayHelper;
use Faker\Factory;
use Faker\Generator as FakerGenerator;
use panlatent\craft\element\generator\events\RegisterProvidersEvent;
use yii\base\Component;

/**
 * @mixin FakerGenerator
 */
class Faker extends Component
{
    public const EVENT_REGISTER_PROVIDERS = 'registerProviders';

    public string $locale = 'en_US';

    public array $providers = [];

    protected ?FakerGenerator $faker = null;

    public function __construct($config = [])
    {
        $languages = array_merge([
            'en' => 'en_US',
            'zh' => 'zh_CN'
        ], ArrayHelper::remove($config, 'languages', []));

        $locale = ArrayHelper::remove($config, 'locale', '');
        if ($locale === '') {
            $language = \Craft::$app->getTargetLanguage();
            $locale = $languages[$language] ?? 'en_US';
        }
        $config['locale'] = $locale;

        parent::__construct($config);
    }

    public function init(): void
    {
        $this->faker = $this->createFaker();
    }

    public function __call($name, $params)
    {
        return $this->faker->$name(...$params);

        return parent::__call($name, $params);
    }

    public function createFaker(): FakerGenerator
    {
        $faker = Factory::create($this->locale);

        $event = new RegisterProvidersEvent([
            'faker' => $faker,
        ]);
        $this->trigger(self::EVENT_REGISTER_PROVIDERS, $event);
        foreach ($event->providers as $provider) {
            $faker->addProvider($provider);
        }

        if (!empty($this->fakerOptions['providers'])) {
            foreach ($this->fakerOptions['providers'] as $provider) {
                $faker->addProvider($provider);
            }
        }

        return $faker;
    }
}