<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Subscriber;
use App\Field;
use App\SubscriberField;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\SubscriberFieldRequest;
use App\Http\Resources\Subscriber as SubscriberResource;
use \Illuminate\Support\Facades\Lang;

class SubscriberFieldController extends Controller
{
    /**
     * Updates an array of subscriber fields given a subscriber id
     * @param int $id
     * @param SubscriberFieldRequest $request
     * @return Response
     */
    public function updateSubscriberFields($id, SubscriberFieldRequest $request)
    {
        $subscriber = Subscriber::find($id);

        if (!$subscriber) {
            return response()->json(['errors' => ['id' => trans('custom.record_not_found')]], 404);
        }

        if ($request->input('fields') !== null && count($request->input('fields')) > 0) {
            foreach ($request->input('fields') as $toUpdate) {
                $subscriberField = SubscriberField::with('field')
                    ->where('subscriber_id', $id)
                    ->where('field_id', $toUpdate['id'])
                    ->first();
                    $subscriberField->value = $toUpdate['value'];
                    $subscriberField->save();
            }

            return new SubscriberResource($subscriber);
        }
    }

    /**
     * Adds an array of subscriber fields given a subscriber id
     * @param int $id
     * @param SubscriberFieldRequest $request
     * @return Response
     */
    public function addSubscriberFields($id, SubscriberFieldRequest $request)
    {
        $subscriber = Subscriber::find($id);

        if (!$subscriber) {
            return response()->json(['errors' => ['id' => trans('custom.record_not_found')]], 404);
        }

        if ($request->input('fields') !== null && count($request->input('fields')) > 0) {
            foreach ($request->input('fields') as $newField) {
                $subscriberField = SubscriberField::create(['subscriber_id' =>
                    $id, 'field_id' => $newField['id'], 'value' => $newField['value']]);
            }

            return new SubscriberResource($subscriber);
        }
    }

    /**
     * Deletes an array of subscriber fields given a subscriber id
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function deleteSubscriberFields($id, Request $request)
    {
        $subscriber = Subscriber::find($id);

        if (!$subscriber) {
            return response()->json(['errors' => ['id' => trans('custom.record_not_found')]], 404);
        }

        if ($request->input('fieldIds') !== null && count($request->input('fieldIds')) > 0) {
            foreach ($request->input('fieldIds') as $toDelete) {
                $subscriberField = SubscriberField::where('subscriber_id', $id)->where('field_id', $toDelete)->first();
                if ($subscriberField !== null) {
                    $subscriberField->delete();
                }
            }

            return new SubscriberResource($subscriber);
        }
    }
}
