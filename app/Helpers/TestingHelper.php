<?php
use Faker\Factory as Faker;

if (!function_exists('getFakeDataByType')) {
    function getFakeDataByType($type)
    {
        $faker = Faker::create();
        switch ($type) {
            case 'number':
                return $faker->randomNumber;
            case 'string':
                return $faker->sentence;
            case 'boolean':
                return $faker->boolean;
            case 'date':
                return $faker->date;
        }
    }
}

if (!function_exists('getBadFakeDataByType')) {
    function getBadFakeDataByType($type)
    {
        $faker = Faker::create();
        switch ($type) {
            case 'number':
                return $faker->sentence;
            case 'string':
                return $faker->boolean;
            case 'boolean':
                return $faker->date;
            case 'date':
                return $faker->boolean;
        }
    }
}