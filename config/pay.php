<?php

return [
    'alipay' => [
        'app_id'         => env( 'ALIPAY_APPID' ), //APPID
        'ali_public_key' => env( 'ALIPAY_PUBLIC_KEY' ), //支付宝公钥,不是应用公钥
        'private_key'    => env( 'AliPAY_APPLICATION_PRIVATE_KEY' ), //应用私钥
        'notify_url'     => env( 'APP_URL' ) . '/payment/alipay/notify',//支付宝通知回调地址
        'return_url'     => env( 'APP_URL' ) . '/payment/alipay/return',//前端回调地址
        'log'            => [
            'file' => storage_path( 'logs/alipay.log' ),
        ],
    ],
    'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path( 'logs/wechat_pay.log' ),
        ],
    ],
];
