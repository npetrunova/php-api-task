<?php
use App\Field;

if (!function_exists('checkFieldsForErrors')) {
    /**
     * Takes a list of subscriber fields and checks
     * whether they are valid before saving to the database
     * @param Array $fields
     * @return Array
     */
    function checkFieldsForErrors($fields)
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
            $isValidInput = validateFieldType($fieldModel['type'], $fieldValue);
            
            if (!$isValidInput) {
                $fieldValidationErrors[] =
                    'Invalid value type for "'.$fieldModel['title'].'", expected '.$fieldModel['type'];
                continue;
            }
        }

        return $fieldValidationErrors;
    }
}

if (!function_exists('validateFieldType')) {
    /**
     * Checks whether the subscriber field value corresponds to the
     * field type
     * @param String $fieldType
     * @param String $fieldValue
     * @return Bool
     */
    function validateFieldType($fieldType, $fieldValue)
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
