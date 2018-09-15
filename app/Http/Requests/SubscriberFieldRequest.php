<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'fields.*.value' => 'required|max:255',
            'fields.*.id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'fields.*.value.required' => 'Value is required for all fields.',
            'fields.*.value.max' => 'Value cannot be bigger than 255 symbols.',
            'fields.*.id.required'    => 'ID is required for all fields.',
        ];
    }
}
