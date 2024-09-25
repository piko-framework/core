<?php

/**
 * This file is part of Piko - Web micro framework
 *
 * @copyright 2019-2024 Sylvain PHILIP
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
     * Generic factory method.
     *
     * @param class-string|array<string, mixed> $type The object type.
     * If it is a string, it should be the fully qualified name of the class.
     * If an array given, it must contain the key 'class' with the value corresponding
     * to the fully qualified name of the class. It should also contain the key 'construct' to give an array of
     * constuctor arguments
     * @param array<string, mixed> $properties A key-value paired array corresponding to the object public properties.
     * @return object
     */
    public static function createObject($type, array $properties = []): object
    {
        if (is_array($type)) {
            $properties = $type;

            if (!isset($properties['class'])) {
                throw new InvalidArgumentException('Missing "class" key in the class definition array.');
            }

            $type = $properties['class'];
            unset($properties['class']);
        }

        if (!is_string($type)) {
            throw new InvalidArgumentException('Type must be string.');
        } elseif (!class_exists($type)) {
            throw new InvalidArgumentException(sprintf('Class %s not found', $type));
        }

        $reflection = new ReflectionClass($type);

        if (isset($properties['construct'])) {
            $constructArgs = $properties['construct'];
            unset($properties['construct']);
        }

        $object = isset($constructArgs) && is_array($constructArgs) ?
        $reflection->newInstanceArgs($constructArgs) : $reflection->newInstance();

        if (count($properties)) {
            static::configureObject($object, $properties);
        }

        return $object;
    }

    /**
     * Configure public properties of an object.
     *
     * @param object $object The object instance.
     * @param array<string, mixed> $data A key-value array corresponding to the public properties of an object.
     * @return void
     */
    public static function configureObject(object $object, array $data = []): void
    {
        $reflection = new ReflectionClass($object);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            /* @var $property \ReflectionProperty */
            if (isset($data[$property->name])) {
                $property->setValue($object, $data[$property->name]);
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
