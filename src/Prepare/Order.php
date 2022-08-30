<?php

namespace Lennan\Fuiou\Sdk\Prepare;

use Lennan\Fuiou\Sdk\Core\Attribute;

/**
 * @property string $order_type 订单类型 ALIPAY,WECHAT,WXXS,WXBX,ALBX,UNIONPAY 必填
 * @property string $order_amt 订单总金额 以分为单位 必填
 * @property string $mchnt_order_no 商户订单号 商户系统内部的订单号 必填
 * @property string $txn_begin_ts 交易起始时间 订单生成时间格式：yyyyMMddHHmmss 必填
 * @property string $goods_des 商品描述 商品或支付单简要描述 必填
 * @property string $term_id 终端号 随机8字节数字字母组合 必填
 * @property string $term_ip 终端IP 终端IP 必填
 */
class Order extends Attribute
{

    protected $attributes = [
        'order_amt',
        'mchnt_order_no',
        'goods_des',
        'goods_detail',
        'goods_tag',
        'txn_begin_ts',
        'addn_inf',
        'curr_type',
        'openid',
        'sub_openid'
    ];

    /**
     * Constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        parent::__construct($attributes);
    }
}