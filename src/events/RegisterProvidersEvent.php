<?php

namespace panlatent\craft\element\generator\events;

use Faker\Generator as FakerGenerator;
use yii\base\Event;

class RegisterProvidersEvent extends Event
{
    public ?FakerGenerator $faker = null;

    public array $providers = [];
}