<?php

namespace panlatent\craft\element\generator;

use Craft;
use craft\base\FieldInterface;
use craft\elements\Asset;
use craft\helpers\StringHelper;

class ValueBag
{
    public function __construct(protected array $values = [])
    {

    }

    public function get(string $name, $default = null): mixed
    {
        return $this->parseAttribute($name, $default);
    }

    public function fieldValue(FieldInterface $field): mixed
    {
        return (new FieldValueExpression($field, $this->get($field->handle)))->getValue();
    }

    public function getAsset(string $name): Asset
    {
        $path = $this->get($name);
        return $this->parserAsset($path);
    }

    public function has(string $name): bool
    {
        return isset($this->values[$name]);
    }

    protected function parseAttribute(string $key, $default = null): mixed
    {
        if (!isset($this->values[$key])) {
            return $default instanceof \Closure ? $default() : $default;
        }
        $value = $this->values[$key];
        unset($this->values[$key]);

        return ValueExpression::parse($value);
    }

    protected function parserAsset($path): Asset
    {
        // @see https://www.php.net/manual/en/wrappers.data.php
        $fp = fopen($path, 'r');
        $mime = stream_get_meta_data($fp)['mediatype'];
        $ext = match($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
        };

        $filename = 'asset_'. StringHelper::randomString(10). '.' . $ext;
        $fileLocation = Craft::getAlias('@runtime/temp') . DIRECTORY_SEPARATOR. $filename;
        file_put_contents($fileLocation, stream_get_contents($fp));
        fclose($fp);

        $asset = new Asset();
        $asset->tempFilePath = $fileLocation;
        $asset->filename = $filename;

        return $asset;
    }
}