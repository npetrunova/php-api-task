<?php
if (!function_exists('validateEmailDomain')) {
    /**
     * Checks is email domain exists
     * @param String $email
     * @return Bool
     */
    function validateEmailDomain($email)
    {
        list($user, $domain) = explode('@', $email);
        return checkdnsrr($domain, 'MX');
    }
}

if (!function_exists('formatSubscriberData')) {
    /**
     * Formats subscriber data in a format that contains only
     * the essential data that might be needed by the frontend
     * @param Subscriber $subscriber
     * @return Array
     */
    function formatSubscriberData($subscriber)
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

if (!function_exists('formatSubscriberDataArray')) {
    function formatSubscriberDataArray($subscribers)
    {
        $responseArray = [];
        foreach ($subscribers as $subscriber) {
            $formatedSubscriber = formatSubscriberData($subscriber);
            $responseArray[] = $formatedSubscriber;
        }

        return $responseArray;
    }
}
