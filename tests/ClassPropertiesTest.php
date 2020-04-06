<?php

namespace WebHappens\MagicProperties\Tests;

use WebHappens\MagicProperties\ClassProperties;
use WebHappens\MagicProperties\Tests\Stubs\Bus;

class ClassPropertiesTest extends TestCase
{
    /** @test */
    public function cache_works()
    {
        $this->assertFalse(ClassProperties::hasCache(Bus::class));

        $busProperties = ClassProperties::for(Bus::class);

        $this->assertTrue(ClassProperties::hasCache(Bus::class));
        $this->assertInstanceOf(ClassProperties::class, $busProperties);
        $this->assertSame($busProperties, ClassProperties::for(Bus::class));
    }

    /** @test */
    public function all_properties_are_returned()
    {
        $expected = [
            'driver',
            'route',
            'callsign',
            'transmission',
            'fuel',
            'make',
            'model',
            'capacity',
        ];

        $busProperties = ClassProperties::for(Bus::class);
        $this->assertEquals($expected, $busProperties->keys());

        $busProperties = ClassProperties::for(new Bus);
        $this->assertEquals($expected, $busProperties->keys());
    }

    /** @test */
    public function except_properties()
    {
        $busProperties = ClassProperties::for(Bus::class);

        $expected = [
            'driver',
            'callsign',
            'make',
            'model',
            'capacity',
        ];

        $this->assertEquals($expected, $busProperties->except('route', 'fuel', 'transmission')->keys());
        $this->assertEquals($expected, $busProperties->except(['route', 'fuel', 'transmission'])->keys());
    }

    /** @test */
    public function only_properties()
    {
        $busProperties = ClassProperties::for(Bus::class);

        $this->assertEquals([
            'transmission',
            'fuel',
        ], $busProperties->only('transmission', 'fuel')->keys());

        $this->assertEquals([
            'make',
            'model',
            'capacity',
        ], $busProperties->only(['make', 'model', 'capacity'])->keys());
    }

    /** @test */
    public function static_properties()
    {
        $busProperties = ClassProperties::for(Bus::class);

        $this->assertEquals([
            'transmission',
            'fuel',
        ], $busProperties->static()->keys());
    }
}
