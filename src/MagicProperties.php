<?php

namespace WebHappens\MagicProperties;

use BadMethodCallException;
use WebHappens\MagicProperties\Str;

trait MagicProperties
{
    private $_classProperties;

    public function setPropertyValues($data = [])
    {
        foreach ($data as $property => $value) {
            if ( ! $this->hasProperty($property)) {
                continue;
            }

            $this->setProperty($property, $value);
        }

        return $this;
    }

    public function toArray()
    {
        return $this->getMagicProperties()
            ->mapWithKeys(function ($property) {
                return [$property => $this->getProperty($property)];
            })
            ->toArray();
    }

    protected function hasProperty($name)
    {
        return array_search($name, $this->getMagicProperties()) !== false;
    }

    protected function getProperty($property)
    {
        if ( ! $this->hasProperty($property)) {
            trigger_error(sprintf(
                'Undefined property %s::%s', static::class, $property
            ));
        }

        $value = $this->{$property};

        if (method_exists($this, 'get'.Str::studly($property).'Property')) {
            return $this->{'get'.Str::studly($property).'Property'}($value);
        }

        return $value;
    }

    protected function setProperty($property, $value)
    {
        if (method_exists($this, 'set'.Str::studly($property).'Property')) {
            $value = $this->{'set'.Str::studly($property).'Property'}($value);
        }

        $this->{$property} = $value;

        return $this;
    }

    protected function getMagicProperties()
    {
        $properties = [];

        foreach ($this->getProperties() as $name => $property) {
            if ($property->isStatic() || $property->isPrivate()) {
                continue;
            }

            if (in_array($name, $this->getHiddenProperties())) {
                continue;
            }

            array_push($properties, $name);
        }

        return $properties;
    }

    protected function getHiddenProperties()
    {
        if (isset($this->hidden) && is_array($this->hidden)) {
            return $this->hidden;
        }

        return [];
    }

    protected function getProperties()
    {
        return ClassProperties::for($this);
    }

    protected function getPropertyValues()
    {
        $properties = [];

        foreach($this->getMagicProperties() as $property) {
            $properties[$property] = $this->getProperty($property);
        }

        return $properties;
    }

    public function __call($name, $arguments)
    {
        if (Str::startsWith($name, 'get')) {
            $name = lcfirst(str_replace('get', '', $name));
        }

        if ($this->hasProperty($name)) {
            if (count($arguments) === 0) {
                return $this->getProperty($name);
            }

            if (count($arguments) === 1) {
                return $this->setProperty($name, $arguments[0]);
            }

            return $this->setProperty($name, $arguments);
        }

        throw new BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()', static::class, $name
        ));
    }

    public function __get($key)
    {
        return $this->getProperty($key);
    }

    public function __set($key, $value)
    {
        $this->setProperty($key, $value);
    }
}
