<?php

namespace Lennan\Fuiou\Sdk;


use Lennan\Fuiou\Sdk\Aggregate\Order;
use Lennan\Fuiou\Sdk\Core\Collection;
use Lennan\Fuiou\Sdk\Core\Exceptions\HttpException;
use Lennan\Fuiou\Sdk\Core\Http;
use Lennan\Fuiou\Sdk\Core\XML;
use Psr\Http\Message\ResponseInterface;
use \GuzzleHttp\Client as HttpClient;


/**
 *
 */
class Api
{
    /**
     * API版本
     */
    const API_VERSION = '1';

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
     * API 请求
     * @param $api
     * @param array $params
     * @param string $method
     * @param array $options
     * @param bool $returnResponse
     * @return Collection|ResponseInterface
     * @throws HttpException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($api, array $params, string $method = 'post', array $options = [], bool $returnResponse = false)
    {
        // 加载配置数据
        $params = array_merge($params, $this->config->only(['mchnt_cd', 'ins_cd']));
        // 版本号
        $params['version'] = self::API_VERSION;
        // 随机字符串
        $params['random_str'] = uniqid();
        // 终端号(没有真实终端号统一填88888888)
        $params['term_id'] = $params['term_id'] ?? self::TERM_ID;
        // ip
        $params['term_ip'] = get_client_ip();
        // 生成签名
        $params['sign'] = generate_sign($params, $this->getPrivateKey(), 'openssl');
        $params = array_filter($params);
        // 生成XML格式
        $xml = XML::build($params, self::XML_ROOT);
        // 生成富又需要的BODY格式
        $body = ['body' => json_encode(['req' => urldecode(urldecode($xml))], JSON_UNESCAPED_UNICODE)];

        // 合并options
        $options = array_merge($options, $body);
        // 请求
        $response = $this->getHttp()->request($this->wrapApi($api), $method, $options);

        if($response->getStatusCode() !== 200){
            throw new HttpException('[富友支付异常]请求异常: HTTP状态码 '.$response->getStatusCode());
        }

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
     * @return mixed
     */
    public function getPrivateKey()
    {
        return get_private_key($this->config['secret']);
    }

    /**
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
}