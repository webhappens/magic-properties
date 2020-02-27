<?php

namespace WebHappens\MagicProperties\Tests\Stubs;

use WebHappens\MagicProperties\MagicProperties;

class Route
{
    use MagicProperties;

    protected $number;
    protected $destination;

    protected $readonly = ['number'];

    public function __construct($number, $destination = null)
    {
        $this->number = $number;
        $this->destination = $destination;
    }
}
