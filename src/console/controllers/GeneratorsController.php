<?php

namespace panlatent\craft\element\generator\console\controllers;

use panlatent\craft\element\generator\base\Generator;
use panlatent\craft\element\generator\base\SavableGeneratorInterface;
use panlatent\craft\element\generator\helpers\ElementDumper;
use panlatent\craft\element\generator\Plugin;
use yii\console\Controller;
use yii\console\ExitCode;

class GeneratorsController extends Controller
{
    public $defaultAction = 'generate';

    public string $site;

    public string $username;

    /**
     * @var bool Generate all fields.
     * @see ElementGenerate::MODE_FULL
     */
    public bool $full = false;

    /**
     * @var int Number of elements to generate
     */
    public int $size = 1;

    /**
     * @var bool
     */
    public bool $asJson = false;

    /**
     * @var bool Save the generated element
     */
    public bool $save = false;

   // public array $fieldValues = [];

    /**
     * @inheritdoc
     */
    public function options($actionID): array
    {
        return array_merge(parent::options($actionID), match ($actionID) {
            'generate' => ['full', 'size', 'asJson', 'save'],
            default => [],
        });
    }

    public function actionList(): int
    {
        $generators = Plugin::getInstance()->getGenerators()->getAllGenerators();
        foreach ($generators as $name => $generator) {
            $this->stdout(sprintf("%s\n", $name));
        }
        return ExitCode::OK;
    }

    public function actionGenerate(string $name = ''): int
    {
        if ($name === '') {
            $this->stderr("No generator name specified\n");
            $names = array_keys(Plugin::getInstance()->getGenerators()->getAllGenerators());
            $this->stdout("Available generators: " . implode(", ", $names) . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $elements = \Craft::$app->getElements();
        $generator = Plugin::getInstance()->getGenerators()->getGeneratorByName($name);
        if (!$generator) {
            $this->stderr("Generator {$name} not found\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if ($this->full) {
            $generator->mode = Generator::MODE_FULL;
        }

        for ($i = 0; $i < $this->size; $i++) {
            $element = $generator->generate();
            if (!$this->asJson) {
                $this->stdout(str_pad('', 60, '=') . "\n");
                ElementDumper::dump($element);
                $this->stdout(str_pad('', 60, '=') . "\n");
            }

            if (!$element->validate()) {
                $this->stderr("Element validation failed: " . print_r($element->getErrors(), true) . "\n");
                continue;
            }

            if ($this->asJson) {
                $this->stdout(json_encode($element) . "\n");
            }

            if ($this->save) {
                if ($generator instanceof SavableGeneratorInterface) {
                    $success = $generator->save($element);
                } else {
                    $success = $elements->saveElement($element);
                }
                if (!$success) {
                    $this->stderr("Element save failed: " . print_r($element->getErrors(), true));
                } else {
                    $this->stdout("Element save success.\n");
                }
            }
        }

        return ExitCode::OK;
    }
}