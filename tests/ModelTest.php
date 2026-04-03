<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Piko\ModelTrait;
use Piko\Tests\lab\TestModel;
use Piko\Tests\lab\TypedTestModel;

class ModelTest extends TestCase
{
    public function testModelValidation(): void
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

    public function testModelBind(): void
    {
        $model = new TestModel();
        $model->bind(['firstName' => 'John', 'lastName' => 'Lennon']);

        $this->assertSame('John', $model->firstName);
        $this->assertSame('Lennon', $model->lastName);

        $modelArray = $model->toArray();
        $this->assertArrayHasKey('firstName', $modelArray);
        $this->assertArrayHasKey('lastName', $modelArray);
        $this->assertSame('John', $modelArray['firstName']);
        $this->assertSame('Lennon', $modelArray['lastName']);
    }

    public function testModelBindWithTypedProperties(): void
    {
        $model = new TypedTestModel();
        $model->bind([
            'id' => '1',
            'active' => 'false',
            'income' => '20230.95',
            'nickname' => null,
        ]);

        $this->assertSame(1, $model->id);
        $this->assertFalse($model->active);
        $this->assertSame(20230.95, $model->income);
        $this->assertNull($model->nickname);

        $modelArray = $model->toArray();
        $this->assertSame(1, $modelArray['id']);
        $this->assertFalse($modelArray['active']);
        $this->assertSame(20230.95, $modelArray['income']);
        $this->assertNull($modelArray['nickname']);
    }

    public function testModelBindConvertsEmptyStringToNullForNullableNonStringProperties(): void
    {
        $model = new class () {
            use ModelTrait;

            public ?int $id = 42;
        };

        $model->bind(['id' => '']);

        $this->assertNull($model->id);
        $this->assertNull($model->toArray()['id']);
    }

    public function testModelBindKeepsBooleanValuesAndFallsBackForUnrecognizedStrings(): void
    {
        $model = new class () {
            use ModelTrait;

            public bool $active = false;
        };

        $model->bind(['active' => true]);
        $this->assertTrue($model->active);

        $model->bind(['active' => 'foo']);
        $this->assertTrue($model->active);
    }

    public function testModelBindCastsStringProperties(): void
    {
        $model = new class () {
            use ModelTrait;

            public string $name = '';
        };

        $model->bind(['name' => 123]);

        $this->assertSame('123', $model->name);
        $this->assertSame('123', $model->toArray()['name']);
    }

    public function testModelBindLeavesUnsupportedTypedValuesAsIs(): void
    {
        $model = new class () {
            use ModelTrait;

            public array $tags = [];
        };

        $model->bind(['tags' => ['php', 'piko']]);

        $this->assertSame(['php', 'piko'], $model->tags);
        $this->assertSame(['php', 'piko'], $model->toArray()['tags']);
    }
}
