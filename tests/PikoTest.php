<?php
namespace tests;

use PHPUnit\Framework\TestCase;
use Piko;

class PikoTest extends TestCase
{
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown(): void
    {
        Piko::reset();
    }

    public function testUnregisteredAlias()
    {
        $this->assertFalse(Piko::getAlias('@test'));
    }

    public function testNormalAlias()
    {
        Piko::setAlias('@tests', __DIR__);
        $this->assertEquals(__DIR__, Piko::getAlias('@tests'));
        $this->assertEquals('/tests', Piko::getAlias('/tests'));
    }

    public function testWrongAlias()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Alias must start with the @ character');
        Piko::setAlias('#test', __DIR__);
    }

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

        Piko::configureObject($object, $data);

        $this->assertEquals('hello 1', $object->data1);
        $this->assertNull($object->getData2());
        $this->assertNull($object->getData3());
    }
}
