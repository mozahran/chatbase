<?php

use App\ConversationReply;
use Faker\Generator as Faker;

$factory->define(App\ConversationReply::class, function (Faker $faker) {
    return [
        ConversationReply::FIELD_TEXT => $faker->text,
    ];
});
