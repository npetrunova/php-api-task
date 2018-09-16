<?php

use Illuminate\Database\Seeder;
use App\Subscriber;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(FieldsTableSeeder::class);
        $this->call(SubscribersTableSeeder::class);
        $this->call(SubscriberFieldsTableSeeder::class);
    }
}
