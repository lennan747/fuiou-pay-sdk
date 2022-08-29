<?php

namespace Lennan\Fuiou\Sdk\ServiceProviders;

use Lennan\Fuiou\Sdk\Prepare\Prepare;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PrepareServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $pimple
     * @return void
     */
    public function register(Container $pimple)
    {
        $pimple['prepare'] = function ($pimple) {
            return new Prepare($pimple['config']);
        };
    }
}