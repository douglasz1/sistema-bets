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

$factory->define(Bets\Models\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'username' => $faker->unique()->userName,
        'balance' => $faker->numberBetween(1, 1500),
        'tips_min' => $faker->numberBetween(1, 3),
        'commission1' => $faker->numberBetween(1, 10),
        'commission2' => $faker->numberBetween(1, 10),
        'commission3' => $faker->numberBetween(1, 10),
        'password' => $password ?: $password = bcrypt('123456'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(Bets\Models\League::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->country,
    ];
});

$factory->define(Bets\Models\Role::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'label' => $faker->name,
    ];
});

$factory->define(Bets\Models\Permission::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'label' => $faker->name,
    ];
});

$factory->define(Bets\Models\Company::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->company
    ];
});

$factory->define(Bets\Models\QuotationCategory::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'label' => $faker->name,
        'order' => 100
    ];
});