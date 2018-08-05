<?php

use App\ChatReply;
use Faker\Generator as Faker;

$factory->define(App\ChatReply::class, function (Faker $faker) {
    return [
        ChatReply::FIELD_TEXT => $faker->text,
    ];
});
