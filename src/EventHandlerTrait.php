<?php

/**
 * This file is part of Piko - Web micro framework
 *
 * @copyright 2024 Sylvain Philip
 * @license LGPL-3.0; see LICENSE.txt
 * @link https://github.com/piko-framework/core
 */

declare(strict_types=1);

namespace Piko;

use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * An instance using this trait become an event handler :
 * it can dispatches events and listen to them.
 *
 * @author Sylvain Philip <contact@sphilip.com>
 */
trait EventHandlerTrait
{
    /**
     * @var EventDispatcherInterface
     */
    protected ?EventDispatcherInterface $eventDispatcher = null;

    /**
     * @var ListenerProvider
     */
    protected ?ListenerProvider $listenerProvider = null;

    /**
     * Registers an event listener.
     *
     * @param string $eventClassName The class name of the event to listen for.
     * @param callable $callback The callback to execute when the event is dispatched.
     * @param int|null $priority Optional priority for the listener (higher means earlier execution).
     */
    public function on(string $eventClassName, callable $callback, ?int $priority = null)
    {
        if ($this->eventDispatcher === null) {
            $this->listenerProvider = new ListenerProvider();
            $this->eventDispatcher = new EventDispatcher($this->listenerProvider);
        }

        $this->listenerProvider->addListenerForEvent($eventClassName, $callback, $priority);
    }

    /**
     * Trigger an event that may be listen by event listeners.
     *
     * @param object $event The event instance to dispatch.
     * @return object The same event instance that may be altered by event listeners.
     */
    public function trigger(object $event): object
    {
        if ($this->eventDispatcher instanceof EventDispatcherInterface) {
            return $this->eventDispatcher->dispatch($event);
        }

        return $event;
    }
}
