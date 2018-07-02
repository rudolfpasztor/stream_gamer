<?php

use Faker\Generator as Faker;

$factory->define(App\EndUser::class, function (Faker $faker) {
    return [
        'nick'              => $faker->userName,
        'twitch_id'         => $faker->unique()->ean8,
        'email'             => preg_replace('/@example\..*/', $faker->unique()->ean8.'@ciri.com', $faker->unique()->safeEmail),
        'avatar_url'        => '',
        'api_token'         => bin2hex(openssl_random_pseudo_bytes(30)),
        'foreign_user_id'   => '',
    ];
});
