<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
   $image = $faker->randomElement([
       'images/501c6886b715dac898a08c99445013f8.png',
       'images/2020-5-9-21-20-43-772.jpg',
       'images/I54W%{8DUESHL@24QK}$SB4.png'
   ]);

    return [
        'title'        => $faker->word,
        'description'  => $faker->sentence,
        'image'        => $image,
        'on_sale'      => true,
        'rating'       => $faker->numberBetween(0, 5),
        'sold_count'   => 0,
        'review_count' => 0,
        'price'        => 0,
    ];

});
