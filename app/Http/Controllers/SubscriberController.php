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
}
