<?php

/**
 * This file is part of Piko - Web micro framework
 *
 * @copyright 2022 Sylvain PHILIP
 * @license LGPL-3.0; see LICENSE.txt
 * @link https://github.com/piko-framework/core
 */

declare(strict_types=1);

namespace Piko;

use ReflectionClass;
use ReflectionProperty;

/**
 * This class contains various utilities.
 *
 * @author Sylvain PHILIP <contact@sphilip.com>
 */
class Utils
{
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
}
