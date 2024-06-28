# Options

## Generator Options

Generator configuration arrays `generators` can contain the following settings:

### `class`

The generator class name to be used. If this value is not set, a corresponding generator will be specified based on the element type.

For Craft’s built-in types:

| Element Type | Generator Type    | Class                                                          |
|--------------|-------------------|----------------------------------------------------------------|
| Entry        | EntryGenerator    | panlatent\craft\element\generator\generators\EntryGenerator    | 
| User         | UserGenerator     | panlatent\craft\element\generator\generators\UserGenerator     |
| Category     | CategoryGenerator | panlatent\craft\element\generator\generators\CategoryGenerator | 
| Tag          | TagGenerator      | panlatent\craft\element\generator\generators\TagGenerator      |


### `elementType` (Required)

The class name of the element type being generated. Craft's built-in element type classes include:

!!! tip

    For Craft's built-in element types, there will be additional parameters [Builtin Element Options](builtin-element-options.md)

- craft\elements\Asset
- craft\elements\Category
- craft\elements\Entry
- craft\elements\GlobalSet
- craft\elements\MatrixBlock
- craft\elements\Tag
- craft\elements\User

```php
'elementType' => craft\elements\Entry::class,
```

### `site`

Generates the site handle of the element. If this value is not set, it defaults to the primary site.

```php
'site' => 'default',
```

Currently, the purpose of showing the claim site is to bind the language of the generated content, which will set the language used by Faker.

### `values` (Required)

The callback function used to generate content. Generator obtains the value of the new element through the array returned by the callback function, 
which includes native attributes and custom fields.

We provide a `context` parameter to assist in generating values.

```php
'values' => function(\panlatent\craft\element\generator\value\Context $ctx) {
    return [];
},
```
#### `Context`

`Context` is a helper class that helps you generate values. 

+ `$ctx->faker` provides common methods for generating data, which you can extend. Powered by [fakerphp/faker](https://github.com/fakerphp/faker)
+ `$ctx->random` Provides the ability to extract data from common data (fields, files, etc.).

The preferred way to generate a value is to get it from Faker.

##### Faker

```php
'values' => function(\panlatent\craft\element\generator\value\Context $ctx) {
    return [
        'companyName' = $ctx->faker->company();
    ];
},
```

Faker is ready to use out of the box, please refer to its documentation for specific usage. [:octicons-arrow-right-24:Faker Docs](https://fakerphp.org)

Only specific issues are listed here.

###### Language

The language used by Faker is determined by the generator, and the site language is used by default, determined according to `site`.
We can specify Faker language separately to deal with different requirements.

!!! tip

    The context here is in a generator configuration, not `values`. Reference [`context`](#context)

```php
'context' => [
    'faker' => ['locale' => 'en_US'],
]
```

There are some differences between Faker's language code and Craft's language code, and we've done some work to patch this, but can't cover every situation.

You can define a mapping array to handle this edge case:

```php
'context' => [
    'faker' => [
        'languages' => [
            'en' => 'en_US',
        ]
    ]
],
```

The keys are Craft language codes and the values are Faker language codes.

###### Register Provider

!!! tip

    Registering the same class name (full class name) overrides the built-in provider.

=== "Global"

    ```php
    <?php

    use panlatent\craft\element\generator\value\Context;

    Event::on(Context::class, Context::EVENT_REGISTER_PROVIDERS, function(RegisterProvidersEvent $event) {
        $event->providers[] = YourProvider::class;
    })
    ```

=== "In Generator"

    ```php
    'context' => [
        'faker' => [
            'providers' => [
                YourProvider::class,
            ]
        ],
    ],
    ```

##### Random

Random is able to randomly select some data from elements, options or even CSV.

##### ChatGPT

### `context`

### `scenario`

### `defaultValues`

### `mode`

## Default Options

Default configuration arrays `default` can contain the following settings:

