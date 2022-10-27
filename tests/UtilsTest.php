<?php
use PHPUnit\Framework\TestCase;

use Piko\Utils;

class UtilsTest extends TestCase
{
    public function testConfigureObject()
    {
        $object = new class {
            public $data1;
            protected $data2;
            private $data3;

            public function getData2()
            {
                return $this->data2;
            }

            public function getData3()
            {
                return $this->data3;
            }
        };

        $data = [
            'data1' => 'hello 1',
            'data2' => 'hello 2',
            'data3' => 'hello 3',
        ];

        Utils::configureObject($object, $data);

        $this->assertEquals('hello 1', $object->data1);
        $this->assertNull($object->getData2());
        $this->assertNull($object->getData3());
    }
}
