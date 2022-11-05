<?php
use PHPUnit\Framework\TestCase;
use Piko\Tests\lab\TestModel;

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

    public function testCreateObjectWithDefinitionString()
    {
        $object = Piko::createObject(DateTime::class);
        $this->assertInstanceOf(DateTime::class, $object);
    }

    public function testCreateObjectWithDefinitionStringAndProperties()
    {
        $object = Piko::createObject(TestModel::class, ['firstName' => 'John']);
        $this->assertInstanceOf(TestModel::class, $object);
        $this->assertEquals('John', $object->firstName);
    }

    public function testCreateObjectWithDefinitionArray()
    {
        $object = Piko::createObject([
            'class' => DateTime::class,
            'construct' => ['2019-03-01']
        ]);

        $this->assertInstanceOf(DateTime::class, $object);
        $this->assertEquals('2019', $object->format('Y'));
    }

    public function testCreateObjectWithoutClass()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing "class" key in the class definition array.');
        Piko::createObject([]);
    }

    public function testCreateObjectWithWrongClassType()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Type must be string.');
        Piko::createObject(['class' => new DateTime()]);
    }

    public function testCreateObjectWithUnknownType()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class UnknownClass not found');
        Piko::createObject('UnknownClass');
    }
}
