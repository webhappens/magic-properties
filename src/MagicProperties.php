<?php

namespace WebHappens\MagicProperties;

use Exception;
use BadMethodCallException;
use WebHappens\MagicProperties\Str;

trait MagicProperties
{

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

    protected function hasProperty($name)
    {
        return isset($this->getMagicProperties()[$name]);
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
        if ($this->isReadonlyProperty($property)) {
            throw new Exception("Property `{$property}` is readonly");
        }

        if (method_exists($this, 'set'.Str::studly($property).'Property')) {
            $value = $this->{'set'.Str::studly($property).'Property'}($value);
        }

        $this->{$property} = $value;

        return $this;
    }

    protected function getMagicProperties()
    {
        return $this->getProperties()
            ->visibility('public', 'protected')
            ->default()
            ->static(false);
    }

    protected function getReadonlyPropeties()
    {
        if (isset($this->readonly) && is_array($this->readonly)) {
            return $this->readonly;
        }

        return [];
    }

    protected function isReadonlyProperty($property)
    {
        return in_array($property, $this->getReadonlyPropeties());
    }

    protected function getHiddenProperties()
    {
        if (isset($this->hidden) && is_array($this->hidden)) {
            return $this->hidden;
        }

        return [];
    }

    protected function isHiddenProperty($property)
    {
        return in_array($property, $this->getHiddenProperties());
    }

    protected function getProperties()
    {
        return ClassProperties::for($this);
    }

    protected function getPropertyValues()
    {
        $properties = $this->getMagicProperties()->except($this->getHiddenProperties())->keys();

        return array_combine(
            $properties,
            array_map(function($property) {
                return $this->getProperty($property);
            }, $properties)
        );
    }

    protected function matchMagicProperty($property)
    {
        if (Str::startsWith($property, ['get', 'set'])) {
            $property = lcfirst(str_replace(['get', 'set'], '', $property));
        }

        if ($this->hasProperty($property)) {
            return $property;
        }
    }

    public function __call($name, $arguments)
    {
        if ($property = $this->matchMagicProperty($name)) {
            return $this->callMagicProperty($property, $arguments);
        }

        throw new BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()',
            static::class,
            $name
        ));
    }

    public function callMagicProperty($property, $arguments)
    {
        switch (count($arguments)) {
            case 0:
                return $this->getProperty($property);

            case 1:
                return $this->setProperty($property, $arguments[0]);

            default:
                return $this->setProperty($property, $arguments);
        }
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
