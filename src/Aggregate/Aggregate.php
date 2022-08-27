<?php

namespace Lennan\Fuiou\Sdk\Aggregate;

use GuzzleHttp\Exception\GuzzleException;
use Lennan\Foiou\Sdk\Core\Exceptions\InvalidArgumentException;
use Lennan\Fuiou\Sdk\Api;
use Lennan\Fuiou\Sdk\Config;
use Lennan\Fuiou\Sdk\Core\Collection;
use Psr\Http\Message\ResponseInterface;

class Aggregate
{

    /**
     * 聚合支付 统一下单接口
     */
    const AGGREGATE = '/aggregatePay/wxPreCreate';

    /**
     * @var Api
     */
    protected $api;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->api = new Api($config);
    }

    /**
     * @param array $order
     * @return ResponseInterface
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function pay($order)
    {
        $order['txn_begin_ts'] = date('YmdHis',time());
        $order = new Order($order);
        return $this->api->request(self::AGGREGATE, $order->all());
    }
}