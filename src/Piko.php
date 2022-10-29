<?php

/**
 * This file is part of Piko - Web micro framework
 *
 * @copyright 2019-2022 Sylvain PHILIP
 * @license LGPL-3.0; see LICENSE.txt
 * @link https://github.com/piko-framework/core
 */

declare(strict_types=1);

/**
 * Piko is the helper class for the Piko framework.
 *
 * @author Sylvain PHILIP <contact@sphilip.com>
 */
class Piko
{
    /**
     * The aliases container.
     *
     * @var string[]
     */
    protected static $aliases = [];

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
     * Configure public properties of an object.
     *
     * @param object $object The object instance.
     * @param array<string, mixed> $data A key-value array corresponding to the public properties of an object.
     * @return void
     */
    public static function configureObject($object, array $data = []): void
    {
        $class = get_class($object);
        $reflection = new ReflectionClass($class);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            /* @var $property \ReflectionProperty */
            if (isset($data[$property->name])) {
                $object->{$property->name} = $data[$property->name];
            }
        }
    }

    /**
     * Reset aliases
     */
    public static function reset(): void
    {
        static::$aliases = [];
    }
}
