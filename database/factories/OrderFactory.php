<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Order;
use Faker\Generator as Faker;

$factory->define( Order::class, function ( Faker $faker ) {

    //随机选取用户
    /* Put the query's results in random order.*/
    $user = \App\Models\User::query()->inRandomOrder()->first();
    //随机选该用户的一个地址
    $address = $user->addresses()->inRandomOrder()->first();
    //20%的概率标记用户退款,随机数为0,1值小于2，返回true，
    $refund = random_int( 0, 9 ) < 2;
    //随机发货状态
    $ship_status = $faker->randomElement( array_keys( Order::$shipStatusMap ) );
    //优惠券
    $coupon = null;
    //随机30%使用了优惠券
    if ( random_int( 0, 9 ) < 3 ) {
        $coupon = \App\Models\Coupon::query()->inRandomOrder()->first();
        //增加优惠券使用量
        $coupon->changeUsed( true );
    }

    return [
        'address'        => [
            'address'       => $address->full_address,
            'zipcode'       => $address->zipcode,
            'contact_name'  => $address->contact_name,
            'contact_phone' => $address->contact_phone,
        ],
        'no'             => $faker->uuid,
        'total_amount'   => 0,
        'remark'         => $faker->sentence,
        'paid_at'        => $faker->dateTimeBetween( '-30 days' ), // 30天前到现在任意时间点
        'payment_method' => $faker->randomElement( [ 'wechat', 'alipay' ] ),
        'payment_no'     => $faker->uuid,
        'refund_status'  => $refund ? Order::REFUND_STATUS_SUCCESS : Order::REFUND_STATUS_PENDING,
        'refund_no'      => $refund ? Order::createRefundNo() : null,
        'closed'         => false,
        'reviewed'       => (boolean) random_int( 0, 1 ),
        'ship_status'    => $ship_status,
        'ship_data'      => $ship_status === Order::SHIP_STATUS_PENDING ? null : [
            'express_company' => $faker->company,
            'express_no'      => $faker->uuid,
        ],
        'extra'          => $refund ? [ 'refund_reason' => $faker->sentence ] : [],
        'user_id'        => $user->id,
        'coupon_code_id' => $coupon ? $coupon->id : null,
    ];
} );
