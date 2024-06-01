<?php

namespace panlatent\craft\element\generator\helpers;

use Craft;
use craft\base\ElementInterface;
use craft\elements\db\ElementQuery;
use craft\fields\data\SingleOptionFieldData;
use Money\Money;
use yii\helpers\Console;

abstract class ElementDumper
{
    public static function dump(ElementInterface $element): void
    {
        $var = self::export($element);

        $isCli = \in_array(\PHP_SAPI, ['cli', 'phpdbg'], true);
        if ($isCli) {
           self::consolePrint($var);
        }
    }

    public static function dd(ElementInterface $element): never
    {
        self::dump($element);
        exit(0);
    }

    public static function export(ElementInterface $element): array
    {
        $var = [];

        // Set language
        $language = Craft::$app->language;
        Craft::$app->language = $element->getSite()->getLocale()->getLanguageID();

        $attributes = $element->attributes();

        if ($element::hasTitles()) {
            $var[Craft::t('app', 'Title')] = $element->title;
        }

        foreach (self::elementAttributes() as $name => $handle) {
            if (in_array($handle, $attributes)) {
                $var[$name] = $element->$handle;
                //$this->stdout(sprintf("%s: %s\n", $name, $element->$handle));
            }
        }

        $layout = $element->getFieldLayout();
        foreach ($layout->getVisibleCustomFields($element) as $field) {
            $var[$field->name] = self::getPrintFieldValue($element->{$field->handle});
        }

        if ($element::hasStatuses()) {
            $status = $element::statuses()[$element->getStatus()] ?? '';
            $var[Craft::t('app', 'Status')] = $status['label'] ?? $status;
        }

        // Restore language
        Craft::$app->language = $language;

        return $var;
    }

    protected static function getPrintFieldValue($value): string
    {
        if ($value === null) {
            return 'null';
        }

        if ($value instanceof ElementQuery) {
            $values = array_map(fn($value) => (string)$value, $value->all());
            return '[' . implode(', ', $values) . ']';
        } elseif ($value instanceof SingleOptionFieldData) {
            return (string)$value->label;
        } elseif ($value instanceof Money) {
            return $value->getAmount() . ' ' . $value->getCurrency();
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif ($value instanceof \DateTime) {
            return Craft::$app->formatter->asDate($value);
        } elseif ($value instanceof \Stringable) {
            return $value->__toString();
        }

        return '"' . $value . '"';
    }

    protected static function elementAttributes(): array
    {
        return [
            Craft::t('app', 'Username') => 'username',
            Craft::t('app', 'Full Name') => 'fullName',
            Craft::t('app', 'Email') => 'email',
            // Craft::t('app', 'First Name') => 'firstName',
            // Craft::t('app', 'Last Name') => 'lastName',
        ];
    }

    private static function consolePrint(array $var): void
    {
        foreach ($var as $key => $value) {
            Console::stdout(sprintf("%s: %s\n", $key, $value));
        }
    }
}