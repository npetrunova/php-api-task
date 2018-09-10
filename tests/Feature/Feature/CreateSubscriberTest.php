<?php

namespace Tests\Feature\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Subscriber;

class CreateSubscriberTest extends TestCase
{
    /**
     * Test to create a user only with name and email.
     *
     * @return void
     */
    public function testCreateSubscriberSuccessfully()
    {
        $payload = ['email' => 'testlogin@gmail.com', 'name' => 'Johny B'];

        $this->json('POST', 'api/createSubcriber', $payload)
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'msg',
                    'subscriber' => [
                        'id',
                        'name',
                        'email',
                        'state'
                    ],
                ]    
            ]);
    }
    /**
     * Test to create a user with name and email and fields.
     *
     * @return void
     */
    public function testCreateSubscriberWithFieldsSuccessfully()
    {
        $payload = [
            'email' => 'testlogin@gmail.com', 
            'name' => 'Johny B',
            'fields' => [
                [
                'id' => '1',
                'value' => 'jfdj'
                ]   
            ]
        ];

        $this->json('POST', 'api/createSubcriber', $payload)
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'msg',
                    'subscriber' => [
                        'id',
                        'name',
                        'email',
                        'state',
                        'fields' =>[
                            [
                                'id',
                                'value'
                            ]
                        ]
                    ],
                 ]    
            ]);
    }
    /**
     * Test to fail to create a user only with name and email because 
     * of wrong email domain
     * @return void
     */
    public function testCreateSubscriberToFailwithWrongEmailDomain()
    {
        $payload = ['email' => 'testlogin@jkhsg.com', 'name' => 'Johny B'];

        $this->json('POST', 'api/createSubcriber', $payload)
            ->assertStatus(422)
            ->assertSeeText( 'Invalid emmail domain' );
    }
    /**
     * Test to fail to create a user with fields because of wrong type
     * of value for field
     */
    public function testCreateSubscriberToFailwithWrongFieldValueType()
    {
        $payload = [
            'email' => 'testlogin@gmail.com', 
            'name' => 'Johny B',
            'fields' => [
                [
                'id' => '2',
                'value' => 'jfdgh'
                ]   
            ]
        ];

        $this->json('POST', 'api/createSubcriber', $payload)
            ->assertStatus(422)
            ->assertJsonStructure([
                'errors'   
            ]);
    }
    /**
     * Test to fail to create a subscriber with fields because of missing
     * value for field
     */
    public function testCreateSubscriberToFailwithNoFieldValueType()
    {
        $payload = [
            'email' => 'testlogin@gmail.com', 
            'name' => 'Johny B',
            'fields' => [
                [
                'id' => '2'
                ]   
            ]
        ];

        $this->json('POST', 'api/createSubcriber', $payload)
            ->assertStatus(422)
            ->assertJsonStructure([
                'errors'   
            ]);
    }
    /**
     * Test to fail to create a subscriber with missing name value
     */
    public function testCreateSubscriberToFailwithNoName()
    {
        $payload = [
            'email' => 'testlogin@gmail.com', 
            'fields' => [
                [
                'id' => '2',
                'value' => '3'
                ]   
            ]
        ];

        $this->json('POST', 'api/createSubcriber', $payload)
            ->assertStatus(422)
            ->assertJsonStructure([
                'errors'   
            ]);
    }
}
