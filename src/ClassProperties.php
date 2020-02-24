<?php

namespace WebHappens\MagicProperties;

use ReflectionClass;

class ClassProperties
{
    protected static $class;

    public static function for(object $object)
    {
        $class = get_class($object);

        if ( ! isset(static::$class[$class])) {
            foreach ((new ReflectionClass($object))->getProperties() as $property) {
                if ( ! $property->isDefault()) {
                    continue;
                }

                static::$class[$class][$property->getName()] = $property;
            }
        }

        return static::$class[$class];
    }
}
