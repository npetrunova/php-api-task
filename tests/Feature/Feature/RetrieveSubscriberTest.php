<?php

namespace Tests\Feature\Feature;

use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Subscriber;
use App\Http\Resources\Subscriber as SubscriberResource;

class RetrieveSubscriberTest extends TestCase
{
    /**
     * Test to retrieve all subscribers successfully
     * Should pass if there's no subscribers as well
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
        $subscriber = Subscriber::orderBy('id', 'desc')->first();
        $subscriberState = $subscriber->state;

        $this->json('GET', 'api/getSubscribersByState/'.$subscriberState)
            ->assertStatus(200)
            ->assertJsonFragment([
                "id"=> $subscriber->id,
                "name"=> $subscriber->name,
                "email"=> $subscriber->email,
                "state"=> $subscriberState,
                "fields"=> []
            ]);
    }
    /**
     * This test will pass assuming that the data for subscriber 1 has not
     * been modified since it's been seeded.
     */
    public function testRetrieveSubscriberByIdSucessfully()
    {
        $subscriber = Subscriber::first();
        $this->json('GET', 'api/getSubscriber/'.$subscriber->id)
            ->assertStatus(200)
            ->assertJsonFragment(["id" => $subscriber->id]);
    }
    /**
     * Testing retrieving an unexisting subscriber
     */
    public function testRetrieveSubscriberByIdToFailBecauseOfUnexistingId()
    {
        $this->json('GET', 'api/getSubscriber/300')
            ->assertStatus(404)
            ->assertSeeText('Record not found');
    }
}
