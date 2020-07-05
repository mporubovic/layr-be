<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Card;
use Faker\Generator as Faker;

$factory->define(Card::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'user_id' => factory(App\User::class),
        'content_count' => '1',
        'content_type' => 'image',
        'content_id' => $faker->randomDigit,
        'interpreter_type' => 'imageviewer',
        'interpreter_id' => '3',
    ];
});
