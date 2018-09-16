<?php

namespace Tests\Feature\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Field;
use App\SubscriberField;
use Faker\Factory as Faker;

class ManageFieldsTest extends TestCase
{
    /**
     * Test to successfully fetch all fields from the database
     */
    public function testRetrieveAllFields()
    {
        $field = Field::find(2);
        $this->json('GET', 'api/getFields')
        ->assertStatus(200)
        ->assertJsonFragment([
            [
                "id" => $field->id,
                "title" => $field->title,
                "type" => $field->type
            ]
        ]);
    }
    /**
     * To successfully fetch one field by id
     */
    public function testRetrieveFieldByIdSuccessfully()
    {
        $field = Field::find(2);
        $this->json('GET', 'api/getField/'.$field->id)
        ->assertStatus(200)
        ->assertJsonFragment([
            "data"=> [
                "id" => $field->id,
                "title" => $field->title,
                "type" => $field->type
            ]
        ]);
    }
    /**
     * Fail to fetch a field by id if the id doesn't exist
     */
    public function testRetrieveFieldByIdToFailRecordNotFound()
    {
        $this->json('GET', 'api/getField/100')
        ->assertStatus(404)
        ->assertSeeText('Record not found');
    }
    /**
     * Create successfully a field
     */
    public function testCreateFieldSuccessfully()
    {
        $faker = Faker::create();
        $title = $faker->word;
        $type = $faker->randomElement(['date', 'number', 'string', 'boolean' ]);
        $payload = ['title' => $title, 'type' => $type];

        $this->json('POST', 'api/createField', $payload)
        ->assertStatus(201)
        ->assertJsonFragment([
                'title' => $title,
                'type' => $type
        ]);
    }
    /**
     * Fail to create a field because of a missing type
     */
    public function testCreateFieldToFailMissingType()
    {
        $faker = Faker::create();
        $payload = ['title' => $faker->word];

        $this->json('POST', 'api/createField', $payload)
        ->assertStatus(422)
        ->assertJsonStructure([
            'errors'
        ]);
    }
    /**
     * Deleting successfully a field given an id and that the
     * field is not referenced in subscriber_fields table
     */
    public function testDeleteFieldSuccessfully()
    {
        $field = Field::orderBy('id', 'desc')->first();
        $this->json('DELETE', 'api/deleteField/'.$field->id)
        ->assertStatus(200)
        ->assertJsonFragment([
            'data' =>[
                'msg' => 'Field deleted successfully!',
            ]
        ]);
    }
    /**
     * Fails to delete as the field is referenced in subscriber_fields table
     */
    public function testDeleteFieldFailFieldInUse()
    {
        $subscriberField = SubscriberField::find(2);
        $this->json('DELETE', 'api/deleteField/'.$subscriberField->field_id)
        ->assertStatus(422)
        ->assertSeeText('Cannot delete field as it is assigned to subscribers');
    }
    /**
     * Fails to delete as the field to be deleted does not exist
     */
    public function testDeleteFieldFailFieldDoesNotExist()
    {
        $this->json('DELETE', 'api/deleteField/111')
        ->assertStatus(404)
        ->assertSeeText('Record not found');
    }
}
