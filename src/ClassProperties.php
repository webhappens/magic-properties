<?php

namespace WebHappens\MagicProperties;

use ArrayAccess;
use ArrayIterator;
use ReflectionClass;
use IteratorAggregate;

class ClassProperties implements ArrayAccess, IteratorAggregate
{
    protected static $cache;

    protected $class;
    protected $properties = [];

    public static function for($class)
    {
        $class = static::normaliseClass($class);

        if (static::hasCache($class)) {
            return static::$cache[$class];
        }

        $reflection = new ReflectionClass(static::normaliseClass($class));

        $properties = [];
        foreach($reflection->getProperties() as $property) {
            $properties[$property->getName()] = $property;
        }

        return static::$cache[$class] = new static($properties);
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

    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    public function default($check = true)
    {
        return $this->filter(function($property) use ($check) {
            return $property->isDefault() === $check;
        });
    }

    public function static($check = true)
    {
        return $this->filter(function($property) use ($check) {
            return $property->isStatic() === $check;
        });
    }

    public function visibility($visibilities)
    {
        $visibilities = is_array($visibilities) ? $visibilities : func_get_args();

        return $this->filter(function($property) use ($visibilities) {
            foreach($visibilities as $visibility) {
                if($property->{'is' . ucfirst($visibility)}()) {
                    return true;
                }
            }

            return false;
        });
    }

    public function except($except)
    {
        $except = is_array($except) ? $except : func_get_args();

        return $this->filter(function($property) use ($except) {
            return ! in_array($property->getName(), $except);
        });
    }

    public function only($only)
    {
        $only = is_array($only) ? $only : func_get_args();

        return $this->filter(function($property) use ($only) {
            return in_array($property->getName(), $only);
        });
    }

    public function filter(callable $filter)
    {
        return new static(array_filter($this->properties, $filter));
    }

    public function keys()
    {
        return array_keys($this->properties);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->properties);
    }

    public function offsetExists($key)
    {
        return array_key_exists($key, $this->properties);
    }

    public function offsetGet($key)
    {
        return $this->properties[$key];
    }

    public function offsetSet($key, $value)
    {
        $this->properties[$key] = $value;
    }

    public function offsetUnset($key)
    {
        unset($this->properties[$key]);
    }
}
