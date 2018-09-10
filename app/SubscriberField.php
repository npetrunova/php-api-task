<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriberField extends Model
{
    protected $fillable = ['subscriber_id', 'field_id', 'value'];
    /**
     * Custom validation messages
     */
    public static $messages = [
        'fields.*.value.required' => 'Value is required for all fields.',
        'fields.*.value.max' => 'Value cannot be bigger than 255 symbols.',
        'fields.*.id.required'    => 'ID is required for all fields.',
    ];
     /**
     * Validation rules
     */
    public static $rules = [
        'fields.*.value' => 'required|max:255',
        'fields.*.id' => 'required'
    ];
    /**
     * Establishes a relationship with the Field model
     */
    public function field()
    {
        return $this->belongsTo('App\Field');
    }
    /**
     * Takes a list of subscriber fields and checks
     * whether they are valid before saving to the database
     * @param Array $fields
     * @return Array
     */
    public static function checkFieldsForErrors($fields)
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
            $isValidInput = SubscriberField::validateFieldType($fieldModel['type'], $fieldValue);
            
            if (!$isValidInput) {
                $fieldValidationErrors[] =
                    'Invalid value type for "'.$fieldModel['title'].'", expected '.$fieldModel['type'];
                continue;
            }
        }

        return $fieldValidationErrors;
    }
    /**
     * Checks whether the subscriber field value corresponds to the
     * field type
     * @param String $fieldType
     * @param String $fieldValue
     * @return Bool
     */
    public static function validateFieldType($fieldType, $fieldValue)
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
