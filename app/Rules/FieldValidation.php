<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Field;
use App\SubscriberField;
use Illuminate\Support\Facades\Route;

class FieldValidation
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function checkIfFieldExists($attribute, $value)
    {
        $fieldId = $value['id'];
        $fieldModel = Field::select(['title', 'type'])->where('id', $fieldId)->first();

        if ($fieldModel === null) {
            return false;
        }

        return true;
    }

    public function checkIfValueTypeCorrect($attribute, $value)
    {
        $fieldId = $value['id'];
        $fieldValue = $value['value'];
        $fieldModel = Field::select(['title', 'type'])->where('id', $fieldId)->first();

        if ($fieldModel === null) {
            return false;
        }
        $fieldType = $fieldModel['type'];
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

    public function checkForFieldDuplicate($attribute, $value)
    {
        $current_params = Route::current()->parameters();

        $fieldId = $value['id'];
        $existsAlready = SubscriberField::where('field_id', $fieldId)
        ->where('subscriber_id', $current_params['id'])
        ->exists();

        return !$existsAlready;
    }
}
