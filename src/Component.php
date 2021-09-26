<?php

/**
 * This file is part of Piko - Web micro framework
 *
 * @copyright 2019-2021 Sylvain PHILIP
 * @license LGPL-3.0; see LICENSE.txt
 * @link https://github.com/piko-framework/piko
 */

declare(strict_types=1);

namespace piko;

use RuntimeException;

/**
 * Component class offers events and behaviors features to inherited classes.
 * Public properties can be initialized with an array of configuration during instantiation.
 *
 * Events offer the possibility to execute external code when they are triggered.
 * Behaviors offer the possibility to add custom methods without extending the class.
 *
 * @author Sylvain PHILIP <contact@sphilip.com>
 */
abstract class Component
{
    /**
     * Behaviors container.
     *
     * @var array<callable>
     */
    public $behaviors = [];

    /**
     * Event listeners container.
     *
     * @var array<callable[]>
     */
    public $on = [];

    /**
     * Static event listeners container.
     *
     * @var array<callable[]>
     */
    public static $when = [];

    /**
     * Constructor
     *
     * @param array<string, array> $config A configuration array to set public properties of the class.
     * @return void
     */
    public function __construct(array $config = [])
    {
        Piko::configureObject($this, $config);
        $this->init();
    }

    /**
     * Method called at the end of the constructor.
     * This could be overriden in inherited classes.
     *
     * @return void
     */
    protected function init(): void
    {
    }

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
     * Event registration.
     *
     * @param string $eventName The event name to listen.
     * @param callable $callback The event listener to register. Must be  one of the following:
     *                        - A Closure (function(){ ... })
     *                        - An object method ([$object, 'methodName'])
     *                        - A static class method ('MyClass::myMethod')
     *                        - A global function ('myFunction')
     *                        - An object implementing __invoke()
     * @param string $priority The order priority in the events stack ('after' or 'before'). Default to 'after'.
     *
     * @return void
     */
    public function on(string $eventName, callable $callback, string $priority = 'after'): void
    {
        if (! isset($this->on[$eventName])) {
            $this->on[$eventName] = [];
        }

        if ($priority == 'before') {
            array_unshift($this->on[$eventName], $callback);
        } else {
            $this->on[$eventName][] = $callback;
        }
    }

    /**
     * Static event registration.
     *
     * @param string $eventName The event name to listen.
     * @param callable $callback The event listener to register. Must be  one of the following:
     *                        - A Closure (function(){ ... })
     *                        - An object method ([$object, 'methodName'])
     *                        - A static class method ('MyClass::myMethod')
     *                        - A global function ('myFunction')
     *                        - An object implementing __invoke()
     * @param string $priority The order priority in the events stack ('after' or 'before'). Default to 'after'.
     *
     * @return void
     */
    public static function when(string $eventName, callable $callback, string $priority = 'after'): void
    {
        if (! isset(static::$when[$eventName])) {
            static::$when[$eventName] = [];
        }

        if ($priority == 'before') {
            array_unshift(static::$when[$eventName], $callback);
        } else {
            static::$when[$eventName][] = $callback;
        }
    }

    /**
     * Trigger an event.
     *
     * Event listeners will be called in the order they are registered.
     *
     * @param string $eventName The event name to trigger.
     * @param array<int, mixed> $args The event arguments.
     * @return mixed[]
     */
    public function trigger(string $eventName, array $args = [])
    {
        $return = [];

        if (isset($this->on[$eventName])) {
            foreach ($this->on[$eventName] as $callback) {
                $return[] = call_user_func_array($callback, $args);
            }
        }

        if (isset(static::$when[$eventName])) {
            foreach (static::$when[$eventName] as $callback) {
                $return[] = call_user_func_array($callback, $args);
            }
        }

        return $return;
    }

    /**
     * Attach a behavior to the component instance.
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
