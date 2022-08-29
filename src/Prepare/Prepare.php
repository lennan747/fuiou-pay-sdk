<?php

namespace Lennan\Fuiou\Sdk\Prepare;

use Lennan\Fuiou\Sdk\Api;
use Lennan\Fuiou\Sdk\Core\Collection;
use Lennan\Fuiou\Sdk\Core\Exceptions\FuiouPayException;
use Lennan\Fuiou\Sdk\Core\Exceptions\HttpException;
use Lennan\Fuiou\Sdk\Core\Exceptions\InvalidArgumentException;
use Lennan\Fuiou\Sdk\Unity\Order;

class Prepare extends Api
{
    /**
     * 统一下单接口
     */
    const UNITY_API = '/aggregatePay/preCreate';

    /**
     * 公众号/服务窗统一下单接口
     */
    const WECHAT_API = '/aggregatePay/wxPreCreate';

    /**
     * 统一下单
     * 富友开放接口文档： 自2019年7月23日开始，微信新增商户不再具有主扫下单权限
     *
     * @param Order $order
     * @param string $tradeType
     * @return Collection
     * @throws FuiouPayException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unity(Order $order, string $tradeType)
    {
        $params = $this->getParams($order, $tradeType);
        $api = $this->getApi(self::UNITY_API);
        return $this->prepare($api, $params);
    }

    /**
     * @param Order $order
     * @param string $tradeType
     * @return Collection
     * @throws FuiouPayException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function wechat(Order $order, string $tradeType)
    {
        $params = $this->getParams($order->all(), $tradeType);
        $api = $this->getApi(self::WECHAT_API);
        return $this->prepare($api, $params);
    }

    /**
     * @param string $api
     * @param array $params
     * @return Collection
     * @throws FuiouPayException
     * @throws HttpException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function prepare(string $api, array $params): Collection
    {
        $options = [
            'headers' => [
                ''
            ]
        ];
        $response = $this->request($api, $params, 'POST', $options, true);
        $resString = $response->getBody()->getContents();
        $response = new Collection(json_decode($resString, true));
        if ($response->get('result_code') !== 000000) {
            throw new FuiouPayException('[富友支付异常]聚合支付异常：异常代码：' . $response->get('result_code') . ' 异常信息：' . $response->get('result_msg'));
        }
        return $response;
    }

    /**
     * 生成请求参数
     *
     * @param $params
     * @param $tradeType
     * @return array
     * @throws InvalidArgumentException
     */
    public function getParams($params, $tradeType)
    {
        $params = array_merge($params, ['trade_type' => $tradeType, 'notify_url' => $this->notifyUrl]);
        $params['sign'] = $this->generateSign($params);
        return $params;
    }

    /**
     * 生成下单签名签
     *
     * @param array $params
     * @return string
     * @throws InvalidArgumentException
     */
    public function generateSign(array $params)
    {
        $signArray = [
            'mchnt_cd', 'order_type', 'order_amt', 'mchnt_order_no', 'txn_begin_ts', 'goods_des', 'term_id',
            'term_ip', 'notify_url', 'random_str', 'version', 'mchnt_key'
        ];

        foreach ($signArray as $key => $item) {
            $param = isset($params[$item]) ? $params[$item] : null;
            if (is_null($param) || !$param) {
                throw new InvalidArgumentException('Fuiou sign error ! ' . $item . ' is not null');
            }
            $signArray[$key] = $param;
        }

        return md5(implode('|', $signArray));
    }
}