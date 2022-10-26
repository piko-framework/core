<?php
use PHPUnit\Framework\TestCase;
use Piko\Event;
use Piko\EventHandlerTrait;

class TestEvent extends Event {
    public $value = '';
};

class EventHandlerTest extends TestCase
{
    protected function createEventHandler(): object
    {
        return new class {
            use EventHandlerTrait;
        };
    }

    public static function ontestEvent(TestEvent $event)
    {
        $event->value .= 'l !';
    }

    public function testListenAndDispatchEvent()
    {
        $eventHandler = $this->createEventHandler();

        $eventHandler->on(TestEvent::class, function(TestEvent $event) {
            $event->value .= 'o';
        });

        $eventHandler->on(TestEvent::class, [new class {
            public function onTestEvent(TestEvent $event)
            {
                $event->value .= 'o';
            }
        }, 'onTestEvent']);

        $eventHandler->on(TestEvent::class, 'EventHandlerTest::ontestEvent');

        $eventHandler->on(TestEvent::class, new class {
            public function __invoke(TestEvent $event)
            {
                $event->value .= 'C';
            }
        }, 10); // Priority 10

        $event = new TestEvent();
        $eventHandler->trigger($event);

        $this->assertEquals('Cool !', $event->value);
    }
}
