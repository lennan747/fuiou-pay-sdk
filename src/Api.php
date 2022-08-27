<?php

namespace Lennan\Fuiou\Sdk;

use Lennan\Foiou\Sdk\Core\Exceptions\InvalidArgumentException;
use Lennan\Fuiou\Sdk\Aggregate\Order;
use Lennan\Fuiou\Sdk\Core\Collection;
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
        // 版本号
        $params['version'] = self::API_VERSION;
        // 随机字符串
        $params['random_str'] = uniqid();
        // 过滤字符串
        $params = array_filter($params);
        // 终端号(没有真实终端号统一填88888888)
        $params['term_id'] = $params['term_id'] ?? self::TERM_ID;
        // ip
        $params['term_ip'] = get_client_ip();
        // 生成签名
        $params['sign'] = generate_sign($params, $this->getPrivateKey(), 'openssl');
        // 生成XML格式
        $xml = XML::build($params, self::XML_ROOT);
        // 生成富友支付需要的格式
        $params = ['req' => '%253C%253Fxml%2Bversion%253D%25221.0%2522%2Bencoding%253D%2522GBK%2522%2Bstandalone%253D%2522yes%2522%253F%253E%253Cxml%253E%253Corder_type%253EWECHAT%253C%252Forder_type%253E%253Corder_amt%253E0.01%253C%252Forder_amt%253E%253Cmchnt_order_no%253E1066152854755155%253C%252Fmchnt_order_no%253E%253Cgoods_des%253E%25E6%258F%258F%25E8%25BF%25B0%253C%252Fgoods_des%253E%253Creserved_expire_minute%253E120%253C%252Freserved_expire_minute%253E%253Ctxn_begin_ts%253E20220827150051%253C%252Ftxn_begin_ts%253E%253Cmchnt_cd%253E0002900F1503036%253C%252Fmchnt_cd%253E%253Cins_cd%253E08A9999999%253C%252Fins_cd%253E%253Cversion%253E1%253C%252Fversion%253E%253Crandom_str%253E6309c1232a002%253C%252Frandom_str%253E%253Cterm_id%253E88888888%253C%252Fterm_id%253E%253Cterm_ip%253E127.0.1.1%253C%252Fterm_ip%253E%253Csign%253EWPyKbOWKZ0Fxx5spkjrZQ8Ph75C1dwjuiqvieDSe8eDbHoaLtkhpQpqhnJRfTXneDQsy5cGAs%252FR473415Kj21hHLR2dEhwh0nV8rTD%252FHDlcoEkoVvVZyqvMEuS8mTToxXqdIJC2HbOfUz35pggknsf%252BN9NYizHDGzpeSBuYLIRs%253D%253C%252Fsign%253E%253Cxml%253E'];
        is_array($params) && $params = json_encode($params, JSON_UNESCAPED_UNICODE);
        // 拼接API
        $api = $this->wrapApi($api);
        $response = $this->getHttp()->request('POST', $api,  ['body' => $params, 'headers' => ["content-type" => "application/json"]]);
        print_r($response->getBody()->getContents());
        exit();
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
     * @return \GuzzleHttp\Client
     */
    public function getHttp()
    {
//        if (is_null($this->http)) {
//            $this->http = new \GuzzleHttp\Client([
//                'connect_timeout' => 5,
//                'timeout' => 5
//            ]);
//        }

        return $this->http = new HttpClient([
            'connect_timeout' => 5,
            'timeout' => 5
        ]);
    }

    /**
     * 获取返回结果
     *
     * @param $response
     * @return Collection
     */
    protected function parseResponse($response): Collection
    {
        echo '----------------';
        print_r($response);
        exit();

        if ($response instanceof ResponseInterface) {
            $response = $response->getBody();
        }

        return new Collection((array)XML::parse($response));
    }

//    /**
//     * @return \string[][]
//     */
//    protected function getOptions($options): array
//    {
//        return array_merge(['headers' => ['content-type' => 'application/json']], $options);
//    }
}