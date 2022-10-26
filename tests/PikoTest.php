<?php
use PHPUnit\Framework\TestCase;
use Piko\Piko;

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

    public function testRegistryAccess()
    {
        $date = new DateTime();
        Piko::set('date', $date);
        Piko::set('year', function(){
            return date('Y');
        });

        $this->assertInstanceOf('DateTime', Piko::get('date'));
        $this->assertEquals('Default value', Piko::get('unknownKey', 'Default value'));
        $this->assertEquals(date('Y'), Piko::get('year'));
    }

    public function testSingleton()
    {
        $date = Piko::createObject('DateTime');
        $date2 = Piko::createObject('DateTime');

        $this->assertEquals(spl_object_hash($date), spl_object_hash($date2));
    }

    public function testAlias()
    {
        $this->assertFalse(Piko::getAlias('@test'));
        Piko::setAlias('@tests', __DIR__);
        $this->assertEquals(__DIR__, Piko::getAlias('@tests'));
        $this->assertEquals('/tests', Piko::getAlias('/tests'));
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Alias must start with the @ character');
        Piko::setAlias('#test', __DIR__);
    }

    public function testCreateObjectWithArray()
    {
        $mock = Piko::createObject([
            'class' => 'tests\lab\MockComponent',
            'color' => 'red',
            'data' => ['height' => 200]
        ]);

        $this->assertInstanceOf('tests\lab\MockComponent', $mock);
        $this->assertEquals('red', $mock->color);
        $this->assertArrayHasKey('height', $mock->data);
    }
}
