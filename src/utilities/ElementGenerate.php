<?php

namespace panlatent\craft\element\generator\utilities;

use Craft;
use craft\base\Utility;
use panlatent\craft\element\generator\Plugin;

class ElementGenerate extends Utility
{
    public static function displayName(): string
    {
        return Craft::t('element-generator', 'Element Generate');
    }

    public static function id(): string
    {
        return 'element-generator';
    }

    public static function icon(): ?string
    {
        return Craft::getAlias('@element-generator/icon-mask.svg');
    }

    public static function contentHtml(): string
    {
        $view = Craft::$app->getView();

        $plugin = Plugin::getInstance();
        return $view->renderTemplate('element-generator/_components/utilities/ElementGenerate.twig', [
            'generators' => $plugin->getGenerators()->getAllGenerators(),
        ]);
    }
}
