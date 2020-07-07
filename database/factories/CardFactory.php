<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Card;
use Faker\Generator as Faker;

$factory->define(Card::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'user_id' => factory(App\Models\User::class),
        'interpreter_type' => 'imageviewer',
        'interpreter_id' => '3',
    ];
});
