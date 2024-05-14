<?php

namespace panlatent\craft\element\generator\base;

use craft\base\ElementInterface;

trait SavableGeneratorTrait
{
    public function save(ElementInterface $element): bool
    {
        if (!$this->beforeSave($element)) {
            return false;
        }

        $transaction = \Craft::$app->getDb()->beginTransaction();
        try {
            $success = \Craft::$app->getElements()->saveElement($element);
            if (!$success) {
                return false;
            }

            $this->afterSave($element);

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        return true;
    }

    protected function beforeSave(ElementInterface $element): bool
    {
        return true;
    }

    protected function afterSave(ElementInterface $element): void
    {

    }
}