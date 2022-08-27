<?php

namespace Lennan\Fuiou\Test;

use GuzzleHttp\Exception\GuzzleException;
use Lennan\Foiou\Sdk\Core\Exceptions\InvalidArgumentException;
use Lennan\Fuiou\Sdk\Aggregate\Order;
use Lennan\Fuiou\Sdk\Application;
use PHPUnit\Framework\TestCase;

class FuiouTest extends TestCase
{


    /**
     * @return string[]
     */
    public function testAggregate(): array
    {
        $config = [
            'notify_url' => '',
            'mchnt_cd' => '0002900F0370542',
            'ins_cd' => '08A9999999',
            'app_key' => '',
            'secret' => 'MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAJgAzD8fEvBHQTyxUEeK963mjziMWG7nxpi+pDMdtWiakc6xVhhbaipLaHo4wVI92A2wr3ptGQ1/YsASEHm3m2wGOpT2vrb2Ln/S7lz1ShjTKaT8U6rKgCdpQNHUuLhBQlpJer2mcYEzG/nGzcyalOCgXC/6CySiJCWJmPyR45bJAgMBAAECgYBHFfBvAKBBwIEQ2jeaDbKBIFcQcgoVa81jt5xgz178WXUg/awu3emLeBKXPh2i0YtN87hM/+J8fnt3KbuMwMItCsTD72XFXLM4FgzJ4555CUCXBf5/tcKpS2xT8qV8QDr8oLKA18sQxWp8BMPrNp0epmwun/gwgxoyQrJUB5YgZQJBAOiVXHiTnc3KwvIkdOEPmlfePFnkD4zzcv2UwTlHWgCyM/L8SCAFclXmSiJfKSZZS7o0kIeJJ6xe3Mf4/HSlhdMCQQCnTow+TnlEhDTPtWa+TUgzOys83Q/VLikqKmDzkWJ7I12+WX6AbxxEHLD+THn0JGrlvzTEIZyCe0sjQy4LzQNzAkEAr2SjfVJkuGJlrNENSwPHMugmvusbRwH3/38ET7udBdVdE6poga1Z0al+0njMwVypnNwy+eLWhkhrWmpLh3OjfQJAI3BV8JS6xzKh5SVtn/3Kv19XJ0tEIUnn2lCjvLQdAixZnQpj61ydxie1rggRBQ/5vLSlvq3H8zOelNeUF1fT1QJADNo+tkHVXLY9H2kdWFoYTvuLexHAgrsnHxONOlSA5hcVLd1B3p9utOt3QeDf6x2i1lqhTH2w8gzjvsnx13tWqg=='
        ];

        $data['goods_des']="描述";
        $data['order_type']="WECHAT";
        $data['order_amt']="2000";
        $data['notify_url']="http://test.modernmasters.com/index.php/Supplier/User/myResources.html";
        $data['addn_inf']="";
        $data['curr_type']="CNY";
        $data['term_id']="";
        $data['goods_detail']="";
        $data['goods_tag']="";
        $data['txn_begin_ts']=date('YmdHis',time());
        $order = $data;

        $app = new Application($config);

        try {
            $res = $app->aggregate->prepare($order);
            print_r($res);
        } catch (GuzzleException|InvalidArgumentException $e) {
//            $file  = 'log.txt';
//            // 这个函数支持版本(PHP 5)
//            file_put_contents($file, $e->getMessage(),FILE_APPEND);
            echo $e->getMessage();
        }
//        print_r($res);


        $this->assertNotEmpty($config);
        return $config;
    }

//    public function testEmpty(): array
//    {
//        $stack = [];
////        $this->assertEmpty($stack);
//
//        return $stack;
//    }
//
//    /**
//     * @depends testEmpty
//     */
//    public function testPush(array $stack): array
//    {
//        print_r($stack);
//        array_push($stack, 'foo');
//        $this->assertSame('foo', $stack[count($stack)-1]);
//        $this->assertNotEmpty($stack);
//
//        return $stack;
//    }
//
//    /**
//     * @return string
//     */
//    public function testGetName() :string
//    {
//        $string = "tsingchan";
//        $this->assertStringStartsWith("tsing", $string);
//        echo 111;
//        return $string;
//    }
//
//    /**
//     * @depends testPush
//     * @depends testGetName
//     */
//    public function testPop(array $stack,string $name): void
//    {
//        $stack[]=$name;
//        $this->assertSame('tsingchan', array_pop($stack));
//        $this->assertSame('foo', array_pop($stack));
//        $this->assertEmpty($stack);
////        $this->assertNotEmpty($stack);
//    }
}