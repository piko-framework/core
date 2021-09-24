# Piko framework core

[![Coverage Status](https://coveralls.io/repos/github/piko-framework/core/badge.svg?branch=main)](https://coveralls.io/github/piko-framework/core?branch=main)

The base package of the piko Framework. Contains two classes : Piko and Component

## Piko

This class is an helper offering :

 - A container to store mixed data
 - A path aliases container
 - A singleton factory

Data store example :

```php
use piko\Piko;

$date = new DateTime();
Piko::set('date', $date);
echo Piko::get('date')->format('Y-m-d');

// Can store a Callable type
Piko::set('year', function(){
    return date('Y');
});

echo Piko::get('year');

```

Path aliases example:

```php
use piko\Piko;

Piko::setAlias('@web', '/');
Piko::setAlias('@webroot', '/var/www');

if (file_exists(Piko::getAlias('@webroot/images/home.jpg')) {
  echo 'Image uri : ' . Piko::getAlias('@web/images/home.jpg')
}

```

Create and access uniques instances in your app :

```php
use piko\Piko;

$date = Piko::createObject('DateTime');
$date2 = Piko::createObject('DateTime');

var_dump(spl_object_hash($date) === spl_object_hash($date2));

```

## Component

Component is an abstract class offering :

 - A way to populate public properties of the inherited instance during the construction
 - An event manager
 - A way to inject custom behaviors
 
 ### examples

Component instanciation with an array of configuration :

```php
class Car extends \piko\Component
{
    public $color;
    public $type;
}

$car = new Car(['color' => 'red', 'type' => 'break']);

echo $car->color; // red
echo $car->type; // break

```

Events management :

```php
class Car extends \piko\Component
{
    public function run()
    {
        $this->trigger('run');
    }
}

$car = new Car();
$car->on('run', function() {
    echo 'I run!';
});

$car->run(); // Display I run!

```

Behavior injection :

```php
class Car extends \piko\Component
{
}

$car = new Car();

$car->attachBehavior('slowDown', function() {
    echo 'I am slow down';
});

$car->slowDown(); // Display I am slow down

```
