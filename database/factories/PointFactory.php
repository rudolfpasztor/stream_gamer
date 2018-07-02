<?php

use Faker\Generator as Faker;

$factory->define(App\Point::class, function (Faker $faker) {
    return [
        'end_user_id'   => $faker->numberBetween($min = 1, $max = 500),
        'campaign_id'   => $faker->randomElement($array = array('1', '1')),
        'source'        => $faker->randomElement($array = array('view', 'event', 'given_by_admin', 'compensation')),
        'count'         => $faker->numberBetween($min = 10, $max = 50),
    ];
});