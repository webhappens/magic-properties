<?php

namespace WebHappens\MagicProperties\Tests\Stubs;

use WebHappens\MagicProperties\MagicProperties;

class Vehicle
{
    use MagicProperties;

    protected static $transmission = [
        'automtic',
        'manual',
    ];

    protected static $fuel = [
        'deisel',
        'petrol',
        'electric',
    ];

    protected $make;
    protected $model;
    protected $capacity = 1;

    protected function getMakeProperty($make)
    {
        return $make ?? 'Unknown';
    }

    protected function getModelProperty($model)
    {
        return $model ?? 'Unknown';
    }
}
