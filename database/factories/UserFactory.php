<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Group;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'first_name'        => $faker->firstName,
        'last_name'         => $faker->lastName,
        'email'             => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token'    => Str::random(100),
        'added_by'          => User::first() ? User::first()->id : 1,
        'managed_by'        => Group::first() ? Group::first()->id : 1,
        'default_logo'      => null,
        'super_admin'       => false,
        'lang'              => $faker->randomElement([
            User::LANG_EN,
            User::LANG_DE,
//            User::LANG_FR
        ]),
    ];
});

$factory->state(User::class, 'root', [
    'first_name'  => 'Root',
    'last_name'   => 'User',
    'email'       => 'user@root.login',
    'super_admin' => true,
    'lang'        => User::LANG_EN
]);

$factory->state(User::class, 'superAdmin', [
    'first_name'  => 'Super',
    'last_name'   => 'Admin',
    'email'       => 'superadmin@user.login',
    'super_admin' => true,
]);

$factory->state(User::class, 'country', [
    'first_name'  => 'Country',
    'last_name'   => 'Admin',
    'email'       => 'countryadmin@user.login',
]);

$factory->state(User::class, 'canton', [
    'first_name'  => 'Canton',
    'last_name'   => 'Admin',
    'email'       => 'cantonadmin@user.login',
]);

$factory->state(User::class, 'local', [
    'first_name'  => 'Local',
    'last_name'   => 'User',
    'email'       => 'localuser@user.login',
]);
