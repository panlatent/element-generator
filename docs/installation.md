# Installation

You can install Element Generator via the plugin store, or through Composer.

## via Craft Plugin Store

To install [Element Generator](https://plugins.craftcms.com/element-generator), navigate to the Plugin Store section of your Craft control panel, search for Element Generator, and click the Install/Try button.

## via Composer

### Install package

1. Open your terminal and go to your Craft project:

    ```bash
    cd /path/to/project
    ```

2. Then tell Composer to load the plugin:

=== "DDEV"

    ``` bash
    ddev composer require panlatent/element-generator
    ```

=== "Host"

    ``` bash
    composer require panlatent/element-generator
    ```

### Install plugin

=== "DDEV"

    ``` bash
    ddev plugin/install element-generator
    ```

=== "Host"

    ``` bash
    ./craft plugin/install element-generator
    ```