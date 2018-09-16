<?php

namespace App\Http\Controllers;

use App\Subscriber;
use App\Field;
use App\SubscriberField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\SubscriberRequest;
use App\Http\Requests\SubscriberStateRequest;
use App\Http\Resources\Subscriber as SubscriberResource;
use \Illuminate\Support\Facades\Lang;

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

        // Make sure the field values are of valid types
        if ($fields != null && count($fields) > 0) {
            $fieldValidationErrors = checkFieldsForErrors($fields);
            if (!empty($fieldValidationErrors)) {
                return response()->json(['errors' => $fieldValidationErrors], 422);
            }
        }

        $createdRecord = Subscriber::create(['name' => $name, 'email' => $email]);

        if ($fields != null && count($fields) > 0) {
            foreach ($fields as $field) {
                $subscriberField = SubscriberField::create(
                    ['subscriber_id' => $createdRecord->id,'field_id' => $field['id'], 'value' => $field['value']]
                );
            }
        }

        return new SubscriberResource(Subscriber::find($createdRecord->id));
    }

    /**
     * Function to retrieve all existing subscribers
     * @return Response
     */
    public function retrieveSubscribers()
    {
        return SubscriberResource::collection(Subscriber::all());
    }

    /**
     * Function to retrieve all existing subscribers that have a given state
     * @param String $state
     * @return Response
     */
    public function retrieveSubscribersByState($state)
    {
        return SubscriberResource::collection(
            Subscriber::where('state', $state)->get()
        );
    }

    /**
     * Function to retrieve one subscriber given an id
     * @param int $id
     * @return Response
     */
    public function retrieveSubscriber($id)
    {
        $subscriber = new SubscriberResource(Subscriber::find($id));
        
        if ($subscriber === null) {
            return response()->json(['errors' => ['id' => trans('custom.record_not_found')]], 404);
        }

        return $subscriber;
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
        $subscriber = optional(Subscriber::find($id))->delete();
        if ($subscriber == null) {
            return response()->json(['errors' => ['id' => trans('custom.record_not_found')]], 404);
        }

        return response()->json(['data' =>['msg' => trans('custom.subscriber_deleted_successfully')]], 200);
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
            return response()->json(['errors' => ['id' => trans('custom.record_not_found')]], 404);
        }

        $subscriber->name = $request->input('name');
        $subscriber->email = $request->input('email');
        $subscriber->save();

        return new SubscriberResource($subscriber);
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
            return response()->json(['errors' => ['id' => trans('custom.record_not_found')]], 404);
        }

        $subscriber->state = $request->input('state');
        $subscriber->save();

        return new SubscriberResource($subscriber);
    }
}
