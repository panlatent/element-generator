<?php

namespace panlatent\craft\element\generator;

use Craft;
use craft\base\Model;
use craft\console\Application as ConsoleApplication;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Utilities;
use panlatent\craft\element\generator\console\controllers\GeneratorsController;
use panlatent\craft\element\generator\models\Settings;
use panlatent\craft\element\generator\services\Generators;
use panlatent\craft\element\generator\utilities\ElementGenerate as GeneratorsUtility;
use yii\base\Event;

/**
 * element-generator plugin
 *
 * @method static Plugin getInstance()
 * @method Settings getSettings()
 * @property-read Generators $generators
 * @author panlatent <panlatent@gmail.com>
 * @copyright panlatent
 * @license https://craftcms.github.io/license/ Craft License
 */
class Plugin extends \craft\base\Plugin
{
    public static function config(): array
    {
        return [
            'components' => [
                'generators' => ['class' => Generators::class],
            ],
        ];
    }

    public string $schemaVersion = '1.1.0';

    public function init(): void
    {
        parent::init();

        Craft::setAlias('@element-generator', __DIR__);

        Craft::$app->onInit(function() {
            $this->attachEventHandlers();

            if (Craft::$app instanceof ConsoleApplication) {
                Craft::$app->controllerMap['gen'] = GeneratorsController::class;
            }
        });
    }

    public function getGenerators(): Generators
    {
        return $this->get('generators');
    }

    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    private function attachEventHandlers(): void
    {
        Event::on(Utilities::class, Utilities::EVENT_REGISTER_UTILITY_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = GeneratorsUtility::class;
        });
    }
}
