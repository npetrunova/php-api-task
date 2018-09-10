<?php

namespace App\Http\Controllers;

use App\Subscriber;
use App\Field;
use App\SubscriberField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriberController extends Controller
{
    /**
     * Creates a new Subscriber entry and Subscriber fields entries if any
     * @param Request $request
     * @return Response
     */
    public function createSubcriber(Request $request)
    {
        $validator = Validator::make($request->all(), Subscriber::$rules, Subscriber::$messages);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['errors' => $errors->toArray()], 422);
        }

        $name = $request->input('name');
        $email = $request->input('email');
        $fields = $request->input('fields');
        // Validate email domain
        $isValidEmailDomain = Subscriber::validateEmailDomain($email);

        if (!$isValidEmailDomain) {
            return response()->json(['errors' => 'Invalid emmail domain'], 422);
        }

        // If no additional fields, just create subscriber
        if ($fields === null || count($fields) === 0) {
            $createdRecord = $this->saveSubscriber($name, $email);
            $responseArray = ['data' => ['msg' => 'Subscriber created successfully', 'subscriber' => $createdRecord]];

            return response()->json($responseArray, 200);
        }
        // Make sure the field values are of valid types
        $fieldValidationErrors = SubscriberField::checkFieldsForErrors($fields);
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
        $createdRecord = Subscriber::find($subscriber->id);
        unset($createdRecord['updated_at']);
        unset($createdRecord['created_at']);

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
            $responseArray = [];
            foreach ($subscribers as $subscriber) {
                $formatedSubscriber = Subscriber::formatSubscriberData($subscriber);
                $responseArray[] = $formatedSubscriber;
            }
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
        $responseArray = Subscriber::formatSubscriberData($subscriber);

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
     * @param Request $request
     * @return Response
     */
    public function updateSubscriber($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'max:100',
            'email' => 'email|max:320'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['errors' => $errors->toArray()], 422);
        }

        $subscriber = Subscriber::find($id);

        if (!$subscriber) {
            return response()->json(['errors' => ['id' => ['Record not found']]], 404);
        }

        $isValidEmailDomain = Subscriber::validateEmailDomain($request->input('email'));

        if (!$isValidEmailDomain) {
            return response()->json(['errors' => 'Invalid emmail domain'], 422);
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
    public function updateSubscriberState($id, Request $request)
    {
        $acceptedStates = ['active', 'unsubscribed', 'junk', 'bounced', 'unconfirmed'];
        $validator = Validator::make($request->all(), [
            'state' => 'in:'.implode(',', $acceptedStates)
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['errors' => $errors->toArray()], 422);
        }

        $subscriber = Subscriber::find($id);

        if (!$subscriber) {
            return response()->json(['errors' => ['id' => ['Record not found']]], 404);
        }

        $subscriber->state = $request->input('state');
        $subscriber->save();

        return response()->json(['data' =>['msg' => 'Subscriber state updated successfully!']], 200);
    }
}
