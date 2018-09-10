<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $fillable = ['name', 'email', 'state'];
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
        'name' => 'required|max:100',
        'email' => 'required|email|max:320',
        'fields.*.value' => 'required|max:255',
        'fields.*.id' => 'required'
    ];
    /**
     * Establishing a relationship with SubscriberField model
     */
    public function fields()
    {
        return $this->hasMany('App\SubscriberField');
    }
    /**
     * Checks is email domain exists
     * @param String $email
     * @return Bool
     */
    public static function validateEmailDomain($email)
    {
        list($user, $domain) = explode('@', $email);
        return checkdnsrr($domain, 'MX');
    }
    /**
     * Formats subscriber data in a format that contains only
     * the essential data that might be needed by the frontend
     * @param Subscriber $subscriber
     * @return Array
     */
    public static function formatSubscriberData($subscriber)
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
}
