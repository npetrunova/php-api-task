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
        return [
            'fields.*.value' => 'required|max:255',
            'fields.*.id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'fields.*.value.required' => trans('custom.value_required'),
            'fields.*.value.max' => trans('custom.value_max_length'),
            'fields.*.id.required'    => trans('custom.id_required'),
        ];
    }
}
