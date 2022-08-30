<?php

namespace Lennan\Fuiou\Sdk;


use Lennan\Fuiou\Sdk\Core\Collection;
use Lennan\Fuiou\Sdk\Core\Exceptions\HttpException;
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
    const API_VERSION = '1';

    /**
     * 终端号(没有真实终端号统一填88888888)
     */
    const TERM_ID = '88888888';

    /**
     * XML头部
     */
    const XML_ROOT = '?xml version="1.0" encoding="GBK" standalone="yes"?';

    /**
     * 测试环境API地址
     */
    const API_HOST_DEV = 'https://aipaytest.fuioupay.com';

    /**
     * 正式环境API地址
     */
    const API_HOST_PRO = 'https://aipay.fuioupay.com';

    /**
     * 正式环境API地址
     */
    const API_HOST_PRO_XS = 'https://aipay-xs.fuioupay.com';

    /**
     * @var bool
     */
    public $debug = false;

    /**
     * @var string
     */
    protected $notifyUrl;


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
        if ($config->get('environment') === 'dev') {
            $this->debug = true;
        }
        $this->config = $config;
    }

    /**
     * API 请求
     *
     * @param string $api
     * @param array $params
     * @param string $method
     * @param array $options
     * @param bool $returnResponse
     * @return Collection|ResponseInterface
     * @throws HttpException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(string $api, array $params, string $method = 'post', array $options = [], bool $returnResponse = false)
    {
        // 生成XML格式
        // $xml = XML::build($params, self::XML_ROOT);
        // 生成富又需要的BODY格式
        $body = ['body' => json_encode($params, JSON_UNESCAPED_UNICODE)];
        // 合并options
        $options = array_merge($options, $body);
        $response = $this->getHttp()->request($api, $method, $options);
        if ($response->getStatusCode() !== 200) {
            throw new HttpException('[富友支付异常]请求异常: HTTP状态码 ' . $response->getStatusCode());
        }
        return $returnResponse ? $response : $this->parseResponse($response);
    }

    /**
     * @return array 解除参数
     */
    public function baseParams(): array
    {
        // 加载配置数据
        return array_merge(
            $this->config->only(['mchnt_cd', 'ins_cd', 'mchnt_key']),
            ['version' => self::API_VERSION, 'random_str' => uniqid()]
        );
    }

    /**
     * 请求客户端
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
     * 获取API地址
     *
     * @param string $api
     * @param bool $isXs
     * @return string
     */
    public function getApi(string $api, bool $isXs = false): string
    {
        if ($this->debug) {
            return self::API_HOST_DEV . $api;
        } else {
            return $isXs ? self::API_HOST_PRO . $api : self::API_HOST_PRO_XS . $api;
        }
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