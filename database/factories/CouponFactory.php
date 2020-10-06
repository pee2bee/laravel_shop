<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Coupon;
use Faker\Generator as Faker;

$factory->define( Coupon::class, function ( Faker $faker ) {

    //随机一个类型
    $type = $faker->randomElement( array_keys( Coupon::$typeMap ) );
    //根据类型生成随机折扣
    $value = $type === Coupon::TYPE_FIXED ? random_int( 1, 400 ) : random_int( 1, 50 );

    //如果是固定金额的优惠，则订单最低金额要大于固定金额，不然就成0元购了
    if ( $type === Coupon::TYPE_FIXED ) {
        $min_amount = $value + 20;
    } else {
        //百分比折扣，最低使用金额随便取就行
        $min_amount = random_int( 10, 50 );
    }

    return [
        //
        'name'       => join( '', $faker->words ),//数组拼接字符串
        'code'       => Coupon::createCouponCode(),
        'type'       => $type,
        'value'      => $value,
        'total'      => 100,
        'used'       => random_int( 1, 50 ),
        'min_amount' => $min_amount,
        'not_before' => Date( 'Y-m-d H:i:s', strtotime( 'yesterday' ) ),
        'not_after'  => Date( 'Y-m-d H:i:s', strtotime( 'next year' ) ),
        'enabled'    => true
    ];
} );
