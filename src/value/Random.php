<?php

namespace panlatent\craft\element\generator\value;

use Craft;
use craft\base\ElementInterface;
use craft\fields\BaseOptionsField;
use craft\fields\BaseRelationField;
use craft\fields\Matrix;
use craft\fields\Money;
use craft\helpers\ElementHelper;
use craft\services\ElementSources;
use panlatent\craft\element\generator\DefaultValueProvider;
use yii\db\Expression;

class Random
{
    public function __construct()
    {

    }

    public function bool(): bool
    {
        return (bool)random_int(0, 1);
    }

    /**
     * @param BaseRelationField $field
     * @param int|null $min
     * @param int|null $max
     * @return array
     */
    public function elements(BaseRelationField $field, ?int $min = null, ?int $max = null): array
    {
        /** @var ElementInterface|string  $class */
        $class = $field::elementType();
        $query = $class::find();

        $sources = $field->getInputSources();
        $source = ElementHelper::findSource($class, array_values($sources)[random_int(0, count($sources) -1)], ElementSources::CONTEXT_FIELD);
        if (isset($source['criteria'])) {
            Craft::configure($query, $source['criteria']);
        }

        $query
            ->orderBy(new Expression('rand()'))
            ->limit(random_int($min ?? $field->minRelations ?? 0, $max ?? $field->maxRelations ?? 5));

        return $query->ids();
    }

    public function matrix(Matrix $field, DefaultValueProvider $provider): array
    {
        $types = $field->getBlockTypes();
        $totalBlock = random_int($field->minBlocks, $field->maxBlocks);

        $blocks = [];

        for ($i = 1; $i <= $totalBlock; $i++) {
            $type = $types[random_int(0, count($types) - 1)];
            $blocks["new:$i"] = [
                'type' => $type->handle,
                'fields' => $provider->getValues($type->getCustomFields()),
            ];
        }

        return [
            'sortOrder' => array_keys($blocks),
            'blocks' => $blocks,
        ];
    }

    public function option(BaseOptionsField $field)
    {
        $options = $field->options;
        return $options[random_int(0, count($options) - 1)]['value'];
    }

    public function money(Money $field): int
    {
        return random_int($field->min, $field->max);
    }


}