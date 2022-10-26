<?php
use PHPUnit\Framework\TestCase;

use Piko\BehaviorTrait;

class BehaviorTest extends TestCase
{
    public function testAttachAnDetachBehavior()
    {
        $object = new class {
            use BehaviorTrait;
        };

        $object->attachBehavior('sum', function ($a, $b) {
            return $a + $b;
        });

        $this->assertEquals(12, $object->sum(10, 2));

        $object->detachBehavior('sum');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Behavior sum not registered.');

        $object->sum(10, 2);
    }
}