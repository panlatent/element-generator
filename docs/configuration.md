# Configuration

## Create configuration file

To define your generators, create a new `element-generator.php` file within your `config/` folder. This file should return an array with an generators key, which defines your generators.

=== "Template Codes" 

    ```php linenums="1"
    <?php

    use panlatent\craft\element\generator\value\Context;

    return [
        'default' => [],
        'generators' => [],
    ];
    ```

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

## Add a generator

生成器是用来生成元素内容的可配项，用来定义生成逻辑，通过在配置文件中的 `generators` 配置。键是生成器的名字，值是一个数组或返回数组的回调函数。对于所有生成器，必须
指定元素类型

### Class

### Element type

#### Entry

### Site and Localization

Language

### Default options

### Values

调用 `values` ，生成器通过回调函数获取元素的值，这包括原生属性值与自定义字段值。 我们提供了一个 `context` 参数来辅助生成值，

`Context` is a helper class that helps you generate values. `#!php $ctx->faker` provides common methods for generating data, which you can extend. Powered by [fakerphp/faker](https://github.com/fakerphp/faker)

生成一个值的首选方式是从 faker 中获得。

#### Faker

注册额外的 Faker Provider

```php
use panlatent\craft\element\generator\value\Context;

Event::on(Context::class, Context::EVENT_REGISTER_PROVIDERS, function(RegisterProvidersEvent $event) {
    $event->providers[] = YourProvider::class;
})
```

!!! tip

    Registering the same class name (full class name) overrides the built-in provider.


#### Faker Language

Faker 使用的语言由生成器确定，默认使用点站语言。但我们可以单独指定 Faker 语言。

#### Random

Random 能够从元素、选项甚至 CSV 中随机选择一些数据

#### ChatGPT

### Mode

### Default value provider
