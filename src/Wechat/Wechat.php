<?php

namespace Lennan\Fuiou\Sdk\Wechat;

use Lennan\Fuiou\Sdk\Api;
use Lennan\Fuiou\Sdk\Config;
use Lennan\Fuiou\Sdk\Core\Collection;
use Lennan\Fuiou\Sdk\Core\Exceptions\FuiouPayException;

class Wechat
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
     * @param $order
     * @return Collection
     * @throws FuiouPayException
     * @throws GuzzleException
     */
    public function prepare($order): Collection
    {
        // 生成订单数据
        $order = new Order($order);

        $options = ['headers' => ['Content-Type' => 'application/json']];

        $response = $this->api->request(self::AGGREGATE, $order->all(), 'POST', $options, true);

        $resString = $response->getBody()->getContents();

        $response = new Collection(json_decode($resString, true));

        if ($response->get('result_code') !== 000000) {
            throw new FuiouPayException('[富友支付异常]微信支付异常：异常代码：' . $response->get('result_code') . ' 异常信息：' . $response->get('result_msg'));
        }

        return $response;
    }
}