<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Support\Facades\Lang;

class SubscriberFieldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'fields.*' =>'bail|does_field_exist|check_value_type|check_for_duplicate',
                    'fields.*.value' => 'required|max:255',
                    'fields.*.id' => 'required'
                ];
            case 'PUT':
                return [
                    'fields.*' =>'bail|does_field_exist|check_value_type',
                    'fields.*.value' => 'required|max:255',
                    'fields.*.id' => 'required'
                ];
        }
    }

    public function messages()
    {
        return [
            'fields.*.value.required' => trans('custom.value_required'),
            'fields.*.value.max' => trans('custom.value_max_length'),
            'fields.*.id.required'    => trans('custom.id_required'),
            'fields.*.does_field_exist' => trans('custom.does_field_exist'),
            'fields.*.check_value_type' => trans('custom.check_value_type'),
            'fields.*.check_for_duplicate' => trans('custom.check_for_duplicate')
        ];
    }
}
