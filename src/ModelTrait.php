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
        $reflection = new \ReflectionClass($class);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        $attributes = [];

        foreach ($properties as $property) {
            /* @var $property \ReflectionProperty */
            if ($property->class === $class) {
                $attributes[$property->name] = $property->getValue($this);
            }
        }

        return $attributes;
    }

    /**
     * Bind the data to the model attribubes.
     *
     * @param array<mixed> $data An array of data (name-value pairs).
     * @return void
     */
    public function bind(array $data): void
    {
        $attributes = $this->getAttributes();

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $attributes)) {
                $this->$key = $value;
            }
        }
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
