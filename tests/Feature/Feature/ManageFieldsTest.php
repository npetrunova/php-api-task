<?php

namespace Tests\Feature\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageFieldsTest extends TestCase
{
    /**
     * Test to successfully fetch all fields from the database
     */
    public function testRetrieveAllFields()
    {
        $this->json('GET', 'api/getFields')
        ->assertStatus(200)
        ->assertJsonFragment([
            "data"=> [
                [
                    "id" => 1,
                    "title" => "User code",
                    "type" => "string"
                ],
                [
                    "id" => 2,
                    "title" => "Frequent Flyer Number",
                    "type" => "number"
                ]
            ]
        ]);
    }
    /**
     * To successfully fetch one field by id
     */
    public function testRetrieveFieldByIdSuccessfully()
    {
        $this->json('GET', 'api/getField/1')
        ->assertStatus(200)
        ->assertJsonFragment([
            "data"=> [
                "id" => 1,
                 "title" => "User code",
                "type" => "string"
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
        ->assertJsonFragment([
            'errors' =>
                ['id' => ['Record not found']]
        ]);
    }
    /**
     * Create successfully a field
     */
    public function testCreateFieldSuccessfully()
    {
        $payload = ['title' => 'Date of Birth', 'type' => 'date'];

        $this->json('POST', 'api/createField', $payload)
        ->assertStatus(201)
        ->assertJsonFragment([
            'data' =>[
                'msg' => 'Field created successfully!',
                'field' => [
                    'id' => 3,
                    'title' => 'Date of Birth',
                    'type' => 'date'
                ]
            ]
        ]);
    }
    /**
     * Fail to create a field because of a missing type
     */
    public function testCreateFieldToFailMissingType()
    {
        $payload = ['title' => 'Date of Birth'];

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
        $this->json('DELETE', 'api/deleteField/3')
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
        $this->json('DELETE', 'api/deleteField/1')
        ->assertStatus(422)
        ->assertJsonFragment([
            'errors' => ['id' =>
                ['Cannot delete field as it is assigned to subscribers']]
        ]);
    }
    /**
     * Fails to delete as the field to be deleted does not exist
     */
    public function testDeleteFieldFailFieldDoesNotExist()
    {
        $this->json('DELETE', 'api/deleteField/111')
        ->assertStatus(404)
        ->assertJsonFragment([
            'errors' => ['id' => ['Record not found'] ]
        ]);
    }
}
