<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Field;

class FieldsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 5) as $index) {
            Field::create([
                'title' => $faker->word,
                'type' =>  $faker->randomElement(['date', 'number', 'string', 'boolean' ])
            ]);
        }
    }
}
