<?php

namespace Tests\Feature\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory as Faker;
use App\Field;
use App\Subscriber;

class ManageExistingSubscriberTest extends TestCase
{
    /**
     * Testing update of subscriber name and email
     *
     * @return void
     */
    public function testUpdateSubscriberSuccessfully()
    {
        $faker = Faker::create();
        $email = $faker->freeEmail;
        $name = $faker->name;
        $payload = ['email' => $email, 'name' => $name];

        $this->json('PUT', 'api/updateSubscriber/3', $payload)
            ->assertStatus(200)
            ->assertJsonFragment([
                'name' => $name,
                'email' => $email
            ]);
    }
    /**
     * Testing refuse to update if new email doesn't have a
     * valide domain
     */
    public function testupdateSubscriberFailOnEmailDomainCheck()
    {
        $faker = Faker::create();
        $payload = ['email' => $faker->word, 'name' => $faker->name];

        $this->json('PUT', 'api/updateSubscriber/3', $payload)
            ->assertStatus(422)
            ->assertSeeText('Invalid email domain');
    }
    /**
     * Testing updating successfully a subscriber's state
     * given a valid state value
     */
    public function testUpdateSubscriberStateSuccessfully()
    {
        $payload = ['state' =>'junk'];

        $this->json('PUT', 'api/updateSubscriberState/3', $payload)
            ->assertStatus(200)
            ->assertJsonFragment([
                'state' => 'junk'
            ]);
    }
    /**
     * Testing fail on updating a subscriber's state
     * when the given state value is invalid
     */
    public function testUpdateSubscriberStateToFailOnStateValue()
    {
        $payload = ['state' =>'pigeon'];

        $this->json('PUT', 'api/updateSubscriberState/3', $payload)
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
        $faker = Faker::create();
        $subscriber = Subscriber::has('fields', '<', 1)->first();
        $field = Field::find(2);

        $value = getFakeDataByType($field->type);

        $payload = [
            'fields' => [
                [
                'id' => $field->id,
                'value' => $value
                ]
            ]
        ];

        $this->json('POST', 'api/addSubscriberFields/'.$subscriber->id, $payload)
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $subscriber->id
            ]);
    }
    /**
     * Testing that you can't add an already existing field as a
     * subscriber field
     */
    public function testAddNewFieldsToUserToFailFieldExistsAlready()
    {
        $faker = Faker::create();
        $subscriber = Subscriber::has('fields')->first();
        $field = $subscriber->fields[0];

        $value = getFakeDataByType($field->field->type);

        $payload = [
            'fields' => [
                [
                'id' => $field->field_id,
                'value' => $value
                ]
            ]
        ];

        $this->json('POST', 'api/addSubscriberFields/'.$subscriber->id, $payload)
            ->assertStatus(422)
            ->assertSeeText('Field already assigned to subscriber.');
    }
    /**
     * Testing that you can't add a field as a
     * subscriber field if the field value is not valid
     */
    public function testAddNewFieldsToUserToFailInvalidFieldType()
    {
        $faker = Faker::create();
        $subscriber = Subscriber::has('fields')->first();
        $field = $subscriber->fields[0];

        $value = getBadFakeDataByType($field->field->type);

        $payload = [
            'fields' => [
                [
                'id' => $field->field_id,
                'value' => $value
                ]
            ]
        ];

        $this->json('POST', 'api/addSubscriberFields/'.$subscriber->id, $payload)
            ->assertStatus(422)
            ->assertSeeText('Invalid value type');
    }
    /**
     * Update already existing subscriber fields with new values successfully
     */
    public function testUpdateFieldsToUserSuccessfully()
    {
        $faker = Faker::create();
        $subscriber = Subscriber::has('fields')->first();
        $field = $subscriber->fields[0];

        $value = getFakeDataByType($field->field->type);

        $payload = [
            'fields' => [
                [
                'id' => $field->field_id,
                'value' => $value
                ]
            ]
        ];

        $this->json('PUT', 'api/updateSubscriberFields/'.$subscriber->id, $payload)
            ->assertStatus(200)
            ->assertJsonFragment([
                'fieldId' => $field->field_id
            ]);
    }
    /**
     * Testing that you can't update a subscriber field
     * if the field value is not valid
     */
    public function testUpdateFieldsToUserToFailInvalidFieldType()
    {
        $faker = Faker::create();
        $subscriber = Subscriber::has('fields')->first();
        $field = $subscriber->fields[0];

        $value = getBadFakeDataByType($field->field->type);

        $payload = [
            'fields' => [
                [
                'id' => $field->field_id,
                'value' => $value
                ]
            ]
        ];

        $this->json('PUT', 'api/updateSubscriberFields/3', $payload)
            ->assertStatus(422)
            ->assertSeeText('Invalid value type');
    }
    /**
     * Testing sucessfully deleting a subscriber field
     */
    public function testDeleteSubscriberFieldsSuccessfully()
    {
        $subscriber = Subscriber::has('fields')->first();
        $field = $subscriber->fields[0];
        $payload = [
            'fieldIds' => [$field->field_id]
        ];
        $this->json('DELETE', 'api/deleteSubscriberFields/'.$subscriber->id, $payload)
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $subscriber->id
            ]);
    }
}
