<?php

namespace Lennan\Fuiou\Sdk\Prepare;

use Lennan\Fuiou\Sdk\Api;
use Lennan\Fuiou\Sdk\Core\Collection;
use Lennan\Fuiou\Sdk\Core\Exceptions\FuiouPayException;
use Lennan\Fuiou\Sdk\Core\Exceptions\HttpException;
use Lennan\Fuiou\Sdk\Core\Exceptions\InvalidArgumentException;
use Lennan\Fuiou\Sdk\Prepare\Order;
use function Lennan\Fuiou\Sdk\get_client_ip;

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
     * @param \Lennan\Fuiou\Sdk\Prepare\Order $order
     * @param string $orderType
     * @param $tradeType
     * @return Collection
     * @throws FuiouPayException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pay(Order $order, string $orderType, $tradeType = null): Collection
    {
        $api = $orderType == 'WECHAT' && !empty($tradeType) ? $this->getApi(self::WECHAT_API) : $this->getApi(self::UNITY_API);
        $params = $this->getParams($order->all(), $orderType, $tradeType);
        $options = ['headers' => ['Content-Type' => 'application/json']];

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
     * @param array $params
     * @param string $orderType
     * @param string|null $tradeType
     * @return array
     * @throws InvalidArgumentException
     */
    public function getParams(array $params, string $orderType, string $tradeType = null)
    {
        // 签名前处理
        if (isset($params['order_type'])) unset($params['order_type']);
        if (isset($params['trade_type'])) unset($params['trade_type']);
        // 终端号
        if (!isset($params['term_id']) || empty($params['term_id'])) $params['term_id'] = self::TERM_ID;
        if (!isset($params['term_ip']) || empty($params['term_ip'])) $params['term_ip'] = get_client_ip();
        // 下单时间
        if (!isset($params['txn_begin_ts']) || empty($params['txn_begin_ts'])) $params['txn_begin_ts'] = date('YmdHis', time());

        // 签名参数
        $type = $orderType == 'WECHAT' && !empty($tradeType) ? $tradeType : $orderType;
        $params = array_merge($params, $this->baseParams(), ['notify_url' => $this->config->get('notify_url')]);
        $params['sign'] = $this->generateSign($params, $type);

        // 请求参数
        $orderType == 'WECHAT' && !empty($tradeType) ? $params['trade_type'] = $tradeType : $params['order_type'] = $orderType;

        return $params;
    }

    /**
     * 生成下单签名签
     *
     * @param array $params
     * @return string
     * @throws InvalidArgumentException
     */
    public function generateSign(array $params, string $type)
    {
        $params['type'] = $type;
        $signArray = [
            'mchnt_cd', 'type', 'order_amt', 'mchnt_order_no', 'txn_begin_ts', 'goods_des', 'term_id',
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

    /**
     * @param array $params
     * @return bool
     */
    public function checkSign(array $params)
    {
        $sign = $params['full_sign'];
        unset($params['full_sign'], $params['sign']);
        $params['mchnt_key'] = $this->config->get('mchnt_key');
        return md5(implode('|', $params)) === $sign;
    }
}