<?php

namespace Tests\Feature\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Subscriber;
use App\Field;
use Faker\Factory as Faker;

class CreateSubscriberTest extends TestCase
{
    /**
     * Test to create a user only with name and email.
     *
     * @return void
     */
    public function testCreateSubscriberSuccessfully()
    {
        $faker = Faker::create();
        $payload = ['email' => $faker->freeEmail, 'name' => $faker->name];

        $this->json('POST', 'api/createSubcriber', $payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                        'id',
                        'name',
                        'email',
                        'state',
                        'fields'
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
        $faker = Faker::create();
        $field = Field::find(2);

        switch ($field->type) {
            case 'number':
                $value = $faker->randomNumber;
                break;
            case 'string':
                $value = $faker->sentence;
                break;
            case 'boolean':
                $value = $faker->boolean;
                break;
            case 'date':
                $value = $faker->date;
        }

        $payload = [
            'email' => $faker->freeEmail,
            'name' => $faker->name,
            'fields' => [
                [
                'id' => $field->id,
                'value' => $value
                ]
            ]
        ];

        $this->json('POST', 'api/createSubcriber', $payload)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
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
        $faker = Faker::create();
        $payload = ['email' => $faker->word, 'name' => $faker->name];

        $this->json('POST', 'api/createSubcriber', $payload)
            ->assertStatus(422)
            ->assertSeeText('Invalid email domain');
    }
    /**
     * Test to fail to create a user with fields because of wrong type
     * of value for field
     */
    public function testCreateSubscriberToFailwithWrongFieldValueType()
    {
        $faker = Faker::create();
        $field = Field::find(2);

        switch ($field->type) {
            case 'number':
                $value = $faker->sentence;
                break;
            case 'string':
                $value = $faker->boolean;
                break;
            case 'boolean':
                $value = $faker->date;
                break;
            case 'date':
                $value = $faker->boolean;
        }

        $payload = [
            'email' => $faker->freeEmail,
            'name' => $faker->name,
            'fields' => [
                [
                'id' => $field->id,
                'value' => $value
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
        $faker = Faker::create();
        $payload = [
            'email' => $faker->freeEmail,
            'name' => $faker->name,
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
        $faker = Faker::create();
        $payload = [
            'email' => $faker->freeEmail,
            'fields' => []
        ];

        $this->json('POST', 'api/createSubcriber', $payload)
            ->assertStatus(422)
            ->assertJsonStructure([
                'errors'
            ]);
    }
}
