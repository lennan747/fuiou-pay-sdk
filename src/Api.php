<?php

namespace Lennan\Fuiou\Sdk;

use Lennan\Foiou\Sdk\Core\Exceptions\InvalidArgumentException;
use Lennan\Fuiou\Sdk\Aggregate\Order;
use Lennan\Fuiou\Sdk\Core\Collection;
use Lennan\Fuiou\Sdk\Core\Http;
use Lennan\Fuiou\Sdk\Core\XML;
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
     * 终端号(没有真实终端号统一填88888888)
     */
    const TERM_ID = '88888888';
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
     * XML头部
     */
    const XML_ROOT = '?xml version="1.0" encoding="GBK" standalone="yes"?';


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
     * @param $api
     * @param array $params
     * @param string $method
     * @param array $options
     * @param bool $returnResponse
     * @return Collection|ResponseInterface
     * @throws InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($api, array $params, string $method = 'post', array $options = [], bool $returnResponse = false)
    {
        // 加载配置数据
        $params = array_merge($params, $this->config->only(['mchnt_cd', 'ins_cd']));
        // 终端号(没有真实终端号统一填88888888)
        $params['term_id'] = $params['term_id'] ?? self::TERM_ID;
        // 版本号
        $params['version'] = self::VERSION;
        // 随机字符串
        $params['random_str'] = uniqid();
        // 过滤字符串
        $params = array_filter($params);
        // 生成签名
        $params['sign'] = generate_sign($params, $this->getPrivateKey(), 'openssl');
        // 生成XML格式
        $xml = XML::build($params, self::XML_ROOT);
        // 生成富友支付需要的格式
        $params = ['req' => urlencode(urlencode($xml))];
        // 合并option
//        $options = ['query' => [],'body' => $params,'headers' => ['content-type' => 'application/json']];
        // 拼接API
        $api = $this->wrapApi($api);

        $response = $this->getHttp()->json($api, $params);

        return $returnResponse ? $response : $this->parseResponse($response);
    }

    /**
     * @param string $api
     * @return string
     */
    public function wrapApi(string $api): string
    {
        return self::DEV_API_HOST . $api;
    }

    /**
     * 获取私钥
     *
     * @return resource
     * @throws InvalidArgumentException
     */
    public function getPrivateKey()
    {
        return get_private_key($this->config['secret']);
    }

    /**
     * 获取请求客户端
     *
     * @return Http
     */
    public function getHttp(): Http
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
    protected function parseResponse($response): Collection
    {

        if ($response instanceof ResponseInterface) {
            $response = $response->getBody();
        }

        return new Collection((array)XML::parse($response));
    }

    /**
     * @return \string[][]
     */
    protected function getOptions($options): array
    {
        return array_merge(['headers' => ['content-type' => 'application/json']], $options);
    }
}