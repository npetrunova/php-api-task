<?php

namespace App\Http\Controllers;

use App\Subscriber;
use App\Field;
use App\SubscriberField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\SubscriberRequest;
use App\Http\Requests\SubscriberStateRequest;

class SubscriberController extends Controller
{
    /**
     * Creates a new Subscriber entry and Subscriber fields entries if any
     * @param Request $request
     * @return Response
     */
    public function createSubcriber(SubscriberRequest $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $fields = $request->input('fields');

        // If no additional fields, just create subscriber
        if ($fields === null || count($fields) === 0) {
            $createdRecord = $this->saveSubscriber($name, $email);
            $responseArray = ['data' => ['msg' => 'Subscriber created successfully', 'subscriber' => $createdRecord]];

            return response()->json($responseArray, 201);
        }
        // Make sure the field values are of valid types
        $fieldValidationErrors = checkFieldsForErrors($fields);
        if (!empty($fieldValidationErrors)) {
            return response()->json(['errors' => $fieldValidationErrors], 422);
        }

        $createdRecord = $this->saveSubscriber($name, $email);
        foreach ($fields as $field) {
            $subscriberField = SubscriberField::create(
                ['subscriber_id' => $createdRecord->id,'field_id' => $field['id'], 'value' => $field['value']]
            );
        }
        $createdRecord['fields'] = $fields;

        return response()->json(['data' =>
            ['msg' => 'Subscriber created successfully!', 'subscriber' => $createdRecord]], 201);
    }

    /**
     * Function to save a subscriber that's being called if
     * the data passed in the request passes all validations
     * @param String $name
     * @param String $email
     * @return Subscriber
     */
    private function saveSubscriber($name, $email)
    {
        $subscriber = Subscriber::create(['name' => $name, 'email' => $email]);
        $createdRecord = Subscriber::select('id', 'name', 'email', 'state')->where('id', $subscriber->id)->first();

        return $createdRecord;
    }

    /**
     * Function to retrieve all existing subscribers
     * @return Response
     */
    public function retrieveSubscribers()
    {
        $subscribers = Subscriber::with('fields.field')->get();

        if (count($subscribers) > 0) {
            $responseArray = formatSubscriberDataArray($subscribers);
            return response()->json(['data' => $responseArray], 200);
        }

        return response()->json([], 204);
    }

    /**
     * Function to retrieve all existing subscribers that have a given state
     * @param String $state
     * @return Response
     */
    public function retrieveSubscribersByState($state)
    {
        $subscribers = Subscriber::with('fields.field')
            ->where('state', $state)
            ->get();

        if (count($subscribers) > 0) {
            $responseArray = formatSubscriberDataArray($subscribers);
            return response()->json(['data' => $responseArray], 200);
        }

        return response()->json([], 204);
    }

    /**
     * Function to retrieve one subscriber given an id
     * @param int $id
     * @return Response
     */
    public function retrieveSubscriber($id)
    {
        $subscriber = Subscriber::find($id);

        if ($subscriber === null) {
            return response()->json(['errors' => ['id' => ['Record not found']]], 404);
        }
        $responseArray = formatSubscriberData($subscriber);

        return response()->json(['data' => $responseArray], 200);
    }

    /**
     * Function to delete a subscriber given an id.
     * The deletion process removes any subscriber fields
     * for that subscriber
     * @param int $id
     * @return Response
     */
    public function deleteSubscriber($id)
    {
        $subscriber = Subscriber::find($id);

        if (!$subscriber) {
            return response()->json(['errors' => ['id' => ['Record not found']]], 404);
        }
        $subscriber->delete();

        return response()->json(['data' =>['msg' => 'Subscriber deleted successfully!']], 200);
    }

    /**
     * Function to update subscriber's basic info (email and name) given an id
     * Performs validation checks on name and email (including email domain validation)
     * before saving the changes to the database
     * @param int $id
     * @param SubscriberRequest $request
     * @return Response
     */
    public function updateSubscriber($id, SubscriberRequest $request)
    {
        $subscriber = Subscriber::find($id);

        if (!$subscriber) {
            return response()->json(['errors' => ['id' => ['Record not found']]], 404);
        }

        $subscriber->name = $request->input('name');
        $subscriber->email = $request->input('email');
        $subscriber->save();

        return response()->json(['data' =>['msg' => 'Subscriber updated successfully!']], 200);
    }

    /**
     * Function to update subscriber's state given an id
     * It would only update the state if it's one of the accepted values
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function updateSubscriberState($id, SubscriberStateRequest $request)
    {
        $subscriber = Subscriber::find($id);

        if (!$subscriber) {
            return response()->json(['errors' => ['id' => ['Record not found']]], 404);
        }

        $subscriber->state = $request->input('state');
        $subscriber->save();

        return response()->json(['data' =>['msg' => 'Subscriber state updated successfully!']], 200);
    }
}
