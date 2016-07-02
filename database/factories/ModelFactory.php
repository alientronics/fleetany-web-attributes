<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(Alientronics\FleetanyWebAttributes\Entities\Key::class, function ($faker) {
    return [
        'entity_key' => 'vehicle',
        'description' => 'Description',
        'type' => 'string',
        'company_id' => 1,
    ];
});

$factory->define(Alientronics\FleetanyWebAttributes\Entities\Value::class, function ($faker) {
    return [
        'entity_key' => 'vehicle',
        'entity_id' => str_random(10),
        'attribute_id' => str_random(10),
        'value' => 1,
    ];
});
        