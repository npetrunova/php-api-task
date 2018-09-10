<?php

namespace Tests\Feature\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageExistingSubscriberTest extends TestCase
{
    /**
     * Testing update of subscriber name and email
     *
     * @return void
     */
    public function testUpdateSubscriberSuccessfully()
    {
        $payload = ['email' => 'testlogin@gmail.com', 'name' => 'Johny B'];

        $this->json('POST', 'api/updateSubscriber/3', $payload)
            ->assertStatus(200)
            ->assertJsonFragment([
                'data' => [
                    'msg' => "Subscriber updated successfully!"
                ]
            ]);
    }
    /**
     * Testing refuse to update if new email doesn't have a
     * valide domain
     */
    public function testupdateSubscriberFailOnEmailDomainCheck()
    {
        $payload = ['email' => 'testlogin@fsdg.com', 'name' => 'Johny B'];

        $this->json('POST', 'api/updateSubscriber/3', $payload)
            ->assertStatus(422)
            ->assertSeeText('Invalid emmail domain');
    }
    /**
     * Testing updating successfully a subscriber's state
     * given a valid state value
     */
    public function testUpdateSubscriberStateSuccessfully()
    {
        $payload = ['state' =>'junk'];

        $this->json('POST', 'api/updateSubscriberState/3', $payload)
            ->assertStatus(200)
            ->assertJsonFragment([
                'data' => [
                    'msg' => "Subscriber state updated successfully!"
                ]
            ]);
    }
    /**
     * Testing fail on updating a subscriber's state
     * when the given state value is invalid
     */
    public function testUpdateSubscriberStateToFailOnStateValue()
    {
        $payload = ['state' =>'pigeon'];

        $this->json('POST', 'api/updateSubscriberState/3', $payload)
            ->assertStatus(422)
            ->assertJsonStructure([
                'errors'
            ]);
    }
    /**
     * Add new subscriber field to a subscriber successfully
     */
    public function testAddNewFieldsToUserSuccessfully()
    {
        $payload = [
            'fields' => [
                [
                'id' => '1',
                'value' => 'jfdj'
                ]
            ]
        ];

        $this->json('POST', 'api/addSubscriberFields/3', $payload)
            ->assertStatus(200)
            ->assertJsonFragment([
                'data' => [
                    'msg' => "Subscriber fields added successfully!"
                ]
            ]);
    }
    /**
     * Testing that you can't add an already existing field as a
     * subscriber field
     */
    public function testAddNewFieldsToUserToFailFieldExistsAlready()
    {
        $payload = [
            'fields' => [
                [
                'id' => '1',
                'value' => 'jfdj'
                ]
            ]
        ];

        $this->json('POST', 'api/addSubscriberFields/3', $payload)
            ->assertStatus(422)
            ->assertJsonFragment([
                'errors' =>
                    ['Invalid value type or field already exists, could not insert fields.']
            ]);
    }
    /**
     * Testing that you can't add a field as a
     * subscriber field if the field value is not valid
     */
    public function testAddNewFieldsToUserToFailInvalidFieldType()
    {
        $payload = [
            'fields' => [
                [
                'id' => '2',
                'value' => 'jfdj'
                ]
            ]
        ];

        $this->json('POST', 'api/addSubscriberFields/3', $payload)
            ->assertStatus(422)
            ->assertJsonFragment([
                'errors' =>
                    ['Invalid value type or field already exists, could not insert fields.']
            ]);
    }
    /**
     * Update already existing subscriber fields with new values successfully
     */
    public function testUpdateFieldsToUserSuccessfully()
    {
        $payload = [
            'fields' => [
                [
                'id' => '1',
                'value' => 'edited'
                ]
            ]
        ];

        $this->json('POST', 'api/updateSubscriberFields/3', $payload)
            ->assertStatus(200)
            ->assertJsonFragment([
                'data' => [
                    'msg' => "Subscriber fields updated successfully!"
                ]
            ]);
    }
    /**
     * Testing that you can't update a subscriber field
     * if the field value is not valid
     */
    public function testUpdateFieldsToUserToFailInvalidFieldType()
    {
        $payload = [
            'fields' => [
                [
                'id' => '1',
                'value' => false
                ]
            ]
        ];

        $this->json('POST', 'api/updateSubscriberFields/3', $payload)
            ->assertStatus(422)
            ->assertJsonFragment([
                'errors' =>
                    ['Invalid value type, could not update fields.']
            ]);
    }

    public function testDeleteSubscriberFieldsSuccessfully()
    {
        $payload = [
            'fieldIds' => [1]
        ];
        $this->json('DELETE', 'api/deleteSubscriberFields/3', $payload)
            ->assertStatus(200)
            ->assertJsonFragment([
                'data' => [
                    'msg' => "Subscriber fields deleted successfully!"
                ]
            ]);
    }
}
