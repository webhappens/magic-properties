<?php

namespace WebHappens\MagicProperties\Tests;

use Exception;
use WebHappens\MagicProperties\Tests\Stubs\Bus;
use WebHappens\MagicProperties\Tests\Stubs\Route;

class MagicPropertiesTest extends TestCase
{
    /** @test */
    public function default_property_values_are_preserved()
    {
        $bus = new Bus;

        $this->assertEquals(1, $bus->capacity);
        $this->assertIsInt($bus->capacity);
        $this->assertEquals(1, $bus->capacity());
        $this->assertIsInt($bus->capacity());
        $this->assertEquals(1, $bus->getCapacity());
        $this->assertIsInt($bus->getCapacity());
    }

    /** @test */
    public function can_set_and_get_a_value()
    {
        $bus = new Bus;

        $this->assertNull($bus->driver);
        $this->assertNull($bus->driver());

        $bus->driver = 'Bill';
        $this->assertEquals('Bill', $bus->driver);
        $this->assertEquals('Bill', $bus->driver());
        $this->assertEquals('Bill', $bus->getDriver());

        $bus->driver('Ben');
        $this->assertEquals('Ben', $bus->driver);
        $this->assertEquals('Ben', $bus->driver());
        $this->assertEquals('Ben', $bus->getDriver());

        $bus->setDriver('Bob');
        $this->assertEquals('Bob', $bus->driver);
        $this->assertEquals('Bob', $bus->driver());
        $this->assertEquals('Bob', $bus->getDriver());
    }

    /** @test */
    public function set_mutatator_is_called()
    {
        $bus = new Bus;

        $this->assertNull($bus->route);
        $this->assertNull($bus->route());

        $bus->route = 'X5';
        $this->assertInstanceOf(Route::class, $bus->route);
        $this->assertEquals('X5', $bus->route->number);
    }

    /** @test */
    public function get_mutatator_is_called()
    {
        $bus = new Bus;

        $this->assertNull($bus->callsign);
        $this->assertNull($bus->callsign());
        $this->assertNull($bus->getCallsign());

        $bus->route = 'X5';
        $bus->route->destination = 'Cambridge';

        $this->assertEquals('X5 - Cambridge', $bus->callsign);
        $this->assertEquals('X5 - Cambridge', $bus->callsign());
        $this->assertEquals('X5 - Cambridge', $bus->getCallsign());

        $this->assertEquals('Unknown', $bus->model);
        $this->assertEquals('Unknown', $bus->model());
        $this->assertEquals('Unknown', $bus->getModel());
    }

    /** @test */
    public function readonly_errors_on_set()
    {
        $route = new Route('X5', 'Oxford');

        $this->assertEquals('X5', $route->number);
        $this->assertEquals('Oxford', $route->destination);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Property `number` is readonly');
        $route->number = '405';
        $this->assertEquals('X5', $route->number);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Property `number` is readonly');
        $route->number('405');
        $this->assertEquals('X5', $route->number);
    }

    /** @test */
    public function returns_array_of_property_values()
    {
        $bus = (new Bus)
            ->make('General Motors')
            ->model('TDH-5303')
            ->driver('Sandra Bullock')
            ->route('33', 'Downtown')
            ->capacity(43);

        $this->assertEquals([
            'capacity' => 43,
            'driver' => 'Sandra Bullock',
            'route' => new Route('33', 'Downtown'),
            'callsign' => '33 - Downtown',
            'make' => 'General Motors',
            'model' => 'TDH-5303',
        ], $bus->toArray());
    }
}
