# Basic

## Create configuration file

To define your generators, create a new `element-generator.php` file within your `config/` folder. This file should return an array with an generators key, which defines your generators.

=== "Example: Posts"

    ```php
    <?php

    use panlatent\craft\element\generator\value\Context;

    return [
        'default' => [],
        'generators' => [
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

=== "Template Code"

    ```php
    <?php

    return [
        'default' => [],
        'generators' => [],
    ];
    ```

Generators are configurable items used to generate element content. They are used to define the generation logic and are
configured in the `generators` array in the configuration file. The key is the name of the generator and the value is
an array or a callback function that returns an array. For all generators, the `elementType` must be specified.