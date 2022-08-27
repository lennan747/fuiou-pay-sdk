<?php

namespace Lennan\Fuiou\Sdk;

use Lennan\Fuiou\Sdk\Aggregate\Order;
use Psr\Http\Message\ResponseInterface;

/**
 *
 */
class Api
{
    /**
     * API版本
     */
    const VERSION = '1.0';
    /**
     * 正式地址
     */
    const PRO_API_HOST = 'https://aipay.fuioupay.com';

    /**
     * 正式地址
     */
    const PRO_API_HOST_XS = 'https://aipay-xs.fuioupay.com';

    /**
     * 测试地址
     */
    const DEV_API_HOST = 'https://aipaytest.fuioupay.com';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var
     */
    protected $http;

    /**
     * 载入商户配置
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * 请求
     *
     * @param $api
     * @param array $params
     * @param $method
     * @param array $options
     * @param $returnResponse
     * @return \EasyWeChat\Support\Collection|Collection|mixed
     */
    public function request($api, array $params, $method = 'post', array $options = [], $returnResponse = false)
    {
        $params = array_merge($params, $this->config->only(['mchnt_cd']));

        $params = array_filter($params);

        $params['sign'] = generate_sign();

        $options = array_merge(['body' => XML::build($params)], $options);

        return $returnResponse ? $response : $this->parseResponse($response);
    }

    /**
     * 获取请求客户端
     *
     * @return Http
     */
    public function getHttp()
    {
        if (is_null($this->http)) {
            $this->http = new Http();
        }

        return $this->http;
    }

    /**
     * 获取返回结果
     *
     * @param $response
     * @return Collection
     */
    protected function parseResponse($response)
    {
        if ($response instanceof ResponseInterface) {
            $response = $response->getBody();
        }

        return new Collection((array)XML::parse($response));
    }
}