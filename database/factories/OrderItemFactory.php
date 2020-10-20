<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\OrderItem;
use Faker\Generator as Faker;

$factory->define( OrderItem::class, function ( Faker $faker ) {

    //随机选取商品
    $product_sku = \App\Models\ProductSku::query()->inRandomOrder()->first();
    //sku对应的商品
    $product = $product_sku->product;

    return [
        //
        'amount'         => random_int( 1, 5 ), // 购买数量随机 1 - 5 份
        'price'          => $product_sku->price,
        'rating'         => null,
        'review'         => null,
        'reviewed_at'    => null,
        'product_id'     => $product->id,
        'product_sku_id' => $product_sku->id,
    ];
} );
