<?php

/**
 * @param $class_name
 */
spl_autoload_register(function ($class_name) {
    $namespace = 'Carbon_Field_Iso8601Duration';

    if (strpos($class_name, $namespace . '\\') !== 0) {
        return false;
    }

    $parts = explode('\\', substr($class_name, strlen($namespace . '\\')));

    $path = __DIR__ . '/core';
    foreach ($parts as $part) {
        $path .= '/' . $part;
    }
    $path .= '.php';

    if (!file_exists($path)) {
        return false;
    }

    require_once $path;

    return true;
});

use \Carbon_Fields\Carbon_Fields;
use \Carbon_Field_Iso8601Duration\Iso8601Duration_Field;

if (!defined('Carbon_Field_Iso8601Duration\\DIR')) {
    define('Carbon_Field_Iso8601Duration\\DIR', __DIR__);
}

Carbon_Fields::extend(Iso8601Duration_Field::class, function ($container) {

    return new Iso8601Duration_Field(
        $container['arguments']['type'],
        $container['arguments']['name'],
        $container['arguments']['label']
    );
});
