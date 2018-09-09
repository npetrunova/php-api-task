<?php

namespace App\Http\Controllers;

use App\Subscriber;
use App\Field;
use App\SubscriberField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriberController extends Controller
{
    public function createSubcriber(Request $request)
    {
        $messages = [
            'fields.*.value.required' => 'Value is required for all fields.',
            'fields.*.value.max' => 'Value cannot be bigger than 255 symbols.',
            'fields.*.id.required'    => 'ID is required for all fields.',
        ];
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'email' => 'required|email|max:320',
            'fields.*.value' => 'required|max:255',
            'fields.*.id' => 'required'
        ], $messages);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['errors' => $errors->toArray()], 422);
        }

        $name = $request->input('name');
        $email = $request->input('email');
        $fields = $request->input('fields');
        // Validate email domain
        $isValidEmailDomain = $this->validateEmailDomain($email);

        if (!$isValidEmailDomain) {
            return response()->json(['errors' => 'Invalid emmail domain'], 422);
        }

        // If no additional fields, just create subscriber
        if ($fields === null || count($fields) === 0) {
            $subscriber = Subscriber::create(['name' => $name, 'email' => $email]);
            // Select newly created subscriber
            $createdRecord = Subscriber::find($subscriber->id);
            unset($createdRecord['updated_at']);
            unset($createdRecord['created_at']);

            $responseArray = ['data' => ['msg' => 'Subscriber created successfully', 'subscriber' => $createdRecord]];

            return response()->json($responseArray, 200);
        }
        $fieldValidationErrors = $this->checkFieldsForErrors($fields);
        if (!empty($fieldValidationErrors)) {
            return response()->json(['errors' => $fieldValidationErrors], 422);
        }

        $subscriber = Subscriber::create(['name' => $name, 'email' => $email]);
        $createdRecord = Subscriber::find($subscriber->id);
        unset($createdRecord['updated_at']);
        unset($createdRecord['created_at']);
        foreach ($fields as $field) {
            $subscriberField = SubscriberField::create(['subscriber_id' => $subscriber->id,'field_id' => $field['id'], 'value' => $field['value']]);
        }
        $createdRecord['fields'] = $fields;

        return response()->json(['data' =>['msg' => 'Subscriber created successfully!', 'subscriber' => $createdRecord]], 201);
    }

    private function checkFieldsForErrors($fields)
    {
        $fieldValidationErrors = [];
        foreach ($fields as $field) {
            $fieldId = $field['id'];
            $fieldValue = $field['value'];
            $fieldModel = Field::select(['title', 'type'])->where('id', $fieldId)->first();

            if ($fieldModel === null) {
                $fieldValidationErrors[] = 'Field with id: '.$fieldId.' does not exist';
                continue;
            }
            $isValidInput = $this->validateFieldType($fieldModel['type'], $fieldValue);
            
            if (!$isValidInput) {
                $fieldValidationErrors[] =
                    'Invalid value type for "'.$fieldModel['title'].'", expected '.$fieldModel['type'];
                continue;
            }
        }

        return $fieldValidationErrors;
    }

    private function validateFieldType($fieldType, $fieldValue)
    {
        switch ($fieldType) {
            case 'number':
                return is_numeric($fieldValue);
            case 'string':
                return is_string($fieldValue);
            case 'boolean':
                return is_bool($fieldValue);
            case 'date':
                return (bool)strtotime($fieldValue);
        }
    }

    public function retrieveSubscribers()
    {
        $subscribers = Subscriber::with('fields.field')->get();

        if (count($subscribers) > 0) {
            $responseArray = [];
            foreach ($subscribers as $subscriber) {
                $formatedSubscriber = $this->formatSubscriberData($subscriber);
                $responseArray[] = $formatedSubscriber;
            }
            return response()->json(['data' => $responseArray], 200);
        }

        return response()->json([], 204);
    }

    public function retrieveSubscriber($id)
    {
        $subscriber = Subscriber::find($id);

        if ($subscriber === null) {
            return response()->json(['errors' => ['id' => ['Record not found']]], 404);
        }
        $responseArray = $this->formatSubscriberData($subscriber);

        return response()->json(['data' => $responseArray], 200);
    }

    public function deleteSubscriber($id)
    {
        $subscriber = Subscriber::find($id);

        if (!$subscriber) {
            return response()->json(['errors' => ['id' => ['Record not found']]], 404);
        }
        $subscriber->delete();

        return response()->json(['data' =>['msg' => 'Subscriber deleted successfully!']], 200);
    }

    public function updateSubscriber($id, Request $request)
    {
        $acceptedStates = ['active', 'unsubscribed', 'junk', 'bounced', 'unconfirmed'];
        $validator = Validator::make($request->all(), [
            'name' => 'max:100',
            'email' => 'email|max:320',
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

        $isValidEmailDomain = $this->validateEmailDomain($request->input('email'));

        if (!$isValidEmailDomain) {
            return response()->json(['errors' => 'Invalid emmail domain'], 422);
        }

        $subscriber->name = $request->input('name');
        $subscriber->email = $request->input('email');
        $subscriber->state = $request->input('state');
        $subscriber->save();

        return response()->json(['data' =>['msg' => 'Subscriber updated successfully!']], 200);
    }

    public function updateSubscriberFields($id, Request $request)
    {
        $messages = [
            'fields.*.value.required' => 'Value is required for all fields.',
            'fields.*.value.max' => 'Value cannot be bigger than 255 symbols.',
            'fields.*.id.required'    => 'ID is required for all fields.',
        ];

        $validator = Validator::make($request->all(), [
            'fields.*.value' => 'required|max:255',
            'fields.*.id' => 'required'
        ], $messages);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['errors' => $errors->toArray()], 422);
        }

        $subscriber = Subscriber::find($id);

        if (!$subscriber) {
            return response()->json(['errors' => ['id' => ['Record not found']]], 404);
        }

        if ($request->input('fields') !== null && count($request->input('fields')) > 0) {
            $isValidFields = true;
            foreach ($request->input('fields') as $toUpdate) {
                $subscriberField = SubscriberField::with('field')->where('subscriber_id', $id)->where('field_id', $toUpdate['id'])->first();
                $isValidType  = $this->validateFieldType($subscriberField->field->type, $toUpdate['value']);
                if ($subscriberField === null || !$isValidType) {
                    $isValidFields  = false;
                    break;
                }
            }
            if (!$isValidFields) {
                return response()->json(['errors' => 'Invalid value type, could not update fields.'], 422);
            }
            foreach ($request->input('fields') as $toUpdate) {
                $subscriberField = SubscriberField::with('field')->where('subscriber_id', $id)->where('field_id', $toUpdate['id'])->first();
                    $subscriberField->value = $toUpdate['value'];
                    $subscriberField->save(); //check for validation
            }

            return response()->json(['data' =>['msg' => 'Subscriber fields updated successfully!']], 200);
        }
    }

    public function addSubscriberFields($id, Request $request)
    {
        $messages = [
            'fields.*.value.required' => 'Value is required for all fields.',
            'fields.*.value.max' => 'Value cannot be bigger than 255 symbols.',
            'fields.*.id.required'    => 'ID is required for all fields.',
        ];

        $validator = Validator::make($request->all(), [
            'fields.*.value' => 'required|max:255',
            'fields.*.id' => 'required'
        ], $messages);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['errors' => $errors->toArray()], 422);
        }

        $subscriber = Subscriber::find($id);

        if (!$subscriber) {
            return response()->json(['errors' => ['id' => ['Record not found']]], 404);
        }

        if ($request->input('fields') !== null && count($request->input('fields')) > 0) {
            $isValidFields = true;
            foreach ($request->input('fields') as $newField) {
                $fieldModel = Field::select(['title', 'type'])->where('id', $newField['id'])->first();
                if ($fieldModel === null) {
                    $isValidFields = false;
                    break;
                }
                $isValidInput = $this->validateFieldType($fieldModel->type, $newField['value']);
                $existsAlready = SubscriberField::where('field_id', $newField['id'])->where('subscriber_id', $id)->exists();
                if (!$isValidInput || $existsAlready) {
                    $isValidFields = false;
                }
            }

            if (!$isValidFields) {
                return response()->json(['errors' => 'Invalid value type or field already exists, could not insert fields.'], 422);
            }

            foreach ($request->input('fields') as $newField) {
                $subscriberField = SubscriberField::create(['subscriber_id' => $id, 'field_id' => $newField['id'], 'value' => $newField['value']]);
            }

            return response()->json(['data' =>['msg' => 'Subscriber fields added successfully!']], 200);
        }
    }

    public function deleteSubscriberFields($id, Request $request)
    {
        $subscriber = Subscriber::find($id);

        if (!$subscriber) {
            return response()->json(['errors' => ['id' => ['Record not found']]], 404);
        }

        if ($request->input('fieldIds') !== null && count($request->input('fieldIds')) > 0) {
            foreach ($request->input('fieldIds') as $toDelete) {
                $subscriberField = SubscriberField::where('subscriber_id', $id)->where('field_id', $toDelete)->first();
                if ($subscriberField !== null) {
                    $subscriberField->delete();
                }
            }

            return response()->json(['data' =>['msg' => 'Subscriber fields deleted successfully!']], 200);
        }
    }

    private function formatSubscriberData($subscriber)
    {
        $responseArray = [];
        $subscriberId = $subscriber->id;
        $responseArray['id'] = $subscriberId;
        $responseArray['name'] = $subscriber->name;
        $responseArray['email'] = $subscriber->email;
        $responseArray['state'] = $subscriber->state;
        if (count($subscriber->fields) == 0) {
            $responseArray['fields'] = [];
        } else {
            foreach ($subscriber->fields as $field) {
                $subscriberFieldId = $field->id;
                $responseArray['fields'][$subscriberFieldId]['id'] = $subscriberFieldId;
                $responseArray['fields'][$subscriberFieldId]['fieldId'] = $field->field_id;
                $responseArray['fields'][$subscriberFieldId]['value'] = $field->value;
                $responseArray['fields'][$subscriberFieldId]['title'] = $field->field->title;
            }
        }

        return $responseArray;
    }

    private function validateEmailDomain($email)
    {
        list($user, $domain) = explode('@', $email);
        return checkdnsrr($domain, 'MX');
    }
}
