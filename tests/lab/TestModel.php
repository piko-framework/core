<?php
namespace tests\lab;

class TestModel
{
    use \Piko\ModelTrait;

    public $firstName = '';
    public $lastName = '';

    protected function validate(): void
    {
        if (empty($this->firstName)) {
            $this->setError('firstName', 'FirstName cannot not be empty');
        }

        if (empty($this->lastName)) {
            $this->setError('lastName', 'lastName cannot not be empty');
        }
    }
}