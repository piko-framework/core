<?php

/**
 * This file is part of Piko - Web micro framework
 *
 * @copyright 2022 Sylvain Philip
 * @license LGPL-3.0; see LICENSE.txt
 * @link https://github.com/piko-framework/core
 */

declare(strict_types=1);

namespace Piko;

use RuntimeException;

/**
 * An instance using this trait can attach dynamically custom methods to itself.
 *
 * @author Sylvain Philip <contact@sphilip.com>
 */
trait BehaviorTrait
{
    /**
     * Behaviors container.
     *
     * @var array<callable>
     */
    public $behaviors = [];

    /**
     * Magic method to call a behavior.
     *
     * @param string $name The name of the behavior.
     * @param array<int, mixed> $args The behavior arguments.
     * @throws RuntimeException
     * @return mixed
     */
    public function __call(string $name, array $args)
    {
        if (isset($this->behaviors[$name])) {
            return call_user_func_array($this->behaviors[$name], $args);
        }

        throw new RuntimeException("Behavior $name not registered.");
    }

    /**
     * Attach a behavior to the class instance.
     *
     * @param string $name The behavior name.
     * @param callable $callback The behavior implementation. Must be  one of the following:
     *                        - A Closure (function(){ ... })
     *                        - An object method ([$object, 'methodName'])
     *                        - A static class method ('MyClass::myMethod')
     *                        - A global function ('myFunction')
     *                        - An object implementing __invoke()
     * @return void
     */
    public function attachBehavior(string $name, callable $callback): void
    {
        if (!isset($this->behaviors[$name])) {
            $this->behaviors[$name] = $callback;
        }
    }

    /**
     * Detach a behavior.
     *
     * @param string $name The behavior name.
     * @return void
     */
    public function detachBehavior(string $name): void
    {
        if (isset($this->behaviors[$name])) {
            unset($this->behaviors[$name]);
        }
    }
}
