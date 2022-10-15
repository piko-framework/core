<?php

/**
 * This file is part of Piko - Web micro framework
 *
 * @copyright 2019-2022 Sylvain PHILIP
 * @license LGPL-3.0; see LICENSE.txt
 * @link https://github.com/piko-framework/core
 */

declare(strict_types=1);

namespace piko;

use InvalidArgumentException;

/**
 * Piko is the helper class for the Piko framework.
 *
 * @author Sylvain PHILIP <contact@sphilip.com>
 */
class Piko
{
    /**
     * The registry container.
     *
     * @var mixed[]
     */
    protected static $registry = [];

    /**
     * The aliases container.
     *
     * @var string[]
     */
    protected static $aliases = [];

    /**
     * Retrieve data from the registry.
     *
     * @param string $key The registry key.
     * @param mixed $default Default value if data is not found from the registry.
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $data = static::$registry[$key] ?? $default;

        if (is_callable($data)) {
            return call_user_func($data);
        }

        return $data;
    }

    /**
     * Store data in the registry.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, $value): void
    {
        static::$registry[$key] = $value;
    }

    /**
     * Registers a path alias.
     *
     * A path alias is a short name representing a long path (a file path, a URL, etc.)
     *
     * @param string $alias The alias name (e.g. "@web"). It must start with a '@' character.
     * @param string $path the path corresponding to the alias.
     * @return void
     * @throws InvalidArgumentException if $path is an invalid alias.
     * @see Piko::getAlias()
     */
    public static function setAlias(string $alias, string $path): void
    {
        if ($alias[0] != '@') {
            throw new InvalidArgumentException('Alias must start with the @ character');
        }

        static::$aliases[$alias] = $path;
    }

    /**
     * Translates a path alias into an actual path.
     *
     * @param string $alias The alias to be translated.
     * @return string|bool The path corresponding to the alias. False if the alias is not registered.
     */
    public static function getAlias(string $alias)
    {
        if ($alias[0] != '@') {
            return $alias;
        }

        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);

        if (isset(static::$aliases[$root])) {
            return $pos === false ? static::$aliases[$root] : static::$aliases[$root] . substr($alias, $pos);
        }

        return false;
    }

    /**
     * Singleton factory method.
     *
     * @param string|array<string, array> $type The object type.
     * If it is a string, it should be the fully qualified name of the class.
     * If an array given, it should contain the key 'class' with the value corresponding
     * to the fully qualified name of the class
     * @param array<string, array> $properties A name-value pair array corresponding to the object public properties.
     * @return object
     */
    public static function createObject($type, array $properties = [])
    {
        if (is_array($type)) {
            $properties = $type;
            $type = $properties['class'];
            unset($properties['class']);
        }

        /** @phpstan-ignore-next-line */
        if (!isset(static::$registry[$type])) {
            /** @phpstan-ignore-next-line */
            static::$registry[$type] = empty($properties) ? new $type() : new $type($properties);
        }

        /** @phpstan-ignore-next-line */
        return static::$registry[$type];
    }

    /**
     * Configure public attributes of an object.
     *
     * @param object $object The object instance.
     * @param array<string, array> $properties A name-value pair array corresponding to the object public properties.
     * @return void
     */
    public static function configureObject($object, array $properties = []): void
    {
        foreach ($properties as $key => $value) {
            $object->$key = $value;
        }
    }

    /**
     * Reset aliases and registry
     */
    public static function reset(): void
    {
        static::$aliases = [];
        static::$registry = [];
    }
}
