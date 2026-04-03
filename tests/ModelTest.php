<?php
use PHPUnit\Framework\TestCase;

use Piko\Tests\lab\TestModel;
use Piko\Tests\lab\TypedTestModel;

class ModelTest extends TestCase
{
    public function testModelValidation()
    {
        $model = new TestModel();

        $this->assertFalse($model->isValid());

        $errors = $model->getErrors();

        $this->assertArrayHasKey('firstName', $errors);
        $this->assertArrayHasKey('lastName', $errors);

        $model = new TestModel();

        $model->firstName = 'John';
        $model->lastName = 'Lennon';

        $this->assertTrue($model->isValid());
    }

    public function testModelBind()
    {
        $model = new TestModel();
        $model->bind(['firstName' => 'John', 'lastName' => 'Lennon']);
        $this->assertEquals('John', $model->firstName);
        $this->assertEquals('Lennon', $model->lastName);

        $modelArray = $model->toArray();
        $this->assertArrayHasKey('firstName', $modelArray);
        $this->assertArrayHasKey('lastName', $modelArray);
        $this->assertEquals('John', $modelArray['firstName']);
        $this->assertEquals('Lennon', $modelArray['lastName']);
    }

    public function testModelBindWithTypedProperties()
    {
        $model = new TypedTestModel();
        $model->bind([
            'id' => '1',
            'active' => 'false',
            'income' => '20230.95',
            'nickname' => null,
        ]);

        $this->assertSame(1, $model->id);
        $this->assertSame(false, $model->active);
        $this->assertEquals(20230.95, $model->income);
        $this->assertNull($model->nickname);

        $modelArray = $model->toArray();
        $this->assertSame(1, $modelArray['id']);
        $this->assertSame(false, $modelArray['active']);
        $this->assertEquals(20230.95, $modelArray['income']);
        $this->assertNull($modelArray['nickname']);
    }
}
