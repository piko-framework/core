<?php
namespace Piko\Tests\lab;

class TypedTestModel
{
    use \Piko\ModelTrait;

    public ?int $id = null;
    public ?bool $active = false;
    public float $income = 0.0;
    public ?string $nickname = null;
}
