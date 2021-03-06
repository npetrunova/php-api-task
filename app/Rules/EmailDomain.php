<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EmailDomain
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (strpos($value, '@') === false) {
            return false;
        }

        list($user, $domain) = explode('@', $value);
        return checkdnsrr($domain, 'MX');
    }
}
