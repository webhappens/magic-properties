<?php

namespace WebHappens\MagicProperties;

use ArrayIterator;
use ReflectionClass;
use IteratorAggregate;

class ClassProperties implements IteratorAggregate
{
    protected static $cache;

    protected $class;
    protected $properties = [];
    protected $filters = [];

    public static function for($class)
    {
        $class = static::normaliseClass($class);

        if (static::hasCache($class)) {
            return static::$cache[$class];
        }

        return static::$cache[$class] = new static($class);
    }

    public static function hasCache($class)
    {
        return isset(static::$cache[static::normaliseClass($class)]);
    }

    protected static function normaliseClass($class)
    {
        if (is_object($class)) {
            return get_class($class);
        }

        return $class;
    }

    public function __construct($class)
    {
        $this->class = $class;

        foreach((new ReflectionClass(static::normaliseClass($class)))->getProperties() as $property) {
            $this->properties[$property->getName()] = $property;
        }
    }

    public function getIterator()
    {
        return new ArrayIterator($this->properties);
    }
}
