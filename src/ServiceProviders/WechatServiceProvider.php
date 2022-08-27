<?php

namespace Lennan\Fuiou\Sdk\ServiceProviders;

use Lennan\Fuiou\Sdk\Wechat\Wechat;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class WechatServiceProvider implements ServiceProviderInterface
{

    public function register(Container $pimple)
    {
        $pimple['wechat'] = function ($pimple) {
            return new Wechat($pimple['config']);
        };
    }
}