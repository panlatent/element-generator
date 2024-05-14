<?php

namespace panlatent\craft\element\generator\base;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\models\Site;
use panlatent\craft\element\generator\ValueBag;

trait ElementAttributes
{
    public mixed $title;

    public ?string $site = null;

    public string $scenario = Element::SCENARIO_LIVE;

    public function getSite(): Site
    {
        if ($this->site === null) {
            return Craft::$app->getSites()->getPrimarySite();
        }
        return Craft::$app->getSites()->getSiteByHandle($this->site);
    }

    protected function makeAttributes(ElementInterface $element, ValueBag $values): array
    {
        $attributes = [];

        if ($element::hasTitles()) {
            $attributes['title'] = $values->get('title', fn() => $this->context->faker->text());
        }

        if ($element::hasStatuses()) {
            $status = $values->get('status');
            if ($status !== null) {
                $this->setStatusAttribute($status, $attributes);
            }
        }

        return $attributes;
    }

    protected function setStatusAttribute(string $status, array &$attributes): void
    {
        switch ($status) {
            case Element::STATUS_ARCHIVED:
                $attributes[Element::STATUS_ARCHIVED] = true;
                break;
            case Element::STATUS_DISABLED:
                $attributes[Element::STATUS_DISABLED] = false;
                break;
            case Element::STATUS_ENABLED:
                $attributes[Element::STATUS_ENABLED] = true;
                break;
        }
    }
}