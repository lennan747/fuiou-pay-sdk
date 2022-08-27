<?php

namespace Lennan\Fuiou\Sdk\ServiceProviders;

use Lennan\Fuiou\Sdk\Aggregate\Aggregate;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class AggregateServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $pimple
     * @return void
     */
    public function register(Container $pimple)
    {
        $pimple['aggregate'] = function ($pimple) {
            return new Aggregate($pimple['config']);
        };
    }
}