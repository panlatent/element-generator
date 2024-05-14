# element-generator
Generate content for your elements in Craft CMS.


## Requirements

This plugin requires Craft CMS 4.8.0 or later, and PHP 8.1 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “element-generator”. Then press “Install”.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require panlatent/element-generator

# tell Craft to install the plugin
./craft plugin/install element-generator
```

## Setup

To define your generators, create a new `element-generator.php` file within your `config/` folder. This file should return an array with an generators key, which defines your generators.

```php
<?php

use panlatent\craft\element\generator\value\Context;

return [
    'generators' => [
        'default' => [],
        'posts' => function () {
            return [
                'elementType' => \craft\elements\Entry::class,
                'section' => 'posts',
                'values' => function (Context $ctx) {
                    $title = $ctx->faker->company();
                    return [
                        'title' => $title,
                        'shortName' => mb_substr($title, 0, 4),
                        // price is matrix field
                         'price' => [
                            [
                                'type' => 'range',
                                'fields' => [
                                    'min' => $min,
                                    'max' => $min + $ctx->faker->numberBetween(10, 100)*5000,
                                ],
                            ]
                        ],
                        // publishOption is custom field. The closure can inject field object.
                        'publishOption' => function($field) {
                            // Context->random provides some quick methods to pick field value.
                            return $ctx->random->option($field);
                        }
                    ];
                },
            ];
        },
    ],
];
```

`Context` is a helper class that helps you generate values. `$ctx->faker` provides common methods for generating data, which you can extend. Powered by [fakerphp/faker](https://github.com/fakerphp/faker)

```php
use panlatent\craft\element\generator\value\Context;

Event::on(Context::class, Context::EVENT_REGISTER_PROVIDERS, function(RegisterProvidersEvent $event) {
    $event->providers[] = YourProvider::class;
})
```

    Tips: Registering the same class name (full class name) overrides the built-in provider.



## Usages

### Console
Generate elements in the console using commands:
```bash
craft gen [name] # e.g. ./craft gen posts
```
New data will be printed to the terminal by default, use `--save` to save to the database. 
The `--size` option generates the specified number of elements.

## Development

You can create a new generator to support custom element type.