<?php

namespace WebHappens\MagicProperties\Tests\Stubs;

use WebHappens\MagicProperties\MagicProperties;

class Bus extends Vehicle
{
    protected $driver;
    protected $route;
    protected $callsign;

    protected function setRouteProperty($route)
    {
        if (is_array($route)) {
            return new Route($route[0], $route[1]);
        }

        return new Route($route);
    }

    protected function getCallsignProperty()
    {
        if (isset($this->route)) {
            return "{$this->route->number} - {$this->route->destination}";
        }
    }

    public function toArray()
    {
        return $this->getPropertyValues();
    }
}
