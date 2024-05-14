<?php

namespace panlatent\craft\element\generator\controllers;

use Craft;
use craft\web\Controller;
use panlatent\craft\element\generator\Plugin;
use yii\base\InvalidConfigException;
use yii\web\Response;

class UtilitiesController extends Controller
{
    public function actionGenerateAction(): ?Response
    {
        $request = $this->request;
        $elementService = Craft::$app->getElements();

        $name = $request->getRequiredBodyParam('name');
        $generator = Plugin::getInstance()->getGenerators()->getGeneratorByName($name);
        if (!$generator) {
            throw new InvalidConfigException("Not found generator: $name");
        }

        $total = $request->getBodyParam('total', 1);

        $savedTotal = 0;
        for ($i = 0; $i < $total; ++$i) {
            $element = $generator->generate();
            if ($elementService->saveElement($element)) {
                $savedTotal++;
            }
        }

        Craft::$app->getSession()->setNotice(Craft::t('element-generator', '{total} elements saved.', ['total' => $savedTotal]));

        return null;
    }
}