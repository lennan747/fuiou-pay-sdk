<?php

namespace Lennan\Fuiou\Sdk\Aggregate;

use Lennan\Fuiou\Sdk\Config;

class Aggregate
{

    public function __construct(Config $config)
    {

    }

    public function pay()
    {
        echo 'success';
    }
}