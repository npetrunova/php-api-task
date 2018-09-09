<?php

use Illuminate\Database\Seeder;

class SubscribersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DB::table('subscribers')->get()->count() == 0) {
            DB::table('subscribers')->insert(
                [
                    'name' => 'John Doe',
                    'email'  => 'john@gmail.com'
                ]
            );
            DB::table('subscribers')->insert(
                [
                    'name' => 'Jane Doe',
                    'email'  => 'jane@gmail.com',
                    'state' => 'active'
                ]
            );
        }
    }
}
