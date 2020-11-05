<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Product;
use Faker\Generator as Faker;

$factory->define( Product::class, function ( Faker $faker ) {
    $image = $faker->randomElement( [
        'images/timg.jpg',
        'images/timg (1).jpg',
        'images/timg (2).jpg',
        'images/timg (3).jpg',
        'images/timg (4).jpg',
        'images/timg (5).jpg',
        'images/timg (6).jpg',
        'images/timg (7).jpg',
        'images/u=1965453035,893720880&fm=26&gp=0.jpg',
        'images/u=3192481053,1467100590&fm=26&gp=0.jpg',
        'images/u=3807224309,3405548595&fm=26&gp=0.jpg',
        'images/u=3924847948,2683293372&fm=26&gp=0.jpg'
    ] );

    return [
        'title'        => $faker->word,
        'description'  => $faker->sentence,
        'image'        => $image,
        'on_sale'      => true,
        'rating'       => $faker->numberBetween( 0, 5 ),
        'sold_count'   => 0,
        'review_count' => 0,
        'price'        => 0,
    ];
} );
