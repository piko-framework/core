<?php
use PHPUnit\Framework\TestCase;

use tests\lab\TestModel;

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
}
