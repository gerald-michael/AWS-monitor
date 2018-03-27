<?php

use Faker\Generator as Faker;
use station\User;
use station\Node;
use station\Station;
use station\Sensor;
use station\NodeStatus;
use station\NodeStatusConfiguration;
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
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

 $factory->define(Station::class, function (Faker $faker) {
     
     return [
         
         'station_name' => $faker->company,
         'station_location' => $faker->streetName,
         'longitude' => $faker->longitude,
         'latitude' => $faker->latitude,
         'station_number' => $faker->unique()->numberBetween($min = 0, $max = 2147483647),
         'location' => $faker->streetName,
         'city' => $faker->city,
         'region'=>$faker->address,
         'code' => $faker->postcode,
         'date_opened' => new DateTime,
         'date_closed' => new DateTime
     ];
         
 });

 
 $factory->define(Node::class, function (Faker $faker) {
    
    return [
        
        'txt_key' =>$faker->randomElement(['mak-2m', 'mak-gnd',"mak-10m"]),
        'mac_address' => $faker->unique()->macAddress
    ];
});

$factory->define(Sensor::class, function (Faker $faker) {

    return [
        
        'parameter_read' => $faker->unique()->name,
        'identifier_used' => $faker->unique()->name,
        'min_value' => $faker->numberBetween($min = 0, $max = 10),
        'max_value' => $faker->numberBetween($min = 10, $max = 20),
        'report_key_title' => $faker->name,
        'report_key_value' => $faker->name,
        'report_time_interval' => $faker->numberBetween($min = 30, $max = 60)
    ];
});



$factory->define(NodeStatus::class, function (Faker $faker) {

    return [
        
        'v_in' => $faker->numberBetween($min = 0, $max = 10),
        'rssi' => $faker->finance->amount(2,20,2),
        'drop' => $faker->finance->amount(2,20,2),
        'vmcu' => $faker->finance->amount(2,20,2),
        'lqi' => $faker->finance->amount(2,20,2),
        'date_time' => new DateTime
        
    ];
});


$factory->define(NodeStatusConfiguration::class, function (Faker $faker) {
     
     return [
         
        'v_in_label' => $faker->name,
        'v_in_key_title' => $faker->title,
        'v_in_key_value' => $faker->unique()->name,
        'v_in_min_value' => $faker->finance()->amount(2,20,2),
        'v_in_max_value' => $faker->finance()->amount(2,20,2),
        'v_mcu_label' => $faker->unique()->title,
        'v_mcu_key_title' => $faker->unique()->title,
        'v_mcu_key_value' => $faker->unique()->name,
        'v_mcu_min_value' => $faker->finance()->amount(2,20,2),
        'v_mcu_max_value' => $faker->finance()->amount(2,20,2)
     ];
         
 });