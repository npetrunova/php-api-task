<?php

namespace Tests\Feature\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RetrieveSubscriberTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRetrieveAllSubscribersSuccessfully()
    {
        $this->json('GET', 'api/getSubscribers')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => []    
            ]);
    }
    /**
     * This test should pass assuming that no changes to the data have been
     * done to subscriber with id 2. A seeder has been made to provide it
     * with state value 'active'
     * 
     * @return void
     */
    public function testRetrieveAllSubscribersByStateSuccessfully()
    {
        $this->json('GET', 'api/getSubscribersByState/active')
            ->assertStatus(200)
            ->assertJsonFragment([
                        "id"=> 2,
                        "name"=> "Jane Doe",
                        "email"=> "jane@gmail.com",
                        "state"=> "active",
                        "fields"=> [] 
            ])
            ->assertDontSeeText('unconfirmed')
            ->assertDontSeeText('unsubscribed')
            ->assertDontSeeText('junk')
            ->assertDontSeeText('bounced');
    }
    /**
     * This test will pass assuming that the data for subscriber 1 has not
     * been modified since it's been seeded.
     */
    public function testRetrieveSubscriberByIdSucessfully()
    {
        $this->json('GET', 'api/getSubscriber/1')
            ->assertStatus(200)
            ->assertJsonFragment([
                "data" => [
                    "id" => 1,
                    "name" => "John Doe",
                    "email" => "john@gmail.com",
                    "state" => "unconfirmed",
                    "fields" => [
                        "1" => [
                            "id" => 1,
                            "fieldId" => 1,
                            "value" => "user001",
                            "title" => "User code"
                        ]
                    ]
                ]   
            ]);
    }

    public function testRetrieveSubscriberByIdToDailBecauseOfUnexistingId()
    {
        $this->json('GET', 'api/getSubscriber/300')
            ->assertStatus(404)
            ->assertJsonFragment([
                "errors" => [
                    'id' =>['Record not found']
                ]
            ]);
    }
}
