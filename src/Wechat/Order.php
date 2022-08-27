<?php

namespace Lennan\Fuiou\Sdk\Wechat;

use Lennan\Fuiou\Sdk\Core\Attribute;

class Order extends Attribute
{
    protected $attributes = [
        'order_type',
        'order_amt',
        'mchnt_order_no',
        'txn_begin_ts',
        'goods_des',
        'goods_detail',
        'goods_tag',
        'term_id',
        'term_ip',
        'addn_inf',
        'curr_type',
        'reserved_expire_minute',
        'reserved_user_creid',
        'reserved_user_truename',
        'openid',
        'sub_openid',
        'sub_appid'
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