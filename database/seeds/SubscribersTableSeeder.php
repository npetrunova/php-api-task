<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Subscriber;

class SubscribersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $index) {
            Subscriber::create([
                'name' => $faker->name,
                'email' => $faker->freeEmail,
                'state' =>  $faker->randomElement(['active', 'unsubscribed', 'junk', 'bounced', 'unconfirmed' ])
            ]);
        }
    }
}
