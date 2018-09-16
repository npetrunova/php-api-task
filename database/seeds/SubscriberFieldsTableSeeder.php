<?php

use Illuminate\Database\Seeder;
use App\Subscriber;
use App\Field;
use App\SubscriberField;
use Faker\Factory as Faker;

class SubscriberFieldsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $subscribers = Subscriber::all()->pluck('id')->toArray();
        $fields = Field::all()->pluck('id')->toArray();

        $faker = Faker::create();

        foreach (range(1, 5) as $index) {
            $field_id = $faker->randomElement($fields);
            $fieldType = Field::select('type')->where('id', $field_id)->first();

            switch ($fieldType->type) {
                case 'number':
                    $value = $faker->randomNumber;
                    break;
                case 'string':
                    $value = $faker->sentence;
                    break;
                case 'boolean':
                    $value = $faker->boolean;
                    break;
                case 'date':
                    $value = $faker->date;
            }
            SubscriberField::create([
                'subscriber_id' => $faker->randomElement($subscribers),
                'field_id' =>  $field_id,
                'value' => $value
            ]);
        }
    }
}
