<?php

/**
 * This file is part of Piko - Web micro framework
 *
 * @copyright 2019-2022 Sylvain PHILIP
 * @license LGPL-3.0; see LICENSE.txt
 * @link https://github.com/piko-framework/core
 */

declare(strict_types=1);

namespace Piko;

use ReflectionClass;
use ReflectionProperty;

/**
 * Base model trait.
 *
 * @author Sylvain PHILIP <contact@sphilip.com>
 */
trait ModelTrait
{
    /**
     * Errors hash container
     *
     * @var array<string>
     */
    protected $errors = [];

    /**
     * Get the public properties reprenting the data model
     *
     * @return array<mixed>
     */
    protected function getAttributes(): array
    {
        $class = get_called_class();
        $reflection = new ReflectionClass($class);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        $attributes = [];

        foreach ($properties as $property) {
            /* @var $property \ReflectionProperty */
            if ($property->class === $class) {
                $attributes[$property->name] = $this->{$property->name} ?? null;
            }
        }

        return $attributes;
    }

    /**
     * Bind the data to the model attributes.
     *
     * @param array<mixed> $data An array of data (name-value pairs).
     * @return void
     */
    public function bind(array $data): void
    {
        $reflection = new ReflectionClass($this);

        foreach ($data as $key => $value) {

            if ($reflection->hasProperty($key)) {
                $property = $reflection->getProperty($key);

                if ($property->isPublic() && $property->class === $reflection->getName()) {
                    $this->{$key} = $this->castValueForProperty($property, $value);
                }
            }
        }
    }

    /**
     * Cast a bound value according to the declared property type.
     *
     * @param ReflectionProperty $property
     * @param mixed $value
     *
     * @return mixed
     */
    private function castValueForProperty(ReflectionProperty $property, $value)
    {
        if ($value === null) {
            return null;
        }

        $type = $property->getType();

        if ($type === null) {
            return $value;
        }

        $typeName = $type->getName();

        if ($type->allowsNull() && $value === '' && $typeName !== 'string') {
            return null;
        }

        return match ($typeName) {
            'int' => (int) $value,
            'float' => (float) $value,
            'bool' => $this->castBooleanValue($value),
            'string' =>  (string) $value,
            default => $value
        };
    }

    /**
     * Cast a value to a boolean using common form representations.
     *
     * @param mixed $value
     *
     * @return bool
     */
    private function castBooleanValue($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($filtered !== null) {
            return $filtered;
        }

        return (bool) $value;
    }

    /**
     * Get the model data as an associative array.
     *
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return $this->getAttributes();
    }

    /**
     * Return the errors hash container
     *
     * @return array<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Set an error that will be appended to the errors container
     *
     * @param string $errorName
     * @param string $errorMsg
     *
     * @see ModelTrait::$errors
     */
    protected function setError(string $errorName, string $errorMsg): void
    {
        $this->errors[$errorName] = $errorMsg;
    }

    /**
     * Validate this model (Should be extended).
     * Inherited method should fill the errors array using the setError method if the model is not valid.
     *
     * @see ModelTrait::setError()
     * @see ModelTrait::isValid()
     *
     * @codeCoverageIgnore
     *
     * @return void
     */
    protected function validate(): void
    {
    }

    /**
     * Check if the model is valid
     *
     * @return boolean
     */
    public function isValid(): bool
    {
        $this->validate();

        return empty($this->errors);
    }
}
