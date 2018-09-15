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
            'fields.*.value.required' => 'Value is required for all fields.',
            'fields.*.value.max' => 'Value cannot be bigger than 255 symbols.',
            'fields.*.id.required' => 'ID is required for all fields.',
            'email_domain' => 'Invalid email domain.',
        ];
    }
}
