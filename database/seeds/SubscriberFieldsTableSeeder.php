<?php

use Illuminate\Database\Seeder;
use App\Subscriber;
use App\Field;

class SubscriberFieldsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DB::table('subscriber_fields')->get()->count() == 0) {
            $subscriber = Subscriber::first();
            $field = Field::first();
            DB::table('subscriber_fields')->insert(
                [
                    'subscriber_id' => $subscriber->id,
                    'field_id'  => $field->id,
                    'value' => 'user001'
                ]
            );
        }
    }
}
