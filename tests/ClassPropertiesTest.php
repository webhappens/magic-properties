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
}
