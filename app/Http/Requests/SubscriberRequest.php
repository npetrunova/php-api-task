<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriberRequest extends FormRequest
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
                    'name' => 'required|max:100',
                    'email' => 'required|email|email_domain|max:320',
                    'fields.*' =>'bail|does_field_exist|check_value_type',
                    'fields.*.value' => 'required|max:255',
                    'fields.*.id' => 'required'
                ];
            case 'PUT':
                return [
                    'name' => 'max:100',
                    'email' => 'email|email_domain|max:320'
                ];
        }
    }

    public function messages()
    {
        return [
            'fields.*.value.required' => trans('custom.value_required'),
            'fields.*.value.max' => trans('custom.value_max_length'),
            'fields.*.id.required' => trans('custom.id_required'),
            'email_domain' => trans('custom.email_domain_fail'),
            'fields.*.does_field_exist' => trans('custom.does_field_exist'),
            'fields.*.check_value_type' => trans('custom.check_value_type'),
        ];
    }
}
