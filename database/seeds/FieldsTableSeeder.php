<?php

use Illuminate\Database\Seeder;

class FieldsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Run only if table is empty
        if (DB::table('fields')->get()->count() == 0) {
            DB::table('fields')->insert(
                [
                    'title' => 'User code',
                    'type'  => 'string',
                ]
            );
            DB::table('fields')->insert(
                [
                    'title' => 'Frequent Flyer Number',
                    'type'  => 'number',
                ]
            );
        }
    }
}
