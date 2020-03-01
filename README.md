![tests](https://github.com/webhappens/magic-properties/workflows/tests/badge.svg)

# Magic properties

Add a simple fluid interface for getters, setters, accessors and mutators to any PHP class.

  - [Installation](#installation)
  - [Getters](#getters)
  - [Setters](#setters)
  - [Readonly properties](#readonly-properties)
  - [Chainable setters](#chainable-setters)
  - [Accessors](#accessors)
  - [Mutators](#mutators)
  - [Serialization](#serialization)

## Installation

Install via composer:

```shell
composer require webhappens/magic-properties
```

Insert the `MagicProperties` trait into your class:

```php
use \WebHappens\MagicProperties\MagicProperties;
```

If your class is already using the `__call` method, add the following to it:

```php
public function __call($method, $arguments)
{
    // ...

    if ($property = $this->matchMagicProperty($name)) {
        return $this->callMagicProperty($property, $arguments);
    }

    // ...

    // throw new \BadMethodCallException();
}
```

## Getters

Getters support both "property" and "method" syntax and allow you to get `public` and `protected` properties.

```php
$person = new class {
    use MagicProperties;

    public $name = 'Sam';
    protected $role = 'developer';
};

$name = $person->name;
// or
$name = $person->name();

$role = $person->role;
// or
$role = $person->role();
```

The Getter "method" name may optionally be prefixed with "get" if you prefer that syntax.

```php
$person->getName();
```

## Setters

Setters support both "property" and "method" syntax and allow you to set `public` and `protected` properties.

```php
$person = new class {
    use MagicProperties;

    public $name;
    protected $role;
};

$person->name = 'Sam';
// or
$person->name('Sam');

$person->role = 'developer';
// or
$person->role('developer');
```

The Setter "method" name may optionally be prefixed with "set" if you prefer that syntax.

```php
$person->setName('Sam');
```

## Readonly properties

If you want to protect specific `protected` properties from being set from **outside the class**, you may list them in an `$readonly` array inside your class.

```php
protected $readonly = ['id'];
```

Readonly properties can still be changed from **inside the class** using the standard php syntax.

## Chainable setters

When using the "method" syntax for Setters you may set multiple properties in a single chain.

```php
$person->name('Sam')->role('developer');
```

## Accessors

When a Getter is called for a  `protected` property from **outside the class**, the value is passed through an Accessor method, giving you a chance to modify it before it is returned.

When called from **inside the class**, it is only passed through an Accessor method when "method" syntax is used.
 
`public` and `private` properties are **never** passed through an Accessor method.

To add an Accessor method for a `protected` property, simply create a `protected` method that follows the Accessor naming convention of `get{PropertyName}Property`.

The method will receive the stored value as a single argument and should return the modified value.

```php
$person = new class {
    use MagicProperties;

    protected $role = 'developer';

    protected function getRoleProperty($value)
    {
        return ucwords($value);
    }
};

$role = $person->role;
// or
$role = $person->role();
```

In this example, the role will be returned with an uppercase first letter.

## Mutators

When a Setter is called for a  `protected` property from **outside the class**, the value is passed through a Mutator method, giving you a chance to modify it before it is set.

When called from **inside the class**, it is only passed through a Mutator method when "method" syntax is used.
 
`public` and `private` properties are **never** passed through a Mutator method.

To add a Mutator method for a `protected` property, simply create a `protected` method that follows the Mutator naming convention of `set{PropertyName}Property`.

The method will receive the passed value as a single argument and should return the modified value.

```php
$person = new class {
    use MagicProperties;

    protected $role;

    protected function setRoleProperty($value)
    {
        return ucwords($value);
    }
};

$person->role = 'developer';
// or
$person->role('developer');
```

In this example, the role will be stored with an uppercase first letter.

## Serialization

Call the `getMagicProperties` method to serialize all `public` and `protected` properties to an array.

## Credits

- Sam Leicester: sam@webhappens.co.uk
- Ben Gurney: ben@webhappens.co.uk
- [All Contributors](../../contributors)

Our `Str` class is just a copy of some functions from Laravel's `Str` helper.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
